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

class WikiController extends JObject
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
		
		$this->_sub = false;
		if (isset($config['sub'])) {
			$this->_sub = $config['sub'];
		}
		$this->_group = false;
		if (isset($config['group'])) {
			$this->_group = $config['group'];
		}
		$this->_access = false;
		if (isset($config['access'])) {
			$this->_access = $config['access'];
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
		$this->_task = ($task) ? $task : 'view';
		return $task;
	}
	
	//-----------
	
	public function execute()
	{	
		$database =& JFactory::getDBO();
		$database->setQuery("SHOW TABLES");
		$tables = $database->loadResultArray();

		if ($tables && array_search($database->_table_prefix.'wiki_page', $tables)===false) {
			echo WikiHtml::error( JText::_('WIKI_ERROR_MISSING_TABLES') );
			return;
		}
		
		$config = new WikiConfig( array('option'=>$this->_option) );
		$this->config = $config;
		
		define('WIKI_SUBPAGE_SEPARATOR',$config->subpage_separator);
		define('WIKI_MAX_PAGENAME_LENGTH',$config->max_pagename_length);
		
		$wp = new WikiPage( $database );
		$c = $wp->count();
		if (!$c) {
			$result = WikiSetup::initialize( $this->_option );
			if ($result) {
				$this->setError( $result );
			}
		}
		
		switch ($this->getTask()) 
		{
			case 'media':          $this->media();          break;
			case 'list':           $this->list_files();     break;
			case 'upload':         $this->upload();         break;
			case 'deletefolder':   $this->delete_folder();  break;
			case 'deletefile':     $this->delete_file();    break;

			case 'new':            $this->edit();           break;
			case 'edit':           $this->edit();           break;
			case 'save':           $this->save();           break;
			case 'cancel':         $this->cancel();         break;
			case 'delete':         $this->delete();         break;
			case 'deleterevision': $this->deleterevision(); break;
			case 'approve':        $this->approve();        break;
			case 'renamepage':     $this->renamepage();     break;
			case 'saverename':     $this->saverename();     break;
			
			case 'compare':        $this->compare();        break;
			case 'view':           $this->view();           break;
			case 'history':        $this->history();        break;
			
			case 'comments':       $this->comments();       break;
			case 'editcomment':    $this->editcomment();    break;
			case 'addcomment':     $this->editcomment();    break;
			case 'savecomment':    $this->savecomment();    break;
			case 'removecomment':  $this->removecomment();  break;
			case 'reportcomment':  $this->reportcomment();  break;
			
			default: $this->view(); break;
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
	
	//-----------
	
	private function getStyles($option='') 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function getScripts($name='', $path='')
	{
		$document =& JFactory::getDocument();
		if (!$name) {
			$name = $this->_name;
		}
		if (!$path) {
			$path = DS.'components'.DS.$this->_option.DS;
		}
		if (is_file(JPATH_ROOT.$path.$name.'.js')) {
			$document->addScript($path.$name.'.js');
		}
	}

	//----------------------------------------------------------
	// Page/Source Views
	//----------------------------------------------------------

	protected function view() 
	{
		// Load the page
		$this->getPage();

		// Include any CSS
		$this->getStyles();
		
		// Include any Scripts
		$this->getScripts();
	
		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$pagetitle = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
		$pagetitle = stripslashes($pagetitle);
		
		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$pagetitle);
	
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}

		// Does a page exist for the given pagename?
		if (!$this->page->exist()) {
			// No! Ask if they want to create a new page
			echo WikiHtml::doesNotExist( $pagetitle, $this->pagename, $this->scope, $this->_option );
		} else {
			if ($this->page->scope && !$this->page->group) {
				$bits = explode('/',$this->page->scope);
				$database =& JFactory::getDBO();
				$s = array();
				foreach ($bits as $bit) 
				{
					$bit = trim($bit);
					if ($bit != '/' && $bit != '') {
						$p = new WikiPage( $database );
						$p->load( $bit, implode('/',$s) );
						if ($p->id) {
							$pathway->addItem($p->title,'index.php?option='.$this->_option.a.'scope='.$p->scope.a.'pagename='.$p->pagename);
						}
						$s[] = $bit;
					}
				}
			}
			$pathway->addItem($pagetitle,'index.php?option='.$this->_option.a.'scope='.$this->scope.a.'pagename='.$this->pagename);
			
			// Yes!
			// Retrieve a specific version if given
			$version = JRequest::getInt( 'version', 0 );
			if ($version && $version > 0) {
				$revision = $this->page->getRevision($version);
				if (!$revision) {
					echo WikiHtml::NoSuchRevision($this->page, $version);
					return;
				}
			} else {
				$revision = $this->page->getCurrentRevision();
			}
			
			// Get the page's tags
			$tags = $this->page->getTags();
			
			// Up the hit counter
			$this->page->hit();

			// Parse the HTML
			$p = new WikiParser( $pagetitle, $this->_option, $this->scope, $this->pagename, $this->page->id, '', $this->_group );
			$revision->pagehtml = $p->parse( $revision->pagetext );
			
			// Did we get passed a search string?
			$q = urldecode(trim(JRequest::getVar( 'q', '' )));
			$q = stripslashes($q);
			//if ($q) {
				// Yes, highlight any occurrences of the string
				//$revision->pagehtml = eregi_replace( $p->glyphs($q), "<span class=\"highlight\">\\0</span>", $revision->pagehtml);
			//}
			
			// Create a linked Table of Contents
			$output = $p->formatHeadings( $revision->pagehtml );
			
			// Get the authors
			//$database =& JFactory::getDBO();
			//$wa = new WikiAuthor( $database );
			//$authors = $wa->getAuthors( $this->page->id );
			//$authors = $this->page->getAuthors();
			
			// Get non-author contributors
			$contributors = $revision->getContributors();

			// Check authorization
			$authorized = $this->checkAuthorization( 'read' );

			// Check if the page is group restricted and the user is authorized
			if ($this->page->group != '' && $this->page->access != 0 && !$authorized) {
				echo WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH') );
				return;
			}

			// Output errors
			if ($this->getError()) {
				echo WikiHtml::error( $this->getError() );
			}
			// Output other messages
			if ($this->_message) {
				echo WikiHtml::passed( $this->_message );
			}
			// Output content
			echo WikiHtml::view( $this->_sub, JText::_(strtoupper($this->_name)), $pagetitle, $this->page, $revision, $this->_option, $tags, $output, $this->_task, $authorized, $contributors, $q );
		}
	}

	//-----------

	private function login()
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		
		// Output HTML
		echo WikiHtml::div( WikiHtml::hed( 2,ucwords($this->_name) ), 'full', 'content-header');
		echo '<div class="main section">'.n;
		ximport('xmodule');
		XModuleHelper::displayModules('force_mod');
		echo '</div><!-- / .main section -->'.n;
	}
	
	//-----------

	protected function edit()
	{
	    $juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Check if they are logged in
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Load the page
		$this->getPage();
		
		// Get the most recent version for editing
		$revision = $this->page->getCurrentRevision();
		$revision->created_by = $juser->get('id');

		// If we have a specific section, extract it from the full text
		/*$section = JRequest::getInt( 'section', 0 );
		if ($section) {
			$p = new WikiParser( $this->pagename );
			$s = $p->getSection( $revision->pagetext, $section, '' );
		}*/
		
		// If an existing page, pull its tags for editing
		$tagstring = '';
		if ($this->page->id) {
			// Get the tags on this page
			$tags = $this->page->getTags();
			
			if (count($tags) > 0) {
				$tagarray = array();
				foreach ($tags as $tag)
				{
					$tagarray[] = $tag['raw_tag'];
				}
				$tagstring = implode( ', ', $tagarray );
			}
			
			// Get the list of authors and find out their usernames
			//$wa = new WikiAuthor( $database );
			/*$auths = $wa->getAuthors( $this->page->id );
			if (count($auths) > 0) {
				$autharray = array();
				foreach ($auths as $auth)
				{
					$zuser =& XUser::getInstance( trim($auth) );
					if (is_object($zuser)) {
						$autharray[] = $zuser->get('username');
					}
				}
				$authors = implode( ', ', $autharray );
			}*/
			$authors = $this->page->authors;
		} else {
			$this->page->created_by = $juser->get('id');
			$this->page->scope = $this->scope;
			if ($this->_group != '') {
				$this->page->group = $this->_group;
				$this->page->access = 1;
			}
			$this->page->title = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
			$authors = '';
		}

		// Include any CSS
		$this->getStyles();
		
		$b = dirname( __FILE__ );
		$path = substr($b, (strlen(JPATH_BASE) + 1), strlen($b));
		
		$jdocument =& JFactory::getDocument();
		$jdocument->addStyleSheet(DS.$path.DS.'wiki.css');
		
		$this->getScripts('wiki',DS.$path.DS);
		
		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$pagetitle = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
		$pagetitle = ($pagetitle) ? $pagetitle : JText::_('NEW').' '.JText::_(strtoupper($this->_name));
		
		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$pagetitle.': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename.a.'task='.$this->_task);
		
		// Check authorization
		$authorized = $this->checkAuthorization();
		
		// Check if the page is locked and the user is authorized
		/*if ($this->page->state == 1 && $authorized !== 'admin') {
			echo WikiHtml::div(WikiHtml::hed( 2, $pagetitle ), 'full', 'content-header');
			echo WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH') );
			return;
		}*/

		// Check if the page is group restricted and the user is authorized
		if ($this->page->group != '' && $this->page->access != 0 && !$authorized) {
			echo WikiHtml::div(WikiHtml::hed( 2, $pagetitle ), 'full', 'content-header');
			echo WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH') );
			return;
		}
		
		$revision->summary = '';

		// Are we previewing?
		if (is_object($this->preview)) {
			// Yes - get the preview so we can parse it and display
			$preview = $this->preview;
			
			// Parse the HTML
			$p = new WikiParser( $pagetitle, $this->_option, $this->scope, $this->pagename, $this->page->id, '', $this->_group );
			$preview->pagehtml = $p->parse( $preview->pagetext );
			
			$revision->pagetext   = $preview->pagetext;
			$revision->summary    = $preview->summary;
			$revision->minor_edit = $preview->minor_edit;
			
			// Get title
			$this->page->title = trim(JRequest::getVar( 'title', '', 'post' ));

			// Get parameters
			$params = JRequest::getVar( 'params', '', 'post' );
			if (is_array($params)) {
				$txt = array();
				foreach ($params as $k=>$v) 
				{
					$txt[] = "$k=$v";
				}
				$this->page->params = implode( "\n", $txt );
			}
			
			$tagstring = trim(JRequest::getVar( 'tags', '' ));
			$authors = trim(JRequest::getVar( 'authors', '' ));
		} else {
			$preview = NULL;
		}

		// Process the page's params
		$params =& new JParameter( $this->page->params, 'components'.DS.$this->_option.DS.$this->_name.'.xml', 'component' );
		
		// Output content
		echo WikiHtml::edit( $this->_sub, JText::_(strtoupper($this->_name)), $authorized, $pagetitle, $this->page, $revision, $authors, $this->_option, $tagstring, $this->_task, $params, $preview );
	}
	
	//-----------
	
	protected function save()
	{
		$database =& JFactory::getDBO();
		$config =& $this->config;
	
		$pageid = JRequest::getInt( 'pageid', 0, 'post' );
		
		// Was the preview button pushed?
		$preview = trim(JRequest::getVar( 'preview', '' ));
		if ($preview) {
			// Create a new revision
			$revision = new WikiPageRevision( $database );
			$revision->pageid     = $pageid;
			$revision->created    = date( 'Y-m-d H:i:s', time() );
			$revision->created_by = JRequest::getInt( 'created_by', 0, 'post' );
			$revision->version    = JRequest::getInt( 'version', 0, 'post' );
			//$revision->pagetext   = stripslashes(trim(JRequest::getVar( 'pagetext', '', 'post' )));
			$revision->pagetext   = stripslashes(rtrim($_POST['pagetext']));
			$revision->summary    = trim(JRequest::getVar( 'summary', '', 'post' ));
			$revision->minor_edit = JRequest::getInt( 'minor_edit', 0, 'post' );
			
			// Save it as a preview object
			$this->preview = $revision;
			
			// Set the component task
			if (!$pageid) {
				$this->_task = 'new';
			} else {
				$this->_task = 'edit';
			}
			
			// Push on through to the edit form
			$this->edit();
			return;
		}

		// Initiate extended database class
		$page = new WikiPage( $database );
		if (!$pageid) {
			// New page - save it to the database
			$page->pagename = trim(JRequest::getVar( 'pagename', '', 'post' ));
			$page->scope = trim(JRequest::getVar( 'scope', '' ));
			if (!$page->pagename) {
				// No pagename so let's generate one from the title
				$page->pagename = trim(JRequest::getVar( 'title', '', 'post' ));
				if (!$page->pagename) {
					echo WikiHtml::alert( JText::_('WIKI_ERROR_NO_PAGE_TITLE') );
					exit();
				}
				$page->pagename = $this->normalize($page->pagename);
				// Check that no other pages are using the new title
				$g = new WikiPage( $database );
				$g->load( $page->pagename, $page->scope );
				if ($g->exist()) {
					echo WikiHtml::alert( JText::_('WIKI_ERROR_PAGE_EXIST') );
					exit();
				}
			}
			$page->hits = 0;
			$page->created_by = JRequest::getInt( 'created_by', 0, 'post' );
			$page->rating = '0.0';
			$page->times_rated = 0;
			$page->access = JRequest::getInt( 'access', 0, 'post' );
			$page->group = trim(JRequest::getVar( 'group', '', 'post' ));
			
			$old = new WikiPageRevision( $database );
		} else {
			// Existing page - load it up
			$page->load( $pageid );

			// Get the revision before changes
			$old = $page->getCurrentRevision();
		}

		// Get title
		$page->title = trim(JRequest::getVar( 'title', '', 'post' ));

		if ($this->checkAuthorization()) {
			// Get allowed authors
			$page->authors = trim(JRequest::getVar( 'authors', '' ));
			
			// Get parameters
			$params = JRequest::getVar( 'params', '', 'post' );
			if (is_array( $params )) {
				$txt = array();
				foreach ( $params as $k=>$v) 
				{
					$txt[] = "$k=$v";
				}
				$page->params = implode( "\n", $txt );
			}
			
			// Get group access controls
			$page->access = JRequest::getInt( 'access', 0, 'post' );
			$page->group = trim(JRequest::getVar( 'group', '', 'post' ));
		}

		// Check content
		if (!$page->check()) {
			echo WikiHtml::alert( $page->getError() );
			exit();
		}

		// Store new content
		if (!$page->store()) {
			echo WikiHtml::alert( $page->getError() );
			exit();
		}
		// Make sure we have a page ID, we'll need it
		if (!$page->id) {
			$page->getID();
		}
		
		$this->page = $page;
		
		// Rename the temporary upload directory if it exist
		$lid = JRequest::getInt( 'lid', 0, 'post' );
		if ($lid != $page->id) {
			if (is_dir(JPATH_ROOT.$config->filepath.DS.$lid)) {
				jimport('joomla.filesystem.folder');
				if (!JFolder::move(JPATH_ROOT.$config->filepath.DS.$lid,JPATH_ROOT.$config->filepath.DS.$page->id)) {
					$this->setError( JFolder::move(JPATH_ROOT.$config->filepath.DS.$lid,JPATH_ROOT.$config->filepath.DS.$page->id) );
					//return;
				}
				//rename(JPATH_ROOT.$config->filepath.DS.$lid,JPATH_ROOT.$config->filepath.DS.$page->id);
				$wpa = new WikiPageAttachment( $database );
				$wpa->setPageID( $lid, $page->id );
			}
		}
		
		// Create a new revision
		$revision = new WikiPageRevision( $database );
		$revision->pageid     = $page->id;
		$revision->created    = date( 'Y-m-d H:i:s', time() );
		$revision->created_by = JRequest::getInt( 'created_by', 0, 'post' );
		$revision->version    = JRequest::getInt( 'version', 0, 'post' );
		$revision->version++;
		//$revision->pagetext   = trim(JRequest::getVar( 'pagetext', '', 'post' ));
		$revision->pagetext   = rtrim($_POST['pagetext']);
		$revision->approved   = ($this->checkAuthorization()) ? 1 : 0;
		$revision->summary    = trim(JRequest::getVar( 'summary', '', 'post' ));
		$revision->minor_edit = JRequest::getInt( 'minor_edit', 0, 'post' );
		
		// Stripslashes just to make sure
		$old->pagetext = rtrim(stripslashes($old->pagetext));
		$revision->pagetext = rtrim(stripslashes($revision->pagetext));
		
		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if ($old->pagetext != $revision->pagetext) {
			// Transform the wikitext to HTML
			$p = new WikiParser( $page->pagename, $this->_option, $page->scope, $page->pagename );
			$revision->pagehtml = $p->parse( $revision->pagetext );
			
			// Parse attachments
			$a = new WikiPageAttachment( $database );
			$a->pageid = $pageid;
			$a->path = $config->filepath;
			$revision->pagehtml = $a->parse($revision->pagehtml);
			
			// Check content
			if (!$revision->check()) {
				echo WikiHtml::alert( $revision->getError() );
				exit();
			}
			// Store content
			if (!$revision->store()) {
				echo WikiHtml::alert( $revision->getError() );
				exit();
			}
		}
		
		// Process tags
		$tags = trim(JRequest::getVar( 'tags', '' ));
		$tagging = new WikiTags( $database );
		$tagging->tag_object($revision->created_by, $page->id, $tags, 1, 1);

		// Log the action
		$juser =& JFactory::getUser();
		$log = new WikiLog( $database );
		$log->pid = $page->id;
		$log->uid = $juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		if ($revision->version == 1) {
			$log->action = 'page_created';
		} else {
			$log->action = 'page_edited';
		}
		$log->actorid = $juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}

		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'scope='.$page->scope.a.'pagename='.$page->pagename);
	}
	
	//-----------
	
	protected function delete()
	{
		$juser =& JFactory::getUser();
		
		// Check if they are logged in
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Load the page
		$this->getPage();
		
		//if ($this->pagename) {
		if (is_object($this->page)) {
			$database =& JFactory::getDBO();
			
			$authorized = $this->checkAuthorization();
			
			// Make sure they're authorized to delete
			if ($authorized) {
				$confirmed = JRequest::getInt( 'confirm', 0, 'post' );
				
				switch ($confirmed) 
				{
					case 1:
						$database =& JFactory::getDBO();
						$page = new WikiPage( $database );

						// Delete the page's history, tags, comments, etc.
						$page->deleteBits( $this->page->id );

						// Finally, delete the page itself
						$page->delete( $this->page->id );

						// Delete the page's files
						ximport('fileuploadutils');
						FileUploadUtils::delete_dir( $this->config->filepath .DS. $this->page->id );
						
						// Log the action
						$log = new WikiLog( $database );
						$log->pid = $this->page->id;
						$log->uid = $juser->get('id');
						$log->timestamp = date( 'Y-m-d H:i:s', time() );
						$log->action = 'page_removed';
						$log->actorid = $juser->get('id');
						if (!$log->store()) {
							$this->setError( $log->getError() );
						}
					break;
					
					default:
						// Include any CSS
						$this->getStyles();

						// Prep the pagename for display 
						// e.g. "MainPage" becomes "Main Page"
						$pagetitle = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);

						// Set the page's <title> tag
						$document =& JFactory::getDocument();
						$document->setTitle( JText::_(strtoupper($this->_name)).': '.$pagetitle.': '.JText::_(strtoupper($this->_task)) );
						
						// Set the pathway
						$app =& JFactory::getApplication();
						$pathway =& $app->getPathway();
						if (count($pathway->getPathWay()) <= 0) {
							$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
						}
						$pathway->addItem($pagetitle,'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename);
						$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename.a.'task='.$this->_task);
						
						// Output content
						echo WikiHtml::delete( $this->_sub, JText::_(strtoupper($this->_name)), $authorized, $pagetitle, $this->page, $this->_option, '' );
						return;
					break;
				}
			} else {
				$this->setError( JText::_('WIKI_ERROR_NOTAUTH') );
				$this->view();
				return;
			}
		} else {
			$this->setError( JText::_('WIKI_ERROR_PAGE_NOT_FOUND') );
		}
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'scope='.$this->scope);
	}
	
	//-----------
	
	protected function renamepage() 
	{
	    $juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Check if they are logged in
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Load the page
		$this->getPage();
		
		// Include any CSS
		$this->getStyles();
		
		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$pagetitle = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
		
		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$pagetitle.': '.JText::_('RENAME') );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper('RENAME')),'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename.a.'task='.$this->_task);
		
		// Check authorization
		$authorized = $this->checkAuthorization();
		
		if (!$authorized) {
			echo WikiHtml::error( JText::_('WIKI_ERROR_NOTAUTH') );
			return;
		}
		
		// Output content
		echo WikiHtml::renamepage( $this->_sub, JText::_(strtoupper($this->_name)), $authorized, $pagetitle, $this->page, $this->_option, 'edit' );
	}
	
	//-----------
	
	protected function saverename()
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

		// Incoming
		$oldpagename = trim(JRequest::getVar( 'oldpagename', '', 'post' ));
		$newpagename = trim(JRequest::getVar( 'newpagename', '', 'post' ));
		$scope = trim(JRequest::getVar( 'scope', '', 'post' ));
		
		// Remove any bad characters
		$newpagename = $this->normalize($newpagename);

		// Make sure they actually made changes
		if ($oldpagename == $newpagename) {
			echo WikiHtml::alert( JText::_('WIKI_ERROR_PAGENAME_UNCHANGED') );
			exit();
		}
		
		// Check that no other pages are using the new title
		$p = new WikiPage( $database );
		$p->load( $newpagename, $scope );
		if ($p->exist()) {
			echo WikiHtml::alert( JText::_('WIKI_ERROR_PAGE_EXIST').' '.JText::_('CHOOSE_ANOTHER_PAGENAME') );
			exit();
		} else {
			// Load the page, reset the name, and save
			$page = new WikiPage( $database );
			$page->load( $oldpagename, $scope );
			$page->pagename = $newpagename;
			$page->_tbl_key = 'id';

			if (!$page->check()) {
				echo WikiHtml::alert( $page->getError() );
				exit();
			}
			if (!$page->store()) {
				echo WikiHtml::alert( $page->getError() );
				exit();
			}
			
			// Log the action
			$log = new WikiLog( $database );
			$log->pid = $page->id;
			$log->uid = $juser->get('id');
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'page_renamed';
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'scope='.$page->scope.a.'pagename='.$page->pagename);
	}
	
	//----------------------------------------------------------
	// History Views
	//----------------------------------------------------------
	
	protected function history()
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$this->getPage();
		
		// Get all revisions
		$rev = new WikiPageRevision( $database );
		$revisions = $rev->getRevisions( $this->page->id );
		
		// Include any CSS
		$this->getStyles();
		
		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$pagetitle = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
		
		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$pagetitle.': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename.a.'task='.$this->_task);
		
		// Check authorization
		$authorized = $this->checkAuthorization();
		
		// Output errors
		if ($this->getError()) {
			echo WikiHtml::error( $this->getError() );
		}
		// Output other messages
		if ($this->_message) {
			echo WikiHtml::passed( $this->_message );
		}
		// Output content
		echo WikiHtml::history( $this->_sub, JText::_(strtoupper($this->_name)), $pagetitle, $this->page, $revisions, $this->_option, $this->_task, $authorized );
	}

	//-----------

	protected function compare() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$this->getPage();
		
		// Incoming
		$oldid = JRequest::getInt( 'oldid', 0 );
		$diff = JRequest::getInt( 'diff', 0 );
		
		// Do some error checking
		if (!$diff) {
			echo WikiHtml::error( JText::_('WIKI_ERROR_MISSING_VERSION') );
			return;
		}
		if ($diff == $oldid) {
			echo WikiHtml::error( JText::_('WIKI_ERROR_SAME_VERSIONS') );
			return;
		}
		
		// If no initial page is given, compare to the current revision
		if (!$oldid) {
			$or = $this->page->getCurrentRevision();
		} else {
			$or = $this->page->getRevision( $oldid );
		}
		$dr = $this->page->getRevision( $diff );
		
		// Diff the two versions
		$ota = explode( "\n", $or->pagetext );
		$nta = explode( "\n", $dr->pagetext );
		$diffs = new Diff( $ota, $nta );
		$formatter = new TableDiffFormatter();
		$content = $formatter->format( $diffs );
		
		// Include any CSS
		$this->getStyles();
		
		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$pagetitle = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
		
		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$pagetitle.': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename.a.'task='.$this->_task);
		
		// Check authorization
		$authorized = $this->checkAuthorization();
		
		// Output errors
		if ($this->getError()) {
			echo WikiHtml::error( $this->getError() );
		}
		// Output other messages
		if ($this->_message) {
			echo WikiHtml::passed( $this->_message );
		}
		// Output content
		echo WikiHtml::compare( $this->_sub, JText::_(strtoupper($this->_name)), $pagetitle, $this->page, $content, $this->_option, $this->_task, $or, $dr, $authorized );
	}

	//-----------
	
	protected function deleterevision() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$pagename = trim(JRequest::getVar( 'pagename', '' ));
		$scope = trim(JRequest::getVar( 'scope', '' ));
		$id = JRequest::getInt( 'oldid', 0 );
		
		if ($id) {
			// Load the revision
			$revision = new WikiPageRevision( $database );
			$revision->load( $id );
			
			// Get a count of all approved revisions
			$count = $revision->getRevisionCount();
			
			// Can't delete - it's the only approved version!
			if ($count <= 1) {
				$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'scope='.$scope.'pagename='.$pagename.a.'task=history');
				return;
			}
			
			// Delete it
			$revision->delete( $id );
		}
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'scope='.$scope.a.'pagename='.$pagename.a.'task=history');
	}
	
	//-----------
	
	protected function approve() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$pagename = trim(JRequest::getVar( 'pagename', '' ));
		$scope = trim(JRequest::getVar( 'scope', '' ));
		$id = JRequest::getInt( 'oldid', 0 );
		
		if ($id) {
			// Load the revision, approve it, and save
			$revision = new WikiPageRevision( $database );
			$revision->load( $id );
			$revision->approved = 1;
			if (!$revision->check()) {
				echo WikiHtml::alert( $revision->getError() );
				exit();
			}
			if (!$revision->store()) {
				echo WikiHtml::alert( $revision->getError() );
				exit();
			}
		}
		
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'scope='.$scope.a.'pagename='.$pagename);
	}

	//----------------------------------------------------------
	// Comment Views
	//----------------------------------------------------------
	
	protected function comments()
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$this->getPage();
		
		// Viewing comments for a specific version?
		$v = JRequest::getInt( 'version', 0 );
		if ($v) {
			$ver = "AND version=".$v;
		} else {
			$ver = "";
		}
		
		// Get comments
		$c = new WikiPageComment( $database );
		$c->getComments($this->page->id, 0, $ver, '');
		$comments = $database->loadObjectList();
		for ($i=0,$n=count($comments);$i<$n;$i++) 
		{
			// Get replies
			$c->getComments($this->page->id, $comments[$i]->id, $ver, '');
			$comments[$i]->children = $database->loadObjectList();
			for ($k=0,$m=count($comments[$i]->children);$k<$m;$k++) 
			{
				// Get replies to replies
				$c->getComments($this->page->id, $comments[$i]->children[$k]->id, $ver, 'LIMIT 4');
				$comments[$i]->children[$k]->children = $database->loadObjectList();
			}
		}

		// Do we have a comment object? If so, then we're in edit mode
		if (is_object($this->comment)) {
			$mycomment =& $this->comment;
		} else {
			$mycomment = NULL;
		}
		
		$revision = new WikiPageRevision( $database );
		$versions = $revision->getRevisionNumbers( $this->page->id );
		
		// Include any CSS
		$this->getStyles();
		
		// Prep the pagename for display 
		// e.g. "MainPage" becomes "Main Page"
		$pagetitle = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
		
		// Set the page's <title> tag
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$pagetitle.': '.JText::_(strtoupper($this->_task)) );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename.a.'task='.$this->_task);
		
		// Check authorization
		$authorized = $this->checkAuthorization();
		
		// Output errors
		if ($this->getError()) {
			echo WikiHtml::error( $this->getError() );
		}
		// Output other messages
		if ($this->_message) {
			echo WikiHtml::passed( $this->_message );
		}
		// Output content
		echo WikiHtml::comments( $this->_sub, JText::_(strtoupper($this->_name)), $pagetitle, $this->page, $comments, $this->_option, $this->_task, $mycomment, $authorized, $versions, $v );
	}
	
	//-----------
	
	protected function editcomment() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$this->getPage();
		
		// Is the user logged in?
		// If not, then we need to stop everything else and display a login form
		if ($juser->get('guest')) {
			// Include any CSS
			$this->getStyles();
			
			// Prep the pagename for display 
			// e.g. "MainPage" becomes "Main Page"
			$pagetitle = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
		
			// Set the page's <title> tag
			$document =& JFactory::getDocument();
			$document->setTitle( JText::_(strtoupper($this->_name)).': '.$pagetitle.': '.JText::_('ADD_COMMENT') );

			// Set the pathway
			$app =& JFactory::getApplication();
			$pathway =& $app->getPathway();
			if (count($pathway->getPathWay()) <= 0) {
				$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
			}
			$pathway->addItem($pagetitle,'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename);
			$pathway->addItem(JText::_('ADD_COMMENT'),'index.php?option='.$this->_option.a.'scope='.$this->page->scope.a.'pagename='.$this->pagename.a.'task='.$this->_task);

			$params =& new JParameter( $this->page->params );
			
			// Check authorization
			$authorized = $this->checkAuthorization();

			ximport('xmodule');
			
			echo WikiHtml::hed(2,$pagetitle);
			echo WikiHtml::subMenu( $this->_sub, $this->_option, $this->page->pagename, $this->page->scope, $this->page->state, $this->_task, $params, $authorized );
			echo WikiHtml::warning( JText::_('WIKI_WARNING_LOGIN_REQUIRED') );

			XModuleHelper::displayModules('force_mod',-1);
			return;
		}
		
		// Retrieve a comment ID if we're editing
		$id = JRequest::getInt( 'id', 0 );
		$comment = new WikiPageComment( $database );
		$comment->load( $id );
		if (!$id) {
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			$revision = $this->page->getCurrentRevision();
			
			$comment->pageid     = $revision->pageid;
			$comment->version    = $revision->version;
			$comment->parent     = JRequest::getInt( 'parent', 0 );
			$comment->created_by = $juser->get('id');
		}
		
		// Add the comment object to our controller's registry
		// This is how comments() knows if it needs to display a form or not
		$this->comment = $comment;

		$this->comments();
	}
	
	//-----------
	
	protected function savecomment() 
	{
		$database =& JFactory::getDBO();

		$pagename = trim(JRequest::getVar( 'pagename', '', 'post' ));
		$scope = trim(JRequest::getVar( 'scope', '', 'post' ));
		
		// Bind the form data to our object
		$row = new WikiPageComment( $database );
		if (!$row->bind( $_POST )) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}
		
		// Parse the wikitext and set some values
		$p = new WikiParser($pagename);
		$row->chtml = $p->parse($row->ctext);
		$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
		$row->created   = ($row->created) ? $row->created : date( "Y-m-d H:i:s" );
		
		// Check for missing (required) fields
		if (!$row->check()) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}

		// Save the data
		if (!$row->store()) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}
		
		// Did they rate the page? 
		// If so, update the page with the new average rating
		if ($row->rating) {
			$page = new WikiPage( $database );
			$page->load( intval($row->pageid) );
			$page->calculateRating();
			if (!$page->store()) {
				echo WikiHtml::alert( $page->getError() );
				exit();
			}
		}
		
		// Redirect to Comments page
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.a.'scope='.$scope.a.'pagename='.$pagename.a.'task=comments');
	}

	//-----------

	protected function removecomment() 
	{
		$database =& JFactory::getDBO();
		
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Make sure we have a comment to delete
		if ($id) {
			// Make sure they're authorized to delete (must be an author)
			if ( $this->checkAuthorization() ) {	
				$comment = new WikiPageComment( $database );
				$comment->delete( $id );
		
				$this->_message = JText::_('WIKI_COMMENT_DELETED');
			} else {
				$this->setError( JText::_('WIKI_ERROR_NOTAUTH') );
			}
		}
		
		$this->comments();
	}
	
	//-----------

	protected function reportcomment() 
	{
		$database =& JFactory::getDBO();
		
		$id = JRequest::getInt( 'id', 0, 'request' );
		
		// Make sure we have a comment to report
		if ($id) {
			$comment = new WikiPageComment( $database );
			$comment->report( $id );
		
			$this->_message = JText::sprintf('WIKI_COMMENT_REPORTED',$id);
		}
		
		$this->comments();
	}

	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	protected function upload()
	{
		// Get some libraries we'll need
		ximport('fileupload');
		
		// Get some objects we'll need
		$config =& $this->config;
		
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'post' );

		// Build the upload path if it doesn't exist
		$file_path = JPATH_ROOT.$config->filepath.DS.$listdir;
		if (!is_dir( $file_path )) {
			$this->make_path( $file_path );
		}
		
		// Upload new files
		$upload = new FileUpload;
		$upload->upload_dir     = $file_path;
		$upload->temp_file_name = trim($_FILES['upload']['tmp_name']);
		$upload->file_name      = trim(strtolower($_FILES['upload']['name']));
		//$upload->file_name      = str_replace(' ', '_', $upload->file_name);
		$normalized_valid_chars = 'a-zA-Z0-9\-\_\.';
		$upload->file_name      = preg_replace("/[^$normalized_valid_chars]/", "", $upload->file_name);
		$upload->ext_array      = $config->file_ext;
		$upload->max_file_size  = $config->maxAllowed;
		
		$result = $upload->upload_file_no_validation();
		
		// Was there an error?
		if (!$result) {
			$this->setError( JText::_('ERROR_UPLOADING').' '.$upload->err );
		} else {
			// File was uploaded
			$database =& JFactory::getDBO();
			
			$juser =& JFactory::getUser();
			
			// Create database entry
			$description = trim(JRequest::getVar( 'description', '', 'post' ));
			$description = htmlspecialchars($description);
			
			$bits = array('id'=>'','pageid'=>$listdir,'filename'=>$upload->file_name,'description'=>$description,'created'=>date( 'Y-m-d H:i:s', time() ),'created_by'=>$juser->get('id'));
			$attachment = new WikiPageAttachment( $database );
			$attachment->bind( $bits );
			if (!$attachment->check()) {
				$this->setError( $attachment->getError() );
			}
			if (!$attachment->store()) {
				$this->setError( $attachment->getError() );
			}
		}
		
		$this->media();
	}

	//-----------

	protected function delete_folder() 
	{
		$config =& $this->config;
		
		// Incoming
		$delFolder = trim(JRequest::getVar( 'delFolder', '', 'get' ));
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );

		// Delete the folder
		$del_folder = JPATH_ROOT.$config->filepath.DS.$listdir.DS.$delFolder;
		rmdir($del_folder);
		
		$this->media();
	}

	//-----------

	protected function delete_file() 
	{
		$database =& JFactory::getDBO();
		$config   =& $this->config;
		
		// Incoming
		$delFile = trim(JRequest::getVar( 'delFile', '', 'get' ));
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );
		//$id = JRequest::getInt( 'id', 0, 'get' );

		// Delete the file
		$del_file = JPATH_ROOT.$config->filepath.DS.$listdir.DS.$delFile;
		unlink($del_file);
		
		// Delete the database entry for the file
		$attachment = new WikiPageAttachment( $database );
		$attachment->deleteFile( $delFile, $listdir );
		
		$this->media();
	}

	//-----------

	protected function media() 
	{
		$config =& $this->config;
		
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'request' );
	
		// Display any errors
		if ($this->getError()) {
			WikiHtml::error($this->getError());
		}
		
		echo WikiHtml::media($config, $listdir, $this->_option, $this->_name);
	}

	//-----------

	protected function recursive_listdir($base) 
	{ 
	    static $filelist = array(); 
	    static $dirlist  = array(); 

	    if (is_dir($base)) { 
	       $dh = opendir($base); 
	       while (false !== ($dir = readdir($dh))) 
		   { 
	           if (is_dir($base .DS. $dir) && $dir !== '.' && $dir !== '..' && strtolower($dir) !== 'cvs') { 
	                $subbase    = $base .DS. $dir; 
	                $dirlist[]  = $subbase; 
	                $subdirlist = $this->recursive_listdir($subbase); 
	            } 
	        } 
	        closedir($dh); 
	    } 
	    return $dirlist; 
	} 

	//-----------
 
	protected function list_files() 
	{
		$database =& JFactory::getDBO();
		$config =& $this->config;
		
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );

		// Get the directory we'll be reading out of
		$d = @dir(JPATH_ROOT.$config->filepath.DS.$listdir);

		//$attachment = new WikiPageAttachment( $database );

		$html = WikiHtml::attachTop( $this->_option, $this->_name );
		if ($d) {
			$images  = array();
			$folders = array();
			$docs    = array();
	
			// Loop through all files and separate them into arrays of images, folders, and other
			while (false !== ($entry = $d->read())) 
			{
				$img_file = $entry; 
				if (is_file(JPATH_ROOT.$config->filepath.DS.$listdir.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|jpeg|jpe|tif|tiff|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if (is_dir(JPATH_ROOT.$config->filepath.DS.$listdir.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();	

			//$html .= WikiHtml::imageStyle( $listdir );	

			if (count($images) > 0 || count($folders) > 0 || count($docs) > 0) {	
				ksort($images);
				ksort($folders);
				ksort($docs);

				$html .= WikiHtml::draw_table_header();

				for ($i=0; $i<count($folders); $i++) 
				{
					$folder_name = key($folders);		
					WikiHtml::show_dir( DS.$folders[$folder_name], $folder_name, $listdir, $this->_option );
					next($folders);
				}
				for ($i=0; $i<count($docs); $i++) 
				{
					$doc_name = key($docs);	
					$iconfile = $config->iconpath.DS.substr($doc_name,-3).'.png';

					if (file_exists(JPATH_ROOT.$iconfile))	{
						$icon = $iconfile;
					} else {
						$icon = $config->iconpath.DS.'unknown.png';
					}
					
					//$a = $attachment->getID($doc_name, $listdir);
					
					$html .= WikiHtml::show_doc($this->_option, $docs[$doc_name], $listdir, $icon);
					next($docs);
				}
				for ($i=0; $i<count($images); $i++) 
				{
					$image_name = key($images);
					$iconfile = $config->iconpath.DS.substr($image_name,-3).'.png';
					if (file_exists(JPATH_ROOT.$iconfile))	{
						$icon = $iconfile;
					} else {
						$icon = $config->iconpath.DS.'unknown.png';
					}

					//$a = $attachment->getID($image_name, $listdir);

					$html .= WikiHtml::show_doc($this->_option, $images[$image_name], $listdir, $icon);
					next($images);
				}
				
				$html .= WikiHtml::draw_table_footer();
			} else {
				$html .= WikiHtml::draw_no_results();
			}
		} else {
			$html .= WikiHtml::draw_no_results();
		}
		$html .= WikiHtml::attachBottom();
		echo $html;
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	private function make_path( $path, $mode=0777 ) 
	{
		if (file_exists( $path )) {
		    return true;
		}
		$path = str_replace( '\\', '/', $path );
		$path = str_replace( '//', '/', $path );
		$parts = explode( '/', $path );
	
		$n = count( $parts );
		if ($n < 1) {
		    return mkdir( $path, $mode );
		} else {
			$path = '';
			for ($i = 0; $i < $n; $i++) 
			{
			    $path .= $parts[$i] . '/';
			    if (!file_exists( $path )) {
			        if (!mkdir( $path, $mode )) {
			            return false;
					}
				}
			}
			return true;
		}
	}

	//-----------

	private function normalize( $txt ) 
	{
		//$valid_chars = '\-\:a-zA-Z0-9';
		$valid_chars = 'a-zA-Z0-9';
		$normalized = preg_replace("/[^$valid_chars]/", "", $txt);
		return $normalized;
	}
	
	//-----------

	private function getPage() 
	{	
		// Check if the page object already exist
		if (!is_object($this->page)) {
			if (!$this->pagename)
				$this->pagename = trim(JRequest::getVar( 'pagename', '', 'default', 'none', 2 ));
				//$this->pagename = $this->normalize($this->pagename);
				//if (substr(strtolower($this->pagename), 0, 5) != 'help:')
				//	$this->pagename = str_replace(':','-',$this->pagename);

			if (!$this->scope) 
				$this->scope = trim(JRequest::getVar( 'scope', '' ));

			// No page name given! Default to the home page
			if (!$this->pagename && $this->_task != 'new') 
				$this->pagename = $this->config->homepage;
			
			// Load the page
			$database =& JFactory::getDBO();
			$page = new WikiPage( $database );
			$page->load( $this->pagename, $this->scope );
			if (!$page->exist() && (substr(strtolower($this->pagename), 0, 4) == 'help')) {
				$page->load( $this->pagename, '' );
				$page->scope = $this->scope;
			}

			$this->page = $page;
		}
	}
	
	//-----------

	private function checkAuthorization( $type='edit' ) 
	{
		$juser =& JFactory::getUser();

		// Check if they are logged in
		if ($juser->get('guest'))
			return false;

		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return 'admin';
		}

		$params =& new JParameter( $this->page->params );
		if ($params->get( 'mode' ) == 'knol') {
			if ($type == 'edit') {
				// Knol mode - only authorized authors can make pre-approved changes
				// Check if they're the page creator
				if ($this->page->created_by == $juser->get('id')) {
					return true;
				}

				// Check if they're in the approved author list
				$authors = $this->page->getAuthors();
				if (in_array($juser->get('username'),$authors)) {
					return true;
				}

				return false;
			} else {
				// Is a specific group set?
				if ($this->page->group != '') {
					// We have a specific group. What is the access level?
					if ($this->page->access == 1 || $this->page->access == 2) {
						// This page is a private page
						// So, we need to check if the current user is a member of the page's group
						$usergroups = XUserHelper::getGroups( $juser->get('id') );
						if ($usergroups && count($usergroups) > 0) {
							foreach ($usergroups as $ug) 
							{
								if ($ug->cn == $this->page->group && $ug->regconfirmed) {
									// They are a member. Return true.
									return true;
								}
							}
						}
						// Default is no access
						return false;
					}
				}
			}
		} else {
			// Wiki mode

			// Is a specific group set?
			if ($this->page->group != '') {
				// We have a specific group. What is the access level?
				if ($this->page->access == 1 || $this->page->access == 2 || $this->_task == 'edit') {
					// This page is a private page
					// So, we need to check if the current user is a member of the page's group
						$usergroups = XUserHelper::getGroups( $juser->get('id') );
						if ($usergroups && count($usergroups) > 0) {
							foreach ($usergroups as $ug) 
							{
								if ($ug->cn == $this->page->group && $ug->regconfirmed) {
									// They are a member. Return true.
									return true;
								}
							}
						}
					// Default is no access
					return false;
				}

				// Group set but public access
				if ($this->_task == 'edit') {
					return false;
				} else {
					return true;
				}
			}
			
			// No specific group set - default is everyone has access
			return true;
		}

		return false;
	}
	
	//-----------
	
	private function splitPagename($page) 
	{
		$lang =& JFactory::getLanguage();
		$language = $lang->getBackwardLang();
		
		if (preg_match("/\s/", $page))
			return $page;  // Already split --- don't split any more.
    
		// This algorithm is specialized for several languages.
		// (Thanks to Pierrick MEIGNEN)
		// Improvements for other languages welcome.
		static $RE;
		if (!isset($RE)) {
			// This mess splits between a lower-case letter followed by
			// either an upper-case or a numeral; except that it wont
			// split the prefixes 'Mc', 'De', or 'Di' off of their tails.
			switch ($language) 
			{
				case 'french':
					$RE[] = '/([[:lower:]])((?<!Mc|Di)[[:upper:]]|\d)/';
					break;
				case 'english':
				case 'italian':
				case 'es':
				case 'german':
					$RE[] = '/([[:lower:]])((?<!Mc|De|Di)[[:upper:]]|\d)/';
					break;
			}
			
			$sep = preg_quote(WIKI_SUBPAGE_SEPARATOR, '/');
			// This the single-letter words 'I' and 'A' from any following
			// capitalized words.
			switch ($language) 
			{
				case 'french':
					$RE[] = "/(?<= |${sep}|^)([À])([[:upper:]][[:lower:]])/";
					break;
				case 'english':
				default:
					$RE[] = "/(?<= |${sep}|^)([AI])([[:upper:]][[:lower:]])/";
					break;
			}
			
			// Split numerals from following letters.
			$RE[] = '/(\d)([[:alpha:]])/';
			// Split at subpage seperators.
			$RE[] = "/([^${sep}]+)(${sep})/";
			$RE[] = "/(${sep})([^${sep}]+)/";
			foreach ($RE as $key)
				$RE[$key] = $this->pcre_fix_posix_classes($key);
		}

		foreach ($RE as $regexp) 
		{
			$page = preg_replace($regexp, '\\1 \\2', $page);
		}

		$r = '/(.+?)\:(.+?)/';
		$page = preg_replace($r, '\\1: \\2', $page);
		$page = str_replace('_', ' ', $page);
		return $page;
	}

	// Older version (pre 3.x?) of the PCRE library do not support
	// POSIX named character classes (e.g. [[:alnum:]]).
	//
	// This is a helper function which can be used to convert a regexp
	// which contains POSIX named character classes to one that doesn't.
	//
	// All instances of strings like '[:<class>:]' are replaced by the equivalent
	// enumerated character class.
	//
	// Implementation Notes:
	//
	// Currently we use hard-coded values which are valid only for
	// ISO-8859-1.  Also, currently on the classes [:alpha:], [:alnum:],
	// [:upper:] and [:lower:] are implemented.  (The missing classes:
	// [:blank:], [:cntrl:], [:digit:], [:graph:], [:print:], [:punct:],
	// [:space:], and [:xdigit:] could easily be added if needed.)
	//
	// This is a hack.  I tried to generate these classes automatically
	// using ereg(), but discovered that in my PHP, at least, ereg() is
	// slightly broken w.r.t. POSIX character classes.  (It includes
	// "\xaa" and "\xba" in [:alpha:].)
	//
	// So for now, this will do.  --Jeff <dairiki@dairiki.org> 14 Mar, 2001
	private function pcre_fix_posix_classes($regexp) 
	{
		// First check to see if our PCRE lib supports POSIX character
		// classes.  If it does, there's nothing to do.
		if (preg_match('/[[:upper:]]/', ''))
			return $regexp;
			
		static $classes = array('alnum' => "0-9A-Za-z\xc0-\xd6\xd8-\xf6\xf8-\xff",
								'alpha' => "A-Za-z\xc0-\xd6\xd8-\xf6\xf8-\xff",
								'upper' => "A-Z\xc0-\xd6\xd8-\xde",
								'lower' => "a-z\xdf-\xf6\xf8-\xff");
		
		$keys = join('|', array_keys($classes));
		
		return preg_replace("/\[:($keys):]/e", '$classes["\1"]', $regexp);
	}
}
?>
