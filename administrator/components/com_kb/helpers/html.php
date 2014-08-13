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

/**
 * Knowledgebase helper class for HTML
 */
class KbHelperHtml
{
	/**
	 * Outputs a <select> element with a specific value chosen
	 *
	 * @param      array  $categories Data to populate list with
	 * @param      mixed  $value      Chosen value
	 * @param      string $name       Field name
	 * @return     string HTML <select>
	 */
	public static function sectionSelect($categories, $val, $name, $id='')
	{
		$out  = '<select name="' . $name . '" id="' . ($id ? $id : str_replace(array('[', ']'), '', $name)) . '">';
		$out .= '<option value="">' . JText::_('COM_KB_SELECT_CATEGORY') . '</option>';
		foreach ($categories as $category)
		{
			$selected = ($category->get('id') == $val)
					  ? ' selected="selected"'
					  : '';
			$out .= '<option value="' . $category->get('id') . '"' . $selected . '>' . stripslashes($category->get('title')) . '</option>';
		}
		$out .= '</select>';
		return $out;
	}
}

