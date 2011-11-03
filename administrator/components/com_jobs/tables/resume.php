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
 * Short description for 'Resume'
 * 
 * Long description (if any) ...
 */
class Resume extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid		= NULL;  // @var int(11)

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created	= NULL;

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title		= NULL;

	/**
	 * Description for 'filename'
	 * 
	 * @var unknown
	 */
	var $filename	= NULL;

	/**
	 * Description for 'main'
	 * 
	 * @var unknown
	 */
	var $main		= NULL;  // tinyint  0 - no, 1 - yes

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
		parent::__construct( '#__jobs_resumes', 'id', $db );
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
		if (intval( $this->uid ) == 0) {
			$this->setError( JText::_('ERROR_MISSING_UID') );
			return false;
		}

		if (trim( $this->filename ) == '') {
			$this->setError( JText::_('ERROR_MISSING_FILENAME') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $name Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function load( $name=NULL )
	{
		if ($name !== NULL) {
			$this->_tbl_key = 'uid';
		}
		$k = $this->_tbl_key;
		if ($name !== NULL) {
			$this->$k = $name;
		}
		$name = $this->$k;
		if ($name === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE $this->_tbl_key='$name' AND main='1' LIMIT 1" );
		//return $this->_db->loadObject( $this );

		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			return false;
		}
	}

	/**
	 * Short description for 'delete_resume'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function delete_resume ($id = NULL)
	{
		if ($id === NULL) {
			$id == $this->id;
		}
		if ($id === NULL) {
			return false;
		}

		$query  = "DELETE FROM $this->_tbl WHERE id=".$id;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getResumeFiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $pile Parameter description (if any) ...
	 * @param      mixed $uid Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getResumeFiles ($pile = 'all', $uid = 0, $admin = 0)
	{
		$query  = "SELECT DISTINCT r.uid, r.filename FROM $this->_tbl AS r ";
		$query .= "JOIN #__jobs_seekers AS s ON s.uid=r.uid ";
		$query .= 	($pile == 'shortlisted' && $uid)  ? " JOIN #__jobs_shortlist AS W ON W.seeker=s.uid AND W.emp=".$uid." AND s.uid != '".$uid."' AND s.uid=r.uid AND W.category='resume' " : "";
		$uid 	 = $admin ? 1 : $uid;
		$query .= 	($pile == 'applied' && $uid)  ? " LEFT JOIN #__jobs_openings AS J ON J.employerid='$uid' JOIN #__jobs_applications AS A ON A.jid=J.id AND A.uid=s.uid AND A.status=1 " : "";
		$query .= "WHERE s.active=1 AND r.main=1 ";

		$files = array();

		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();

		if ($result) {
			foreach ($result as $r)
			{
				$files[$r->uid] = $r->filename;
			}
		}

		$files = array_unique($files);
		return $files;
	}
}

