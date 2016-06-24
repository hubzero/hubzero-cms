<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to transfer com_users config to com_members
 **/
class Migration20160624123301ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT * FROM `#__extensions` WHERE `element` IN ('com_users', 'com_members')";
			$this->db->setQuery($query);
			$objs = $this->db->loadObjectList();

			$users   = null;
			$members = null;

			foreach ($objs as $obj)
			{
				if ($obj->element == 'com_users')
				{
					$users = new \Hubzero\Config\Registry($obj->params);
				}

				if ($obj->element == 'com_members')
				{
					$members = new \Hubzero\Config\Registry($obj->params);
				}
			}
			if ($users && $members)
			{
				$params = array(
					'allowUserRegistration' => 1,
					'new_usertype' => 2,
					'guest_usergroup' => 1,
					'sendpassword' => 1,
					'useractivation' => 2,
					'simple_registration' => 0,
					'allow_duplicate_emails' => 0,
					'mail_to_admin' => 1,
					'captcha' => '',
					'frontend_userparams' => 1,
					'site_language' => 0,
					'change_login_name' => 0,
					'reset_count' => 10,
					'reset_time' => 1,
					'login_attempts_limit' => 10,
					'login_attempts_timeframe' => 1
				);

				foreach ($params as $param => $dflt)
				{
					$members->set($param, $users->get('param', $dflt));
				}

				$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($members->toString()) . " WHERE `element`='com_members'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// No down
	}
}
