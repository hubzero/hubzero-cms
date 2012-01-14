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
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class ForumPagination extends JObject
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
class ForumPaginationObject extends JObject
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