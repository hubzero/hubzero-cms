<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Admin\Helpers;

use Hubzero\Base\Obj;
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
	public static $extension = 'com_media';

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
