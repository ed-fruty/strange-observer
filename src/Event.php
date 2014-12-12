<?php
namespace Fruty\Observe;

/**
 * Class Event
 * @package Fruty\Observe
 * @author Fruty <ed.fruty@gmail.com>
 */
class Event 
{
    /**
     * Low priority value
     */
    const PRIORITY_LOW = 10;

    /**
     * Normal priority value
     */
    const PRIORITY_NORMAL = 100;

    /**
     * High priority value
     */
    const PRIORITY_HIGH = 1000;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var object
     */
    protected $instance;

    /**
     * @var bool
     */
    protected $isPropagationStopped = false;

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return object
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param object $instance
     * @return $this
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * Check is propagation stopped
     *
     * @access public
     * @return bool
     */
    public function isPropagationStopped()
    {
        return $this->isPropagationStopped;
    }

    /**
     * Stop propagation
     *
     * @access public
     */
    public function stopPropagation()
    {
        $this->isPropagationStopped = true;
    }
} 