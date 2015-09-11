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

namespace Modules\QuickIcon;

/**
 * Utility class for icons.
 */
class Icons
{
	/**
	 * Method to generate html code for a list of buttons
	 *
	 * @param   array   $buttons  Array of buttons
	 * @return  string
	 */
	public static function buttons($buttons)
	{
		$html = array();
		foreach ($buttons as $button)
		{
			$html[] = self::button($button);
		}
		return implode($html);
	}

	/**
	 * Method to generate html code for a list of buttons
	 *
	 * @param   array|object   $button  Button properties
	 * @return  string
	 */
	public static function button($button)
	{
		if (!empty($button['access']))
		{
			if (is_bool($button['access']))
			{
				if ($button['access'] == false)
				{
					return '';
				}
			}
			else
			{

				// Take each pair of permission, context values.
				for ($i = 0, $n = count($button['access']); $i < $n; $i += 2)
				{
					if (!\User::authorise($button['access'][$i], $button['access'][$i+1]))
					{
						return '';
					}
				}
			}
		}

		$html[] = '<div class="icon-wrapper"' . (empty($button['id']) ? '' : (' id="' . $button['id'] . '"')) . '>';
		$html[] = '<div class="icon">';
		$html[] = '<a href="' . $button['link'] . '"';
		$html[] = (empty($button['target']) ? '' : (' target="' . $button['target'] . '"'));
		$html[] = (empty($button['onclick']) ? '' : (' onclick="' . $button['onclick'] . '"'));
		$html[] = (empty($button['title']) ? '' : (' title="' . htmlspecialchars($button['title']) . '"'));
		$html[] = '>';
		if (isset($button['image']) && $button['image'])
		{
			$html[] = \Html::asset('image', empty($button['image']) ? '' : $button['image'], empty($button['alt']) ? null : htmlspecialchars($button['alt']), null, true);
		}
		$html[] = (empty($button['text'])) ? '' : ('<span>' . $button['text'] . '</span>');
		$html[] = '</a>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode($html);
	}
}
