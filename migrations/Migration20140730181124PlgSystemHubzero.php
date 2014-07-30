<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		$plugin = \JPluginHelper::getPlugin('system', 'hubzero');
		$params = new \JRegistry($plugin->params);
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