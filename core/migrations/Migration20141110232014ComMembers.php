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
		$query = "SELECT `uidNumber`
				  FROM `#__xprofiles`
				  WHERE (`givenName` = '' OR `givenName` IS NULL)
				  AND (`surname` = '' OR `surname` IS NULL);";
		$this->db->setQuery($query);
		$result = $this->db->loadColumn();

		// fix each name
		foreach ($result as $uidNumber)
		{
			$profile = \Hubzero\User\User::oneOrNew($uidNumber);

			if (!$profile->get('id'))
			{
				continue;
			}

			$firstname  = $profile->get('givenName');
			$middlename = $profile->get('middleName');
			$lastname   = $profile->get('surname');
			$name       = $profile->get('name');
			$username   = $profile->get('username');

			// all good
			if ($firstname && $surname)
			{
				continue;
			}

			if (empty($firstname) && empty($middlename) && empty($surname) && empty($name))
			{
				$name = $username;
				$firstname = $username;
			}
			else if (empty($firstname) && empty($middlename) && empty($surname))
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

			// update name
			$profile->set('name', $name);
			$firstname = trim($firstname);
			if ($firstname)
			{
				$profile->set('givenName', $firstname);
			}
			$middlename = trim($middlename);
			if ($middlename)
			{
				$profile->set('middleName', $middlename);
			}
			$lastname = trim($lastname);
			if ($lastname)
			{
				$profile->set('surname', $lastname);
			}
			$profile->update();
		}
	}
}