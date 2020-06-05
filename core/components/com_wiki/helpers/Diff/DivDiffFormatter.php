<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

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
	public function __construct()
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
	public function _block_header($xbeg, $xlen, $ybeg, $ylen)
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
	 * @param      unknown $lines Parameter description (if any) ...
	 * @param      string $prefix Parameter description (if any) ...
	 * @param      string $color Parameter description (if any) ...
	 * @return     void
	 */
	public function _lines($lines, $prefix=' ', $color='white')
	{
	}

	/**
	 * HTML-escape parameter before calling this
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function addedLine($line)
	{
		return $this->wrapLine('+', 'diff-addedline', $line);
	}

	/**
	 * HTML-escape parameter before calling this
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function deletedLine($line)
	{
		return $this->wrapLine('-', 'diff-deletedline', $line);
	}

	/**
	 * HTML-escape parameter before calling this
	 *
	 * @param      unknown $line Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function contextLine($line)
	{
		return $this->wrapLine(' ', 'diff-context', $line);
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
	private function wrapLine($marker, $class, $line)
	{
		if (trim($line) !== '')
		{
			// The <div> wrapper is needed for 'overflow: auto' style to scroll properly
			$line = '<div class="'.$class.'">'.$line.'</div>';
		}

		return $line;
	}

	/**
	 * Short description for 'emptyLine'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function emptyLine()
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
	public function _added($lines)
	{
		foreach ($lines as $line)
		{
			echo '' . $this->emptyLine() . $this->addedLine(htmlspecialchars ($line)) . "\n";
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
	public function _deleted($lines)
	{
		foreach ($lines as $line)
		{
			echo '' . $this->deletedLine(htmlspecialchars ($line)) . $this->emptyLine() . "\n";
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
	public function _context($lines)
	{
		foreach ($lines as $line)
		{
			echo '<div>' .
				$this->contextLine(htmlspecialchars ($line)) .
				$this->contextLine(htmlspecialchars ($line)) .
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
	public function _changed($orig, $closing)
	{
		$diff = new WordLevelDiff($orig, $closing);
		$del = $diff->orig();
		$add = $diff->closing();

		// Notice that WordLevelDiff returns HTML-escaped output.
		// Hence, we will be calling addedLine/deletedLine without HTML-escaping.

		while ($line = array_shift($del))
		{
			$aline = array_shift($add);
			echo '' . $this->deletedLine($line) . $this->addedLine($aline) . "\n";
		}
		foreach ($add as $line)
		{
			// If any leftovers
			echo '' . $this->emptyLine() . $this->addedLine($line) . "\n";
		}
	}
}
