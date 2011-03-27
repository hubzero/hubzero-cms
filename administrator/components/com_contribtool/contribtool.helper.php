<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
				$juser =& JUser::getInstance( $uid );
				if($juser) {
					$logins[] = $juser->get('username');
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

		$sql = "SELECT * FROM #__tool_statusviews WHERE ticketid='".$ticketid."' AND uid=".$juser->get('id');
			$database->setQuery( $sql );
			$found = $database->loadObjectList();
			if($found) {
				$elapsed = strtotime($when) - strtotime($found[0]->viewed);
				$database->setQuery( "UPDATE #__tool_statusviews SET viewed='".$when."', elapsed='".$elapsed."' WHERE ticketid='".$ticketid."' AND uid=".$juser->get('id'));
					if (!$database->query()) {
						echo "<script type=\"text/javascript\"> alert('".$database->getErrorMsg()."');</script>\n";
						exit;
					}
			}
			else {
				$database->setQuery( "INSERT INTO #__tool_statusviews (uid, ticketid, viewed, elapsed) VALUES (".$juser->get('id').", '".$ticketid."', '".$when."', '500000')");
					if (!$database->query()) {
						echo "<script type=\"text/javascript\"> alert('".$database->getErrorMsg()."');</script>\n";
						exit;
					}
			}

	}


}


?>
