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
 * Wiki helper class for HTML
 */
class WikiHtml
{
	/**
	 * Short description for 'alert'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $msg Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function alert($msg)
	{
		return "<script type=\"text/javascript\"> alert('" . $msg . "'); window.history.go(-1); </script>\n";
	}

	/**
	 * Short description for 'formSelect'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $name Parameter description (if any) ...
	 * @param      array $array Parameter description (if any) ...
	 * @param      mixed $value Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $id Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function formSelect($name, $array, $value, $class='', $id)
	{
		$out  = '<select name="' . $name . '" id="' . $name . '" onchange="return listItemTask(\'cb' . $id . '\',\'regroup\')"';
		$out .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		$out .= ' <option value="0"';
		$out .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
		$out .= '>' . JText::_('NONE') . '</option>' . "\n";
		foreach ($array as $anode)
		{
			$selected = ($anode->id == $value || $anode->title == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="' . $anode->id . '"' . $selected . '>' . $anode->title . '</option>' . "\n";
		}
		$out .= '</select>' . "\n";
		return $out;
	}

	/**
	 * Short description for 'sectionSelect'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $categories Parameter description (if any) ...
	 * @param      unknown $val Parameter description (if any) ...
	 * @param      string $name Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function sectionSelect($categories, $val, $name)
	{
		$out  = '<select name="' . $name . '">' . "\n";
		$out .= "\t" . '<option value="">' . JText::_('SELECT_CATEGORY') . '</option>' . "\n";
		foreach ($categories as $category)
		{
			$selected = ($category->id == $val)
					  ? ' selected="selected"'
					  : '';
			$out .= "\t" . '<option value="' . $category->id . '"' . $selected . '>' . $category->title . '</option>' . "\n";
		}
		$out .= '</select>' . "\n";
		return $out;
	}
}

