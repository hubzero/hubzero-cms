<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

/**
 * Courses model class for a course
 */
class Iterator implements \Countable, \Iterator
{
    /**
     * Container for properties
     *
     * @var int
     */
    private $pos = 0;

    /**
     * Container for properties
     *
     * @var int
     */
    private $total = 0;

    /**
     * Container for properties
     *
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param      array $data Array of data
     * @return     void
     */
    public function __construct($data = null)
    {
        if (is_array($data)) {
            $this->data = $data;
        }
        $this->total = count($this->data);
    }

    /**
     * Add item to the array
     *
     * @param      mixed $value
     * @return     void
     */
    public function add($value)
    {
        $this->data[$this->total++] = $value;
    }

    /**
     * Reset cursor to starting point
     *
     * @return     void
     */

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->pos = 0;
    }

    /**
     * Fetch an array value
     * Accepts either a numerical index value or string for
     * previous or next record based on current cursor position
     *
     * @param      mixed $key Numeric index or string [first, last, prev, next]
     * @return     mixed
     */
    public function fetch($key)
    {
        // Get cursor position
        $cur = $this->pos;
        $res = null;
        // Is it a number?
        if (is_numeric($key)) {
            // Set the cursor
            $this->pos = $key;
            // Get current item
            $res = $this->current();
        } elseif (is_string($key)) {
            // Is it a string?
            $key = strtolower(trim($key));
            switch ($key) {
                // First record
                case 'first':
                    $this->first();
                    $res = $this->current();
                    break;
                    // Previous record from the cursor point
                case 'prev':
                    $this->prev();
                    $res = $this->current();
                    break;
                    // Next record from the cursor point
                case 'next':
                    $this->next();
                    $res = $this->current();
                    break;
                    // Last record
                case 'last':
                    $this->last();
                    $res = $this->current();
                    break;
                    // FAIL!
                default:
                    $res = null;
                    break;
            }
        }
        // Reset cursor to starting position
        $this->pos = $cur;
        // Return result
        return $res;
    }

    /**
     * Is the current position the first one?
     *
     * @return     bool
     */
    public function isFirst()
    {
        return !isset($this->data[$this->pos - 1]);
    }

    /**
     * Is the current position the last one?
     *
     * @return     bool
     */
    public function isLast()
    {
        return !isset($this->data[$this->pos + 1]);
    }

    /**
     * Return the current array value if the cursor is at
     * a valid index
     *
     * @return     mixed
     */

    #[\ReturnTypeWillChange]
    public function current()
    {
        if ($this->valid()) {
            return $this->data[$this->pos];
        }
        return null;
    }

    /**
     * Return the array count
     *
     * @return array
     */
    public function total()
    {
        return $this->total;
    }

    /**
     * Return the array count
     *
     * @return array
     */

    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->total;
    }

    /**
     * Return the first array value
     *
     * @return void
     */

    #[\ReturnTypeWillChange]
    public function first()
    {
        $this->rewind();
    }

    /**
     * Return the last array value
     *
     * @return  void
     */
    #[\ReturnTypeWillChange]
    public function last()
    {
        $this->pos = ($this->total - 1);
    }

    /**
     * Return the key for the current cursor position
     * @param      integer $idx current cursor position
     * @return     mixed
     */

    #[\ReturnTypeWillChange]
    public function key($idx = null)
    {
        if ($idx !== null) {
            $old = $this->pos;
            $this->pos = (int) $idx;
            if (!$this->valid()) {
                $this->pos = $old;
            }
        }
        return $this->pos;
    }

    /**
     * Set cursor position to previous position and return array value
     *
     * @return void
     */

    #[\ReturnTypeWillChange]
    public function prev()
    {
        --$this->pos;
    }

    /**
     * Set cursor position to next position and return array value
     *
     * @return void
     */

    #[\ReturnTypeWillChange]
    public function next()
    {
        ++$this->pos;
    }

    /**
     * Check if the current cursor position is valid
     *
     * @return bool
     */

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset($this->data[$this->pos]);
    }
}
