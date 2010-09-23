<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/*

	Hubzero_Session::getInstance();
		Fetch Hubzero_Session data for current session, or create object for current session

	$xsession->set('ip', $ip);
	$xsession->update();

	INSERT (session_id,ip) VALUES ($session_id,$ip) ON DUPLICATE KEY UPDATE ip=$ip;

	DELETE xs from jos_xsession xs LEFT OUTER JOIN jos_session s ON s.session_id = xs.session_id WHERE s.session_id IS NULL;
	DELETE xs FROM jos_xsession AS xs LEFT OUTER JOIN jos_session AS s ON s.session_id = xs.session_id WHERE s.session_id IS NULL;

	Hubzero_Session_Helper::purge();
	Hubzero_Session_Helper::set_ip();


*/

class Hubzero_Session_Helper
{
	public function purge()
	{
		$db = JFactory::getDBO();
		
		$query = 'DELETE LOW_PRIORITY xs FROM jos_xsession AS xs LEFT OUTER JOIN jos_session AS s ON s.session_id = xs.session_id WHERE s.session_id IS NULL;';
		$db->setQuery($query);
		$db->query();
	}
	
	//-----------

	public function set_ip($session_id, $ip)
	{
		$db = JFactory::getDBO();
		
		$query = 'INSERT INTO jos_xsession (session_id,ip) VALUES (' . $db->Quote($session_id) . ',' . $db->Quote($ip) . ') ON DUPLICATE KEY UPDATE ip=' . $db->Quote($ip) . ';';
		$db->setQuery($query);
		$db->query();
		$query = 'UPDATE jos_session SET ip=' . $db->Quote($ip) . " WHERE session_id=" . $db->Quote($session_id) . ";";
		$db->setQuery($query);
		$db->query();
	}
}	

/*  Rough drafts at a couple option to flesh out a proper Hubzero_Session class. Unneeded in core at this time. */
/*
class JTableHubzeroSession extends JTable
{
	var $session_id = NULL;
	var $ip = NULL;

	function __construct( &$db )
        {
                parent::__construct( '#__xsession', 'session_id', $db );
        }

	function create()
        {
                $ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
                if (!$ret) {
                        $this->setError(get_class( $this ).'::create failed - '.$this->_db->getErrorMsg());
                        return false;
                } else {
                        return true;
                }
        }

	function purge()
	{
		$query = 'DELETE LOW_PRIORITY xs FROM jos_xsession AS xs LEFT OUTER JOIN jos_session AS s ON s.session_id = xs.session_id WHERE s.session_id IS NULL;';

		$this->_db->setQuery($query);

		$result = $this->_db->query();
	}
}


class Hubzero_Session extends JObject
{
	// properties
        private $session_id = '';
	private $password = '';
	private $ip = '';
	private $host = '';
	private $domain = '';
	private $signed = '';
	private $countrySHORT = '';
	private $countryLONG = '';
	private $ipREGION = '';
	private $ipCITY = '';
	private $ipLATITUDE = '';
	private $ipLONGITUDE = '';
	private $bot = '';

	public function setError($msg)
        {
                $bt = debug_backtrace();

                $error = "Hubzero_Session::" . $bt[1]['function'] . "():" . $msg;

                array_push($this->_errors, $error);
        }

        private function logDebug($msg)
        {
                $xlog =& Hubzero_Factory::getLogger();
                $xlog->logDebug($msg);

                return true;
        }

        public function clear()
        {
                $classvars = get_class_vars('Hubzero_Session');

                foreach ($classvars as $property => $value)
                {
                        if ('_s_' == substr($property, 0, 3)) // don't touch static variables
                                continue;

                        unset($this->$property);
                        $this->$property = $value;
                }

                $objvars = get_object_vars($this);

                foreach ($objvars as $property => $value)
                {
                        if (!array_key_exists($property, $classvars))
                                unset($this->$property);
                }

                return true;
        }

        static function getInstance($session_id = null)
        {
                $instance = new Hubzero_Session($session_id);

                if ($instance->get('session_id') == '')
                        return false;

                return $instance;
        }

	public function get($property)
        {
                if ('_' == substr($property, 0, 1))
                {
                        $this->setError("Can't access private properties");
                        return false;
                }

                if (!property_exists('Hubzero_Session',$property))
                {
                        $this->setError("Unknown property: $property");
                        return false;
                }

                return $this->$property;
        }

        public function set($property,$value)
        {
                if ('_' == substr($property, 0, 1))
                {
                        $this->setError("Can't access private properties");
                        return false;
                }

                if (!property_exists('Hubzero_User_Profile', $property))
                {
                        $this->setError("Unknown property: $property");
                        return false;
                }

                $this->$property = $value;

                return true;
        }

	private function load($session_id = null)
        {
                $db = &JFactory::getDBO();

                if (empty($user))
                {
                        $this->setError('No session specified');
                        return false;
                }

                $query = "SELECT * FROM #__xsession WHERE session_id = " . $db->Quote($session_id) . ";";

                $db->setQuery($query);

                $result = $db->loadAssoc();

                if ($result === false)
                {
                        $this->setError('Error retrieving data from xsession table: ' . $db->getErrorMsg());
                        return false;
                }

                if (empty($result))
                {
                        $this->setError('No such session [' . $session_id . ']');
                        return false;
                }

                $this->clear();

                foreach($result as $property=>$value)
                        $this->set($property,$value);

                $classvars = get_class_vars('Hubzero_Session');

                foreach ($classvars as $property => $value)
                {
                        if ('_auxv_' == substr($property, 0, 6) || '_auxs_' == substr($property, 0, 6))
                                $this->$property = false; // this property is loaded on demand
                }

                return true;
        }

	private function update()
        {
                $db = &JFactory::getDBO();

                $query = "UPDATE #__xsession SET ";

                $classvars = get_class_vars('Hubzero_Session');
                $first = true;

                foreach ($classvars as $property => $value)
                {
                        if ('_' == substr($property, 0, 1))
                                continue;

                        if (!$first)
                                $query .= ',';
                        else
                                $first = false;

                        $query .= "$property=" . $db->Quote($this->get($property));
                }

                $query .= " WHERE session_id=" . $db->Quote($this->get('session_id')) . ";";

                $db->setQuery( $query );

                if (!$db->query())
                {
                        $this->setError('Error updating data in xsession table: ' . $db->getErrorMsg());
                        return false;
                }

                return true;
        }

        private function create()
        {
                $db = &JFactory::getDBO();
                $xhub = &Hubzero_Factory::getHub();

                $modifiedDate = gmdate('Y-m-d H:i:s');

                if (is_numeric($this->get('uidNumber')))
                {
                        $query = "INSERT INTO #__xsession (session_id,time,guest,usertype,password,ip,host,domain,signed,countrySHORT,countryLONG,ipREGION,ipCITY,ipLATITUDE,ipLONGITUDE) VALUE ("
                                . $db->Quote($this->get('uidNumber')) . ','
                                . $db->Quote($this->get('username')) . ','
                                . $db->Quote($modifiedDate) . ");";

                        $db->setQuery( $query );

                        if (!$db->query())
                        {
                                $errno = $db->getErrorNum();

                                if ($errno == 1062)
                                        $this->setError('uidNumber (' . $this->get('uidNumber')
                                                . ') already exists'
                                                . ' in xprofiles table');
                                else
                                        $this->setError('Error inserting user data to xprofiles table: '
                                                . $db->getErrorMsg());

                                return false;
                        }
                }
                else
                {
                        $token = uniqid();

                        $query = "INSERT INTO #__xprofiles (uidNumber,username,modifiedDate) SELECT "
                                . "IF(MIN(uidNumber)>0,-1,MIN(uidNumber)-1),"
                                . $db->Quote($token) . ',' . $db->Quote($modifiedDate) . " FROM #__xprofiles;";

                        $db->setQuery( $query );

                        if (!$db->query())
                        {
                                $this->setError('Error inserting non-user data to xprofiles table: '
                                        . $db->getErrorMsg());

                                return false;
                        }

                        $query = "SELECT uidNumber from #__xprofiles WHERE username=" . $db->Quote($token)
                                . " AND modifiedDate=" . $db->Quote($modifiedDate);

                        $db->setQuery($query);

                        $result = $db->loadResultArray();

                        if ($result === false)
                        {
                                $this->setError('Error adding data to xprofiles table: '
                                        . $db->getErrorMsg());

                                return false;
                        }

                        if (count($result) > 1)
                        {
                                $this->setError('Error adding data to xprofiles table: '
                                        . $db->getErrorMsg());

                                return false;
                        }

                        $this->set('uidNumber', $result[0]);
                }

                if ($this->_mysql_update() === false)
                        return false;

                return true;
        }

}
*/
