<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Admin\Helpers;

use Hubzero\Base\Obj;


class Permissions
{
	/**
	 * Name of the component
	 *
	 * @var  string
	 */
	public static $extension = 'com_cart';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $extension  The extension.
	 * @param   integer  $assetId    The category ID.
	 * @return  object
	 */
	public static function getActions($assetType='component', $assetId = 0)
	{
		$assetName  = self::$extension;
		$assetName .= '.' . $assetType;
		if ($assetId)
		{
			$assetName .= '.' . (int) $assetId;
		}

		$user = \User::getInstance();
		$result = new Obj;

		$actions = array(
			'admin',
			'manage',
			'create',
			'edit',
			'edit.state',
			'delete'
		);

		foreach ($actions as $action)
		{
			$result->set('core.' . $action, $user->authorise('core.' . $action, $assetName));
		}

		return $result;
	}
}
