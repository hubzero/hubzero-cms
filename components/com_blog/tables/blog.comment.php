<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

//----------------------------------------------------------
// Blog Comment database class
//----------------------------------------------------------

class BlogComment extends JTable
{
	var $id         = NULL;  // @var int(11) primary key
	var $entry_id   = NULL;  // @var int(11)
	var $content    = NULL;  // @var text
	var $created    = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $created_by = NULL;  // @var int(11)
	var $anonymous  = NULL;  // @var int(3)
	var $parent     = NULL;  // @var int(11)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__blog_comments', 'id', $db );
	}

	public function check()
	{
		if (trim( $this->content ) == '') {
			$this->setError( JText::_('Your comment must contain text.') );
			return false;
		}
		if (!$this->entry_id) {
			$this->setError( JText::_('Missing entry ID.') );
			return false;
		}
		if (!$this->created_by) {
			$this->setError( JText::_('Missing creator ID.') );
			return false;
		}
		return true;
	}

	public function loadUserComment( $entry_id, $user_id )
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE entry_id=".$entry_id." AND created_by=".$user_id." LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	public function getComments( $entry_id=NULL, $parent=NULL )
	{
		if (!$entry_id) {
			$entry_id = $this->entry_id;
		}
		if (!$parent) {
			$parent = 0;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE entry_id=$entry_id AND parent=$parent ORDER BY created ASC" );
		return $this->_db->loadObjectList();
	}

	public function getAllComments( $entry_id=NULL )
	{
		if (!$entry_id) {
			$entry_id = $this->entry_id;
		}

		$comments = $this->getComments( $entry_id, 0 );
		if ($comments) {
			$ra = null;
			if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'reportabuse.php')) {
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'reportabuse.php' );
				$ra = new ReportAbuse( $this->_db );
			}
			foreach ($comments as $key => $row)
			{
				if ($ra) {
					$comments[$key]->reports = $ra->getCount( array('id'=>$comments[$key]->id, 'category'=>'blog') );
				}
				$comments[$key]->replies = $this->getComments( $entry_id, $row->id );
				if ($comments[$key]->replies) {
					foreach ($comments[$key]->replies as $ky => $rw)
					{
						if ($ra) {
							$comments[$key]->replies[$ky]->reports = $ra->getCount( array('id'=>$rw->id, 'category'=>'blog') );
						}
						$comments[$key]->replies[$ky]->replies = $this->getComments( $entry_id, $rw->id );
						if ($comments[$key]->replies[$ky]->replies && $ra) {
							foreach ($comments[$key]->replies[$ky]->replies as $kyy => $rwy)
							{
								$comments[$key]->replies[$ky]->replies[$kyy]->reports = $ra->getCount( array('id'=>$rwy->id, 'category'=>'blog') );
							}
						}
					}
				}
			}
		}
		return $comments;
	}

	public function deleteChildren( $id=NULL )
	{
		if (!$id) {
			$id = $this->id;
		}

		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE parent=".$id );
		$comments = $this->_db->loadObjectList();
		if ($comments) {
			foreach ($comments as $row)
			{
				// Delete abuse reports
				/*$this->_db->setQuery( "DELETE FROM #__abuse_reports WHERE referenceid=".$row->id." AND category='blog'" );
				if (!$this->_db->query()) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}*/
				// Delete children
				$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE parent=".$row->id );
				if (!$this->_db->query()) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}
			}
			$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE parent=".$id );
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}
		return true;
	}
}

