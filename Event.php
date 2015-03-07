<?php
namespace Kadet\Utils;

/**
 * Class Event
 * @package Kadet\Utils
 *
 * Provides
 */
class Event
{
    /**
     * Arguments types array.
     * @var array
     */
    private $_arguments;

    /**
     * Delegates array.
     * @var callable[]
     */
    private $_delegates = array();

    /**
     * @param array $arguments Argument what delegate must accept.
     */
    public function __construct($arguments = array())
    {
        $this->_arguments = $arguments;
    }

    /**
     * Adds callback to event queue.
     *
     * @param callable $delegate Delegate to run when event is fired.
     * @param bool     $front    If set to true, this callback will be ran first.
     */
    public function add(callable $delegate, $front = false)
    {
        if($front)
            array_unshift($this->_delegates, $delegate);
        else
            $this->_delegates[] = $delegate;
    }

    /**
     * Removes callback from event queue.
     * @param callable $delegate Delegate to remove from event queue.
     *
     * @throws \InvalidArgumentException
     */
    public function remove(callable $delegate)
    {
        if(in_array($delegate, $this->_delegates))
            unset($this->_delegates[array_search($delegate, $this->_delegates)]);
    }

    /**
     * @param array $arguments Arguments provided to delegates.
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    public function runArray($arguments)
    {
        if (count($arguments) < count($this->_arguments)) throw new \OutOfRangeException();

        # check argument types
        foreach ($this->_arguments as $no => $type) {
            if (gettype($arguments[$no]) != $type)
                throw new \InvalidArgumentException("Argument $no is not $type.");
        }

        foreach ($this->_delegates as $delegate)
            if(call_user_func_array($delegate, $arguments));
    }

    /**
     * Run event.
     */
    public function run()
    {
        $this->runArray(func_get_args());
    }

    /**
     * Run event.
     */
    public function __invoke() {
        $this->runArray(func_get_args());
    }
}