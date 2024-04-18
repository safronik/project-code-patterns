<?php

namespace Safronik\CodePatterns\Generative;

use Safronik\CodePatterns\ContainerItem;
use Safronik\CodePatterns\Interfaces\Serviceable;
use Safronik\Helpers\ReflectionHelper;

/**
 * Service Locator
 *
 * Features:
 * - Lazy load
 * - Automatic get available classes
 * - Parameters passing
 * - Parameter filter
 * - Aliases
 * - Singleton support
 *
 * @author  Roman safronov
 * @version 1.0.0
 */

trait Container
{
    use Singleton;
    
    protected array $services = [];
    protected array $aliases  = [];
    
    /**
     * Adds a gateway as a first parameter to the class parameters constructor
     *
     * @param mixed $service
     * @param array $params
     *
     * @return void
     */
    abstract protected function filterInitParameters( mixed $service, array &$params ): void;
    
    public function __construct( $services )
    {
        $this->appendBulk( $services );
    }
    
    public static function get( string $alias, mixed $params = [] ): mixed
    {
        static::isInitialized()
            || throw new \Exception( 'Container ' . static::class . ' is not initialized yet. Please, do so before use it.' );
        
        $service_classname = static::getInstance()->aliases[ $alias ] ?? $alias;
        
        return isset( static::getInstance()->services[ $service_classname ] )
            ? static::getInstance()->services[ $service_classname ]( $params )
            : throw new \Exception( "ContainerItem '$alias' not found container: '" . static::class . "'" );
    }
    
    public static function has( string $alias ): bool
    {
        static::isInitialized()
            || throw new \Exception( 'Container ' . static::class . ' is not initialized yet. Please, do so before use it.' );
        
        $service_classname = self::getInstance()->aliases[ $alias ] ?? $alias;
        
        return isset( self::getInstance()->services[ $service_classname ] );
    }
    
    protected function appendBulk( array $services ): void
    {
        foreach( $services as $alias => $service_classname){
            if( class_exists( $service_classname ) ){
                $this->append( $service_classname, is_string( $alias ) ? $alias : null );
            }
        }
    }
    
    private function append( string $service_classname, string|null $alias = null ): void
    {
        $this->addAlias( $alias, $service_classname );
        
        $using_singleton                      = ReflectionHelper::isClassUseTrait( $service_classname, Singleton::class );
        $this->services[ $service_classname ] =
            function( $params ) use ( $using_singleton, $service_classname ){
            
                // Append gateway as the first parameter
                $this->filterInitParameters( $service_classname, $params );
                
                /** @var Singleton|Serviceable $service_classname  */
                // Create new object or get an instance in case of singleton
                return $using_singleton
                    ? $service_classname::getInstance( ...$params )
                    : new $service_classname( ...$params );
            };
    }
    
    private function addAlias( string|null $alias, string|ContainerItem $service ): void
    {
        $alias = $alias ?? $service::getAlias();
        if( $alias ){
            $this->aliases[ $alias ] = $service;
        }
    }
    
    /**
     * Get available classes from the directory with precondition
     * Search only final classes
     * Skip classes if the name starts with _
     *
     * @param string      $directory          Directory to scan
     * @param string      $namespace          Namespace for directory
     * @param string|null $classname_contains Search param. Class name should contain the string
     *
     * @return array
     */
    protected function getAvailable( string $directory, string $namespace, ?string $classname_contains = null ): array
    {
        // Get classes
        $classes = ReflectionHelper::getClassesFromDirectory(
            $directory,
            $namespace,
            filter         : $classname_contains,
            recursive      : true,
            filter_callback: 'Safronik\Helpers\ClassHelper::filterFinalClasses'
        );
        
        // Create aliases
        // Example: Safronik\Modules\DBMigrator\DBMigratorModule -> DBMigrator
        $aliases  = array_map(
            static fn( $class ) =>
                str_replace(
                    $classname_contains,
                    '',
                    substr( $class, strrpos( $class, '\\' ) + 1 )
                ),
            $classes,
        );
        
        return array_combine( $aliases, $classes );
    }

}