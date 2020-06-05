<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Menus\Models\Module;
use Components\Menus\Models\Menu;
use Request;
use Notify;
use Lang;
use User;
use App;

/**
 * The Menu List Controller
 */
class Menus extends AdminController
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

		parent::execute();
	}

	/**
	 * Display a list of articles
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$filters = array(
			'parent_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.parent_id',
				'filter_parent_id',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$query = Menu::all()
			->group('id')
			->group('menutype')
			->group('title')
			->group('description');

		// Get records
		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$modules = Menu::getModules();

		$modMenuId = (int) Module::getModMenuId();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('items', $rows)
			->set('modules', $modules)
			->set('modMenuId', $modMenuId)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('cid', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$row = Menu::oneOrNew($id);
		}

		$form = $row->getForm();

		// Output the HTML
		$this->view
			->set('item', $row)
			->set('form', $form)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an item
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');
		$cid = Request::getInt('cid', 0, 'post');

		// Initiate extended database class
		$row = Menu::oneOrNew($cid)->set($fields);

		// Trigger before save event
		$isNew  = $row->isNew();
		$result = Event::trigger('onMenuBeforeSave', array(&$row, $isNew));

		if (in_array(false, $result, true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Trigger after save event
		Event::trigger('onMenuAfterSave', array(&$row, $isNew));

		// Notify of success
		Notify::success(Lang::txt('COM_KB_ARTICLE_SAVED'));

		// Redirect to main listing or go back to edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		if ($this->getTask() == 'save2new')
		{
			$row = Menu::blank();

			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Removes an item
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get items to remove from the request.
		$ids = Request::getArray('cid', array());

		// Make sure the item ids are integers
		\Hubzero\Utility\Arr::toInteger($ids);

		if (!is_array($ids) || count($ids) < 1)
		{
			Notify::error(Lang::txt('COM_MENUS_NO_MENUS_SELECTED'));
		}
		else
		{
			$success = 0;

			foreach ($ids as $id)
			{
				// Load the record
				$model = Menu::oneOrFail(intval($id));

				// Trigger before delete event
				Event::trigger('onMenuBeforeDelete', array('com_menus.menu', $model->getTableName()));

				// Attempt to delete the record
				$menuType = $model->get('menutype');

				if (!$model->destroy())
				{
					Notify::error($model->getError());
					continue;
				}

				if ($menuType == User::getState($this->_option . '.items.menutype'))
				{
					User::setState($this->_option . '.items.menutype', null);
				}

				// Trigger after delete event
				Event::trigger('onMenuAfterDelete', array('com_menus.menu', $model->getTableName()));

				$success++;
			}

			if ($success)
			{
				// Set the success message
				Notify::success(Lang::txt('COM_MENUS_N_MENUS_DELETED', $success));
			}
		}

		$this->cancelTask();
	}

	/**
	 * Rebuild the menu tree.
	 *
	 * @return  bool  False on failure or error, true on success.
	 */
	public function rebuildTask()
	{
		Request::checkToken();

		// Initialise variables.
		$model = Menu::oneOrFail($id);

		if ($model->rebuild())
		{
			// Reorder succeeded.
			Notify::success(Lang::txt('JTOOLBAR_REBUILD_SUCCESS'));
		}
		else
		{
			// Rebuild failed.
			Notify::error(Lang::txt('JTOOLBAR_REBUILD_FAILED', $model->getError()));
		}

		$this->cancelTask();
	}

	/**
	 * Resync component IDs to menu items.
	 *
	 * @return  void
	 */
	public function resyncTask()
	{
		// Initialise variables.
		$db = App::get('db');
		$parts = null;

		// Load a lookup table of all the component id's.
		$components = $db->setQuery(
			'SELECT element, extension_id' .
			' FROM #__extensions' .
			' WHERE type = ' . $db->quote('component')
		)->loadAssocList('element', 'extension_id');

		if ($error = $db->getErrorMsg())
		{
			App::abort(500, $error);
		}

		// Load all the component menu links
		$items = $db->setQuery(
			'SELECT id, link, component_id' .
			' FROM #__menu' .
			' WHERE type = ' . $db->quote('component')
		)->loadObjectList();

		if ($error = $db->getErrorMsg())
		{
			App::abort(500, $error);
		}

		foreach ($items as $item)
		{
			// Parse the link.
			parse_str(parse_url($item->link, PHP_URL_QUERY), $parts);

			// Tease out the option.
			if (isset($parts['option']))
			{
				$option = $parts['option'];

				// Lookup the component ID
				if (isset($components[$option]))
				{
					$componentId = $components[$option];
				}
				else
				{
					// Mismatch. Needs human intervention.
					$componentId = -1;
				}

				// Check for mis-matched component id's in the menu link.
				if ($item->component_id != $componentId)
				{
					// Update the menu table.
					$log = "Link $item->id refers to $item->component_id, converting to $componentId ($item->link)";
					echo "<br/>$log";

					$db->setQuery(
						'UPDATE `#__menu`' .
						' SET component_id = ' . $componentId .
						' WHERE id = ' . $item->id
					)->query();

					if ($error = $db->getErrorMsg())
					{
						App::abort(500, $error);
					}
				}
			}
		}
	}
}
