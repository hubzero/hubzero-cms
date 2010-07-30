<?php
/**
 * @package     HUBzero CMS
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 *
 * Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3 as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Hubzero_Password_Rule
{
    public function getRules($group = null, $all = false)
    {
        $db = &JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        if (empty($group))
            $group = "'%'";
	else
	    $group = $db->Quote($group);

        $query = "SELECT id,rule,class,value,description,failuremsg FROM " .
            "#__password_rule WHERE `group` LIKE $group";

        if ($all == false)
            $query .= " AND enabled='1'";

        $query .= " ORDER BY ordering ASC;";

        $db->setQuery($query);

        $result = $db->loadAssocList();

        return $result;
    }

    public function analyze($password)
    {
		ximport('Hubzero_Password_CharacterClass');

        $stats = array();
		$len = strlen($password);
		$stats['count'][0] = $len;
		$stats['uniqueCharacters'] = 0;
		$stats['uniqueClasses'] = 0;
		$classes = array();
		$histogram = array();

		for($i = 0; $i < $len; $i++)
		{
			$c = $password[$i];

	        $cl = Hubzero_Password_CharacterClass::match($c);

			foreach($cl as $class)
			{
				if (empty($stats['count'][$class->name]))
				{
					$stats['count'][$class->name] = 1;
					if ($class->flag)
						$stats['uniqueClasses']++;
				}
				else
					$stats['count'][$class->name]++;
			}
			
		    if (empty($histogram[$c]))
			{
				$histogram[$c] = 1;
				$stats['uniqueCharacters']++;
			}
			else
				$histogram[$c]++;
		}

		return $stats;
	}

    public function validate($password, $rules, $user, $name=null)
    {
		ximport('Hubzero_Password_Blacklist');
		ximport('Hubzero_Users_Password_History');
		$fail = array();
		$stats = self::analyze($password);

		foreach($rules as $rule)
		{
			if ($rule['rule'] == 'minCharacterClasses')
			{
				if ($stats['uniqueClasses'] < $rule['value'])
					$fail[] = $rule['failuremsg'];
			}
			else if ($rule['rule'] == 'maxCharacterClasses')
			{
				if ($stats['uniqueClasses'] > $rule['value'])
					$fail[] = $rule['failuremsg'];
			}
			else if ($rule['rule'] == 'minPasswordLength')
			{
				if ($stats['count'][0] < $rule['value'])
					$fail[] = $rule['failuremsg'];
			}
			else if ($rule['rule'] == 'maxPasswordLength')
			{
				if ($stats['count'][0] > $rule['value'])
					$fail[] = $rule['failuremsg'];
			}
			else if ($rule['rule'] == 'maxClassCharacters')
			{
				if (empty($rule['class']))
					continue;

				$class = $rule['class'];

				if (empty($stats['count'][$class]))
					$stats['count'][$class] = 0;

				if ($stats['count'][$class] > $rule['value'])
					$fail[] = $rule['failuremsg'];
			}
			else if ($rule['rule'] == 'minClassCharacters')
			{
				if (empty($rule['class']))
					continue;

				$class = $rule['class'];

				if (empty($stats['count'][$class]))
					$stats['count'][$class] = 0;

				if ($stats['count'][$class] < $rule['value'])
					$fail[] = $rule['failuremsg'];
			}
			else if ($rule['rule'] == 'minUniqueCharacters')
			{
				if ($stats['uniqueCharacters'] < $rule['value'])
					$fail[] = $rule['failuremsg'];
			}
			else if ($rule['rule'] == 'notBlacklisted')
			{
				if (Hubzero_Password_Blacklist::basedOnBlackList($password))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notNameBased')
			{
				ximport('xprofile');

				if ($name == null)
				{
					$xuser = XProfile::getInstance($user);

					if (!is_object($xuser))
						continue;

					$givenName = $xuser->get('givenName');
				   	$middleName = $xuser->get('middleName');
				   	$surname = $xuser->get('surname');

					$name = $givenName;

					if (!empty($middleName))
					{
						if (empty($name))
							$name = $middleName;
						else
							$name .= " " . $middleName;
					}

					if (!empty($surname))
					{
						if (empty($name))
							$name = $surname;
						else
							$name .= " " . $surname;
					}
				}

				if (self::isBasedOnName($password,$name))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notUsernameBased')
			{
				if (is_numeric($user))
				{
					$juser = JUser::getInstance($user);

					if (!is_object($juser))
						continue;

					$user = $juser->get('username');
				}

				if (self::isBasedOnUsername($password,$user))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notReused')
			{
				$passhash = "{MD5}" . base64_encode(pack('H*', md5($password)));

				$date = new DateTime('now');
				$date->modify("-" . $rule['value'] . "day");

				$phist = Hubzero_Users_Password_History::getInstance($user);
				if (!is_object($phist))
					continue;

				if ($phist->exists($passhash, $date->format("Y-m-d H:i:s")))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notRepeat')
			{
				if (Hubzero_Users_Password::passwordMatches($user, $password))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] === 'true')
			{
				
			}
			else
				$fail[] = $rule['failuremsg'];
		}

		if (empty($fail))
			return true;
		else
			return $fail;
	}

	private function normalize_word($word)
	{
		$nword = '';

		$len = strlen($word);

		for($i = 0; $i < $len; $i++)
		{
			$o = ord( $word[$i] );

			if ($o < 97) // convert to lowercase
            	$o += 32;

			if ($o > 122 || $o < 97) // skip anything not a lowercase letter
            	continue;
			$nword .= chr($o);
		}

		return $nword;
	}

	public function isBasedOnName($word,$name)
	{
		$word = self::normalize_word($word);

		$names = explode(" ", $name);

		$count = count($names);

		$words = array();
		$fullname = self::normalize_word($name);
		$words[] = $fullname;
		$words[] = strrev($fullname);

		foreach($names as $e)
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

		foreach($words as $w)
		{
			if (empty($w))
				continue;
		
			if (strpos($w, $word) !== false)
				return true;
		}

		return false;
	}

	public function isBasedOnUsername($word,$username)
	{
		$word = self::normalize_word($word);
		$username = self::normalize_word($username);

		$words = array();
		$words[] = $username;
		$words[] = strrev($username);
		
       	foreach($words as $w)
		{
			if (empty($w))
				continue;

			if (empty($word))
				continue;

			if (strpos($w, $word) !== false)
                return true;
		}

        return false;
	}
}
