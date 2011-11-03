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

/**
 * Short description for 'ResourcesReview'
 * 
 * Long description (if any) ...
 */
class ResourcesReview extends JTable
{

	/**
	 * Description for 'resource_id'
	 * 
	 * @var unknown
	 */
	var $resource_id = NULL;  // @var int(11)

	/**
	 * Description for 'user_id'
	 * 
	 * @var unknown
	 */
	var $user_id     = NULL;  // @var int(11)

	/**
	 * Description for 'rating'
	 * 
	 * @var unknown
	 */
	var $rating      = NULL;  // @var decimal(2,1)

	/**
	 * Description for 'comment'
	 * 
	 * @var unknown
	 */
	var $comment     = NULL;  // @var text

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created     = NULL;  // @var datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'anonymous'
	 * 
	 * @var unknown
	 */
	var $anonymous   = NULL;  // @var int(3)

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id          = NULL;  // @var int(11) primary key

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
		parent::__construct( '#__resource_ratings', 'id', $db );
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
		if (trim( $this->rating ) == '') {
			$this->setError( JText::_('Your review must have a rating.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'loadUserReview'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $resourceid Parameter description (if any) ...
	 * @param      string $userid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadUserReview( $resourceid, $userid )
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE resource_id=".$resourceid." AND user_id=".$userid." LIMIT 1" );

		//return $this->_db->loadObject( $this );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'loadUserRating'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $resourceid Parameter description (if any) ...
	 * @param      string $userid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function loadUserRating( $resourceid, $userid )
	{
		$this->_db->setQuery( "SELECT rating FROM $this->_tbl WHERE resource_id=".$resourceid." AND user_id=".$userid." LIMIT 1" );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRatings'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRatings( $id=NULL )
	{
		$juser =& JFactory::getUser();

		if (!$id) {
			$id = $this->resource_id;
		}
		//$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE resource_id=".$id." ORDER BY created DESC" );
		$this->_db->setQuery( "SELECT rr.*, rr.id as id, v.helpful AS vote, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=rr.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=rr.id) AS nothelpful "
			."\n FROM $this->_tbl AS rr "
			."\n LEFT JOIN #__vote_log AS v ON v.referenceid=rr.id AND v.category='review' AND v.voter='".$juser->get('id')."' "
			."\n WHERE rr.resource_id=".$id."  ORDER BY rr.created DESC" );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getRating'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      string $userid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRating( $id=NULL, $userid )
	{
		if (!$userid) {
			$juser =& JFactory::getUser();
			$userid = $juser->get('id');
		}

		if (!$id) {
			$id = $this->resource_id;
		}
		//$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE resource_id=".$id." ORDER BY created DESC" );
		$this->_db->setQuery( "SELECT rr.*, rr.id as id, v.helpful AS vote, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=rr.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=rr.id) AS nothelpful "
			."\n FROM $this->_tbl AS rr "
			."\n LEFT JOIN #__vote_log AS v ON v.referenceid=rr.id AND v.category='review' AND v.voter='".$userid."' "
			."\n WHERE rr.id=".$id." " );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getVote'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getVote( $id, $category = 'review', $uid )
	{
		if (!$id) {
			$id = $this->id;
		}

		if ($id === NULL or $uid === NULL) {
			return false;
		}

		$query  = "SELECT v.helpful ";
		$query .= "FROM #__vote_log as v  ";
		$query .= "WHERE v.referenceid = '".$id."' AND v.category='".$category."' AND v.voter='".$uid."' LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

