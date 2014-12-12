<?php
namespace Fruty\Observe;

/**
 * Class Invoker
 * @package Fruty\Observe
 * @author Fruty <ed.fruty@gmail.com>
 */
class Invoker 
{
    /**
     * @var object
     */
    protected $object;

    /**
     * @var Observer
     */
    protected $observer;

    /**
     * Constructor
     *
     * @access public
     * @param object $object
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->observer = new Observer($this);
    }

    /**
     * Get Observer or real object instance
     *
     * @param bool $realInstance
     * @return Observer
     */
    public function __invoke($realInstance = false)
    {
        return $realInstance ? $this->object : $this->observer;
    }

    /**
     * Remap getter to real instance
     *
     * @access public
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this(true)->$property;
    }

    /**
     * Remap setter to real instance
     *
     * @access public
     * @param $property
     * @param $value
     * @return mixed
     */
    public function __set($property, $value)
    {
        return $this(true)->$property = $value;
    }

    /**
     * Catch all calls, fire events and remap to real instance
     *
     * @access public
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args = array())
    {
        $event = $this->observer->createEvent()
            ->setMethod($method)
            ->setParams($args)
            ->setInstance($this)
            ->setType('before');

        $before = $this->observer->trigger()->getResult();

        if ($event->isPropagationStopped()) {
            return $before;
        }
        /** @var \Closure $closure */
        if ($closure = $this->observer->bound($method)) {
            $callback = $closure->bindTo($this, $this);
            $result = Executor::callClosure($callback, $event->getParams());
        } else {
            $result = Executor::call($this(true), $method, $event->getParams());
        }
        $event->setType('after')->setResult($result);
        return $this->observer->trigger()->getResult();
    }
}