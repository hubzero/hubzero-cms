<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Admin\Helpers;

use Hubzero\Base\Object;
use User;

class Permissions
{

	/*
	 * Component name
	 *
	 * @var   string
	 */
	public static $extension = 'com_forms';

	/*
	 * Set of actions
	 *
	 * @var   array
	 */
	protected static $_actions = array(
		'admin',
		'manage',
		'create',
		'edit',
		'edit.state',
		'delete'
	);

	/*
	 * Gets a list of the actions that can be performed
	 *
	 * @param    string    $assetType   Asset type
	 * @param    integer   $assetId     Category ID
	 * @return   object
	 */
	public static function getActions($assetType = 'component', $assetId = 0)
	{
		$assetName  = static::_buildAssetName($assetType, $assetId);
		$result = new Object;

		foreach (static::$_actions as $action)
		{
			$action = "core.$action";
			$isAuthorized = User::authorise($action, $assetName);

			$result->set($action, $isAuthorized);
		}

		return $result;
	}

	protected static function _buildAssetName($assetType, $assetId)
	{
		$assetId = (int) $assetId;
		$assetName  = self::$extension;

		$assetName .= ".$assetType";

		if ($assetId)
		{
			$assetName .= ".$assetId";
		}

		return $assetName;
	}

}
