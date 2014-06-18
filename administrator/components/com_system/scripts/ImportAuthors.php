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

/**
 * Script for importing authors
 */
class ImportAuthors extends SystemHelperScript
{
	/**
	 * Description
	 *
	 * @var string
	 */
	protected $_description = 'Import user profiles from LDAP.';

	/**
	 * Options
	 *
	 * @var array
	 */
	protected $_options = array(array('override' => '1'));

	/**
	 * Run the script
	 *
	 * @return     boolean
	 */
	public function run()
	{
		$override = JRequest::getVar('override', false);
		$override = $override ? true : false;
		/*
			+------------------------+--------------+------+-----+---------+----------------+
			| Field                  | Type         | Null | Key | Default | Extra          |
			+------------------------+--------------+------+-----+---------+----------------+
			| id                     | int(11)      | NO   | PRI | NULL    | auto_increment |
			| firstname              | varchar(32)  | NO   | MUL |         |                |
			| middlename             | varchar(32)  | NO   |     |         |                |
			| lastname               | varchar(32)  | NO   |     |         |                |
			| org                    | varchar(100) | NO   |     |         |                |
			| bio                    | text         | NO   |     |         |                |
			| url                    | varchar(250) | NO   |     |         |                |
			| picture                | varchar(250) | NO   |     |         |                |
			| principal_investigator | tinyint(1)   | NO   |     | 0       |                |
			+------------------------+--------------+------+-----+---------+----------------+

			Iterate through each author.
				Load record.
				Load matching profile.
				Error message if no matching profile.
				Conditionally load first,middle,last names into profile
				Conditionally load composite first,middle,last name into profile
				Conditionally load org into profile
				Conditionally load bio into profile
				Conditionally load url into profile
				Conditionally load picture into profile
				Conditionally load principal_investigator into profile
				Save profile
				Print success
		*/

		echo 'importing authors...<br />';

		$query = "SELECT * FROM #__author;";

		$this->_db->setQuery($query);

		$result = $this->_db->query();

		if ($result === false)
		{
			echo 'Error retrieving data from xprofiles table: ' . $this->_db->getErrorMsg();
			return false;
		}

		foreach ($result as $row)
		{
			$this->_importAuthor($row, $override);
		}

		return true;
	}

	/**
	 * Convert an author into a user account with profile
	 *
	 * @param      array   $row      Associated array of author info
	 * @param      boolean $override Force data push
	 * @return     void
	 */
	private function _importAuthor($row = null, $override = false)
	{
		if ($row == 0)
		{
			return;
		}

		if (!is_array($row))
		{
			$query = "SELECT * FROM #__author WHERE id ='$row`;";
			$this->_db->setQuery($query);
			$result = $this->_db->query();
			$row = $this->_db->loadAssoc();
		}

		$xprofile = \Hubzero\User\Profile::getInstance($row['id']);

		if (!is_object($xprofile))
		{
			echo 'Failed to load profile for ' . $row['id'] . "<br />\n";
			return;
		}

		$xprofile->setParam('show_bio', '1');
		$xprofile->setParam('show_url', '1');
		$xprofile->setParam('show_picture', '1');
		$xprofile->setParam('show_organization', '1');
		$xprofile->update();

		if (($xprofile->get('givenName') == '' || $override) && !empty($row['firstname']))
		{
			$xprofile->set('givenName', $row['firstname']);
		}
		if (($xprofile->get('middleName') == '' || $override) && !empty($row['middlename']))
		{
			$xprofile->set('middlename', $row['middlename']);
		}
		if (($xprofile->get('surname') == '' || $override) && !empty($row['lastname']))
		{
			$xprofile->set('surname', $row['lastname']);
		}
		if (($xprofile->get('organization') == '' || $override) && !empty($row['org']))
		{
			$xprofile->set('organization', $row['org']);
		}
		if (($xprofile->get('bio') == '' || $override) && !empty($row['bio']))
		{
			$xprofile->set('bio', $row['bio']);
		}
		if (($xprofile->get('url') == '' || $override) && !empty($row['url']))
		{
			$xprofile->set('url', $row['url']);
		}
		if (($xprofile->get('picture') == '' || $override) && !empty($row['picture']))
		{
			$xprofile->set('picture', $row['picture']);
		}
		if (($xprofile->get('vip') == '' || $override) && !empty($row['principal_investigator']))
		{
			$xprofile->set('vip', $row['principal_investigator']);
		}
		if (($xprofile->get('name') == '' || $override) && !(empty($row['firstname']) && empty($row['middlename']) && empty($row['lastname'])))
		{
			$name = '';
			if (!empty($row['firstname']))
			{
				$name .= $row['firstname'];
			}
			if (!empty($row['middlename']))
			{
				$name .= ' ' . $row['middlename'];
			}
			if (!empty($row['lastname']))
			{
				$name .= ' ' . $row['lastname'];
			}

			$name = trim($name);

			$xprofile->set('name', $name);
		}

		$xprofile->setParam('show_bio','1');
		$xprofile->setParam('show_url','1');
		$xprofile->setParam('show_picture','1');
		$xprofile->setParam('show_organization','1');
		$xprofile->set('public','1');

		$result = $xprofile->update();

		if ($result)
		{
			echo 'Imported author data into profile for user ' . $xprofile->get('name') . '(' . $xprofile->get('uidNumber') . ')' . '<br />';
		}
		else
		{
			echo 'Failed to import author data into profile for user ' . $xprofile->get('name') . '(' . $xprofile->get('uidNumber') . ')' . '<br />';
		}

		return;
	}
}
