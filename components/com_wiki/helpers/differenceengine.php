<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Constant to indicate diff cache compatibility.
// Bump this when changing the diff formatting in a way that
// fixes important bugs or such to force cached diff views to
// clear.

/**
 * Description for ''MW_DIFF_VERSION''
 */
define( 'MW_DIFF_VERSION', '1.11a' );

// A PHP diff engine for phpwiki. (Taken from phpwiki-1.3.3)
//
// Copyright (C) 2000, 2001 Geoffrey T. Dairiki <dairiki@dairiki.org>
// You may copy this code freely under the conditions of the GPL.

/**
 * Description for ''USE_ASSERTS''
 */
define('USE_ASSERTS', function_exists('assert'));

/**
 * Short description for '_DiffOp'
 *
 * Long description (if any) ...
 */
class _DiffOp
{

	/**
	 * Description for 'type'
	 *
	 * @var unknown
	 */
	var $type;

	/**
	 * Description for 'orig'
	 *
	 * @var unknown
	 */
	var $orig;

	/**
	 * Description for 'closing'
	 *
	 * @var unknown
	 */
	var $closing;

	/**
	 * Short description for 'reverse'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function reverse()
	{
		trigger_error('pure virtual', E_USER_ERROR);
	}

	/**
	 * Short description for 'norig'
	 *
	 * Long description (if any) ...
	 *
	 * @return     integer Return description (if any) ...
	 */
	function norig()
	{
		return $this->orig ? sizeof($this->orig) : 0;
	}

	/**
	 * Short description for 'nclosing'
	 *
	 * Long description (if any) ...
	 *
	 * @return     integer Return description (if any) ...
	 */
	function nclosing()
	{
		return $this->closing ? sizeof($this->closing) : 0;
	}
}

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class _DiffOp_Copy extends _DiffOp
{

	/**
	 * Description for 'type'
	 *
	 * @var string
	 */
	var $type = 'copy';

	/**
	 * Short description for '_DiffOp_Copy'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $orig Parameter description (if any) ...
	 * @param      boolean $closing Parameter description (if any) ...
	 * @return     void
	 */
	function _DiffOp_Copy ($orig, $closing = false)
	{
		if (!is_array($closing))
			$closing = $orig;
		$this->orig = $orig;
		$this->closing = $closing;
	}

	/**
	 * Short description for 'reverse'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	function reverse()
	{
		return new _DiffOp_Copy($this->closing, $this->orig);
	}
}

/**
 * Short description for '_DiffOp_Delete'
 *
 * Long description (if any) ...
 */
class _DiffOp_Delete extends _DiffOp
{

	/**
	 * Description for 'type'
	 *
	 * @var string
	 */
	var $type = 'delete';

	/**
	 * Short description for '_DiffOp_Delete'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _DiffOp_Delete ($lines)
	{
		$this->orig = $lines;
		$this->closing = false;
	}

	/**
	 * Short description for 'reverse'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	function reverse()
	{
		return new _DiffOp_Add($this->orig);
	}
}

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class _DiffOp_Add extends _DiffOp
{

	/**
	 * Description for 'type'
	 *
	 * @var string
	 */
	var $type = 'add';

	/**
	 * Short description for '_DiffOp_Add'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _DiffOp_Add ($lines)
	{
		$this->closing = $lines;
		$this->orig = false;
	}

	/**
	 * Short description for 'reverse'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	function reverse()
	{
		return new _DiffOp_Delete($this->closing);
	}
}

/**
 * Short description for '_DiffOp_Change'
 *
 * Long description (if any) ...
 */
class _DiffOp_Change extends _DiffOp
{

	/**
	 * Description for 'type'
	 *
	 * @var string
	 */
	var $type = 'change';

	/**
	 * Short description for '_DiffOp_Change'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $orig Parameter description (if any) ...
	 * @param      unknown $closing Parameter description (if any) ...
	 * @return     void
	 */
	function _DiffOp_Change ($orig, $closing)
	{
		$this->orig = $orig;
		$this->closing = $closing;
	}

	/**
	 * Short description for 'reverse'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	function reverse()
	{
		return new _DiffOp_Change($this->closing, $this->orig);
	}
}

//-------------------------------------------------------------
// Class used internally by Diff to actually compute the diffs.
//
// The algorithm used here is mostly lifted from the perl module
// Algorithm::Diff (version 1.06) by Ned Konz, which is available at:
//	 http://www.perl.com/CPAN/authors/id/N/NE/NEDKONZ/Algorithm-Diff-1.06.zip
//
// More ideas are taken from:
//	 http://www.ics.uci.edu/~eppstein/161/960229.html
//
// Some ideas are (and a bit of code) are from from analyze.c, from GNU
// diffutils-2.7, which can be found at:
//	 ftp://gnudist.gnu.org/pub/gnu/diffutils/diffutils-2.7.tar.gz
//
// closingly, some ideas (subdivision by NCHUNKS > 2, and some optimizations)
// are my own.
//
// Line length limits for robustness added by Tim Starling, 2005-08-31
//
// @author Geoffrey T. Dairiki, Tim Starling
// @private
// @addtogroup DifferenceEngine
//-------------------------------------------------------------

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class _DiffEngine
{

	/**
	 * Description for 'AX_XREF_LENGTH'
	 */
	const MAX_XREF_LENGTH =  10000;

	/**
	 * Short description for 'diff'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $from_lines Parameter description (if any) ...
	 * @param      array $to_lines Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function diff ($from_lines, $to_lines)
	{
		$n_from = sizeof($from_lines);
		$n_to = sizeof($to_lines);

		$this->xchanged = $this->ychanged = array();
		$this->xv = $this->yv = array();
		$this->xind = $this->yind = array();
		unset($this->seq);
		unset($this->in_seq);
		unset($this->lcs);

		// Skip leading common lines.
		for ($skip = 0; $skip < $n_from && $skip < $n_to; $skip++) {
			if ($from_lines[$skip] !== $to_lines[$skip])
				break;
			$this->xchanged[$skip] = $this->ychanged[$skip] = false;
		}
		// Skip trailing common lines.
		$xi = $n_from; $yi = $n_to;
		for ($endskip = 0; --$xi > $skip && --$yi > $skip; $endskip++) {
			if ($from_lines[$xi] !== $to_lines[$yi])
				break;
			$this->xchanged[$xi] = $this->ychanged[$yi] = false;
		}

		// Ignore lines which do not exist in both files.
		for ($xi = $skip; $xi < $n_from - $endskip; $xi++) {
			$xhash[$this->_line_hash($from_lines[$xi])] = 1;
		}

		for ($yi = $skip; $yi < $n_to - $endskip; $yi++) {
			$line = $to_lines[$yi];
			if ( ($this->ychanged[$yi] = empty($xhash[$this->_line_hash($line)])) )
				continue;
			$yhash[$this->_line_hash($line)] = 1;
			$this->yv[] = $line;
			$this->yind[] = $yi;
		}
		for ($xi = $skip; $xi < $n_from - $endskip; $xi++) {
			$line = $from_lines[$xi];
			if ( ($this->xchanged[$xi] = empty($yhash[$this->_line_hash($line)])) )
				continue;
			$this->xv[] = $line;
			$this->xind[] = $xi;
		}

		// Find the LCS.
		$this->_compareseq(0, sizeof($this->xv), 0, sizeof($this->yv));

		// Merge edits when possible
		$this->_shift_boundaries($from_lines, $this->xchanged, $this->ychanged);
		$this->_shift_boundaries($to_lines, $this->ychanged, $this->xchanged);

		// Compute the edit operations.
		$edits = array();
		$xi = $yi = 0;
		while ($xi < $n_from || $yi < $n_to) {
			USE_ASSERTS && assert($yi < $n_to || $this->xchanged[$xi]);
			USE_ASSERTS && assert($xi < $n_from || $this->ychanged[$yi]);

			// Skip matching "snake".
			$copy = array();
			while ( $xi < $n_from && $yi < $n_to
					&& !$this->xchanged[$xi] && !$this->ychanged[$yi]) {
				$copy[] = $from_lines[$xi++];
				++$yi;
			}
			if ($copy)
				$edits[] = new _DiffOp_Copy($copy);

			// Find deletes & adds.
			$delete = array();
			while ($xi < $n_from && $this->xchanged[$xi])
				$delete[] = $from_lines[$xi++];

			$add = array();
			while ($yi < $n_to && $this->ychanged[$yi])
				$add[] = $to_lines[$yi++];

			if ($delete && $add)
				$edits[] = new _DiffOp_Change($delete, $add);
			elseif ($delete)
				$edits[] = new _DiffOp_Delete($delete);
			elseif ($add)
				$edits[] = new _DiffOp_Add($add);
		}

		return $edits;
	}

	// Returns the whole line if it's small enough, or the MD5 hash otherwise

	/**
	 * Short description for '_line_hash'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	function _line_hash( $line )
	{
		if ( strlen( $line ) > self::MAX_XREF_LENGTH ) {
			return md5( $line );
		} else {
			return $line;
		}
	}

	// Divide the Largest Common Subsequence (LCS) of the sequences
	// [XOFF, XLIM) and [YOFF, YLIM) into NCHUNKS approximately equally
	// sized segments.
	//
	// Returns (LCS, PTS).	LCS is the length of the LCS. PTS is an
	// array of NCHUNKS+1 (X, Y) indexes giving the diving points between
	// sub sequences.  The first sub-sequence is contained in [X0, X1),
	// [Y0, Y1), the second in [X1, X2), [Y1, Y2) and so on.  Note
	// that (X0, Y0) == (XOFF, YOFF) and
	// (X[NCHUNKS], Y[NCHUNKS]) == (XLIM, YLIM).
	//
	// This function assumes that the first lines of the specified portions
	// of the two files do not match, and likewise that the last lines do not
	// match.  The caller must trim matching lines from the beginning and end
	// of the portions it is going to specify.

	/**
	 * Short description for '_diag'
	 *
	 * Long description (if any) ...
	 *
	 * @param      number $xoff Parameter description (if any) ...
	 * @param      number $xlim Parameter description (if any) ...
	 * @param      number $yoff Parameter description (if any) ...
	 * @param      number $ylim Parameter description (if any) ...
	 * @param      number $nchunks Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	function _diag ($xoff, $xlim, $yoff, $ylim, $nchunks)
	{
		$flip = false;

		if ($xlim - $xoff > $ylim - $yoff) {
			// Things seems faster (I'm not sure I understand why)
				// when the shortest sequence in X.
				$flip = true;
			list ($xoff, $xlim, $yoff, $ylim)
			= array( $yoff, $ylim, $xoff, $xlim);
		}

		if ($flip)
			for ($i = $ylim - 1; $i >= $yoff; $i--)
				$ymatches[$this->xv[$i]][] = $i;
		else
			for ($i = $ylim - 1; $i >= $yoff; $i--)
				$ymatches[$this->yv[$i]][] = $i;

		$this->lcs = 0;
		$this->seq[0]= $yoff - 1;
		$this->in_seq = array();
		$ymids[0] = array();

		$numer = $xlim - $xoff + $nchunks - 1;
		$x = $xoff;
		for ($chunk = 0; $chunk < $nchunks; $chunk++)
		{
			if ($chunk > 0)
				for ($i = 0; $i <= $this->lcs; $i++)
					$ymids[$i][$chunk-1] = $this->seq[$i];

			$x1 = $xoff + (int)(($numer + ($xlim-$xoff)*$chunk) / $nchunks);
			for ( ; $x < $x1; $x++)
			{
				$line = $flip ? $this->yv[$x] : $this->xv[$x];
					if (empty($ymatches[$line]))
						continue;
				$matches = $ymatches[$line];
				reset($matches);
				while (list ($junk, $y) = each($matches))
					if (empty($this->in_seq[$y])) {
						$k = $this->_lcs_pos($y);
						USE_ASSERTS && assert($k > 0);
						$ymids[$k] = $ymids[$k-1];
						break;
					}
				while (list ( /* $junk */, $y) = each($matches)) {
					if ($y > $this->seq[$k-1]) {
						USE_ASSERTS && assert($y < $this->seq[$k]);
						// Optimization: this is a common case:
						//	next match is just replacing previous match.
						$this->in_seq[$this->seq[$k]] = false;
						$this->seq[$k] = $y;
						$this->in_seq[$y] = 1;
					} else if (empty($this->in_seq[$y])) {
						$k = $this->_lcs_pos($y);
						USE_ASSERTS && assert($k > 0);
						$ymids[$k] = $ymids[$k-1];
					}
				}
			}
		}

		$seps[] = $flip ? array($yoff, $xoff) : array($xoff, $yoff);
		$ymid = $ymids[$this->lcs];
		for ($n = 0; $n < $nchunks - 1; $n++) {
			$x1 = $xoff + (int)(($numer + ($xlim - $xoff) * $n) / $nchunks);
			$y1 = $ymid[$n] + 1;
			$seps[] = $flip ? array($y1, $x1) : array($x1, $y1);
		}
		$seps[] = $flip ? array($ylim, $xlim) : array($xlim, $ylim);

		return array($this->lcs, $seps);
	}

	/**
	 * Short description for '_lcs_pos'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $ypos Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	function _lcs_pos ($ypos)
	{
		$end = $this->lcs;
		if ($end == 0 || $ypos > $this->seq[$end]) {
			$this->seq[++$this->lcs] = $ypos;
			$this->in_seq[$ypos] = 1;
			return $this->lcs;
		}

		$beg = 1;
		while ($beg < $end) {
			$mid = (int)(($beg + $end) / 2);
			if ( $ypos > $this->seq[$mid] )
				$beg = $mid + 1;
			else
				$end = $mid;
		}

		USE_ASSERTS && assert($ypos != $this->seq[$end]);

		$this->in_seq[$this->seq[$end]] = false;
		$this->seq[$end] = $ypos;
		$this->in_seq[$ypos] = 1;

		return $end;
	}

	// Find LCS of two sequences.
	//
	// The results are recorded in the vectors $this->{x,y}changed[], by
	// storing a 1 in the element for each line that is an insertion
	// or deletion (ie. is not in the LCS).
	//
	// The subsequence of file 0 is [XOFF, XLIM) and likewise for file 1.
	//
	// Note that XLIM, YLIM are exclusive bounds.
	// All line numbers are origin-0 and discarded lines are not counted.

	/**
	 * Short description for '_compareseq'
	 *
	 * Long description (if any) ...
	 *
	 * @param      number $xoff Parameter description (if any) ...
	 * @param      number $xlim Parameter description (if any) ...
	 * @param      number $yoff Parameter description (if any) ...
	 * @param      number $ylim Parameter description (if any) ...
	 * @return     void
	 */
	function _compareseq ($xoff, $xlim, $yoff, $ylim)
	{
		// Slide down the bottom initial diagonal.
		while ($xoff < $xlim && $yoff < $ylim
			   && $this->xv[$xoff] == $this->yv[$yoff]) {
			++$xoff;
			++$yoff;
		}

		// Slide up the top initial diagonal.
		while ($xlim > $xoff && $ylim > $yoff
			   && $this->xv[$xlim - 1] == $this->yv[$ylim - 1]) {
			--$xlim;
			--$ylim;
		}

		if ($xoff == $xlim || $yoff == $ylim)
			$lcs = 0;
		else {
			// This is ad hoc but seems to work well.
			//$nchunks = sqrt(min($xlim - $xoff, $ylim - $yoff) / 2.5);
			//$nchunks = max(2,min(8,(int)$nchunks));
			$nchunks = min(7, $xlim - $xoff, $ylim - $yoff) + 1;
			list ($lcs, $seps)
			= $this->_diag($xoff,$xlim,$yoff, $ylim,$nchunks);
		}

		if ($lcs == 0) {
			// X and Y sequences have no common subsequence:
			// mark all changed.
			while ($yoff < $ylim)
				$this->ychanged[$this->yind[$yoff++]] = 1;
			while ($xoff < $xlim)
				$this->xchanged[$this->xind[$xoff++]] = 1;
		} else {
			// Use the partitions to split this problem into subproblems.
			reset($seps);
			$pt1 = $seps[0];
			while ($pt2 = next($seps)) {
				$this->_compareseq ($pt1[0], $pt2[0], $pt1[1], $pt2[1]);
				$pt1 = $pt2;
			}
		}
	}

	// Adjust inserts/deletes of identical lines to join changes
	// as much as possible.
	//
	// We do something when a run of changed lines include a
	// line at one end and has an excluded, identical line at the other.
	// We are free to choose which identical line is included.
	// `compareseq' usually chooses the one at the beginning,
	// but usually it is cleaner to consider the following identical line
	// to be the "change".
	//
	// This is extracted verbatim from analyze.c (GNU diffutils-2.7).

	/**
	 * Short description for '_shift_boundaries'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $lines Parameter description (if any) ...
	 * @param      array &$changed Parameter description (if any) ...
	 * @param      array $other_changed Parameter description (if any) ...
	 * @return     void
	 */
	function _shift_boundaries ($lines, &$changed, $other_changed)
	{
		$i = 0;
		$j = 0;

		USE_ASSERTS && assert('sizeof($lines) == sizeof($changed)');
		$len = sizeof($lines);
		$other_len = sizeof($other_changed);

		while (1)
		{
			// Scan forwards to find beginning of another run of changes.
			// Also keep track of the corresponding point in the other file.
			//
			// Throughout this code, $i and $j are adjusted together so that
			// the first $i elements of $changed and the first $j elements
			// of $other_changed both contain the same number of zeros
			// (unchanged lines).
			// Furthermore, $j is always kept so that $j == $other_len or
			// $other_changed[$j] == false.
			while ($j < $other_len && $other_changed[$j])
				$j++;

			while ($i < $len && ! $changed[$i]) {
				USE_ASSERTS && assert('$j < $other_len && ! $other_changed[$j]');
				$i++; $j++;
				while ($j < $other_len && $other_changed[$j])
					$j++;
			}

			if ($i == $len)
				break;

			$start = $i;

			// Find the end of this run of changes.
			while (++$i < $len && $changed[$i])
				continue;

			do {
				// Record the length of this run of changes, so that
				// we can later determine whether the run has grown.
				$runlength = $i - $start;

				// Move the changed region back, so long as the
				// previous unchanged line matches the last changed one.
				// This merges with previous changed regions.
				while ($start > 0 && $lines[$start - 1] == $lines[$i - 1])
				{
					$changed[--$start] = 1;
					$changed[--$i] = false;
					while ($start > 0 && $changed[$start - 1])
						$start--;
					USE_ASSERTS && assert('$j > 0');
					while ($other_changed[--$j])
						continue;
					USE_ASSERTS && assert('$j >= 0 && !$other_changed[$j]');
				}

				// Set CORRESPONDING to the end of the changed run, at the last
				// point where it corresponds to a changed run in the other file.
				// CORRESPONDING == LEN means no such point has been found.
				$corresponding = $j < $other_len ? $i : $len;

				// Move the changed region forward, so long as the
				// first changed line matches the following unchanged one.
				// This merges with following changed regions.
				// Do this second, so that if there are no merges,
				// the changed region is moved forward as far as possible.
				while ($i < $len && $lines[$start] == $lines[$i])
				{
					$changed[$start++] = false;
					$changed[$i++] = 1;
					while ($i < $len && $changed[$i])
						$i++;

					USE_ASSERTS && assert('$j < $other_len && ! $other_changed[$j]');
					$j++;
					if ($j < $other_len && $other_changed[$j]) {
						$corresponding = $i;
						while ($j < $other_len && $other_changed[$j])
							$j++;
					}
				}
			} while ($runlength != $i - $start);

			// If possible, move the fully-merged run of changes
			// back to a corresponding run in the other file.
			while ($corresponding < $i)
			{
				$changed[--$start] = 1;
				$changed[--$i] = 0;
				USE_ASSERTS && assert('$j > 0');
				while ($other_changed[--$j])
					continue;
				USE_ASSERTS && assert('$j >= 0 && !$other_changed[$j]');
			}
		}
	}
}

//-------------------------------------------------------------
// Class representing a 'diff' between two sequences of strings.
//-------------------------------------------------------------

/**
 * Short description for 'Diff'
 *
 * Long description (if any) ...
 */
class Diff
{

	/**
	 * Description for 'edits'
	 *
	 * @var array
	 */
	var $edits;

	// Constructor.
	// Computes diff between sequences of strings.
	//
	// @param $from_lines array An array of strings.
	//		  (Typically these are lines from a file.)
	// @param $to_lines array An array of strings.

	/**
	 * Short description for 'Diff'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $from_lines Parameter description (if any) ...
	 * @param      unknown $to_lines Parameter description (if any) ...
	 * @return     void
	 */
	function Diff($from_lines, $to_lines)
	{
		$eng = new _DiffEngine;
		$this->edits = $eng->diff($from_lines, $to_lines);
		//$this->_check($from_lines, $to_lines);
	}

	// Compute reversed Diff.
	//
	// SYNOPSIS:
	//
	//	$diff = new Diff($lines1, $lines2);
	//	$rev = $diff->reverse();
	// @return object A Diff object representing the inverse of the
	//				  original diff.

	/**
	 * Short description for 'reverse'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	function reverse()
	{
		$rev = $this;
		$rev->edits = array();
		foreach ($this->edits as $edit) {
			$rev->edits[] = $edit->reverse();
		}
		return $rev;
	}

	// Check for empty diff.
	//
	// @return bool True iff two sequences were identical.

	/**
	 * Short description for 'isEmpty'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	function isEmpty()
	{
		foreach ($this->edits as $edit) {
			if ($edit->type != 'copy')
				return false;
		}
		return true;
	}

	// Compute the length of the Longest Common Subsequence (LCS).
	//
	// This is mostly for diagnostic purposed.
	//
	// @return int The length of the LCS.

	/**
	 * Short description for 'lcs'
	 *
	 * Long description (if any) ...
	 *
	 * @return     integer Return description (if any) ...
	 */
	function lcs()
	{
		$lcs = 0;
		foreach ($this->edits as $edit) {
			if ($edit->type == 'copy')
				$lcs += sizeof($edit->orig);
		}
		return $lcs;
	}

	// Get the original set of lines.
	//
	// This reconstructs the $from_lines parameter passed to the
	// constructor.
	//
	// @return array The original sequence of strings.

	/**
	 * Short description for 'orig'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	function orig()
	{
		$lines = array();

		foreach ($this->edits as $edit) {
			if ($edit->orig)
				array_splice($lines, sizeof($lines), 0, $edit->orig);
		}
		return $lines;
	}

	// Get the closing set of lines.
	//
	// This reconstructs the $to_lines parameter passed to the
	// constructor.
	//
	// @return array The sequence of strings.

	/**
	 * Short description for 'closing'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	function closing()
	{
		$lines = array();

		foreach ($this->edits as $edit) {
			if ($edit->closing)
				array_splice($lines, sizeof($lines), 0, $edit->closing);
		}
		return $lines;
	}

	// Check a Diff for validity.
	//
	// This is here only for debugging purposes.

	/**
	 * Short description for '_check'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $from_lines Parameter description (if any) ...
	 * @param      unknown $to_lines Parameter description (if any) ...
	 * @return     void
	 */
	function _check($from_lines, $to_lines)
	{
		if (serialize($from_lines) != serialize($this->orig()))
			trigger_error("Reconstructed original doesn't match", E_USER_ERROR);
		if (serialize($to_lines) != serialize($this->closing()))
			trigger_error("Reconstructed closing doesn't match", E_USER_ERROR);

		$rev = $this->reverse();
		if (serialize($to_lines) != serialize($rev->orig()))
			trigger_error("Reversed original doesn't match", E_USER_ERROR);
		if (serialize($from_lines) != serialize($rev->closing()))
			trigger_error("Reversed closing doesn't match", E_USER_ERROR);

		$prevtype = 'none';
		foreach ($this->edits as $edit) {
			if ( $prevtype == $edit->type )
				trigger_error("Edit sequence is non-optimal", E_USER_ERROR);
			$prevtype = $edit->type;
		}

		$lcs = $this->lcs();
		trigger_error('Diff okay: LCS = '.$lcs, E_USER_NOTICE);
	}
}

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class MappedDiff extends Diff
{
	// Constructor.
	//
	// Computes diff between sequences of strings.
	//
	// This can be used to compute things like
	// case-insensitve diffs, or diffs which ignore
	// changes in white-space.
	//
	// @param $from_lines array An array of strings.
	//	(Typically these are lines from a file.)
	//
	// @param $to_lines array An array of strings.
	//
	// @param $mapped_from_lines array This array should
	//	have the same size number of elements as $from_lines.
	//	The elements in $mapped_from_lines and
	//	$mapped_to_lines are what is actually compared
	//	when computing the diff.
	//
	// @param $mapped_to_lines array This array should
	//	have the same number of elements as $to_lines.

	/**
	 * Short description for 'MappedDiff'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $from_lines Parameter description (if any) ...
	 * @param      unknown $to_lines Parameter description (if any) ...
	 * @param      unknown $mapped_from_lines Parameter description (if any) ...
	 * @param      unknown $mapped_to_lines Parameter description (if any) ...
	 * @return     void
	 */
	function MappedDiff($from_lines, $to_lines, $mapped_from_lines, $mapped_to_lines)
	{
		assert(sizeof($from_lines) == sizeof($mapped_from_lines));
		assert(sizeof($to_lines) == sizeof($mapped_to_lines));

		$this->Diff($mapped_from_lines, $mapped_to_lines);

		$xi = $yi = 0;
		for ($i = 0; $i < sizeof($this->edits); $i++)
		{
			$orig = &$this->edits[$i]->orig;
			if (is_array($orig)) {
				$orig = array_slice($from_lines, $xi, sizeof($orig));
				$xi += sizeof($orig);
			}

			$closing = &$this->edits[$i]->closing;
			if (is_array($closing)) {
				$closing = array_slice($to_lines, $yi, sizeof($closing));
				$yi += sizeof($closing);
			}
		}
	}
}

//-------------------------------------------------------------
// A class to format Diffs
//
// This class formats the diff in classic diff format.
// It is intended that this class be customized via inheritance,
// to obtain fancier outputs.
// @todo document
// @private
// @addtogroup DifferenceEngine
//-------------------------------------------------------------

/**
 * Short description for 'DiffFormatter'
 *
 * Long description (if any) ...
 */
class DiffFormatter
{
	// Number of leading context "lines" to preserve.
	//
	// This should be left at zero for this class, but subclasses
	// may want to set this to other values.

	/**
	 * Description for 'leading_context_lines'
	 *
	 * @var integer
	 */
	var $leading_context_lines = 0;

	// Number of trailing context "lines" to preserve.
	//
	// This should be left at zero for this class, but subclasses
	// may want to set this to other values.

	/**
	 * Description for 'trailing_context_lines'
	 *
	 * @var integer
	 */
	var $trailing_context_lines = 0;

	/**
	 * Description for 'i'
	 *
	 * @var mixed
	 */
	var $i = 0;

	// Format a diff.
	//
	// @param $diff object A Diff object.
	// @return string The formatted output.

	/**
	 * Short description for 'format'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $diff Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	function format($diff)
	{
		$xi = $yi = 1;
		$block = false;
		$context = array();

		$nlead = $this->leading_context_lines;
		$ntrail = $this->trailing_context_lines;

		$this->_start_diff();
		//print_r($diff->edits);
		echo '<table class="diffs">'."\n";
		echo "\t".'<tbody>'."\n";
		foreach ($diff->edits as $edit)
		{
			//$this->i++;
			if ($edit->type == 'copy') {
				if (is_array($block)) {
					if (sizeof($edit->orig) <= $nlead + $ntrail) {
						$block[] = $edit;
					} else {
						if ($ntrail) {
							$context = array_slice($edit->orig, 0, $ntrail);
							$block[] = new _DiffOp_Copy($context);
						}
						$this->_block($x0, $ntrail + $xi - $x0,
									  $y0, $ntrail + $yi - $y0,
									  $block);
						$block = false;
					}
				}
				$context = $edit->orig;
			} else {
				if (!is_array($block)) {
					$context = array_slice($context, sizeof($context) - $nlead);
					$x0 = $xi - sizeof($context);
					$y0 = $yi - sizeof($context);
					$block = array();
					if ($context)
						$block[] = new _DiffOp_Copy($context);
				}
				$block[] = $edit;
			}

			if ($edit->orig)
				$xi += sizeof($edit->orig);
			if ($edit->closing)
				$yi += sizeof($edit->closing);

			foreach ($context as $ctx)
			{
				$this->i++;
				echo "\t\t".'<tr>'."\n";
				echo "\t\t\t".'<th>'.$this->i.'</th>'."\n";
				echo "\t\t\t".'<td colspan="4">'.$ctx.'</td>'."\n";
				echo "\t\t".'</tr>'."\n";
			}
			//$content = implode("</td></tr><tr><td>", $context);
			//$content = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;', $content);
			//echo $content;
		}

		if (is_array($block))
			$this->_block($x0, $xi - $x0,
						  $y0, $yi - $y0,
						  $block);
		echo "\t".'</tbody>'."\n";
		echo '</table>'."\n";
		$end = $this->_end_diff();

		return $end;
	}

	/**
	 * Short description for '_block'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $xbeg Parameter description (if any) ...
	 * @param      unknown $xlen Parameter description (if any) ...
	 * @param      unknown $ybeg Parameter description (if any) ...
	 * @param      unknown $ylen Parameter description (if any) ...
	 * @param      array &$edits Parameter description (if any) ...
	 * @return     void
	 */
	function _block($xbeg, $xlen, $ybeg, $ylen, &$edits)
	{
		$this->_start_block($this->_block_header($xbeg, $xlen, $ybeg, $ylen));
		foreach ($edits as $edit)
		{
			if ($edit->type == 'copy')
				$this->_context($edit->orig);
			elseif ($edit->type == 'add')
				$this->_added($edit->closing);
			elseif ($edit->type == 'delete')
				$this->_deleted($edit->orig);
			elseif ($edit->type == 'change')
				$this->_changed($edit->orig, $edit->closing);
			else
				trigger_error('Unknown edit type', E_USER_ERROR);
		}
		$this->_end_block();
	}

	/**
	 * Short description for '_start_diff'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function _start_diff()
	{
		ob_start();
	}

	/**
	 * Short description for '_end_diff'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function _end_diff()
	{
		$val = ob_get_contents();
		ob_end_clean();
		return $val;
	}

	/**
	 * Short description for '_block_header'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $xbeg Parameter description (if any) ...
	 * @param      number $xlen Parameter description (if any) ...
	 * @param      string $ybeg Parameter description (if any) ...
	 * @param      number $ylen Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	function _block_header($xbeg, $xlen, $ybeg, $ylen)
	{
		if ($xlen > 1)
			$xbeg .= "," . ($xbeg + $xlen - 1);
		if ($ylen > 1)
			$ybeg .= "," . ($ybeg + $ylen - 1);

		return $xbeg . ($xlen ? ($ylen ? 'c' : 'd') : 'a') . $ybeg;
	}

	/**
	 * Short description for '_start_block'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $header Parameter description (if any) ...
	 * @return     void
	 */
	function _start_block($header)
	{
		echo $header;
	}

	/**
	 * Short description for '_end_block'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function _end_block()
	{
	}

	/**
	 * Short description for '_lines'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $lines Parameter description (if any) ...
	 * @param      string $prefix Parameter description (if any) ...
	 * @return     void
	 */
	function _lines($lines, $prefix = ' ')
	{
		foreach ($lines as $line)
			echo "$prefix $line\n";
	}

	/**
	 * Short description for '_context'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _context($lines)
	{
		$this->_lines($lines);
	}

	/**
	 * Short description for '_added'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _added($lines)
	{
		$this->_lines($lines, '>');
	}

	/**
	 * Short description for '_deleted'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _deleted($lines)
	{
		$this->_lines($lines, '<');
	}

	/**
	 * Short description for '_changed'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $orig Parameter description (if any) ...
	 * @param      unknown $closing Parameter description (if any) ...
	 * @return     void
	 */
	function _changed($orig, $closing) {
		$this->_deleted($orig);
		echo "---\n";
		$this->_added($closing);
	}
}

//-------------------------------------------------------------
//	Additions by Axel Boldt follow,
//  partly taken from diff.php, phpwiki-1.3.3
//-------------------------------------------------------------

/**
 * Description for ''NBSP''
 */
define('NBSP', '&#160;');	// iso-8859-x non-breaking space.

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class _HWLDF_WordAccumulator
{

	/**
	 * Short description for '_HWLDF_WordAccumulator'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function _HWLDF_WordAccumulator ()
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
	function _flushGroup ($new_tag)
	{
		if ($this->_group !== '') {
			if ($this->_tag == 'ins')
				$this->_line .= '<ins class="diffchange">' .
					htmlspecialchars ( $this->_group ) . '</ins>';
			elseif ($this->_tag == 'del')
				$this->_line .= '<del class="diffchange">' .
					htmlspecialchars ( $this->_group ) . '</del>';
			else
				$this->_line .= htmlspecialchars ( $this->_group );
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
	function _flushLine ($new_tag)
	{
		$this->_flushGroup($new_tag);
		if ($this->_line != '')
			array_push ( $this->_lines, $this->_line );
		else
			// make empty lines visible by inserting an NBSP
			array_push ( $this->_lines, NBSP );
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
	function addWords ($words, $tag = '')
	{
		if ($tag != $this->_tag)
			$this->_flushGroup($tag);

		foreach ($words as $word) {
			// new-line should only come as first char of word.
			if ($word == '')
				continue;
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
	function getLines()
	{
		$this->_flushLine('~done');
		return $this->_lines;
	}
}

/**
 * Short description for 'WordLevelDiff'
 *
 * Long description (if any) ...
 */
class WordLevelDiff extends MappedDiff
{

	/**
	 * Description for 'AX_LINE_LENGTH'
	 */
	const MAX_LINE_LENGTH = 10000;

	/**
	 * Short description for 'WordLevelDiff'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $orig_lines Parameter description (if any) ...
	 * @param      unknown $closing_lines Parameter description (if any) ...
	 * @return     void
	 */
	function WordLevelDiff ($orig_lines, $closing_lines)
	{
		list ($orig_words, $orig_stripped) = $this->_split($orig_lines);
		list ($closing_words, $closing_stripped) = $this->_split($closing_lines);

		$this->MappedDiff($orig_words, $closing_words, $orig_stripped, $closing_stripped);
	}

	/**
	 * Short description for '_split'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $lines Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	function _split($lines)
	{
		$words = array();
		$stripped = array();
		$first = true;
		foreach ( $lines as $line )
		{
			// If the line is too long, just pretend the entire line is one big word
			// This prevents resource exhaustion problems
			if ( $first ) {
				$first = false;
			} else {
				$words[] = "\n";
				$stripped[] = "\n";
			}
			if ( strlen( $line ) > self::MAX_LINE_LENGTH ) {
				$words[] = $line;
				$stripped[] = $line;
			} else {
				$m = array();
				if (preg_match_all('/ ( [^\S\n]+ | [0-9_A-Za-z\x80-\xff]+ | . ) (?: (?!< \n) [^\S\n])? /xs',
					$line, $m))
				{
					$words = array_merge( $words, $m[0] );
					$stripped = array_merge( $stripped, $m[1] );
				}
			}
		}
		return array($words, $stripped);
	}

	/**
	 * Short description for 'orig'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function orig ()
	{
		$orig = new _HWLDF_WordAccumulator;

		foreach ($this->edits as $edit)
		{
			if ($edit->type == 'copy')
				$orig->addWords($edit->orig);
			elseif ($edit->orig)
				$orig->addWords($edit->orig, 'del');
		}
		$lines = $orig->getLines();

		return $lines;
	}

	/**
	 * Short description for 'closing'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function closing ()
	{
		$closing = new _HWLDF_WordAccumulator;

		foreach ($this->edits as $edit) {
			if ($edit->type == 'copy')
				$closing->addWords($edit->closing);
			elseif ($edit->closing)
				$closing->addWords($edit->closing, 'ins');
		}
		$lines = $closing->getLines();

		return $lines;
	}
}

//-------------------------------------------------------------
//	Wikipedia Table style diff formatter.
//-------------------------------------------------------------

/**
 * Short description for 'class'
 *
 * Long description (if any) ...
 */
class TableDiffFormatter extends DiffFormatter
{

	/**
	 * Short description for 'TableDiffFormatter'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function TableDiffFormatter()
	{
		$this->leading_context_lines = 0;
		$this->trailing_context_lines = 0;
	}

	/**
	 * Short description for '_block_header'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $xbeg Parameter description (if any) ...
	 * @param      unknown $xlen Parameter description (if any) ...
	 * @param      string $ybeg Parameter description (if any) ...
	 * @param      unknown $ylen Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	function _block_header( $xbeg, $xlen, $ybeg, $ylen )
	{
		$r  = "\t\t".'<tr>'."\n";
		$r .= "\t\t\t".'<td colspan="2" class="diff-lineno"><!--LINE '.$xbeg.'--></td>'."\n";
		$r .= "\t\t\t".'<td colspan="2" class="diff-lineno"><!--LINE '.$ybeg.'--></td>'."\n";
		$r .= "\t\t".'</tr>'."\n";
		//return $r;
		return '';
	}

	/**
	 * Short description for '_start_block'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $header Parameter description (if any) ...
	 * @return     void
	 */
	function _start_block( $header )
	{
		echo $header;
	}

	/**
	 * Short description for '_end_block'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function _end_block()
	{
		//echo '</table>';
	}

	/**
	 * Short description for '_lines'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @param      string $prefix Parameter description (if any) ...
	 * @param      string $color Parameter description (if any) ...
	 * @return     void
	 */
	function _lines( $lines, $prefix=' ', $color='white' )
	{
	}

	// HTML-escape parameter before calling this

	/**
	 * Short description for 'addedLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @param      integer $colspan Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	function addedLine( $line, $colspan=0 )
	{
		return $this->wrapLine( '+', 'diff-addedline', $line, $colspan );
	}

	// HTML-escape parameter before calling this

	/**
	 * Short description for 'deletedLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @param      integer $colspan Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	function deletedLine( $line, $colspan=0 )
	{
		return $this->wrapLine( '-', 'diff-deletedline', $line, $colspan );
	}

	// HTML-escape parameter before calling this

	/**
	 * Short description for 'contextLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	function contextLine( $line )
	{
		return $this->wrapLine( ' ', 'diff-context', $line );
	}

	/**
	 * Short description for 'wrapLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $marker Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $line Parameter description (if any) ...
	 * @param      mixed $colspan Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function wrapLine( $marker, $class, $line, $colspan=0 )
	{
		if ($line !== '') {
			// The <div> wrapper is needed for 'overflow: auto' style to scroll properly
			$line = "<div>$line</div>";
		}
		$html  = "\t\t\t".'<td class="diff-marker">'.$marker.'</td>'."\n";
		$html .= "\t\t\t".'<td ';
		$html .= ($colspan > 0) ? 'colspan="'.$colspan.'" ' : '';
		$html .= 'class="'.$class.'">'.$line.'</td>'."\n";
		return $html;
	}

	/**
	 * Short description for 'emptyLine'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function emptyLine()
	{
		//return "\t\t\t".'<td colspan="2">&nbsp;</td>'."\n";
	}

	/**
	 * Short description for '_added'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _added( $lines )
	{
		foreach ($lines as $line)
		{
			$this->i++;
			echo "\t\t".'<tr>'."\n";
			echo "\t\t\t".'<th>'.$this->i.'</th>'."\n";
			echo $this->emptyLine() . $this->addedLine( htmlspecialchars ( $line ), 3 );
			echo "\t\t".'</tr>'."\n";
		}
	}

	/**
	 * Short description for '_deleted'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _deleted($lines)
	{
		foreach ($lines as $line)
		{
			$this->i++;
			echo "\t\t".'<tr>'."\n";
			echo "\t\t\t".'<th>'.$this->i.'</th>'."\n";
			echo $this->deletedLine( htmlspecialchars ( $line ), 3 ) . $this->emptyLine();
			echo "\t\t".'</tr>'."\n";
		}
	}

	/**
	 * Short description for '_context'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _context( $lines )
	{
		/*foreach ($lines as $line) {
			echo '<tr>' .
				$this->contextLine( htmlspecialchars ( $line ) ) .
				$this->contextLine( htmlspecialchars ( $line ) ) . "</tr>\n";
		}*/
	}

	/**
	 * Short description for '_changed'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $orig Parameter description (if any) ...
	 * @param      unknown $closing Parameter description (if any) ...
	 * @return     void
	 */
	function _changed( $orig, $closing )
	{
		$diff = new WordLevelDiff( $orig, $closing );
		$del = $diff->orig();
		$add = $diff->closing();

		// Notice that WordLevelDiff returns HTML-escaped output.
		// Hence, we will be calling addedLine/deletedLine without HTML-escaping.

		while ( $line = array_shift( $del ) )
		{
			$this->i++;
			$aline = array_shift( $add );
			echo "\t\t".'<tr>'."\n";
			echo "\t\t\t".'<th>'.$this->i.'</th>'."\n";
			echo $this->deletedLine( $line ) . $this->addedLine( $aline );
			echo "\t\t".'</tr>'."\n";
		}
		// If any leftovers
		foreach ($add as $line)
		{
			$this->i++;
			echo "\t\t".'<tr>'."\n";
			echo "\t\t\t".'<th>'.$this->i.'</th>'."\n";
			echo $this->emptyLine() . $this->addedLine( $line );
			echo "\t\t".'</tr>'."\n";
		}
		//$this->i--;
	}
}

//-------------------------------------------------------------
//  Div style diff formatter. Highlights blocks that have
//  changed following a format like this:
//
//     unchanged code
//     <div class="diff-deletedline">oldcode</div>
//     <div class="diff-addedline">newcode</div>
//     unchanged code
//-------------------------------------------------------------

/**
 * Short description for 'DivDiffFormatter'
 *
 * Long description (if any) ...
 */
class DivDiffFormatter extends DiffFormatter
{

	/**
	 * Short description for 'DivDiffFormatter'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function DivDiffFormatter()
	{
		$this->leading_context_lines = 0;
		$this->trailing_context_lines = 0;
	}

	/**
	 * Short description for '_block_header'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $xbeg Parameter description (if any) ...
	 * @param      unknown $xlen Parameter description (if any) ...
	 * @param      string $ybeg Parameter description (if any) ...
	 * @param      unknown $ylen Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	function _block_header( $xbeg, $xlen, $ybeg, $ylen )
	{
		$r = '<!--LINE '.$xbeg."-->\n" . '<!--LINE '.$ybeg."-->\n";
		return $r;
	}

	/**
	 * Short description for '_start_block'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $header Parameter description (if any) ...
	 * @return     void
	 */
	function _start_block( $header )
	{
		echo $header;
	}

	/**
	 * Short description for '_end_block'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function _end_block()
	{
	}

	/**
	 * Short description for '_lines'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $lines Parameter description (if any) ...
	 * @param      string $prefix Parameter description (if any) ...
	 * @param      string $color Parameter description (if any) ...
	 * @return     void
	 */
	function _lines( $lines, $prefix=' ', $color='white' )
	{
	}

	// HTML-escape parameter before calling this

	/**
	 * Short description for 'addedLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	function addedLine( $line )
	{
		return $this->wrapLine( '+', 'diff-addedline', $line );
	}

	// HTML-escape parameter before calling this

	/**
	 * Short description for 'deletedLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	function deletedLine( $line )
	{
		return $this->wrapLine( '-', 'diff-deletedline', $line );
	}

	// HTML-escape parameter before calling this

	/**
	 * Short description for 'contextLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	function contextLine( $line )
	{
		return $this->wrapLine( ' ', 'diff-context', $line );
	}

	/**
	 * Short description for 'wrapLine'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $marker Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $line Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function wrapLine( $marker, $class, $line )
	{
		if (trim($line) !== '') {
			// The <div> wrapper is needed for 'overflow: auto' style to scroll properly
			$line = '<div class="'.$class.'">'.$line.'</div>';
		}
		//return "<td class='diff-marker'>$marker</td><td class='$class'>$line</td>";
		return $line;
	}

	/**
	 * Short description for 'emptyLine'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	function emptyLine()
	{
		return '<br />';
	}

	/**
	 * Short description for '_added'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _added( $lines )
	{
		foreach ($lines as $line) {
			echo '' . $this->emptyLine() . $this->addedLine( htmlspecialchars ( $line ) ) . "\n";
		}
	}

	/**
	 * Short description for '_deleted'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _deleted($lines)
	{
		foreach ($lines as $line)
		{
			echo '' . $this->deletedLine( htmlspecialchars ( $line ) ) . $this->emptyLine() . "\n";
		}
	}

	/**
	 * Short description for '_context'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $lines Parameter description (if any) ...
	 * @return     void
	 */
	function _context( $lines )
	{
		foreach ($lines as $line)
		{
			echo '<div>' .
				$this->contextLine( htmlspecialchars ( $line ) ) .
				$this->contextLine( htmlspecialchars ( $line ) ) .
				"</div>\n";
		}
	}

	/**
	 * Short description for '_changed'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $orig Parameter description (if any) ...
	 * @param      unknown $closing Parameter description (if any) ...
	 * @return     void
	 */
	function _changed( $orig, $closing )
	{
		$diff = new WordLevelDiff( $orig, $closing );
		$del = $diff->orig();
		$add = $diff->closing();

		// Notice that WordLevelDiff returns HTML-escaped output.
		// Hence, we will be calling addedLine/deletedLine without HTML-escaping.

		while ( $line = array_shift( $del ) )
		{
			$aline = array_shift( $add );
			echo '' . $this->deletedLine( $line ) . $this->addedLine( $aline ) . "\n";
		}
		foreach ($add as $line)
		{
			// If any leftovers
			echo '' . $this->emptyLine() . $this->addedLine( $line ) . "\n";
		}
	}
}

