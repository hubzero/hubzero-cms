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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Blog Entry database class
//----------------------------------------------------------

/**
 * Short description for 'BlogEntry'
 * 
 * Long description (if any) ...
 */
class BlogEntry extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id           = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title        = NULL;  // @var varchar(150)

	/**
	 * Description for 'alias'
	 * 
	 * @var unknown
	 */
	var $alias        = NULL;  // @var varchar(150)

	/**
	 * Description for 'content'
	 * 
	 * @var unknown
	 */
	var $content      = NULL;  // @var text

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created      = NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by   = NULL;  // @var int(11)

	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state        = NULL;  // @var int(3)

	/**
	 * Description for 'publish_up'
	 * 
	 * @var unknown
	 */
	var $publish_up   = NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'publish_down'
	 * 
	 * @var unknown
	 */
	var $publish_down = NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $params		  = NULL;  // @var text

	/**
	 * Description for 'group_id'
	 * 
	 * @var unknown
	 */
	var $group_id     = NULL;  // @var int(11)

	/**
	 * Description for 'hits'
	 * 
	 * @var unknown
	 */
	var $hits         = NULL;  // @var int(11)

	/**
	 * Description for 'allow_comments'
	 * 
	 * @var unknown
	 */
	var $allow_comments = NULL;  // @var int(2)

	/**
	 * Description for 'scope'
	 * 
	 * @var unknown
	 */
	var $scope        = NULL;  // @var varchar(100)

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
		parent::__construct( '#__blog_entries', 'id', $db );
	}

	/**
	 * Short description for 'loadAlias'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @param      unknown $scope Parameter description (if any) ...
	 * @param      unknown $created_by Parameter description (if any) ...
	 * @param      unknown $group_id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadAlias( $oid=NULL, $scope=NULL, $created_by=NULL, $group_id=NULL )
	{
		if ($oid === NULL) {
			return false;
		}
		if ($scope === NULL) {
			$scope = $this->scope;
		}
		if (!$scope) {
			return false;
		}
		switch ($scope)
		{
			case 'member':
				if ($created_by === NULL) {
					$created_by = $this->created_by;
				}
				if (!$created_by) {
					return false;
				}
				$query = "SELECT * FROM $this->_tbl WHERE alias='$oid' AND scope='$scope' AND created_by='$created_by'";
			break;

			case 'group':
				if ($group_id === NULL) {
					$group_id = $this->group_id;
				}
				if (!$group_id) {
					return false;
				}
				//$query = "SELECT * FROM $this->_tbl WHERE alias='$oid' AND scope='$scope' AND group_id='$group_id'";
				$query = "SELECT * FROM $this->_tbl WHERE alias='$oid' AND group_id='$group_id'";
			break;

			default:
				$query = "SELECT * FROM $this->_tbl WHERE alias='$oid' AND scope='$scope'";
			break;
		}
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
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
			$this->setError( JText::_('Please provide a title.') );
			return false;
		}
		if (trim( $this->content ) == '') {
			$this->setError( JText::_('Please provide content.') );
			return false;
		}
		if (!$this->created_by) {
			$this->setError( JText::_('Missing creator ID.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getEntriesCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getEntriesCount($filters=array())
	{
		$filters['limit'] = 0;
		$query = "SELECT COUNT(*) ".$this->_buildAdminQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getEntries'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getEntries($filters=array())
	{
		$bc = new BlogComment( $this->_db );

		$query = "SELECT m.*, (SELECT COUNT(*) FROM ".$bc->getTableName()." AS c WHERE c.entry_id=m.id) AS comments, u.name ".$this->_buildAdminQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for '_buildAdminQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _buildAdminQuery($filters)
	{
		$filters['scope'] = 'site';

		$nullDate = $this->_db->getNullDate();
		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$query  = "FROM $this->_tbl AS m,
					#__xprofiles AS u  
					WHERE m.scope='".$filters['scope']."' AND m.created_by=u.uidNumber ";
		if (isset($filters['created_by']) && $filters['created_by'] != 0) {
			$query .= " AND m.created_by=".$filters['created_by'];
		}
		if (isset($filters['group_id']) && $filters['group_id'] != 0) {
			$query .= " AND m.group_id=".$filters['group_id'];
		}
		if (isset($filters['state']) && $filters['state'] != '') {
			switch ($filters['state'])
			{
				case 'public':
					$query .= " AND m.state=1";
				break;
				case 'registered':
					$query .= " AND m.state>0";
				break;
				case 'private':
					$query .= " AND m.state=0";
				break;
			}
		}
		if (isset($filters['search']) && $filters['search'] != '') {
			$filters['search'] = strtolower(stripslashes($filters['search']));
			$query .= " AND (LOWER(m.title) LIKE '%".$filters['search']."%' OR LOWER(m.content) LIKE '%".$filters['search']."%')";
			//$query .= " AND ( (MATCH(m.title) AGAINST ('".addslashes($filters['search'])."') > 0) OR (MATCH(m.content) AGAINST ('".addslashes($filters['search'])."') > 0) )";
		}
		if (isset($filters['order']) && $filters['order'] != '') {
			$query .= " ORDER BY ".$filters['order'];
		} else {
			$query .= " ORDER BY publish_up DESC";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		return $query;
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;
		$query = "SELECT COUNT(*) ".$this->_buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords($filters=array())
	{
		$bc = new BlogComment( $this->_db );

		$query = "SELECT m.*, (SELECT COUNT(*) FROM ".$bc->getTableName()." AS c WHERE c.entry_id=m.id) AS comments, u.name ".$this->_buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for '_buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _buildQuery($filters)
	{
		$nullDate = $this->_db->getNullDate();
		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$query  = "FROM $this->_tbl AS m,
					#__xprofiles AS u  
					WHERE m.scope='".$filters['scope']."' AND m.created_by=u.uidNumber ";

		if (isset($filters['year']) && $filters['year'] != 0) {
			if (isset($filters['month']) && $filters['month'] != 0) {
				$startmonth = $filters['year'].'-'.$filters['month'].'-01 00:00:00';

				if ($filters['month']+1 == 13) {
					$year = $filters['year'] + 1;
					$month = 1;
				} else {
					$month = ($filters['month']+1);
					$year = $filters['year'];
				}
				$endmonth = sprintf( "%4d-%02d-%02d 00:00:00",$year,$month,1);

				$query .= " AND m.publish_up >= ".$this->_db->Quote($startmonth)." AND m.publish_up < ".$this->_db->Quote($endmonth)." ";
			} else {
				$startyear = $filters['year'].'-01-01 00:00:00';
				$endyear = ($filters['year']+1).'-01-01 00:00:00';

				$query .= " AND m.publish_up >= ".$this->_db->Quote($startyear)." AND m.publish_up < ".$this->_db->Quote($endyear)." ";
			}
		} else {
			$query .= "AND (m.publish_up = ".$this->_db->Quote($nullDate)." OR m.publish_up <= ".$this->_db->Quote($now).") 
					AND (m.publish_down = ".$this->_db->Quote($nullDate)." OR m.publish_down >= ".$this->_db->Quote($now).")";
		}
		if (isset($filters['created_by']) && $filters['created_by'] != 0) {
			$query .= " AND m.created_by=".$filters['created_by'];
		}
		if (isset($filters['group_id']) && $filters['group_id'] != 0) {
			$query .= " AND m.group_id=".$filters['group_id'];
		}
		if (isset($filters['state']) && $filters['state'] != '') {
			switch ($filters['state'])
			{
				case 'public':
					$query .= " AND m.state=1 AND u.public=1 ";
				break;
				case 'registered':
					$query .= " AND m.state>0";
				break;
				case 'private':
					$query .= " AND m.state=0";
				break;
			}
		}
		if (isset($filters['search']) && $filters['search'] != '') {
			$filters['search'] = strtolower(stripslashes($filters['search']));
			$query .= " AND (LOWER(m.title) LIKE '%".$filters['search']."%' OR LOWER(m.content) LIKE '%".$filters['search']."%')";
			//$query .= " AND ( (MATCH(m.title) AGAINST ('".addslashes($filters['search'])."') > 0) OR (MATCH(m.content) AGAINST ('".addslashes($filters['search'])."') > 0) )";
		}
		if (isset($filters['order']) && $filters['order'] != '') {
			$query .= " ORDER BY ".$filters['order'];
		} else {
			$query .= " ORDER BY publish_up DESC";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		return $query;
	}

	/**
	 * Short description for 'deleteComments'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteComments($id=null)
	{
		if (!$id) {
			$id = $this->id;
		}
		if (!$id) {
			$this->setError( JText::_('Missing Entry ID.') );
			return false;
		}

		$bc = new BlogComment( $this->_db );

		$this->_db->setQuery( "DELETE FROM ".$bc->getTableName()." WHERE entry_id=$id" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Short description for 'deleteFiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteFiles($id=null)
	{
		// Build the file path
		/*$path = JPATH_ROOT;
		$config = $this->config;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $config->get('uploadpath').DS.$member->get('uidNumber');

		if (is_dir($path)) { 
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($path)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
				return false;
			}
		}*/
		return true;
	}

	/**
	 * Short description for 'deleteTags'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteTags($id=null)
	{
		if (!$id) {
			$id = $this->id;
		}
		if (!$id) {
			$this->setError( JText::_('Missing Entry ID.') );
			return false;
		}

		$bt = new BlogTags( $this->_db );
		if (!$bt->remove_all_tags($id)) {
			$this->setError( JText::_('UNABLE_TO_DELETE_TAGS') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getPopularEntries'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getPopularEntries($filters=array())
	{
		$filters['order'] = 'hits DESC';

		$bc = new BlogComment( $this->_db );

		$query = "SELECT m.id, m.alias, m.title, created_by, publish_up, (SELECT COUNT(*) FROM ".$bc->getTableName()." AS c WHERE c.entry_id=m.id) AS comments, u.name ".$this->_buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getRecentEntries'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecentEntries($filters=array())
	{
		$filters['order'] = 'publish_up DESC';

		$bc = new BlogComment( $this->_db );

		$query = "SELECT m.id, m.alias, m.title, created_by, publish_up, (SELECT COUNT(*) FROM ".$bc->getTableName()." AS c WHERE c.entry_id=m.id) AS comments, u.name ".$this->_buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getDateOfFirstEntry'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getDateOfFirstEntry($filters=array())
	{
		$filters['order'] = 'publish_up ASC';
		$filters['limit'] = 1;
		$filters['start'] = 0;
		$filters['year'] = 0;
		$filters['month'] = 0;

		$query = "SELECT publish_up ".$this->_buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

