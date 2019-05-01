<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Categories\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Inflector;
use Hubzero\Access\Access;
use Hubzero\Access\Rules;
use Hubzero\Access\Asset;
use Components\Categories\Models\Category;
use Components\Categories\Admin\Helpers\CategoriesHelper;
use Request;
use Config;
use Notify;
use Route;
use User;
use Lang;
use Date;
use App;

/**
 * Categories controller
 */
class Categories extends AdminController
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
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('archive', 'state');
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
			Notify::warning(Lang::txt('COM_CATEGORIES_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}
		$batchOptions = Request::getArray('batch', array());
		if (empty($batchOptions['category_id']))
		{
			Notify::warning(Lang::txt('COM_CATEGORIES_BATCH_NO_CATEGORY_SELECTED'));
			return $this->cancelTask();
		}
		$action = $batchOptions['move_copy'] == 'm' ? 'moved' : 'copied';

		$params = array(
			'access' => isset($batchOptions['assetgroup_id']) ? $batchOptions['assetgroup_id'] : null,
			'language' => isset($batchOptions['language_id']) ? $batchOptions['language_id'] : null
		);

		$categories = Category::all()->whereIn('id', $ids);
		foreach ($categories as $category)
		{
			$category->moveOrCopyWithChildren($batchOptions['category_id'], $batchOptions['move_copy'], $params);
		}
		Notify::success(Lang::txt('COM_CATEGORIES_BATCH_SUCCESS', $action));

		$this->cancelTask();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . 'search',
				'filter_search',
				''
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . 'access',
				'filter_access',
				0
			),
			'published' => Request::getState(
				$this->_option . '.' . $this->_controller . 'published',
				'filter_published',
				''
			),
			'level' => Request::getState(
				$this->_option . '.' . $this->_controller . 'level',
				'filter_level'
			),
			'language' => Request::getState(
				$this->_option . '.' . $this->_controller . 'language',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'lft'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'extension' => Request::getState(
				$this->_option . '.' . $this->_controller . '.extension',
				'extension'
			)
		);
		$filterableFields = array(
			'category_id' => 'catid',
			'access'      => 'access',
			'extension'   => 'extension'
		);
		$categories = Category::all()
			->select('`#__categories`.*')
			->select('`#__languages`.`title`', 'language_title')
			->join('#__languages', 'language', 'lang_code', 'left outer');

		foreach ($filterableFields as $index => $column)
		{
			if (isset($filters[$index]) && $filters[$index] != '')
			{
				$categories->whereEquals($column, $filters[$index]);
			}
		}

		$searchableFields = array('title', 'description');
		if (!empty($filters['search']))
		{
			$firstSearch = array_shift($searchableFields);
			$categories->whereLike($firstSearch, $filters['search'], 1);
			foreach ($searchableFields as $field)
			{
				$categories->orWhereLike($field, $filters['search'], 1);
			}
			$categories->resetDepth();
		}

		if (isset($filters['published']))
		{
			if ($filters['published'] == '')
			{
				$categories->where('published', '>=', 0);
			}
			elseif ($filters['published'] != '*')
			{
				$categories->whereEquals('published', $filters['published']);
			}
		}

		if (!empty($filters['level']))
		{
			$categories->where('level', '<=', $filters['level']);
		}

		$categories->order($filters['sort'], $filters['sort_Dir']);
		$items = $categories->paginated('limitstart', 'limit');
		$itemsArray = array();
		$ordering = array();

		$levels = Category::all()
			->whereEquals('extension', $filters['extension'])
			->group('level')
			->group('id')
			->order('level', 'asc')
			->rows();

		$fLevels = array();
		foreach ($levels as $level)
		{
			$level = $level->get('level');
			$fLevels[$level] = $level;
		}
		foreach ($items as $item)
		{
			$itemsArray[] = $item;
			$parentId = $item->get('parent_id', '0');
			$ordering[$parentId][] = $item->get('id');
		}
		$extension = $filters['extension'];

		$canDo = CategoriesHelper::getActions($extension, 'category', 0);
		CategoriesHelper::addSubmenu($extension);

		$this->view->set('pagination', $items->pagination);
		$this->view->set('f_levels', $fLevels);
		$this->view->set('filters', $filters);
		$this->view->set('ordering', $ordering);
		$this->view->set('items', $itemsArray);
		$this->view->set('canDo', $canDo);
		$this->view->setLayout('default')->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $category
	 * @return  void
	 */
	public function editTask($category = null)
	{
		$id = Request::getInt('id', 0);
		$extension = Request::getCmd('extension');

		if (!($category instanceof Category))
		{
			$category = Category::oneOrNew($id);
		}

		if (!$category->isNew())
		{
			$assetId = $category->get('asset_id');
			$createdBy = $category->get('created_by');
			$checkedOut = $category->get('checked_out');
			if ($checkedOut)
			{
				if ($checkedOut != User::getInstance()->get('id'))
				{
					Notify::error(Lang::txt('COM_CATEGORY_CHECKED_IN_ERROR', $category->id));
					$this->cancelTask();
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
			$category->set('checked_out', User::getInstance()->get('id'));
			$category->set('checked_out_time', Date::of()->toSql());
			$category->save();
			$lang = 'COM_CATEGORIES_CATEGORY_EDIT_TITLE';
		}
		else
		{
			if (!User::authorise('core.create', $extension))
			{
				App::abort(403, Lang::txt('COM_CONTENT_NOT_AUTHORIZED'));
			}
			$category->set('extension', $extension);
			$lang = 'COM_CATEGORIES_CATEGORY_ADD_TITLE';
		}

		$extensionLang = Lang::txt(strtoupper($extension));
		$title = Lang::txt($lang, $extensionLang);
		$canDo = CategoriesHelper::getActions($extension, 'category', $category->get('id', 0));
		CategoriesHelper::addSubmenu($extension);

		$this->view->set('title', $title);
		$this->view->set('item', $category);
		$this->view->set('form', $category->getForm());
		$this->view->set('canDo', $canDo);
		$this->view->setLayout('edit');
		$this->view->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		$items      = Request::getArray('fields', array());
		$extension  = Request::getCmd('extension');
		$categoryId = Request::getInt('id');

		$category = Category::oneOrNew($categoryId);

		if ($category->get('checked_out'))
		{
			$category->set('checked_out', 0);
			$category->set('checked_out_time', null);
			$category->save();
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
			$category->assetRules = new Rules($rules);
			$category->setNameSpace($extension);
		}
		unset($items['rules']);

		$category->set($items);

		if ($this->_task == 'save2copy')
		{
			$category->set('id', 0);
		}

		if (!$category->saveAsChildOf($items['parent_id']))
		{
			Notify::error($category->getError());
			return $this->editTask($category);
		}

		Notify::success(Lang::txt('COM_CATEGORIES_SAVE_SUCCESS'));

		if ($this->_task == 'apply' || $this->_task == 'save2copy')
		{
			return $this->editTask($category);
		}
		elseif ($this->_task == 'save2new')
		{
			Request::setVar('id', 0);
			return $this->editTask();
		}

		$this->cancelTask();
	}

	/**
	 * Set the state for one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		Request::checkToken();

		$states = array(
			'publish' => array(
				'value' => '1',
				'lang' => 'COM_CATEGORIES_N_ITEMS_PUBLISHED'
			),
			'unpublish' => array(
				'value' => '0',
				'lang' => 'COM_CATEGORIES_N_ITEMS_UNPUBLISHED'
			),
			'archive' => array(
				'value' => '2',
				'lang' => 'COM_CATEGORIES_N_ITEMS_ARCHIVED'
			),
			'trash' => array(
				'value' => '-2',
				'lang' => 'COM_CATEGORIES_N_ITEMS_TRASHED'
			)
		);
		$state = $states[$this->_task];

		$ids = Request::getArray('cid');

		if (!empty($ids))
		{
			$categories = Category::all()
				->whereIn('id', $ids)
				->rows();

			$permissionErrors = 0;
			foreach ($categories as $index => $category)
			{
				if (!User::authorise('core.edit.state', $category->asset_id))
				{
					Notify::error("Can't change state drop $index");
					$permissionErrors++;
					continue;
				}

				$category->set('published', $state['value']);
			}
		}

		$count = count($categories);

		if ($count != $permissionErrors)
		{
			if ($categories->save())
			{
				$title = Inflector::pluralize('category', $count);
				Notify::success(Lang::txt($state['lang'], $count, $title));
			}
		}

		$this->cancelTask();
	}

	/**
	 * Check-in one or more entries
	 *
	 * @return  void
	 */
	public function checkinTask()
	{
		Request::checkToken();

		$ids = Request::getArray('cid');

		$categories = Category::all()
			->whereIn('id', $ids)
			->rows();

		$success = 0;
		foreach ($categories as $category)
		{
			$category->set('checked_out', 0);
			$category->set('checked_out_time', null);

			if (!$category->save())
			{
				Notify::error($category->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			$title = Inflector::pluralize(Lang::txt('COM_CATEGORY'), $success);

			Notify::success(Lang::txt('COM_CATEGORIES_N_ITEMS_CHECKED_IN_MORE', $success, $title));
		}

		$this->cancelTask();
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  boolean  True on success
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
		$extension = Request::getState($this->_option . '.' . $this->_controller . '.extension', 'extension');

		foreach ($ids as $id)
		{
			// Load the record and reorder it
			$model = Category::oneOrFail(intval($id));

			if (!$model->move($inc, $extension))
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
	 * Cancels a task and redirects to default view
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		if ($this->_task == 'cancel')
		{
			$id = Request::getInt('id', 0);
			$category = Category::one($id);
			$categoryCheckedOut = $category instanceof Category ? $category->get('checked_out') : 0;

			if ($category && User::get('id') == $categoryCheckedOut)
			{
				$category->set('checked_out', 0);
				$category->set('checked_out_time', null);
				$category->save();
			}
		}
		$extension = Request::getCmd('extension');

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . ($this->_controller ? '&controller=' . $this->_controller : '') . '&extension=' . $extension, false)
		);
	}

	/**
	 * Save the order for categories
	 *
	 * @return  void
	 */
	public function saveorderTask()
	{
		Request::checkToken();

		$ordering  = Request::getArray('order', array());
		$extension = Request::getState($this->_option . '.' . $this->_controller . '.extension', 'extension');

		if (!Category::saveorder($ordering, $extension))
		{
			Notify::error(Lang::txt('COM_CONTENT_ORDERING_ERROR'));
		}
		else
		{
			Notify::success(Lang::txt('COM_CONTENT_ORDERING_SUCCESS'));
		}

		$this->cancelTask();
	}
}
