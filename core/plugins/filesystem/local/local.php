<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once __DIR__ . DS . 'LocalAdapter.php';

/**
 * Plugin class for local filesystem connectivity
 */
class plgFilesystemLocal extends \Hubzero\Plugin\Plugin
{
	/**
	 * Initializes the local filesystem connection
	 *
	 * @param   array   $params  Any connection params needed
	 * @return  object
	 **/
	public static function init($params = [])
	{
		$path = rtrim($params['path'], '/') . '/' . (isset($params['subdir']) ? trim($params['subdir'], '/') : '');
		return new LocalAdapter($path);
	}
}
