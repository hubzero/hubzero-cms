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

ximport('Hubzero_Controller');

/**
 * Short description for 'WikiController'
 * 
 * Long description (if any) ...
 */
class WikiController extends Hubzero_Controller
{

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $config Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $config=array() )
	{
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

		parent::__construct($config);
	}

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->config = JComponentHelper::getParams( 'com_wiki' );

	/**
	 * Description for ''WIKI_SUBPAGE_SEPARATOR''
	 */
		define('WIKI_SUBPAGE_SEPARATOR',$this->config->get('subpage_separator'));

	/**
	 * Description for ''WIKI_MAX_PAGENAME_LENGTH''
	 */
		define('WIKI_MAX_PAGENAME_LENGTH',$this->config->get('max_pagename_length'));

		$wp = new WikiPage( $this->database );
		$c = $wp->count();
		if (!$c) {
			$result = WikiSetup::initialize( $this->_option );
			if ($result) {
				$this->setError( $result );
			}
		}

		$this->_task = JRequest::getVar( 'task', 'view' );
		$this->_base_path = JPATH_ROOT.DS.'components'.DS.'com_wiki';

		if (!$this->pagename) {
			$this->pagename = trim(JRequest::getVar( 'pagename', '', 'default', 'none', 2 ));
		}
		if (substr(strtolower($this->pagename), 0, 5) == 'image'
		 || substr(strtolower($this->pagename), 0, 4) == 'file') {
			$this->_task = 'download';
		}

		switch ($this->_task)
		{
			// Media manager
			case 'media':          $this->media();          break;
			case 'list':           $this->list_files();     break;
			case 'upload':         $this->upload();         break;
			case 'deletefolder':   $this->delete_folder();  break;
			case 'deletefile':     $this->delete_file();    break;
			case 'download':       $this->download();       break;

			// Page tasks
			case 'view':           $this->view();           break;
			case 'new':            $this->edit();           break;
			case 'edit':           $this->edit();           break;
			case 'save':           $this->save();           break;
			case 'cancel':         $this->cancel();         break;
			case 'delete':         $this->delete();         break;
			case 'deleterevision': $this->deleterevision(); break;
			case 'approve':        $this->approve();        break;
			case 'renamepage':     $this->renamepage();     break;
			case 'saverename':     $this->saverename();     break;

			// Version compare
			case 'history':        $this->history();        break;
			case 'compare':        $this->compare();        break;

			// Comments
			case 'comments':       $this->comments();       break;
			case 'editcomment':    $this->editcomment();    break;
			case 'addcomment':     $this->editcomment();    break;
			case 'savecomment':    $this->savecomment();    break;
			case 'removecomment':  $this->removecomment();  break;
			case 'reportcomment':  $this->reportcomment();  break;
			case 'update': $this->_update(); break;
			default: $this->view(); break;
		}
	}

	/**
	 * Short description for '_update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function _update()
	{
		$wpa = new WikiPageAuthor($this->database);
		if ($wpa->transitionAuthors()) {
			echo 'Done.';
		} else {
			echo $wpa->getError();
		}
	}
	/**
	 * Short description for 'getScripts'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      string $path Parameter description (if any) ...
	 * @return     void
	 */

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

	/**
	 * Short description for 'view'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function view()
	{
		// Load the page
		$this->getPage();

		// Include any CSS
		$this->_getStyles();

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
			$this->page->pagename = $this->pagename;
			$this->page->scope = $this->scope;
			$this->page->group = $this->_group;

			// No! Ask if they want to create a new page
			$view = new JView( array('base_path'=>$this->_base_path,'name'=>'page', 'layout'=>'doesnotexist') );
			$view->title = $pagetitle;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->sub = $this->_sub;
			$view->page = $this->page;
			$view->pagename = $this->pagename;
			$view->scope = $this->scope;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}

		if ($this->page->scope && !$this->page->group) {
			$bits = explode('/',$this->page->scope);
			//$database =& JFactory::getDBO();
			$s = array();
			foreach ($bits as $bit)
			{
				$bit = trim($bit);
				if ($bit != '/' && $bit != '') {
					$p = new WikiPage( $this->database );
					$p->load( $bit, implode('/',$s) );
					if ($p->id) {
						$pathway->addItem($p->title,'index.php?option='.$this->_option.'&scope='.$p->scope.'&pagename='.$p->pagename);
					}
					$s[] = $bit;
				}
			}
		}
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.'&scope='.$this->scope.'&pagename='.$this->pagename);

		// Retrieve a specific version if given
		$version = JRequest::getInt( 'version', 0 );
		if ($version && $version > 0) {
			$revision = $this->page->getRevision($version);
			if (!$revision->id) {
				$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'page', 'layout'=>'nosuchrevision') );
				$view->title = $pagetitle;
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->sub = $this->_sub;
				$view->page = $this->page;
				$view->version = $version;
				$view->authorized = $this->checkAuthorization('view');
				$view->editauthorized = $this->checkAuthorization('edit');

				if ($this->getError()) {
					$view->setError( $this->getError() );
				}
				$view->display();
				return;
			}
		} else {
			$revision = $this->page->getCurrentRevision();
		}

		// Up the hit counter
		$this->page->hit();

		// Load the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $this->scope,
			'pagename' => $this->pagename,
			'pageid'   => $this->page->id,
			'filepath' => '',
			'domain'   => $this->_group
		);

		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();

		// Parse the text
		$revision->pagehtml = $p->parse($revision->pagetext, $wikiconfig, true, true);

		// Create a linked Table of Contents
		$output = (is_object($p->_parser)) ? $p->_parser->parser->formatHeadings( $revision->pagehtml ) : array('text'=>$revision->pagehtml,'toc'=>'');

		// Get non-author contributors
		$contributors = $revision->getContributors();

		// Check authorization
		$authorized = $this->checkAuthorization('view');
		$editauthorized = $this->checkAuthorization('edit');

		// Get the page's tags
		if ($authorized == 'admin') {
			$tags = $this->page->getTags(1);
		} else {
			$tags = $this->page->getTags();
		}

		// Check if the page is group restricted and the user is authorized
		if ($this->page->group != '' && $this->page->access != 0 && !$authorized) {
			if ($this->_sub) {
				echo WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH') );
			} else {
				JError::raiseWarning( 403, JText::_('WIKI_WARNING_NOT_AUTH') );
			}
			return;
		}

		// Output content
		/*if ($this->_sub) {
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>$this->_sub,
					'element'=>'wiki',
					'name'=>'page'
				)
			);
		} else {*/
			$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'page') );
		//}
		$view->title = $pagetitle;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->message = $this->_message;
		$view->page = $this->page;
		$view->revision = $revision;
		$view->sub = $this->_sub;
		$view->authorized = $authorized;
		$view->editauthorized = $editauthorized;
		$view->output = $output;
		$view->tags = $tags;
		$view->contributors = $contributors;
		$view->warning = ($this->warning) ? $this->warning : '';
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'login'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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
		if ($this->_sub) {
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>$this->_sub,
					'element'=>'wiki',
					'name'=>'login'
				)
			);
		} else {
			$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'login') );
		}
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->sub = $this->_sub;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();

	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function edit()
	{

		// Check if they are logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Load the page
		$this->getPage();
		$ischild = false;
		if ($this->page->id && $this->_task == 'new') {
			$this->page->id = 0;
			$ischild = true;
		}

		// Get the most recent version for editing
		$revision = $this->page->getCurrentRevision();
		$revision->created_by = $this->juser->get('id');

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
			$wpa = new WikiPageAuthor($this->database);
			$auths = $wpa->getAuthors($this->page->id);
			$authors = '';
			if (count($auths) > 0) {
				$autharray = array();
				foreach ($auths as $auth)
				{
					$autharray[] = $auth->username;
				}
				$authors = implode(', ', $autharray);
			}
		} else {
			$this->page->created_by = $this->juser->get('id');
			$this->page->scope = $this->scope;
			if ($this->_group != '') {
				$this->page->group = $this->_group;
				$this->page->access = 1;
			}

			if ($ischild && $this->page->pagename) {
				$this->page->scope .= ($this->page->scope) ? DS.$this->page->pagename : $this->page->pagename;
				$this->page->pagename = '';
				$this->page->title = 'New Page';
			}

			$this->page->title = ($this->page->title) ? $this->page->title : $this->splitPagename($this->pagename);
			$authors = '';
		}

		// Include any CSS
		$this->_getStyles();

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
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename.'&task='.$this->_task);

		// Check authorization
		$authorized = $this->checkAuthorization('view');
		$editauthorized = $this->checkAuthorization('edit');

		// Check if the page is locked and the user is authorized
		if ($this->page->state == 1 && $editauthorized !== 'admin' && $editauthorized !== 'manager') {
			//echo WikiHtml::div(WikiHtml::hed( 2, $pagetitle ), 'full', 'content-header');
			//echo WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH_TO_MODIFY') );
			//JError::raiseWarning( 403, JText::_('WIKI_WARNING_NOT_AUTH') );
			$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'error') );
			$view->title = $pagetitle;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->sub = $this->_sub;
			$view->warning = JText::_('WIKI_WARNING_NOT_AUTH_EDITOR');
			$view->page = $this->page;
			$view->authorized = $authorized;
			$view->editauthorized = $editauthorized;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			/*$this->setError( JText::_('WIKI_ERROR_NOTAUTH') );
			$this->view();*/
			return;
		}

		// Check if the page is group restricted and the user is authorized
		if ($this->page->group != '' && $this->page->access != 0 && !$editauthorized) {
			//echo WikiHtml::div(WikiHtml::hed( 2, $pagetitle ), 'full', 'content-header');
			//echo WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH_TO_MODIFY') );
			//JError::raiseWarning( 403, JText::_('WIKI_WARNING_NOT_AUTH') );
			$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'error') );
			$view->title = $pagetitle;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->sub = $this->_sub;
			$view->warning = JText::_('WIKI_WARNING_NOT_AUTH_EDITOR');
			$view->page = $this->page;
			$view->authorized = $authorized;
			$view->editauthorized = $editauthorized;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			/*$this->setError( JText::_('WIKI_ERROR_NOTAUTH') );
			$this->view();*/
			return;
		}

		$revision->summary = '';

		$preview = NULL;
		// Are we previewing?
		if (is_object($this->preview) || $this->getError()) {
			if (!$this->getError()) {
			// Yes - get the preview so we can parse it and display
			$preview = $this->preview;

			// Parse the HTML
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => $this->scope,
				'pagename' => $this->pagename,
				'pageid'   => $this->page->id,
				'filepath' => '',
				'domain'   => $this->_group
			);
				ximport('Hubzero_Wiki_Parser');
				$p =& Hubzero_Wiki_Parser::getInstance();
				$preview->pagehtml = $p->parse($this->preview->pagetext, $wikiconfig, true, true);
			}

			$revision->pagetext   = $this->preview->pagetext;
			$revision->summary    = $this->preview->summary;
			$revision->minor_edit = $this->preview->minor_edit;

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
		}

		// Process the page's params
		$params = new JParameter( $this->page->params, 'components'.DS.$this->_option.DS.$this->_name.'.xml', 'component' );

		// Output content
		//echo WikiHtml::edit( $this->_sub, JText::_(strtoupper($this->_name)), $authorized, $pagetitle, $this->page, $revision, $authors, $this->_option, $tagstring, $this->_task, $params, $preview );
		$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'edit') );
		$view->title = $pagetitle;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->sub = $this->_sub;
		$view->message = $this->_message;
		$view->page = $this->page;
		$view->name = JText::_(strtoupper($this->_name));
		$view->authorized = $authorized;
		$view->editauthorized = $editauthorized;
		$view->revision = $revision;
		$view->authors = $authors;
		$view->params = $params;
		$view->preview = $preview;
		$view->tags = $tagstring;
		$view->tplate = trim(JRequest::getVar( 'tplate', '' ));
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function save()
	{
		$pageid = JRequest::getInt( 'pageid', 0, 'post' );

		// Was the preview button pushed?
		$preview = trim(JRequest::getVar( 'preview', '' ));
		if ($preview) {
			// Create a new revision
			$revision = new WikiPageRevision( $this->database );
			$revision->pageid     = $pageid;
			$revision->created    = date( 'Y-m-d H:i:s', time() );
			$revision->created_by = JRequest::getInt( 'created_by', 0, 'post' );
			$revision->version    = JRequest::getInt( 'version', 0, 'post' );
			//$revision->pagetext   = stripslashes(trim(JRequest::getVar( 'pagetext', '', 'post' )));
			$revision->pagetext   = stripslashes(rtrim($_POST['pagetext']));
			$revision->summary    = preg_replace('/\s+/', ' ',trim(JRequest::getVar( 'summary', '', 'post' )));
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
		$page = new WikiPage( $this->database );
		if (!$pageid) {
			// New page - save it to the database
			$page->pagename = trim(JRequest::getVar( 'pagename', '', 'post' ));
			$page->scope = trim(JRequest::getVar( 'scope', '' ));
			if (!$page->pagename) {
				// No pagename so let's generate one from the title
				$page->pagename = trim(JRequest::getVar( 'title', '', 'post' ));
				if (!$page->pagename) {
					$this->setError( JText::_('WIKI_ERROR_NO_PAGE_TITLE') );
				} else {
					$page->pagename = $this->normalize($page->pagename);
				}

				if (!$this->getError()) {
					if (substr(strtolower($page->pagename), 0, 6) == 'image:'
				 	|| substr(strtolower($page->pagename), 0, 5) == 'file:') {
						$this->setError( JText::_('WIKI_ERROR_INVALID_TITLE') );
					}
				}

				if (!$this->getError()) {
					// Check that no other pages are using the new title
					$g = new WikiPage( $this->database );
					$g->load( $page->pagename, $page->scope );
					if ($g->exist()) {
						$this->setError( JText::_('WIKI_ERROR_PAGE_EXIST') );
					}
				}

				if ($this->getError()) {
					$revision = new WikiPageRevision( $this->database );
					$revision->pageid     = $pageid;
					$revision->created    = date( 'Y-m-d H:i:s', time() );
					$revision->created_by = JRequest::getInt( 'created_by', 0, 'post' );
					$revision->version    = JRequest::getInt( 'version', 0, 'post' );
					$revision->pagetext   = stripslashes(rtrim($_POST['pagetext']));
					$revision->summary    = preg_replace('/\s+/', ' ',trim(JRequest::getVar( 'summary', '', 'post' )));
					$revision->minor_edit = JRequest::getInt( 'minor_edit', 0, 'post' );
					$this->preview = $revision;
					$this->_task = 'new';
					$this->edit();
					return;
				}
			}
			$page->hits = 0;
			$page->created_by = JRequest::getInt( 'created_by', 0, 'post' );
			$page->rating = '0.0';
			$page->times_rated = 0;
			$page->access = JRequest::getInt( 'access', 0, 'post' );
			$page->group = trim(JRequest::getVar( 'group', '', 'post' ));
			$page->state = JRequest::getInt( 'state', 0, 'post' );

			$old = new WikiPageRevision( $this->database );
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
			//$page->authors = trim(JRequest::getVar( 'authors', '', 'post' ));
			if (!$page->updateAuthors(JRequest::getVar('authors', '', 'post'))) {
				$this->setError($page->getError());
			}

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
			$page->state = JRequest::getInt( 'state', 0, 'post' );
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
			if (is_dir(JPATH_ROOT.$this->config->get('filepath').DS.$lid)) {
				jimport('joomla.filesystem.folder');
				if (!JFolder::move(JPATH_ROOT.$this->config->get('filepath').DS.$lid,JPATH_ROOT.$this->config->get('filepath').DS.$page->id)) {
					$this->setError( JFolder::move(JPATH_ROOT.$this->config->get('filepath').DS.$lid,JPATH_ROOT.$this->config->get('filepath').DS.$page->id) );
				}
				$wpa = new WikiPageAttachment( $this->database );
				$wpa->setPageID( $lid, $page->id );
			}
		}

		// Create a new revision
		$revision = new WikiPageRevision( $this->database );
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
			$revision->pagehtml = $p->parse($revision->pagetext, $wikiconfig);

			// Parse attachments
			$a = new WikiPageAttachment( $this->database );
			$a->pageid = $pageid;
			$a->path = $this->config->get('filepath');
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
		$tagging = new WikiTags( $this->database );
		$tagging->tag_object($revision->created_by, $page->id, $tags, 1, 1);

		// Log the action
		$log = new WikiLog( $this->database );
		$log->pid = $page->id;
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		if ($revision->version == 1) {
			$log->action = 'page_created';
		} else {
			$log->action = 'page_edited';
		}
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}

		// Redirect
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&scope='.$page->scope.'&pagename='.$page->pagename);
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function delete()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Load the page
		$this->getPage();

		if (is_object($this->page)) {
			$authorized = $this->checkAuthorization('view');
			$editauthorized = $this->checkAuthorization('edit');

			// Make sure they're authorized to delete
			if ($authorized) {
				$confirmed = JRequest::getInt( 'confirm', 0, 'post' );

				switch ($confirmed)
				{
					case 1:
						$page = new WikiPage( $this->database );

						// Delete the page's history, tags, comments, etc.
						$page->deleteBits( $this->page->id );

						// Finally, delete the page itself
						$page->delete( $this->page->id );

						// Delete the page's files
						if (is_dir($this->config->get('filepath') .DS. $this->page->id)) {
							jimport('joomla.filesystem.folder');
							if (!JFolder::delete($this->config->get('filepath') .DS. $this->page->id)) {
								$this->setError( JText::_('COM_WIKI_UNABLE_TO_DELETE_FOLDER') );
							}
						}

						// Log the action
						$log = new WikiLog( $this->database );
						$log->pid = $this->page->id;
						$log->uid = $this->juser->get('id');
						$log->timestamp = date( 'Y-m-d H:i:s', time() );
						$log->action = 'page_removed';
						$log->actorid = $this->juser->get('id');
						if (!$log->store()) {
							$this->setError( $log->getError() );
						}
					break;

					default:
						// Include any CSS
						$this->_getStyles();

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
						$pathway->addItem($pagetitle,'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename);
						$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename.'&task='.$this->_task);

						// Output content
						$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'delete') );
						$view->title = $pagetitle;
						$view->option = $this->_option;
						$view->task = $this->_task;
						$view->sub = $this->_sub;
						$view->message = $this->_message;
						$view->page = $this->page;
						$view->name = JText::_(strtoupper($this->_name));
						$view->authorized = $authorized;
						$view->editauthorized = $editauthorized;
						if ($this->getError()) {
							$view->setError( $this->getError() );
						}
						$view->display();
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

		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&scope='.$this->scope);
	}

	/**
	 * Short description for 'renamepage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function renamepage()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Load the page
		$this->getPage();

		// Include any CSS
		$this->_getStyles();

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
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper('RENAME')),'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename.'&task='.$this->_task);

		// Check authorization
		$authorized = $this->checkAuthorization('view');
		$editauthorized = $this->checkAuthorization('edit');
		if (!$authorized) {
			//echo WikiHtml::error( JText::_('WIKI_ERROR_NOTAUTH') );
			//JError::raiseWarning( 403, JText::_('WIKI_WARNING_NOT_AUTH') );
			$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'error') );
			$view->title = $pagetitle;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->sub = $this->_sub;
			$view->warning = JText::_('WIKI_WARNING_NOT_AUTH_TO_MODIFY');
			$view->page = $this->page;
			$view->authorized = $authorized;
			$view->editauthorized = $editauthorized;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}

		// Output content
		$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'edit','layout'=>'rename') );
		$view->title = $pagetitle;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->sub = $this->_sub;
		$view->message = $this->_message;
		$view->page = $this->page;
		$view->name = JText::_(strtoupper($this->_name));
		$view->authorized = $authorized;
		$view->editauthorized = $editauthorized;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'saverename'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function saverename()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Incoming
		$oldpagename = trim(JRequest::getVar( 'oldpagename', '', 'post' ));
		$newpagename = trim(JRequest::getVar( 'newpagename', '', 'post' ));
		$scope = trim(JRequest::getVar( 'scope', '', 'post' ));

		// Remove any bad characters
		$newpagename = $this->normalize($newpagename);

		// Make sure they actually made changes
		/*if ($oldpagename == $newpagename) {
			echo WikiHtml::alert( JText::_('WIKI_ERROR_PAGENAME_UNCHANGED') );
			exit();
		}*/

		// Are they just changing case of characters?
		if (strtolower($oldpagename) != strtolower($newpagename)) {
			// Check that no other pages are using the new title
			$p = new WikiPage( $this->database );
			$p->load( $newpagename, $scope );
			if ($p->exist()) {
				echo WikiHtml::alert( JText::_('WIKI_ERROR_PAGE_EXIST').' '.JText::_('CHOOSE_ANOTHER_PAGENAME') );
				exit();
			}
		}

		// Load the page, reset the name, and save
		$page = new WikiPage( $this->database );
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
		$log = new WikiLog( $this->database );
		$log->pid = $page->id;
		$log->uid = $this->juser->get('id');
		$log->timestamp = date( 'Y-m-d H:i:s', time() );
		$log->action = 'page_renamed';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) {
			$this->setError( $log->getError() );
		}

		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&scope='.$page->scope.'&pagename='.$page->pagename);
	}

	//----------------------------------------------------------
	// History Views
	//----------------------------------------------------------

	/**
	 * Short description for 'history'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function history()
	{
		$this->getPage();

		// Get all revisions
		$rev = new WikiPageRevision( $this->database );
		$revisions = $rev->getRevisions( $this->page->id );

		// Include any CSS
		$this->_getStyles();

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
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename.'&task='.$this->_task);

		// Check authorization
		$authorized = $this->checkAuthorization('view');
		$editauthorized = $this->checkAuthorization('edit');

		// Output content
		$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'history') );
		$view->title = $pagetitle;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->sub = $this->_sub;
		$view->message = $this->_message;
		$view->page = $this->page;
		$view->revisions = $revisions;
		$view->authorized = $authorized;
		$view->editauthorized = $editauthorized;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'compare'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function compare()
	{
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
		$this->_getStyles();

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
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename.'&task='.$this->_task);

		// Check authorization
		$authorized = $this->checkAuthorization('view');
		$editauthorized = $this->checkAuthorization('edit');

		// Output content
		$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'history','layout'=>'compare') );
		$view->title = $pagetitle;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->sub = $this->_sub;
		$view->message = $this->_message;
		$view->page = $this->page;
		$view->or = $or;
		$view->dr = $dr;
		$view->name = JText::_(strtoupper($this->_name));
		$view->authorized = $authorized;
		$view->editauthorized = $editauthorized;
		$view->content = $content;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'deleterevision'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function deleterevision()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Incoming
		$pagename = trim(JRequest::getVar( 'pagename', '' ));
		$scope = trim(JRequest::getVar( 'scope', '' ));
		$id = JRequest::getInt( 'oldid', 0 );

		if ($id) {
			$this->getPage();

			// Load the revision
			$revision = new WikiPageRevision( $this->database );
			$revision->load( $id );

			if ($this->checkAuthorization()) {
				// Get a count of all approved revisions
				$count = $revision->getRevisionCount();

				// Can't delete - it's the only approved version!
				if ($count <= 1) {
					$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&scope='.$scope.'pagename='.$pagename.'&task=history');
					return;
				}

				// Delete it
				$revision->delete( $id );

				// Log the action
				$log = new WikiLog( $this->database );
				$log->pid = $this->page->id;
				$log->uid = $this->juser->get('id');
				$log->timestamp = date( 'Y-m-d H:i:s', time() );
				$log->action = 'revision_removed';
				$log->actorid = $this->juser->get('id');
				if (!$log->store()) {
					$this->setError( $log->getError() );
				}
			}
		}

		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&scope='.$scope.'&pagename='.$pagename.'&task=history');
	}

	/**
	 * Short description for 'approve'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function approve()
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Incoming
		$pagename = trim(JRequest::getVar( 'pagename', '' ));
		$scope = trim(JRequest::getVar( 'scope', '' ));
		$id = JRequest::getInt( 'oldid', 0 );

		if ($id) {
			$this->getPage();

			if ($this->checkAuthorization()) {
				// Load the revision, approve it, and save
				$revision = new WikiPageRevision( $this->database );
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

				// Log the action
				$log = new WikiLog( $this->database );
				$log->pid = $this->page->id;
				$log->uid = $this->juser->get('id');
				$log->timestamp = date( 'Y-m-d H:i:s', time() );
				$log->action = 'revision_approved';
				$log->actorid = $this->juser->get('id');
				if (!$log->store()) {
					$this->setError( $log->getError() );
				}
			}
		}

		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&scope='.$scope.'&pagename='.$pagename);
	}

	//----------------------------------------------------------
	// Comment Views
	//----------------------------------------------------------

	/**
	 * Short description for 'comments'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function comments()
	{
		$this->getPage();

		// Viewing comments for a specific version?
		$v = JRequest::getInt( 'version', 0 );
		if ($v) {
			$ver = "AND version=".$v;
		} else {
			$ver = "";
		}

		// Get comments
		$c = new WikiPageComment( $this->database );
		$c->getComments($this->page->id, 0, $ver, '');
		$comments = $this->database->loadObjectList();
		for ($i=0,$n=count($comments);$i<$n;$i++)
		{
			// Get replies
			$c->getComments($this->page->id, $comments[$i]->id, $ver, '');
			$comments[$i]->children = $this->database->loadObjectList();
			for ($k=0,$m=count($comments[$i]->children);$k<$m;$k++)
			{
				// Get replies to replies
				$c->getComments($this->page->id, $comments[$i]->children[$k]->id, $ver, 'LIMIT 4');
				$comments[$i]->children[$k]->children = $this->database->loadObjectList();
			}
		}

		// Do we have a comment object? If so, then we're in edit mode
		if (is_object($this->comment)) {
			$mycomment =& $this->comment;
		} else {
			$mycomment = NULL;
		}

		$revision = new WikiPageRevision( $this->database );
		$versions = $revision->getRevisionNumbers( $this->page->id );

		// Include any CSS
		$this->_getStyles();

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
		$pathway->addItem($pagetitle,'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename);
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.'&scope='.$this->page->scope.'&pagename='.$this->pagename.'&task='.$this->_task);

		// Check authorization
		$authorized = $this->checkAuthorization('view');
		$editauthorized = $this->checkAuthorization('edit');

		// Output content
		$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'comments') );
		$view->title = $pagetitle;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->juser = $this->juser;

		$view->sub = $this->_sub;
		$view->message = $this->_message;
		$view->page = $this->page;
		$view->name = JText::_(strtoupper($this->_name));
		$view->comments = $comments;
		$view->mycomment = $mycomment;
		$view->authorized = $authorized;
		$view->editauthorized = $editauthorized;
		$view->versions = $versions;
		$view->v = $v;

		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'editcomment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function editcomment()
	{
		$this->getPage();

		// Is the user logged in?
		// If not, then we need to stop everything else and display a login form
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}

		// Retrieve a comment ID if we're editing
		$id = JRequest::getInt( 'id', 0 );
		$comment = new WikiPageComment( $this->database );
		$comment->load( $id );
		if (!$id) {
			// No ID, so we're creating a new comment
			// In that case, we'll need to set some data...
			$revision = $this->page->getCurrentRevision();

			$comment->pageid     = $revision->pageid;
			$comment->version    = $revision->version;
			$comment->parent     = JRequest::getInt( 'parent', 0 );
			$comment->created_by = $this->juser->get('id');
		}

		// Add the comment object to our controller's registry
		// This is how comments() knows if it needs to display a form or not
		$this->comment = $comment;

		$this->comments();
	}

	/**
	 * Short description for 'savecomment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function savecomment()
	{
		$pagename = trim(JRequest::getVar( 'pagename', '', 'post' ));
		$scope = trim(JRequest::getVar( 'scope', '', 'post' ));

		// Bind the form data to our object
		$row = new WikiPageComment( $this->database );
		if (!$row->bind( $_POST )) {
			echo WikiHtml::alert( $row->getError() );
			exit();
		}

		// Parse the wikitext and set some values
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $scope,
			'pagename' => $pagename,
			'pageid'   => $row->pageid,
			'filepath' => '',
			'domain'   => $this->_group
		);
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		$row->chtml = $p->parse($row->ctext, $wikiconfig);

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
			$page = new WikiPage( $this->database );
			$page->load( intval($row->pageid) );
			$page->calculateRating();
			if (!$page->store()) {
				echo WikiHtml::alert( $page->getError() );
				exit();
			}
		}

		// Redirect to Comments page
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&scope='.$scope.'&pagename='.$pagename.'&task=comments');
	}

	/**
	 * Short description for 'removecomment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function removecomment()
	{
		$id = JRequest::getInt( 'id', 0, 'request' );

		// Make sure we have a comment to delete
		if ($id) {
			// Make sure they're authorized to delete (must be an author)
			if ( $this->checkAuthorization() ) {
				$comment = new WikiPageComment( $this->database );
				$comment->delete( $id );

				$this->_message = JText::_('WIKI_COMMENT_DELETED');
			} else {
				$this->setError( JText::_('WIKI_ERROR_NOTAUTH') );
			}
		}

		$this->comments();
	}

	/**
	 * Short description for 'reportcomment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function reportcomment()
	{
		$id = JRequest::getInt( 'id', 0, 'request' );

		// Make sure we have a comment to report
		if ($id) {
			$comment = new WikiPageComment( $this->database );
			$comment->report( $id );

			$this->_message = JText::sprintf('WIKI_COMMENT_REPORTED',$id);
		}

		$this->comments();
	}

	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	/**
	 * Short description for 'upload'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function upload()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) {
			$this->media();
			return;
		}

		// Ensure we have an ID to work with
		$listdir = JRequest::getInt( 'listdir', 0, 'post' );
		if (!$listdir) {
			$this->setError( JText::_('WIKI_NO_ID') );
			$this->media();
			return;
		}

		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('WIKI_NO_FILE') );
			$this->media();
			return;
		}

		// Build the upload path if it doesn't exist
		$path = JPATH_ROOT;
		if (substr($this->config->get('filepath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $this->config->get('filepath').DS.$listdir;

		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('Error uploading. Unable to create path.') );
				$this->media();
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = urldecode($file['name']);
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		// Upload new files
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
		} else {
			// File was uploaded

			// Create database entry
			$description = trim(JRequest::getVar( 'description', '', 'post' ));
			$description = htmlspecialchars($description);

			$bits = array('id'=>'','pageid'=>$listdir,'filename'=>$file['name'],'description'=>$description,'created'=>date( 'Y-m-d H:i:s', time() ),'created_by'=>$this->juser->get('id'));
			$attachment = new WikiPageAttachment( $this->database );
			$attachment->bind( $bits );
			if (!$attachment->check()) {
				$this->setError( $attachment->getError() );
			}
			if (!$attachment->store()) {
				$this->setError( $attachment->getError() );
			}
		}

		// Push through to the media view
		$this->media();
	}

	/**
	 * Short description for 'delete_folder'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function delete_folder()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) {
			$this->media();
			return;
		}

		// Incoming group ID
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );
		if (!$listdir) {
			$this->setError( JText::_('WIKI_NO_ID') );
			$this->media();
			return;
		}

		// Incoming folder
		$folder = trim(JRequest::getVar( 'folder', '', 'get' ));
		if (!$folder) {
			$this->setError( JText::_('WIKI_NO_DIRECTORY') );
			$this->media();
			return;
		}

		// Build the file path
		$path = JPATH_ROOT;
		if (substr($this->config->get('filepath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $this->config->get('filepath').DS.$listdir.DS.$folder;

		// Delete the folder
		if (is_dir($path)) {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($path)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
			}
		} else {
			$this->setError( JText::_('WIKI_NO_DIRECTORY') );
		}

		// Push through to the media view
		$this->media();
	}

	/**
	 * Short description for 'delete_file'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function delete_file()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) {
			$this->media();
			return;
		}

		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );
		if (!$listdir) {
			$this->setError( JText::_('WIKI_NO_ID') );
			$this->media();
			return;
		}

		// Incoming file
		$file = trim(JRequest::getVar( 'file', '', 'get' ));
		if (!$file) {
			$this->setError( JText::_('WIKI_NO_FILE') );
			$this->media();
			return;
		}

		// Build the file path
		$path = JPATH_ROOT;
		if (substr($this->config->get('filepath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $this->config->get('filepath').DS.$listdir;

		// Delete the file
		if (!file_exists($path.DS.$file) or !$file) {
			$this->setError( JText::_('FILE_NOT_FOUND') );
			$this->media();
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
			} else {
				// Delete the database entry for the file
				$attachment = new WikiPageAttachment( $this->database );
				$attachment->deleteFile( $file, $listdir );
			}
		}

		// Push through to the media view
		$this->media();
	}

	/**
	 * Short description for 'media'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function media()
	{
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'request' );

		// Output HTML
		$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'edit', 'layout'=>'filebrowser') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->config = $this->config;
		$view->listdir = $listdir;
		$view->name = $this->_name;
		$view->sub = $this->_sub;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'recursive_listdir'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $base Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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

	/**
	 * Short description for 'list_files'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function list_files()
	{
		// Incoming
		$listdir = JRequest::getInt( 'listdir', 0, 'get' );

		if (!$listdir) {
			$this->setError( JText::_('WIKI_NO_ID') );
		}

		$path = JPATH_ROOT;
		if (substr($this->config->get('filepath'), 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $this->config->get('filepath').DS.$listdir;

		// Get the directory we'll be reading out of
		$d = @dir($path);

		$images  = array();
		$folders = array();
		$docs    = array();

		if ($d) {
			// Loop through all files and separate them into arrays of images, folders, and other
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|jpeg|jpe|tif|tiff|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if (is_dir($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();

			ksort($images);
			ksort($folders);
			ksort($docs);
		}

		$view = new JView( array('base_path'=>$this->_base_path, 'name'=>'edit', 'layout'=>'filelist') );
		$view->option = $this->_option;
		$view->docs = $docs;
		$view->folders = $folders;
		$view->images = $images;
		$view->config = $this->config;
		$view->listdir = $listdir;
		$view->name = $this->_name;
		$view->sub = $this->_sub;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	/**
	 * Short description for 'make_path'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $path Parameter description (if any) ...
	 * @param      integer $mode Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'normalize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $txt Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function normalize( $txt )
	{
		//$valid_chars = '\-\:a-zA-Z0-9';
		return preg_replace("/[^\:a-zA-Z0-9]/", "", $txt);
	}

	/**
	 * Short description for 'getPage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
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
				$this->pagename = $this->config->get('homepage');

			// Load the page
			$page = new WikiPage( $this->database );
			$page->load( $this->pagename, $this->scope );
			if (!$page->exist() && (substr(strtolower($this->pagename), 0, 4) == 'help')) {
				$page->load( $this->pagename, '' );
				$page->scope = $this->scope;
			}

			$this->page = $page;
		}
	}

	/**
	 * Short description for 'checkAuthorization'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $type Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function checkAuthorization( $type='edit' )
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) {
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage')) {
			return 'admin';
		}

		if ($this->page->group != '') {
			ximport('Hubzero_Group');
			$group = Hubzero_Group::getInstance( $this->page->group );
			if ($group->is_member_of('managers',$this->juser->get('id'))) {
				return 'manager';
			}
		}

		$params = new JParameter( $this->page->params );
		if ($params->get( 'mode' ) == 'knol') {
			if ($type == 'edit') {
				// Knol mode - only authorized authors can make pre-approved changes
				// Check if they're the page creator
				if ($this->page->created_by == $this->juser->get('id')) {
					return true;
				}

				// Check if they're in the approved author list
				if ($this->page->isAuthor($this->juser->get('id'))) {
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
						$usergroups = Hubzero_User_Helper::getGroups($this->juser->get('id'));
						if ($usergroups && count($usergroups) > 0) {
							foreach ($usergroups as $ug)
							{
								if ($ug->cn == $this->page->group && $ug->registered && $ug->regconfirmed) {
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
						$usergroups = Hubzero_User_Helper::getGroups( $this->juser->get('id') );
						if ($usergroups && count($usergroups) > 0) {
							foreach ($usergroups as $ug)
							{
								if ($ug->cn == $this->page->group && $ug->registered && $ug->regconfirmed) {
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

	/**
	 * Short description for 'splitPagename'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $page Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
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
					$RE[] = "/(?<= |${sep}|^)([�])([[:upper:]][[:lower:]])/";
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

	/**
	 * Short description for 'pcre_fix_posix_classes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $regexp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'download'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function download()
	{
		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		// Ensure we have a database object
		if (!$this->database) {
			JError::raiseError( 500, JText::_('COM_WIKI_DATABASE_NOT_FOUND') );
			return;
		}

		// Instantiate an attachment object
		$attachment = new WikiPageAttachment( $this->database );
		if (substr(strtolower($this->pagename), 0, 5) == 'image') {
			$attachment->filename .= substr($this->pagename, 6);
		} else if (substr(strtolower($this->pagename), 0, 4) == 'file') {
			$attachment->filename .= substr($this->pagename, 5);
		}
		$attachment->filename = urldecode($attachment->filename);

		// Get the scope of the parent page the file is attached to
		if (!$this->scope) {
			$this->scope = trim(JRequest::getVar( 'scope', '' ));
		}
		$segments = explode(DS,$this->scope);
		$pagename = array_pop($segments);
		$scope = implode(DS,$segments);

		// Get the parent page the file is attached to
		$this->page = new WikiPage( $this->database );
		$this->page->load( $pagename, $scope );

		// Load the page
		if (!$this->page->id) {
			JError::raiseError( 404, JText::_('COM_WIKI_PAGE_NOT_FOUND') );
			return;
		}

		// Check authorization
		$authorized = $this->checkAuthorization('view');

		// Check if the page is group restricted and the user is authorized
		if ($this->page->group != '' && $this->page->access != 0 && !$authorized) {
			if ($this->_sub) {
				echo WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH') );
			} else {
				JError::raiseWarning( 403, JText::_('WIKI_WARNING_NOT_AUTH') );
			}
			return;
		}

		// Ensure we have a path
		if (empty($attachment->filename)) {
			JError::raiseError( 404, JText::_('COM_WIKI_FILE_NOT_FOUND') );
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $attachment->filename)) {
			JError::raiseError( 404, JText::_('COM_WIKI_BAD_FILE_PATH') );
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $attachment->filename)) {
			JError::raiseError( 404, JText::_('COM_WIKI_BAD_FILE_PATH') );
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $attachment->filename)) {
			JError::raiseError( 404, JText::_('COM_WIKI_BAD_FILE_PATH') );
			return;
		}
		// Disallow \
		if (strpos('\\',$attachment->filename)) {
			JError::raiseError( 404, JText::_('COM_WIKI_BAD_FILE_PATH') );
			return;
		}
		// Disallow ..
		if (strpos('..',$attachment->filename)) {
			JError::raiseError( 404, JText::_('COM_WIKI_BAD_FILE_PATH') );
			return;
		}

		// Get the configured upload path
		//$base_path = '/site/wiki/'.$this->page->id;
		$base_path = $this->config->get('filepath');
		if ($base_path) {
			// Make sure the path doesn't end with a slash
			if (substr($base_path, -1) == DS) {
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($base_path, 0, 1) != DS) {
				$base_path = DS.$base_path;
			}
		}
		$base_path .= DS.$this->page->id;

		// Does the path start with a slash?
		if (substr($attachment->filename, 0, 1) != DS) {
			$attachment->filename = DS.$attachment->filename;
			// Does the beginning of the $attachment->path match the config path?
			if (substr($attachment->filename, 0, strlen($base_path)) == $base_path) {
				// Yes - this means the full path got saved at some point
			} else {
				// No - append it
				$attachment->filename = $base_path.$attachment->filename;
			}
		}

		// Add JPATH_ROOT
		$filename = JPATH_ROOT.$attachment->filename;

		// Ensure the file exist
		if (!file_exists($filename)) {
			JError::raiseError( 404, JText::_('COM_WIKI_FILE_NOT_FOUND').' '.$filename );
			return;
		}
		//echo $filename; //http://dev.nanohub.org/topics/aTCADLab/Image:tcad_large_600pix.gif
		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve()) {
			// Should only get here on error
			JError::raiseError( 404, JText::_('COM_WIKI_SERVER_ERROR') );
		} else {
			exit;
		}
		return;
	}
}

