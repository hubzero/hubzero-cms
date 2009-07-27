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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class ToolAuthor extends  JTable
{
	var $toolname     	   = NULL;  // @var varchar (50)
	var $revision          = NULL;  // @var int (11)
	var $uid      	   	   = NULL;  // @var int (11)
	var $ordering     	   = NULL;  // @var int (11)
	var $version_id        = NULL;  // @var int (11)
	
	
	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__tool_authors', 'version_id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		
		if (!$this->version_id) {
			$this->setError( JText::_('CONTRIBTOOL_ERROR_AUTHOR_NO_VERSIONID') );
			return false;
		}

		if (!$this->uid) {
			$this->setError( JText::_('CONTRIBTOOL_ERROR_AUTHOR_NO_UID') );
			return false;
		}
		
		if (!$this->toolname) {
			$this->setError( JText::_('CONTRIBTOOL_ERROR_AUTHOR_NO_TOOLNAME') );
			return false;
		}
		if (!$this->revision) {
			$this->setError( JText::_('CONTRIBTOOL_ERROR_AUTHOR_NO_REVISION') );
			return false;
		}
		
		return true;
	}
	
	
	//-----------
	
	public function getToolContributions($uid)
	{
		if (!$uid) {
			return false;
		}
		$sql = " SELECT f.toolname FROM #__tool as f "
				."JOIN #__tool_groups AS g ON f.id=g.toolid AND g.role=1 "
				."JOIN #__xgroups AS xg ON g.cn=xg.cn "
				."JOIN #__xgroups_managers AS m ON xg.gidNumber=m.gidNumber AND uidNumber='$uid' ";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	
	}
	
	//-----------
	
	public function getToolAuthors($version='', $rid=0, $toolname='', $revision='', $authors=array())
	{
		if($version=='dev' && $rid) {
			$query = "SELECT authorid as uidNumber FROM #__author_assoc WHERE subid= ".$rid." AND subtable='resources' ORDER BY ordering";
			$this->_db->setQuery( $query );
			$authors = $this->_db->loadObjectList();
		}
		else  {
			$query  = "SELECT DISTINCT a.uid as uidNumber ";
			$query .= "FROM #__tool_authors as a  ";
			if($version=='current' && $toolname) {
				$objV = new ToolVersion($this->_db);
				$rev = $objV->getCurrentVersionProperty ($toolname, 'revision');
				if($rev) {
					$query .= "JOIN #__tool_version as v ON a.toolname=v.toolname AND a.revision=v.revision WHERE a.toolname= '".$toolname."' AND a.revision='".$rev."'";
				} else {
					$query .= "JOIN #__tool_version as v ON a.toolname=v.toolname AND a.revision=v.revision WHERE a.toolname= '".$toolname."' AND v.state=1 ORDER BY v.revision DESC";
				}
			}
			else if(intval($version)) {
			$query .= "WHERE a.version_id= '".$version."' ORDER BY a.ordering";
			}
			else if($toolname && $revision) {
			$query .= "WHERE a.toolname = '".$toolname."' AND a.revision = '".$revision."' ORDER BY a.ordering";
			}
			$this->_db->setQuery( $query );
			$authors = $this->_db->loadObjectList();
		}	
		return $authors;
	}
	//-----------

	public function saveAuthors($authors, $version='dev', $rid=0, $revision=0, $toolname='') 
	{
		if (!$rid) {
			return false;
		}
		
		if($authors) { $authors = ContribtoolHelper::transform($authors, 'uidNumber'); }

		$dev_authors = $this->getToolAuthors('dev', $rid);
		$dev_authors = ContribtoolHelper::transform($dev_authors, 'uidNumber');
		
		if($dev_authors && $version=='dev') {
			// update 
			$to_delete = array_diff($current_authors, $authors);
			if($to_delete) {
				foreach($to_delete as $del) {
					$query = "DELETE FROM #__author_assoc  WHERE authorid='".$del."' AND subid= ".$rid." AND subtable='resources'";
					$this->_db->setQuery( $query );
					$this->_db->query();
				}
			}			
		}
		
		// add new authors
		if($version=='dev') {
			// development version is updated
			
			$to_delete = array_diff($dev_authors, $authors);
			
			$rc = new ResourcesContributor($this->_db);
			$rc->subtable = 'resources';
			$rc->subid = $rid;
			
			if($to_delete ) {
				foreach($to_delete as $del) {
					$query = "DELETE FROM #__author_assoc  WHERE authorid='".$del."' AND subid= ".$rid." AND subtable='resources'";
					$this->_db->setQuery( $query );
					$this->_db->query();
				}
			}
			// Get the last child in the ordering
			$order = $rc->getLastOrder( $rid, 'resources' );
			$order = $order + 1; // new items are always last
			
			foreach ($authors as $authid) {
				// Check if they're already linked to this resource
				$rc->loadAssociation( $authid, $rid, 'resources' );
				if (!$rc->authorid) {
					$xuser =& JUser::getInstance( $authid );
					if ($xuser) {
						$this->_author_check($xuser);
	
						// New record
						$rc->authorid = $authid;
						$rc->ordering = $order;
						if($rc->createAssociation()) {
							return false;
						}
	
						$order++;
					}
				}			
			}
		}
		else if($dev_authors) {
			// new version is being published, transfer data from author_assoc
			$i=0;
			foreach($dev_authors as $authid) {
				$xuser =& JUser::getInstance( $authid );
				if($xuser) {
					$this->_author_check($xuser);
					$query = "INSERT INTO #__tool_authors (toolname, revision, uid, ordering, version_id) VALUES ('".$toolname."','".$revision."','".$authid."','".$i."', '".$version."')";
					$this->_db->setQuery( $query );
					if(!$this->_db->query()) {
						return false;
					}
					$i++;
				}
			}
		}
		
		return true;
	}
	//-----------

	public function _author_check($xuser)
	{
		if (is_object($xuser)) {
			$xprofile =& XProfile::getInstance($xuser->get('id'));
			if (is_object($xprofile)) {
				if ($xprofile->get('givenName') == '' && $xprofile->get('middleName') == '' && $xprofile->get('surname') == '') {
					$bits = explode(' ', $xuser->get('name'));
					$xprofile->set('surname', array_pop($bits));
					if (count($bits) >= 1) {
						$xprofile->set('givenName', array_shift($bits));
					}
					if (count($bits) >= 1) {
						$xprofile->set('middleName', implode(' ',$bits));
					}
				}
			}
		}
	}

	
	//-----------

}


?>