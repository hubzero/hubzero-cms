<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Constant to indicate diff cache compatibility.
 * Bump this when changing the diff formatting in a way that
 * fixes important bugs or such to force cached diff views to
 * clear.
 */
define('MW_DIFF_VERSION', '1.11a');

/**
 * A PHP diff engine for phpwiki. (Taken from phpwiki-1.3.3)
 *
 * Copyright (C) 2000, 2001 Geoffrey T. Dairiki <dairiki@dairiki.org>
 * You may copy this code freely under the conditions of the GPL.
 */
define('USE_ASSERTS', function_exists('assert'));

// Operations
require_once __DIR__ . '/Diff/_DiffOp.php';
require_once __DIR__ . '/Diff/_DiffOp_Add.php';
require_once __DIR__ . '/Diff/_DiffOp_Change.php';
require_once __DIR__ . '/Diff/_DiffOp_Copy.php';
require_once __DIR__ . '/Diff/_DiffOp_Delete.php';
require_once __DIR__ . '/Diff/_DiffEngine.php';
require_once __DIR__ . '/Diff/_HWLDF_WordAccumulator.php';

require_once __DIR__ . '/Diff/MappedDiff.php';
require_once __DIR__ . '/Diff/WordLevelDiff.php';

// Formatters
require_once __DIR__ . '/Diff/DiffFormatter.php';
require_once __DIR__ . '/Diff/DivDiffFormatter.php';
require_once __DIR__ . '/Diff/TableDiffFormatter.php';

/**
 * Class representing a 'diff' between two sequences of strings.
 */
class Diff
{
	/**
	 * Description for 'edits'
	 *
	 * @var  array
	 */
	public $edits;

	/**
	 * Constructor.
	 * Computes diff between sequences of strings.
	 *
	 * @param   array  $from_lines  An array of strings. (Typically these are lines from a file.)
	 * @param   array  $to_lines  An array of strings.
	 * @return  void
	 */
	public function __construct($from_lines, $to_lines)
	{
		$eng = new _DiffEngine;
		$this->edits = $eng->diff($from_lines, $to_lines);
		//$this->_check($from_lines, $to_lines);
	}

	/**
	 * Compute reversed Diff.
	 *
	 * SYNOPSIS:
	 *
	 *    $diff = new Diff($lines1, $lines2);
	 *    $rev = $diff->reverse();
	 *
	 * @return  object  A Diff object representing the inverse of the original diff.
	 */
	public function reverse()
	{
		$rev = $this;
		$rev->edits = array();
		foreach ($this->edits as $edit)
		{
			$rev->edits[] = $edit->reverse();
		}
		return $rev;
	}

	/**
	 * Check for empty diff.
	 *
	 * @return  bool  True iff two sequences were identical.
	 */
	public function isEmpty()
	{
		foreach ($this->edits as $edit)
		{
			if ($edit->type != 'copy')
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Compute the length of the Longest Common Subsequence (LCS).
	 *
	 * This is mostly for diagnostic purposed.
	 *
	 * @return  integer  The length of the LCS.
	 */
	public function lcs()
	{
		$lcs = 0;
		foreach ($this->edits as $edit)
		{
			if ($edit->type == 'copy')
			{
				$lcs += count($edit->orig);
			}
		}
		return $lcs;
	}

	/**
	 * Get the original set of lines.
	 *
	 * This reconstructs the $from_lines parameter passed to the constructor.
	 *
	 * @return  array  The original sequence of strings.
	 */
	public function orig()
	{
		$lines = array();

		foreach ($this->edits as $edit)
		{
			if ($edit->orig)
			{
				array_splice($lines, count($lines), 0, $edit->orig);
			}
		}
		return $lines;
	}

	/**
	 * Get the closing set of lines.
	 *
	 * This reconstructs the $to_lines parameter passed to the constructor.
	 *
	 * @return  array  The sequence of strings.
	 */
	public function closing()
	{
		$lines = array();

		foreach ($this->edits as $edit)
		{
			if ($edit->closing)
			{
				array_splice($lines, count($lines), 0, $edit->closing);
			}
		}
		return $lines;
	}

	/**
	 * Check a Diff for validity.
	 *
	 * This is here only for debugging purposes.
	 *
	 * @param   unknown $from_lines
	 * @param   unknown $to_lines
	 * @return  void
	 */
	public function _check($from_lines, $to_lines)
	{
		if (serialize($from_lines) != serialize($this->orig()))
		{
			trigger_error("Reconstructed original doesn't match", E_USER_ERROR);
		}
		if (serialize($to_lines) != serialize($this->closing()))
		{
			trigger_error("Reconstructed closing doesn't match", E_USER_ERROR);
		}

		$rev = $this->reverse();
		if (serialize($to_lines) != serialize($rev->orig()))
		{
			trigger_error("Reversed original doesn't match", E_USER_ERROR);
		}
		if (serialize($from_lines) != serialize($rev->closing()))
		{
			trigger_error("Reversed closing doesn't match", E_USER_ERROR);
		}

		$prevtype = 'none';
		foreach ($this->edits as $edit)
		{
			if ($prevtype == $edit->type)
			{
				trigger_error("Edit sequence is non-optimal", E_USER_ERROR);
			}
			$prevtype = $edit->type;
		}

		$lcs = $this->lcs();
		trigger_error('Diff okay: LCS = '.$lcs, E_USER_NOTICE);
	}
}
