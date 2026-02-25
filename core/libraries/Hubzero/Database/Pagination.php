<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Pagination class
 */
class Pagination
{
    /**
     * Total rows available
     *
     * @var  int
     **/
    public $total;

    /**
     * Pagination starting point
     *
     * @var  int
     **/
    public $start;

    /**
     * Pagination page limit
     *
     * @var  int
     **/
    public $limit;

    /**
     * The HUBzero paginator
     *
     * @var  object
     **/
    private $paginator;

    /**
     * Attempts to forward calls to the paginator itself
     *
     * @param   string  $name       The method name being called
     * @param   array   $arguments  The method arguments provided
     * @return  $this
     **/
    public function __call($name, $arguments)
    {
        // See if we need to call a method on the HUBzero paginator
        if (in_array($name, get_class_methods($this->getPaginator()))) {
            $result = call_user_func_array(array($this->getPaginator(), $name), $arguments);

            // If we got back something other than the class itself, return it
            if (!($result instanceof \Hubzero\Pagination\Paginator)) {
                return $result;
            }
        }

        return $this;
    }

    /**
     * Initializes pagination object
     *
     * @param   string  $namespace  The session state variable namespace
     * @param   int     $total      Total number of records
     * @param   string  $start      The variable name representing the pagination start number
     * @param   string  $limit      The variable name representing the pagination limit number
     * @return  object
     **/
    public static function init($namespace, $total, $start = 'start', $limit = 'limit')
    {
        $instance = new self();

        $instance->total = $total;
        $instance->start = \Hubzero\Facades\Request::getInt(
            $start,
            \Hubzero\Facades\User::getState($namespace . '.start', 0)
        );
        $instance->limit = \Hubzero\Facades\Request::getInt(
            $limit,
            \Hubzero\Facades\User::getState($namespace . '.limit', \Hubzero\Facades\Config::get('list_limit'))
        );

        if ($instance->limit < 0) {
            $instance->limit = \Hubzero\Facades\Config::get('list_limit');
        }
        $instance->start = ($instance->limit != 0)
            ? (int) (floor($instance->start / $instance->limit) * $instance->limit)
            : 0;
        if ($instance->start < 0) {
            $instance->start = 0;
        }

        \Hubzero\Facades\User::setState($namespace . '.start', $instance->start);
        \Hubzero\Facades\User::setState($namespace . '.limit', $instance->limit);

        return $instance;
    }

    /**
     * Returns the html pagination output
     *
     * @return  string
     **/
    public function __toString()
    {
        return $this->getPaginator()->render();
    }

    /**
     * Gets the HUBzero paginator, or creates a new one
     *
     * @return  \Hubzero\Pagination\Paginator
     **/
    protected function getPaginator()
    {
        if (!isset($this->paginator)) {
            $this->paginator = new \Hubzero\Pagination\Paginator($this->total, $this->start, $this->limit);
        }

        return $this->paginator;
    }
}
