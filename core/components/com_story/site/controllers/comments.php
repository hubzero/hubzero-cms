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
use Hubzero\Moderation\Moderator;
use Event;
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
		$this->registerTask('ajaxmoderate', 'moderate');

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

		// Moderation is offered to people who read.
		Event::trigger('moderation.onStoryDiscussionViewed', array($discussion));

		$moderator = $this->moderator();

		$this->view
			->set('mayModerate', $this->mayModerate())
			->set('moderator', $moderator)
			->set('reasons', $this->reasonsFor($discussion))
			->set('credits', $moderator->credits('com_story.comment'))
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

		// Saying something here reverses any moderating this reader did in
		// this discussion. Taking part after moderating is the same conflict
		// as moderating after taking part, and it is resolved the same way.
		Event::trigger('moderation.onStoryCommentSaved', array($row));

		if ($row->get('state') == Comment::STATE_PUBLISHED && !$row->get('anonymous'))
		{
			Event::trigger('system.logActivity', array(
				'activity' => array(
					'action'      => 'created',
					'scope'       => 'story.comment',
					'scope_id'    => $row->get('id'),
					'anonymous'   => $row->get('anonymous', 0),
					'description' => Lang::txt('COM_STORY_ACTIVITY_COMMENTED', $discussion->get('title')),
					'details'     => array(
						'discussion' => $discussion->get('id'),
						'url'        => Route::url($this->discussionLink($discussion) . '#c' . $row->get('id'))
					)
				),
				'recipients' => array()
			));
		}

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
	 * The reasons this reader may pick from, or an empty list
	 *
	 * Asked once for the page rather than once per comment: the set is the
	 * same for every comment in it.
	 *
	 * @param   object  $discussion
	 * @return  array
	 */
	protected function reasonsFor($discussion)
	{
		if (User::isGuest())
		{
			return array();
		}

		return \Hubzero\Moderation\Reason::forType('com_story.comment');
	}

	/**
	 * A comment, wrapped as something the moderation library can work on
	 *
	 * Resolved through the plugin group rather than by naming the adapter
	 * class. The component has no business knowing which plugin speaks for
	 * its comments, and triggering the event is also what loads the group.
	 *
	 * @param   integer  $id
	 * @return  mixed    object|null
	 */
	protected function moderatable($id)
	{
		$found = Event::trigger('moderation.onModerationResolveItem', array('com_story.comment', (int) $id));

		foreach ((array) $found as $item)
		{
			if (is_object($item))
			{
				return $item;
			}
		}

		return null;
	}

	/**
	 * Whether this reader is allowed to moderate at all
	 *
	 * The permission gates spending as well as earning. Gating only the grant
	 * would leave somebody who had already been handed credits moderating for
	 * as long as those lasted after the permission was taken away, which is
	 * not what revoking a permission is understood to mean.
	 *
	 * @return  boolean
	 */
	protected function mayModerate()
	{
		if (User::isGuest())
		{
			return false;
		}

		return (User::authorise('story.moderate.unlimited', $this->_option)
			|| User::authorise('story.moderate', $this->_option));
	}

	/**
	 * The current reader, as a moderator
	 *
	 * `story.moderate.unlimited` is what separates staff moderation from
	 * crowd moderation, and it is the only thing that does. Somebody holding
	 * it spends no credits; everybody else spends one per moderation and is
	 * only granted any if they hold `story.moderate`. Granting that to nobody
	 * empties the eligible pool, so the off switch is a permission rather
	 * than a setting.
	 *
	 * @return  object
	 */
	protected function moderator()
	{
		return new Moderator(
			(int) User::get('id'),
			User::authorise('story.moderate.unlimited', $this->_option)
		);
	}

	/**
	 * Move a comment's score
	 *
	 * Answers HTML or JSON depending on how it was asked, so a moderator can
	 * work through a discussion without losing their place. Both paths go
	 * through the same Moderator::moderate().
	 *
	 * @return  void
	 */
	public function moderateTask()
	{
		Request::checkToken(array('get', 'post'));

		$id     = Request::getInt('comment', 0);
		$reason = Request::getWord('reason', '');
		$ajax   = ($this->getTask() == 'ajaxmoderate' || Request::getInt('no_html', 0));

		$item = $this->moderatable($id);

		if (!$item)
		{
			return $this->moderationAnswer($ajax, false, Lang::txt('COM_STORY_COMMENT_NOT_FOUND'), $id);
		}

		if (!$this->mayModerate())
		{
			return $this->moderationAnswer($ajax, false, $this->refusalText('permission'), $id);
		}

		$moderator = $this->moderator();
		$log       = $moderator->moderate($item, $reason, array('ip' => Request::ip()));

		if (!$log)
		{
			return $this->moderationAnswer($ajax, false, $this->refusalText($moderator->why()), $id);
		}

		// The library moved the score. Standing is a separate question, and
		// the karma plugin is the only thing entitled to an opinion on it.
		Event::trigger('karma.onStoryCommentModerated', array($item->comment(), $log));

		$this->notifyAuthor($item, $log);

		return $this->moderationAnswer($ajax, true, Lang::txt('COM_STORY_MODERATED'), $id, $item->currentScore());
	}

	/**
	 * Say why a moderation was refused, in words
	 *
	 * Rendering the actual cause rather than greying a control out is the
	 * difference between a rule somebody can learn and one they can only
	 * bump into.
	 *
	 * @param   string  $code
	 * @return  string
	 */
	protected function refusalText($code)
	{
		$known = array('nobody', 'own', 'already', 'participant', 'credits', 'reason', 'failed', 'permission');

		if (!in_array($code, $known))
		{
			$code = 'failed';
		}

		return Lang::txt('COM_STORY_MODERATION_REFUSED_' . strtoupper($code));
	}

	/**
	 * Answer a moderation, in whichever form it was asked for
	 *
	 * @param   boolean  $ajax
	 * @param   boolean  $ok
	 * @param   string   $message
	 * @param   integer  $id
	 * @param   float    $score
	 * @return  void
	 */
	protected function moderationAnswer($ajax, $ok, $message, $id, $score = null)
	{
		if ($ajax)
		{
			Document::setType('raw');

			echo json_encode(array(
				'success' => (bool) $ok,
				'message' => $message,
				'comment' => (int) $id,
				'score'   => ($score === null) ? null : (float) $score
			));

			return;
		}

		$ok ? Notify::success($message) : Notify::warning($message);

		$discussion = Discussion::oneOrNew(Request::getInt('discussion', 0));

		App::redirect(Route::url($this->discussionLink($discussion) . '#c' . (int) $id));
	}

	/**
	 * Tell somebody their comment was moderated
	 *
	 * Not for an anonymous comment: there is nobody the moderation attaches
	 * to, which is the same reason it moves no karma.
	 *
	 * @param   object  $item
	 * @param   object  $log
	 * @return  void
	 */
	protected function notifyAuthor($item, $log)
	{
		if (!$author = (int) $item->authorId())
		{
			return;
		}

		if ($author === (int) User::get('id'))
		{
			return;
		}

		Event::trigger('story.onStoryCommentModerationNotice', array($item->comment(), $log, $author));
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
