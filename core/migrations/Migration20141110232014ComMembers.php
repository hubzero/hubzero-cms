<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing members names that dont have individual names filled in.
 **/
class Migration20141110232014ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// load all members without given name or surname filled in
		$query = "SELECT `uidNumber`, `username`, `name`, `surname`, `givenName`, `middleName`
				  FROM `#__xprofiles`
				  WHERE (`givenName` = '' OR `givenName` IS NULL)
				  AND (`surname` = '' OR `surname` IS NULL);";
		$this->db->setQuery($query);
		$result = $this->db->loadObjectList();

		// fix each name
		foreach ($result as $profile)
		{
			$firstname  = $profile->givenName;
			$middlename = $profile->middleName;
			$lastname   = $profile->surname;
			$name       = $profile->name;
			$username   = $profile->username;

			// all good
			if ($firstname && $surname)
			{
				continue;
			}

			if (empty($firstname) && empty($middlename) && empty($surname) && empty($name))
			{
				$query = "SELECT `name` FROM `#__users` WHERE `id`=" . $profile->uidNumber;
				$this->db->setQuery($query);
				$name = $this->db->loadResult();

				$name = $name ?: $username;
				$firstname = $username;
			}

			if (empty($firstname) && empty($middlename) && empty($surname))
			{
				$words = array_map('trim', explode(' ', $name));
				$count = count($words);

				if ($count == 1)
				{
					$firstname = $words[0];
				}
				else if ($count == 2)
				{
					$firstname = $words[0];
					$lastname  = $words[1];
				}
				else if ($count == 3)
				{
					$firstname  = $words[0];
					$middlename = $words[1];
					$lastname   = $words[2];
				}
				else
				{
					$firstname  = $words[0];
					$lastname   = $words[$count-1];
					$middlename = $words[1];

					for ($i = 2; $i < $count-1; $i++)
					{
						$middlename .= ' ' . $words[$i];
					}
				}

				// TODO:
				// if firstname all caps, and lastname isn't, switch them
				// reparse names with " de , del ,  in them
			}

			$firstname  = trim($firstname);
			$middlename = trim($middlename);
			$lastname   = trim($lastname);

			// update name
			$query = "UPDATE `#__xprofiles`
				  SET `name` = " . $this->db->quote($name) . ", `givenName` = " . $this->db->quote($firstname) . ", `middleName` = " . $this->db->quote($middlename) . ", `surname` = " . $this->db->quote($lastname) . "
				  WHERE `uidNumber`=" . $profile->uidNumber;
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}