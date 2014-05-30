<?php


namespace Mcprohosting\Retry;


class Runner
{
    /**
     * The main action to try to call.
     *
     * @var callable
     */
    public $action;

    /**
     * Function to run to return test value.
     *
     * @var callable
     */
    public $condition;

    /**
     * Value to compare the condition against.
     *
     * @var array
     */
    public $value = array('is', false);

    /**
     * The value in milliseconds between iterations.
     *
     * @var integer
     */
    public $delay = 0;

    public function __construct()
    {
        $this->condition = function ($data) {
            return $data;
        };
    }

    /**
     * Sets the called action to be the given argument.
     *
     * @param callable $callable
     * @return self
     */
    public function running($callable)
    {
        $this->action = $callable;

        return $this;
    }

    /**
     * Sets the callable (or string) to test against.
     *
     * @param mixed $condition
     * @return self
     */
    public function soLongAs($condition)
    {
        if (!is_callable($condition)) {
            $this->condition = function ($data) use ($condition) {
                return $condition;
            };
        } else {
            $this->condition = $condition;
        }

        return $this;
    }

    /**
     * Sets the value to compare against. This is a strict comparison.
     *
     * @param mixed $value
     * @return self
     */
    public function is($value)
    {
        $this->value = array('is', $value);

        return $this;
    }

    /**
     * Proxy for is()
     *
     * @param mixed $value
     * @return $this
     */
    public function whileIs($value)
    {
        return $this->is($value);
    }

    /**
     * Sets the value to compare against. This is a strict comparison.
     *
     * @param mixed $value
     * @return self
     */
    public function isnt($value)
    {
        $this->value = array('isnt', $value);

        return $this;
    }

    /**
     * Proxy for isnt()
     *
     * @param mixed $value
     * @return $this
     */
    public function whileIsnt($value)
    {
        return $this->isnt($value);
    }

    /**
     * Sets the value in milliseconds to wait between iterations.
     *
     * @param integer $duration
     * @return self
     */
    public function delay($duration)
    {
        $this->delay = round($duration * 1000);

        return $this;
    }

    /**
     * Runs the setup a certain number of times.
     *
     * @param $max_times
     * @return mixed
     */
    public function go($max_times)
    {
        for ($i = 0; $i < $max_times; $i++) {
            $data = $this->action();

            if ($this->testValue($data)) {
                return $data;
            }

            usleep($this->delay);
        }

        return false;
    }

    /**
     * Checks to see whether the value is the one we're looking for to compare.
     *
     * @param mixed $value
     * @return bool
     */
    public function testValue($value)
    {
        $comp = $this->condition($value) === $this->value[1];

        return $this->value[0] === 'is' ? !$comp : $comp;
    }

    /**
     * Handle calls like Javascript function literals, allowing us to call properties as functions without the hassle
     * of an intermediate variable.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $arguments)
    {
        if (isset($this->$method)) {
            return call_user_func_array($this->$method, $arguments);
        }

        throw new \BadMethodCallException('Call to undefined method [' . $method . '] on ' . get_class());
    }
} 
