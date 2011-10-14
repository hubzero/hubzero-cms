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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'BlogController'
 * 
 * Long description (if any) ...
 */
class BlogController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_task = JRequest::getVar('task','');

		switch ($this->_task)
		{
			// File manager for uploading images/files to be used in group descriptions
			case 'media':        $this->_media();        break;
			case 'listfiles':    $this->_listfiles();    break;
			case 'upload':       $this->_upload();       break;
			case 'deletefolder': $this->_deletefolder(); break;
			case 'deletefile':   $this->_deletefile();   break;

			// Feeds
			case 'feed.rss': $this->_feed();   break;
			case 'feed':     $this->_feed();   break;
			case 'comments.rss': $this->_commentsFeed();   break;
			case 'comments':     $this->_commentsFeed();   break;

			// Comments
			case 'savecomment':   $this->_saveComment();   break;
			//case 'newcomment':    $this->_newComment();    break;
			//case 'editcomment':   $this->_editComment();   break;
			case 'deletecomment': $this->_deleteComment(); break;

			// Entry
			case 'save':   $this->_save();   break;
			case 'new':    $this->_new();    break;
			case 'edit':   $this->_edit();   break;
			case 'delete': $this->_delete(); break;
			case 'entry':  $this->_entry();  break;

			// Entries
			case 'archive':
			case 'browse':
			default: $this->_browse(); break;
		}
	}

	/**
	 * Short description for '_buildPathway'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		$title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				$title,
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			if ($this->_task != 'entry' && $this->_task != 'savecomment' && $this->_task != 'deletecomment') {
				$pathway->addItem(
					JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
					'index.php?option='.$this->_option.'&task='.$this->_task
				);
			}
			$year = JRequest::getInt('year', 0);
			if ($year) {
				$pathway->addItem(
					$year,
					'index.php?option='.$this->_option.'&year='.$year
				);
			}
			$month = JRequest::getInt('month', 0);
			if ($month) {
				$pathway->addItem(
					sprintf("%02d",$month),
					'index.php?option='.$this->_option.'&year='.$year.'&month='.sprintf("%02d",$month)
				);
			}
			if ($this->row) {
				$pathway->addItem(
					stripslashes($this->row->title),
					'index.php?option='.$this->_option.'&year='.$year.'&month='.sprintf("%02d",$month).'&alias='.$this->row->alias
				);
			}
		}
	}

	/**
	 * Short description for '_buildTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->_title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));
		if ($this->_task) {
			if ($this->_task != 'entry' && $this->_task != 'savecomment' && $this->_task != 'deletecomment') {
				$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
			}
			$year = JRequest::getInt('year', 0);
			if ($year) {
				$this->_title .= ': '.$year;
			}
			$month = JRequest::getInt('month', 0);
			if ($month) {
				$this->_title .= ': '.sprintf("%02d",$month);
			}
			if ($this->row) {
				$this->_title .= ': '.stripslashes($this->row->title);
			}
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	/**
	 * Short description for '_browse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function _browse()
	{
		// Instantiate a new view
		$view = new JView(array('name'=>'browse'));
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->authorized = $this->_authorize();
		$view->config = $this->config;

		// Get configuration
		$jconfig = JFactory::getConfig();
		
		// Filters for returning results
		$view->filters = array();
		$view->filters['limit'] = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$view->filters['start'] = JRequest::getInt('limitstart', 0);
		$view->filters['year'] = JRequest::getInt('year', 0);
		$view->filters['month'] = JRequest::getInt('month', 0);
		$view->filters['scope'] = 'site';
		$view->filters['group_id'] = 0;
		$view->filters['search'] = JRequest::getVar('search','');

		if ($this->juser->get('guest')) {
			$view->filters['state'] = 'public';
		} else {
			if (!$view->authorized) {
				$view->filters['state'] = 'registered';
			}
		}

		// Instantiate the BlogEntry object
		$be = new BlogEntry($this->database);

		// Get a record count
		$view->total = $be->getCount($view->filters);

		// Get the records
		$view->rows = $be->getRecords($view->filters);

		// Highlight search results
		if ($view->filters['search']) {
			$view->rows = $this->_highlight($view->filters['search'], $view->rows);
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		$view->firstentry = $be->getDateOfFirstEntry($view->filters);

		$view->year = $view->filters['year'];
		$view->month = $view->filters['month'];

		// Get any errors for display
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts();

		$view->popular = $be->getPopularEntries($view->filters);
		$view->recent = $be->getRecentEntries($view->filters);

		$view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

		// Output HTML
		$view->display();
	}

	/**
	 * Short description for '_highlight'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $searchquery Parameter description (if any) ...
	 * @param      array $results Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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

	/**
	 * Short description for '_entry'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _entry()
	{
		$view = new JView(array('name'=>'entry'));
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->authorized = $this->_authorize();
		$view->config = $this->config;

		$alias = JRequest::getVar( 'alias', '' );

		if (!$alias) {
			return $this->_browse();
		}

		$view->row = new BlogEntry($this->database);
		$view->row->loadAlias($alias, 'site');

		if (!$view->row->id) {
			return $this->_browse();
		}
		$this->row = $view->row;

		// Check authorization
		if (($view->row->state == 2 && $this->juser->get('guest')) || ($view->row->state == 0 && !$view->authorized)) {
			JError::raiseError( 403, JText::_('COM_BLOG_NOT_AUTH') );
			return;
		}

		if ($this->juser->get('id') != $view->row->created_by) {
			$view->row->hit();
		}

		if ($view->row->content) {
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => 'blog',
				'pagename' => $view->row->alias,
				'pageid'   => 0,
				'filepath' => $this->config->get('uploadpath'),
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
		//$filters['created_by'] = $this->member->get('uidNumber');
		$filters['scope'] = 'site';
		$filters['group_id'] = 0;

		if ($this->juser->get('guest')) {
			$filters['state'] = 'public';
		} else {
			if (!$view->authorized) {
				$filters['state'] = 'registered';
			}
		}
		$view->popular = $view->row->getPopularEntries($filters);
		$view->recent = $view->row->getRecentEntries($filters);
		$view->firstentry = $view->row->getDateOfFirstEntry($filters);

		// Push some scripts to the template
		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts();

		$view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for '_new'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _new()
	{
		return $this->_edit();
	}

	/**
	 * Short description for '_edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _edit()
	{
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_BLOG_LOGIN_NOTICE') );
			return $this->_login();
		}

		$view = new JView(array('name'=>'edit'));
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->authorized = $this->_authorize();

		$id = JRequest::getInt('entry', 0);

		$view->entry = new BlogEntry($this->database);
		$view->entry->load($id);
		if (!$view->entry->id) {
			$view->entry->allow_comments = 1;
			$view->entry->state = 1;
			$view->entry->scope = 'site';
			$view->entry->created_by = $this->juser->get('id');
		}

		$bt = new BlogTags($this->database);
		$view->tags = $bt->get_tag_string($view->entry->id);

		// Push some scripts to the template
		$this->_buildTitle();
		$this->_buildPathway();
		$this->_getStyles();
		$this->_getScripts();

		$view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));

		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for '_normalizeTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $title Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _normalizeTitle($title)
	{
		$title = str_replace(' ', '-', $this->_shortenTitle($title));
		$title = preg_replace("/[^a-zA-Z0-9\-]/", '', $title);
		return strtolower($title);
	}

	/**
	 * Short description for '_shortenTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $text Parameter description (if any) ...
	 * @param      integer $chars Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for '_save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _save()
	{
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_BLOG_LOGIN_NOTICE') );
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
		$bt->tag_object($this->juser->get('id'), $row->id, $tags, 1, 1);

		//return $this->_entry();
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias);
	}

	/**
	 * Short description for '_delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _delete()
	{
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_BLOG_LOGIN_NOTICE') );
			return;
		}

		if (!$this->_authorize()) {
			$this->setError( JText::_('COM_BLOG_NOT_AUTHORIZED') );
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
				$this->setError( JText::_('COM_BLOG_ERROR_CONFIRM_DELETION') );
			}

			// Push some scripts to the template
			$this->_buildTitle();
			$this->_buildPathway();
			$this->_getStyles();
			$this->_getScripts();

			// Output HTML
			$view = new JView( array('name'=>'delete') );
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->title = ($this->config->get('title')) ? $this->config->get('title') : JText::_(strtoupper($this->_option));
			$view->entry = $entry;
			$view->authorized = $this->_authorize();
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
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

	/**
	 * Short description for '_saveComment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _saveComment()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_BLOG_LOGIN_NOTICE') );
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

	/**
	 * Short description for '_deleteComment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _deleteComment()
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) {
			$this->setError( JText::_('COM_BLOG_LOGIN_NOTICE') );
			return;
		}

		// Incoming
		$id = JRequest::getInt( 'comment', 0 );
		if (!$id) {
			return $this->_entry();
		}

		// Initiate a blog comment object
		$comment = new BlogComment( $this->database );
		$comment->load( $id );

		if ($this->juser->get('id') != $comment->created_by && !$this->authorized) {
			return $this->_entry();
		}

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

	/**
	 * Short description for '_feed'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function _feed()
	{
		if (!$this->config->get('feeds_enabled')) {
			$this->_browse();
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

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['year'] = JRequest::getInt('year', 0);
		$filters['month'] = JRequest::getInt('month', 0);
		$filters['scope'] = 'site';
		$filters['group_id'] = 0;
		$filters['search'] = JRequest::getVar('search','');

		if ($this->juser->get('guest')) {
			$filters['state'] = 'public';
		} else {
			if (!$this->_authorize()) {
				$filters['state'] = 'registered';
			}
		}

		// Instantiate the BlogEntry object
		$be = new BlogEntry($this->database);

		// Get the records
		$rows = $be->getRecords($filters);

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename').' - '.JText::_(strtoupper($this->_option));
		$doc->title .= ($filters['year']) ? ': '.$year : '';
		$doc->title .= ($filters['month']) ? ': '.sprintf("%02d",$filters['month']) : '';

		$doc->description = JText::sprintf('COM_BLOG_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'));
		$doc->copyright = JText::sprintf('COM_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('COM_BLOG_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) {
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => 'blog',
				'pagename' => '',
				'pageid'   => 0,
				'filepath' => $this->config->get('uploadpath'),
				'domain'   => ''
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();

			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to article
				$link = JRoute::_('index.php?option='.$this->_option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias);

				$cuser =& JUser::getInstance($row->created_by);
				$author = $cuser->get('name');

				// Strip html from feed item description text
				$description = $p->parse(stripslashes($row->content), $wikiconfig);
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
				if ($this->config->get('feed_entries') == 'partial') {
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

	/**
	 * Short description for '_commentsFeed'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function _commentsFeed()
	{
		if (!$this->config->get('feeds_enabled')) {
			$this->_task = 'entry';
			$this->_entry();
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
			return $this->_browse();
		}

		$entry = new BlogEntry($this->database);
		$entry->loadAlias($alias, 'site');

		if (!$entry->id) {
			$this->_task = 'browse';
			return $this->_browse();
		}

		$bc = new BlogComment($this->database);
		$rows = $bc->getAllComments($entry->id);

		$year = JRequest::getInt( 'year', date("Y") );
		$month = JRequest::getInt( 'month', 0 );

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename').' - '.JText::_(strtoupper($this->_option));
		$doc->title .= ($year) ? ': '.$year : '';
		$doc->title .= ($month) ? ': '.sprintf("%02d",$month) : '';
		$doc->title .= ($entry->title) ? ': '.stripslashes($entry->title) : '';
		$doc->title .= ': '.JText::_('Comments');

		$doc->description = JText::sprintf('COM_BLOG_COMMENTS_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'), stripslashes($entry->title));
		$doc->copyright = JText::sprintf('COM_BLOG_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		//$doc->category = JText::_('COM_BLOG_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) {
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => 'blog',
				'pagename' => $entry->alias,
				'pageid'   => 0,
				'filepath' => $this->config->get('uploadpath'),
				'domain'   => ''
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();

			foreach ($rows as $row)
			{
				// URL link to article
				$link = JRoute::_('index.php?option='.$this->_option.'&task='.JHTML::_('date',$entry->publish_up, '%Y', 0).'/'.JHTML::_('date',$entry->publish_up, '%m', 0).'/'.$entry->alias.'#c'.$row->id);

				$author = JText::_('COM_BLOG_ANONYMOUS');
				if (!$row->anonymous) {
					$cuser =& JUser::getInstance($row->created_by);
					$author = $cuser->get('name');
				}

				// Prepare the title
				$title = JText::sprintf('Comment by %s', $author).' @ '.JHTML::_('date',$row->created, '%I:%M %p', 0).' on '.JHTML::_('date',$row->created, '%d %b, %Y', 0);

				// Strip html from feed item description text
				if ($row->reports) {
					$description = JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE');
				} else {
					$description = $p->parse(stripslashes($row->content), $wikiconfig);
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
						$link = JRoute::_('index.php?option='.$this->_option.'&task='.JHTML::_('date',$entry->publish_up, '%Y', 0).'/'.JHTML::_('date',$entry->publish_up, '%m', 0).'/'.$entry->alias.'#c'.$reply->id);

						$author = JText::_('COM_BLOG_ANONYMOUS');
						if (!$reply->anonymous) {
							$cuser =& JUser::getInstance($reply->created_by);
							$author = $cuser->get('name');
						}

						// Prepare the title
						$title = JText::sprintf('Reply to comment #%s by %s', $row->id, $author).' @ '.JHTML::_('date',$reply->created, '%I:%M %p', 0).' on '.JHTML::_('date',$reply->created, '%d %b, %Y', 0);

						// Strip html from feed item description text
						if ($reply->reports) {
							$description = JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE');
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
								$link = JRoute::_('index.php?option='.$this->_option.'&task='.JHTML::_('date',$entry->publish_up, '%Y', 0).'/'.JHTML::_('date',$entry->publish_up, '%m', 0).'/'.$entry->alias.'#c'.$response->id);

								$author = JText::_('COM_BLOG_ANONYMOUS');
								if (!$response->anonymous) {
									$cuser =& JUser::getInstance($response->created_by);
									$author = $cuser->get('name');
								}

								// Prepare the title
								$title = JText::sprintf('Reply to comment #%s by %s', $reply->id, $author).' @ '.JHTML::_('date',$response->created, '%I:%M %p', 0).' on '.JHTML::_('date',$response->created, '%d %b, %Y', 0);

								// Strip html from feed item description text
								if ($response->reports) {
									$description = JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE');
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

	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------


	/**
	 * Short description for '_upload'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function _upload()
	{
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->_media();
			return;
		}

		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('COM_BLOG_NO_FILE') );
			$this->_media();
			return;
		}

		// Incoming
		$scope = JRequest::getVar( 'scope', 'site' );
		$id = JRequest::getInt( 'id', 0 );

		// Build the file path
		$path = $this->_getUploadPath($scope, $id);

		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('COM_BLOG_UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->_media();
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('COM_BLOG_ERROR_UPLOADING') );
		}

		// Push through to the media view
		$this->_media();
	}

	/**
	 * Short description for '_deletefolder'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function _deletefolder()
	{
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->_media();
			return;
		}

		// Incoming file
		$file = trim(JRequest::getVar( 'folder', '', 'get' ));
		if (!$file) {
			$this->setError( JText::_('COM_BLOG_NO_DIRECTORY') );
			$this->_media();
			return;
		}

		// Incoming
		$scope = JRequest::getVar( 'scope', 'site' );
		$id = JRequest::getInt( 'id', 0 );

		// Build the file path
		$path = $this->_getUploadPath($scope, $id);

		$del_folder = $path.DS.$file;

		// Delete the folder
		if (is_dir($del_folder)) {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($del_folder)) {
				$this->setError( JText::_('COM_BLOG_UNABLE_TO_DELETE_DIRECTORY') );
			}
		}

		// Push through to the media view
		$this->_media();
	}

	/**
	 * Short description for '_deletefile'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function _deletefile()
	{
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->_media();
			return;
		}

		// Incoming file
		$file = trim(JRequest::getVar( 'file', '', 'get' ));
		if (!$file) {
			$this->setError( JText::_('COM_BLOG_NO_FILE') );
			$this->_media();
			return;
		}

		// Incoming
		$scope = JRequest::getVar( 'scope', 'site' );
		$id = JRequest::getInt( 'id', 0 );

		// Build the file path
		$path = $this->_getUploadPath($scope, $id);

		if (!file_exists($path.DS.$file) or !$file) {
			$this->setError( JText::_('COM_BLOG_FILE_NOT_FOUND') );
			$this->_media();
			return;
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('COM_BLOG_UNABLE_TO_DELETE_FILE') );
			}
		}

		// Push through to the media view
		$this->_media();
	}

	/**
	 * Short description for '_media'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _media()
	{
		// Incoming
		$scope = JRequest::getVar( 'scope', 'site' );
		$id = JRequest::getInt( 'id', 0 );

		// Output HTML
		$view = new JView( array('name'=>'edit', 'layout'=>'filebrowser') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->config = $this->config;
		$view->id = $id;
		$view->scope = $scope;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for '_getUploadPath'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $scope Parameter description (if any) ...
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	protected function _getUploadPath($scope, $id)
	{
		$path = JPATH_ROOT;
		switch ($scope)
		{
			case 'member':
				jimport( 'joomla.plugin.plugin' );
				$plugin = JPluginHelper::getPlugin( 'members', 'blog' );
				$params = new JParameter( $plugin->params );
				$p = $params->get('uploadpath');
				$p = str_replace('{{uid}}',BlogHelperMember::niceidformat($id),$p);
			break;

			case 'group':
				jimport( 'joomla.plugin.plugin' );
				$plugin = JPluginHelper::getPlugin( 'groups', 'blog' );
				$params = new JParameter( $plugin->params );
				$p = $params->get('uploadpath');
				$p = str_replace('{{gid}}',BlogHelperMember::niceidformat($id),$p);
			break;

			case 'site':
			default:
				$p = $this->config->get('uploadpath');
			break;
		}
		if (substr($p, 0, 1) != DS) {
			$path .= DS;
		}
		$path .= $p;
		return $path;
	}

	/**
	 * Short description for '_recursiveListdir'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $base Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	protected function _recursiveListdir($base)
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
	                $subdirlist = $this->_recursiveListdir($subbase);
	            }
	        }
	        closedir($dh);
	    }
	    return $dirlist;
	}

	/**
	 * Short description for '_listfiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _listfiles()
	{
		// Incoming
		$scope = JRequest::getVar( 'scope', 'site' );
		$id = JRequest::getInt( 'id', 0 );

		$path = $this->_getUploadPath($scope, $id);

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
		//} else {
		//	$this->setError( JText::sprintf('COM_BLOG_ERROR_MISSING_DIRECTORY', $path) );
		}

		$view = new JView( array('name'=>'edit', 'layout'=>'filelist') );
		$view->option = $this->_option;
		$view->docs = $docs;
		$view->folders = $folders;
		$view->images = $images;
		$view->config = $this->config;
		$view->id = $id;
		$view->scope = $scope;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
}
