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

class KbController
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;

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
		$task = strtolower(JRequest::getVar('task', '','request'));
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		switch ($this->getTask()) 
		{
			// Articles
			case 'resethits':        $this->resethits();      break;
			case 'resethelpful':     $this->resethelpful();   break;
			case 'newfaq':           $this->editfaq();        break;
			case 'editfaq':          $this->editfaq();        break;
			case 'savefaq':          $this->savefaq();        break;
			case 'deletefaq':        $this->deletefaq();      break;
			case 'publish':          $this->publish(1);       break;
			case 'unpublish':        $this->publish(1);       break;
			case 'accesspublic':     $this->access();         break;
			case 'accessregistered': $this->access();         break;
			case 'accessspecial':    $this->access();         break;
			case 'cancel':           $this->cancel();         break;
			
			// Categories
			case 'newcat':           $this->editcategory();   break;
			case 'editcat':          $this->editcategory();   break;
			case 'savecat':          $this->savecategory();   break;
			case 'deletecat':        $this->deletecategory(); break;
			case 'publishc':         $this->publish();        break;
			case 'unpublishc':       $this->publish();        break;
			
			// Browsing
			case 'orphans':          $this->orphans();        break;
			case 'category':         $this->articles();       break;
			case 'articles':         $this->articles();       break;
			case 'categories':       $this->categories();     break;

			default: $this->categories(); break;
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
	// Collection functions
	//----------------------------------------------------------

	protected function categories()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		
		// Incoming
		$filters = array();
		$filters['id'] = JRequest::getInt( 'id', 0 );
		if (!$filters['id']) {
			$filters['id'] = JRequest::getInt( 'cid', 0 );
		}
		$filters['filterby'] = JRequest::getVar( 'filterby', 'm.id' );
		
		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = JRequest::getInt('limitstart', 0);

		$c = new KbCategory( $database );
		
		// Get record count
		$total = $c->getCategoriesCount( $filters );

		// Get records
		$rows = $c->getCategoriesAll( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Output HTML
		KbHtml::categories( $database, $rows, $pageNav, $this->_option, $filters['filterby'], $filters['id'], $this->_task );
	}

	//-----------

	protected function articles()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		// Incoming
		$filters = array();
		$filters['cid'] = JRequest::getInt( 'cid', 0 );
		$filters['id'] = JRequest::getInt( 'id', 0 );

		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = JRequest::getInt('limitstart', 0);
	
		// Paging filter
		$filters['filterby'] = JRequest::getVar( 'filterby', 'm.id' );
		
		$a = new KbArticle( $database );
		
		// Get record count
		$total = $a->getArticlesCount( $filters );

		// Get records
		$rows = $a->getArticlesAll( $filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
	
		// Get the sections
		$row = new KbCategory( $database );
		$sections = $row->getAllSections();
		
		if ($filters['cid']) {
			$out = KbHtml::sectionSelect( $sections, $filters['cid'], 'id' );
		} else {
			$out = KbHtml::sectionSelect( $sections, $filters['id'], 'id' );
		}

		// Output HTML
		KbHtml::articles( $database, $rows, $pageNav, $this->_option, $filters['filterby'], $out, $filters['id'], $this->_task, $filters['cid'] );
	}

	//-----------

	protected function orphans()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		
		// Incoming
		$filters = array();
		$filters['orphans'] = true;

		// Get configuration
		$config = JFactory::getConfig();
		
		// Get paging variables
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = JRequest::getInt('limitstart', 0);
	
		// Paging filter
		$filters['filterby'] = JRequest::getVar( 'filterby', 'm.id' );
		
		$a = new KbArticle( $database );
		
		// Get record count
		$total = $a->getArticlesCount( $filters );
		
		// Get records
		$rows = $a->getArticlesAll( $filters );
	
		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );
	
		$out = JText::_('NONE');

		// Output HTML
		KbHtml::articles( $database, $rows, $pageNav, $this->_option, $filters['filterby'], $out, -1, $this->_task );
	}

	//-----------

	protected function editfaq() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
        $created_by_id = 0;

		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );

		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
		
		if ($this->_task == 'newfaq') {
			$sect = (isset($id)) ? $id : 0;
			$id = 0;
		}
		
		// Load the article
		$row = new KbArticle( $database );
		$row->load( $id );
	
		// Fail if checked out not by 'me'
		if ($row->checked_out && $row->checked_out <> $juser->get('id')) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_('KB_CHECKED_OUT');
			return;
		}

		//$created_by_id = $row->created_by;
		
		if ($id) {
			// Editing existing
			$row->checkout( $juser->get('id') );

			// Get name of creator
			$query = "SELECT name from #__users WHERE id=".$row->created_by;
			$database->setQuery( $query );
			$row->created_by = $database->loadResult();

			// Get name of modifier
			$query = "SELECT name from #__users WHERE id=".$row->modified_by;
			$database->setQuery( $query );
			$row->modified_by = $database->loadResult();
		
			//$row->introtext = KbHtml::unpee($row->introtext);
			//$row->fulltext  = KbHtml::unpee($row->fulltext);
		} else {
			// Creating new
			$row->title       = '';
			$row->introtext   = '';
			$row->fulltext    = '';
			$row->created     = date( 'Y-m-d H:i:s', time() );
			$row->created_by  = $juser->get('id');
			$row->modified    = '0000-00-00 00:00:00';
			$row->modified_by = '';
			$row->state       = 1;
			$row->access      = 0;
			$row->hits        = 0;
			$row->version     = 1;
			$row->section     = $sect;
			$row->category    = 0;
			$row->helpful     = 0;
			$row->nothelpful  = 0;
			$row->alias       = '';
		}
	
		$c = new KbCategory( $database );
		
		// Get the sections
		$sections = $c->getAllSections();
		
		// Get the sections
		$categories = $c->getAllCategories();

		// Build the <select> list for sections
		$lists['sections'] = KbHtml::sectionSelect( $sections, $row->section, 'section' );
		
		// Build the <select> list for categories
		$lists['categories'] = KbHtml::sectionSelect( $categories, $row->category, 'category' );

		// Build list of users
		$lists['created_by'] = JHTML::_('list.users', 'created_by', $row->created_by, 0, '', 'name', 1);
		
		// Build the <select> list for the group access
		$lists['access'] = JHTML::_('list.accesslevel', $row);

		// Output HTML
		KbHtml::editFaqForm( $row, $lists, $params, $juser->get('id'), $this->_option );
	}

	//-----------

	protected function editcategory()
	{
		$database =& JFactory::getDBO();
        $juser =& JFactory::getUser();

		// Incoming
		$ids  = JRequest::getVar( 'id', array(0) );
		$task = JRequest::getVar( 'task', '' );
		$cid  = JRequest::getInt( 'cid', 0 );
	
		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
	
		// Load category
		$row = new KbCategory( $database );
		$row->load( $id );

		// Get the sections
		$sections = $row->getAllSections();
		
		// Build the <select> for sections
		$lists['categories'] = KbHtml::sectionSelect( $sections, $row->section, 'section' );
		
		// Build the html select list for the group access
		$lists['access'] = JHTML::_('list.accesslevel', $row);
		
		// Output HTML
		KbHtml::editCatForm( $row, $lists, $juser->get('id'), $this->_option, $cid );
	}

	//----------------------------------------------------------
	//  Processers
	//----------------------------------------------------------
	
	protected function savefaq() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$id  = JRequest::getInt( 'id', 0 );

		// Initiate extended database class
		$row = new KbArticle( $database );
		if (!$row->bind( $_POST )) {
			echo KbHtml::alert( $row->getError() );
			exit();
		}
		$isNew = false;
		if ($row->id < 1) {
			$isNew = true;
			
			// New entry
			$row->created    = $row->created ? $row->created : date( "Y-m-d H:i:s" );
			$row->created_by = $row->created_by ? $row->created_by : $juser->get('id');
		} else {
			// Updating entry
			$row->modified    = date( "Y-m-d H:i:s" );
			$row->modified_by = $juser->get('id');
			$row->created     = $row->created ? $row->created : date( "Y-m-d H:i:s" );
			$row->created_by  = $row->created_by ? $row->created_by : $juser->get('id');
		}

		if (!$row->alias) {
			$normalized_valid_chars = 'a-zA-Z0-9_';
			$row->alias = str_replace(' ','_',$row->title);
			$row->alias = preg_replace("/[^$normalized_valid_chars]/", "", $row->alias);
			$row->alias = strtolower($row->alias);
		}

		// Code cleaner for xhtml transitional compliance
		$row->introtext = trim($row->introtext);
		$row->fulltext  = trim($row->fulltext);
		//$row->introtext = ($row->introtext) ? KbHtml::autop($row->introtext) : '';
		//$row->fulltext  = KbHtml::autop($row->fulltext);
		$row->introtext = trim($row->introtext);
		$row->fulltext  = trim($row->fulltext);

		// Check content
		if (!$row->check()) {
			echo KbHtml::alert( $row->getError() );
			exit();
		}
		$row->version++;
		
		// Store new content
		if (!$row->store()) {
			echo KbHtml::alert( $row->getError() );
			exit();
		}

		$row->checkin();

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($cid) ? '&task=category&cid='.$cid : '';
		$this->_message = JText::_('KB_ARTICLE_SAVED');
	}

	//-----------

	protected function savecategory() 
	{
		$database =& JFactory::getDBO();

		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$cid = JRequest::getInt( 'cid', 0 );

		// Initiate extended database class
		$row = new KbCategory( $database );
		if (!$row->bind( $_POST )) {
			echo KbHtml::alert( $row->getError() );
			exit();
		}
		
		if (!$row->alias) {
			$normalized_valid_chars = 'a-zA-Z0-9_';
			$row->alias = str_replace(' ','_',$row->title);
			$row->alias = preg_replace("/[^$normalized_valid_chars]/", "", $row->alias);
			$row->alias = strtolower($row->alias);
		}

		// Trim up whitespace
		$row->description = trim($row->description);

		// Check content
		if (!$row->check()) {
			echo KbHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo KbHtml::alert( $row->getError() );
			exit();
		}

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($cid) ? '&task=categories&id='.$cid : '';
		$this->_message = JText::_('KB_CATEGORY_SAVED');
	}

	//-----------

	protected function deletefaq() 
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
	
		if (!empty($ids)) {
			// Create a category object
			$article = new KbArticle( $database );
			
			foreach ($ids as $id)
			{
				// Delete the SEF
				$article->deleteSef( $id );
	
				// Delete the category
				$article->delete( $id );
			}
		}
		
		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=';
		if ($cid == -1) {
			$this->_redirect .= 'orphans';
		} else {
			$this->_redirect .= 'category&id='.$cid;
		}
	}

	//-----------

	protected function deletecategory() 
	{
		$database =& JFactory::getDBO();

		// Incoming
		$step = JRequest::getInt( 'step', 1 );
		$step = (!$step) ? 1 : $step;
		
		// What step are we on?
		switch ($step)
		{
			case 1:
				// Incoming
				$ids = JRequest::getVar( 'id', array(0) );
				if (is_array($ids) && !empty($ids)) {
					$id = $ids[0];
				}
				
				// Output HTML
				KbHtml::deleteOptions( $id, $this->_option, 'deletecat' );
			break;
			
			case 2:
				// Incoming
				$id = JRequest::getInt( 'id', 0 );
				
				// Make sure we have an ID to work with
				if (!$id) {
					echo KbHtml::alert( JText::_('KB_NO_ID') );
					return;
				}
				
				// Check if we're deleting collection and all FAQs or just the collection page
				$action = JRequest::getVar( 'action', 'removefaqs' );
				
				// Create an article object
				$article = new KbArticle( $database );
				
				// Get all the articles in this collection
				$faqs = $article->getCollection( $id );

				if ($faqs) {
					// Loop through the articles
					foreach ($faqs as $faq)
					{
						if ($action == 'deletefaqs') {
							$article->delete( $faq->id );
						} else {
							// Load the article
							$a = new KbArticle( $database );
							$a->load( $faq->id );
							// Make some changes
							if ($faq->category == $id) {
								$a->category = 0;
							} else {
								$a->section = 0;
							}
							// Check and store the changes
							if (!$a->check()) {
								return $a->getError();
							}
							if (!$a->store()) {
								return $a->getError();
							}
						}
					}
				}
	
				// Create a category object
				$category = new KbCategory( $database );
				
				// Delete the SEF
				$category->deleteSef( $id );
	
				// Delete the category
				$category->delete( $id );

				// Set the redirect
				$this->_redirect = 'index.php?option='.$this->_option;
			break;
		}
	}

	//-----------

	protected function access() 
	{
		$database =& JFactory::getDBO();
	
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Make sure we have an ID to work with
		if (!$id) {
			echo KbHtml::alert( JText::_('KB_NO_ID') );
			return;
		}

		// Load the article
		$row = new KbArticle( $database );
		$row->load( $id );
		
		// Set the access
		switch ($this->_task) 
		{
			case 'accesspublic':     $row->access = 0; break;
			case 'accessregistered': $row->access = 1; break;
			case 'accessspecial':    $row->access = 2; break;
		}
		
		// Check and store the changes
		if (!$row->check()) {
			echo $row->getError();
			return;
		}
		if (!$row->store()) {
			echo $row->getError();
			return;
		}

		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------

	protected function publish($w=0) 
	{
		$database =& JFactory::getDBO();

		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}

		$publish = ($this->_task == 'publish' || $this->_task == 'publishc') ? 1 : 0;

		// Check for an ID
		if (count( $ids ) < 1) {
			if ($publish == 1) {
				$action = JText::_('KB_SELECT_PUBLISH');
			} else {
				$action = JText::_('KB_SELECT_UNPUBLISH');
			}
			echo KbHtml::alert( $action );
			exit;
		}

		// Get a total (used for the redirect message)
		$total = count( $ids );

		// Update record(s)
		foreach ($ids as $id) 
		{
			if ($w) {
				// Updating an article
				$row = new KbArticle( $database );
			} else {
				// Updating a category
				$row = new KbCategory( $database );
			}
			
			$row->load( $id );
			$row->state = $publish;
			$row->store();
		}

		// Set message
		if ($publish == '-1') {
			$this->_message = JText::sprintf( 'KB_ARCHIVED', $total );
		} elseif ($publish == "1") {
			$this->_message = JText::sprintf( 'KB_PUBLISHED', $total );
		} elseif ($publish == "0") {
			$this->_message = JText::sprintf( 'KB_UNPUBLISHED', $total );
		}

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($cid) ? '&task=category&id='.$cid : '';
	}
	
	//-----------

	protected function cancel()
	{
		$database =& JFactory::getDBO();

		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$id  = JRequest::getInt( 'id', 0 );
		$sid = JRequest::getInt( 'section', 0 );
		$cat = JRequest::getInt( 'category', 0 );
	
		// Make sure we have an ID to work with
		if ($id) {
			// Bind the posted data to the article object and check it in
			$article = new KbArticle( $database );
			$article->bind( $_POST );
			$article->checkin();
		}

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		if ($cat) {
			$this->_redirect .= ($cat) ? '&task=category&id='.$cat : '';
			$this->_redirect .= ($sid) ? '&cid='.$sid : '';
		} else {
			$this->_redirect .= ($sid) ? '&task=category&id='.$sid : '';
		}
		$this->_redirect .= ($cid) ? '&task=categories&id='.$cid : '';
	}

	//-----------

	protected function resethits()
	{
		$database =& JFactory::getDBO();

		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$id  = JRequest::getInt( 'id', 0 );
	
		// Make sure we have an ID to work with
		if (!$id) {
			echo KbHtml::alert( JText::_('KB_NO_ID') );
			return;
		}
		
		// Load and reset the article's hits
		$article = new KbArticle( $database );
		$article->load( $id );
		$article->hits = 0;
		if (!$article->check()) {
			return $article->getError();
		}
		if (!$article->store()) {
			return $article->getError();
		}
		$article->checkin();

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($cid) ? '&task=category&cid='.$cid : '';
	}

	//-----------

	protected function resethelpful()
	{
		$database =& JFactory::getDBO();

		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$id  = JRequest::getInt( 'id', 0 );
		
		// Make sure we have an ID to work with
		if (!$id) {
			echo KbHtml::alert( JText::_('KB_NO_ID') );
			return;
		}
	
		// Load and reset the article's ratings
		$article = new KbArticle( $database );
		$article->load( $id );
		$article->helpful = 0;
		$article->nothelpful = 0;
		if (!$article->check()) {
			return $article->getError();
		}
		if (!$article->store()) {
			return $article->getError();
		}
		$article->checkin();

		// Delete all the entries associated with this article
		$helpful = new KbHelpful( $database );
		$helpful->deleteHelpful( $id );

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($cid) ? '&task=category&cid='.$cid : '';
	}
}
?>