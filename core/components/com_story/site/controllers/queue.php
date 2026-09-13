<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Story\Models\Submission;
use Hubzero\Item\Vote;
use Hubzero\Utility\Date;
use Document;
use Pathway;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'submission.php';

/**
 * The public ranked queue
 *
 * Everything waiting on an editor, in the order readers have put it. Open on
 * purpose: a queue readers cannot see is one they cannot help with, and the
 * whole argument for ranking it is that readers do the ranking.
 */
class Queue extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'display');

		parent::execute();
	}

	/**
	 * The queue, hottest first
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (!$this->config->get('queue_public', 1) && !User::authorise('story.publish', $this->_option))
		{
			App::abort(404, Lang::txt('COM_STORY_QUEUE_CLOSED'));
		}

		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_STORY'), 'index.php?option=' . $this->_option);
		}

		Pathway::append(Lang::txt('COM_STORY_QUEUE'), 'index.php?option=' . $this->_option . '&view=queue');

		Document::setTitle(Lang::txt('COM_STORY_QUEUE'));

		$rows = Submission::open()
			->order('popularity', 'desc')
			->paginated('limitstart', 'limit')
			->rows();

		// Which of these this reader has already had their say on, asked once
		// rather than once per row.
		$voted = array();

		if (!User::isGuest())
		{
			$mine = Vote::all()
				->whereEquals('item_type', Submission::VOTE_TYPE)
				->whereEquals('created_by', (int) User::get('id'))
				->rows();

			foreach ($mine as $vote)
			{
				$voted[(int) $vote->get('item_id')] = (int) $vote->get('vote');
			}
		}

		$this->view
			->set('rows', $rows)
			->set('voted', $voted)
			->set('canVote', !User::isGuest())
			->set('config', $this->config)
			->display();
	}

	/**
	 * Say whether this is worth running
	 *
	 * Casting a vote earns the voter nothing. Karma weights these votes, and
	 * an accepted submission awards karma — paying for the vote as well would
	 * close that loop and make the queue farmable by anyone willing to click.
	 *
	 * @return  void
	 */
	public function voteTask()
	{
		Request::checkToken(array('get', 'post'));

		if (User::isGuest())
		{
			App::abort(403, Lang::txt('COM_STORY_VOTE_NEED_LOGIN'));
		}

		$id  = Request::getInt('submission', 0);
		$dir = (Request::getWord('vote', 'up') == 'down') ? -1 : 1;

		$row = Submission::oneOrNew($id);

		if (!$row->get('id') || $row->isDecided())
		{
			App::abort(404, Lang::txt('COM_STORY_SUBMISSION_NOT_FOUND'));
		}

		if ((int) $row->get('created_by') === (int) User::get('id'))
		{
			Notify::warning(Lang::txt('COM_STORY_VOTE_OWN'));

			return $this->cancel();
		}

		// One opinion each. Changing your mind is allowed; having two is not.
		$vote = Vote::oneByScope($row->get('id'), Submission::VOTE_TYPE, User::get('id'));

		$vote->set(array(
			'item_id'    => (int) $row->get('id'),
			'item_type'  => Submission::VOTE_TYPE,
			'created_by' => (int) User::get('id'),
			'vote'       => $dir
		));

		if (!$vote->get('created'))
		{
			$vote->set('created', Date::of('now')->toSql());
		}

		if (!$vote->save())
		{
			Notify::error($vote->getError());

			return $this->cancel();
		}

		$row->rescore($this->config);
		$row->save();

		Notify::success(Lang::txt('COM_STORY_VOTE_RECORDED'));

		return $this->cancel();
	}

	/**
	 * Back to the queue
	 *
	 * @return  void
	 */
	protected function cancel()
	{
		App::redirect(Route::url('index.php?option=' . $this->_option . '&view=queue'));
	}
}
