<?php
/**
 * HUBzero CMS
 *
 * Copyright 2010-2012 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author	  Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2010-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Password;

use Hubzero\User\Profile;
use Hubzero\User\Password\History;
use Hubzero\User\Password;

class Rule
{
	public static function getRules($group = null, $all = false)
	{
		$db =  \JFactory::getDBO();

		if (empty($db)) 
		{
			return array();
		}

		if (empty($group)) 
		{
			$group = "'%'";
		}
		else 
		{
			$group = $db->Quote($group);
		}

		$query = "SELECT id,rule,class,value,description,failuremsg FROM " . "#__password_rule WHERE `grp` LIKE $group";

		if ($all == false) 
		{
			$query .= " AND enabled='1'";
		} 

		$query .= " ORDER BY ordering ASC;";

		$db->setQuery($query);

		$result = $db->loadAssocList();

		if (empty($result)) 
		{
			return array();
		}

		return $result;
	}

	public static function analyze($password)
	{
		$stats = array();
		$len = strlen($password);
		$stats['count'][0] = $len;
		$stats['uniqueCharacters'] = 0;
		$stats['uniqueClasses'] = 0;
		$classes = array();
		$histogram = array();

		for ($i = 0; $i < $len; $i++) 
		{
			$c = $password[$i];

			$cl = CharacterClass::match($c);

			foreach ($cl as $class) 
			{
				if (empty($stats['count'][$class->name]))
				{
					$stats['count'][$class->name] = 1;
					if ($class->flag) 
					{
						$stats['uniqueClasses']++;
					}
				}
				else 
				{
					$stats['count'][$class->name]++;
				}
			}

			if (empty($histogram[$c])) 
			{
				$histogram[$c] = 1;
				$stats['uniqueCharacters']++;
			}
			else 
			{
				$histogram[$c]++;
			}
		}

		return $stats;
	}

	public static function validate($password, $rules, $user, $name=null)
	{
		if (empty($rules)) 
		{
			return array();
		}

		$fail = array();

		$stats = self::analyze($password);

		foreach ($rules as $rule) 
		{
			if ($rule['rule'] == 'minCharacterClasses') 
			{
				if ($stats['uniqueClasses'] < $rule['value']) 
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'maxCharacterClasses') 
			{
				if ($stats['uniqueClasses'] > $rule['value']) 
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'minPasswordLength') 
			{
				if ($stats['count'][0] < $rule['value']) 
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'maxPasswordLength') 
			{
				if ($stats['count'][0] > $rule['value']) 
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'maxClassCharacters') 
			{
				if (empty($rule['class'])) 
				{
					continue;
				}

				$class = $rule['class'];

				if (empty($stats['count'][$class])) 
				{
					$stats['count'][$class] = 0;
				}

				if ($stats['count'][$class] > $rule['value']) 
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'minClassCharacters') 
			{
				if (empty($rule['class'])) 
				{
					continue;
				}

				$class = $rule['class'];

				if (empty($stats['count'][$class])) 
				{
					$stats['count'][$class] = 0;
				}

				if ($stats['count'][$class] < $rule['value']) 
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'minUniqueCharacters') 
			{
				if ($stats['uniqueCharacters'] < $rule['value']) 
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notBlacklisted') 
			{
				if (Blacklist::basedOnBlackList($password)) 
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notNameBased') 
			{
				if ($name == null) 
				{
					$xuser = Profile::getInstance($user);

					if (!is_object($xuser)) 
					{
						continue;
					}

					$givenName = $xuser->get('givenName');
					$middleName = $xuser->get('middleName');
					$surname = $xuser->get('surname');

					$name = $givenName;

					if (!empty($middleName)) {
						if (empty($name)) {
							$name = $middleName;
						}
						else {
							$name .= " " . $middleName;
						}
					}

					if (!empty($surname)) {
						if (empty($name)) {
							$name = $surname;
						}
						else {
							$name .= " " . $surname;
						}
					}
				}

				if (self::isBasedOnName($password,$name)) {
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notUsernameBased') {
				if (is_numeric($user)) {
					$juser = \JUser::getInstance($user);

					if (!is_object($juser)) {
						continue;
					}

					$user = $juser->get('username');
				}

				if (self::isBasedOnUsername($password,$user)) {
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notReused') {
				$date = new \DateTime('now');
				$date->modify("-" . $rule['value'] . "day");

				$phist = History::getInstance($user);
				if (!is_object($phist)) {
					continue;
				}

				if ($phist->exists($password, $date->format("Y-m-d H:i:s"))) {
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notRepeat') {
				if (Password::passwordMatches($user, $password, true)) {
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] === 'true') {
			}
			else {
				$fail[] = $rule['failuremsg'];
			}
		}

		if (empty($fail)) 
		{
			return array();
		}
		else 
		{
			return $fail;
		}
	}

	private static function normalize_word($word)
	{
		$nword = '';

		$len = strlen($word);

		for ($i = 0; $i < $len; $i++) 
		{
			$o = ord( $word[$i] );

			if ($o < 97) 
			{ // convert to lowercase
				$o += 32;
			}

			if ($o > 122 || $o < 97) 
			{ // skip anything not a lowercase letter
				continue;
			}

			$nword .= chr($o);
		}

		return $nword;
	}

	public static function isBasedOnName($word,$name)
	{
		$word = self::normalize_word($word);

		$names = explode(" ", $name);

		$count = count($names);

		$words = array();
		$fullname = self::normalize_word($name);
		$words[] = $fullname;
		$words[] = strrev($fullname);

		foreach ($names as $e) 
		{
			$e = self::normalize_word($e);

			if (strlen($e) > 3) 
			{
				$words[] = $e;
				$words[] = strrev($e);
			}
		}

		if ($count > 1) 
		{
			$e = self::normalize_word($names[0] . $names[$count-1]);
			$words[] = $e;
			$words[] = strrev($e);
		}

		foreach ($words as $w) 
		{
			if (empty($w)) 
			{
				continue;
			}

			if (strpos($w, $word) !== false) 
			{
				return true;
			}
		}

		return false;
	}

	public static function isBasedOnUsername($word,$username)
	{
		$word = self::normalize_word($word);
		$username = self::normalize_word($username);

		$words = array();
		$words[] = $username;
		$words[] = strrev($username);

		foreach ($words as $w) 
		{
			if (empty($w)) 
			{
				continue;
			}

			if (empty($word)) 
			{
				continue;
			}

			if (strpos($w, $word) !== false) 
			{
				return true;
			}
		}

		return false;
	}
}
