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

class WhoisController extends JObject
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
	
	private function getTask()
	{
		$task = $this->access_check();
		$task = ($task) ? $task : JRequest::getVar( 'task', 'view' );
		$this->_task = $task;
		return $task;
	}

	//-----------

	public function execute()
	{
		$juser =& JFactory::getUser();
		$this->_admin = $juser->authorize($this->_option, 'manage');
		
		switch ( $this->getTask() ) 
		{
			case 'restrict': $this->restricted(); break;
			case 'login':    $this->login();      break;
			case 'activity': $this->activity();   break;
			case 'view':     $this->whois();      break;
			
			default: $this->whois(); break;
		}
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//-----------
	
	private function getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file('components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}
	//----------------------------------------------------------
	// Checks
	//----------------------------------------------------------

	protected function access_check()
	{
	    $juser =& JFactory::getUser();
		$xuser =& XFactory::getUser();
		
		if ($juser->get('guest')) {
			return 'login';
		}

		if (!$this->_admin) {
			return 'restrict';
		}
		
		return '';
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function login()
	{
		ximport('xmodule');
		
		//$xhub =& XFactory::getHub();
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Output HTML
		echo WhoisHtml::div( WhoisHtml::hed(2, JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task))), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		XModuleHelper::displayModules('force_mod');
		echo '</div>'.n;
	}
	
	//-----------
	
	protected function restricted()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Output HTML
		echo WhoisHtml::div( WhoisHtml::hed(2, JText::_(strtoupper($this->_name))), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		echo WhoisHtml::error( JText::_('WHOIS_RESTRICTED') );
		echo '</div>'.n;
	}
	
	//-----------

	protected function whois()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Push some styles to the template
		$this->getStyles();
		
		// Incoming
		$query = JRequest::getVar( 'query', '' );
		$uid   = JRequest::getVar( 'username', '' );
		$mail  = JRequest::getVar( 'email', '' );
		
		if ($uid) {
			$search = 'uid=' . $uid;
		} elseif ($mail) {
			$search = 'mail=' . $mail;
		} else {
			$search = $query;
		}

		$html = WhoisHtml::form($this->_option);
		if ($search) {
			$result = $this->_parse_simplesearch($search);
			$logins = $this->_get_uidsbyfilter($result); 
			$summaries = $this->_get_summarybyfilter($result);
			if ((count($logins) <= 0) || ($logins == false))  {
				$html .= WhoisHtml::error( JText::_('WHOIS_NOT_FOUND') );
			} elseif (count($logins) > 1) {
				$html .= WhoisHtml::list_matches($summaries, $this->_option);
			} else {
				$user = $logins[0]['uid'];
				$html .= WhoisHtml::viewaccount_markup($user, true, true, false);
			}
		}
		$html .= '</div>'.n;
		
		// Output HTML
		echo $html;
	}
	
	//-----------
	
	protected function activity()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Push some styles to the template
		$this->getStyles();
		
		$database =& JFactory::getDBO();
		
		// Get logged-in users
		$prevuser = '';
		$user  = array();
		$users = array();

		$sql = "SELECT s.username, x.host, x.ip, (UNIX_TIMESTAMP(NOW()) - s.time) AS idle 
				FROM #__session AS s, #__xsession AS x
				WHERE s.username <> '' AND s.session_id=x.session_id
				ORDER BY username, host, ip, idle DESC";

		$database->setQuery( $sql );
		$result = $database->loadObjectList();

		if ($result && count($result) > 0) {
			foreach ($result as $row)
			{
				if ($prevuser != $row->username) {
					if ($user) {
						//$xlog->logDebug("com_whois::activity($prevuser)");
						$xuser =& XUser::getInstance($prevuser);
						$users[$prevuser] = $user;
						$users[$prevuser]['name'] = $xuser->get('name');
						$users[$prevuser]['org'] = $xuser->get('org');
						$users[$prevuser]['orgtype'] = $xuser->get('orgtype');
						//$users[$prevuser]['countryresident'] = getcountry($xuser->get('countryresident'));
						$users[$prevuser]['countryresident'] = $xuser->get('countryresident');
					}
					$prevuser = $row->username;
					$user = array();
				}
				array_push($user, array('host' => $row->host, 'ip' => $row->ip, 'idle' => $row->idle));
			}
			if ($user) {
				//$xlog->logDebug("com_whois::activity($prevuser)");
				$xuser =& XUser::getInstance($prevuser);
				$users[$prevuser] = $user;
				$users[$prevuser]['name'] = $xuser->get('name');
				$users[$prevuser]['org'] = $xuser->get('org');
				$users[$prevuser]['orgtype'] = $xuser->get('orgtype');
				//$users[$prevuser]['countryresident'] = getcountry($xuser->get('countryresident'));
				$users[$prevuser]['countryresident'] = $xuser->get('countryresident');
			}
		}

		/*$ds = array();
		
		$ipdbo =& WhoisUtils::getIPDBO();
		if ($ipdbo) {
			$ipdbo->setQuery( "SELECT domain FROM domainfilter" );
			$domains = $ipdbo->loadObjectList();
			if ($domains) {
				foreach ($domains as $domain)
				{
					$ds[] = $domain->domain;
				}
			}
		}
		
		$ds = implode("','",$ds);*/
		
		$guests = array();
		/*
		$sql = "SELECT x.host, x.ip, (UNIX_TIMESTAMP(NOW()) - s.time) AS idle 
				FROM #__session AS s, #__xsession AS x 
				WHERE s.username = '' AND s.session_id=x.session_id AND x.domain NOT IN ('$ds')
				ORDER BY host DESC, ip, idle DESC";
		*/
		$sql = "SELECT x.host, x.ip, (UNIX_TIMESTAMP(NOW()) - s.time) AS idle 
				FROM #__session AS s, #__xsession AS x 
				WHERE s.username = '' AND s.session_id=x.session_id
				ORDER BY host DESC, ip, idle DESC";

		$database->setQuery( $sql );
		$result = $database->loadObjectList();
		if ($result) {
			if (count($result) > 0) {
				foreach($result as $row) 
				{
					array_push($guests, array('host' => $row->host, 'ip' => $row->ip, 'idle' => $row->idle));
				}
			}
		}
		
		// Output HTML
		echo WhoisHtml::activity( $this->_option, $users, $guests );
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	private function _get_summarybyfilter() 
	{
		$nargs = func_num_args();

		if ($nargs < 1)
			return false;

		if ($nargs == 1) {
			$args = func_get_arg(0);

			if (!is_array($args)) {
				$args = array( $args );
			}
		} else {
			$args = func_get_args();
		}
		
		return( $this->_get_attrsbyfilter('uid uidNumber mail cn',$args) );
	}
	
	//-----------

	private function _get_uidsbyfilter() 
	{
		$nargs = func_num_args();

		if ($nargs < 1)
			return false;

		if ($nargs == 1) {
			$args = func_get_arg(0);

			if (!is_array($args)) {
				$args = array( $args );
			}
		} else {
			$args = func_get_args();
		}

		return( $this->_get_attrsbyfilter('uid',$args) );
	}

	//-----------

	private function _get_attrsbyfilter() 
	{
		$result = false;
		$filter = '';
		$conn =& XFactory::getLDC();
        $xhub =& XFactory::getHub();
        $ldap_base = $xhub->getCfg('hubLDAPBaseDN');

		$nargs = func_num_args();

		if ( (!$conn) || ($nargs <=1 ))
		    return false;

		$attrs = func_get_arg(0);

		if ($nargs == 2) {
			$args = func_get_arg(1);
			if (!is_array($args)) {
				$args = array( $args );
			}
		} else {
			$args = func_get_args();

			array_shift($args);
		}

		$nargs = count($args);
 
		if ($nargs > 1)
			$filter = '(&';

		for ($i = 0; $i < $nargs; $i++) 
		{
			$arg = $args[$i];
			$filter = $filter .'('. $arg .')';
		}

		if ($nargs > 1)
			$filter = $filter . ")";
			
		if ($conn) {
			$attr_req = explode(' ', $attrs);
			$ldap_base_dn = 'ou=users,'. $ldap_base;
			$userentry = @ldap_search($conn, $ldap_base_dn, $filter, $attr_req, 0, 100, 0 ,3);

			if (!empty($userentry) && ldap_count_entries($conn, $userentry) > 0) {
				$entry = ldap_first_entry($conn, $userentry);

				$result = array();
				$j = 0;
            
				while ( $entry ) 
				{ 
					$attr = ldap_get_attributes($conn, $entry);
	     
					if ($attr[ $attr_req[0] ][0]) {
						for ($i = 0; $i < count($attr_req); $i++) 
						{
							$result[$j][$attr_req[$i]] = $attr[ $attr_req[$i] ][0];
						}
						$j++;
					}

					$entry = ldap_next_entry($conn, $entry);
				}
			}
		}

		return $result;
	}

	//-----------

	private function _parse_email_address($adrstring) 
	{
		$address = '';
		// < > delimited email addresses override any others
		if (ereg("< *(.+)\@(.+) *>", $adrstring, $match) === false)
			ereg("([^ <\"]+)\@([^ >\"]+)", $adrstring, $match);
		$mailbox = $match[1];
		$host    = $match[2];
		// remove email portion to get name portion
		$name = str_replace($match[0], "", $adrstring);
		// strip any exterior parens from name
		if ( ereg("^ *\((.*)\) *$", $name, $match) )
			$name = $match[1];
		// strip any exterior quotes from name
		if ( ereg("^ *\"(.*)\" *$", $name, $match) )
			$name = $match[1];
		$personal=trim($name);

		if ($mailbox && $host)
			$addr = $mailbox .'@'. $host;
		else
			$addr = '';

		return( array($addr, $personal) );
	}

	//-----------

	private function _parse_simplesearch($searchstr) 
	{
		$address = array();
		$subs = preg_split("/\s*,\s*/", $searchstr);

		for ($i=0; $i < count($subs); $i++) {
			if (strlen($subs[$i]) <= 0) {
				;
			}
			else if (preg_match("/^proxyUidNumber\s*(\!?\-?\+?=)\s*([^\s]+)/i", $subs[$i], $match)) {
				$thisresult = 'proxyUidNumber';
				if ($match[1] == "=") {
					$thisresult .= '=';
				}
				elseif ($match[1] == "-=") {
					$thisresult .= '<=';
				}
				elseif ($match[1] == "+=") {
					$thisresult .= '>=';
				}
				if ($match[1] == "!=") {
					$thisresult .= '=';
				}
				$thisresult .= $match[2];
				if ($match[1] == "!=") {
					$result[] = "!(" . $thisresult . ")";
				}
				else {
					$result[] = $thisresult;
				}
			}
			else if (preg_match("/^proxyConfirmed\s*(\!?=)\s*([^\s]+)/i", $subs[$i], $match)) {
				$thisresult = null;
				if (strtolower($match[2]) == "true" || $match[2] == 1 || $match[2] == -1) {
					$thisresult = true;
				}
				elseif (strtolower($match[2]) == "false" || $match[2] == 0) {
					$thisresult = false;
				}
				if($thisresult === true || $thisresult === false) {
					if ($match[1] == "!=") {
						$thisresult = !$thisresult;
					}
					if ($thisresult) {
						$result[] = "&(!(proxyPassword=*))(proxyUidNumber=*)";
					}
					else {
						$result[] = "&(proxyPassword=*)(proxyUidNumber=*)";
					}
				}
			}
			else if (preg_match("/^emailConfirmed\s*(\!?\-?\+?=)\s*([^\s]+)/i", $subs[$i], $match)) {
				$thisresult = 'emailConfirmed';
				if ($match[1] == "=") {
					$thisresult .= '=';
				}
				elseif ($match[1] == "-=") {
					$thisresult .= '<=';
				}
				elseif ($match[1] == "+=") {
					$thisresult .= '>=';
				}
				elseif ($match[1] == "!=") {
					$thisresult .= '=';
				}
				if (strtolower($match[2]) == "true") {
					$thisresult .= "1";
					if ($match[1] != "=" && $match[1] != "!=") {
						$match[1] = "=";
						$thisresult = "";
					}
				}
				elseif (strtolower($match[2]) == "false") {
					$thisresult .= "1";
					if ($match[1] == "=") {
						$match[1] = "!=";
					}
					elseif ($match[1] == "!=") {
						$match[1] = "=";
					}
					else {
						$match[1] = "=";
						$thisresult = "";
					}
				}
				else {
					$thisresult .= $match[2];
				}
				if ($match[1] == "!=") {
					$result[] = "!(" . $thisresult . ")";
				}
				else {
					$result[] = $thisresult;
				}
			}
			else if (preg_match("/^uidNumber\s*(\!?\-?\+?=)\s*([^\s]+)/i", $subs[$i], $match)) {
				$thisresult = 'uidNumber';
				if ($match[1] == "=") {
					$thisresult .= '=';
				}
				elseif ($match[1] == "-=") {
					$thisresult .= '<=';
				}
				elseif ($match[1] == "+=") {
					$thisresult .= '>=';
				}
				if ($match[1] == "!=") {
					$thisresult .= '=';
				}
				$thisresult .= $match[2];
				if ($match[1] == "!=") {
					$result[] = "!(" . $thisresult . ")";
				}
				else {
					$result[] = $thisresult;
				}
			}
			else if (preg_match("/^uid\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = 'uid='. $match[1];
			}
			else if (preg_match("/^username\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = 'uid='. $match[1];
			}
			else if (preg_match("/^login\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = 'uid='. $match[1];
			}
			else if (preg_match("/^(em|m)ail\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = 'mail='. $match[2];
			}
			else if (preg_match("/^name\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = 'cn='. $match[1];
			}
			else if (preg_match("/^cn\s*=\s*([^\s]+)/i", $subs[$i], $match)) {
				$result[] = 'cn='. $match[1];
			}
			else if (preg_match("/=/", $subs[$i], $match)) {
				;
			}
			else if ( preg_match("/^[0-9]+$/", $subs[$i]) ) {
				$result[] = 'uidNumber='. $subs[$i];
			}
			else if ( preg_match("/^[^\s@]+$/", $subs[$i]) ) {
				$result[] = 'uid='. $subs[$i];
			}
			else {
				$address[] = $subs[$i];
			}
		}

		for ($i = 0; $i < count($address); $i++) 
		{
			$addr = $this->_parse_email_address($address[$i]);
			if ($addr[0])
				$result[] = 'mail='. $addr[0];
				if ($addr[1])
					$result[] = 'cn='. $addr[1];
		}
		return( $result );
	}
}
?>
