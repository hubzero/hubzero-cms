<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

class ContribtoolHelper
{
	//------------------------------------
	// Misceleneous contribtool functions
	//------------------------------------
	
	public function makeArray($string='') {
		
		
		$string		= ereg_replace(' ',',',$string);		
		$arr 		= split(',',$string);
		//$arr 		= $this->cleanArray($arr); 
		$arr 		= ContribtoolHelper::cleanArray($arr); 
		$arr 		= array_unique($arr);
		
		return $arr;
	}
	
	
	//-----------

	public function cleanArray($array) {
        
		foreach ($array as $key => $value) {
			$value = trim($value);
            if ($value == "") unset($array[$key]);
        }
        
		return $array;
	}
	
	//-----------
	
	public function check_validInput($field) 
	{
		if(eregi("^[_0-9a-zA-Z.:-]+$", $field) or $field=='') {
			return(0);
		} else {
			return(1);
		}
	}
	//-----------
	
	public function getLicenses($database) 
	{
		$database->setQuery( "SELECT text, name, title"
				. "\n FROM #__tool_licenses ORDER BY ordering ASC"
				);
		return $database->loadObjectList();
	}
	
	//-----------
	
	public function transform($array, $label, $newarray=array()) {
		if(count($array)>0) {
			foreach($array as $a) {
				if(is_object($a)) {
					$newarray[]= $a->$label;
				}
				else {
					$newarray[]= $a;
				}
			}
		}
		
		return $newarray;
	}
	//-----------
	
	public function getLogins($uids, $logins = array()) {
		if(is_array($uids)) {
			foreach ($uids as $uid) {
				$xuser =& JUser::getInstance( $uid );
				if($xuser) {
					$logins[] = $xuser->get('username');
				}
			}
		}	
		return $logins;
	}	
	//-----------
	public function record_view($database, $ticketid)
	{
		$juser =& JFactory::getUser();
		$when 	   = date( 'Y-m-d H:i:s', time() );

		$sql = "SELECT * FROM #__tool_statusviews WHERE ticketid='".$ticketid."' AND uid=".$xuser->get('uid');
			$database->setQuery( $sql );
			$found = $database->loadObjectList();
			if($found) {
				$elapsed = strtotime($when) - strtotime($found[0]->viewed);
				$database->setQuery( "UPDATE #__tool_statusviews SET viewed='".$when."', elapsed='".$elapsed."' WHERE ticketid='".$ticketid."' AND uid=".$xuser->get('uid'));
					if (!$database->query()) {
						echo "<script type=\"text/javascript\"> alert('".$database->getErrorMsg()."');</script>\n";
						exit;
					}
			}
			else {
				$database->setQuery( "INSERT INTO #__tool_statusviews (uid, ticketid, viewed, elapsed) VALUES (".$xuser->get('uid').", '".$ticketid."', '".$when."', '500000')");
					if (!$database->query()) {
						echo "<script type=\"text/javascript\"> alert('".$database->getErrorMsg()."');</script>\n";
						exit;
					}
			}

	}


}


?>