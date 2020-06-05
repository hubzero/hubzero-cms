<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * A class to format Diffs
 *
 * This class formats the diff in classic diff format.
 * It is intended that this class be customized via inheritance,
 * to obtain fancier outputs.
 *
 * @todo document
 */
class DiffFormatter
{
	/**
	 * Number of leading context "lines" to preserve.
	 *
	 * This should be left at zero for this class, but subclasses
	 * may want to set this to other values.
	 *
	 * @var integer
	 */
	public $leading_context_lines = 0;

	/**
	 * Number of trailing context "lines" to preserve.
	 *
	 * This should be left at zero for this class, but subclasses
	 * may want to set this to other values.
	 *
	 * @var integer
	 */
	public $trailing_context_lines = 0;

	/**
	 * Description for 'i'
	 *
	 * @var mixed
	 */
	public $i = 0;

	/**
	 * Format a diff.
	 *
	 * @param   object  $diff  A Diff object.
	 * @return  string  The formatted output.
	 */
	public function format($diff, Closure $formatContextOutput = null)
	{
		$xi = $yi = 1;
		$block = false;
		$context = array();

		$nlead = $this->leading_context_lines;
		$ntrail = $this->trailing_context_lines;

		$this->_start_diff();

		echo '<table class="diffs">'."\n";
		echo "\t".'<tbody>'."\n";
		foreach ($diff->edits as $edit)
		{
			//$this->i++;
			if ($edit->type == 'copy')
			{
				if (is_array($block))
				{
					if (count($edit->orig) <= $nlead + $ntrail)
					{
						$block[] = $edit;
					}
					else
					{
						if ($ntrail)
						{
							$context = array_slice($edit->orig, 0, $ntrail);
							$block[] = new _DiffOp_Copy($context);
						}
						$this->_block(
							$x0,
							$ntrail + $xi - $x0,
							$y0,
							$ntrail + $yi - $y0,
							$block
						);
						$block = false;
					}
				}
				$context = $edit->orig;
			}
			else
			{
				if (!is_array($block))
				{
					$context = array_slice($context, count($context) - $nlead);
					$x0 = $xi - count($context);
					$y0 = $yi - count($context);
					$block = array();
					if ($context)
					{
						$block[] = new _DiffOp_Copy($context);
					}
				}
				$block[] = $edit;
			}

			if ($edit->orig)
			{
				$xi += count($edit->orig);
			}
			if ($edit->closing)
			{
				$yi += count($edit->closing);
			}

			foreach ($context as $ctx)
			{
				if ($formatContextOutput)
				{
					$ctx = $formatContextOutput($ctx);
				}

				$this->i++;
				echo "\t\t".'<tr>'."\n";
				echo "\t\t\t".'<th>'.$this->i.'</th>'."\n";
				echo "\t\t\t".'<td colspan="4">'.$ctx.'</td>'."\n";
				echo "\t\t".'</tr>'."\n";
			}
		}

		if (is_array($block))
		{
			$this->_block(
				$x0,
				$xi - $x0,
				$y0,
				$yi - $y0,
				$block
			);
		}
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
	public function _block($xbeg, $xlen, $ybeg, $ylen, &$edits)
	{
		$this->_start_block($this->_block_header($xbeg, $xlen, $ybeg, $ylen));
		foreach ($edits as $edit)
		{
			if ($edit->type == 'copy')
			{
				$this->_context($edit->orig);
			}
			elseif ($edit->type == 'add')
			{
				$this->_added($edit->closing);
			}
			elseif ($edit->type == 'delete')
			{
				$this->_deleted($edit->orig);
			}
			elseif ($edit->type == 'change')
			{
				$this->_changed($edit->orig, $edit->closing);
			}
			else
			{
				trigger_error('Unknown edit type', E_USER_ERROR);
			}
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
	public function _start_diff()
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
	public function _end_diff()
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
	public function _block_header($xbeg, $xlen, $ybeg, $ylen)
	{
		if ($xlen > 1)
		{
			$xbeg .= "," . ($xbeg + $xlen - 1);
		}
		if ($ylen > 1)
		{
			$ybeg .= "," . ($ybeg + $ylen - 1);
		}

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
	public function _start_block($header)
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
	public function _end_block()
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
	public function _lines($lines, $prefix = ' ')
	{
		foreach ($lines as $line)
		{
			echo "$prefix $line\n";
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
	public function _context($lines)
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
	public function _added($lines)
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
	public function _deleted($lines)
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
	public function _changed($orig, $closing)
	{
		$this->_deleted($orig);
		echo "---\n";
		$this->_added($closing);
	}
}
