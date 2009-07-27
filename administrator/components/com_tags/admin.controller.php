<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

class TagsController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
		
	//-----------
	
	private function getTask()
	{
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		switch ($this->getTask()) 
		{
			case 'add':    $this->add();    break;
			case 'edit':   $this->edit();   break;
			case 'cancel': $this->cancel(); break;
			case 'save':   $this->save();   break;
			case 'remove': $this->remove(); break;
			case 'merge':  $this->merge();  break;
			case 'browse': $this->browse(); break;
			
			default: $this->browse(); break;
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}
	
	//----------------------------------------------------------
	// Tag functions
	//----------------------------------------------------------

	protected function browse()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Incoming
		$filters = array();
		$filters['limit']  = $app->getUserStateFromRequest($this->_option.'.browse.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start']  = $app->getUserStateFromRequest($this->_option.'.browse.limitstart', 'limitstart', 0, 'int');
		$filters['search'] = urldecode(trim($app->getUserStateFromRequest($this->_option.'.browse.search','search', '')));
		$filters['by']     = trim($app->getUserStateFromRequest($this->_option.'.browse.by', 'filterby', 'all'));
		
		$t = new TagsTag( $database );

		// Record count
		$total = $t->getCount( $filters );
		
		$filters['limit'] = ($filters['limit'] == 0) ? 'all' : $filters['limit'];
		
		// Get records
		$rows = $t->getRecords( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		TagsHTML::browse( $rows, $pageNav, $this->_option, 'tags', $filters );
	}

	//-----------
	
	protected function add() 
	{
		$this->edit();
	}

	//-----------

	protected function edit($tag=NULL)
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
	
		// Load a tag object if one doesn't already exist
		if (!$tag) {
			$tag = new TagsTag( $database );
			$tag->load( $id );
			
			if ($id) {
				$action = 'Edit';
			} else {
				$action = 'Add';
			}
		}

		TagsHTML::edit( $database, $tag, $this->_option, $this->getError() );
	}

	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------
	
	protected function save()
	{
		$database =& JFactory::getDBO();

		$row = new TagsTag( $database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}
		
		$row->admin = JRequest::getInt('admin', 0);
		$row->raw_tag = trim($row->raw_tag);
		
		$t = new Tags();
		$row->tag = $t->normalize_tag($row->raw_tag);

		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		// Make sure the tag doesn't already exist
		if (!$row->id) {
			if ($row->checkExistence()) {
				$this->setError( JText::_('TAG_EXIST') );
				$this->edit($row);
				return;
			}
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}
	
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'TAG_SAVED' );
	}

	//-----------

	protected function remove()
	{
		$database =& JFactory::getDBO();
		
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}
		
		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Get Tags plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();
		
		foreach ($ids as $id) 
		{
			// Remove references to the tag
			$dispatcher->trigger( 'onTagDelete', array($id) );
			
			// Remove the tag
			$tag = new TagsTag( $database );
			$tag->delete( $id );
		}
	
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'TAG_REMOVED' );
	}
	
	//-----------

	protected function merge()
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		$ids  = JRequest::getVar('id', array());
		$step = JRequest::getInt('step', 1);
		$step = ($step) ? $step : 1;
		
		if (!is_array($ids)) {
			$ids = array(0);
		}
		
		// Make sure we have some IDs to work with
		if ($step == 1 && (!$ids || count($ids) < 2)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Load the tag plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();
		
		$idstr = implode(',',$ids);
		
		switch ($step)
		{
			case 1:
				$tags = array();
				
				// Loop through the IDs of the tags we want to merge
				foreach ($ids as $id) 
				{
					// Load the tag's info
					$tag = new TagsTag( $database );
					$tag->load( $id );
					
					// Get the total number of items associated with this tag
					$totals = $dispatcher->trigger( 'onTagCount', array($id) );
					$total = 0;
					foreach ($totals as $t) 
					{
						$total = $total + $t;
					}
					$tag->total = $total;
					
					// Add the tag object to an array
					$tags[] = $tag;
				}
				
				// Get all tags
				$t = new TagsTag( $database );
				$rows = $t->getAllTags();
				
				TagsHtml::merge( $this->_option, $idstr, $rows, 2, $tags );
			break;
			
			case 2:
				// Get the string of tag IDs we plan to merge
				$ind = JRequest::getVar('ids', '', 'post');
				if ($ind) {
					$ids = explode(',',$ind);
				} else {
					$ids = array();
				}
				
				// Incoming
				$tag_exist = JRequest::getInt('existingtag', 0, 'post');
				$tag_new   = JRequest::getVar('newtag', '', 'post');
				
				// Are we merging tags into a totally new tag?
				if ($tag_new) {
					// Yes, we are
					$_POST['raw_tag'] = $tag_new;
					$_POST['alias'] = '';
					$_POST['description'] = '';
					
					$this->save(0);
					
					$tagging = new Tags( $database );
					$mtag = $tagging->get_raw_tag_id($tag_new);
				} else {
					// No, we're merging into an existing tag
					$mtag = $tag_exist;
				}
				
				foreach ($ids as $id)
				{
					if ($mtag != $id) {
						// Get all the associations to this tag
						// Loop through the associations and link them to a different tag
						$dispatcher->trigger( 'onTagMove', array($id, $mtag) );
				
						// Delete the tag
						$tag = new TagsTag( $database );
						$tag->delete( $id );
					}
				}
				
				$this->_redirect = 'index.php?option='.$this->_option;
				$this->_message = JText::_( 'TAGS_MERGED' );
			break;
		}
	}
}
?>