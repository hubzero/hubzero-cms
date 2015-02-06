<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Utility class for creating HTML Grids
 */
class JHtmlRedirect
{
	/**
	 * @param	int $value	The state value.
	 * @param	int $i
	 * @param	string		An optional prefix for the task.
	 * @param	boolean		An optional setting for access control on the action.
	 */
	public static function published($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			1  => array('on',       'unpublish', 'JENABLED',   'COM_REDIRECT_DISABLE_LINK'),
			0  => array('off',      'publish',   'JDISABLED',  'COM_REDIRECT_ENABLE_LINK'),
			2  => array('archived', 'unpublish', 'JARCHIVED', 'JUNARCHIVE'),
			-2 => array('trash',    'publish',   'JTRASHED',  'COM_REDIRECT_ENABLE_LINK'),
		);
		$state = JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html  = '<span>' . JText::_($state[3]) . '</span>'; //JHtml::_('image', 'admin/'.$state[0], JText::_($state[2]), NULL, true);
		if ($canChange)
		{
			$html = '<a class="state ' . $state[0] . '" href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'. $html.'</a>';
		}

		return $html;
	}
}
