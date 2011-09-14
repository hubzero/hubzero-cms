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
// XForum database class
//----------------------------------------------------------


/**
 * Short description for 'XForum'
 * 
 * Long description (if any) ...
 */
class XForum extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'topic'
	 * 
	 * @var unknown
	 */
	var $topic      = NULL;  // @var varchar(255)


	/**
	 * Description for 'comment'
	 * 
	 * @var unknown
	 */
	var $comment    = NULL;  // @var text


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
	var $created_by = NULL;  // @var int(11)


	/**
	 * Description for 'modified'
	 * 
	 * @var unknown
	 */
	var $modified   = NULL;  // @var datetime (0000-00-00 00:00:00)


	/**
	 * Description for 'modified_by'
	 * 
	 * @var unknown
	 */
	var $modified_by = NULL;  // @var int(11)


	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state      = NULL;  // @var int(2)


	/**
	 * Description for 'sticky'
	 * 
	 * @var unknown
	 */
	var $sticky     = NULL;  // @var int(2)


	/**
	 * Description for 'parent'
	 * 
	 * @var unknown
	 */
	var $parent     = NULL;  // @var int(11)


	/**
	 * Description for 'hits'
	 * 
	 * @var unknown
	 */
	var $hits       = NULL;  // @var int(11)


	/**
	 * Description for 'group'
	 * 
	 * @var unknown
	 */
	var $group      = NULL;  // @var int(11)


	/**
	 * Description for 'access'
	 * 
	 * @var unknown
	 */
	var $access     = NULL;  // @var tinyint(2)  0=public, 1=registered, 2=special, 3=protected, 4=private


	/**
	 * Description for 'anonymous'
	 * 
	 * @var unknown
	 */
	var $anonymous  = NULL;  // @var tinyint(2)

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
		parent::__construct( '#__xforum', 'id', $db );
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
		if (trim( $this->comment ) == '' or trim( $this->comment ) == JText::_('Enter your comments...')) {
			$this->setError( JText::_('Please provide a comment') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery( $filters=array() )
	{
		$query  = "FROM $this->_tbl AS c WHERE ";
		if (isset($filters['parent']) && $filters['parent'] != 0) {
			$query .= "c.parent=".$filters['parent']." OR c.id=".$filters['parent']." ORDER BY c.created ASC";
		} else {
			if (isset($filters['state'])) {
				$query .= "c.state=".$filters['state']." AND ";
			}
			if (isset($filters['sticky']) && $filters['sticky'] != 0) {
				$query .= "c.sticky=".$filters['sticky']." AND ";
			}
			if (isset($filters['group']) && $filters['group'] != 0) {
				$query .= "c.group=".$filters['group']." AND ";
			}
			if (!isset($filters['authorized']) || !$filters['authorized']) {
				$query .= "c.access=0 AND ";
			}
			if (isset($filters['search']) && $filters['search'] != '') {
				$query .= "(c.topic LIKE '%".$filters['search']."%' OR c.comment LIKE '%".$filters['search']."%') AND ";
			}
			$query .= "c.parent=0";
			if (isset($filters['limit']) && $filters['limit'] != 0) {
				if (isset($filters['sticky']) && $filters['sticky'] == false) {
					$query .= " ORDER BY lastpost DESC, c.created DESC";
				} else {
					$query .= " ORDER BY c.sticky DESC, lastpost DESC, c.created DESC";
				}
			}
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

		$query = "SELECT COUNT(*) ".$this->buildQuery( $filters );

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
	public function getRecords( $filters=array() )
	{
		$query = "SELECT c.*";
		if (!isset($filters['parent']) || $filters['parent'] == 0) {
			$query .= ", (SELECT COUNT(*) FROM $this->_tbl AS r WHERE r.parent=c.id) AS replies ";
			$query .= ", (SELECT d.created FROM $this->_tbl AS d WHERE d.parent=c.id ORDER BY created DESC LIMIT 1) AS lastpost ";
		}
		$query .= $this->buildQuery( $filters );

		if($filters['limit'] != 0) {
			$query .= ' LIMIT '.$filters['start'].','.$filters['limit'];
		}
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getLastPost'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $parent Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getLastPost( $parent=null )
	{
		if (!$parent) {
			$parent = $this->parent;
		}
		if (!$parent) {
			return null;
		}

		$query = "SELECT r.* FROM $this->_tbl AS r WHERE r.parent=$parent ORDER BY created DESC LIMIT 1";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'deleteReplies'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $parent Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteReplies( $parent=null )
	{
		if (!$parent) {
			$parent = $this->parent;
		}
		if (!$parent) {
			return null;
		}

		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE parent=$parent" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}
}

//----------------------------------------------------------
// XForum pagination class
//----------------------------------------------------------


/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class XForumPagination extends JObject
{

	/**
	 * Description for 'limitstart'
	 * 
	 * @var number
	 */
	var $limitstart = null;  // The record number to start dislpaying from


	/**
	 * Description for 'limit'
	 * 
	 * @var number
	 */
	var $limit = null;       // Number of rows to display per page


	/**
	 * Description for 'total'
	 * 
	 * @var number
	 */
	var $total = null;       // Total number of rows


	/**
	 * Description for '_viewall'
	 * 
	 * @var boolean
	 */
	var $_viewall = false;   // View all flag


	/**
	 * Description for 'forum'
	 * 
	 * @var mixed
	 */
	var $forum = null;       // The forum we're paging for

	// Constructor


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $total Parameter description (if any) ...
	 * @param      unknown $limitstart Parameter description (if any) ...
	 * @param      unknown $limit Parameter description (if any) ...
	 * @param      unknown $forum Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($total, $limitstart, $limit, $forum)
	{
		// Value/Type checking
		$this->total		= (int) $total;
		$this->limitstart	= (int) max($limitstart, 0);
		$this->limit		= (int) max($limit, 0);
		$this->forum		= (int) $forum;

		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}

		if (!$this->limit) {
			$this->limit = $total;
			$this->limitstart = 0;
		}

		if ($this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}

		// Set the total pages and current page values
		if ($this->limit > 0) {
			$this->set( 'pages.total', ceil($this->total / $this->limit));
			$this->set( 'pages.current', ceil(($this->limitstart + 1) / $this->limit));
		}

		// Set the pagination iteration loop values
		$displayedPages	= 10;
		$this->set( 'pages.start', (floor(($this->get('pages.current') -1) / $displayedPages)) * $displayedPages +1);
		if ($this->get('pages.start') + $displayedPages -1 < $this->get('pages.total')) {
			$this->set( 'pages.stop', $this->get('pages.start') + $displayedPages -1);
		} else {
			$this->set( 'pages.stop', $this->get('pages.total'));
		}

		// If we are viewing all records set the view all flag to true
		if ($this->limit == $total) {
			$this->_viewall = true;
		}
	}

	// Return the rationalised offset for a row with a given index.


	/**
	 * Short description for 'getRowOffset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $index Parameter description (if any) ...
	 * @return     number Return description (if any) ...
	 */
	public function getRowOffset($index)
	{
		return $index +1 + $this->limitstart;
	}

	// Return the pagination data object, only creating it if it doesn't already exist


	/**
	 * Short description for 'getData'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function getData()
	{
		static $data;
		if (!is_object($data)) {
			$data = $this->_buildDataObject();
		}
		return $data;
	}

	// Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x


	/**
	 * Short description for 'getPagesLinks'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function getPagesLinks()
	{
		//$lang =& JFactory::getLanguage();

		// Build the page navigation list
		$data = $this->_buildDataObject();

		$list = array();

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = $this->_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = $this->_item_inactive($page);
			}
		}

		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = $this->_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = $this->_item_inactive($data->end);
		}

		if ($this->total > $this->limit) {
			return $this->_list_render($list);
		} else {
			return '';
		}
	}

	/**
	 * Short description for '_list_render'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $list Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function _list_render($list)
	{
		// Initialize variables
		$lang =& JFactory::getLanguage();
		$html = null;

		// Reverse output rendering for right-to-left display
		if ($lang->isRTL()) {
			$list['pages'] = array_reverse( $list['pages'] );
			foreach ( $list['pages'] as $page )
			{
				$html .= $page['data'];
			}
			$html .= $list['end']['data'];
		} else {
			foreach ( $list['pages'] as $page )
			{
				$html .= $page['data'].' ';
			}
			$html .= $list['end']['data'];
		}
		return $html;
	}

	/**
	 * Short description for '_item_active'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed &$item Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function _item_active(&$item)
	{
		return '<a title="'.$item->text.'" href="'.$item->link.'" class="pagenav">'.$item->text.'</a>';
	}

	/**
	 * Short description for '_item_inactive'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed &$item Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function _item_inactive(&$item)
	{
		return '<span class="pagenav">'.$item->text.'</span>';
	}

	/**
	 * Create and return the pagination data object
	 *
	 * @access	public
	 * @return	object	Pagination data object
	 * @since	1.5
	 */
	public function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();

		// Set the next and end data objects
		$data->end	= new XForumPaginationObject(JText::_('End'));

		if ($this->get('pages.current') < $this->get('pages.total'))
		{
			$end  = ($this->get('pages.total') -1) * $this->limit;

			$data->end->base	= $end;
			$data->end->link	= JRoute::_('&topic='.$this->forum.'&limitstart='.$end);
		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.start'); $i <= $stop; $i ++)
		{
			$offset = ($i -1) * $this->limit;

			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route

			$data->pages[$i] = new XForumPaginationObject($i);
			//if ($i != $this->get('pages.current') || $this->_viewall)
			//{
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link	= JRoute::_('&topic='.$this->forum.'&limitstart='.$offset);
			//}
		}
		return $data;
	}
}

//----------------------------------------------------------
// XForum Pagination object representing a particular item in the pagination lists
//----------------------------------------------------------


/**
 * Short description for 'XForumPaginationObject'
 * 
 * Long description (if any) ...
 */
class XForumPaginationObject extends JObject
{

	/**
	 * Description for 'text'
	 * 
	 * @var unknown
	 */
	var $text;

	/**
	 * Description for 'base'
	 * 
	 * @var unknown
	 */
	var $base;

	/**
	 * Description for 'link'
	 * 
	 * @var unknown
	 */
	var $link;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      unknown $base Parameter description (if any) ...
	 * @param      unknown $link Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($text, $base=null, $link=null)
	{
		$this->text = $text;
		$this->base = $base;
		$this->link = $link;
	}
}
?>