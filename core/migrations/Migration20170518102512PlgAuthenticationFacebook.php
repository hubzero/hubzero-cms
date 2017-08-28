<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for moving registerDate data from #__xprofiles to #__users
 **/
class Migration20170518102512PlgAuthenticationFacebook extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions') && $this->db->tableHasField('#__extensions', 'params'))
		{
			$facebookWhere = " WHERE element = 'facebook' AND type = 'plugin' AND folder = 'authentication'";
			$query = "SELECT params FROM `#__extensions`" . $facebookWhere . ";";
			$this->db->setQuery($query);
			$params = $this->db->loadResult();
			if (!empty($params))
			{
				$params = (array) json_decode($params);
				$params['graph_version'] = 'v2.9';
				$updateQuery = "UPDATE `#__extensions` SET params = '" . json_encode($params) . "'" . $facebookWhere . ";";
				$this->db->setQuery($updateQuery);
				$this->db->query();
			}
		}
	}
}
