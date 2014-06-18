<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ini_set('memory_limit', '512M');

/**
 * Script for fixing names
 */
class FixNames extends SystemHelperScript
{
	/**
	 * Description
	 *
	 * @var string
	 */
	protected $_description = 'Import givenName/middleName/surname from name.';

	/**
	 * Run the script
	 *
	 * @return     boolean
	 */
	public function run()
	{
		echo 'Fixing names...<br />';

		$query = "SELECT uidNumber FROM #__xprofiles WHERE (givenName = '' OR givenName IS NULL) AND (surname = '' OR surname IS NULL);";

		$this->_db->setQuery($query);

		$result = $this->_db->loadObjectList();

		if ($result === false)
		{
			echo 'Error retrieving data from xprofiles table: ' . $this->_db->getErrorMsg();
			return false;
		}

		if ($result)
		{
			foreach ($result as $row)
			{
				$this->_fixName($row->uidNumber);
			}
		}
		else
		{
			echo 'No changes to be made.';
		}

		return true;
	}

	/**
	 * Break apart a name into it's respective fields
	 *
	 * @param      string $name User's name
	 * @return     void
	 */
	private function _fixName($name)
	{
		$xprofile = new \Hubzero\User\Profile();

		if ($xprofile->load($name) === false)
		{
			echo "Error loading $name\n";
		}
		else
		{
			$firstname  = $xprofile->get('givenName');
			$middlename = $xprofile->get('middleName');
			$lastname   = $xprofile->get('surname');
			$name       = $xprofile->get('name');
			$username   = $xprofile->get('username');

			if ($firstname && $surname)
			{
				unset($xprofile);

				echo "passed $name as [$firstname] [$lastname] <br />\n";
				return;
			}

			if (empty($firstname) && empty($middlename) && empty($surname) && empty($name))
			{
				$name = $username;
				$firstname = $username;
			}
			else if (empty($firstname) && empty($middlename) && empty($surname))
			{
				$words = explode(' ', $name);
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

			$xprofile->set('name', $name);
			$firstname = trim($firstname);
			if ($firstname)
			{
				$xprofile->set('givenName', $firstname);
			}
			$middlename = trim($middlename);
			if ($middlename)
			{
				$xprofile->set('middleName', $middlename);
			}
			$lastname = trim($lastname);
			if ($lastname)
			{
				$xprofile->set('surname', $lastname);
			}
			$xprofile->update();

			echo "saved $name as [$firstname] [$middlename] [$lastname] <br />\n";
		}

		unset($xprofile);
	}
}
