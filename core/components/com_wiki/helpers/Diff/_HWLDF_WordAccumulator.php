<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * iso-8859-x non-breaking space.
 */
define('NBSP', '&#160;');

/**
 * Additions by Axel Boldt follow,
 * partly taken from diff.php, phpwiki-1.3.3
 */
class _HWLDF_WordAccumulator
{
	var $_lines = '';
	var $_line = '';
	var $_group = '';
	var $_tag = '';

	/**
	 * Short description for '_HWLDF_WordAccumulator'
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
		if ($this->_group !== '')
		{
			if ($this->_tag == 'ins')
			{
				$this->_line .= '<ins class="diffchange">' . htmlspecialchars($this->_group) . '</ins>';
			}
			elseif ($this->_tag == 'del')
			{
				$this->_line .= '<del class="diffchange">' . htmlspecialchars($this->_group) . '</del>';
			}
			else
			{
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
		if ($this->_line != '')
		{
			array_push($this->_lines, $this->_line);
		}
		else
		{
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
		if ($tag != $this->_tag)
		{
			$this->_flushGroup($tag);
		}

		foreach ($words as $word)
		{
			// new-line should only come as first char of word.
			if ($word == '')
			{
				continue;
			}
			if ($word[0] == "\n")
			{
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
