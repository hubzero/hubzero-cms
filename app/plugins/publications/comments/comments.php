<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications plugin class for displaying comments
 */
class plgPublicationsComments extends \Hubzero\Plugin\Plugin
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
	 * Return the alias and name for this category of content
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function &onPublicationAreas($publication, $version = 'default', $extended = true) // $model)
	{
		$areas = array();

		if ($publication->_category->_params->get('plg_comments') && $extended)
		{
			$areas['comments'] = Lang::txt('PLG_PUBLICATIONS_COMMENTS');
		}

		return $areas;
	}

	/**
	 * Return data on a publication view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true) // $model, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onPublicationAreas($publication))
			 && !array_intersect($areas, array_keys($this->onPublicationAreas($publication))))
			{
				$rtrn = 'metadata';
			}
		}
		if (!$publication->_category->_params->get('plg_comments') || !$extended)
		{
			return $arr;
		}

		// Initialize the bits
		$this->obj = $publication;
		$this->option = $option;
		$this->obj_id   = $publication->version->id;
		$this->obj_type = substr($option, 4);
		$this->url = $publication->link('version') . '&active=' . $this->_name;

		include_once __DIR__ . '/models/comment.php';

		$arr['name']  = 'comments';
		$arr['count'] = $this->_countComments();

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$arr['metadata'] = $this->view('default', 'metadata')
				->set('url', Route::url($this->url . '#comments'))
				->set('url_action', Route::url($this->url . '#commentform'))
				->set('comments', $arr['count'])
				->loadTemplate();
		}

		// Are we returning any HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$this->view = $this->view('default', 'view');
			$this->view->option   = $this->option;
			$this->view->obj      = $this->obj;
			$this->view->obj_id   = $this->obj_id;
			$this->view->obj_type = $this->obj_type;
			$this->view->url      = $this->url;
			$this->view->sortby   = $this->sortby   = Request::getVar('sortby', 'created');	
			$this->view->depth    = 0;

			$this->_authorize();

			$this->comment = new Plugins\Publications\Comments\Models\Comment();

			$this->view->params   = $this->params;
			$this->view->task     = $this->task    = Request::getVar('action', '');

			switch ($this->task)
			{
				// Feeds
				case 'feed.rss':   $this->_feed();   break;
				case 'feed':       $this->_feed();   break;

				// Entries
				case 'new':
				case 'reply':
				case 'edit':       $this->_save();   break;
				case 'commentdelete':
				case 'delete':     $this->_delete(); break;
				case 'view':       $this->_view();   break;
				case 'commentvote':
				case 'vote':       $this->_vote();   break;

				default:           $this->_view();   break;
			}

			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}

			// Return the Input tag
			$arr['html'] = $this->view->loadTemplate();
		}

		return $arr;
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
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->url, false, true))),
			Lang::txt('PLG_PUBLICATIONS_COMMENTS_LOGIN_NOTICE'),
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

		$item = Plugins\Publications\Comments\Models\Comment::oneOrFail($item_id);

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
				Notify::success(Lang::txt('PLG_PUBLICATIONS_COMMENTS_VOTE_SAVED'));
			}

			App::redirect(
				Route::url($this->url)
			);
		}

		$this->view->set('item', $item)
				   ->set('no_html', $no_html)
				   ->setLayout('vote')
				   ->setErrors($this->getErrors());

		// Ugly brute force method of cleaning output
		ob_clean();
		echo $this->view->loadTemplate();
		exit();
	}

	/**
	 * Get number of comments
	 * 
	 * @return	integer
	 */
	protected function _countComments()
	{
		return Plugins\Publications\Comments\Models\Comment::all()
			->whereEquals('item_type', $this->obj_type)
			->whereEquals('item_id', $this->obj_id)
			->whereIn('state', array(
				Plugins\Publications\Comments\Models\Comment::STATE_PUBLISHED,
				Plugins\Publications\Comments\Models\Comment::STATE_FLAGGED,
				Plugins\Publications\Comments\Models\Comment::STATE_DELETED
			))
			->whereIn('access', User::getAuthorisedViewLevels())
			->count();
	}

	/**
	 * Show a list of comments
	 *
	 * @return  void
	 */
	protected function _view()
	{
		$no_html = Request::getInt('no_html', 0);

		$comments = Plugins\Publications\Comments\Models\Comment::all()
			->whereEquals('item_type', $this->obj_type)
			->whereEquals('item_id', $this->obj_id)
			->whereEquals('parent', 0)
			->whereIn('state', array(
				Plugins\Publications\Comments\Models\Comment::STATE_PUBLISHED,
				Plugins\Publications\Comments\Models\Comment::STATE_FLAGGED,
				Plugins\Publications\Comments\Models\Comment::STATE_DELETED
			))
			->whereIn('access', User::getAuthorisedViewLevels()); 			// ->limit($this->params->get('display_limit', 25))

		if ($this->sortby == 'likes') {
			$comments = $comments->order('state', 'asc')
			                     ->order('positive', 'desc');
		}
		
		$comments = $comments->order('created', 'desc');

		$this->view
			->set('comments', $comments)
			->set('params', $this->params)
			->setErrors($this->getErrors());

		if (!$no_html) {
			return $this->view;
		} else {
			// Ugly brute force method of cleaning output
			ob_clean();
			$this->view->setLayout('list');
			echo $this->view->loadTemplate();
			exit();
		}
	}

	/**
	 * Validate a submitted entry
	 * TODO: Put in code to check that comment id (if exists) belongs to correct publication
	 *   and parent is correct.
	 * 
	 * @return  void
	 */
	protected function _validate(&$comment, $row)
	{
		$comment['item_id'] = $this->obj_id;
		$comment['item_type'] = $this->obj_type;
		$comment['state'] = 1;
		$comment['access'] = 1;
		$comment['created_by'] = ($this->task != 'edit' ? User::get('id') : $row->get('created_by'));
		$comment['anonymous'] = (array_key_exists('anonymous', $comment) && $comment['anonymous'] ? 1 : 0);
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
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);
		$row = Plugins\Publications\Comments\Models\Comment::oneOrNew($comment['id']);
		$this->_validate($comment, $row);
		$row->set($comment);

		if ($row->get('id') && !$this->params->get('access-edit-comment'))
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->url, false, true))),
				Lang::txt('PLG_PUBLICATIONS_COMMENTS_NOTAUTH'),
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
				Route::url($this->url),
				$row->getError(),
				'error'
			);
		}

		$upload = Request::getVar('comment_file', '', 'files', 'array');
		$file_exists = $row->files()->rows();
		if (!empty($upload) && $upload['name'])
		{
			if ($upload['error'])
			{
				$this->setError(\Lang::txt('PLG_PUBLICATIONS_COMMENTS_ERROR_UPLOADING_FILE'));
			}

			$file = new Plugins\Publications\Comments\Models\File();
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
				// Replace file
				if ($file_exists->count()) {
					// Delete file on server
					$file_exists->first()->destroy();
				}
				$file->save();
			}
		} else {
			// Remove file
			if ($file_exists->count() && ($comment['filename'] == '')) {
				$file_exists->first()->destroy();
			}
		}

		// Record the activity
		$recipients = array(
			['publication', $row->get('item_id')]//,
		);
		$users = array();
		foreach ($this->obj->authors() as $author)
		{
			$profile = User::getInstance($author->user_id);
			if ($profile->get('id') && !in_array($profile->get('id'), $users))
			{
				$users[] = $profile->get('id');
				$recipients[] = ['user', $profile->get('id')];
			}
		}
		if (($row->get('created_by') != $this->obj->created_by) && !in_array($this->obj->created_by, $users))
		{
			$users[] = $this->obj->created_by;
			$recipients[] = ['user', $this->obj->created_by];
		}
		if ($row->get('parent') && !in_array($row->parent()->get('created_by'), $users))
		{
			$users[] = $row->parent()->get('created_by');
			$recipients[] = ['user', $row->parent()->get('created_by')];
		}
		// Don't message yourself (although can send to activity feed/log file)
		$users = array_diff($users, [User::get('id')]);

		$url = Route::url('index.php?option=' . $this->option . '&id=' . $this->obj->get('id') . '&v=' . $this->obj->get('version_number') . '&active=comments#c' . $row->get('id'));

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($comment['id'] ? 'updated' : 'created'),
				'scope'       => 'publication.comment',
				'scope_id'    => $row->get('id'),
				'anonymous'   => $row->get('anonymous', 0),
				'description' => Lang::txt(
					'PLG_PUBLICATIONS_COMMENTS_' . ($comment['id'] ? 'UPDATED' : 'CREATED'),
					$row->get('id'),
					'<a href="' . Route::url($url) . '">' . $this->obj->title . '</a>'
				),
				'details'     => array(
					'id'        => $row->get('id'),
					'item_id'   => $row->get('item_id'),
					'item_type' => $row->get('item_type'),
					'url'       => Route::url($url)
				)
			],
			'recipients' => $recipients
		]);

		// Email notification?
		if ($this->params->get('comments_email', 1))
		{
			$from = array(
				'name'  => (!$row->get('anonymous') ? $row->creator->get('name', Lang::txt('PLG_PUBLICATIONS_COMMENTS_UNKNOWN')) : Lang::txt('PLG_PUBLICATIONS_COMMENTS_ANONYMOUS')) . ' @ ' . Config::get('sitename'),
				'email' => Config::get('mailfrom'),
				'replytoname'  => Config::get('sitename'),
				'replytoemail' => Config::get('mailfrom')
			);

			$msg = array();

			$eview = new Hubzero\Mail\View(array(
				'base_path' => __DIR__,
				'name'      => 'email',
				'layout'    => 'comment_plain'
			));

			// plain text
			$eview
				->set('option', $this->option)
				->set('comment', $row)
				->set('publication', $this->obj);

			$plain = $eview->loadTemplate(false);
			$msg['plaintext'] = str_replace("\n", "\r\n", $plain);

			// HTML
			$eview->setLayout('comment_html');
			$html = $eview->loadTemplate();
			$msg['multipart'] = str_replace("\n", "\r\n", $html);

			$subject = Lang::txt('PLG_PUBLICATIONS_COMMENTS') . ': ' . $this->obj->title;

			if (!Event::trigger('xmessage.onSendMessage', array('publications_new_comment', $subject, $msg, $from, $users, $this->option)))
			{
				Notify::error(Lang::txt('PLG_PUBLICATIONS_COMMENTS_ERROR_EMAIL_MEMBERS_FAILED'));
			}
		}

		$this->_view();
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
		$comment = Plugins\Publications\Comments\Models\Comment::oneOrFail($id);

		if (User::get('id') != $comment->get('created_by')
		 && !$this->params->get('access-delete-comment'))
		{
			App::redirect(
				Route::url($this->url)
			);
		}

		$comment->set('state', Plugins\Publications\Comments\Models\Comment::STATE_DELETED);

		// Delete the entry itself
		if (!$comment->save())
		{
			$this->setError($comment->getError());
		}
		else
		{
			// Record the activity
			$recipients = array(
				['publication', $comment->get('item_id')],
				['user', $comment->get('created_by')]
			);
			if ($comment->get('created_by') != $this->obj->created_by)
			{
				$recipients[] = ['user', $this->obj->created_by];
			}

			$url = Route::url('index.php?option=' . $this->option . '&id=' . $comment->get('item_id') . '&active=comments#c' . $comment->get('id'));

			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'deleted',
					'scope'       => 'publication.comment',
					'scope_id'    => $comment->get('id'),
					'description' => Lang::txt(
						'PLG_PUBLICATIONS_COMMENTS_DELETED',
						$comment->get('id'),
						'<a href="' . Route::url($url) . '">' . $this->obj->title . '</a>'
					),
					'details'     => array(
						'id'        => $comment->get('id'),
						'item_id'   => $comment->get('item_id'),
						'item_type' => $comment->get('item_type'),
						'url'       => Route::url($url)
					)
				],
				'recipients' => $recipients
			]);
		}

		$this->_view();
	}
}
