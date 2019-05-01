<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
