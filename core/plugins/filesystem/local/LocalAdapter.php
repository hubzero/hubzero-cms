<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * HUBzero extension of flysystem local adapter
 */
class LocalAdapter extends \League\Flysystem\Adapter\Local
{
	/**
	 * Map file info to normalized key names
	 *
	 * @param   \SplFileInfo  $file  The original file info class
	 * @return  array
	 */
	protected function mapFileInfo(SplFileInfo $file)
	{
		$default = parent::mapFileInfo($file);

		$default['owner'] = $file->getOwner();

		return $default;
	}
}
