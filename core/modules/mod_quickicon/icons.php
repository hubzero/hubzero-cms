<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
