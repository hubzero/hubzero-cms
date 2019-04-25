<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating system search preference
 **/
class Migration20140730181124PlgSystemHubzero extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = $this->getParams('plg_system_hubzero');
		$search = $params->get('search');

		if ($search && $search == 'ysearch')
		{
			$params->set('search', 'search');
			$component = new \JTableExtension($this->db);
			$component->load(array('folder'=>'system', 'element'=>'hubzero'));
			$component->set('params', (string) $params);
			$component->store();
		}
	}
}
