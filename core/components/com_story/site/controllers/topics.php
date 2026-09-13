<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Story\Models\Manager;
use Components\Story\Models\Topic;
use Document;
use Pathway;
use Request;
use Lang;
use App;

/**
 * Everything filed under one topic
 */
class Topics extends SiteController
{
	/**
	 * Stories in this scope
	 *
	 * @var  object
	 */
	protected $stories = null;

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->stories = new Manager('site', 0);

		$this->registerTask('__default', 'display');

		parent::execute();
	}

	/**
	 * List a topic's stories, or every topic
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt('COM_STORY'), 'index.php?option=' . $this->_option);
		}

		$alias = Request::getString('topic', '');
		$topic = null;
		$rows  = null;

		if ($alias)
		{
			$topic = Topic::oneByAlias($alias);

			if (!$topic->get('id') || !$topic->isPublished())
			{
				App::abort(404, Lang::txt('COM_STORY_TOPIC_NOT_FOUND'));
			}

			Pathway::append($topic->get('title'), 'index.php?option=' . $this->_option . '&view=topics&topic=' . $alias);

			$rows = $this->stories->stories(array('topic_id' => $topic->get('id')))
				->paginated('limitstart', 'limit')
				->rows();
		}

		Document::setTitle($topic->get('id') ? $topic->get('title') : Lang::txt('COM_STORY_TOPICS'));

		$this->view
			->set('topic', $topic)
			->set('topics', $this->stories->topics()->rows())
			->set('rows', $rows)
			->set('config', $this->config)
			->display();
	}
}
