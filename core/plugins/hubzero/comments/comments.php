<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * HUBzero plugin class for displaying comments
 */
class plgHubzeroComments extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * List of allowed extensions
	 *
	 * @var  array
	 */
	private $_allowedExtensions = null;

	/**
	 * Display comments on an object
	 *
	 * @param   object   $obj
	 * @param   unknown  $option
	 * @param   string   $url
	 * @param   unknown  $params
	 * @return  string   HTML
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
		$this->view->option   = $this->option   = $option;
		$this->view->obj      = $this->obj      = $obj;
		$this->view->obj_id   = $this->obj_id   = ($obj instanceof \Hubzero\Base\Model ? $obj->get('id') : $obj->id);
		$this->view->obj_type = $this->obj_type = substr($option, 4);
		$this->view->url      = $this->url      = ($url ? $url : Route::url('index.php?option=' . $this->option . '&id=' . $this->obj_id . '&active=comments'));
		$this->view->depth    = 0;

		$this->_authorize();

		if ($params instanceof \Hubzero\Config\Registry)
		{
			$this->params->merge($params);
			$this->params->set('onCommentMark', $params->get('onCommentMark'));
		}

		$this->comment = new \Plugins\Hubzero\Comments\Models\Comment();

		$this->view->params   = $this->params;
		$this->view->task     = $this->task    = Request::getCmd('action', '');

		switch ($this->task)
		{
			// Feeds
			case 'feed.rss':
			case 'feed':
				$this->_feed();
				break;

			// Entries
			case 'commentsave':
			case 'save':
				$this->_save();
				break;
			case 'commentnew':
			case 'commentedit':
			case 'edit':
				$this->_view();
				break;
			case 'commentdelete':
			case 'delete':
				$this->_delete();
				break;
			case 'view':
				$this->_view();
				break;
			case 'commentvote':
			case 'vote':
				$this->_vote();
				break;

			default:
				$this->_view();
				break;
		}

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Return the Input tag
		return $this->view->loadTemplate();
	}

	/**
	 * Set permissions
	 *
	 * @param   string   $assetType  Type of asset to set permissions for (component, section, category, thread, post)
	 * @param   integer  $assetId    Specific object to check permissions for
	 * @return  void
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

			$d = $this->obj->get('created', $this->obj->get('publish_up'));

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
	 * Show a list of comments
	 *
	 * @return  void
	 */
	protected function _login()
	{
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($this->url)),
			Lang::txt('PLG_HUBZERO_COMMENTS_LOGIN_NOTICE'),
			'warning'
		);
	}

	/**
	 * Vote on a comment
	 *
	 * @return  void
	 */
	protected function _vote()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		$no_html = Request::getInt('no_html', 0);

		// Record the vote
		if ($item_id = Request::getInt('voteup', 0))
		{
			$how = 1;
		}
		else if ($item_id = Request::getInt('votedown', 0))
		{
			$how = -1;
		}

		$item = \Plugins\Hubzero\Comments\Models\Comment::oneOrFail($item_id);

		if (!$item->vote($how))
		{
			$this->setError($item->getError());
		}

		if (!$no_html)
		{
			if ($this->getError())
			{
				Notify::error($this->getError());
			}
			else
			{
				Notify::success(Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_SAVED'));
			}

			App::redirect(
				$this->url
			);
		}

		$item->set('vote', $how);

		$this->view->setLayout('vote');
		$this->view->set('item', $item);
		$this->view->setErrors($this->getErrors());

		// Ugly brute force method of cleaning output
		ob_clean();
		echo $this->view->loadTemplate();
		exit();
	}

	/**
	 * Show a list of comments
	 *
	 * @return  void
	 */
	protected function _view()
	{
		$comments = \Plugins\Hubzero\Comments\Models\Comment::all()
			->whereEquals('item_type', $this->obj_type)
			->whereEquals('item_id', $this->obj_id)
			->whereEquals('parent', 0)
			->whereIn('state', array(
				Plugins\Hubzero\Comments\Models\Comment::STATE_PUBLISHED,
				Plugins\Hubzero\Comments\Models\Comment::STATE_FLAGGED
			))
			->limit($this->params->get('display_limit', 25))
			->ordered()
			->paginated();

		$this->view
			->set('comments', $comments)
			->setErrors($this->getErrors());
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	protected function _save()
	{
		// Ensure the user is logged in
		if (User::isGuest())
		{
			return $this->_login();
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$comment = Request::getArray('comment', array(), 'post');

		// Instantiate a new comment object
		$row = \Plugins\Hubzero\Comments\Models\Comment::oneOrNew($comment['id'])->set($comment);

		if ($row->get('id') && !$this->params->get('access-edit-comment'))
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($this->url)),
				Lang::txt('PLG_HUBZERO_COMMENTS_NOTAUTH'),
				'warning'
			);
		}

		// Store new content
		if (!$row->save())
		{
			User::setState(
				'failed_comment',
				$row->get('content')
			);

			App::redirect(
				$this->url,
				$row->getError(),
				'error'
			);
		}

		$upload = Request::getArray('comment_file', array(), 'files');

		if (!empty($upload) && $upload['name'])
		{
			if ($upload['error'])
			{
				$this->setError(\Lang::txt('PLG_HUBZERO_COMMENTS_ERROR_UPLOADING_FILE'));
			}

			$file = new \Plugins\Hubzero\Comments\Models\File();
			$file->set('comment_id', $row->get('id'));
			$file->setUploadDir($this->params->get('comments_uploadpath', '/site/comments'));

			$fileName = $upload['name'];
			$fileTemp = $upload['tmp_name'];

			if (!$file->upload($fileName, $fileTemp))
			{
				$this->setError($file->getError());
			}
			else
			{
				$file->save();
			}
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
	 * @return  void
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
			App::redirect(
				$this->url
			);
		}

		// Initiate a blog comment object
		$comment = \Plugins\Hubzero\Comments\Models\Comment::oneOrFail($id);

		if (User::get('id') != $comment->get('created_by')
		 && !$this->params->get('access-delete-comment'))
		{
			App::redirect($this->url);
		}

		$comment->set('state', \Plugins\Hubzero\Comments\Models\Comment::STATE_DELETED);

		// Delete the entry itself
		if (!$comment->save())
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
	 * @return  void
	 */
	protected function _feed()
	{
		if (!$this->params->get('comments_feeds'))
		{
			$this->action = 'view';
			$this->_view();
			return;
		}

		// Set the mime encoding for the document
		Document::setType('feed');

		$title = $this->obj->get('title');

		// Start a new feed object
		$doc = Document::instance();
		$doc->link = Route::url($this->url);

		$doc->title  = Config::get('sitename') . ' - ' . Lang::txt(strtoupper($this->_option));
		$doc->title .= ($title) ? ': ' . stripslashes($title) : '';
		$doc->title .= ': ' . Lang::txt('PLG_HUBZERO_COMMENTS');

		$doc->description = Lang::txt('PLG_HUBZERO_COMMENTS_RSS_DESCRIPTION', Config::get('sitename'), stripslashes($title));
		$doc->copyright   = Lang::txt('PLG_HUBZERO_COMMENTS_RSS_COPYRIGHT', date("Y"), Config::get('sitename'));

		$comments = \Plugins\Hubzero\Comments\Models\Comment::all()
			->whereEquals('item_type', $this->obj_type)
			->whereEquals('item_id', $this->obj_id)
			->whereEquals('parent', 0)
			->limit($this->params->get('display_limit', 25))
			->ordered()
			->paginated()
			->rows();

		// Start outputing results if any found
		foreach ($comments as $row)
		{
			// URL link to article
			$link = Route::url('index.php?option=' . $this->_option . '&section=' . $section->alias . '&category=' . $category->alias . '&alias=' . $entry->alias . '#c' . $row->id);

			$author = Lang::txt('JANONYMOUS');
			if (!$row->get('anonymous'))
			{
				$author = $row->creator->get('name');
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
				$description = strip_tags($row->content);
			}

			@$date = ($row->created() ? date('r', strtotime($row->created())) : '');

			// Load individual item creator class
			$item = new \Hubzero\Document\Type\Feed\Item();
			$item->title       = $title;
			$item->link        = $link;
			$item->description = $description;
			$item->date        = $date;
			$item->category    = '';
			$item->author      = $author;

			// Loads item info into rss array
			$doc->addItem($item);

			// Check for any replies
			foreach ($row->replies()->rows() as $reply)
			{
				// URL link to article
				$link = Route::url('index.php?option=' . $this->_option . '&section=' . $section->alias . '&category=' . $category->alias . '&alias=' . $entry->alias . '#c' . $reply->id);

				$author = Lang::txt('JANONYMOUS');
				if (!$reply->anonymous)
				{
					$author = $reply->creator->get('name');
				}

				// Prepare the title
				$title = Lang::txt('PLG_HUBZERO_COMMENTS_REPLY_TO_COMMENT', $row->id, $author) . ' @ ' . Date::of($reply->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . ' ' . Lang::txt('PLG_HUBZERO_COMMENTS_ON') . ' ' . Date::of($reply->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

				// Strip html from feed item description text
				if ($reply->reports)
				{
					$description = Lang::txt('PLG_HUBZERO_COMMENTS_REPORTED_AS_ABUSIVE');
				}
				else
				{
					$description = (is_object($p)) ? $p->parse(stripslashes($reply->content)) : nl2br(stripslashes($reply->content));
				}
				$description = html_entity_decode(\Hubzero\Utility\Sanitize::clean($description));

				@$date = ($reply->created ? gmdate('r', strtotime($reply->created)) : '');

				// Load individual item creator class
				$item = new \Hubzero\Document\Type\Feed\Item();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = '';
				$item->author      = $author;

				// Loads item info into rss array
				$doc->addItem($item);

				foreach ($reply->replies()->rows() as $response)
				{
					// URL link to article
					$link = Route::url('index.php?option=' . $this->_option . '&section=' . $section->alias . '&category=' . $category->alias . '&alias=' . $entry->alias . '#c' . $response->id);

					$author = Lang::txt('JANONYMOUS');
					if (!$response->anonymous)
					{
						$author = $response->creator->get('name');
					}

					// Prepare the title
					$title = Lang::txt('PLG_HUBZERO_COMMENTS_REPLY_TO_COMMENT', $reply->id, $author) . ' @ ' . Date::of($response->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . ' ' . Lang::txt('PLG_HUBZERO_COMMENTS_ON') . ' ' . Date::of($response->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

					// Strip html from feed item description text
					if ($response->reports)
					{
						$description = Lang::txt('PLG_HUBZERO_COMMENTS_REPORTED_AS_ABUSIVE');
					}
					else
					{
						$description = nl2br(stripslashes($response->content));
					}
					$description = html_entity_decode(\Hubzero\Utility\Sanitize::clean($description));

					@$date = ($response->created ? gmdate('r', strtotime($response->created)) : '');

					// Load individual item creator class
					$item = new \Hubzero\Document\Type\Feed\Item();
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

		// Output the feed
		echo $doc->render();
	}
}
