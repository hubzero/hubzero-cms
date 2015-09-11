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

// no direct access
defined('_HZEXEC_') or die();

/**
 * HTML Helper
 */
abstract class InstallerHtmlManage
{
	/**
	 * Returns a published state on a grid
	 *
	 * @param   integer  $value     The state value.
	 * @param   integer  $i         The row index
	 * @param   boolean  $enabled   An optional setting for access control on the action.
	 * @param   string   $checkbox  An optional prefix for checkboxes.
	 * @return  string   The Html code
	 */
	public static function state($value, $i, $enabled = true, $checkbox = 'cb')
	{
		$states	= array(
			2 => array(
				'',
				'COM_INSTALLER_EXTENSION_PROTECTED',
				'',
				'COM_INSTALLER_EXTENSION_PROTECTED',
				false,
				'protected',
				'protected'
			),
			1 => array(
				'unpublish',
				'COM_INSTALLER_EXTENSION_ENABLED',
				'COM_INSTALLER_EXTENSION_DISABLE',
				'COM_INSTALLER_EXTENSION_ENABLED',
				false,
				'publish',
				'publish'
			),
			0 => array(
				'publish',
				'COM_INSTALLER_EXTENSION_DISABLED',
				'COM_INSTALLER_EXTENSION_ENABLE',
				'COM_INSTALLER_EXTENSION_DISABLED',
				false,
				'unpublish',
				'unpublish'
			),
		);

		return Html::grid('state', $states, $value, $i, 'manage.', $enabled, true, $checkbox);
	}
}
