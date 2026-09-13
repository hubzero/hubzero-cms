<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Story\Models\Submission;
use Components\Story\Models\Section;
use Components\Story\Models\Topic;
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
 * Suggesting a story, and seeing what became of it
 */
class Submissions extends SiteController
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

		parent::execute();
	}

	/**
	 * Breadcrumbs
	 *
	 * @param   string  $label
	 * @param   string  $link
	 * @return  void
	 */
	protected function trail($label, $link)
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_STORY'), 'index.php?option=' . $this->_option);
		}

		Pathway::append($label, $link);
	}

	/**
	 * What this reader has sent in, and what came of it
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (User::isGuest())
		{
			return $this->newTask();
		}

		$this->trail(Lang::txt('COM_STORY_MY_SUBMISSIONS'), 'index.php?option=' . $this->_option . '&view=submissions');

		Document::setTitle(Lang::txt('COM_STORY_MY_SUBMISSIONS'));

		$rows = Submission::all()
			->whereEquals('created_by', (int) User::get('id'))
			->order('created', 'desc')
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('config', $this->config)
			->display();
	}

	/**
	 * The form
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function newTask($row = null)
	{
		if (User::isGuest())
		{
			$return = base64_encode(Request::getString('REQUEST_URI', '', 'server'));

			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return, false),
				Lang::txt('COM_STORY_SUBMIT_NEED_LOGIN'),
				'warning'
			);

			return;
		}

		$this->trail(Lang::txt('COM_STORY_SUBMIT'), 'index.php?option=' . $this->_option . '&view=submissions&task=new');

		Document::setTitle(Lang::txt('COM_STORY_SUBMIT'));

		if (!is_object($row))
		{
			$row = Submission::blank();
		}

		$this->view
			->set('row', $row)
			->set('sections', Section::all()->whereEquals('state', 1)->order('ordering', 'asc')->rows())
			->set('topics', Topic::all()->whereEquals('state', 1)->order('ordering', 'asc')->rows())
			->set('config', $this->config)
			->setLayout('edit')
			->display();
	}

	/**
	 * Take one in
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		if (User::isGuest())
		{
			App::abort(403, Lang::txt('COM_STORY_SUBMIT_NEED_LOGIN'));
		}

		$fields = Request::getArray('fields', array(), 'post');

		$row = Submission::blank();
		$row->set(array(
			'subject'    => isset($fields['subject']) ? $fields['subject'] : '',
			'body'       => isset($fields['body']) ? $fields['body'] : '',
			'url'        => isset($fields['url']) ? $fields['url'] : '',
			'topic_id'   => isset($fields['topic_id']) ? (int) $fields['topic_id'] : 0,
			'section_id' => isset($fields['section_id']) ? (int) $fields['section_id'] : 0,
			'state'      => Submission::STATE_PENDING,
			'created_by' => (int) User::get('id'),
			'email'      => (string) User::get('email'),
			'ip'         => Request::ip()
		));

		// Its resting score, before anybody has voted on it.
		$row->rescore($this->config);

		if (!$row->save())
		{
			foreach ($row->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->newTask($row);
		}

		Notify::success(Lang::txt('COM_STORY_SUBMITTED'));

		App::redirect(Route::url('index.php?option=' . $this->_option . '&view=submissions'));
	}
}
