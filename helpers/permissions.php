<?php
namespace Components\Drwho\Helpers;

use Hubzero\Base\Object;
use User;

/**
 * Permissions helper
 */
class Permissions
{
	/**
	 * Name of the component
	 *
	 * @var  string
	 */
	public static $extension = 'com_partners';

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

		$user = User::getRoot();
		$result = new Object;

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
			$result->set('core.' . $action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

