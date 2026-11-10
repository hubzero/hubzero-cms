<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * iso-8859-x non-breaking space.
 */
if (!defined('NBSP')) {
    define('NBSP', '&#160;');
}

/**
 * Additions by Axel Boldt follow,
 * partly taken from diff.php, phpwiki-1.3.3
 *
 */
class WordAccumulator
{
    public $lines = '';
    public $line = '';
    public $group = '';
    public $tag = '';

    /**
     * Short description for 'WordAccumulator'
     *
     * Long description (if any) ...
     *
     * @return     void
     */
    public function __construct()
    {
        $this->lines = [];
        $this->line = '';
        $this->group = '';
        $this->tag = '';
    }

    /**
     * Short description for '_flushGroup'
     *
     * Long description (if any) ...
     *
     * @param      unknown $new_tag Parameter description (if any) ...
     * @return     void
     */
    public function flushGroup($new_tag)
    {
        if ($this->group !== '') {
            if ($this->tag == 'ins') {
                $this->line .= '<ins class="diffchange">' . htmlspecialchars($this->group) . '</ins>';
            } elseif ($this->tag == 'del') {
                $this->line .= '<del class="diffchange">' . htmlspecialchars($this->group) . '</del>';
            } else {
                $this->line .= htmlspecialchars($this->group);
            }
        }
        $this->group = '';
        $this->tag = $new_tag;
    }

    /**
     * Short description for '_flushLine'
     *
     * Long description (if any) ...
     *
     * @param      unknown $new_tag Parameter description (if any) ...
     * @return     void
     */
    public function flushLine($new_tag)
    {
        $this->flushGroup($new_tag);
        if ($this->line != '') {
            array_push($this->lines, $this->line);
        } else {
            // make empty lines visible by inserting an NBSP
            array_push($this->lines, NBSP);
        }
        $this->line = '';
    }

    /**
     * Short description for 'addWords'
     *
     * Long description (if any) ...
     *
     * @param      array $words Parameter description (if any) ...
     * @param      string $tag Parameter description (if any) ...
     * @return     void
     */
    public function addWords($words, $tag = '')
    {
        if ($tag != $this->tag) {
            $this->flushGroup($tag);
        }

        foreach ($words as $word) {
            // new-line should only come as first char of word.
            if ($word == '') {
                continue;
            }
            if ($word[0] == "\n") {
                $this->flushLine($tag);
                $word = substr($word, 1);
            }
            assert(!strstr($word, "\n"));
            $this->group .= $word;
        }
    }

    /**
     * Short description for 'getLines'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    public function getLines()
    {
        $this->flushLine('~done');
        return $this->lines;
    }
}
