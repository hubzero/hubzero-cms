<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Wishlist'
 * 
 * Long description (if any) ...
 */
class Wishlist extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         	= NULL;  // @var int(11) Primary key

	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category       = NULL;  // @var varchar(50)

	/**
	 * Description for 'referenceid'
	 * 
	 * @var unknown
	 */
	var $referenceid	= NULL;  // @var int(11)

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description	= NULL;  // @var text

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title			= NULL;  // @var varchar(150)

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created    	= NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by 	= NULL;  // @var int(11)

	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state     		= NULL;  // @var int(3)

	/**
	 * Description for 'public'
	 * 
	 * @var unknown
	 */
	var $public			= NULL;  // @var int(3)  // can any user view and submit to it?

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__wishlist', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('Missing title for the wish list') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'get_wishlistID'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $rid Parameter description (if any) ...
	 * @param      string $cat Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function get_wishlistID($rid=0, $cat='resource')
	{
		if ($rid === NULL) {
			$rid = $this->referenceid;
		}
		if ($rid === NULL) {
			return false;
		}

		// get individuals
		$sql = "SELECT id"
			. "\n FROM $this->_tbl "
			. "\n WHERE referenceid='".$rid."' AND category='".$cat."' ORDER BY id DESC LIMIT 1";

		$this->_db->setQuery( $sql );
		return  $this->_db->loadResult();
	}

	/**
	 * Short description for 'createlist'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $category Parameter description (if any) ...
	 * @param      unknown $refid Parameter description (if any) ...
	 * @param      integer $public Parameter description (if any) ...
	 * @param      string $title Parameter description (if any) ...
	 * @param      string $description Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function createlist($category='resource', $refid, $public=1, $title='', $description='')
	{
		if ($refid === NULL) {
			return false;
		}

		$xhub =& Hubzero_Factory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$juser =& JFactory::getUser();

		$this->created = date( 'Y-m-d H:i:s' );
		$this->category = $category;
		$this->created_by = $juser->get('id');
		$this->referenceid = $refid;
		$this->description = $description;
		$this->public = $public;

		switch ($category)
		{
			case 'general':
				$this->title = $title ? $title : $hubShortName;

				if (!$this->store()) {
					$this->_error = $this->getError();
					return false;
				} else {
					// Checkin wishlist
					$this->checkin();
				}

				return $this->id;
			break;

			case 'resource':
				// resources can only have one list
				if (!$this->get_wishlist('',$refid, 'resource')) {
					$this->title = $title ? $title :'Resource #'.$rid;

					if (!$this->store()) {
						$this->_error = $this->getError();
						return false;
					} else {
						// Checkin wishlist
						$this->checkin();
					}

					return $this->id;
				} else {
					return $this->get_wishlistID($refid); // return existing id
				}
			break;

			case 'group':
				$this->title = $title ? $title :'Group #'.$rid;
				if (!$this->store()) {
					$this->_error = $this->getError();
					return false;
				} else {
					// Checkin wishlist
					$this->checkin();
				}

				return $this->id;
			break;

			case 'user':
				$this->title = $title;
				if (!$this->store()) {
					$this->_error = $this->getError();
					return false;
				} else {
					// Checkin wishlist
					$this->checkin();
				}

				return $this->id;
			break;
		}

		return 0;
	}

	/**
	 * Short description for 'getTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getTitle($id)
	{
		if ($id === NULL) {
			return false;
		}
		$sql = "SELECT w.title "
				. "\n FROM $this->_tbl AS w";
		$sql .=	"\n WHERE w.id=".$id;

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'is_primary'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function is_primary($id)
	{
		if ($id === NULL) {
			return false;
		}
		$sql = "SELECT w.* FROM $this->_tbl AS w WHERE w.id=".$id." AND w.referenceid=1 AND w.category='general'";

		$this->_db->setQuery( $sql );
		$bingo = $this->_db->loadResult();
		if ($bingo) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'get_wishlist'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      mixed $refid Parameter description (if any) ...
	 * @param      string $cat Parameter description (if any) ...
	 * @param      integer $primary Parameter description (if any) ...
	 * @param      integer $getversions Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function get_wishlist($id='', $refid=0, $cat='', $primary = 0, $getversions=0)
	{
		if ($id===NULL && $refid===0 && $cat===NULL) {
			return false;
		}
		if ($id && !intval($id)) {
			return false;
		}
		if ($refid && !intval($refid)) {
			return false;
		}

		$sql = "SELECT w.*";
		//if($cat == 'resource') {
			//$sql .= "\n , r.title as resourcetitle, r.type as resourcetype, r.alias, r.introtext";
		//}
			$sql .= "\n FROM $this->_tbl AS w";
		//if($cat == 'resource') {
			//$sql .= "\n JOIN #__resources AS r ON r.id=w.referenceid";	
		//}
		if ($id) {
			$sql .=	"\n WHERE w.id=".$id;
		} else if ($refid && $cat) {
			$sql .=	"\n WHERE w.referenceid=".$refid." AND w.category='".$cat."'";
		} else if ($primary) {
			$sql .=	"\n WHERE w.referenceid=1 AND w.category='general'";
		}

		$this->_db->setQuery( $sql );
		$res = $this->_db->loadObjectList();
		$wishlist = ($res) ? $res[0] : array();

		// get parent 
		//$parent = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);

		if (count($wishlist) > 0 && $wishlist->category=='resource') {
			$wishlist->resource = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);
			// Currenty for tools only
			if ($getversions && $wishlist->resource && isset($wishlist->resource->type) && $wishlist->resource->type==7) {
				$wishlist->resource->versions = $this->get_parent_versions($wishlist->referenceid, $wishlist->resource->type );
			}
		}

		return $wishlist;
	}
	//-----------

	/**
	 * Short description for 'get_parent_versions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      integer $type Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function get_parent_versions($rid, $type)
	{
		$versions = array();
		// currently for tools only
		if ($type == 7) {
			$query = "SELECT v.id FROM #__tool_version as v JOIN #__resources as r ON r.alias = v.toolname WHERE r.id='".$rid."'";
			$query.= " AND v.state=3 ";
			$query.= " OR v.state!=3 ORDER BY state DESC, revision DESC LIMIT 3";
			$this->_db->setQuery( $query );
			$result  = $this->_db->loadObjectList();
			$versions = $result ? $result : array();
		}

		return $versions;
	}

	/**
	 * Short description for 'get_wishlist_parent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $refid Parameter description (if any) ...
	 * @param      string $cat Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function get_wishlist_parent($refid, $cat='resource')
	{
		$resource = array();
		if ($cat == 'resource') {
			$sql = "SELECT r.title, r.type, r.alias, r.introtext, t.type as typetitle"
				. "\n FROM #__resources AS r"
				. "\n LEFT JOIN #__resource_types AS t ON t.id=r.type "
				. "\n WHERE r.id='".$refid."'";
			$this->_db->setQuery( $sql );
			$res  = $this->_db->loadObjectList();
			$resource = ($res) ? $res[0]: array();
		}

		return $resource;
	}

	/**
	 * Short description for 'getCons'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $refid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCons($refid)
	{
		$sql = "SELECT n.uidNumber AS id"
			 . "\n FROM #__xprofiles AS n"
			 . "\n JOIN #__author_assoc AS a ON n.uidNumber=a.authorid"
			 . "\n WHERE a.subtable = 'resources'"
			 . "\n AND a.subid=". $refid;

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getToolDevGroup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $refid Parameter description (if any) ...
	 * @param      array $groups Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getToolDevGroup($refid, $groups = array())
	{
		$query  = "SELECT g.cn FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= " JOIN #__tool AS t ON g.toolid=t.id ";
		$query .= " JOIN #__resources as r ON r.alias = t.toolname";
		$query .= " WHERE r.id = '".$refid."' AND g.role=1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

