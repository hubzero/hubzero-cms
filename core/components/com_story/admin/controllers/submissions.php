<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Story\Models\Submission;
use Components\Story\Models\Story;
use Components\Story\Models\Text;
use Hubzero\Utility\Date;
use Request;
use Notify;
use Route;
use Event;
use Lang;
use User;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'submission.php';

/**
 * Triage: deciding what becomes a story
 *
 * The screen editors spend their time in. Sorted by what other editors have
 * marked and by what has been flagged as needing a decision — deliberately not
 * by the public ranking, which is a different question and lives in its own
 * column.
 */
class Submissions extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'display');
		$this->registerTask('hold', 'decide');
		$this->registerTask('reject', 'decide');
		$this->registerTask('spam', 'decide');

		parent::execute();
	}

	/**
	 * The queue as an editor sees it
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'state'  => Request::getState($this->_option . '.submissions.state', 'state', ''),
			'search' => urldecode(Request::getState($this->_option . '.submissions.search', 'search', ''))
		);

		$query = Submission::all();

		if ($filters['state'])
		{
			$query->whereEquals('state', $filters['state']);
		}
		else
		{
			$query->whereIn('state', Submission::openStates());
		}

		if ($filters['search'])
		{
			$query->whereLike('subject', $filters['search']);
		}

		$rows = $query
			->order('attention_needed', 'desc')
			->order('editor_popularity', 'desc')
			->order('popularity', 'desc')
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('config', $this->config)
			->display();
	}

	/**
	 * Turn a submission into a draft story
	 *
	 * One screen, one click, and the editor lands in the story editor with
	 * the words already in it. Provenance travels with it: the story keeps
	 * both who suggested it and which submission it came from, so credit is a
	 * property of the record rather than something an editor has to remember.
	 *
	 * The story arrives as a draft, never published. Accepting a suggestion
	 * is agreeing it is worth writing, which is not the same as agreeing it
	 * is written.
	 *
	 * @return  void
	 */
	public function acceptTask()
	{
		Request::checkToken(array('get', 'post'));

		if (!User::authorise('story.publish', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());

		if (!$ids && $id = Request::getInt('submission', 0))
		{
			$ids = array($id);
		}

		if (!$ids)
		{
			Notify::warning(Lang::txt('COM_STORY_NOTHING_SELECTED'));

			return $this->cancelTask();
		}

		$row = Submission::oneOrFail((int) $ids[0]);

		if ($row->isDecided())
		{
			Notify::warning(Lang::txt('COM_STORY_ALREADY_DECIDED'));

			return $this->cancelTask();
		}

		$story = Story::blank();
		$story->set(array(
			'title'         => $row->get('subject'),
			'section_id'    => (int) $row->get('section_id'),
			'topic_id'      => (int) $row->get('topic_id'),
			'state'         => Story::STATE_DRAFT,
			'created_by'    => (int) User::get('id'),
			'submitter_id'  => (int) $row->get('created_by'),
			'submission_id' => (int) $row->get('id'),
			'scope'         => 'site',
			'scope_id'      => 0,
			'access'        => 1
		));

		if (!$story->save())
		{
			foreach ($story->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->cancelTask();
		}

		$intro = trim((string) $row->get('body'));

		if ($url = trim((string) $row->get('url')))
		{
			$intro .= ($intro ? "\n\n" : '') . $url;
		}

		$text = Text::oneByStory($story->get('id'));
		$text->set(array('intro' => $intro, 'body' => '', 'related' => ''));
		$text->save();

		$row->set(array(
			'state'      => Submission::STATE_ACCEPTED,
			'story_id'   => (int) $story->get('id'),
			'decided_by' => (int) User::get('id'),
			'decided_at' => Date::of('now')->toSql()
		));
		$row->rescore($this->config);
		$row->save();

		// The submitter earns for the acceptance, never for the votes that
		// got it there — that loop is what the per-source cap and the silent
		// vote are both guarding against.
		Event::trigger('karma.onStorySubmissionAccepted', array($row));

		Notify::success(Lang::txt('COM_STORY_ACCEPTED_INTO_DRAFT'));

		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=stories&task=edit&id[]=' . (int) $story->get('id'), false));
	}

	/**
	 * Hold, reject or discard
	 *
	 * @return  void
	 */
	public function decideTask()
	{
		Request::checkToken(array('get', 'post'));

		if (!User::authorise('story.publish', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$states = array(
			'hold'   => Submission::STATE_HOLD,
			'reject' => Submission::STATE_REJECTED,
			'spam'   => Submission::STATE_SPAM
		);

		$task  = $this->getTask();
		$state = isset($states[$task]) ? $states[$task] : Submission::STATE_HOLD;

		$ids = Request::getArray('id', array());

		if (!$ids && $id = Request::getInt('submission', 0))
		{
			$ids = array($id);
		}

		$done = 0;

		foreach ($ids as $id)
		{
			$row = Submission::oneOrNew((int) $id);

			if (!$row->get('id'))
			{
				continue;
			}

			$row->set(array(
				'state'      => $state,
				'decided_by' => (int) User::get('id'),
				'decided_at' => Date::of('now')->toSql()
			));

			// The state changes what it rests at, so the ranking moves with it.
			$row->rescore($this->config);

			if ($row->save())
			{
				$done++;
			}
		}

		Notify::success(Lang::txt('COM_STORY_SUBMISSION_DECIDED', $done));

		$this->cancelTask();
	}

	/**
	 * Flag that a human has to decide this one
	 *
	 * @return  void
	 */
	public function attentionTask()
	{
		Request::checkToken(array('get', 'post'));

		if (!User::authorise('story.publish', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids  = Request::getArray('id', array());
		$done = 0;

		foreach ($ids as $id)
		{
			$row = Submission::oneOrNew((int) $id);

			if (!$row->get('id'))
			{
				continue;
			}

			$row->set('attention_needed', $row->get('attention_needed') ? 0 : 1);

			if ($row->save())
			{
				$done++;
			}
		}

		Notify::success(Lang::txt('COM_STORY_SUBMISSION_DECIDED', $done));

		$this->cancelTask();
	}
}
