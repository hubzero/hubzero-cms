<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for moving data from #__xprofiles to #__user_profiles
 **/
class Migration20160513140417ComMembers extends Base
{
	/**
	 * Profile fields to move to new profiles table
	 *
	 * @var  array
	 */
	public static $moveToProfile = array(
		'orgtype',
		'organization',
		'countryresident',
		'countryorigin',
		'gender',
		'url',
		'reason',
		'nativeTribe',
		'phone',
		'orcid'
	);

	/**
	 * Multi-value Profile fields to move to new profiles table
	 *
	 * @var  array
	 */
	public static $moveToProfileMulti = array(
		'disability',
		'edulevel',
		'hispanic',
		'race',
		'bio'
	);

	/**
	 * Profile fields to move to users table
	 *
	 * @var  array
	 */
	public static $moveToUser = array(
		'mailPreferenceOption' => 'sendMail',
		'usageAgreement' => 'usageAgreement',
		'emailConfirmed' => 'activation',
		'regIP' => 'registerIP',
		'regHost' => 'registerHost',
		'homeDirectory' => 'homeDirectory',
		'loginShell' => 'loginShell',
		'ftpShell' => 'ftpShell'
	);

	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xprofiles'))
		{
			if ($this->db->tableExists('#__users'))
			{
				$query = "UPDATE `#__users` AS u
						INNER JOIN `#__xprofiles` AS x ON x.`uidNumber`=u.`id`
						SET u.`homeDirectory` = x.`homeDirectory`,
							u.`loginShell` = x.`loginShell`,
							u.`ftpShell` = x.`ftpShell`,
							u.`usageAgreement` = x.`usageAgreement`,
							u.`activation` = x.`emailConfirmed`,
							u.`registerIP` = x.`regIP`,
							u.`access` = x.`public`,
							u.`sendEmail` = x.`mailPreferenceOption`;";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__users` AS u
						INNER JOIN `#__xprofiles` AS x ON x.`uidNumber`=u.`id`
						SET u.`access` = 5 WHERE x.`public` = 0;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableExists('#__user_profiles'))
			{
				if (!$this->db->tableHasField('#__user_profiles', 'access'))
				{
					$query = "ALTER TABLE `#__user_profiles` ADD COLUMN `access` INT(10) NOT NULL DEFAULT 0;";
					$this->db->setQuery($query);
					$this->db->query();
				}

				$fields = array();
				if ($this->db->tableExists('#__user_profile_fields'))
				{
					$query = "SELECT * FROM `#__user_profile_fields`";
					$this->db->setQuery($query);
					$fields = $this->db->loadAssocList('name');
				}

				foreach (self::$moveToProfile as $field)
				{
					$ordering = 0;
					$access   = 5;

					if (isset($fields[$field]))
					{
						$ordering = (int)$fields[$field]['ordering'];
						$access   = (int)$fields[$field]['access'];
					}

					$query = "INSERT INTO `#__user_profiles` (`user_id`, `profile_key`, `profile_value`, `ordering`, `access`)
							SELECT `uidNumber`, '$field', `$field`, $ordering, $access FROM `#__xprofiles` WHERE `$field` != '' AND `$field` IS NOT NULL;";
					$this->db->setQuery($query);
					$this->db->query();
				}

				foreach (self::$moveToProfileMulti as $field)
				{
					if (!$this->db->tableExists('#__xprofiles_' . $field))
					{
						continue;
					}

					$ordering = 0;
					$access   = 5;

					if (isset($fields[$field]))
					{
						$ordering = (int)$fields[$field]['ordering'];
						$access   = (int)$fields[$field]['access'];
					}

					$query = "INSERT INTO `#__user_profiles` (`user_id`, `profile_key`, `profile_value`, `ordering`, `access`)
							SELECT `uidNumber`, '$field', `$field`, $ordering, $access FROM `#__xprofiles_$field` WHERE `$field` != '' AND `$field` IS NOT NULL;";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			if ($this->db->tableExists('#__xprofiles_address'))
			{
				$query = "SELECT * FROM `#__xprofiles_address`;";
				$this->db->setQuery($query);
				$addresses = $this->db->loadObjectList();

				$ordering = 0;
				$access   = 5;

				if (isset($fields[$field]))
				{
					$ordering = (int)$fields['address']['ordering'];
					$access   = (int)$fields['address']['access'];
				}

				foreach ($addresses as $address)
				{
					$a = new stdClass;
					$a->address1  = $address->address1;
					$a->address2  = $address->address2;
					$a->city      = $address->addressCity;
					$a->region    = $address->addressRegion;
					$a->postal    = $address->addressPostal;
					$a->country   = $address->addressCountry;
					$a->latitude  = $address->addressLatitude;
					$a->longitude = $address->addressLongitude;

					$id = $address->uidNumber;

					$query = "INSERT INTO `#__user_profiles` (`user_id`, `profile_key`, `profile_value`, `ordering`, `access`)
							VALUES ($id, 'address', " . $this->db->quote(json_encode($a)) . ", $ordering, $access);";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}