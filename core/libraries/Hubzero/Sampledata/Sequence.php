<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Sampledata;

/**
 * Numbers in a fixed order
 *
 * Sample content wants variety without surprise: a hub built twice from the
 * same pack should come out the same, so that a screenshot taken today matches
 * the one taken next month. Every choice a step makes comes from here, and
 * here the numbers are drawn from a seed made of the step's own name.
 **/
class Sequence
{
    /**
     * Where the sequence has got to
     *
     * @var  int
     */
    private $state;

    /**
     * Start a sequence
     *
     * @param   string  $seed  What the numbers are drawn from
     * @return  void
     */
    public function __construct($seed = '')
    {
        // A stable seed, so the same name always gives the same numbers
        $this->state = (int) hexdec(substr(md5((string) $seed), 0, 8));
    }

    /**
     * The next number, between two bounds
     *
     * @param   int  $min  The lowest number to return
     * @param   int  $max  The highest number to return
     * @return  int
     */
    public function between($min, $max)
    {
        $min = (int) $min;
        $max = (int) $max;

        if ($max <= $min) {
            return $min;
        }

        return $min + ($this->next() % (($max - $min) + 1));
    }

    /**
     * One of the given values
     *
     * @param   array  $values  What to choose from
     * @return  mixed  Null when there is nothing to choose
     */
    public function pick(array $values)
    {
        if (empty($values)) {
            return null;
        }

        $values = array_values($values);

        return $values[$this->between(0, count($values) - 1)];
    }

    /**
     * Some of the given values, in the order they were given
     *
     * @param   array  $values  What to choose from
     * @param   int    $count   How many to take
     * @return  array
     */
    public function take(array $values, $count)
    {
        $values = array_values($values);
        $count  = max(0, min((int) $count, count($values)));
        $keys   = array_keys($values);

        $chosen = [];

        while (count($chosen) < $count) {
            $key = $this->pick($keys);
            $chosen[] = $key;
            $keys = array_values(array_diff($keys, [$key]));
        }

        sort($chosen);

        return array_map(function ($key) use ($values) {
            return $values[$key];
        }, $chosen);
    }

    /**
     * The values, in an order of this sequence's choosing
     *
     * @param   array  $values  What to shuffle
     * @return  array
     */
    public function shuffle(array $values)
    {
        $values = array_values($values);

        for ($i = count($values) - 1; $i > 0; $i--) {
            $j = $this->between(0, $i);
            $swap = $values[$i];
            $values[$i] = $values[$j];
            $values[$j] = $swap;
        }

        return $values;
    }

    /**
     * A date, so many days either side of a given one
     *
     * @param   string  $around  The date to work from
     * @param   int     $spread  How many days either side to reach
     * @return  string  The date, ready for the database
     */
    public function dateNear($around, $spread = 180)
    {
        $base = strtotime($around) ?: time();
        $days = $this->between(-abs((int) $spread), abs((int) $spread));

        return gmdate('Y-m-d H:i:s', $base + ($days * 86400) + $this->between(0, 86399));
    }

    /**
     * Draw the next number
     *
     * A linear congruential generator: small, and the same everywhere PHP
     * runs, which the built-in generators are not required to be.
     *
     * @return  int
     */
    private function next()
    {
        $this->state = (($this->state * 1103515245) + 12345) & 0x7FFFFFFF;

        return $this->state;
    }
}
