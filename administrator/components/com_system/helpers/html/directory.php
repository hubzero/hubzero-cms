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

// no direct access
defined('_JEXEC') or die;

/**
 * Utility class working with directory
 */
abstract class JHtmlDirectory
{
	/**
	 * Method to generate a (un)writable message for directory
	 *
	 * @param	boolean	$writable is the directory writable?
	 *
	 * @return	string	html code
	 */
	public static function writable($writable)
	{
		if ($writable)
		{
			return '<span class="writable" style="color: green;">' . JText::_('COM_SYSTEM_INFO_WRITABLE') . '</span>';
		}
		else
		{
			return '<span class="unwritable" style="color: red;">' . JText::_('COM_SYSTEM_INFO_UNWRITABLE') . '</span>';
		}
	}

	/**
	 * Method to generate a message for a directory
	 *
	 * @param	string	$dir the directory
	 * @param	boolean	$message the message
	 * @param	boolean	$visible is the $dir visible?
	 *
	 * @return	string	html code
	 */
	public static function message($dir, $message, $visible=true)
	{
		if ($visible)
		{
			$output = $dir;
		}
		else
		{
			$output ='';
		}
		if (empty($message))
		{
			return $output;
		}
		else
		{
			return $output . ' <strong>' . JText::_($message) . '</strong>';
		}
	}
}
