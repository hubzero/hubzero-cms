<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Helpers;

use Hubzero\Base\Obj;
use User;

/**
 * Developer helper for permissions
 * 
 * This ties into the permissions ACL and the 
 * component's access.xml that defines that permissions
 * exist for what objects.
 */
class Permissions
{
	/**
	 * Name of the component
	 *
	 * @var  string
	 */
	public static $extension = 'com_developer';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $extension   The extension.
	 * @param   integer  $categoryId  The category ID.
	 * @return  JObject
	 */
	public static function getActions($assetType='component', $assetId = 0)
	{
		$assetName  = self::$extension;
		if ($assetId)
		{
			$assetName .= '.' . $assetType;
			$assetName .= '.' . (int) $assetId;
		}

		$actions = array(
			'core.admin',
			'core.manage',
			'core.create',
			'core.edit',
			'core.edit.state',
			'core.delete'
		);

		$result = new Obj;

		foreach ($actions as $action)
		{
			$result->set($action, User::authorise($action, $assetName));
		}

		return $result;
	}
}
