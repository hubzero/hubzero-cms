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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Short description for 'EventsPage'
 * 
 * Long description (if any) ...
 */
class EventsPage extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id          = NULL;  // int(11)

	/**
	 * Description for 'event_id'
	 * 
	 * @var string
	 */
	var $event_id    = NULL;  // int(11)

	/**
	 * Description for 'alias'
	 * 
	 * @var unknown
	 */
	var $alias       = NULL;  // string(100)

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title       = NULL;  // string(250)

	/**
	 * Description for 'pagetext'
	 * 
	 * @var unknown
	 */
	var $pagetext    = NULL;  // text

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created     = NULL;  // datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by  = NULL;  // int(11)

	/**
	 * Description for 'modified'
	 * 
	 * @var unknown
	 */
	var $modified    = NULL;  // datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'modified_by'
	 * 
	 * @var unknown
	 */
	var $modified_by = NULL;  // int(11)

	/**
	 * Description for 'ordering'
	 * 
	 * @var string
	 */
	var $ordering    = NULL;  // int(11)

	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $params      = NULL;  // text

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
		parent::__construct( '#__events_pages', 'id', $db );
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
			$this->setError( JText::_('You must enter an alias.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'loadFromAlias'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $alias Parameter description (if any) ...
	 * @param      unknown $event_id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadFromAlias( $alias=NULL, $event_id=NULL )
	{
		if ($alias === NULL) {
			return false;
		}
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE alias='$alias' AND event_id='$event_id'" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'loadFromEvent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $event_id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadFromEvent( $event_id=NULL )
	{
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE event_id='$event_id' ORDER BY ordering ASC LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'loadPages'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $event_id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function loadPages( $event_id=NULL )
	{
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT title, alias, id FROM $this->_tbl WHERE event_id='$event_id' ORDER BY ordering ASC" );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'deletePages'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $event_id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function deletePages( $event_id=NULL )
	{
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE event_id='$event_id'" );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getNeighbor'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $move Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function getNeighbor( $move )
	{
		switch ($move)
		{
			case 'orderup':
			case 'orderuppage':
				$sql = "SELECT * FROM $this->_tbl WHERE event_id=".$this->event_id." AND ordering < ".$this->ordering." ORDER BY ordering DESC LIMIT 1";
				break;

			case 'orderdown':
			case 'orderdownpage':
				$sql = "SELECT * FROM $this->_tbl WHERE event_id=".$this->event_id." AND ordering > ".$this->ordering." ORDER BY ordering LIMIT 1";
				break;
		}
		$this->_db->setQuery( $sql );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters)
	{
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query = "SELECT t.*, NULL as position";
		} else {
			$query = "SELECT count(*)";
		}
		$query .= " FROM $this->_tbl AS t";
		if (isset($filters['event_id']) && $filters['event_id'] != '') {
			$query .= " WHERE t.event_id='".$filters['event_id']."'";
		}
		if (isset($filters['search']) && $filters['search'] != '') {
			if (isset($filters['event_id']) && $filters['event_id'] != '') {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
			}
			$query .= "LOWER( t.title ) LIKE '%".$filters['search']."%'";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " ORDER BY t.ordering ASC LIMIT ".$filters['start'].",".$filters['limit'];
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
	public function getCount( $filters=array() )
	{
		$filters['limit'] = 0;

		$this->_db->setQuery( $this->buildQuery( $filters ) );
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
	public function getRecords( $filters=array() )
	{
		$this->_db->setQuery( $this->buildQuery( $filters ) );
		return $this->_db->loadObjectList();
	}
}

