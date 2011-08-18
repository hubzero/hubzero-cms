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

class KbController extends Hubzero_Controller
{
	public function execute()
	{
		$this->_longname = JText::_('COMPONENT_LONG_NAME');

		$this->_task = strtolower(JRequest::getVar( 'task', 'browse' ));

		switch ( $this->_task ) 
		{
			case 'login':    $this->login();    break;
			
			// Feeds
			//case 'feed.rss': $this->_feed();   break;
			//case 'feed':     $this->_feed();   break;
			case 'comments.rss': $this->commentsfeed();   break;
			case 'comments':     $this->commentsfeed();   break;
			
			// Comments
			case 'savecomment':   $this->savecomment();   break;
			case 'deletecomment': $this->deletecomment(); break;
			case 'vote':   	$this->vote();    break;
			//case 'savereply':   $this->savereply();   break;
			//case 'reply':      	$this->reply();  	  break;
			
			// Articles
			case 'category': $this->category(); break;
			case 'browse':   $this->browse();   break;
			case 'article':  $this->article();  break;

			default: $this->browse(); break;
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function login() 
	{
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Instantiate a view
		$view = new JView( array('name'=>'login') );
		$view->title = JText::_(strtoupper($this->_option)).': '.JText::_('COM_KB_LOGIN');
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function browse() 
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'browse') );
		
		$view->filters = array();
		$view->filters['limit'] = JRequest::getInt('limit', 25);
		$view->filters['start'] = JRequest::getInt('limitstart', 0);
		$view->filters['order'] = JRequest::getVar('order', 'recent');
		$view->filters['section'] = 'all';
		$view->filters['category'] = 0;
		$view->filters['search'] = JRequest::getVar('search','');
		
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
		
		// Add any JS to the template
		$this->_getScripts();

		// Set the pathway
		$this->_buildPathway(null, null, null);

		// Set the page title
		$this->_buildTitle(null, null, null);

		// Output HTML
		$view->database = $this->database;
		$view->option = $this->_option;
		$view->title = $this->_longname;
		$view->catid = 0;
		$view->config = $this->config;
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

		//$view->vote = strtolower(JRequest::getVar( 'vote', '' ));
		//$view->vote_type = JRequest::getVar( 'type', '' );
		//$view->vote_id = JRequest::getInt( 'id', 0 );

		// Get the category
		$view->category = new KbCategory( $this->database );
		$view->section = new KbCategory( $this->database );
		if ($alias == 'all') {
			$view->category->alias = 'all';
			$view->category->title = 'All Articles';
			$view->category->id = 0;
			
			$sect = 0;
			$cat  = 0;
		} else {
			$view->category->loadAlias( $alias );
			if ($view->category->section) {
				$view->section->load( $view->category->section );

				$sect = $view->category->section;
				$cat  = $view->category->id;
			} else {
				$sect = $view->category->id;
				$cat  = 0;
			}
		}

		$view->filters = array();
		$view->filters['limit'] = JRequest::getInt('limit', 25);
		$view->filters['start'] = JRequest::getInt('limitstart', 0);
		$view->filters['order'] = JRequest::getVar('order', 'recent');
		$view->filters['section'] = $sect;
		$view->filters['category'] = $cat;
		$view->filters['search'] = JRequest::getVar('search','');
		if (!$this->juser->get('guest')) {
			$view->filters['user_id'] = $this->juser->get('id');
		}

		// Get the list of articles for this category
		$kba = new KbArticle( $this->database );
		
		// Get a record count
		$view->total = $kba->getCount($view->filters);
		
		// Get the records
		$view->articles = $kba->getRecords($view->filters);
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );
		
		// Get all main categories for menu
		$view->categories = $view->category->getCategories( 1, 1 );
		
		// Get sub categories
		$view->subcategories = $view->category->getCategories( 1, 1, $sect );
		
		// Add the CSS to the template
		$this->_getStyles();
		
		// Add any JS to the template
		$this->_getScripts();
		
		// Set the pathway
		$this->_buildPathway($view->section, $view->category, null);
		
		// Set the page title
		$this->_buildTitle($view->section, $view->category, null);
		
		// Output HTML
		$view->option = $this->_option;
		$view->title = $this->_longname;
		$view->catid = $sect;
		$view->config = $this->config;
		$view->juser = $this->juser;
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
		
		//$this->helpful = '';
		
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
		if (!$this->juser->get('guest')) {
			// See if this person has already voted
			$h = new KbVote( $this->database );
			$view->vote = $h->getVote( $view->article->id, $this->juser->get('id'), $this->_ipAddress() );
		} else {
			$view->vote = strtolower(JRequest::getVar( 'vote', '' ));
		}
		//$view->vote_type = JRequest::getVar( 'type', '' );
		//$view->vote_id = JRequest::getInt( 'id', 0 );
		
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
		
		$view->subcategories = $view->category->getCategories( 1, 1, $view->section->id );
		
		// Get tags on this article
		$kt = new KbTags( $this->database );
		$view->tags = $kt->get_tags_on_object($view->article->id, 0, 0, 0);
		
		// Get comments on this article
		$bc = new KbComment( $this->database );
		$view->comments = $bc->getAllComments($view->article->id);
		
		$view->comment_total = 0;
		if ($view->comments) {
			foreach ($view->comments as $com) 
			{
				$view->comment_total++;
				if ($com->replies) {
					foreach ($com->replies as $rep) 
					{
						$view->comment_total++;
						if ($rep->replies) {
							$view->comment_total = $view->comment_total + count($rep->replies);
						}
					}
				}
			}
		}
		
		$r = JRequest::getInt( 'reply', 0 );
		$view->replyto = new KbComment($this->database);
		$view->replyto->load($r);
		
		// Get parameters and merge with the component params
		$rparams = new JParameter( $view->article->params );
		$view->config = $this->config;
		$view->config->merge( $rparams );
		
		// Add the CSS to the template
		$this->_getStyles();
		
		// Add any JS to the template
		$this->_getScripts();
		
		// Set the pathway
		$this->_buildPathway($view->section, $view->category, $view->article);
		
		// Set the page title
		$this->_buildTitle($view->section, $view->category, $view->article);

		// Output HTML
		$view->option = $this->_option;
		$view->title = $this->_longname;
		$view->juser = $this->juser;
		$view->helpful = $this->helpful;
		$view->catid = $view->section->id;
		//$view->config = $this->config;
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

	protected function vote()
	{
		if ($this->juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Incoming
		$no_html = JRequest::getInt( 'no_html', 0 );
		$type = JRequest::getVar( 'type', '' );
		$id = JRequest::getVar( 'id', '' );
		$vote = strtolower(JRequest::getVar( 'vote', '' ));
		
		$ip = $this->_ipAddress();
		
		// See if this person has already voted
		$h = new KbVote( $this->database );
		$result = $h->getVote( $id, $this->juser->get('id'), $ip, $type );
		
		if ($result) {
			// Already voted
			$this->setError( JText::_('COM_KB_USER_ALREADY_VOTED') );
			$this->article();
			return;
		}
	
		// Did they vote?
		if (!$vote) {
			// Already voted
			$this->setError( JText::_('COM_KB_USER_DIDNT_VOTE') );
			$this->article();
			return;
		}
		
		// Load the article
		switch ($type) 
		{
			case 'entry':
				$row = new KbArticle( $this->database );
			break;
			case 'comment':
				$row = new KbComment( $this->database );
			break;
		}
		$row->load( $id );

		// Record if it was helpful or not
		switch ($vote)
		{
			case 'yes':
			case 'positive':
			case 'like':
				$row->helpful++;
			break;
			
			case 'no':
			case 'negative':
			case 'dislike':
				$row->nothelpful++;
			break;
		}

		if (!$row->store()) {
			$this->setError( $row->getError() );
			return;
		}

		// Record user's vote
		$params = array();
		$params['id'] = NULL;
		$params['object_id'] = $row->id;
		$params['ip'] = $ip;
		$params['vote'] = $vote;
		$params['user_id'] = $this->juser->get('id');
		$params['type'] = $type;

		$h->bind( $params );
		if (!$h->check()) {
			$this->setError( $h->getError() );
			return;
		}
		if (!$h->store()) {
			$this->setError( $h->getError() );
			return;
		}

		if ($no_html) {
			$view = new JView( array('name'=>'vote') );
			$view->option = $this->_option;
			$view->item = $row;
			$view->type = $type;
			$view->vote = $vote;
			$view->id = $id;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		} else {
			if ($type == 'entry') {
				JRequest::setVar('alias', $row->alias);
			}
			$this->article();
		}
	}
	
	//-----------

	private function _server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return TRUE;
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
			 $ip_address = JRequest::getVar('HTTP_CLIENT_IP','','server');
		} elseif ($this->_server('REMOTE_ADDR')) {
			 $ip_address = JRequest::getVar('REMOTE_ADDR','','server');
		} elseif ($this->_server('HTTP_CLIENT_IP')) {
			 $ip_address = JRequest::getVar('HTTP_CLIENT_IP','','server');
		} elseif ($this->_server('HTTP_X_FORWARDED_FOR')) {
			 $ip_address = JRequest::getVar('HTTP_X_FORWARDED_FOR','','server');
		}
		
		if ($ip_address === FALSE) {
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
	
	//----------------------------------------------------------
	// Comments
	//----------------------------------------------------------
	
	protected function savecomment() 
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_KB_LOGIN_NOTICE') );
			return $this->login();
		}
		
		// Incoming
		$comment = JRequest::getVar( 'comment', array(), 'post' );
		
		// Instantiate a new comment object and pass it the data
		$row = new KbComment( $this->database );
		if (!$row->bind( $comment )) {
			$this->setError( $row->getError() );
			return $this->article();
		}
		
		// Set the created time
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );  // use gmdate() ?
		}
		
		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			return $this->article();
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return $this->article();
		}
		
		return $this->article();
	}
	
	//-----------
	
	protected function deletecomment() 
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_KB_LOGIN_NOTICE') );
			return;
		}
		
		// Incoming
		$id = JRequest::getInt( 'comment', 0 );
		if (!$id) {
			return $this->article();
		}
		
		// Initiate a blog comment object
		$comment = new KbComment( $this->database );
		$comment->load( $id );
		
		if ($this->juser->get('id') != $comment->created_by && !$this->authorized) {
			return $this->article();
		}
		
		// Delete all comments on an entry
		if (!$comment->deleteChildren( $id )) {
			$this->setError( $comment->getError() );
			return $this->article();
		}

		// Delete the entry itself
		if (!$comment->delete( $id )) {
			$this->setError( $comment->getError() );
		}
		
		// Return the topics list
		return $this->article();
	}
	
	//-----------
	
	protected function commentsfeed() 
	{
		if (!$this->config->get('feeds_enabled')) {
			$this->_task = 'article';
			$this->article();
			return;
		}
		
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'feed'.DS.'feed.php');
		
		//$mainframe =& $this->mainframe;
		$database =& JFactory::getDBO();
		
		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		//$params =& $mainframe->getParams();
		$app =& JFactory::getApplication();
		$params =& $app->getParams();
		$doc->link = JRoute::_('index.php?option='.$this->_option);

		// Incoming
		$alias = JRequest::getVar( 'alias', '' );
		
		if (!$alias) {
			return $this->browse();
		}

		$entry = new KbArticle($this->database);
		$entry->loadAlias($alias);
		
		if (!$entry->id) {
			$this->_task = 'browse';
			return $this->browse();
		}
		
		// Load the category object
		$section = new KbCategory( $this->database );
		$section->load( $entry->section );
		
		// Load the category object
		$category = new KbCategory( $this->database );
		if ($entry->category) {
			$category->load( $entry->category );
		}
		
		// Load the comments
		$bc = new KbComment($this->database);
		$rows = $bc->getAllComments($entry->id);
		
		//$year = JRequest::getInt( 'year', date("Y") );
		//$month = JRequest::getInt( 'month', 0 );

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename').' - '.JText::_(strtoupper($this->_option));
		//$doc->title .= ($year) ? ': '.$year : '';
		//$doc->title .= ($month) ? ': '.sprintf("%02d",$month) : '';
		$doc->title .= ($entry->title) ? ': '.stripslashes($entry->title) : '';
		$doc->title .= ': '.JText::_('Comments');
		
		$doc->description = JText::sprintf('COM_KB_COMMENTS_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'), stripslashes($entry->title));
		$doc->copyright = JText::sprintf('COM_KB_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		//$doc->category = JText::_('COM_BLOG_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) {
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => '',
				'pagename' => $entry->alias,
				'pageid'   => $entry->id,
				'filepath' => '',
				'domain'   => '' 
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();
			
			foreach ($rows as $row)
			{
				// URL link to article
				$link = JRoute::_('index.php?option='.$this->_option.'&section='.$section->alias.'&category='.$category->alias.'&alias='.$entry->alias.'#c'.$row->id);

				$author = JText::_('COM_KB_ANONYMOUS');
				if (!$row->anonymous) {
					$cuser =& JUser::getInstance($row->created_by);
					$author = $cuser->get('name');
				}
				
				// Prepare the title
				$title = JText::sprintf('Comment by %s', $author).' @ '.JHTML::_('date',$row->created, '%I:%M %p', 0).' on '.JHTML::_('date',$row->created, '%d %b, %Y', 0);
				
				// Strip html from feed item description text
				if ($row->reports) {
					$description = JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
				} else {
					$description = $p->parse($row->content, $wikiconfig);
				}
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
				/*if ($this->config->get('feed_entries') == 'partial') {
					$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
				}*/

				@$date = ( $row->created ? date( 'r', strtotime($row->created) ) : '' );
				
				// Load individual item creator class
				$item = new JFeedItem();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = '';
				$item->author      = $author;

				// Loads item info into rss array
				$doc->addItem( $item );
				
				// Check for any replies
				if ($row->replies) {
					foreach ($row->replies as $reply) 
					{
						// URL link to article
						$link = JRoute::_('index.php?option='.$this->_option.'&section='.$section->alias.'&category='.$category->alias.'&alias='.$entry->alias.'#c'.$reply->id);

						$author = JText::_('COM_KB_ANONYMOUS');
						if (!$reply->anonymous) {
							$cuser =& JUser::getInstance($reply->created_by);
							$author = $cuser->get('name');
						}

						// Prepare the title
						$title = JText::sprintf('Reply to comment #%s by %s', $row->id, $author).' @ '.JHTML::_('date',$reply->created, '%I:%M %p', 0).' on '.JHTML::_('date',$reply->created, '%d %b, %Y', 0);

						// Strip html from feed item description text
						if ($reply->reports) {
							$description = JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
						} else {
							$description = (is_object($p)) ? $p->parse( stripslashes($reply->content) ) : nl2br(stripslashes($reply->content));
						}
						$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
						/*if ($this->config->get('feed_entries') == 'partial') {
							$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
						}*/

						@$date = ( $reply->created ? date( 'r', strtotime($reply->created) ) : '' );

						// Load individual item creator class
						$item = new JFeedItem();
						$item->title       = $title;
						$item->link        = $link;
						$item->description = $description;
						$item->date        = $date;
						$item->category    = '';
						$item->author      = $author;

						// Loads item info into rss array
						$doc->addItem( $item );
						
						if ($reply->replies) {
							foreach ($reply->replies as $response) 
							{
								// URL link to article
								$link = JRoute::_('index.php?option='.$this->_option.'&section='.$section->alias.'&category='.$category->alias.'&alias='.$entry->alias.'#c'.$response->id);

								$author = JText::_('COM_KB_ANONYMOUS');
								if (!$response->anonymous) {
									$cuser =& JUser::getInstance($response->created_by);
									$author = $cuser->get('name');
								}

								// Prepare the title
								$title = JText::sprintf('Reply to comment #%s by %s', $reply->id, $author).' @ '.JHTML::_('date',$response->created, '%I:%M %p', 0).' on '.JHTML::_('date',$response->created, '%d %b, %Y', 0);

								// Strip html from feed item description text
								if ($response->reports) {
									$description = JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
								} else {
									$description = (is_object($p)) ? $p->parse( stripslashes($response->content) ) : nl2br(stripslashes($response->content));
								}
								$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
								/*if ($this->config->get('feed_entries') == 'partial') {
									$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
								}*/

								@$date = ( $response->created ? date( 'r', strtotime($response->created) ) : '' );

								// Load individual item creator class
								$item = new JFeedItem();
								$item->title       = $title;
								$item->link        = $link;
								$item->description = $description;
								$item->date        = $date;
								$item->category    = '';
								$item->author      = $author;
								
								// Loads item info into rss array
								$doc->addItem( $item );
							}
						}
					}
				}
			}
		}
		
		// Output the feed
		echo $doc->render();
	}
	
	//-----------
	
	/*protected function vote($database, $id)
	{
		$ip = $this->_ip_address();
		
		// Login required
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_ANSWERS_PLEASE_LOGIN_TO_VOTE') );
			$this->login();
			return;
		}
			
		// See if a person from this IP has already voted
		$al = new AnswersQuestionsLog( $database );
		$voted = $al->checkVote( $id, $ip );
	
		if ($voted) {	
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=8');
			return;
		}
				
		// load the resource
		$row = new AnswersQuestion( $database );
		$row->load( $id );
		$this->qid = $id;
		
		// check if user is rating his own question
		if ($row->created_by == $this->juser->get('username')) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=9');
			return;
		}
		
		// record vote
		$row->helpful++;
		
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return;
		}
		
		$expires = time() + (7 * 24 * 60 * 60); // in a week
		$expires = date( 'Y-m-d H:i:s', $expires );
		
		// Record user's vote
		$al->qid = $id;
		$al->ip = $ip;
		$al->voter = $this->juser->get('id');
		$al->expires = $expires;
		if (!$al->check()) {
			$this->setError( $al->getError() );
			return;
		}
		if (!$al->store()) {
			$this->setError( $al->getError() );
			return;
		}
	}
	//-----------
	
	protected function vote()
	{
		// Is the user logged in?
		if ($this->juser->get('guest')) {
			$this->login( JText::_('COM_KB_PLEASE_LOGIN_TO_VOTE') );
			return;
		}
		
		// Incoming
		$alias = JRequest::getInt( 'alias', 0 );
		$no_html = JRequest::getInt( 'no_html', 0 );
		$ip   = $this->_ip_address();
		
		if (!$id) {
			// cannot proceed		
			return;
		}
		
		// Incoming
		$vote = strtolower(JRequest::getVar( 'vote', '' ));
	
		// Did they vote?
		if ($vote && ($vote == 'like' || $vote == 'dislike')) {
			// Load the resource
			$row = new KbArticle( $this->database );
			$row->load( $id );

			// Record if it was helpful or not
			if ($vote == 'like'){
				$row->helpful++;
			} elseif ($vote == 'dislike') {
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
			$params['helpful'] = $vote;

			$h->bind( $params );
			if (!$h->check()) {
				$this->setError( $h->getError() );
				return;
			}
			if (!$h->store()) {
				$this->setError( $h->getError() );
				return;
			}
		}
						
		// update display
		if ($no_html) {
			//$response = $row->getResponse($id, $ip);

			$view = new JView( array('name'=>'vote') );
			$view->option = $this->_option;
			$view->item = $row;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		} else {				
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&section='.$section->alias.'&category='.$category->alias.'&alias='.$row->alias.'#c'.$cid);
		}
	}*/
}

