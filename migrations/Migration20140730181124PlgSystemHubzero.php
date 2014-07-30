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
			$query = "UPDATE `#__extensions` SET `params` = " . $this->db->quote((string)$params) . " WHERE `folder` = 'system' AND `element` = 'hubzero'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}