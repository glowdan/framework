<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 16/8/27
 * Time: 下午12:34
 * reference windwalker https://github.com/ventoviro/windwalker
 */

namespace Swoft\Event;

/**
 * Class Event
 * @package Swoft\Event
 */
class Event implements EventInterface, \ArrayAccess, \Serializable
{
    /**
     * @var string 当前的事件名称
     */
    protected $name;

    /**
     * 参数
     * @var array
     */
    protected $params = [];

    /**
     * @var null|string|mixed
     */
    protected $target;

    /**
     * 停止事件关联的监听器队列的执行
     * @var boolean
     */
    protected $stopPropagation = false;

    /**
     * @param string|null $name
     * @param array $params
     * @throws \InvalidArgumentException
     */
    public function __construct($name = null, array $params = [])
    {
        if ($name) {
            $this->setName($name);
        }

        $this->params = $params;
    }

    /**
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function checkName(string $name)
    {
        $name = trim($name);

        if (!$name || \strlen($name) > 50) {
            throw new \InvalidArgumentException('Set up the name can be a not empty string of not more than 50 characters!');
        }

        if (!preg_match('/^\w[\w-.]{1,56}$/i', $name)) {
            throw new \InvalidArgumentException("The service Id[$name] is invalid string！");
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        $this->name = self::checkName($name);
    }

    /**
     * set all param
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addParams(array $params)
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * get all param
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * clear all param
     */
    public function clearParams()
    {
        $old = $this->params;
        $this->params = [];

        return $old;
    }

    /**
     * add a argument
     * @param $name
     * @param $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addParam($name, $value)
    {
        if (!isset($this->params[$name])) {
            $this->setParam($name, $value);
        }

        return $this;
    }

    /**
     * set a argument
     * @param $name
     * @param $value
     * @throws  \InvalidArgumentException  If the argument name is null.
     * @return $this
     */
    public function setParam($name, $value)
    {
        if (null === $name) {
            throw new \InvalidArgumentException('The argument name cannot be null.');
        }

        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getParam($name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * @param $name
     */
    public function removeParam($name)
    {
        if (isset($this->params[$name])) {
            unset($this->params[$name]);
        }
    }


    /**
     * Get target/context from which event was triggered
     * @return null|string|mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the event target
     * @param  null|string|mixed $target
     * @return void
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Indicate whether or not to stop propagating this event
     * @param  bool $flag
     */
    public function stopPropagation($flag)
    {
        $this->stopPropagation = (bool)$flag;
    }

    /**
     * Has this event indicated event propagation should stop?
     * @return bool
     */
    public function isPropagationStopped()
    {
        return $this->stopPropagation;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array($this->name, $this->params, $this->stopPropagation));
    }

    /**
     * Unserialize the event.
     * @param   string $serialized The serialized event.
     * @return  void
     */
    public function unserialize($serialized)
    {
        // ['allowed_class' => null]
        list($this->name, $this->params, $this->stopPropagation) = unserialize($serialized, []);
    }

    /**
     * Tell if the given event argument exists.
     * @param   string $name The argument name.
     * @return  boolean  True if it exists, false otherwise.
     */
    public function offsetExists($name)
    {
        return $this->hasParam($name);
    }

    /**
     * Get an event argument value.
     * @param   string $name The argument name.
     * @return  mixed  The argument value or null if not existing.
     */
    public function offsetGet($name)
    {
        return $this->getParam($name);
    }

    /**
     * Set the value of an event argument.
     * @param   string $name The argument name.
     * @param   mixed $value The argument value.
     * @return  void
     * @throws \InvalidArgumentException
     */
    public function offsetSet($name, $value)
    {
        $this->setParam($name, $value);
    }

    /**
     * Remove an event argument.
     * @param   string $name The argument name.
     * @return  void
     */
    public function offsetUnset($name)
    {
        $this->removeParam($name);
    }

}
