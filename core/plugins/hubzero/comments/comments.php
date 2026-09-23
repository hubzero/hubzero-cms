<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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

			$d = $this->obj->get('created', $this->obj->get('publish_up',''));

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

			$pdt = date('Y', $dt) . '-' . date('m', $dt) . '-' . date('d', $dt) . ' 00:00:00';
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

		// Record the vote. $how and $item_id were left undefined when neither
		// field was posted, which on this hub is a warning and so a 500.
		$item_id = 0;
		$how     = 0;

		if ($id = Request::getInt('voteup', 0))
		{
			$item_id = $id;
			$how     = 1;
		}
		else if ($id = Request::getInt('votedown', 0))
		{
			$item_id = $id;
			$how     = -1;
		}

		if (!$item_id)
		{
			App::abort(404, Lang::txt('PLG_HUBZERO_COMMENTS_NOTAUTH'));
		}

		$item = \Plugins\Hubzero\Comments\Models\Comment::oneOrFail($item_id);

		// The comment has to belong to the item this plugin instance is serving.
		// oneOrFail() resolves any row of #__item_comments, so without this any
		// logged-in member could vote on any comment on the hub by id -- the same
		// gap _save() and _delete() were given a test for.
		if ((string) $item->get('item_type') !== (string) $this->obj_type
		 || (int) $item->get('item_id') !== (int) $this->obj_id)
		{
			App::abort(404, Lang::txt('PLG_HUBZERO_COMMENTS_NOTAUTH'));
		}

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
		// Get the plugin parameter for the default sorting (Newest first vs Oldest first)
		$defaultSorting = $this->params->get('comments_sorting');

		// Make sure we have a valid value
		if ($defaultSorting != 'desc')
		{
			$defaultSorting = 'asc';
		}

		$comments = \Plugins\Hubzero\Comments\Models\Comment::all();
		$comments->orderDir = $defaultSorting;
		$comments->whereEquals('item_type', $this->obj_type)
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

		// Instantiate a comment object
		$cid = isset($comment['id']) ? (int) $comment['id'] : 0;
		$row = \Plugins\Hubzero\Comments\Models\Comment::oneOrNew($cid);

		$__isNew = $row->isNew();

		if (!$__isNew)
		{
			// The comment has to belong to the item this plugin instance is
			// serving, exactly as _delete() requires. oneOrNew() resolves any row
			// of #__item_comments, and access-edit-comment is set for EVERY
			// logged-in user straight from the comments_editable parameter with
			// no per-row test in _authorize() -- so without this, one enabled
			// comments plugin let any member edit any comment on the hub.
			if ((string) $row->get('item_type') !== (string) $this->obj_type
			 || (int) $row->get('item_id') !== (int) $this->obj_id)
			{
				App::redirect($this->url);
			}

			if (!$this->params->get('access-edit-comment')
			 || ($row->get('created_by') != User::get('id') && !$this->params->get('access-admin-comment') && !$this->params->get('access-manage-comment')))
			{
				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($this->url)),
					Lang::txt('PLG_HUBZERO_COMMENTS_NOTAUTH'),
					'warning'
				);
			}

			// What the comment hangs off, who wrote it, when, and whether it has
			// been reported are not the submitter's to change: all are hidden
			// inputs on the form. state matters most -- STATE_FLAGGED is what
			// "report abuse" sets, so leaving it bound let an author clear the
			// flag on their own comment.
			unset($comment['item_type'], $comment['item_id'], $comment['created_by'],
				$comment['state'], $comment['parent'], $comment['created']);
		}

		$row->set($comment);

		if ($__isNew)
		{
			// Only where the form is offered: access-create-comment is what
			// comments_close withdraws, and the view shows the form only on it.
			if (!$this->params->get('access-create-comment'))
			{
				App::redirect($this->url, Lang::txt('PLG_HUBZERO_COMMENTS_NOTAUTH'), 'warning');
			}

			// A reply's parent has to be a comment on this same item.
			if ($row->get('parent'))
			{
				$__parent = \Plugins\Hubzero\Comments\Models\Comment::oneOrNew((int) $row->get('parent'));
				if ($__parent->isNew()
				 || (string) $__parent->get('item_type') !== (string) $this->obj_type
				 || (int) $__parent->get('item_id') !== (int) $this->obj_id)
				{
					App::redirect($this->url);
				}
			}

			// item_type/item_id are hidden inputs too, so a new comment could
			// otherwise be attached to any item of any type -- including one with
			// comments disabled or not visible to the caller. Pin both to the
			// item this plugin instance is actually serving. This must NOT run on
			// the edit path: there it would overwrite the stored item that the
			// unset() above exists to preserve, relocating someone's comment onto
			// whatever thread the request happened to be made from.
			$row->set('item_type', $this->obj_type);
			$row->set('item_id', $this->obj_id);
			$row->set('created_by', User::get('id'));
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

		// The comment has to belong to the item this plugin instance is serving.
		// oneOrFail() resolves any row of #__item_comments, and
		// access-delete-comment is set for EVERY logged-in user straight from
		// the comments_deletable parameter, with no
		// per-row test in _authorize() -- so without this, one enabled
		// comments plugin let any member delete any comment on the hub.
		if ((string) $comment->get('item_type') !== (string) $this->obj_type
		 || (int) $comment->get('item_id') !== (int) $this->obj_id)
		{
			App::redirect($this->url);
		}

		// The rule item.php renders the Delete link on: the author, where the
		// item's comments are deletable, or a manager. access-delete-comment
		// alone is every logged-in user (see above), so testing it OR authorship
		// let any member delete anyone's comment on a deletable item.
		if (!$this->params->get('access-manage-comment')
		 && !$this->params->get('access-admin-comment')
		 && !($this->params->get('access-delete-comment') && User::get('id') == $comment->get('created_by')))
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
