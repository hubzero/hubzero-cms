<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for disabling new user admin notifications by default
 **/
class Migration20150210234536ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = $this->getParams('com_users');
		$params->set('mail_to_admin', '0');

		$this->saveParams('com_users', $params);
	}
}