<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;
use Plugins\Content\Formathtml\Parser;

/**
 * Wiki macro class for linking footnotes
 */
class FootNote extends Macro
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
			$wm = new \stdClass();
			$wm->footnotes = array();
			$wm->footnotes_notes = array();
			$wm->footnotes_count = 0;
		}

		$note = $this->args;

		if ($note)
		{
			$p = new Parser('Footnotes', $this->option, $this->scope, $this->pagename, $this->pageid, $this->filepath, $this->domain);

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

			$footnote = new \stdClass;
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

