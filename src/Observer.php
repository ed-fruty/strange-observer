<?php
namespace Fruty\Observe;

/**
 * Class Observer
 * @package Fruty\Observe
 * @category Observer
 * @author Fruty <ed.fruty@gmail.com>
 */
class Observer 
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * Registered listeners
     * @var array
     */
    protected $on = array();

    /**
     * @var Invoker
     */
    protected $invoker;

    /**
     * Bounded (redeclare) methods
     *
     * @var array
     */
    protected $bind;

    /**
     * Constructor
     *
     * @access public
     * @param Invoker $instance
     */
    public function __construct(Invoker $instance)
    {
        $this->invoker = $instance;
    }

    /**
     * Check is exists listeners for method
     *
     * @access public
     * @param string $method
     * @param string $type
     * @return bool
     */
    public function has($method, $type)
    {
        return isset($this->on[$method][$type]);
    }

    /**
     * Trigger event, call listeners
     *
     * @access public
     * @return Event
     */
    public function trigger()
    {
        if ($this->has($this->event->getMethod(), $this->event->getType())) {
            $data = &$this->on[$this->event->getMethod()][$this->event->getType()];
            krsort($data);
            foreach ($data as $items) {
                foreach ($items as $priority => $action) {
                    if (! $this->event->isPropagationStopped()) {
                        $this->call($action);
                    }
                }
            }
        }
        return $this->event;
    }

    /**
     * @param $action
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function call($action)
    {
        if ($action instanceof \Closure) {
            return $action($this->event);
        }
        if (strpos($action, '@') === false) {
            throw new \InvalidArgumentException("String action must have syntax 'ClassName@actionName'", 500);
        }
        list($class, $method) = explode('@', $action);
        return $class::$method($this->event);
    }

    /**
     * @return Event
     */
    public function createEvent()
    {
        return $this->event = new Event();
    }

    /**
     * Get bounded method if it exists
     *
     * @access public
     * @param string $name
     * @return bool
     */
    public function bound($name)
    {
        return isset($this->bind[$name]) ? $this->bind[$name] : false;
    }

    /**
     * Bind new or redeclare existing method
     *
     * @access public
     * @param $method
     * @param \Closure $closure
     */
    public function bind($method, \Closure $closure)
    {
        $this->bind[$method] = $closure;
    }

    /**
     * Add new listener before action
     *
     * @access public
     * @param string $method
     * @param \Closure $action
     * @param int $priority
     * @return Observer
     */
    public function before($method, $action, $priority = null)
    {
        return $this->on($method, 'before', $action, $priority);
    }

    /**
     * Add new listener after action
     *
     * @access public
     * @param string $method
     * @param \Closure $action
     * @param int $priority
     * @return Observer
     */
    public function after($method, $action, $priority = null)
    {
        return $this->on($method, 'after', $action, $priority);
    }

    /**
     * Add new listener action
     *
     * @access public
     * @param string $method
     * @param string $type
     * @param \Closure $action
     * @param int $priority
     * @return Observer
     */
    public function on($method, $type, $action, $priority = null)
    {
        if ($priority === null) {
            $priority = Event::PRIORITY_NORMAL;
        }
        $this->on[$method][$type][$priority][] = $action;
        return $this;
    }

    /**
     * Add subscriber to class events
     *
     * @access public
     * @param $class
     * @return $this
     */
    public function subscribe($class)
    {
        $priorities = $class::getPriorities();
        foreach (get_class_methods($class) as $method) {
            if (preg_match("~on(after|before)(\w+)~i", $method, $matches)) {
                $priority = (isset($priorities[$method])) ? $priorities[$method] : Event::PRIORITY_NORMAL;
                $this->on(lcfirst($matches[2]), strtolower($matches[1]), "{$class}@{$method}", $priority);
            }
        }
        return $this;
    }

    /**
     * @return \ReflectionClass
     */
    public function reflection()
    {
        $invoker = $this->invoker;
        return new \ReflectionClass($invoker(true));
    }
}