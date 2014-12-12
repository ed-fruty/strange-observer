<?php
namespace Fruty\Observe;

/**
 * Class Executor
 * As we know, call_user_func_array() is not fast function, so try to call functions a little faster
 *
 * @package Fruty\Observe
 * @author Fruty <ed.fruty@gmail.com>
 */
class Executor 
{
    /**
     * Call object method
     *
     * @access public
     * @param object $instance
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function call($instance, $method, array $args = array())
    {
        switch (count($args)) {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }

    /**
     * Call closure
     *
     * @param \Closure $closure
     * @param array $args
     * @return mixed
     */
    public static function callClosure(\Closure $closure, array $args = array())
    {
        switch (count($args)) {
            case 0:
                return $closure();
            case 1:
                return $closure($args[0]);
            case 2:
                return $closure($args[0], $args[1]);
            case 3:
                return $closure($args[0], $args[1], $args[2]);
            case 4:
                return $closure($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array($closure, $args);
        }
    }
}