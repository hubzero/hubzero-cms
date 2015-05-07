<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HUBzero plugin class for displaying comments
 */
class plgHubzeroComments extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * List of allowed extensions
	 *
	 * @var array
	 */
	private $_allowedExtensions = null;

	/**
	 * Display comments on an object
	 *
	 * @param      type    $objType    Object type to pull comments for
	 * @param      integer $objId      Object ID to pull comments for
	 * @param      string  $authorized Authorization level
	 * @return     string HTML
	 */
	public function onAfterDisplayContent($obj, $option, $url=null, $params = null)
	{
		// Ensure we have needed vars
		if (!is_object($obj))
		{
			return '';
		}

		include_once __DIR__ . DS . 'models' . DS . 'comment.php';

		$this->view = $this->view('default', 'view');
		$this->view->database = $this->database = JFactory::getDBO();
		$this->view->option   = $this->option   = $option;
		$this->view->obj      = $this->obj      = $obj;
		$this->view->obj_id   = $this->obj_id   = ($obj instanceof \Hubzero\Base\Model ? $obj->get('id') : $obj->id);
		$this->view->obj_type = $this->obj_type = substr($option, 4);
		$this->view->url      = $this->url      = ($url ? $url : Route::url('index.php?option=' . $this->option . '&id=' . $this->obj_id . '&active=comments'));
		$this->view->depth    = 0;

		$this->_authorize();

		if ($params instanceof JRegistry)
		{
			$this->params->merge($params);
			$this->params->set('onCommentMark', $params->get('onCommentMark'));
		}

		$this->view->params   = $this->params;

		// set allowed Extensions
		// defaults to set of image extensions defined in \Hubzero\Item\Comment
		//$this->comment = new \Hubzero\Item\Comment($this->database);
		//$this->comment->setAllowedExtensions($allowedExtensions);
		$this->comment = new \Plugins\Hubzero\Comments\Models\Comment();

		$this->view->task     = $this->task    = Request::getVar('action', '');

		switch ($this->task)
		{
			// Feeds
			case 'feed.rss': $this->_feed();   break;
			case 'feed':     $this->_feed();   break;

			// Entries
			case 'commentsave':
			case 'save':     $this->_save();   break;
			//case 'new':      $this->_view();   break;
			case 'commentnew':
			case 'commentedit':
			case 'edit':     $this->_view();   break;
			case 'commentdelete':
			case 'delete':   $this->_delete(); break;
			case 'view':     $this->_view();   break;
			case 'commentvote':
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
		if (!User::isGuest())
		{
			// Set comments to viewable
			$this->params->set('access-view-' . $assetType, true);

			$asset  = $this->option;
			if ($assetId)
			{
				$asset .= ($assetType != 'comment') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			// Are they an admin?
			$this->params->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->params->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			if ($this->params->get('access-admin-' . $assetType)
			 || $this->params->get('access-manage-' . $assetType))
			{
				$this->params->set('access-create-' . $assetType, true);
				$this->params->set('access-delete-' . $assetType, true);
				$this->params->set('access-edit-' . $assetType, true);
				return;
			}

			if ($this->obj instanceof \Hubzero\Base\Model)
			{
				$d = $this->obj->get('created', $this->obj->get('publish_up'));
			}
			else
			{
				if (isset($this->obj->publish_up) && $this->obj->publish_up)
				{
					$d = $this->obj->publish_up;
				}
				else
				{
					$d = $this->obj->created;
				}
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
					$dt = mktime(0, 0, 0, $month, $day, $year);
				break;
			}

			$pdt = strftime('Y', $dt) . '-' . strftime('m', $dt) . '-' . strftime('d', $dt) . ' 00:00:00';
			$today = Date::toSql();

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
	 * @param   string $message The message to add
	 * @return  void
	 */
	public function redirect($url, $msg='', $msgType='')
	{
		$url = ($url != '') ? $url : Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->option . '&id=' . $this->obj_id . '&active=comments'), 'server');

		parent::redirect($url, $msg, $msgType);
	}

	/**
	 * Show a list of comments
	 *
	 * @return    void
	 */
	protected function _login()
	{
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($this->url)),
			Lang::txt('PLG_HUBZERO_COMMENTS_LOGIN_NOTICE'),
			'warning'
		);
		return;
	}

	/**
	 * Vote on a comment
	 *
	 * @return    void
	 */
	protected function _vote()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		$no_html = Request::getInt('no_html', 0);

		// Get comments on this article
		$v = new \Hubzero\Item\Vote($this->database);
		$v->created_by = User::get('id');
		$v->item_type  = 'comment';
		//$v->item_id    = Request::getInt('comment', 0);
		//$v->vote       = Request::getVar('vote', 'up');
		if ($item_id = Request::getInt('voteup', 0))
		{
			$v->vote    = 1;
		}
		else if ($item_id = Request::getInt('votedown', 0))
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
			App::redirect(
				$this->url,
				$this->getError(),
				'error'
			);
			return;
		}

		$this->view->setLayout('vote');

		$this->view->item = new \Hubzero\Item\Comment($this->database);
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
			App::redirect(
				$this->url,
				Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_SAVED'),
				'message'
			);
			return;
		}

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
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
		/*$this->view->comments = $this->comment->getComments(
			$this->obj_type,
			$this->obj_id,
			0,
			$this->params->get('comments_limit', 25)
		);*/
		$this->view->comments = $this->comment->replies('list', array(
			'item_type' => $this->obj_type,
			'item_id'   => $this->obj_id,
			'limit'     => $this->params->get('comments_limit', 25)
		));

		// get the accepted file types
		//$this->view->extensions = $this->comment->getAllowedExtensions();

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
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
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		// Instantiate a new comment object
		$row = new \Plugins\Hubzero\Comments\Models\Comment($comment['id']);

		// pass data to comment object
		if (!$row->bind($comment))
		{
			App::redirect(
				$this->url,
				$row->getError(),
				'error'
			);
			return;
		}
		$row->set('uploadDir', $this->params->get('comments_uploadpath', '/site/comments'));
		$row->set('created', Date::toSql());

		if ($row->exists() && !$this->params->get('access-edit-comment'))
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($this->url)),
				Lang::txt('PLG_HUBZERO_COMMENTS_NOTAUTH'),
				'warning'
			);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$key   = 'failed_comment';
			$value = $row->content('raw');
			JFactory::getApplication()->setUserState($key, $value);

			App::redirect(
				$this->url,
				$row->getError(),
				'error'
			);
			return;
		}

		App::redirect(
			$this->url,
			Lang::txt('PLG_HUBZERO_COMMENTS_SAVED'),
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
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Incoming
		$id = Request::getInt('comment', 0);
		if (!$id)
		{
			return $this->_redirect();
		}

		// Initiate a blog comment object
		$comment = new \Plugins\Hubzero\Comments\Models\Comment($id);

		if (User::get('id') != $comment->get('created_by')
		 && !$this->params->get('access-delete-comment'))
		{
			App::redirect($this->url);
			return;
		}

		$comment->set('state', 2);

		// Delete the entry itself
		if (!$comment->store())
		{
			$this->setError($comment->getError());
		}

		App::redirect(
			$this->url,
			Lang::txt('PLG_HUBZERO_COMMENTS_REMOVED'),
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

		include_once(PATH_CORE . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');

		// Set the mime encoding for the document
		$jdoc = JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Load the comments
		$comment = new \Plugins\Hubzero\Comments\Models\Comment();
		$filters = array(
			'parent'    => 0,
			'item_type' => $this->obj_type,
			'item_id'   => $this->obj_id
		);

		if ($this->obj instanceof \Hubzero\Base\Model)
		{
			$title = $this->obj->get('title');
		}
		else
		{
			$title = $this->obj->title;
		}

		// Start a new feed object
		$doc = new JDocumentFeed;
		$doc->link = Route::url($this->url);

		$doc->title  = Config::get('sitename') . ' - ' . Lang::txt(strtoupper($this->_option));
		$doc->title .= ($title) ? ': ' . stripslashes($title) : '';
		$doc->title .= ': ' . Lang::txt('PLG_HUBZERO_COMMENTS');

		$doc->description = Lang::txt('PLG_HUBZERO_COMMENTS_RSS_DESCRIPTION',Config::get('sitename'), stripslashes($title));
		$doc->copyright   = Lang::txt('PLG_HUBZERO_COMMENTS_RSS_COPYRIGHT', date("Y"), Config::get('sitename'));

		// Start outputing results if any found
		if ($comment->replies('list', $filters)->total() > 0)
		{
			foreach ($comment->replies() as $row)
			{
				// URL link to article
				$link = Route::url('index.php?option=' . $this->_option . '&section=' . $section->alias . '&category=' . $category->alias . '&alias=' . $entry->alias . '#c' . $row->id);

				$author = Lang::txt('PLG_HUBZERO_COMMENTS_ANONYMOUS');
				if (!$row->get('anonymous'))
				{
					$author = $row->creator('name');
				}

				// Prepare the title
				$title = Lang::txt('PLG_HUBZERO_COMMENTS_COMMENT_BY', $author) . ' @ ' . $row->created('time') . ' on ' . $row->created('date');

				// Strip html from feed item description text
				if ($row->isReported())
				{
					$description = Lang::txt('PLG_HUBZERO_COMMENTS_REPORTED_AS_ABUSIVE');
				}
				else
				{
					$description = $row->content('clean');
				}

				@$date = ($row->created() ? date('r', strtotime($row->created())) : '');

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
				if ($row->replies()->total())
				{
					foreach ($row->replies() as $reply)
					{
						// URL link to article
						$link = Route::url('index.php?option=' . $this->_option . '&section=' . $section->alias . '&category=' . $category->alias . '&alias=' . $entry->alias . '#c' . $reply->id);

						$author = Lang::txt('COM_KB_ANONYMOUS');
						if (!$reply->anonymous)
						{
							$cuser  = User::getInstance($reply->created_by);
							$author = $cuser->get('name');
						}

						// Prepare the title
						$title = Lang::txt('Reply to comment #%s by %s', $row->id, $author) . ' @ ' . Date::of($reply->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . ' on ' . Date::of($reply->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

						// Strip html from feed item description text
						if ($reply->reports)
						{
							$description = Lang::txt('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
						}
						else
						{
							$description = (is_object($p)) ? $p->parse(stripslashes($reply->content)) : nl2br(stripslashes($reply->content));
						}
						$description = html_entity_decode(\Hubzero\Utility\Sanitize::clean($description));
						/*if ($this->params->get('feed_entries') == 'partial')
						{
							$description = \Hubzero\Utility\String::truncate($description, 300);
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
								$link = Route::url('index.php?option=' . $this->_option . '&section=' . $section->alias . '&category=' . $category->alias . '&alias=' . $entry->alias . '#c' . $response->id);

								$author = Lang::txt('COM_KB_ANONYMOUS');
								if (!$response->anonymous)
								{
									$cuser  = User::getInstance($response->created_by);
									$author = $cuser->get('name');
								}

								// Prepare the title
								$title = Lang::txt('Reply to comment #%s by %s', $reply->id, $author) . ' @ ' . Date::of($response->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . ' on ' . Date::of($response->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

								// Strip html from feed item description text
								if ($response->reports)
								{
									$description = Lang::txt('COM_KB_COMMENT_REPORTED_AS_ABUSIVE');
								}
								else
								{
									$description = (is_object($p)) ? $p->parse(stripslashes($response->content)) : nl2br(stripslashes($response->content));
								}
								$description = html_entity_decode(\Hubzero\Utility\Sanitize::clean($description));
								/*if ($this->params->get('feed_entries') == 'partial')
								{
									$description = \Hubzero\Utility\String::truncate($description, 300, 0);
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
