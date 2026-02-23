<?php

namespace Safronik\CodePatterns\Structural;

use ReflectionClass;
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
 * @version 1.0.1
 */
class DI
{
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
     * @throws ReflectionException
     */
    public function createDependencyMapForDirectory( string $directoryToScan, string $directoryNamespace ): array
    {
        $classes   = self::getClassesFromDirectory( $directoryToScan, $directoryNamespace );
        $class_map = [];
        
        foreach( $classes as $class ){
            $reflection  = new ReflectionClass( $class );
            
            if( $reflection->isInterface() || $reflection->isTrait() ){
                continue;
            }
            
            $class_map[ '\\' . $class ]['constructor']  = static::getConstructor( $class );
            $class_map[ '\\' . $class ]['dependencies'] = static::getDependencies( $reflection );
        }
        
        return $class_map;
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

    private static function getInfo( string $class, ReflectionClass $reflection ): array
    {
        return [
            'constructor'  => static::getConstructor( $class ),
            'dependencies' => static::getDependencies( $reflection ),
        ];
    }

    /**
     * Gets class constructor callback
     */
    private static function getConstructor( string $class ): callable
    {
        return static fn( array $parameters = [] ) =>
                ReflectionHelper::isClassUseTrait( $class, Singleton::class ) && method_exists( $class, 'getInstance')
                    ? $class::getInstance( ...$parameters )
                    : new $class( ...$parameters );
    }

    /**
     * Gets class dependencies by its reflection
     *  with such info as:
     *  - type
     *  - value
     *  - optional
     *  - is_class
     *  - is_interface
     *
     * @return array of dependencies
     */
    private static function getDependencies( ReflectionClass $reflection ): array
    {
        $dependencies = [];
        
        if( $reflection->getConstructor() === null ){
            return [];
        }
        
        foreach( $reflection->getConstructor()?->getParameters() ?? [] as $parameter ){
            
            $type = $parameter->getType();

            $type = $type && is_object( $type ) && method_exists( $type, 'getName' )
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

    /**
     * DI Constructor
     *
     * @param string $class  Class name to create
     * @param mixed  $params Arguments for class constructor
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function get( string $class, mixed $params = [] ): mixed
    {
        // static::getInstance()->isInClassMap( $class )
        //     || throw new ContainerException( "$class is not found in DI-container" );
        
        // Dependency is a singleton and already initialized, no need to create new
        if( ReflectionHelper::isClassUseTrait( $class, Singleton::class ) && $class::isInitialized() ){
            return $class::getInstance();
        }

        if( ! $this->isInClassMap( $class ) ){
            $this->class_map[ '\\' . $class ] = self::getInfo( $class, new ReflectionClass( $class ) );
        }
        
        $item                 = $this->class_map[ '\\' . $class ];
        $item['dependencies'] = $this->standardizePassedParameters( $params ) + ($item['dependencies'] ?? []);

        // Resolving arguments and dependencies
        try{
            $construct_parameters = [];
            foreach( $item['dependencies'] as $name => $dependency ){

                // Value passed, pass it to constructor
                if( isset( $dependency['value'] ) ){
                    $construct_parameters[ $name ] = $dependency[ 'value' ];

                // No value passed and argument is optional
                }elseif( $dependency['optional'] ){
                    continue;

                // No value passed, argument is obligatory, type is interface. Recursively make it using the interface map from config
                }elseif( $dependency['is_interface'] ){
                    $construct_parameters[ $name ] = static::get($this->getByInterface( $dependency['type'] ) );

                // No value passed, argument is obligatory, type is class. Recursively make it
                }elseif( $dependency['is_class'] ){
                    $construct_parameters[ $name ] = static::get( $dependency['type'] );
                }
            }
        }catch( ContainerException $exception ){
            throw new ContainerException( "Couldn't resolve dependencies for $class: " . $exception->getMessage() );
        }

        // Call constructor
        return $item['constructor']( $construct_parameters );
    }
    
    public function setParametersFor( string $class, array $arguments ): void
    {
        $item                 = $this->class_map[ '\\' . $class ] ?? self::getInfo( $class, new ReflectionClass( $class ) );
        $item['dependencies'] = $this->standardizePassedParameters( $arguments ) + $item[ 'dependencies'];

        $this->class_map[ '\\' . $class ] = $item;
    }
    
    private function getByInterface( string $interface, mixed $params = [] ): mixed
    {
        isset( $this->interface_map[ $interface ] )
            || throw new ContainerException("Pair for the interface '$interface' isn't set" );
        
        return $this->interface_map[ $interface ];
    }
    
    private function standardizePassedParameters( array $parameters ): array
    {
        return array_map(
            static fn( $parameter ) => [ 'value' => $parameter, ],
            $parameters
        );
    }
    
    private function isInClassMap( $class ): bool
    {
        return isset( $this->class_map[ '\\' . $class ] );
    }

    public static function use( $param )
    {

    }
}