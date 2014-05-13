<?php
namespace Kadet\Utils;

class Timer
{
    const INFINITE = -0xff;

    /**
     * Last run time.
     *
     * @var int
     */
    protected $_last;

    /**
     * Callback to function.
     *
     * @var callable
     */
    protected $_callback;

    /**
     * Callback params.
     *
     * @var array
     */
    protected $_params;

    /**
     * Timer interval.
     *
     * @var int
     */
    public $interval;

    /**
     * Is timer active?
     *
     * @var bool
     */
    private $active = true;

    /**
     * Run action only once?
     *
     * @var bool
     *
     * @deprecated Use start(1) instead of oneTime.
     */
    public $oneTime = false;

    protected $_times = self::INFINITE;

    /**
     * Array of all timers (to run tick)
     *
     * @var Timer[]
     */
    private static $_timers = array();

    /**
     * @param int      $interval Timer interval.
     * @param callable $function Function to be executed when proper time occurs.
     * @param array    $params   Parameters for function.
     */
    public function __construct($interval, $function, array $params = array())
    {
        $this->interval = $interval;
        $this->_func    = $function;
        $this->_params  = $params;
        $this->_last    = time();

        self::$_timers[] = $this;
    }

    /**
     * Timer tick.
     */
    public function tick()
    {
        if (!$this->active) return;

        if (time() - $this->_last >= $this->interval) {
            call_user_func_array($this->_func, $this->_params);
            $this->_last = time();

            if ($this->oneTime) $this->stop();

            if ($this->_times != self::INFINITE) {
                $this->_times--;
                if($this->_times <= 0) $this->stop();
            }
        }
    }

    /**
     * Runs tick on all timers.
     */
    public static function update()
    {
        foreach (self::$_timers as $timer)
            $timer->tick();
    }

    /**
     * Start timer.
     *
     * @param int $times How many times timer should tick?
     */
    public function start($times = null)
    {
        $this->active = true;
        if($times !== null) $this->_times = $times;
    }

    /**
     * Stop timer.
     */
    public function stop()
    {
        $this->active = false;
    }

    public function __destruct()
    {
        foreach (self::$_timers as $id => $timer) {
            if ($timer == $this)
                unset(self::$_timers[$id]);
        }
    }
}

?>
