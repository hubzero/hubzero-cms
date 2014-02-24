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
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki macro class for linking footnotes
 */
class FootNoteMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 * 
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Add a footnote, or explicitly display collected footnotes when no args (footnote text) are given.';
		$txt['html'] = '<p>Add a footnote, or explicitly display collected footnotes when no args (footnote text) are given.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 * 
	 * @return     string
	 */
	public function render()
	{
		static $wm;

		if (!is_object($wm)) 
		{
			$wm = new stdClass();
			$wm->footnotes = array();
			$wm->footnotes_notes = array();
			$wm->footnotes_count = 0;
		}

		$note = $this->args;

		if ($note) 
		{
			$p = new WikiParser('Footnotes', $this->option, $this->scope, $this->pagename, $this->pageid, $this->filepath, $this->domain);
//echo $note . '<br /><br />';
			$note = $p->parse(trim($note));

			$wm->footnotes_count++;

			if (in_array($note, $wm->footnotes_notes)) 
			{
				$i = array_search($note, $wm->footnotes_notes) + 1;
				$k = $wm->footnotes_count;

				$wm->footnotes[$i-1]->refs[] = 'fndef-' . $k;

				return '<sup><a name="fndef-' . $k . '"></a><a href="#fnref-' . $i . '">&#91;' . $i . '&#93;</a></sup>';
			}

			$i = count($wm->footnotes) + 1;

			$footnote = new stdClass;
			$footnote->content = $note;
			$footnote->id      = 'fnref-' . $i;
			$footnote->refs    = array(
				'fndef-' . $i
			);

			$wm->footnotes_notes[] = $note;
			$wm->footnotes[] = $footnote;

			return '<sup><a name="fndef-' . $i . '"></a><a href="#fnref-' . $i . '">&#91;' . $i . '&#93;</a></sup>';
		} 
		else 
		{
			$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

			$html  = '<ol class="footnotes">';
			foreach ($wm->footnotes as $i => $footnote)
			{
				$html .= '<li><p>';
				if (count($footnote->refs) > 1)
				{
					$html .= '^ ';
					foreach ($footnote->refs as $key => $ref)
					{
						$html .= '<sup><a href="#' . $ref . '">' . strtolower($letters[$key]) . '</a></sup> ';
					}
				}
				else if (count($footnote->refs) == 1)
				{
					$html .= '<a href="#' . $footnote->refs[0] . '">^</a> ';
				}
				$html .= '<a name="fnref-' . ($i + 1) . '"></a>' . substr($footnote->content, 3);
				$html .= '</li>';
			}
			$html .= '</ol>';

			$wm = null;

			return $html;
		}
	}
}

