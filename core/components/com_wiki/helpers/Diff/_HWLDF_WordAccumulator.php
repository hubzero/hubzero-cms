<?php

// phpcs:disable PSR1.Files.SideEffects

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
 * @phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
 * @phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
 */
class WordAccumulator
{
    public $_lines = '';
    public $_line = '';
    public $_group = '';
    public $_tag = '';

    /**
     * Short description for 'WordAccumulator'
     *
     * Long description (if any) ...
     *
     * @return     void
     */
    public function __construct()
    {
        $this->_lines = array();
        $this->_line = '';
        $this->_group = '';
        $this->_tag = '';
    }

    /**
     * Short description for '_flushGroup'
     *
     * Long description (if any) ...
     *
     * @param      unknown $new_tag Parameter description (if any) ...
     * @return     void
     */
    public function _flushGroup($new_tag)
    {
        if ($this->_group !== '') {
            if ($this->_tag == 'ins') {
                $this->_line .= '<ins class="diffchange">' . htmlspecialchars($this->_group) . '</ins>';
            } elseif ($this->_tag == 'del') {
                $this->_line .= '<del class="diffchange">' . htmlspecialchars($this->_group) . '</del>';
            } else {
                $this->_line .= htmlspecialchars($this->_group);
            }
        }
        $this->_group = '';
        $this->_tag = $new_tag;
    }

    /**
     * Short description for '_flushLine'
     *
     * Long description (if any) ...
     *
     * @param      unknown $new_tag Parameter description (if any) ...
     * @return     void
     */
    public function _flushLine($new_tag)
    {
        $this->_flushGroup($new_tag);
        if ($this->_line != '') {
            array_push($this->_lines, $this->_line);
        } else {
            // make empty lines visible by inserting an NBSP
            array_push($this->_lines, NBSP);
        }
        $this->_line = '';
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
        if ($tag != $this->_tag) {
            $this->_flushGroup($tag);
        }

        foreach ($words as $word) {
            // new-line should only come as first char of word.
            if ($word == '') {
                continue;
            }
            if ($word[0] == "\n") {
                $this->_flushLine($tag);
                $word = substr($word, 1);
            }
            assert(!strstr($word, "\n"));
            $this->_group .= $word;
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
        $this->_flushLine('~done');
        return $this->_lines;
    }
}
