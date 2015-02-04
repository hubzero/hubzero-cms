<?php
/**
 * @package    hubzero-cms
 * @author     Shawn Rice <zooley@purdue.edu>
 * @copyright  Copyright 2005-2015 Purdue University. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * Renders a list of support ticket messages
 */
class JElementTicketmessage extends JElement
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Ticketmessage';

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

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'message.php');
		$sr = new SupportMessage($db);

		$messages = $sr->getMessages();

		$html[] = '<option value="0"' . (!$value ? ' selected="selected"' : '') . '>[ none ]</option>';

		foreach ($messages as $anode)
		{
			$html[] = '<option value="' . $anode->id . '"' . ($value == $anode->id ? ' selected="selected"' : '') . '>' . stripslashes($anode->title) . '</option>';
		}

		$html[] = '</select>';

		return implode("\n", $html);
	}
}
