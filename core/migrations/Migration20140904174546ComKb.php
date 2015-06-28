<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for updating kb article text
 **/
class Migration20140904174546ComKb extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT * FROM `#__faq` WHERE `alias` = 'login2' OR `alias` = 'pwreset'";
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $result)
			{
				$result->fulltxt = str_replace('/lostpassword', '/login/reset', $result->fulltxt);
				$result->fulltxt = str_replace('/change_password', '/members/myaccount/account', $result->fulltxt);
				$this->db->updateObject('#__faq', $result, 'id');
			}
		}
	}
}