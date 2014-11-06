<?php
/**
 * @package    hubzero-cms
 * @author     Shawn Rice <zooley@purdue.edu>
 * @copyright  Copyright 2005-2014 Purdue University. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 */
defined('JPATH_PLATFORM') or die;

/**
 * Renders a list of support ticket statuses
 */
class JElementTicketstate extends JElement
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Ticketstate';

	/**
	 * Fetch the element
	 *
	 * @param   string       $name          Element name
	 * @param   string       $value         Element value
	 * @param   JXMLElement  &$node         JXMLElement node object containing the settings for the element
	 * @param   string       $control_name  Control name
	 * @return  string
	 * @since   1.3.1
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = JFactory::getDbo();

		$html = array();

		$html[] = '<select name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '">';

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'status.php');
		$sr = new SupportTableStatus($db);

		$status = $sr->find('list', array('sort' => 'open', 'sort_Dir' => 'DESC'));

		$html[] = '<option value=""' . ($value === '' || $value === null ? ' selected="selected"' : '') . '>--</option>';
		$html[] = '<option value="0"' . ($value === 0 || $value === '0' ? ' selected="selected"' : '') . '>open: New</option>';

		$switched = false;
		foreach ($status as $anode)
		{
			if (!$anode->open && !$switched)
			{
				$html[] = '<option value="-1"' . ($value == -1 ? ' selected="selected"' : '') . '>closed: No resolution</option>';
				$switched = true;
			}
			$html[] = '<option value="' . $anode->id . '"' . ($value == $anode->id ? ' selected="selected"' : '') . '>' . ($anode->open ? 'open: ' : 'closed: ') . stripslashes($anode->title) . '</option>';
		}

		$html[] = '</select>';

		return implode("\n", $html);
	}
}
