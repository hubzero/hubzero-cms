<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Admin\Helpers;

use Hubzero\Base\Obj;
use User;

/**
 * Knowledge Base permissions helper
 */
class Permissions
{
	/**
	 * Name of the component
	 *
	 * @var  string
	 */
	public static $extension = 'com_kb';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $extension  The extension.
	 * @param   integer  $assetId    The asset ID.
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
