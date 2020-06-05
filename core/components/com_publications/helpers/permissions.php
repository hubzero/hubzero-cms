<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

use Hubzero\Base\Obj;
use User;

/**
 * Members permissions helper
 */
class Permissions
{
	/**
	 * Name of the component
	 *
	 * @var  string
	 */
	public static $extension = 'com_publications';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $assetType
	 * @param   integer  $assetId
	 * @return  object   Object
	 */
	public static function getActions($assetType='component', $assetId = 0)
	{
		$assetName  = self::$extension;
		if ($assetId)
		{
			$assetName .= '.' . $assetType;
			$assetName .= '.' . (int) $assetId;
		}

		$result = new Obj;

		$actions = array(
			'core.admin',
			'core.manage',
			'core.create',
			'core.edit',
			'core.edit.state',
			'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, User::authorise($action, $assetName));
		}

		return $result;
	}
}
