<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Inflector;
use Hubzero\Access\Access;
use Hubzero\Access\Rules;
use Hubzero\Access\Asset;
use Components\Content\Models\Article;
use Components\Content\Models\Featured;
use Request;
use Config;
use Notify;
use Route;
use User;
use Lang;
use Date;
use App;

/**
 * Controller class for content articles
 */
class Articles extends AdminController
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
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');
		$this->registerTask('archive', 'state');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('trash', 'state');
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		parent::execute();
	}

	/**
	 * Batch process entries
	 *
	 * @return  void
	 */
	public function batchTask()
	{
		$ids = Request::getArray('cid', array());

		if (empty($ids))
		{
			Notify::error(Lang::txt('JGLOBAL_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		$batchOptions = Request::getArray('batch', array());

		if (empty($batchOptions['category_id']))
		{
			Notify::error(Lang::txt('COM_CONTENT_BATCH_NO_CATEGORY_SELECTED'));
			return $this->cancelTask();
		}

		$action = $batchOptions['move_copy'] == 'm' ? 'moved' : 'copied';

		$params = array(
			'catid'    => $batchOptions['category_id'],
			'access'   => isset($batchOptions['assetgroup_id']) ? $batchOptions['assetgroup_id'] : null,
			'language' => isset($batchOptions['language_id']) ? $batchOptions['language_id'] : null
		);
		$params = array_filter($params);

		$articles = Article::all()->whereIn('id', $ids)->rows();
		foreach ($articles as $article)
		{
			$article->set($params);
			if ($batchOptions['move_copy'] == 'c')
			{
				$article->removeAttribute('id');
			}
		}

		if ($articles->save())
		{
			Notify::success(Lang::txt('COM_CONTENT_BATCH_SUCCESS', $action));
		}

		$this->cancelTask();
	}

	/**
	 * Displays a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if ($layout = Request::getString('layout'))
		{
			$this->context .= '.' . $layout;
		}
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'filter_access',
				0
			),
			'author_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.author_id',
				'author_id'
			),
			'published' => Request::getState(
				$this->_option . '.' . $this->_controller . '.published',
				'filter_published',
				''
			),
			'category_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.category_id',
				'filter_category_id'
			),
			'level' => Request::getState(
				$this->_option . '.' . $this->_controller . '.level',
				0
			),
			'language' => Request::getState(
				$this->_option . '.' . $this->_controller . '.language',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$articles = Article::all()
			->select('`#__languages`.title', 'language_title')
			->select('`#__content`.*')
			->join('#__languages', 'language', 'lang_code', 'left outer');

		$filterableFields = array(
			'category_id' => 'catid',
			'access' => 'access'
		);
		foreach ($filterableFields as $index => $column)
		{
			if (isset($filters[$index]) && $filters[$index] != '')
			{
				$articles->whereEquals($column, $filters[$index]);
			}
		}

		$searchableFields = array('title', 'alias');
		if (!empty($filters['search']))
		{
			$firstSearch = array_shift($searchableFields);
			$articles->whereLike($firstSearch, $filters['search'], 1);
			foreach ($searchableFields as $field)
			{
				$articles->orWhereLike($field, $filters['search'], 1);
			}
			$articles->resetDepth();
		}

		if (isset($filters['published']))
		{
			if ($filters['published'] == '')
			{
				$articles->where('state', '>=', 0);
			}
			elseif ($filters['published'] != '*')
			{
				$articles->whereEquals('state', $filters['published']);
			}
		}

		$articles->including('accessLevel')
				 ->including('category')
				 ->including('author');
		if (strtolower($filters['sort']) == 'ordering')
		{
			$articles->order('catid', 'asc');
		}

		$items = $articles
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$itemsArray = array();
		foreach ($items as $item)
		{
			$itemsArray[] = $item;
		}

		$layout = Request::getCmd('layout', 'default');

		$this->view
			->set('pagination', $items->pagination)
			->set('authors', array())
			->set('f_levels', array())
			->set('filters', $filters)
			->set('items', $itemsArray)
			->setLayout($layout)
			->display();
	}

	/**
	 * Displays a list of featured entries
	 *
	 * @return  void
	 */
	public function featuredTask()
	{
		if ($layout = Request::getString('layout'))
		{
			$this->context .= '.' . $layout;
		}
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.featured.search',
				'filter_search',
				''
			),
			'access' => Request::getState(
				$this->_option . '.featured.access',
				'filter_access',
				0
			),
			'author_id' => Request::getState(
				$this->_option . '.featured.author_id',
				'author_id'
			),
			'published' => Request::getState(
				$this->_option . '.featured.published',
				'filter_published',
				''
			),
			'category_id' => Request::getState(
				$this->_option . '.featured.category_id',
				'filter_category_id'
			),
			'level' => Request::getState(
				$this->_option . '.featured.level',
				0
			),
			'language' => Request::getState(
				$this->_option . '.featured.language',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.featured.sort',
				'filter_order',
				'ordering'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.featured.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$query = Article::all();

		$fp = Featured::blank()->getTableName();
		$a  = $query->getTableName();
		$l  = '#__languages';

		$query
			->select($l . '.title', 'language_title')
			->select($a . '.id')
			->select($a . '.title')
			->select($a . '.alias')
			->select($a . '.checked_out')
			->select($a . '.checked_out_time')
			->select($a . '.catid')
			->select($a . '.state')
			->select($a . '.access')
			->select($a . '.created')
			->select($a . '.created_by_alias')
			->select($a . '.hits')
			->select($a . '.language')
			->select($a . '.publish_up')
			->select($a . '.publish_down')
			->join($l, $l . '.lang_code', $a . '.language', 'left');

		// Join over the content table.
		$query->select($fp . '.ordering');
		$query->join($fp, $fp . '.content_id', $a . '.id', 'inner');

		if (isset($filters['category_id']) && $filters['category_id'])
		{
			$query->whereEquals($a . '.catid', $filters['category_id']);
		}

		if (isset($filters['language']) && $filters['language'])
		{
			$query->whereEquals($a . '.language', $filters['language']);
		}

		if (isset($filters['access']))
		{
			$query->whereEquals($a . '.access', $filters['access']);
		}

		if (isset($filters['search']) && $filters['search'])
		{
			if (stripos($filters['search'], 'id:') === 0)
			{
				$query->whereEquals($a . '.id', (int) substr($filters['search'], 3));
			}
			else
			{
				$query->whereLike($a . '.title', $filters['search'], 1)
					->orWhereLike($a . '.alias', $filters['search'], 1)
					->resetDepth();
			}
		}

		if (isset($filters['published']))
		{
			if ($filters['published'] == '')
			{
				$query->where($a . '.state', '>=', 0);
			}
			elseif ($filters['published'] != '*')
			{
				$query->whereEquals($a . '.state', $filters['published']);
			}
		}

		$query->including('accessLevel')
			->including('category')
			->including('author');
		if (strtolower($filters['sort']) == 'ordering')
		{
			$query->order('catid', 'asc');
		}

		$items = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$itemsArray = array();
		foreach ($items as $item)
		{
			$itemsArray[] = $item;
		}

		$this->view
			->set('pagination', $items->pagination)
			->set('authors', array())
			->set('f_levels', array())
			->set('filters', $filters)
			->set('items', $itemsArray)
			->setLayout('featured')
			->display();
	}

	/**
	 * Displays a form for editing an entry
	 *
	 * @param   mixed  $article
	 * @return  void
	 */
	public function editTask($article = null)
	{
		$id = Request::getInt('id', 0);

		if (!($article instanceof Article))
		{
			$article = Article::oneOrNew($id);
		}

		if (!$article->isNew())
		{
			$assetId = $article->get('asset_id');
			$createdBy = $article->get('created_by');
			$checkedOut = $article->get('checked_out');
			if ($checkedOut)
			{
				if ($checkedOut != User::getInstance()->get('id'))
				{
					Notify::error(Lang::txt('COM_CONTENT_CHECKED_IN_ERROR', $article->id));
					return $this->cancelTask();
				}
			}
			if (!User::authorise('core.edit', $assetId))
			{
				if (!User::authorise('core.edit.own', $assetId)
					|| $createdBy != User::getInstance()->get('id'))
				{
					App::abort(403, Lang::txt('COM_CONTENT_NOT_AUTHORIZED'));
				}
			}

			// Need to offset publish up date with user timezone
            $userTimezone = App::get('user')->getParam('timezone', App::get('config')->get('offset'));
            $article->set('publish_up', Date::of($article->get('publish_up'), $userTimezone)->toSql());

			$article->set('checked_out', User::getInstance()->get('id'));
			$article->set('checked_out_time', Date::of('now')->toSql());
			$article->save();
		}
		else
		{
			if (!User::authorise('core.create', 'com_content'))
			{
				App::abort(403, Lang::txt('COM_CONTENT_NOT_AUTHORIZED'));
			}
		}

		Request::setVar('hidemainmenu', 1);

		$newTasks = array('save2new', 'save2copy');
		$task = in_array($this->_task, $newTasks) ? 'add' : $this->_task;

		// Upon "saving to copy" or "save to new", blank out alias, update created and published up date to now
		if (in_array($this->_task, $newTasks)) {
			$article->set('created', Date::of('now')->toSql());
			$article->set('created_by', User::getInstance()->get('id'));
			$article->set('publish_up', Date::of('now')->toSql());
			$article->set('publish_down', null);
			$article->set('alias', null);
			$article->save();
		}

		$this->view
			->set('task', $task)
			->set('item', $article)
			->set('form', $article->getForm())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save changes to an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming data
		$items = Request::getArray('fields', array(), 'post');

		$articleId = Request::getInt('id');

		$article = Article::oneOrNew($articleId);

		$checkedOut = $article->get('checked_out');
		if ($checkedOut)
		{
			$article->set('checked_out', 0);
			$article->set('checked_out_time', null);
			$article->save();
		}

		if (!empty($items['rules']))
		{
			$rules = array_map(
				function($item)
				{
					return array_filter($item, 'strlen');
				},
				$items['rules']
			);
			$article->assetRules = new Rules($rules);
		}
		unset($items['rules']);

		$article->set($items);

		if ($this->getTask() == 'save2copy')
		{
			$article->set('id', 0);
		}

		if (!$article->save())
		{
			Notify::error($article->getError());
			return $this->editTask($article);
		}

		Notify::success(Lang::txt('COM_CONTENT_SAVE_SUCCESS'));

		if ($this->getTask() == 'apply'
		 || $this->getTask() == 'save2copy')
		{
			return $this->editTask($article);
		}
		elseif ($this->getTask() == 'save2new')
		{
			Request::setVar('id', 0);
			return $this->editTask();
		}

		$this->cancelTask();
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		// Check for request forgeries.
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Initialise variables.
		$ids = Request::getArray('cid', null, 'post');
		$inc = ($this->getTask() == 'orderup') ? -1 : +1;

		$success = 0;

		foreach ($ids as $id)
		{
			// Load the record and reorder it
			$model = Article::oneOrFail(intval($id));

			if (!$model->move($inc))
			{
				Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError()));
				continue;
			}

			$success++;
		}

		if ($success)
		{
			Cache::clean($this->_option);
			// Set the success message
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED'));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Save ordering
	 *
	 * @return  void
	 */
	public function saveorderTask()
	{
		Request::checkToken();

		$ordering = Request::getArray('order', array());

		if (!Article::saveorder($ordering))
		{
			Notify::error(Lang::txt('COM_CONTENT_ORDERING_ERROR'));
		}
		else
		{
			Notify::success(Lang::txt('COM_CONTENT_ORDERING_SUCCESS'));
		}

		$this->cancelTask();
	}

	/**
	 * Change the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		Request::checkToken();

		$states = array(
			'publish' => array(
				'value' => '1',
				'lang' => 'COM_CONTENT_N_ITEMS_PUBLISHED'
			),
			'unpublish' => array(
				'value' => '0',
				'lang' => 'COM_CONTENT_N_ITEMS_UNPUBLISHED'
			),
			'archive' => array(
				'value' => '2',
				'lang' => 'COM_CONTENT_N_ITEMS_ARCHIVED'
			),
			'trash' => array(
				'value' => '-2',
				'lang' => 'COM_CONTENT_N_ITEMS_TRASHED'
			)
		);

		$state = $states[$this->getTask()];

		$ids = Request::getArray('cid');

		if (!empty($ids))
		{
			$articles = Article::all()->whereIn('id', $ids)->rows();
			$permissionErrors = 0;
			foreach ($articles as $index => $article)
			{
				if (!User::authorise('core.edit.state', $article->asset_id))
				{
					Notify::error("Can't change state drop $index");
					$permissionErrors++;
					continue;
				}
				$article->set('state', $state['value']);
			}
		}

		if ($articles->count() != $permissionErrors)
		{
			if ($articles->save())
			{
				$count = (int) count($articles);
				$title = Inflector::pluralize('article', $count);
				Notify::success(Lang::txt($state['lang'], $count, $title));
			}
		}

		$this->cancelTask();
	}

	/**
	 * Cancel a task
	 *
	 * @return  void
	 */
	public function checkinTask()
	{
		Request::checkToken();

		$ids = Request::getArray('cid');

		$articles = Article::all()
			->whereIn('id', $ids)
			->rows();

		$permissionErrors = 0;

		foreach ($articles as $key => $article)
		{
			if (!User::authorise('core.admin', $article->asset_id))
			{
				if ($article->checked_out != User::getInstance()->get('id'))
				{
					Notify::warning(Lang::txt('COM_CONTENT_CHECKED_IN_ERROR', $article->id));
					$permissionErrors++;
					continue;
				}
			}
			$article->set('checked_out', 0);
			$article->set('checked_out_time', null);
		}

		if ($articles->count() != $permissionErrors)
		{
			if ($articles->save())
			{
				$count = (int) count($articles);
				$title = Inflector::pluralize(Lang::txt('COM_CONTENT_ARTICLE'), $count);
				Notify::success(Lang::txt('COM_CONTENT_N_ITEMS_CHECKED_IN_MORE', $count, $title));
			}
		}

		$this->cancelTask();
	}

	/**
	 * Cancel a task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		if ($this->getTask() == 'cancel')
		{
			$id = Request::getInt('id', 0);

			$article = Article::one($id);
			$articleCheckedOut = $article instanceof Article ? $article->get('checked_out') : 0;

			if ($article && User::getInstance()->get('id') == $articleCheckedOut)
			{
				$article->set('checked_out', 0);
				$article->set('checked_out_time', null);
				$article->save();
			}
		}

		parent::cancelTask();
	}
}
