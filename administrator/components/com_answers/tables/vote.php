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
 * Short description for 'Vote'
 * 
 * Long description (if any) ...
 */
class Vote extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id      		= NULL;  // @var int(11) Primary key

	/**
	 * Description for 'referenceid'
	 * 
	 * @var unknown
	 */
	var $referenceid    = NULL;  // @var int(11)

	/**
	 * Description for 'voted'
	 * 
	 * @var unknown
	 */
	var $voted 			= NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'voter'
	 * 
	 * @var unknown
	 */
	var $voter   		= NULL;  // @var int(11)

	/**
	 * Description for 'helpful'
	 * 
	 * @var unknown
	 */
	var $helpful     	= NULL;  // @var varchar(11)

	/**
	 * Description for 'ip'
	 * 
	 * @var unknown
	 */
	var $ip      		= NULL;  // @var varchar(15)

	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category     	= NULL;  // @var varchar(50)

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
		parent::__construct( '#__vote_log', 'id', $db );
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
		if (trim( $this->referenceid ) == '') {
			$this->setError( JText::_('Missing reference ID') );
			return false;
		}
		return true;

		if (trim( $this->category ) == '') {
			$this->setError( JText::_('Missing category') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'checkVote'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $refid Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $voter Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function checkVote($refid=null, $category=null, $voter=null)
	{
		if ($refid == null) {
			$refid = $this->referenceid;
		}
		if ($refid == null) {
			return false;
		}
		if ($category == null) {
			$category = $this->category;
		}
		if ($category == null) {
			return false;
		}

		$now = date( 'Y-m-d H:i:s', time() );

		$query = "SELECT count(*) FROM $this->_tbl WHERE referenceid='".$refid."' AND category = '".$category."' AND voter='".$voter."'";

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
	public function getResults( $filters=array() )
	{
		$query = "SELECT c.* 
				FROM $this->_tbl AS c 
				WHERE c.referenceid=".$filters['id']." AND category='".$filters['category']."' ORDER BY c.voted DESC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

