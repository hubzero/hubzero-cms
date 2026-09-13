<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Story\Models\Story;
use Components\Story\Models\Text;
use Components\Story\Models\Discussion;
use Components\Story\Models\Section;
use Components\Story\Models\Topic;
use Hubzero\Utility\Date;
use Request;
use Notify;
use Lang;
use User;
use App;

/**
 * Manage stories
 */
class Stories extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('archive', 'state');

		parent::execute();
	}

	/**
	 * List stories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search'   => urldecode(Request::getState($this->_option . '.stories.search', 'search', '')),
			'state'    => Request::getState($this->_option . '.stories.state', 'state', '-1'),
			'section'  => Request::getState($this->_option . '.stories.section', 'section', 0, 'int'),
			'sort'     => Request::getState($this->_option . '.stories.sort', 'filter_order', 'publish_up'),
			'sort_Dir' => Request::getState($this->_option . '.stories.sortdir', 'filter_order_Dir', 'DESC')
		);

		$query = Story::all();

		if ($filters['search'])
		{
			$query->whereLike('title', $filters['search']);
		}

		if ($filters['state'] !== '-1' && $filters['state'] !== '')
		{
			$query->whereEquals('state', (int) $filters['state']);
		}

		if ($filters['section'])
		{
			$query->whereEquals('section_id', $filters['section']);
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('sections', Section::all()->order('ordering', 'asc')->rows())
			->display();
	}

	/**
	 * Edit a story
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id  = Request::getArray('id', array(0));
			$id  = (is_array($id) ? $id[0] : $id);
			$row = Story::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set(array(
				'state'      => Story::STATE_DRAFT,
				'publish_up' => with(new Date('now'))->toSql()
			));
		}

		$this->view
			->set('row', $row)
			->set('text', $row->get('id') ? $row->text() : Text::blank())
			->set('sections', Section::all()->order('ordering', 'asc')->rows())
			->set('topics', Topic::all()->order('ordering', 'asc')->rows())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a story and its prose
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$fields = Request::getArray('fields', array(), 'post');
		$prose  = Request::getArray('text', array(), 'post', 'none', 2);

		// Publishing is a separate permission from editing: an author may
		// write a story without being able to put it out.
		if ((int) $fields['state'] === Story::STATE_PUBLISHED && !User::authorise('story.publish', $this->_option))
		{
			Notify::warning(Lang::txt('COM_STORY_CANNOT_PUBLISH'));
			$fields['state'] = Story::STATE_QUEUED;
		}

		$row = Story::oneOrNew($fields['id'])->set($fields);

		if ($row->get('id'))
		{
			$row->set('modified', with(new Date('now'))->toSql());
			$row->set('modified_by', User::get('id'));
		}

		if ($this->config->get('require_kicker', 1) && !trim($row->get('kicker')))
		{
			Notify::error(Lang::txt('COM_STORY_ERROR_KICKER'));
			return $this->editTask($row);
		}

		if (!$row->save())
		{
			foreach ($row->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->editTask($row);
		}

		$text = Text::oneByStory($row->get('id'));
		$text->set(array(
			'intro'   => isset($prose['intro']) ? $prose['intro'] : '',
			'body'    => isset($prose['body']) ? $prose['body'] : '',
			'related' => isset($prose['related']) ? $prose['related'] : ''
		));

		if (!$text->save())
		{
			Notify::error(Lang::txt('COM_STORY_ERROR_TEXT_NOT_SAVED'));
		}

		// A published story gets its discussion here rather than the first
		// time somebody reads it: a page view should not be writing rows.
		if ($row->get('state') == Story::STATE_PUBLISHED && !$row->get('discussion_id'))
		{
			Discussion::forStory($row);
		}

		Notify::success(Lang::txt('COM_STORY_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Move stories between states
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$map = array(
			'publish'   => Story::STATE_PUBLISHED,
			'unpublish' => Story::STATE_DRAFT,
			'archive'   => Story::STATE_ARCHIVED
		);

		$task  = $this->getTask();
		$state = isset($map[$task]) ? $map[$task] : Story::STATE_DRAFT;

		if ($state === Story::STATE_PUBLISHED && !User::authorise('story.publish', $this->_option))
		{
			App::abort(403, Lang::txt('COM_STORY_CANNOT_PUBLISH'));
		}

		$ids = Request::getArray('id', array());

		foreach ($ids as $id)
		{
			$row = Story::oneOrFail((int) $id);
			$row->set('state', $state);

			// A story published with no hour set goes out now rather than
			// silently never, which is what an empty publish_up would mean
			// to a front page that filters on it.
			if ($state === Story::STATE_PUBLISHED && !$row->get('publish_up'))
			{
				$row->set('publish_up', with(new Date('now'))->toSql());
			}

			if (!$row->save())
			{
				Notify::error($row->getError());
			}
		}

		Notify::success(Lang::txt('COM_STORY_STATE_CHANGED', count($ids)));

		$this->cancelTask();
	}

	/**
	 * Delete stories
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());

		foreach ($ids as $id)
		{
			$row = Story::oneOrFail((int) $id);

			if (!$row->destroy())
			{
				Notify::error($row->getError());
			}
		}

		Notify::success(Lang::txt('COM_STORY_REMOVED', count($ids)));

		$this->cancelTask();
	}
}
