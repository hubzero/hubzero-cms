<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * Class to perform replacement based on a simple hashtable lookup
 */
class HashtableReplacer extends Replacer
{
    /**
     * Description for 'table'
     *
     * @var array<mixed>
     */
    public $table;
    /**
     * @var  int
     */
    public $index;

    /**
     * Short description for '__construct'
     *
     * Long description (if any) ...
     *
     * @param      mixed $table Parameter description (if any) ...
     * @param      integer $index Parameter description (if any) ...
     * @return     void
     */
    public function __construct($table, $index = 0)
    {
        $this->table = $table;
        $this->index = $index;
    }

    /**
     * Short description for 'replace'
     *
     * Long description (if any) ...
     *
     * @param      array<mixed> $matches Parameter description (if any) ...
     * @return     array<mixed> Return description (if any) ...
     */
    public function replace($matches)
    {
        return $this->table[$matches[$this->index]];
    }
}
