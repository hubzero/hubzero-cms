<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing members names that are malformed
 **/
class Migration20241218103500ComMembers extends Base
{
	/*
		We want to sanitize and normalize the proper name fields in
		jos_users and jos_xprofile

		Remove all invalid unicode characters
		Replace < with [
		Replace > with ]
		Replace : with -
		Replace & with " & "
		Replace multiple spaces with a single space
		Remove leading and trailing spaces

		If name is empty then rebuild it by concatenating givenName,
		middleName, surname

		If name parts are empty then rebuild them by splitting up
		name using TheIconic Name Parser.

		If jos_xprofile name doesn't match jos_users then
                reset jos_xprofile fields to match.
	*/


	public function cleanUnicode_ProperNames()
	{
		$query = 'SELECT id FROM #__users where surname REGEXP "\\\\p{C}" OR givenName REGEXP "\\\\p{C}" OR middleName REGEXP "\\\\p{C}" OR name REGEXP "\\\\p{C}"';
		$this->db->setQuery($query);
		$results = $this->db->loadColumn();

		foreach($results as $result)
		{
			$user = \Hubzero\User::getInstance($result);
			$user->set('surname', $user->get('surname'));
			$user->set('givenName', $user->get('givenName'));
			$user->set('middleName', $user->get('middleName'));
			$user->set('name', $user->get('name'));
			$user->save();
		}


		$query = 'SELECT uidNumber FROM #__xprofiles where surname REGEXP "\\\\p{C}" OR givenName REGEXP "\\\\p{C}" OR middleName REGEXP "\\\\p{C}" OR name REGEXP "\\\\p{C}"';
		$this->db->setQuery($query);
		$results = $this->db->loadColumn();

		foreach($results as $result)
		{
			$profile = \Hubzero\User\Profile::getInstance($result);
			$profile->set('surname', $profile->get('surname'));
			$profile->set('givenName', $profile->get('givenName'));
			$profile->set('middleName', $profile->get('middleName'));
			$profile->set('name', $profile->get('name'));
			$profile->update();
		}
	}

	public function cleanInvaldChars_ProperNames()
	{
		foreach( array('#__users', '#__xprofiles') as $table)
		{
			foreach( array('name','givenName','middleName','surname') as $field)
			{
				$query = "UPDATE $table SET $field=REPLACE($field,':','-') WHERE $field LIKE '%:%'";
				$this->db->setQuery($query);
				$this->db->execute();

				$query = "UPDATE $table SET $field=REPLACE($field,'<','[') WHERE $field LIKE '%<%'";
				$this->db->setQuery($query);
				$this->db->execute();

				$query = "UPDATE $table SET $field=REPLACE($field,'>',']') WHERE $field LIKE '%>%'";
				$this->db->setQuery($query);
				$this->db->execute();

				$query = "UPDATE $table SET $field=REPLACE($field,'&',' & ') WHERE $field LIKE '%&%'";
				$this->db->setQuery($query);
				$this->db->execute();

				$query = "UPDATE $table SET $field=TRIM($field) WHERE $field LIKE ' %' OR $field LIKE '% '";
				$this->db->setQuery($query);
				$this->db->execute();

				while(1)
				{
					$query = "UPDATE $table SET $field=REPLACE($field,'  ',' ') WHERE $field LIKE '%  %'";
					$this->db->setQuery($query);
					$this->db->execute();

					if (!$this->db->getAffectedRows())
					{
						break;
					}
				}
			}
		}

	}

	public function rebuildName()
	{
		$query = "UPDATE #__users SET givenName='unknown' WHERE TRIM(name)='' AND TRIM(givenName)='' AND TRIM(middleName)='' AND TRIM(surname)='';";
		$this->db->setQuery($query);
		$this->db->execute();

		$query = "UPDATE #__users SET name=CONCAT_WS(' ',givenName,middleName,surname) WHERE TRIM(name)='';";
		$this->db->setQuery($query);
		$this->db->execute();

		$query = "UPDATE #__xprofiles SET name=CONCAT_WS(' ',givenName,middleName,surname) WHERE TRIM(name)='';";
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function rebuildNameParts()
	{
		$query = "SELECT id,name FROM #__users where TRIM(surname)='' AND TRIM(givenName)='' AND TRIM(middleName)=''";
		$this->db->setQuery($query);
		$results = $this->db->loadAssocList();

		foreach($results as $result)
		{
			$parser = new TheIconic\NameParser\Parser();

			$parsedname = $parser->parse($result['name']);

			$firstname = $parsedname->getFirstname();
			$middlename = $parsedname->getMiddlename();
			$lastname = $parsedname->getLastname();
			// update name
			$query = "UPDATE `#__users`
				SET
					`givenName` = " . $this->db->quote($firstname) . ",
					`middleName` = " . $this->db->quote($middlename) . ",
					`surname` = " . $this->db->quote($lastname) . "
				WHERE `id`=" . $result['id'];
			$this->db->setQuery($query);
			$this->db->execute();
		}

		$query = "SELECT uidNumber,name FROM #__xprofiles where TRIM(surname)='' AND TRIM(givenName)='' AND TRIM(middleName)=''";
		$this->db->setQuery($query);
		$results = $this->db->loadAssocList();

		foreach($results as $result)
		{
			$parser = new TheIconic\NameParser\Parser();

			$parsedname = $parser->parse($result['name']);

			$firstname = $parsedname->getFirstname();
			$middlename = $parsedname->getMiddlename();
			$lastname = $parsedname->getLastname();
			// update name
			$query = "UPDATE `#__xprofiles`
				SET
					`givenName` = " . $this->db->quote($firstname) . ",
					`middleName` = " . $this->db->quote($middlename) . ",
					`surname` = " . $this->db->quote($lastname) . "
				WHERE `uidNumber`=" . $result['uidNumber'];
			$this->db->setQuery($query);
			$this->db->execute();
		}
	}

	public function syncProfiles()
	{

		$query = "UPDATE `#__users` AS u, `#__xprofiles` AS x SET x.name=u.name," .
			" x.surname=u.surname,x.middleName=u.middleName,x.givenName=u.givenName" .
			" WHERE u.id=x.uidNumber AND (u.name != x.name OR u.surname != x.surname" .
			" OR u.givenName != x.givenName OR u.middleName != x.middleName);";
		$this->db->setQuery($query);
		$this->db->execute();
	}


	public function up()
	{
		$this->cleanUnicode_ProperNames();
		$this->cleanInvaldChars_ProperNames();
		$this->rebuildName();
		$this->rebuildNameParts();
		$this->syncProfiles();
	}

	public function down()
	{
	}
}

