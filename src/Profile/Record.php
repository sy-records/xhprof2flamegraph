<?php

namespace Xhprof2Flamegraph\Profile;

class Record
{
    /**
     * number of calls
     *
     * @var int
     */
    public $ct;

    /**
     * inclusive time
     *
     * @var int
     */
    public $wt;

    /**
     * cpu time
     *
     * @var int
     */
    public $cpu;

    /**
     * PHP memory usage
     *
     * @var int
     */
    public $mu;

    /**
     *  PHP peak memory usage
     *
     * @var int
     */
    public $pmu;


    public $ect;

    /**
     * exclusive time
     *
     * @var int
     */
    public $ewt;

    /**
     * exclusive cpu time
     *
     * @var int
     */
    public $ecpu;

    /**
     * exclusive PHP memory usage
     *
     * @var int
     */
    public $emu;

    /**
     * exclusive PHP peak memory usage
     *
     * @var int
     */
    public $epmu;

    /**
     *
     * @var string
     */
    public $current_function;

    /**
     * @var Record
     */
    public $parent_function;

    public function hasParent()
    {
        return is_null($this->parent_function) === false;
    }
}
