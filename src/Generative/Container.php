<?php

namespace Safronik\CodePatterns\Generative;

use Safronik\CodePatterns\ContainerItem;
use Safronik\CodePatterns\Exceptions\ContainerException;
use Safronik\Helpers\ReflectionHelper;

/**
 * Service Locator
 *
 * Features:
 * - Lazy load
 * - Automatically get available classes
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
    
    protected array $item = [];
    protected array $aliases  = [];
    
    /**
     * Adds a gateway as a first parameter to the class parameters constructor
     *
     * @param mixed $item
     * @param array $params
     *
     * @return void
     */
    abstract protected function filterInitParameters( mixed $item, array &$params ): void;
    
    public function __construct( $items )
    {
        $this->appendBulk( $items );
    }

    /**
     * Get an item from the container
     *
     * @param string $alias
     * @param mixed $params
     * @return mixed
     * @throws ContainerException
     */
    public static function get( string $alias, mixed $params = [] ): mixed
    {
        static::isInitialized()
            || throw new ContainerException( 'Container ' . static::class . ' is not initialized yet. Please, do so before use it.' );
        
        $item_classname = static::getInstance()->aliases[ $alias ] ?? $alias;
        
        return isset( static::getInstance()->items[ $item_classname ] )
            ? static::getInstance()->items[ $item_classname ]( $params )
            : throw new ContainerException( "ContainerItem '$alias' not found container: '" . static::class . "'" );
    }

    /**
     * Check if the item exists in the container
     *
     * @param string $alias
     * @return bool
     * @throws ContainerException
     */
    public static function has( string $alias ): bool
    {
        static::isInitialized()
            || throw new ContainerException( 'Container ' . static::class . ' is not initialized yet. Please, do so before use it.' );
        
        $item_classname = self::getInstance()->aliases[ $alias ] ?? $alias;
        
        return isset( self::getInstance()->items[ $item_classname ] );
    }
    
    protected function appendBulk( array $items ): void
    {
        foreach( $items as $alias => $item_classname){
            if( class_exists( $item_classname ) ){
                $this->append( $item_classname, is_string( $alias ) ? $alias : null );
            }
        }
    }
    
    private function append( string $item_classname, string|null $alias = null ): void
    {
        $this->addAlias( $alias, $item_classname );
        
        $using_singleton                      = ReflectionHelper::isClassUseTrait( $item_classname, Singleton::class );
        $this->items[ $item_classname ] =
            function( $params ) use ( $using_singleton, $item_classname ){
            
                // Append gateway as the first parameter
                $this->filterInitParameters( $item_classname, $params );
                
                /** @var Singleton|mixed $item_classname  */
                // Create new object or get an instance in case of singleton
                return $using_singleton
                    ? $item_classname::getInstance( ...$params )
                    : new $item_classname( ...$params );
            };
    }
    
    private function addAlias( string|null $alias, string|ContainerItem $item ): void
    {
        $alias = $alias ?? $item::getAlias();
        if( $alias ){
            $this->aliases[ $alias ] = $item;
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
    protected function getFromDirectory( string $directory, string $namespace, ?string $classname_contains = null ): array
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