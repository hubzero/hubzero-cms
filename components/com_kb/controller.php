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
		
		$this->_longname = JText::_('COMPONENT_LONG_NAME');
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
	
	public function execute()
	{
		$default = 'browse';
		
		$task = strtolower(JRequest::getVar( 'task', $default ));
		
		$thisMethods = $this->get_protected_methods( get_class( $this ) );
		if (!in_array($task, $thisMethods)) {
			$task = $default;
			if (!in_array($task, $thisMethods)) {
				return JError::raiseError( 404, JText::_('Task ['.$task.'] not found') );
			}
		}
		
		$this->_task = $task;
		$this->$task();
	}
	
	//-----------
	
	private function get_protected_methods($className) 
	{
		$returnArray = array();

		// Iterate through each method in the class
		foreach (get_class_methods($className) as $method) 
		{
			// Get a reflection object for the class method
			$reflect = new ReflectionMethod($className, $method);
			
			if ($reflect->isProtected()) {
				// The method is one we're looking for, push it onto the return array
				array_push($returnArray,$method);
			}
		}
		
		return $returnArray;
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}

	//-----------
	
	private function getStyles()
	{
	    // add the CSS to the template and set the page title
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function browse() 
	{
		$database =& JFactory::getDBO();
		
		// Add the CSS to the template
		$this->getStyles();
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_longname );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( $this->_longname,'index.php?option='.$this->_option );
		}
		
		// Get all main categories for menu
		$noauth = 1; //=! $mainframe->getCfg('shownoauth');
		$c = new KbCategory( $database );
		$categories = $c->getCategories( $noauth, 1 );

		// Get the lists of popular and most recent articles
		$a = new KbArticle( $database );
		$articles = array();
		$articles['top'] = $a->getArticles(5, 'a.hits DESC');
		$articles['new'] = $a->getArticles(5, 'a.created DESC');

		// Output HTML
		echo KbHtml::browse($categories, $articles, $this->_option, $this->_longname);
	}

	//-----------

	protected function category()
	{
		// Incoming
		$alias = JRequest::getVar( 'alias', '' );

		// Make sure we have an ID
		if (!$alias) {
			$this->browse();
			return;
		}

		$database =& JFactory::getDBO();

		// Get the category
		$category = new KbCategory( $database );
		$category->loadAlias( $alias );
		
		$section = new KbCategory( $database );
		if ($category->section) {
			$section->load( $category->section );
			
			$sect = $category->section;
			$cat  = $category->id;
			
			$title = $this->_longname.': '.stripslashes($section->title).': '.stripslashes($category->title);
			$url = 'index.php?option='.$this->_option.a.'section='.$section->alias.a.'category='.$category->alias;
		} else {
			$sect = $category->id;
			$cat  = 0;
			
			$title = $this->_longname.': '.stripslashes($category->title);
			$url = 'index.php?option='.$this->_option.a.'section='. $category->alias;
		}
		
		// Add the CSS to the template
		$this->getStyles();
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );

		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( $this->_longname,'index.php?option='.$this->_option );
		}
		if ($category->section) {
			$pathway->addItem( stripslashes($section->title),'index.php?option='.$this->_option.a.'section='. $section->alias );
		}
		$pathway->addItem( stripslashes($category->title), $url );

		// Get the list of articles for this category
		$noauth = 1; //=! $mainframe->getCfg('shownoauth');
		$kba = new KbArticle( $database );
		$articles = $kba->getCategoryArticles( $noauth, $sect, $cat, $category->access );
		
		// Get all main categories for menu
		$categories = $category->getCategories( $noauth, 1 );
		
		// Get sub categories
		$subcategories = $category->getCategories( $noauth, 1, $category->id );
		
		// Output HTML
		echo KbHtml::category( $section, $category, $categories, $subcategories, $articles, $category->id, $this->_option, $this->_longname );
	}

	//-----------

	protected function article()
	{
		// Incoming
		$alias = JRequest::getVar( 'alias', '' );
		$id = JRequest::getVar( 'id', '' );
		
		$this->helpful = '';

		$database =& JFactory::getDBO();
		
		// Load the article
		$content = new KbArticle( $database );

		if (!empty($alias))
			$content->loadAlias( $alias );
		else if (!empty($id))
			$content->load( $id );

		if (!$content->id) {
			echo KbHtml::error( JText::_('Article not found.') );
			return;
		}
		
		$content->hit( $content->id );

		// Is the user logged in?
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			// Did they click the helpful link?
			$this->helpful( $content->id );
		}
		
		// Load the category object
		$section = new KbCategory( $database );
		$section->load( $content->section );
		
		// Load the category object
		$category = new KbCategory( $database );
		if ($content->category) {
			$category->load( $content->category );
		}
		
		// Add the CSS to the template
		$this->getStyles();
		
		$title  = $this->_longname;
		$title .= ': '.stripslashes($section->title);
		$title .= ($category->title) ? ': '.stripslashes($category->title) : '';
		$title .= ': '.stripslashes($content->title);
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem( $this->_longname,'index.php?option='.$this->_option );
		}
		$pathway->addItem( stripslashes($section->title),'index.php?option='.$this->_option.a.'section='. $section->alias );
		
		// Get all main categories for menu
		$noauth = 1; //=! $mainframe->getCfg('shownoauth');
		$categories = $category->getCategories( $noauth, 1 );
		
		// Load item's parent category
		// this will only happen if their parent is a sub-category
		if ($content->category) {
			$pathway->addItem( stripslashes($category->title),'index.php?option='.$this->_option.a.'section='. $section->alias.a.'category='. $category->alias );
		}

		$pathway->addItem( stripslashes($content->title),'index.php?option='.$this->_option.a.'section='. $section->alias.a.'category='. $category->alias.a.'article='. $content->alias );
		
		// Output HTML
		echo KbHtml::article( $content, $section, $category, $categories, $content->id, $this->_option, $juser, $this->helpful, $this->_longname );
	}

	//----------------------------------------------------------
	// Misc Functions
	//----------------------------------------------------------

	private function helpful( $id )
	{	
		// Get the user's IP address
		$ip = $this->ipAddress();
	
		$database =& JFactory::getDBO();
		
		// See if a person from this IP has already voted
		$h = new KbHelpful( $database );
		$result = $h->getHelpful( $id, $ip );
		
		if ($result) {
			// Already voted
			$this->helpful = ($result == 'yes') ? JText::_('HELPFUL') : JText::_('NOT_HELPFUL');
			$this->_error = JText::_('USER_ALREADY_VOTED');
			return;
		}
	
		// Incoming
		$helpful = strtolower(JRequest::getVar( 'helpful', '' ));
	
		// Did they vote?
		if ($helpful && ($helpful == 'yes' || $helpful == 'no')) {
			// Load the resource
			$row = new KbArticle( $database );
			$row->load( $id );

			// Record if it was helpful or not
			if ($helpful == 'yes'){
				$row->helpful++;
			} elseif($helpful == 'no') {
				$row->nothelpful++;
			}

			if (!$row->store()) {
				$this->_error = $row->getError();
				return;
			}

			// Record user's vote
			$params = array();
			$params['id'] = NULL;
			$params['fid'] = $row->id;
			$params['ip'] = $ip;
			$params['helpful'] = $helpful;

			$h->bind( $params );
			if (!$h->check()) {
				$this->_error = $h->getError();
				return;
			}
			if (!$h->store()) {
				$this->_error = $h->getError();
				return;
			}

			$this->helpful = ($helpful == 'yes') ? JText::_('HELPFUL') : JText::_('NOT_HELPFUL');
		}
	}
	
	//-----------

	private function server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return $_SERVER[$index];
	}
	
	//-----------
	
	private function validIp($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? false : true;
	}
	
	//-----------

	private function ipAddress()
	{
		if ($this->server('REMOTE_ADDR') AND $this->server('HTTP_CLIENT_IP')) {
			 $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->server('REMOTE_ADDR')) {
			 $ip_address = $_SERVER['REMOTE_ADDR'];
		} elseif ($this->server('HTTP_CLIENT_IP')) {
			 $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->server('HTTP_X_FORWARDED_FOR')) {
			 $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		if ($ip_address === false) {
			$ip_address = '0.0.0.0';
			return $ip_address;
		}
		
		if (strstr($ip_address, ',')) {
			$x = explode(',', $ip_address);
			$ip_address = end($x);
		}
		
		if (!$this->validIp($ip_address)) {
			$ip_address = '0.0.0.0';
		}
				
		return $ip_address;
	}
}
?>
