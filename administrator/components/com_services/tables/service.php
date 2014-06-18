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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Short description for 'Service'
 *
 * Long description (if any) ...
 */
class Service extends JTable
{

	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	var $id          	= NULL;  // @var int(11) Primary key

	/**
	 * Description for 'title'
	 *
	 * @var unknown
	 */
	var $title       	= NULL;  // @var varchar(250)

	/**
	 * Description for 'category'
	 *
	 * @var unknown
	 */
	var $category    	= NULL;  // @var varchar(50)

	/**
	 * Description for 'alias'
	 *
	 * @var unknown
	 */
	var $alias		 	= NULL;  // @var varchar(50)

	/**
	 * Description for 'description'
	 *
	 * @var unknown
	 */
	var $description 	= NULL;  // @var varchar(250)

	/**
	 * Description for 'unitprice'
	 *
	 * @var unknown
	 */
	var $unitprice   	= NULL;  // @var float

	/**
	 * Description for 'pointsprice'
	 *
	 * @var unknown
	 */
	var $pointsprice   	= NULL;  // @var int(11)

	/**
	 * Description for 'currency'
	 *
	 * @var unknown
	 */
	var $currency    	= NULL;  // @var varchar(11)

	/**
	 * Description for 'maxunits'
	 *
	 * @var unknown
	 */
	var $maxunits 		= NULL;  // @var int(11)

	/**
	 * Description for 'minunits'
	 *
	 * @var unknown
	 */
	var $minunits   	= NULL;  // @var int(11)

	/**
	 * Description for 'unitsize'
	 *
	 * @var unknown
	 */
	var $unitsize   	= NULL;  // @var int(11)

	/**
	 * Description for 'status'
	 *
	 * @var unknown
	 */
	var $status   		= NULL;  // @var int(11)

	/**
	 * Description for 'restricted'
	 *
	 * @var unknown
	 */
	var $restricted   	= NULL;  // @var int(11)

	/**
	 * Description for 'ordering'
	 *
	 * @var unknown
	 */
	var $ordering   	= NULL;  // @var int(11)

	/**
	 * Description for 'unitmeasure'
	 *
	 * @var unknown
	 */
	var $unitmeasure    = NULL;  // @var varchar

	/**
	 * Description for 'changed'
	 *
	 * @var unknown
	 */
	var $changed     	= NULL;  // @var datetime

	/**
	 * Description for 'params'
	 *
	 * @var unknown
	 */
	var $params   		= NULL;  // @var text

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
		parent::__construct( '#__users_points_services', 'id', $db );
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
		if (trim( $this->alias ) == '') {
			$this->_error = 'Entry must have an alias.';
			return false;
		}
		if (trim( $this->category ) == '') {
			$this->_error = 'Entry must have a category.';
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'loadService'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $alias Parameter description (if any) ...
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadService( $alias=NULL, $id = NULL )
	{
		if ($alias === NULL && $id === NULL) {
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE ";
		if ($alias) {
			$query .= "alias='$alias' ";
		} else {
			$query .= "id='$id' ";
		}

		$this->_db->setQuery(  $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'getServices'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $category Parameter description (if any) ...
	 * @param      integer $completeinfo Parameter description (if any) ...
	 * @param      integer $active Parameter description (if any) ...
	 * @param      string $sortby Parameter description (if any) ...
	 * @param      string $sortdir Parameter description (if any) ...
	 * @param      string $specialgroup Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getServices($category = NULL, $completeinfo = 0, $active = 1, $sortby = 'category', $sortdir = 'ASC', $specialgroup='', $admin = 0)
	{
		$services = array();

		$query  = "SELECT s.* ";
		$query .= $specialgroup ? " , m.gidNumber as ingroup ": "";
		$query .= "FROM $this->_tbl AS s ";

		// do we have special admin group
		if ($specialgroup) {
			$juser 	  = JFactory::getUser();

			$query .= "JOIN #__xgroups AS xg ON xg.cn='".$specialgroup."' ";
			$query .= " LEFT JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber AND m.uidNumber='".$juser->get('id')."' ";
		}

		$query .= "WHERE 1=1 ";
		if ($category) {
			$query .= "AND s.category ='$category' ";
		}
		if ($active) {
			$query .= "AND s.status = 1 ";
		}
		if (!$admin) {
			$query .= $specialgroup ? "AND (s.restricted = 0 or (s.restricted = 1 AND m.gidNumber IS NOT NULL )) " : " AND s.restricted = 0 ";
		}
		$query .= " ORDER BY $sortby $sortdir ";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if ($result) {
			foreach ($result as $r)
			{
				if ($completeinfo) {
					$services[] = $r;
				} else {
					$services[$r->id] = $r->title;
				}

			}
		}

		return $services;
	}

	/**
	 * Short description for 'getServiceCost'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $id Parameter description (if any) ...
	 * @param      integer $points Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getServiceCost($id, $points = 0)
	{
		if ($id === NULL) {
			return false;
		}

		if ($points) {
			$this->_db->setQuery( "SELECT pointsprice FROM $this->_tbl WHERE id='$id'" );
		} else {
			$this->_db->setQuery( "SELECT unitprice FROM $this->_tbl WHERE id='$id'" );
		}
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getUserService'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      string $field Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getUserService( $uid = NULL, $field = 'alias', $category = 'jobs')
	{
		if ($uid === NULL) {
			return false;
		}

		$field = $field ? 's.'.$field : 's.*';

		$query  = "SELECT $field  ";
		$query .= "FROM $this->_tbl as s ";
		$query .= "JOIN #__users_points_subscriptions AS y ON s.id=y.serviceid  ";

		$query .= "WHERE s.category = '$category' AND y.uid = '$uid' ";
		$query .= " ORDER BY y.id DESC LIMIT 1 ";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

