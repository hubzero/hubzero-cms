<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Story\Models\Comment;
use Components\Story\Models\Discussion;
use Components\Story\Models\Preference;
use Components\Story\Models\Story;
use Components\Story\Helpers\Thread;
use Components\Story\Helpers\Context;
use Document;
use Pathway;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'comment.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'discussion.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'preference.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'thread.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'context.php';

/**
 * A story's discussion, and what a reader may do in it
 */
class Comments extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'display');
		$this->registerTask('add', 'new');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * The discussion the request is about
	 *
	 * @return  object
	 */
	protected function discussion()
	{
		$id = Request::getInt('discussion', 0);

		$row = Discussion::oneOrNew($id);

		if (!$row->get('id'))
		{
			App::abort(404, Lang::txt('COM_STORY_DISCUSSION_NOT_FOUND'));
		}

		return $row;
	}

	/**
	 * Whether the section this discussion sits in takes anonymous comment
	 *
	 * Off unless a section says otherwise. Karma needs a stable identity, and
	 * an anonymous comment correctly moves nobody's — so a hub that turns this
	 * on for a section is choosing to thin the signal there, deliberately.
	 *
	 * @param   object   $discussion
	 * @return  boolean
	 */
	protected function allowsAnonymous($discussion)
	{
		$story = Story::oneOrNew($discussion->get('story_id'));

		if (!$story->get('id'))
		{
			return false;
		}

		$section = $story->section();

		if (!$section || !$section->get('id'))
		{
			return false;
		}

		return (bool) $section->params->get('allow_anonymous', 0);
	}

	/**
	 * Why this reader may not comment here, or an empty string if they may
	 *
	 * Returning the reason rather than a bare false is what lets the view say
	 * something useful instead of hiding the form and leaving the reader to
	 * guess.
	 *
	 * @param   object   $discussion
	 * @return  string
	 */
	protected function refusal($discussion)
	{
		if (!$discussion->isOpen())
		{
			return Lang::txt('COM_STORY_COMMENTS_CLOSED');
		}

		if ($discussion->requiresLogin() && User::isGuest())
		{
			return Lang::txt('COM_STORY_COMMENTS_NEED_LOGIN');
		}

		if (!User::authorise('core.create', $this->_option))
		{
			return Lang::txt('COM_STORY_COMMENTS_NOT_PERMITTED');
		}

		if ($discussion->get('comment_status') == Discussion::COMMENTS_KARMA_GATED)
		{
			// A null allowance means nobody has seeded the gate, so there is
			// nothing to enforce and the setting degrades to "signed in".
			$allowed = \Hubzero\Karma\Karma::gate('com_story.comments_per_day', User::get('id'));

			if ($allowed !== null && $this->postedToday() >= (int) $allowed)
			{
				return Lang::txt('COM_STORY_COMMENTS_RATE_LIMITED', (int) $allowed);
			}
		}

		return '';
	}

	/**
	 * How many comments this reader has made since midnight
	 *
	 * @return  integer
	 */
	protected function postedToday()
	{
		if (User::isGuest())
		{
			return 0;
		}

		return Comment::all()
			->whereEquals('created_by', (int) User::get('id'))
			->where('created', '>=', \Hubzero\Utility\Date::of('now')->format('Y-m-d') . ' 00:00:00')
			->count();
	}

	/**
	 * Breadcrumbs back to the story
	 *
	 * @param   object  $discussion
	 * @return  object  the story, which the caller usually wants anyway
	 */
	protected function buildPathway($discussion)
	{
		$story = Story::oneOrNew($discussion->get('story_id'));

		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_STORY'), 'index.php?option=' . $this->_option);
		}

		if ($story->get('id'))
		{
			Pathway::append($story->get('title'), $story->link());
		}

		return $story;
	}

	/**
	 * The discussion on its own page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$discussion = $this->discussion();
		$story      = $this->buildPathway($discussion);

		$preference = $this->preference();
		$context    = new Context($this->config);

		Document::setTitle($discussion->get('title'));

		$this->view
			->set('discussion', $discussion)
			->set('story', $story)
			->set('preference', $preference)
			->set('context', $context)
			->set('thread', Thread::build($discussion->get('id'), $preference, $context, Request::getInt('comment', 0)))
			->set('refusal', $this->refusal($discussion))
			->set('anonymous', $this->allowsAnonymous($discussion))
			->set('config', $this->config)
			->display();
	}

	/**
	 * The reader's preferences, stored or default
	 *
	 * @return  object
	 */
	protected function preference()
	{
		$preference = Preference::forUser(User::get('id'));

		// A mode in the address wins for this page view only, so a reader can
		// try one without committing to it.
		if ($mode = Request::getWord('mode', ''))
		{
			if (in_array($mode, Preference::modes()))
			{
				$preference->set('mode', $mode);
			}
		}

		return $preference;
	}

	/**
	 * The form for a new comment or a reply
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$discussion = $this->discussion();
		$story      = $this->buildPathway($discussion);

		if ($refusal = $this->refusal($discussion))
		{
			App::redirect(Route::url($this->discussionLink($discussion)), $refusal, 'warning');

			return;
		}

		$row = Comment::blank();
		$row->set('discussion_id', $discussion->get('id'));
		$row->set('parent', Request::getInt('parent', 0));

		$this->view
			->set('discussion', $discussion)
			->set('story', $story)
			->set('row', $row)
			->set('anonymous', $this->allowsAnonymous($discussion))
			->set('config', $this->config)
			->setLayout('edit')
			->display();
	}

	/**
	 * Edit one already said
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!is_object($row))
		{
			$row = Comment::oneOrFail(Request::getInt('comment', 0));
		}

		$discussion = Discussion::oneOrNew($row->get('discussion_id'));
		$story      = $this->buildPathway($discussion);

		if (!$this->canEdit($row))
		{
			App::abort(403, Lang::txt('COM_STORY_COMMENT_NOT_YOURS'));
		}

		$this->view
			->set('discussion', $discussion)
			->set('story', $story)
			->set('row', $row)
			->set('anonymous', $this->allowsAnonymous($discussion))
			->set('config', $this->config)
			->setLayout('edit')
			->display();
	}

	/**
	 * Whether this reader may change a comment
	 *
	 * @param   object   $row
	 * @return  boolean
	 */
	protected function canEdit($row)
	{
		if (User::authorise('core.edit', $this->_option))
		{
			return true;
		}

		if (User::isGuest() || $row->isDeleted())
		{
			return false;
		}

		return (User::authorise('core.edit.own', $this->_option)
			&& $row->get('created_by') == User::get('id'));
	}

	/**
	 * Put one in the discussion
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		$fields = Request::getArray('fields', array(), 'post');

		$row        = Comment::oneOrNew(isset($fields['id']) ? (int) $fields['id'] : 0);
		$discussion = Discussion::oneOrNew($row->isNew()
			? (isset($fields['discussion_id']) ? (int) $fields['discussion_id'] : 0)
			: $row->get('discussion_id'));

		if (!$discussion->get('id'))
		{
			App::abort(404, Lang::txt('COM_STORY_DISCUSSION_NOT_FOUND'));
		}

		if ($row->isNew())
		{
			if ($refusal = $this->refusal($discussion))
			{
				App::redirect(Route::url($this->discussionLink($discussion)), $refusal, 'warning');

				return;
			}
		}
		elseif (!$this->canEdit($row))
		{
			App::abort(403, Lang::txt('COM_STORY_COMMENT_NOT_YOURS'));
		}

		// Only these are the reader's to set. The score columns in particular
		// are nobody's business from a form.
		$row->set(array(
			'discussion_id' => $discussion->get('id'),
			'subject'       => isset($fields['subject']) ? $fields['subject'] : '',
			'comment'       => isset($fields['comment']) ? $fields['comment'] : ''
		));

		if ($row->isNew())
		{
			$row->set('parent', isset($fields['parent']) ? (int) $fields['parent'] : 0);
			$row->set('state', Comment::STATE_PUBLISHED);

			if ($this->allowsAnonymous($discussion) && !empty($fields['anonymous']))
			{
				$row->set('anonymous', 1);
			}

			// Authorship first. `created_by` is an automatic, which Relational
			// fills during save() — too late for a birth score that depends on
			// who the author is and what standing they have. The automatic
			// writes the same value again a moment later.
			$row->set('created_by', (int) User::get('id'));

			// After the anonymous flag, not before: an anonymous comment is
			// born at its own score and earns no karma bonus either way.
			$row->setBirthScore($this->config);

			// Advisory only. Nothing reads it automatically, and on a hub
			// where a whole campus shares one address it would be a poor
			// record of who anybody is.
			$row->set('ip', Request::ip());
		}
		else
		{
			$row->set('modified', \Hubzero\Utility\Date::of('now')->toSql());
			$row->set('modified_by', (int) User::get('id'));
		}

		if (!$row->save())
		{
			foreach ($row->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $row->isNew() ? $this->newTask() : $this->editTask($row);
		}

		$discussion->recount();
		$discussion->touch();

		Notify::success(Lang::txt('COM_STORY_COMMENT_SAVED'));

		App::redirect(Route::url($this->discussionLink($discussion) . '#c' . $row->get('id')));
	}

	/**
	 * Take one back
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		Request::checkToken(array('get', 'post'));

		$row = Comment::oneOrFail(Request::getInt('comment', 0));

		if (!$this->canEdit($row))
		{
			App::abort(403, Lang::txt('COM_STORY_COMMENT_NOT_YOURS'));
		}

		$discussion = Discussion::oneOrNew($row->get('discussion_id'));

		if (!$row->destroy())
		{
			Notify::error($row->getError());
		}
		else
		{
			Notify::success(Lang::txt('COM_STORY_COMMENT_REMOVED'));
		}

		$discussion->recount();

		App::redirect(Route::url($this->discussionLink($discussion)));
	}

	/**
	 * How a reader wants discussions shown to them
	 *
	 * @return  void
	 */
	public function preferencesTask()
	{
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('COM_STORY_PREFERENCES_NEED_LOGIN'));
		}

		$preference = Preference::forUser(User::get('id'));

		if (strtolower(Request::method()) == 'post')
		{
			Request::checkToken();

			$fields = Request::getArray('fields', array(), 'post');

			foreach (array_keys(Preference::defaults()) as $key)
			{
				if (array_key_exists($key, $fields))
				{
					$preference->set($key, $fields[$key]);
				}
			}

			$preference->set('user_id', (int) User::get('id'));

			if ($preference->save())
			{
				Notify::success(Lang::txt('COM_STORY_PREFERENCES_SAVED'));
			}
			else
			{
				foreach ($preference->getErrors() as $error)
				{
					Notify::error($error);
				}
			}
		}

		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_STORY'), 'index.php?option=' . $this->_option);
		}

		Pathway::append(Lang::txt('COM_STORY_PREFERENCES'), 'index.php?option=' . $this->_option . '&view=comments&task=preferences');

		Document::setTitle(Lang::txt('COM_STORY_PREFERENCES'));

		$this->view
			->set('preference', $preference)
			->set('config', $this->config)
			->setLayout('preferences')
			->display();
	}

	/**
	 * Where a discussion lives
	 *
	 * @param   object  $discussion
	 * @return  string
	 */
	protected function discussionLink($discussion)
	{
		return 'index.php?option=' . $this->_option . '&view=comments&discussion=' . $discussion->get('id');
	}
}
