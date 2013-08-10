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
defined('_JEXEC') or die('Restricted access');

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'h:i A';
	$tz = null;
}

jimport('joomla.plugin.plugin');

/**
 * HUBzero plugin class for displaying comments
 */
class plgHubzeroComments extends JPlugin
{
	private $_pushscripts = true;

	/**
	 * Constructor
	 * 
	 * @param      object &$subject The object to observe
	 * @param      array  $config   An optional associative array of configuration settings.
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Display comments on an object
	 * 
	 * @param      type    $objType    Object type to pull comments for
	 * @param      integer $objId      Object ID to pull comments for
	 * @param      string  $authorized Authorization level
	 * @return     string HTML
	 */
	public function onAfterDisplayContent($obj, $option, $url=null) 
	{
		// Ensure we have needed vars
		if (!is_object($obj)) 
		{
			return '';
		}
		
		ximport('Hubzero_Item_Comment');
		ximport('Hubzero_Item_Vote');
		ximport('Hubzero_Plugin_View');

		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'hubzero',
				'element' => 'comments',
				'name'    => 'view',
				'layout'  => 'default'
			)
		);
		$this->view->database = $this->database = JFactory::getDBO();
		$this->view->juser    = $this->juser    = JFactory::getUser();
		$this->view->option   = $this->option   = $option;
		$this->view->obj      = $this->obj      = $obj;
		$this->view->obj_type = $this->obj_type = substr($option, 4);
		$this->view->url      = $this->url      = ($url ? $url : 'index.php?option=' . $this->option . '&id=' . $this->obj->id . '&active=comments');
		$this->view->depth    = 0;

		$this->_authorize();

		$this->view->params   = $this->params;

		$this->view->task     = $this->task    = JRequest::getVar('action', '');
		
		switch ($this->task) 
		{
			// Feeds
			case 'feed.rss': $this->_feed();   break;
			case 'feed':     $this->_feed();   break;

			// Entries
			case 'save':     $this->_save();   break;
			case 'new':      $this->_view();   break;
			case 'edit':     $this->_view();   break;
			case 'delete':   $this->_delete(); break;
			case 'view':     $this->_view();   break;
			case 'vote':     $this->_vote();   break;

			default:         $this->_view();   break;
		}

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Return the Input tag
		return $this->view->loadTemplate();
	}

	/**
	 * Set permissions
	 * 
	 * @param      string  $assetType Type of asset to set permissions for (component, section, category, thread, post)
	 * @param      integer $assetId   Specific object to check permissions for
	 * @return     void
	 */
	protected function _authorize($assetType='comment', $assetId=null)
	{
		// Are comments public or registered members only?
		if ($this->params->get('comments_viewable', 0) <= 0)
		{
			// Public
			$this->params->set('access-view-' . $assetType, true);
		}

		// Logged in?
		if (!$this->juser->get('guest')) 
		{
			// Set comments to viewable
			$this->params->set('access-view-' . $assetType, true);

			// Joomla 1.6+
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$asset  = $this->option;
				if ($assetId)
				{
					$asset .= ($assetType != 'comment') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$yearFormat  = "Y";
				$monthFormat = "m";
				$dayFormat   = "d";

				// Are they an admin?
				$this->params->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->params->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				if ($this->params->set('access-admin-' . $assetType) 
				 || $this->params->set('access-manage-' . $assetType))
				{
					$this->params->set('access-create-' . $assetType, true);
					$this->params->set('access-delete-' . $assetType, true);
					$this->params->set('access-edit-' . $assetType, true);
					return;
				}
			}
			else 
			{
				// Joomla 1.5

				$yearFormat  = "%Y";
				$monthFormat = "%m";
				$dayFormat   = "%d";

				// Are they an admin?
				if ($this->juser->authorize($this->option, 'manage'))
				{
					$this->params->set('access-manage-' . $assetType, true);
					$this->params->set('access-admin-' . $assetType, true);
					$this->params->set('access-create-' . $assetType, true);
					$this->params->set('access-delete-' . $assetType, true);
					$this->params->set('access-edit-' . $assetType, true);
					return;
				}
			}
			if (isset($this->obj->publish_up) && $this->obj->publish_up) 
			{
				$d = $this->obj->publish_up;
			}
			/*else if (isset($this->obj->modified) && $this->obj->modified) 
			{
				$d = $this->obj->modified;
			}*/
			else 
			{
				$d = $this->obj->created;
			}
			$year  = intval(substr($d, 0, 4));
			$month = intval(substr($d, 5, 2));
			$day   = intval(substr($d, 8, 2));

			switch ($this->params->get('comments_close', 'never'))
			{
				case 'day':
					$dt = mktime(0, 0, 0, $month, ($day+1), $year);
				break;
				case 'week':
					$dt = mktime(0, 0, 0, $month, ($day+7), $year);
				break;
				case 'month':
					$dt = mktime(0, 0, 0, ($month+1), $day, $year);
				break;
				case '6months':
					$dt = mktime(0, 0, 0, ($month+6), $day, $year);
				break;
				case 'year':
					$dt = mktime(0, 0, 0, $month, $day, ($year+1));
				break;
				case 'never':
				default:
					$dt =mktime(0, 0, 0, $month, $day, $year);
				break;
			}

			$pdt = strftime($yearFormat, $dt) . '-' . strftime($monthFormat, $dt) . '-' . strftime($dayFormat, $dt) . ' 00:00:00';
			$today = date('Y-m-d H:i:s', time());

			// Can users create comments?
			if ($this->params->get('comments_close', 'never') == 'never' 
			 || ($this->params->get('comments_close', 'never') != 'now' && $today < $pdt)) 
			{
				$this->params->set('access-create-' . $assetType, true);
			}
			// Can users edit comments?
			if ($this->params->get('comments_editable'))
			{
				$this->params->set('access-edit-' . $assetType, true);
			}
			// Can users delete comments?
			if ($this->params->get('comments_deletable'))
			{
				$this->params->set('access-delete-' . $assetType, true);
			}
		}
	}

	/**
	 * Method to add a message to the component message que
	 *
	 * @param	string	$message	The message to add
	 * @return	void
	 */
	public function redirect($url, $msg='', $msgType='')
	{
		$url = ($url != '') ? $url : JRequest::GetVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->option . '&id=' . $this->obj->id . '&active=comments'), 'server');
		$url = str_replace('&amp;', '&', $url);

		$msg = ($msg) ? $msg : '';
		$msgType = ($msgType) ? $msgType : 'message';

		if ($url) 
		{
			$app =& JFactory::getApplication();
			$app->redirect($url, $msg, $msgType);
		}
	}

	/**
	 * Vote on a comment
	 *
	 * @return    void
	 */
	protected function _vote() 
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->setError(JText::_('PLG_HUBZERO_COMMENTS_LOGIN_NOTICE'));
			return $this->_login();
		}

		$no_html = JRequest::getInt('no_html', 0);

		// Get comments on this article
		$v = new Hubzero_Item_Vote($this->database);
		$v->created_by = $this->juser->get('id');
		$v->item_type  = 'comment';
		//$v->item_id    = JRequest::getInt('comment', 0);
		//$v->vote       = JRequest::getVar('vote', 'up');
		if ($item_id = JRequest::getInt('voteup', 0))
		{
			$v->vote    = 1;
		} 
		else if ($item_id = JRequest::getInt('votedown', 0))
		{
			$v->vote    = -1;
		}
		$v->item_id    = $item_id;

		// Check content
		if (!$v->check()) 
		{
			$this->setError($v->getError());
		}
		else 
		{
			// Store new content
			if (!$v->store()) 
			{
				$this->setError($v->getError());
			}
		}

		if ($this->getError() && !$no_html) 
		{
			$this->redirect(
				$this->url, 
				$this->getError(),
				'error'
			);
			return;
		}

		$this->view->setLayout('vote');

		$this->view->item = new Hubzero_Item_Comment($this->database);
		$this->view->item->load($v->item_id);
		if ($v->vote == 1)
		{
			$this->view->item->positive++;
		}
		else 
		{
			$this->view->item->negative++;
		}
		if (!$this->view->item->store()) 
		{
			$this->setError($this->view->item->getError());
		}
		$this->view->item->vote = $v->vote;

		if (!$no_html)
		{
			$this->redirect(
				$this->url, 
				JText::_('PLG_HUBZERO_COMMENTS_VOTE_SAVED'),
				'message'
			);
			return;
		}

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Ugly brute force method of cleaning output
		ob_clean();
		echo $this->view->loadTemplate();
		exit();
	}

	/**
	 * Show a list of comments
	 *
	 * @return    void
	 */
	protected function _view() 
	{
		// Push some needed scripts and stylings to the template but ensure we do it only once
		if ($this->_pushscripts) 
		{
			ximport('Hubzero_Document');
			//Hubzero_Document::addPluginStyleSheet('hubzero', 'comments');
			Hubzero_Document::addPluginScript('hubzero', 'comments');

			$this->_pushscripts = false;
		}

		// Get comments on this article
		$hc = new Hubzero_Item_Comment($this->database);

		$this->view->comments = $hc->getComments(
			$this->obj_type, 
			$this->obj->id,
			0,
			$this->params->get('comments_limit', 25)
		);
		
		//print_r($this->view->comments); die;

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
	}

	/**
	 * Save an entry
	 *
	 * @return    void
	 */
	protected function _save() 
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->redirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($this->url)), 
				JText::_('PLG_HUBZERO_COMMENTS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$comment = JRequest::getVar('comment', array(), 'post');

		// Instantiate a new comment object and pass it the data
		$row = new Hubzero_Item_Comment($this->database);
		if (!$row->bind($comment)) 
		{
			$this->redirect(
				$this->url, 
				$row->getError(),
				'error'
			);
			return;
		}
		$row->setUploadDir($this->params->get('comments_uploadpath', '/site/comments'));

		if ($row->id && !$this->params->get('access-edit-comment')) 
		{
			$this->redirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($this->url)), 
				JText::_('PLG_HUBZERO_COMMENTS_NOTAUTH'),
				'warning'
			);
			return;
		}

		// Check content
		if (!$row->check()) 
		{
			$this->redirect(
				$this->url, 
				$row->getError(),
				'error'
			);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->redirect(
				$this->url, 
				$row->getError(),
				'error'
			);
			return;
		}
		
		$this->redirect(
			$this->url, 
			JText::_('PLG_HUBZERO_COMMENTS_SAVED'),
			'message'
		);
	}

	/**
	 * Mark a comment as deleted
	 * NOTE: Does not actually delete data. Simply marks record.
	 *
	 * @return    void
	 */
	protected function _delete() 
	{
		// Ensure the user is logged in
		if ($this->juser->get('guest')) 
		{
			$this->redirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($this->url)), 
				JText::_('PLG_HUBZERO_COMMENTS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('comment', 0);
		if (!$id) 
		{
			return $this->_redirect();
		}

		// Initiate a blog comment object
		$comment = new Hubzero_Item_Comment($this->database);
		$comment->load($id);

		if ($this->juser->get('id') != $comment->created_by 
		 && !$this->params->get('access-delete-comment')) 
		{
			$this->redirect($this->url);
			return;
		}

		// Delete the entry itself
		if (!$comment->setState($id, 2)) 
		{
			$this->setError($comment->getError());
		}

		$this->redirect(
			$this->url, 
			JText::_('PLG_HUBZERO_COMMENTS_REMOVED'),
			'message'
		);
	}

	/**
	 * Display a feed of comments
	 *
	 * @return    void
	 */
	protected function _feed() 
	{
		if (!$this->params->get('comments_feeds')) 
		{
			$this->action = 'view';
			$this->_view();
			return;
		}

		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		//$params =& $mainframe->getParams();
		$app =& JFactory::getApplication();
		$params =& $app->getParams();

		// Start a new feed object
		$doc = new JDocumentFeed;
		$doc->link = JRoute::_($this->url);

		// Load the category object
		$section = new KbCategory($this->database);
		$section->load($entry->section);
		
		// Load the category object
		$category = new KbCategory($this->database);
		if ($entry->category) {
			$category->load($entry->category);
		}
		
		// Load the comments
		$bc = new KbComment($this->database);
		$rows = $bc->getAllComments($entry->id);
		
		//$year = JRequest::getInt('year', date("Y"));
		//$month = JRequest::getInt('month', 0);

		// Build some basic RSS document information
		$jconfig =& JFactory::getConfig();
		$doc->title  = $jconfig->getValue('config.sitename').' - '.JText::_(strtoupper($this->_option));
		//$doc->title .= ($year) ? ': '.$year : '';
		//$doc->title .= ($month) ? ': '.sprintf("%02d",$month) : '';
		$doc->title .= ($entry->title) ? ': ' . stripslashes($entry->title) : '';
		$doc->title .= ': '.JText::_('Comments');
		
		$doc->description = JText::sprintf('COM_KB_COMMENTS_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'), stripslashes($entry->title));
		$doc->copyright = JText::sprintf('COM_KB_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		//$doc->category = JText::_('COM_BLOG_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) {
			JPluginHelper::importPlugin('hubzero');
			$dispatcher =& JDispatcher::getInstance();
			
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => '',
				'pagename' => $entry->alias,
				'pageid'   => $entry->id,
				'filepath' => '',
				'domain'   => '' 
			);
			
			$result = $dispatcher->trigger('onGetWikiParser', array($wikiconfig, true));
			$p = (is_array($result) && !empty($result)) ? $result[0] : null;
			
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
				$title = JText::sprintf('Comment by %s', $author).' @ '.JHTML::_('date',$row->created, $timeFormat, $tz).' on '.JHTML::_('date',$row->created, $dateFormat, $tz);
				
				// Strip html from feed item description text
				if ($row->reports) {
					$description = JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
				} else {
					$description = (is_object($p)) ? $p->parse(stripslashes($row->content)) : nl2br(stripslashes($row->content));
				}
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
				/*if ($this->params->get('feed_entries') == 'partial') {
					$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
				}*/

				@$date = ($row->created ? date('r', strtotime($row->created)) : '');
				
				// Load individual item creator class
				$item = new JFeedItem();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = '';
				$item->author      = $author;

				// Loads item info into rss array
				$doc->addItem($item);
				
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
						$title = JText::sprintf('Reply to comment #%s by %s', $row->id, $author).' @ '.JHTML::_('date',$reply->created, $timeFormat, $tz).' on '.JHTML::_('date',$reply->created, $dateFormat, $tz);

						// Strip html from feed item description text
						if ($reply->reports) {
							$description = JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
						} else {
							$description = (is_object($p)) ? $p->parse(stripslashes($reply->content)) : nl2br(stripslashes($reply->content));
						}
						$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
						/*if ($this->params->get('feed_entries') == 'partial') {
							$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
						}*/

						@$date = ($reply->created ? date('r', strtotime($reply->created)) : '');

						// Load individual item creator class
						$item = new JFeedItem();
						$item->title       = $title;
						$item->link        = $link;
						$item->description = $description;
						$item->date        = $date;
						$item->category    = '';
						$item->author      = $author;

						// Loads item info into rss array
						$doc->addItem($item);
						
						if ($reply->replies) 
						{
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
								$title = JText::sprintf('Reply to comment #%s by %s', $reply->id, $author).' @ '.JHTML::_('date',$response->created, $timeFormat, $tz).' on '.JHTML::_('date',$response->created, $dateFormat, $tz);

								// Strip html from feed item description text
								if ($response->reports) {
									$description = JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
								} else {
									$description = (is_object($p)) ? $p->parse(stripslashes($response->content)) : nl2br(stripslashes($response->content));
								}
								$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText($description));
								/*if ($this->params->get('feed_entries') == 'partial') {
									$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0);
								}*/

								@$date = ($response->created ? date('r', strtotime($response->created)) : '');

								// Load individual item creator class
								$item = new JFeedItem();
								$item->title       = $title;
								$item->link        = $link;
								$item->description = $description;
								$item->date        = $date;
								$item->category    = '';
								$item->author      = $author;
								
								// Loads item info into rss array
								$doc->addItem($item);
							}
						}
					}
				}
			}
		}
		
		// Output the feed
		echo $doc->render();
	}
}
