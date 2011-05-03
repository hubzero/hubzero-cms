<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2008-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class XImportController extends JObject
{
        private $_name  = NULL;
        private $_data  = array();
        private $_task  = NULL;

        //-----------

        public function __construct( $config=array() )
        {
                $this->_redirect = NULL;
                $this->_message = NULL;
                $this->_messageType = 'message';

                //Set the controller name
                if (empty( $this->_name ))
                {
                        if (isset($config['name']))  {
                                $this->_name = $config['name'];
                        }
                        else
                        {
                                $r = null;
                                if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
                                        echo "Controller::__construct() : Can't get or parse class name.";
                                }
                                $this->_name = strtolower( $r[1] );
                        }
                }

                $this->_option = 'com_'.$this->_name;
        }

        //-----------

        public function __set($property, $value)
        {
                $this->_data[$property] = $value;
        }

        //-----------

        public function __get($property)
        {
                if (isset($this->_data[$property])) {
                        return $this->_data[$property];
                }
        }

        //-----------

		public function execute()
        {
			// Load the component config
            //$config = new XImportConfig( $this->_option );
            //$this->config = $config;
            
			$default = 'browse';
                
           	$task = strtolower(JRequest::getVar('task', '', 'default'));
            $this->_task = $task;

			if (!$this->authorize()) {
				echo "Access Restricted";
				return;
			}

		// ximport/authors?override=1

           if ($task == 'authors')
		{
			$override = JRequest::getVar('override',false,'get');
			$override = $override ? true : false;
			$this->import_authors($override);
		}
		else if ($task == 'fixnames')
		{
			$this->fixnames();
		}
		else if ($task == 'importtrac')
		{
			$this->importtrac();
		}
		else if ($task == 'importusers')
		{
			$this->importusers();
		}
		else if ($task == 'importgroups')
		{
			$this->importgroups();
		}
		else if ($task == 'showusers')
		{
			$this->showusers();
		}
		else if ($task == 'compareusers')
		{
			$this->compareusers();
		}
		else if ($task == 'comparelicenses')
		{
			$this->comparelicenses();
		}
		else if ($task == 'comparegroups')
		{
			$this->comparegroups();
		}
		else if ($task == 'groupcreated')
		{
			$this->moveGroupCreatedDate();
		}
		else
			$this->showlist();

		// ximport/author/id?override=1
        }
        
        //----------------------------------------------------------
        // Redirect functions
        //----------------------------------------------------------
        
        public function redirect()
        {       
                if ($this->_redirect != NULL) {
                        $app =& JFactory::getApplication();
                        $app->redirect( $this->_redirect, $this->_message );
                }
        }

	private function authorize()
        {
                // Check if they are logged in
                $juser =& JFactory::getUser();
                if ($juser->get('guest'))
                        return false;

                // Check if they're a site admin (from Joomla)
                if ($juser->authorize($this->_option, 'manage'))
                        return true;

                return false;
        }

	public function showlist()
	{
	     $upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$result=$upconfig->get('bankaccounts');
		var_dump($result);
		if ($result) { echo "yes";  }

	    	$xprofile = new Hubzero_User_Profile();
		$xprofile->load(5444);
		$regip = $xprofile->get('shadowExpire');
		echo '<a href="/ximport/authors?override=1">Import Author Data (overwrite)</a><br>' . "\n";
		echo '<a href="/ximport/authors">Import Author Data (update) <br>' . "\n";
		echo '<a href="/ximport/fixnames">Import givenName/middleName/surname from name</a><br>' . "\n";
		echo '<a href="/ximport/importtrac">Import trac permissions from old form</a><br>' . "\n";
		echo '<a href="/ximport/importusers">Import user profiles from ldap</a><br>' . "\n";
		echo '<a href="/ximport/importgroups">Import groups from ldap</a><br>' . "\n";
		echo '<a href="/ximport/showusers">show users from ldap</a><br>' . "\n";
		echo '<a href="/ximport/compareusers">compare users from ldap</a><br>' . "\n";
		echo '<a href="/ximport/comparelicenses">compare licenses from ldap</a><br>' . "\n";
		echo '<a href="/ximport/comparegroups">compare groups from ldap</a><br>' . "\n";
		echo "<br>";
		echo '<a href="/ximport/groupcreated">Take group create date from group logs</a><br>' . "\n";
	}

	public function fixname($name)
	{
		$xprofile = new Hubzero_User_Profile();
 
		if ($xprofile->load($name) === false)
			 echo "Error loading $name\n";
		else
		{
			$firstname = $xprofile->get('givenName');
			$middlename = $xprofile->get('middleName');
			$lastname = $xprofile->get('surname');
			$name = $xprofile->get('name');
			$username = $xprofile->get('username');

			if ( empty($firstname) && empty($middlename) && empty($surname) && empty($name))
			{
				$name = $username;
				$firstname = $username;
			}
			else if ( empty($firstname) && empty($middlename) && empty($surname) )
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
					$lastname = $words[1];
				}
				else if ($count == 3)
				{
					$firstname = $words[0];
					$middlename = $words[1];
					$lastname = $words[2];
				}
				else
				{
					$firstname = $words[0];
					$lastname = $words[$count-1];
					$middlename = $words[1];
				
					for($i = 2; $i < $count-1; $i++)
						$middlename .= ' ' .$words[$i];
				}

				// TODO:
				// if firstname all caps, and lastname isn't, switch them
				// reparse names with " de , del ,  in them
        		}

			$xprofile->set('name', $name);
			$xprofile->set('givenName', $firstname);
			$xprofile->set('middleName', $middlename);
			$xprofile->set('surname', $lastname);
			$xprofile->update();
			echo "saved $name as [$firstname] [$middlename] [$lastname] <br>\n";
    		}
	}

	public function fixnames()
	{
		$db = JFactory::getDBO();

                echo "fixing names...<br>";

                $query = "SELECT uidNumber FROM #__xprofiles;";

                $db->setQuery($query);

                $result = $db->query();

                if ($result === false)
                {
                    echo 'Error retrieving data from xprofiles table: ' . $db->getErrorMsg();
                    return false;
                }

                while ($row = mysql_fetch_assoc( $result ))
                    $this->fixname($row['uidNumber']);

                mysql_free_result( $result );
	}

	public function import_author($row = null, $override = false)
	{
		if ($row == 0)
			return;

		if (!is_array($row))
		{
			$query = "SELECT * FROM #__author WHERE id ='$row`;";
			$db->setQuery($query);
			$result = $db->query();
			$row = $db->loadAssoc();
		}

		$xprofile = Hubzero_User_Profile::getInstance( $row['id'] );

		if (!is_object($xprofile))
		{
			echo 'Failed to load profile for ' . $row['id'] . "<br>\n";
			return;
		}

		$xprofile->setParam('show_bio','1');
		$xprofile->setParam('show_url','1');
		$xprofile->setParam('show_picture','1');
		$xprofile->setParam('show_organization','1');
		$xprofile->update();

		if (($xprofile->get('givenName') == '' || $override) && !empty($row['firstname']))
			$xprofile->set('givenName', $row['firstname']);
		if (($xprofile->get('middleName') == '' || $override) && !empty($row['middlename']))
			$xprofile->set('middlename', $row['middlename']);
		if (($xprofile->get('surname') == '' || $override) && !empty($row['lastname']))
			$xprofile->set('surname', $row['lastname']);
		if (($xprofile->get('organization') == '' || $override) && !empty($row['org']))
			$xprofile->set('organization', $row['org']);
		if (($xprofile->get('bio') == '' || $override) && !empty($row['bio']))
			$xprofile->set('bio', $row['bio']);
		if (($xprofile->get('url') == '' || $override) && !empty($row['url']))
			$xprofile->set('url', $row['url']);
		if (($xprofile->get('picture') == '' || $override) && !empty($row['picture']))
			$xprofile->set('picture', $row['picture']);
		if (($xprofile->get('vip') == '' || $override) && !empty($row['principal_investigator']))
			$xprofile->set('vip', $row['principal_investigator']);
		if (($xprofile->get('name') == '' || $override) && !(empty($row['firstname']) && empty($row['middlename']) && empty($row['lastname'])))
		{
			$name = '';
			if (!empty($row['firstname']))
				$name .= $row['firstname'];
			if (!empty($row['middlename']))
				$name .= ' ' . $row['middlename'];
			if (!empty($row['lastname']))
				$name .= ' ' . $row['lastname'];

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
			echo 'Imported author data into profile for user ' . $xprofile->get('name') . '(' . $xprofile->get('uidNumber') . ')' . '<br>';
		else
			echo 'Failed to import author data into profile for user ' . $xprofile->get('name') . '(' . $xprofile->get('uidNumber') . ')' . '<br>';

		return;
	}

	public function import_authors($override = false)
	{
		$db = JFactory::getDBO();
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

		echo "importing authors...<br>";

                $query = "SELECT * FROM #__author;";

                $db->setQuery($query);

                $result = $db->query();

                if ($result === false)
                {
                    echo 'Error retrieving data from xprofiles table: ' . $db->getErrorMsg();
                    return false;
                }

                while ($row = mysql_fetch_assoc( $result ))
                    $this->import_author($row, $override);

                mysql_free_result( $result );

		return true;
	}

	private function importuser($name)
	{
       		$profile = new Hubzero_User_Profile();
        	$profile->load($name,'ldap');

        	$result = $profile->create('mysql');

        	if ($result === false)
                	echo "Error importing $name<br>";
        	else
                	echo "Imported $name<br>";
	}

        public function importusers()
        {
                $db = JFactory::getDBO();

                echo "import users...<br>";

                $query = "SELECT username FROM #__users;";

                $db->setQuery($query);

                $result = $db->query();

                if ($result === false)
                {
                    echo 'Error retrieving data from juser table: ' . $db->getErrorMsg();
                    return false;
                }

                while ($row = mysql_fetch_assoc( $result ))
                    $this->importuser($row['username']);

                mysql_free_result( $result );
        }

	function importgroup($group)
	{
		die('this function is now in groups admin component');
	}
        
	function importgroups()
	{
		die('this function is now in groups admin component');
	}

	function importtrac()
	{
		include 'itrac.php';
		_importtrac();
	}

	function showusers()
	{
		include 'iuser.php';

		_showusers();
	}

	function compareusers()
	{
		include 'iuser.php';

		_compareusers();
	}

	function comparelicenses()
	{
	    	include 'ilicense.php';
		_comparelicenses();
	}

    function comparegroups()
    {
        include 'igroups.php';
        _comparegroups();
    }

	//------
	
	//function to add created date and created by to xgroups table
	//take this data from xgroups_logs table
	//Added May 3, 2011 by Chris Smoak
	
	function moveGroupCreatedDate()
	{
		//instatiate database
		$db =& JFactory::getDBO();
		
		//import group library
		ximport('Hubzero_Group');
		
		//select all logs where group was created
		$sql = "SELECT * FROM #__xgroups_log WHERE action='group_created'";
		$db->setQuery($sql);
		$logs = $db->loadAssocList();
		
		foreach($logs as $log) {
			echo $log['gid'];
			$group = Hubzero_Group::getInstance($log['gid']);
			if(is_object($group)) {
				$group->set('created',$log['timestamp']);
				$group->set('created_by', $log['actorid']);
				$group->update();
			}
		}
	}
	
}

