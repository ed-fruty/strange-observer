<?php
namespace Fruty\Observe;

/**
 * Class Manager
 * @package Fruty\Observe
 * @author Fruty <ed.fruty@gmail.com>
 */
class Manager 
{
    /**
     * Singletons
     *
     * @var array
     */
    protected $singletons = array();

    /**
     * Create class invoker
     *
     * @access public
     * @static
     * @param $class
     * @return Invoker
     */
    public static function make($class)
    {
        return static::instance()->listen($class);
    }

    /**
     * Register class as singleton
     *
     * @access public
     * @static
     * @param $class
     * @return Invoker
     */
    public static function singleton($class)
    {
        return static::instance()->listen($class, true);
    }

    /**
     * Get manager instance
     *
     * @access private
     * @static
     * @return Manager
     */
    private static function instance()
    {
        static $instance;
        if (! $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @param $class
     * @param bool $singleton
     * @return Invoker
     */
    public function listen($class, $singleton = false)
    {
        $instance = $this->makeInstance($class, $singleton);
        return new Invoker($instance);
    }

    /**
     * Get class instance (by existing instance or class name)
     *
     * @access private
     * @param $class
     * @param $singleton
     * @return mixed
     */
    private function makeInstance($class, $singleton)
    {
        $className = is_object($class) ? get_class($class) : $class;
        if ($singleton) {
            if (isset($this->singletons[$className])) {
                return $this->singletons[$className];
            }
        }
        $instance = is_object($class) ? $class : new $class();
        if ($singleton) {
            $this->singletons[$className] = $instance;
        }
        return $instance;
    }
}