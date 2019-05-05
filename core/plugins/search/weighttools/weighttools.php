<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Search pluginf or weighting tools
 */
class plgSearchWeightTools extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'onSearchWeightResources'
	 *
	 * Long description (if any) ...
	 *
	 * @param   unknown  $_terms  Parameter description (if any) ...
	 * @param   object   $res     Parameter description (if any) ...
	 * @return  mixed    Return description (if any) ...
	 */
	public static function onSearchWeightResources($_terms, $res)
	{
		return $res->get_plugin() == 'resources' && $res->get_section() == 'Tools' ? 1 : 0.5;
	}
}
