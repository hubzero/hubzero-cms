<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for filling in missing password hashes in tables
 **/
class Migration20161021135212ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__users') && $this->db->tableExists('#__users_password') &&
		  $this->db->tableHasField('#__users', 'password') && $this->db->tableHasField('#__users_password', 'passhash'))
		{
			$query = "UPDATE  `#__users` AS u, `#__users_password` AS up
						SET u.`password`=up.`passhash`
						WHERE u.`id`=up.`user_id` AND (u.`password`='' OR u.`password` IS NULL) AND (up.`passhash` != '' AND up.`passhash` IS NOT NULL);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users') && $this->db->tableExists('#__xprofiles') &&
		  $this->db->tableHasField('#__users', 'password') && $this->db->tableHasField('#__xprofiles', 'userPassword'))
		{
			$query = "UPDATE `#__users` AS u, `#__xprofiles` AS xp
						SET u.`password`=xp.`userPassword`
						WHERE u.`id`=xp.`uidNumber` AND (u.`password`='' OR u.`password` IS NULL) AND (xp.`userPassword` != '' AND xp.`userPassword` IS NOT NULL);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xprofiles') && $this->db->tableExists('#__users_password') &&
		  $this->db->tableHasField('#__xprofiles', 'userPassword') && $this->db->tableHasField('#__users_password', 'passhash'))
		{
			$query = "UPDATE `#__xprofiles` AS xp, `#__users_password` AS up
						SET xp.`userPassword`=up.`passhash`
						WHERE up.`user_id`=xp.`uidNumber` AND (xp.`userPassword`='' OR xp.`userPassword` IS NULL) AND (up.`passhash` != '' AND up.`passhash` IS NOT NULL);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xprofiles') && $this->db->tableExists('#__users') &&
		  $this->db->tableHasField('#__xprofiles', 'userPassword') && $this->db->tableHasField('#__users', 'password'))
		{
			$query = "UPDATE `#__xprofiles` AS xp, `#__users` AS u
						SET xp.`userPassword`=u.`password`
						WHERE u.`id`=xp.`uidNumber` AND (xp.`userPassword`='' OR xp.`userPassword` IS NULL) AND (u.`password` != '' AND u.`password` IS NOT NULL);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_password') && $this->db->tableExists('#__xprofiles') &&
		  $this->db->tableHasField('#__users_password', 'passhash') && $this->db->tableHasField('#__xprofiles', 'userPassword'))
		{
			$query = "UPDATE `#__users_password` AS up, `#__xprofiles` AS xp
						SET up.`passhash`=xp.`userPassword`
						WHERE up.`user_id`=xp.`uidNumber` AND (up.`passhash` ='' OR up.`passhash` IS NULL) AND (xp.`userPassword` != '' AND xp.`userPassword` IS NOT NULL);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_password') && $this->db->tableExists('#__users') &&
		  $this->db->tableHasField('#__users_password', 'passhash') && $this->db->tableHasField('#__users', 'password'))
		{
			$query = "UPDATE `#__users_password` AS up, `#__users` AS u
						SET up.`passhash`=u.`password`
						WHERE up.`user_id`=u.`id` AND (up.`passhash` ='' OR up.`passhash` IS NULL) AND (u.`password` != '' AND u.`password` IS NOT NULL);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
	
	/**
	 * Down
	 **/
	public function down()
	{
		/* No down method. Irreversible data migration. */
	}
}
