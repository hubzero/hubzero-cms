<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_blog' );

//-----------

class plgMembersBlog extends JPlugin
{
	public function plgMembersBlog(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'blog' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onMembersAreas( $authorized )
	{
		$areas = array(
			'blog' => JText::_('PLG_MEMBERS_BLOG')
		);
		return $areas;
	}

	//-----------

	public function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		$this->member = $member;
		$this->option = $option;
		$this->authorized = $authorized;
		$this->database = JFactory::getDBO();

		$p = new Hubzero_Plugin_Params( $this->database );
		$this->_params = $p->getParams( $this->member->get('uidNumber'), 'members', 'blog' );

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		if ($returnhtml) {
			include_once(JPATH_ROOT.DS.'components'.DS.'com_blog'.DS.'tables'.DS.'blog.entry.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_blog'.DS.'tables'.DS.'blog.comment.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_blog'.DS.'helpers'.DS.'blog.member.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_blog'.DS.'helpers'.DS.'blog.tags.php');
			
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'blog');
			
			$document =& JFactory::getDocument();
			//$document->addStyleSheet('plugins'.DS.'members'.DS.'blog'.DS.'blog.css');
			$document->setTitle( $document->getTitle().': '.JText::_('PLG_MEMBERS_BLOG') );

			$this->task = JRequest::getVar('action','');
			
			if (is_numeric($this->task)) {
				$this->task = 'entry';
			}

			switch ($this->task) 
			{
				// Feeds
				case 'feed.rss': $this->_feed();   break;
				case 'feed':     $this->_feed();   break;
				//case 'comments.rss': $this->_commentsFeed();   break;
				//case 'comments':     $this->_commentsFeed();   break;
				
				// Settings
				case 'savesettings': $arr['html'] = $this->_savesettings(); break;
				case 'settings':     $arr['html'] = $this->_settings();     break;
				
				// Comments
				case 'savecomment':   $arr['html'] = $this->_savecomment();   break;
				case 'newcomment':    $arr['html'] = $this->_newcomment();    break;
				case 'editcomment':   $arr['html'] = $this->_editcomment();   break;
				case 'deletecomment': $arr['html'] = $this->_deletecomment(); break;
				
				// Entries
				case 'save':   $arr['html'] = $this->_save();   break;
				case 'new':    $arr['html'] = $this->_new();    break;
				case 'edit':   $arr['html'] = $this->_edit();   break;
				case 'delete': $arr['html'] = $this->_delete(); break;
				case 'entry':  $arr['html'] = $this->_entry();  break;
				
				case 'archive':
				case 'browse': 
				default: $arr['html'] = $this->_browse(); break;
			}
		} else {
			$arr['metadata'] = $this->_metadata();
		}

		return $arr;
	}
	
	//-----------

	private function _metadata() 
	{
		$html = '<p class="blog"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=blog').'">'.JText::_('Blog entries').'</a></p>'."\n";
		
		return $html;
	}
	
	//-----------
	
	private function _browse() 
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'members',
				'element'=>'blog',
				'name'=>'browse'
			)
		);
		$view->option = $this->option;
		$view->member = $this->member;
		$view->config = $this->_params;
		$view->authorized = $this->authorized;
		
		// Filters for returning results
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', 25);
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['created_by'] = $this->member->get('uidNumber');
		$filters['year'] = JRequest::getInt('year', 0);
		$filters['month'] = JRequest::getInt('month', 0);
		$filters['scope'] = 'member';
		$filters['group_id'] = 0;
		$filters['search'] = JRequest::getVar('search','');
		
		$juri =& JURI::getInstance();
		$path = $juri->getPath();
		if (strstr($path, '/')) {
			$path = str_replace('/members/'.$this->member->get('uidNumber').'/blog', '', $path);
			if (substr($path, 0, 1) == DS) {
				$path = substr($path, 1);
			}
			$bits = explode('/', $path);
			$filters['year'] = (isset($bits[0])) ? $bits[0] : $filters['year'];
			$filters['month'] = (isset($bits[1])) ? $bits[1] : $filters['month'];
		}

		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$filters['state'] = 'public';
		} else {
			if ($juser->get('id') != $this->member->get('uidNumber')) {
				$filters['state'] = 'registered';
			}
		}
		
		$be = new BlogEntry($this->database);
		
		$total = $be->getCount($filters);
		
		$view->rows = $be->getRecords($filters);
		if ($filters['search']) {
			$view->rows = $this->_highlight($filters['search'], $view->rows);
		}
		
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		$pagenavhtml = $pageNav->getListFooter();
		$pagenavhtml = str_replace('members/?','members/'.$this->member->get('uidNumber').'/blog/?',$pagenavhtml);
		$pagenavhtml = str_replace('action=browse','',$pagenavhtml);
		$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
		$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
		
		$view->firstentry = $be->getDateOfFirstEntry($filters);
		
		$view->popular = $be->getPopularEntries($filters);
		$view->recent = $be->getRecentEntries($filters);
		
		$view->year = $filters['year'];
		$view->month = $filters['month'];
		$view->search = $filters['search'];
		$view->pagenavhtml = $pagenavhtml;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
	
	//-----------
	
	private function _feed() 
	{
		if (!$this->_params->get('feeds_enabled')) {
			$this->_browse();
			return;
		}
		
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'feed'.DS.'feed.php');
		
		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		//$params =& $mainframe->getParams();
		$app =& JFactory::getApplication();
		$params =& $app->getParams();
		$doc->link = JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=blog');
		
		// Filters for returning results
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', 25);
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['created_by'] = $this->member->get('uidNumber');
		$filters['year'] = JRequest::getInt('year', 0);
		$filters['month'] = JRequest::getInt('month', 0);
		$filters['scope'] = 'member';
		$filters['group_id'] = 0;
		$filters['search'] = JRequest::getVar('search','');
		
		$juri =& JURI::getInstance();
		$path = $juri->getPath();
		if (strstr($path, '/')) {
			$path = str_replace('/members/'.$this->member->get('uidNumber').'/blog', '', $path);
			if (substr($path, 0, 1) == DS) {
				$path = substr($path, 1);
			}
			$bits = explode('/', $path);
			$filters['year'] = (isset($bits[0])) ? $bits[0] : $filters['year'];
			$filters['month'] = (isset($bits[1])) ? $bits[1] : $filters['month'];
		}

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename').' - '.stripslashes($this->member->get('name')).': '.JText::_('Blog');
		//$doc->title .= ($filters['year']) ? ': '.$filters['year'] : '';
		//$doc->title .= ($filters['month']) ? ': '.sprintf("%02d",$filters['month']) : '';
		
		$doc->description = JText::sprintf('PLG_MEMBERS_BLOG_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'),stripslashes($this->member->get('name')));
		$doc->copyright = JText::sprintf('PLG_MEMBERS_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('PLG_MEMBERS_BLOG_RSS_CATEGORY');

		//$view->canpost = $this->_getPostingPermissions();

		//$juser =& JFactory::getUser();
		//if ($juser->get('guest')) {
			$filters['state'] = 'public';
		//} else {
			//if ($this->authorized != 'member' && $this->authorized != 'manager' && $this->authorized != 'admin') {
				//$filters['state'] = 'registered';
			//}
		//}
		
		$be = new BlogEntry($this->database);

		$rows = $be->getRecords($filters);

		// Start outputing results if any found
		if (count($rows) > 0) {
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();
			
			$path = $this->_params->get('uploadpath');
			$path = str_replace('{{uid}}',BlogHelperMember::niceidformat($this->member->get('uidNumber')),$path);
			
			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to article
				$link = JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias);

				//$cuser =& JUser::getInstance($row->created_by);
				$author = $this->member->get('name'); //$cuser->get('name');
				
				// Strip html from feed item description text
				$wikiconfig = array(
					'option'   => $this->option,
					'scope'    => 'blog',
					'pagename' => $row->alias,
					'pageid'   => 0,
					'filepath' => $path,
					'domain'   => '' 
				);
				$description = $p->parse(stripslashes($row->content), $wikiconfig, true, true);
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
				if ($this->_params->get('feed_entries') == 'partial') {
					$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
				}

				@$date = ( $row->publish_up ? date( 'r', strtotime($row->publish_up) ) : '' );
				
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
		
		// Output the feed
		echo $doc->render();
	}
	
	//-----------
	
	private function _highlight( $searchquery, $results )
	{
		$toks = array($searchquery);
		
		$resultback = 60;
		$resultlen  = 300;
		
		// Loop through all results
		for ($i = 0, $n = count($results); $i < $n; $i++) 
		{
			$row =& $results[$i];
			
			// Clean the text up a bit first
			$lowerrow = strtolower( $row->content );
			
			// Find first occurrence of a search word
			$pos = 0;
			foreach ($toks as $tok) 
			{
				$pos = strpos( $lowerrow, $tok );
				if ($pos !== false) break;
			}

			if ($pos > $resultback) {
				$row->content = substr( $row->content, ($pos - $resultback), $resultlen );
			} else {
				$row->content = substr( $row->content, 0, $resultlen );
			}

			// Highlight each word/phrase found
			foreach ($toks as $tok) 
			{
				if (($tok == 'class') || ($tok == 'span') || ($tok == 'highlight')) {
					continue;
				}
				$row->content = eregi_replace( $tok, "<span class=\"highlight\">\\0</span>", $row->content);
				$row->title = eregi_replace( $tok, "<span class=\"highlight\">\\0</span>", $row->title);
			}
			
			$row->content = trim($row->content).' &#8230;';
		}
		
		return $results;
	}
	
	//-----------
	
	private function _entry() 
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'members',
				'element'=>'blog',
				'name'=>'entry'
			)
		);
		$view->option = $this->option;
		$view->member = $this->member;
		$view->config = $this->_params;
		$view->authorized = $this->authorized;
		
		if (isset($this->entry) && is_object($this->entry)) {
			$view->row = $this->entry;
		} else {
			$juri =& JURI::getInstance();
			$path = $juri->getPath();
			if (strstr($path, '/')) {
				$path = str_replace('/members/'.$this->member->get('uidNumber').'/blog/', '', $path);
				$bits = explode('/', $path);
				$alias = end($bits);
			}

			$view->row = new BlogEntry($this->database);
			$view->row->loadAlias($alias, 'member', $this->member->get('uidNumber'));
		}
		
		if (!$view->row->id) {
			return $this->_browse();
		}
		
		// Check authorization
		$juser =& JFactory::getUser();
		if (($view->row->state == 2 && $juser->get('guest')) || ($view->row->state == 0 && $juser->get('id') != $this->member->get('uidNumber'))) {
			JError::raiseError( 403, JText::_('PLG_MEMBERS_BLOG_NOT_AUTH') );
			return;
		}
		
		$juser =& JFactory::getUser();
		if ($juser->get('id') != $this->member->get('uidNumber')) { 
			$view->row->hit();
		}
		
		if ($view->row->content) {
			$path = $this->_params->get('uploadpath');
			$path = str_replace('{{uid}}',BlogHelperMember::niceidformat($this->member->get('uidNumber')),$path);

			$wikiconfig = array(
				'option'   => $this->option,
				'scope'    => 'blog',
				'pagename' => $view->row->alias,
				'pageid'   => 0,
				'filepath' => $path,
				'domain'   => '' 
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();
			$view->row->content = $p->parse(stripslashes($view->row->content), $wikiconfig);
		}
		
		$bc = new BlogComment($this->database);
		$view->comments = $bc->getAllComments($view->row->id);
		
		//count($this->comments, COUNT_RECURSIVE)
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
		$view->replyto = new BlogComment($this->database);
		$view->replyto->load($r);

		$bt = new BlogTags($this->database);
		$view->tags = $bt->get_tag_cloud(0,0,$view->row->id);
		
		// Filters for returning results
		$filters = array();
		$filters['limit'] = 10;
		$filters['start'] = 0;
		$filters['created_by'] = $this->member->get('uidNumber');
		$filters['group_id'] = 0;
		$filters['scope'] = 'member';
		
		if ($juser->get('guest')) {
			$filters['state'] = 'public';
		} else {
			if ($juser->get('id') != $this->member->get('uidNumber')) {
				$filters['state'] = 'registered';
			}
		}
		$view->popular = $view->row->getPopularEntries($filters);
		$view->recent = $view->row->getRecentEntries($filters);
		
		// Push some scripts to the template
		/*$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'members'.DS.'blog'.DS.'blog.js')) {
			$document->addScript('plugins'.DS.'members'.DS.'blog'.DS.'blog.js');
		}*/
		
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
	
	//-----------
	
	private function _new() 
	{
		return $this->_edit();
	}
	
	//-----------
	
	private function _edit() 
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('MEMBERS_LOGIN_NOTICE') );
			return $this->_login();
		}
		
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'members',
				'element'=>'blog',
				'name'=>'edit'
			)
		);
		$view->option = $this->option;
		$view->member = $this->member;
		$view->task = $this->task;
		$view->config = $this->_params;
		$view->authorized = $this->authorized;
		
		$id = JRequest::getInt('entry', 0);
		
		$view->entry = new BlogEntry($this->database);
		$view->entry->load($id, 'member');
		if (!$view->entry->id) {
			$view->entry->allow_comments = 1;
			$view->entry->state = 1;
			$view->entry->scope = 'member';
			$view->entry->created_by = $this->member->get('uidNumber');
		}
		
		$bt = new BlogTags($this->database);
		$view->tags = $bt->get_tag_string($view->entry->id);
		
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
	
	//-----------
	
	private function _normalizeTitle($title) 
	{
		$title = str_replace(' ', '-', $this->_shortenTitle($title));
		$title = preg_replace("/[^a-zA-Z0-9\-]/", '', $title);
		return strtolower($title);
	}
	
	//-----------
	
	public function _shortenTitle($text, $chars=100) 
	{
		$text = strip_tags($text);
		$text = trim($text);
		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
		}
		return $text;
	}
	
	//-----------
	
	private function _save() 
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('MEMBERS_LOGIN_NOTICE') );
			return $this->_login();
		}
		
		$entry = JRequest::getVar( 'entry', array(), 'post' );
		
		$row = new BlogEntry( $this->database );
		if (!$row->bind( $entry )) {
			$this->setError( $row->getError() );
			return $this->_edit();
		}
		
		//$row->id = JRequest::getInt( 'entry_id', 0 );
		
		if (!$row->id) {
			$row->alias = $this->_normalizeTitle($row->title);
			$row->created = date( 'Y-m-d H:i:s', time() );  // use gmdate() ?
			$row->publish_up = date( 'Y-m-d H:i:s', time() );
		}
		
		if (!$row->publish_up || $row->publish_up == '0000-00-00 00:00:00') {
			$row->publish_up = $row->created;
		}
		
		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			return $this->_edit();
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return $this->_edit();
		}
		
		// Process tags
		$tags = trim(JRequest::getVar( 'tags', '' ));
		$bt = new BlogTags( $this->database );
		$bt->tag_object($this->member->get('uidNumber'), $row->id, $tags, 1, 1);

		//$this->entry = $row;

		//return $this->_entry();
		$app =& JFactory::getApplication();
		$app->redirect( JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias) );
	}
	
	//-----------
	
	private function _delete() 
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('MEMBERS_LOGIN_NOTICE') );
			return;
		}
		
		if (!$this->authorized) {
			$this->setError( JText::_('PLG_MEMBERS_BLOG_NOT_AUTHORIZED') );
			return $this->_browse();
		}
		
		// Incoming
		$id = JRequest::getInt( 'entry', 0 );
		if (!$id) {
			return $this->_browse();
		}
		
		$process = JRequest::getVar( 'process', '' );
		$confirmdel = JRequest::getVar( 'confirmdel', '' );
		
		// Initiate a blog entry object
		$entry = new BlogEntry( $this->database );
		$entry->load( $id );
		
		// Did they confirm delete?
		if (!$process || !$confirmdel) {
			if ($process && !$confirmdel) {
				$this->setError( JText::_('PLG_MEMBERS_BLOG_ERROR_CONFIRM_DELETION') );
			}

			// Output HTML
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'members',
					'element'=>'blog',
					'name'=>'delete'
				)
			);
			$view->option = $this->option;
			$view->member = $this->member;
			$view->task = $this->task;
			$view->config = $this->_params;
			$view->entry = $entry;
			$view->authorized = $this->authorized;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			return $view->loadTemplate();
		}
		
		// Delete all comments on an entry
		if (!$entry->deleteComments( $id )) {
			$this->setError( $entry->getError() );
			return $this->_browse();
		}
		
		// Delete all associated content
		if (!$entry->deleteTags( $id )) {
			$this->setError( $entry->getError() );
			return $this->_browse();
		}
		
		// Delete all associated content
		if (!$entry->deleteFiles( $id )) {
			$this->setError( $entry->getError() );
			return $this->_browse();
		}

		// Delete the entry itself
		if (!$entry->delete( $id )) {
			$this->setError( $entry->getError() );
		}
		
		// Return the topics list
		return $this->_browse();
	}
	
	//-----------
	
	private function _savecomment() 
	{
		// Ensure the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('MEMBERS_LOGIN_NOTICE') );
			return $this->_login();
		}
		
		// Incoming
		$comment = JRequest::getVar( 'comment', array(), 'post' );
		
		// Instantiate a new comment object and pass it the data
		$row = new BlogComment( $this->database );
		if (!$row->bind( $comment )) {
			$this->setError( $row->getError() );
			return $this->_entry();
		}
		
		// Set the created time
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );  // use gmdate() ?
		}
		
		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			return $this->_entry();
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return $this->_entry();
		}
		
		/*
		if ($row->created_by != $this->member->get('uidNumber)) {
			$this->entry = new BlogEntry($this->database);
			$this->entry->load($row->entry_id);
			
			// Get the site configuration
			$jconfig =& JFactory::getConfig();

			// Build the "from" data for the e-mail
			$from = array();
			$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('PLG_MEMBERS_BLOG');
			$from['email'] = $jconfig->getValue('config.mailfrom');

			$subject = JText::_('PLG_MEMBERS_BLOG_SUBJECT_COMMENT_POSTED');

			// Build the SEF referenced in the message
			$juri =& JURI::getInstance();
			$sef = JRoute::_('index.php?option='.$this->option.'&id='. $this->member->get('uidNumber').'&active=blog&task='.JHTML::_('date',$this->entry->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->entry->publish_up, '%m', 0).'/'.$this->entry->alias.'#comments);
			if (substr($sef,0,1) == '/') {
				$sef = substr($sef,1,strlen($sef));
			}

			// Message
			$message  = "The following comment has been posted to your blog entry:\r\n\r\n";
			$message .= stripslashes($row->content)."\r\n\r\n";
			$message .= "To view all comments on the blog entry, go to:\r\n";
			$message .= $juri->base().$sef . "\r\n";

			// Send the message
			JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			if (!$dispatcher->trigger( 'onSendMessage', array( 'blog_comment', $subject, $message, $from, array($this->member->get('uidNumber')), $this->option ))) {
				$this->setError( JText::_('PLG_MEMBERS_BLOG_ERROR_MSG_MEMBER_FAILED') );
			}
		}
		*/
		
		return $this->_entry();
	}
	
	//-----------
	
	private function _deletecomment() 
	{
		// Ensure the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('MEMBERS_LOGIN_NOTICE') );
			return;
		}
		
		// Incoming
		$id = JRequest::getInt( 'comment', 0 );
		if (!$id) {
			return $this->_entry();
		}
		
		// Initiate a blog comment object
		$comment = new BlogComment( $this->database );
		
		// Delete all comments on an entry
		if (!$comment->deleteChildren( $id )) {
			$this->setError( $comment->getError() );
			return $this->_entry();
		}

		// Delete the entry itself
		if (!$comment->delete( $id )) {
			$this->setError( $comment->getError() );
		}
		
		// Return the topics list
		return $this->_entry();
	}
	
	//-----------
	
	private function _settings() 
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('GROUPS_LOGIN_NOTICE') );
			return;
		}
		
		if (!$this->authorized) {
			$this->setError( JText::_('PLG_MEMBERS_BLOG_NOT_AUTHORIZED') );
			return $this->_browse();
		}
		
		// Output HTML
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'members',
				'element'=>'blog',
				'name'=>'settings'
			)
		);
		$view->option = $this->option;
		$view->member = $this->member;
		$view->task = $this->task;
		$view->config = $this->_params;
		$view->settings = new Hubzero_Plugin_Params( $this->database );
		$view->settings->loadPlugin($this->member->get('uidNumber'), 'members', 'blog');
		$view->authorized = $this->authorized;
		$view->message = (isset($this->message)) ? $this->message : '';
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}
	
	//-----------
	
	private function _savesettings() 
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('MEMBERS_LOGIN_NOTICE') );
			return;
		}
		
		if (!$this->authorized) {
			$this->setError( JText::_('PLG_MEMBERS_BLOG_NOT_AUTHORIZED') );
			return $this->_browse();
		}
		
		$settings = JRequest::getVar( 'settings', array(), 'post' );
		
		$row = new Hubzero_Plugin_Params( $this->database );
		if (!$row->bind( $settings )) {
			$this->setError( $row->getError() );
			return $this->_entry();
		}
		
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
			$this->setError( $row->getError() );
			return $this->_settings();
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return $this->_settings();
		}
		
		$this->message = JText::_('Settings successfully saved!');
		
		return $this->_settings();
	}
}