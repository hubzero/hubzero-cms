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
 * Short description for 'AnswersResponse'
 * 
 * Long description (if any) ...
 */
class AnswersResponse extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'qid'
	 * 
	 * @var unknown
	 */
	var $qid        = NULL;  // @var int(11)

	/**
	 * Description for 'answer'
	 * 
	 * @var unknown
	 */
	var $answer     = NULL;  // @var text

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created    = NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by = NULL;  // @var varchar(200)

	/**
	 * Description for 'helpful'
	 * 
	 * @var unknown
	 */
	var $helpful    = NULL;  // @var int(11)

	/**
	 * Description for 'nothelpful'
	 * 
	 * @var unknown
	 */
	var $nothelpful = NULL;  // @var int(11)

	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state      = NULL;  // @var int(3)

	/**
	 * Description for 'anonymous'
	 * 
	 * @var unknown
	 */
	var $anonymous  = NULL;  // @var int(2)

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
		parent::__construct( '#__answers_responses', 'id', $db );
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
		if (trim( $this->answer ) == '') {
			$this->setError( JText::_('Your response must contain text.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getRecords($filters=array())
	{
		$juser =& JFactory::getUser();

		$ab = new ReportAbuse( $this->_db );

		if (isset($filters['qid'])) {
			$qid = $filters['qid'];
		} else {
			$qid = $this->qid;
		}
		if ($qid == null) {
			return false;
		}

		if (!$juser->get('guest')) {
			$query  = "SELECT r.*";
			$query .= ", (SELECT COUNT(*) FROM $ab->_tbl AS a WHERE a.category='answers' AND a.state=0 AND a.referenceid=r.id) AS reports";
			$query .= ", l.helpful AS vote FROM $this->_tbl AS r LEFT JOIN #__answers_log AS l ON r.id=l.rid AND ip='".$filters['ip']."' WHERE r.state!=2 AND r.qid=".$qid;
		} else {
			$query  = "SELECT r.*";
			$query .= ", (SELECT COUNT(*) FROM $ab->_tbl AS a WHERE a.category='answers' AND a.state=0 AND a.referenceid=r.id) AS reports";
			$query .= " FROM $this->_tbl AS r WHERE r.state!=2 AND r.qid=".$qid;
		}
		$query .= " ORDER BY r.state DESC, r.created DESC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getActions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $qid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getActions( $qid=null )
	{
		if ($qid == null) {
			$qid = $this->qid;
		}
		if ($qid == null) {
			return false;
		}

		$query = "SELECT id, helpful, nothelpful, state, created_by FROM $this->_tbl WHERE qid=".$qid." AND state!='2'";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getResponse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      string $ip Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getResponse( $id=null, $ip = null )
	{
		if ($id == null) {
			$id = $this->id;
		}
		if ($id == null) {
			return false;
		}
		if ($ip == null) {
			$ip = $this->ip;
		}
		if ($ip == null) {
			return false;
		}

		$query  = "SELECT r.*, l.helpful AS vote FROM $this->_tbl AS r LEFT JOIN #__answers_log AS l ON r.id=l.rid AND ip='".$ip."' WHERE r.state!=2 AND r.id=".$id;

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'deleteResponse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteResponse( $id=null)
	{
		if ($id == null) {
			$id = $this->id;
		}
		if ($id == null) {
			return false;
		}

		$query  = "UPDATE $this->_tbl SET state='2' WHERE id=".$id;

		$this->_db->setQuery( $query );
		$this->_db->query();
	}

	/**
	 * Short description for 'getIds'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $qid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getIds( $qid=null)
	{
		if ($qid == null) {
			$qid = $this->qid;
		}
		if ($qid == null) {
			return false;
		}

		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE qid=".$qid );
		return $this->_db->loadObjectList();
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
		$filters['sortby'] = '';
		$filters['limit'] = 0;

		$query  = "SELECT COUNT(*) ";
		$query .= $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getResults'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getResults($filters=array())
	{
		$query  = "SELECT m.id, m.answer, m.created, m.created_by, m.helpful, m.nothelpful, m.state, m.anonymous, u.name ";
		$query .= $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters=array())
	{
		$query = "FROM $this->_tbl AS m, #__users AS u WHERE m.created_by=u.username";

		switch ($filters['filterby'])
		{
			case 'all':
				$query .= " AND (m.state=1 OR m.state=0)";
				break;
			case 'accepted':
				$query .= " AND m.state=1";
				break;
			case 'rejected':
			default:
				$query .= " AND m.state=0";
				break;
		}

		if (isset($filters['qid']) && $filters['qid'] > 0) {
			$query .= " AND m.qid=" . $filters['qid'];
		}

		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY " . $filters['sortby'];
		} else {
			if (isset($filters['sort'])) 
			{
				$query .= " ORDER BY " . $filters['sort'] . " " .  $filters['sort_Dir'];
			}
		}

		if (isset($filters['limit']) && $filters['limit'] > 0) {
			$query .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		}

		return $query;
	}
}

