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
		$this->_longname = JText::_('COMPONENT_LONG_NAME');

		$this->_task = strtolower(JRequest::getVar( 'task', 'browse' ));
		
		switch ( $this->_task ) 
		{
			case 'category': $this->category(); break;
			case 'browse':   $this->browse();   break;
			case 'article':  $this->article();  break;

			default: $this->browse(); break;
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function browse() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'browse') );
		
		// Get all main categories for menu
		$c = new KbCategory( $this->database );
		$view->categories = $c->getCategories( 1, 1 );

		// Get the lists of popular and most recent articles
		$a = new KbArticle( $this->database );
		$view->articles = array();
		$view->articles['top'] = $a->getArticles(5, 'a.hits DESC');
		$view->articles['new'] = $a->getArticles(5, 'a.created DESC');

		// Add the CSS to the template
		$this->_getStyles();

		// Set the pathway
		$this->_buildPathway(null, null, null);

		// Set the page title
		$this->_buildTitle(null, null, null);

		// Output HTML
		$view->option = $this->_option;
		$view->title = $this->_longname;
		$view->catid = 0;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
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
		
		// Instantiate a new view
		$view = new JView( array('name'=>'category') );

		// Get the category
		$view->category = new KbCategory( $this->database );
		$view->category->loadAlias( $alias );
		
		$view->section = new KbCategory( $this->database );
		if ($view->category->section) {
			$view->section->load( $view->category->section );
			
			$sect = $view->category->section;
			$cat  = $view->category->id;
		} else {
			$sect = $view->category->id;
			$cat  = 0;
		}

		// Get the list of articles for this category
		$kba = new KbArticle( $this->database );
		$view->articles = $kba->getCategoryArticles( 1, $sect, $cat, $view->category->access );
		
		// Get all main categories for menu
		$view->categories = $view->category->getCategories( 1, 1 );
		
		// Get sub categories
		$view->subcategories = $view->category->getCategories( 1, 1, $view->category->id );
		
		// Add the CSS to the template
		$this->_getStyles();
		
		// Set the pathway
		$this->_buildPathway($view->section, $view->category, null);
		
		// Set the page title
		$this->_buildTitle($view->section, $view->category, null);
		
		// Output HTML
		$view->option = $this->_option;
		$view->title = $this->_longname;
		$view->catid = $sect;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function article()
	{
		// Incoming
		$alias = JRequest::getVar( 'alias', '' );
		$id = JRequest::getVar( 'id', '' );
		
		$this->helpful = '';
		
		// Instantiate a new view
		$view = new JView( array('name'=>'article') );
		
		// Load the article
		$view->article = new KbArticle( $this->database );

		if (!empty($alias)) {
			$view->article->loadAlias( $alias );
		} else if (!empty($id)) {
			$view->article->load( $id );
		}
	
		if (!$view->article->id) {
			JError::raiseError( 404, JText::_('Article not found.') );
			return;
		}
		
		$view->article->hit( $view->article->id );

		// Is the user logged in?
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			// Did they click the helpful link?
			$this->_helpful( $view->article->id );
		}
		
		// Load the category object
		$view->section = new KbCategory( $this->database );
		$view->section->load( $view->article->section );
		
		// Load the category object
		$view->category = new KbCategory( $this->database );
		if ($view->article->category) {
			$view->category->load( $view->article->category );
		}
		
		// Get all main categories for menu
		$view->categories = $view->category->getCategories( 1, 1 );
		
		// Add the CSS to the template
		$this->_getStyles();
		
		// Set the pathway
		$this->_buildPathway($view->section, $view->category, $view->article);
		
		// Set the page title
		$this->_buildTitle($view->section, $view->category, $view->article);
		
		// Output HTML
		$view->option = $this->_option;
		$view->title = $this->_longname;
		$view->juser = $juser;
		$view->helpful = $this->helpful;
		$view->catid = $view->section->id;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Private Functions
	//----------------------------------------------------------

	protected function _buildPathway($section=null, $category=null, $article=null) 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_('COMPONENT_LONG_NAME'),
				'index.php?option='.$this->_option
			);
		}
		if (is_object($section) && $section->alias) {
			$pathway->addItem(
				stripslashes($section->title),
				'index.php?option='.$this->_option.'&section='.$section->alias
			);
		}
		if (is_object($category) && $category->alias) {
			$lnk  = 'index.php?option='.$this->_option;
			$lnk .= (is_object($section) && $section->alias) ? '&section='.$section->alias : '';
			$lnk .= '&category='.$category->alias;
			
			$pathway->addItem(
				stripslashes($category->title),
				$lnk
			);
		}
		if (is_object($article) && $article->alias) {
			$lnk = 'index.php?option='.$this->_option.'&section='.$section->alias;
			if (is_object($category) && $category->alias) {
				$lnk .= '&category='.$category->alias;
			}
			$lnk .= '&alias='.$article->alias;
			
			$pathway->addItem(
				stripslashes($article->title),
				$lnk
			);
		}
	}
	
	//-----------
	
	protected function _buildTitle($section=null, $category=null, $article=null) 
	{
		$title = JText::_('COMPONENT_LONG_NAME');
		if (is_object($section) && $section->title != '') {
			$title .= ': '.stripslashes($section->title);
		}
		if (is_object($category) && $category->title != '') {
			$title .= ': '.stripslashes($category->title);
		}
		if (is_object($article)) {
			$title .= ': '.stripslashes($article->title);
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
	}

	//-----------

	private function _helpful( $id )
	{	
		// Get the user's IP address
		$ip = $this->_ipAddress();
	
		// See if a person from this IP has already voted
		$h = new KbHelpful( $this->database );
		$result = $h->getHelpful( $id, $ip );
		
		if ($result) {
			// Already voted
			$this->helpful = ($result == 'yes') ? JText::_('HELPFUL') : JText::_('NOT_HELPFUL');
			$this->setError( JText::_('USER_ALREADY_VOTED') );
			return;
		}
	
		// Incoming
		$helpful = strtolower(JRequest::getVar( 'helpful', '' ));
	
		// Did they vote?
		if ($helpful && ($helpful == 'yes' || $helpful == 'no')) {
			// Load the resource
			$row = new KbArticle( $this->database );
			$row->load( $id );

			// Record if it was helpful or not
			if ($helpful == 'yes'){
				$row->helpful++;
			} elseif($helpful == 'no') {
				$row->nothelpful++;
			}

			if (!$row->store()) {
				$this->setError( $row->getError() );
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
				$this->setError( $h->getError() );
				return;
			}
			if (!$h->store()) {
				$this->setError( $h->getError() );
				return;
			}

			$this->helpful = ($helpful == 'yes') ? JText::_('HELPFUL') : JText::_('NOT_HELPFUL');
		}
	}
	
	//-----------

	private function _server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return $_SERVER[$index];
	}
	
	//-----------
	
	private function _validIp($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? false : true;
	}
	
	//-----------

	private function _ipAddress()
	{
		if ($this->_server('REMOTE_ADDR') AND $this->_server('HTTP_CLIENT_IP')) {
			 $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->_server('REMOTE_ADDR')) {
			 $ip_address = $_SERVER['REMOTE_ADDR'];
		} elseif ($this->_server('HTTP_CLIENT_IP')) {
			 $ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->_server('HTTP_X_FORWARDED_FOR')) {
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
		
		if (!$this->_validIp($ip_address)) {
			$ip_address = '0.0.0.0';
		}
				
		return $ip_address;
	}
}
