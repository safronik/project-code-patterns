<?php

namespace Safronik\CodePatterns\Structural;

use ReflectionException;
use Safronik\CodePatterns\Exceptions\ContainerException;
use Safronik\CodePatterns\Generative\Singleton;
use Safronik\Helpers\ReflectionHelper;

/**
 * Dependency Injection Container
 *
 * Features:
 * - Lazy load
 * - Automatically get available classes
 * - Parameters passing
 * - Interface mapping
 * - Parameter filter (under construction)
 * - Aliases (under construction)
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
class DI
{
    use Singleton;
    
    private array $class_map;
    private array $interface_map;
    
    public function __construct( array $class_map, array $interface_map = [] )
    {
        $this->class_map     = $class_map;
        $this->interface_map = $interface_map;
    }
    
    /**
     * - Get classes from directory
     * - Resolve their dependencies
     * - Save them to class map
     *
     * @param string $directory
     * @param string $namespace
     *
     * @return array
     * @throws ReflectionException
     */
    public static function getClassMapForDirectory( string $directory, string $namespace = '\\' ): array
    {
        $classes   = self::getClassesFromDirectory( $directory, $namespace );
        $class_map = [];
        
        foreach( $classes as $class ){
            $reflection  = new \ReflectionClass( $class );
            if( $reflection->isInterface() || $reflection->isTrait() ){
                continue;
            }

            $class_map[ '\\' . $class ] = self::resolveClassDependencies( $class, $reflection );
        }
        
        return $class_map;
    }

    /**
     * Set parameters for the class
     * with() analog
     *
     * @param string $class
     * @param array $init_parameters
     * @return self
     * @throws ReflectionException
     */
    public static function setParametersFor( string $class, array $init_parameters ): self
    {
        $di_container                             = self::getInstance();
        $item                                     = $di_container->class_map[ '\\' . $class ] ?? self::resolveClassDependencies( $class, new \ReflectionClass( $class ) );
        $item['dependencies']                     = $di_container->standardizePassedParameters( $init_parameters ) + $item['dependencies'];
        $di_container->class_map[ '\\' . $class ] = $item;

        return $di_container;
    }

    /**
     * Construct and return an instance of the class
     *
     * @param string $class
     * @param array $params
     * @return mixed
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function get( string $class, array $params = [] ): mixed
    {
        /** @var Singleton $class */

        static::isInitialized()
        || throw new ContainerException( 'DI-Container ' . static::class . ' is not initialized yet. Please, do so before use it.' );

        // static::getInstance()->isInClassMap( $class )
        //     || throw new ContainerException( "$class is not found in DI-container" );

        // Dependency is a singleton and already initialized, no need to create new
        if( ReflectionHelper::isClassUseTrait( $class, Singleton::class ) && $class::isInitialized() ){
            return $class::getInstance();
        }

        $container = static::getInstance();

        if( ! $container->isInClassMap( $class ) ){
            $container->class_map[ '\\' . $class ] = self::resolveClassDependencies( $class, new \ReflectionClass( $class ) );
            // @todo save dependency
        }

        $item = $container->class_map[ '\\' . $class ];

        $item['dependencies'] = $container->standardizePassedParameters( $params ) + $item['dependencies'];

        try{
            $construct_parameters = [];
            foreach( $item['dependencies'] as $name => $dependency ){

                if( isset( $dependency['value'] ) ){
                    $construct_parameters[ $name ] = $dependency['value'];

                }elseif( $dependency['is_interface'] ){
                    $construct_parameters[ $name ] = static::get($container->getByInterface( $dependency['type'] ) );

                }elseif( $dependency['is_class'] ){
                    $construct_parameters[ $name ] = static::get( $dependency['type'] );
                }
            }
        }catch( ContainerException $exception ){
            throw new ContainerException( "Couldn't resolve dependencies for $class: " . $exception->getMessage() );
        }

        return $item['constructor']( $construct_parameters );
    }

    /**
     * Returns an array of found classes in the directory corresponding conditions
     *
     * @param string    $directory           Directory to scan
     * @param string    $namespace           Directory namespace
     *
     * @return array of classnames
     */
    private static function getClassesFromDirectory( string $directory, string $namespace ): array
    {
        $found    = [];
        $iterator = new \RecursiveDirectoryIterator( $directory );
        $iterator = new \RecursiveIteratorIterator( $iterator, \RecursiveIteratorIterator::SELF_FIRST );
        
        foreach( $iterator as $file ){
            
            if( ! $file->isFile() || $file->getExtension() !== 'php' ){
                continue;
            }
            
            $classname = $namespace . $file->getPath() . '/' . $file->getBasename( '.php' );
            $classname = str_replace(
                [ $directory, '/' ],
                [ '', '\\' ],
                $classname
            );
            
            try{
                if( class_exists( $classname ) ){
                    $found[] = $classname;
                }
            }catch(\Exception $e){
            
            }
        }
        
        return $found;
    }
    
    private static function resolveClassDependencies( string $class, \ReflectionClass $reflection ): array
    {
        return [
            'constructor'  => static fn( array $parametes = [] ) =>
                ReflectionHelper::isClassUseTrait( $class, Singleton::class )
                    ? $class::getInstance( ...$parametes )
                    : new $class( ...$parametes ),
            'dependencies' => self::getClassDependencies( $reflection ),
        ];
    }
    
    private static function getClassDependencies( \ReflectionClass $reflection ): array
    {
        $dependencies = [];
        
        if( $reflection->getConstructor() === null ){
            return [];
        }
        
        foreach( $reflection->getConstructor()?->getParameters() ?? [] as $parameter ){
            
            $type = $parameter->getType();
            $type = is_object( $type ) && method_exists( $type, 'getName' )
                ? $type->getName()
                : 'unknown';
            $type = in_array( $type, [ 'self', 'static' ], true )
                ? $reflection->getName()
                : $type;
            $dependencies[ $parameter->getName() ] = [
                'type'         => $type,
                'value'        => null,
                'optional'     => $parameter->isOptional(),
                'is_class'     => class_exists( $type ),
                'is_interface' => interface_exists( $type ),
            ];
        }
        
        return $dependencies;
    }

    private function getByInterface( string $interface, mixed $params = [] ): mixed
    {
        isset( $this->interface_map[ $interface ] )
            || throw new ContainerException("Pair for the interface '$interface' isn't set" );
        
        return $this->interface_map[ $interface ];
    }
    
    private function standardizePassedParameters( $parameters ): array
    {
        $standardize_parameters = [];
        
        foreach( $parameters as $name => $parameter ){
            $standardize_parameters[ $name ] = [
                'value' => $parameter,
            ];
        }
        
        return $standardize_parameters;
    }
    
    private function isInClassMap( $class ): bool
    {
        return isset( $this->class_map[ '\\' . $class ] );
    }
}