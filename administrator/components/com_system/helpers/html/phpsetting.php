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
 * Utility class working with phpsetting
 */
abstract class JHtmlPhpSetting
{
	/**
	 * Method to generate a boolean message for a value
	 *
	 * @param   boolean  $val  Is the value set?
	 * @return  string   html code
	 */
	public static function boolean($val)
	{
		return ($val ? '<span class="state on"><span>' . JText::_('JON') : '<span class="state off"><span>' . JText::_('JOFF')) . '</span></span>' ;
	}

	/**
	 * Method to generate a boolean message for a value
	 *
	 * @param   boolean  $val Is the value set?
	 * @return  string   html code
	 */
	public static function set($val)
	{
		return ($val ? '<span class="state yes"><span>' . JText::_('JYES') : '<span class="state no"><span>' . JText::_('JNO')) . '</span></span>' ;
	}

	/**
	 * Method to generate a string message for a value
	 *
	 * @param   string  $val  A php ini value
	 * @return  string  html code
	 */
	public static function string($val)
	{
		return (empty($val) ? JText::_('JNONE') : $val);
	}

	/**
	 * Method to generate an integer from a value
	 *
	 * @param   string   $val  A php ini value
	 * @return  integer
	 */
	public static function integer($val)
	{
		return intval($val);
	}
}
