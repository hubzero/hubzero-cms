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

ximport('Hubzero_Controller');

class KbController extends Hubzero_Controller
{	
	public function execute()
	{
		$this->_task = strtolower(JRequest::getVar('task', '', 'request'));
		
		switch ($this->_task) 
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
	
	//----------------------------------------------------------
	// Collection functions
	//----------------------------------------------------------

	protected function categories()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'categories') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$view->filters = array();
		$view->filters['id'] = JRequest::getInt( 'id', 0 );
		if (!$view->filters['id']) {
			$view->filters['id'] = JRequest::getInt( 'cid', 0 );
		}
		$view->filters['filterby'] = JRequest::getVar( 'filterby', 'm.id' );
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = JRequest::getInt('limitstart', 0);

		$c = new KbCategory( $this->database );
		
		// Get record count
		$view->total = $c->getCategoriesCount( $view->filters );

		// Get records
		$view->rows = $c->getCategoriesAll( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function articles()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'articles') );
		$view->option = $this->_option;

		// Incoming
		$view->filters = array();
		$view->filters['cid'] = JRequest::getInt( 'cid', 0 );
		$view->filters['id'] = JRequest::getInt( 'id', 0 );

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = JRequest::getInt('limitstart', 0);
	
		// Paging filter
		$view->filters['filterby'] = JRequest::getVar( 'filterby', 'm.id' );
		
		$a = new KbArticle( $this->database );
		
		// Get record count
		$view->total = $a->getArticlesCount( $view->filters );

		// Get records
		$view->rows = $a->getArticlesAll( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );
	
		// Get the sections
		$row = new KbCategory( $this->database );
		$view->sections = $row->getAllSections();
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function orphans()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'articles') );
		$view->option = $this->_option;
		
		// Incoming
		$view->filters = array();
		$view->filters['orphans'] = true;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = JRequest::getInt('limitstart', 0);
	
		// Paging filter
		$view->filters['filterby'] = JRequest::getVar( 'filterby', 'm.id' );
		
		$a = new KbArticle( $this->database );
		
		// Get record count
		$view->total = $a->getArticlesCount( $view->filters );
		
		// Get records
		$view->rows = $a->getArticlesAll( $view->filters );
	
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function editfaq() 
	{
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
		$row = new KbArticle( $this->database );
		$row->load( $id );
	
		// Fail if checked out not by 'me'
		if ($row->checked_out && $row->checked_out <> $this->juser->get('id')) {
			$this->_redirect = 'index.php?option='.$this->_option;
			$this->_message = JText::_('KB_CHECKED_OUT');
			return;
		}

		// Instantiate a new view
		$view = new JView( array('name'=>'article') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		if ($id) {
			// Editing existing
			$row->checkout( $this->juser->get('id') );

			// Get name of creator
			$query = "SELECT name from #__users WHERE id=".$row->created_by;
			$this->database->setQuery( $query );
			$row->created_by = $this->database->loadResult();

			// Get name of modifier
			$query = "SELECT name from #__users WHERE id=".$row->modified_by;
			$this->database->setQuery( $query );
			$row->modified_by = $this->database->loadResult();
		} else {
			// Creating new
			$row->title       = '';
			$row->introtext   = '';
			$row->fulltext    = '';
			$row->created     = date( 'Y-m-d H:i:s', time() );
			$row->created_by  = $this->juser->get('id');
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
		$view->row = $row;
		
		$c = new KbCategory( $this->database );
		
		// Get the sections
		$view->sections = $c->getAllSections();
		
		// Get the sections
		$view->categories = $c->getAllCategories();

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function editcategory()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'category') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		$view->cid = JRequest::getInt( 'cid', 0 );
	
		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
	
		// Load category
		$view->row = new KbCategory( $this->database );
		$view->row->load( $id );

		// Get the sections
		$view->sections = $row->getAllSections();
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	//  Processers
	//----------------------------------------------------------
	
	protected function savefaq() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$id  = JRequest::getInt( 'id', 0 );

		// Initiate extended database class
		$row = new KbArticle( $this->database );
		if (!$row->bind( $_POST )) {
			echo KbHtml::alert( $row->getError() );
			exit();
		}

		if ($row->id < 1) {
			// New entry
			$row->created    = $row->created ? $row->created : date( "Y-m-d H:i:s" );
			$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('id');
		} else {
			// Updating entry
			$row->modified    = date( "Y-m-d H:i:s" );
			$row->modified_by = $this->juser->get('id');
			$row->created     = $row->created ? $row->created : date( "Y-m-d H:i:s" );
			$row->created_by  = $row->created_by ? $row->created_by : $this->juser->get('id');
		}

		if (!$row->alias) {
			$normalized_valid_chars = 'a-zA-Z0-9_';
			$row->alias = str_replace(' ','_',$row->title);
			$row->alias = preg_replace("/[^$normalized_valid_chars]/", "", $row->alias);
			$row->alias = strtolower($row->alias);
		}

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
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$cid = JRequest::getInt( 'cid', 0 );

		// Initiate extended database class
		$row = new KbCategory( $this->database );
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
		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$ids = JRequest::getVar( 'id', array(0) );
		if (!is_array( $ids )) {
			$ids = array(0);
		}
	
		if (!empty($ids)) {
			// Create a category object
			$article = new KbArticle( $this->database );
			
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
				
				// Instantiate a new view
				$view = new JView( array('name'=>'category', 'layout'=>'delete') );
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->id = $id;
				
				// Set any errors
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}

				// Output the HTML
				$view->display();
			break;
			
			case 2:
				// Check for request forgeries
				JRequest::checkToken() or jexit( 'Invalid Token' );
				
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
				$article = new KbArticle( $this->database );
				
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
							$a = new KbArticle( $this->database );
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
				$category = new KbCategory( $this->database );
				
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
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Make sure we have an ID to work with
		if (!$id) {
			echo KbHtml::alert( JText::_('KB_NO_ID') );
			return;
		}

		// Load the article
		$row = new KbArticle( $this->database );
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
			echo KbHtml::alert( $row->getError() );
			return;
		}
		if (!$row->store()) {
			echo KbHtml::alert( $row->getError() );
			return;
		}

		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------

	protected function publish($w=0) 
	{
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
				$row = new KbArticle( $this->database );
			} else {
				// Updating a category
				$row = new KbCategory( $this->database );
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
		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$id  = JRequest::getInt( 'id', 0 );
		$sid = JRequest::getInt( 'section', 0 );
		$cat = JRequest::getInt( 'category', 0 );
	
		// Make sure we have an ID to work with
		if ($id) {
			// Bind the posted data to the article object and check it in
			$article = new KbArticle( $this->database );
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
		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$id  = JRequest::getInt( 'id', 0 );
	
		// Make sure we have an ID to work with
		if (!$id) {
			echo KbHtml::alert( JText::_('KB_NO_ID') ); 
			exit;
		}
		
		// Load and reset the article's hits
		$article = new KbArticle( $this->database );
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
		// Incoming
		$cid = JRequest::getInt( 'cid', 0 );
		$id  = JRequest::getInt( 'id', 0 );
		
		// Make sure we have an ID to work with
		if (!$id) {
			echo KbHtml::alert( JText::_('KB_NO_ID') ); 
			exit;
		}
	
		// Load and reset the article's ratings
		$article = new KbArticle( $this->database );
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
		$helpful = new KbHelpful( $this->database );
		$helpful->deleteHelpful( $id );

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_redirect .= ($cid) ? '&task=category&cid='.$cid : '';
	}
}
