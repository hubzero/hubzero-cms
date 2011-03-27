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

ximport('Hubzero_Controller');

class WikiController extends Hubzero_Controller
{	
	public function execute()
	{
		$config = new WikiConfig( array('option'=>$this->_option) );
		$this->config = $config;
		
		define('WIKI_SUBPAGE_SEPARATOR',$config->subpage_separator);
		define('WIKI_MAX_PAGENAME_LENGTH',$config->max_pagename_length);
		
		$this->_task = strtolower(JRequest::getVar('task', 'pages'));

		switch ($this->_task) 
		{
			case 'cancel':           $this->cancel();         break;
			
			// Revisions
			case 'newrevision':      $this->editrevision();   break;
			case 'editrevision':     $this->editrevision();   break;
			case 'saverevision':     $this->saverevision();   break;
			case 'deleterevision':   $this->deleterevision(); break;
			case 'toggleapprove':    $this->toggleapprove();  break;
			
			//case 'publish':          $this->publish(1);       break;
			//case 'unpublish':        $this->publish(1);       break;
			case 'accesspublic':     $this->access();         break;
			case 'accessregistered': $this->access();         break;
			case 'accessspecial':    $this->access();         break;
			
			// Pages
			case 'newpage':          $this->editpage();       break;
			case 'editpage':         $this->editpage();       break;
			case 'savepage':         $this->savepage();       break;
			case 'deletepage':       $this->deletepage();     break;
			case 'resethits':        $this->resethits();      break;
			case 'togglestate':      $this->togglestate();    break;
			
			// Browsing
			case 'revisions':        $this->revisions();      break;
			case 'pages':            $this->pages();          break;

			default: $this->pages(); break;
		}
	}
	
	//----------------------------------------------------------
	// Collection functions
	//----------------------------------------------------------

	protected function pages()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'pages') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.pages.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = JRequest::getInt('limitstart', 0);
		$view->filters['sortby'] = JRequest::getVar( 'sortby', 'id' );
		$view->filters['search'] = JRequest::getVar( 'search', '' );

		$p = new WikiPage( $this->database );
		
		// Get record count
		$view->total = $p->getPagesCount( $view->filters );

		// Get records
		$view->rows = $p->getPages( $view->filters );

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

	protected function revisions()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'revisions') );
		$view->option = $this->_option;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$view->filters = array();
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.revisions.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = $app->getUserStateFromRequest($this->_option.'.revisions.limitstart', 'limitstart', 0, 'int');
		$view->filters['pageid'] = JRequest::getInt( 'pageid', 0 );
		$view->filters['sortby'] = JRequest::getVar( 'sortyby', 'version DESC, created DESC' );
		$view->filters['search'] = JRequest::getVar( 'search', '' );
		
		$view->page = new WikiPage( $this->database );
		if ($view->filters['pageid']) {
			$view->page->load( $view->filters['pageid'] );
		}
		
		$r = new WikiPageRevision( $this->database );
		
		// Get record count
		$view->total = $r->getRecordsCount( $view->filters );

		// Get records
		$view->rows = $r->getRecords( $view->filters );

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

	protected function editpage() 
	{
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
		
		// Load the article
		$row = new WikiPage( $this->database );
		$row->load( $id );

		// Instantiate a new view
		$view = new JView( array('name'=>'page') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		if (!$id) {
			// Creating new
			$row->created_by = $this->juser->get('id');
		}
		
		$creator =& JUser::getInstance($row->created_by);
		
		$view->creator = $creator;
		
		$view->row = $row;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------
	
	protected function editrevision() 
	{
		// Incoming
		$ids = JRequest::getVar( 'id', array(0) );
		if (is_array($ids) && !empty($ids)) {
			$id = $ids[0];
		}
		
		$pageid = JRequest::getInt( 'pageid', 0 );
		if (!$pageid) {
			echo WikiHtml::alert( JText::_('Missing page ID') );
			exit();
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'revision') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		$view->page = new WikiPage( $this->database );
		$view->page->load( $pageid );
		
		if (!$id) {
			// Creating new
			$view->revision = $view->page->getCurrentRevision();
			$view->revision->version++;
			$view->revision->created_by = $this->juser->get('id');
		} else {
			$view->revision = new WikiPageRevision( $this->database );
			$view->revision->load($id);
		}
		
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
	
	protected function savepage() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$page = JRequest::getVar( 'page', array(), 'post' );
		$page = array_map('trim', $page);

		// Initiate extended database class
		$row = new WikiPage( $this->database );
		if (!$row->bind( $page )) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}

		if (!$row->id) {
			$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('id');
		}

		if (!$row->pagename && $row->title) {
			$row->pagename = preg_replace("/[^\:a-zA-Z0-9]/", "", $row->title);
		}
		$row->pagename = preg_replace("/[^\:a-zA-Z0-9]/", "", $row->pagename);
		if (!$row->title && $row->pagename) {
			$row->title = $row->pagename;
		}
		$row->access = JRequest::getInt( 'access', 0, 'post' );
		
		// Get parameters
		$params = JRequest::getVar( 'params', '', 'post' );
		if (is_array( $params )) {
			$txt = array();
			foreach ( $params as $k=>$v) 
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode( "\n", $txt );
		}

		// Check content
		if (!$row->check()) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}
		
		// Store new content
		if (!$row->store()) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}

		// Log the change
		$log = new WikiLog( $this->database );
		$log->pid = $page->id;
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		if ($isNew) {
			$log->action = 'page_created';
		} else {
			$log->action = 'page_edited';
		}
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		$this->_message = JText::_('Page successfully saved');
	}

	//-----------

	protected function saverevision() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$revision = JRequest::getVar( 'revision', array() );
		$revision = array_map('trim',$revision);
		
		// Initiate extended database class
		$row = new WikiPageRevision( $this->database );
		if (!$row->bind( $revision )) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}
		
		$isNew = false;
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );
			$isNew = true;
		}
		
		$page = new WikiPage( $this->database );
		$page->load( $row->pageid );
		
		// Parse text
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $page->scope,
			'pagename' => $page->pagename,
			'pageid'   => $page->id,
			'filepath' => '',
			'domain'   => $this->_group 
		);
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		$row->pagehtml = $p->parse($row->pagetext, $wikiconfig);
		
		// Parse attachments
		/*$a = new WikiPageAttachment( $this->database );
		$a->pageid = $row->pageid;
		$a->path = $this->config->filepath;
		$row->pagehtml = $a->parse($row->pagehtml);*/

		// Check content
		if (!$row->check()) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}

		// Log the change
		$log = new WikiLog( $this->database );
		$log->pid = $page->id;
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		if ($isNew) {
			$log->action = 'revision_created';
		} else {
			$log->action = 'revision_edited';
		}
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}

		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=revisions&pageid='.$row->pageid;
		$this->_message = JText::_('Revision saved');
	}

	//-----------

	protected function deletepage() 
	{
		// Incoming
		$step = JRequest::getInt( 'step', 1 );
		$step = (!$step) ? 1 : $step;
		
		$ids = JRequest::getVar( 'id', array(0) );
		
		// What step are we on?
		switch ($step)
		{
			case 1:
				// Instantiate a new view
				$view = new JView( array('name'=>'page', 'layout'=>'delete') );
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->ids = $ids;
			
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
			
				// Check if they confirmed
				$confirmed = JRequest::getInt( 'confirm', 0 );
				if (!$confirmed) {
					echo WikiHtml::alert( JText::_('Please confirm removal') );
					exit();
				}
			
				if (!empty($ids)) {
					// Create a category object
					$page = new WikiPage( $this->database );

					foreach ($ids as $id)
					{
						// Delete the page's history, tags, comments, etc.
						$page->deleteBits( $id );

						// Finally, delete the page itself
						$page->delete( $id );

						// Delete the page's files
						jimport('joomla.filesystem.folder');
						if (!JFolder::delete($this->config->filepath .DS. $id)) {
							$this->setError( JText::_('COM_WIKI_UNABLE_TO_DELETE_FOLDER') );
						}

						// Log the action
						$log = new WikiLog( $this->database );
						$log->pid = $id;
						$log->uid = $this->juser->get('id');
						$log->timestamp = date( 'Y-m-d H:i:s', time() );
						$log->action = 'page_removed';
						$log->actorid = $this->juser->get('id');
						if (!$log->store()) {
							$this->setError( $log->getError() );
						}
					}
				}
				
				$this->_message = JText::_(count($ids).' page(s) successfully removed');
			break;
		}
		
		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------

	protected function deleterevision() 
	{
		// Incoming
		$step = JRequest::getInt( 'step', 1 );
		$step = (!$step) ? 1 : $step;
		
		$pageid = JRequest::getInt( 'pageid', 0 );
		$ids = JRequest::getVar( 'id', array(0) );
		
		// What step are we on?
		switch ($step)
		{
			case 1:
				// Instantiate a new view
				$view = new JView( array('name'=>'revisions', 'layout'=>'delete') );
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->ids = $ids;
				$view->pageid = $pageid;
				
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
				
				// Check if they confirmed
				$confirmed = JRequest::getInt( 'confirm', 0 );
				if (!$confirmed) {
					echo WikiHtml::alert( JText::_('Please confirm removal') );
					exit();
				}
				
				if (!empty($ids)) {
					// Create a category object
					$revision = new WikiPageRevision( $this->database );

					foreach ($ids as $id)
					{
						// Load the revision
						$revision->load( $id );

						// Get a count of all approved revisions
						$count = $revision->getRevisionCount();

						// Can't delete - it's the only approved version!
						if ($count <= 1) {
							echo WikiHtml::alert( JText::_('Can not remove only available revision') );
							return;
						}

						// Delete it
						$revision->delete( $id );

						// Log the action
						$log = new WikiLog( $this->database );
						$log->pid = $pageid;
						$log->uid = $this->juser->get('id');
						$log->timestamp = date( 'Y-m-d H:i:s', time() );
						$log->action = 'revision_removed';
						$log->actorid = $this->juser->get('id');
						if (!$log->store()) {
							$this->setError( $log->getError() );
						}
					}
					
					$this->_message = JText::_(count($ids).' revision(s) successfully removed');
				}

				// Set the redirect
				$this->_redirect = 'index.php?option='.$this->_option.'&task=revisions&pageid='.$pageid;
			break;
		}
	}

	//-----------

	protected function access() 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );

		// Make sure we have an ID to work with
		if (!$id) {
			echo WikiHtml::alert( JText::_('No ID') );
			return;
		}

		// Load the article
		$row = new WikiPage( $this->database );
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
			echo WikiHtml::alert( $row->getError() );
			return;
		}
		if (!$row->store()) {
			echo WikiHtml::alert( $row->getError() );
			return;
		}

		// Set the redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------

	protected function toggleapprove() 
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		// Incoming
		$pageid = JRequest::getInt( 'pageid', 0 );
		$id = JRequest::getInt( 'id', 0 );
		
		if ($id) {
			// Load the revision, approve it, and save
			$revision = new WikiPageRevision( $this->database );
			$revision->load( $id );
			$revision->approved = JRequest::getInt( 'approve', 0 );
			if (!$revision->check()) {
				echo WikiHtml::alert( $revision->getError() );
				exit();
			}
			if (!$revision->store()) {
				echo WikiHtml::alert( $revision->getError() );
				exit();
			}
			
			// Log the action
			$log = new WikiLog( $this->database );
			$log->pid = $pageid;
			$log->uid = $this->juser->get('id');
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'revision_approved';
			$log->actorid = $this->juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}
		
		$this->_redirect = 'index.php?option='.$this->_option.'&task=revisions&pageid='.$pageid;
	}
	
	//-----------

	protected function cancel()
	{
		// Incoming
		$pageid = JRequest::getInt( 'pageid', 0 );

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
		if ($pageid) {
			$this->_redirect .= '&task=revisions&pageid='.$pageid;
		}
	}

	//-----------

	protected function resethits()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
	
		// Make sure we have an ID to work with
		if (!$id) {
			echo WikiHtml::alert( JText::_('No ID') ); 
			exit;
		}
		
		// Load and reset the article's hits
		$page = new WikiPage( $this->database );
		$page->load( $id );
		$page->hits = 0;
		if (!$page->check()) {
			echo WikiHtml::alert( $page->getError() );
			exit();
		}
		if (!$page->store()) {
			echo WikiHtml::alert( $page->getError() );
			exit();
		}

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
	}
	
	//-----------

	protected function togglestate()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
	
		// Make sure we have an ID to work with
		if (!$id) {
			echo WikiHtml::alert( JText::_('No ID') ); 
			exit;
		}
		
		// Load and reset the article's hits
		$page = new WikiPage( $this->database );
		$page->load( $id );
		$page->state = JRequest::getInt( 'state', 0 );
		if (!$page->check()) {
			echo WikiHtml::alert( $page->getError() );
			exit();
		}
		if (!$page->store()) {
			echo WikiHtml::alert( $page->getError() );
			exit();
		}

		// Set the redirect
		$this->_redirect  = 'index.php?option='.$this->_option;
	}
}

