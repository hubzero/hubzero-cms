<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Modules\Admin\Controllers;

use Hubzero\Utility\Arr;
use Hubzero\Component\AdminController;
use Components\Modules\Models\Module;
use Components\Modules\Helpers\Modules as ModulesHelper;
use Request;
use Notify;
use Config;
use Route;
use Cache;
use Event;
use Date;
use Lang;
use App;

/**
 * Modules controller class.
 */
class Modules extends AdminController
{
	/**
	 * Determine task and execute it.
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Define standard task mappings.
		$this->registerTask('add', 'edit');

		// Value = 0
		$this->registerTask('unpublish', 'publish');

		// Value = 2
		$this->registerTask('archive', 'publish');

		// Value = -2
		$this->registerTask('trash', 'publish');

		// Value = -3
		$this->registerTask('report', 'publish');

		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');

		parent::execute();
	}

	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				\Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			'position' => Request::getState(
				$this->_option . '.' . $this->_controller . '.position',
				'filter_position',
				''
			),
			'module' => Request::getState(
				$this->_option . '.' . $this->_controller . '.module',
				'filter_module',
				''
			),
			'language' => Request::getState(
				$this->_option . '.' . $this->_controller . '.language',
				'filter_language',
				''
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'filter_state',
				''
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'filter_access',
				0,
				'int'
			),
			'client_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.client_id',
				'filter_client_id',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'position'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$query = Module::all();

		$p = $query->getTableName();
		$u = '#__users';
		$a = '#__viewlevels';
		$m = '#__modules_menu';
		$e = '#__extensions';
		$l = '#__languages';

		$query->select($p . '.*')
			->whereEquals('client_id', $filters['client_id']);

		// Join over the language
		$query
			->select($l . '.title', 'language_title')
			->join($l, $l . '.lang_code', $p . '.language', 'left');

		// Join over the users for the checked out user.
		$query
			->select($u . '.name', 'editor')
			->join($u, $u . '.id', $p . '.checked_out', 'left');

		// Join over the access groups.
		$query
			->select($a . '.title', 'access_level')
			->join($a, $a . '.id', $p . '.access', 'left');

		// Join over the access groups.
		$query
			->select('MIN(' . $m . '.menuid)', 'pages')
			->join($m, $m . '.moduleid', $p . '.id', 'left');

		// Join over the extensions
		$query
			->select($e . '.name', 'name')
			->join($e, $e . '.element', $p . '.module', 'left')
			->group($p . '.id')
			->group($p . '.title')
			->group($p . '.note')
			->group($p . '.position')
			->group($p . '.module')
			->group($p . '.language')
			->group($p . '.checked_out')
			->group($p . '.checked_out_time')
			->group($p . '.published')
			->group($p . '.access')
			->group($p . '.ordering')
			->group($l . '.title')
			->group($u . '.name')
			->group($a . '.title')
			->group($e . '.name')
			->group($l . '.lang_code')
			->group($u . '.id')
			->group($a . '.id')
			->group($m . '.moduleid')
			->group($e . '.element')
			->group($p . '.publish_up')
			->group($p . '.publish_down')
			->group($e . '.enabled');

		// Filter by access level.
		if ($filters['access'])
		{
			$query->whereEquals($p . '.access', (int) $filters['access']);
		}

		// Filter by published state
		if (is_numeric($filters['state']))
		{
			$query->whereEquals($p . '.published', (int) $filters['state']);
		}
		elseif ($filters['state'] === '')
		{
			$query->whereIn($p . '.published', array(0, 1));
		}

		// Filter by position.
		if ($filters['position'])
		{
			if ($filters['position'] == 'none')
			{
				$filters['position'] = '';
			}
			$query->whereEquals($p . '.position', $filters['position']);
		}

		// Filter by module.
		if ($filters['module'])
		{
			$query->whereEquals($p . '.module', $filters['module']);
		}

		// Filter by search
		if (!empty($filters['search']))
		{
			if (stripos($filters['search'], 'id:') === 0)
			{
				$query->whereEquals($p . '.id', (int) substr($filters['search'], 3));
			}
			else
			{
				$query->whereLike($p . '.title', $filters['search'], 1)
					->orWhereLike($p . '.note', $filters['search'], 1)
					->resetDepth();
			}
		}

		// Filter by module.
		if ($filters['language'])
		{
			$query->whereEquals($p . '.language', $filters['language']);
		}

		// Order records
		if ($filters['sort'] == 'name')
		{
			$query->order('name', $filters['sort_Dir']);
			$query->order('ordering', 'asc');
		}
		else if ($filters['sort'] == 'ordering')
		{
			$query->order('position', 'asc');
			$query->order('ordering', $filters['sort_Dir']);
			$query->order('name', 'asc');
		}
		else
		{
			$query->order($filters['sort'], $filters['sort_Dir']);
			$query->order('name', 'asc');
			$query->order('ordering', 'asc');
		}

		/* Pagination query doesn't seem to work in this case.
		We have to pull the entire record list and count it to
		get an accurate total.

		$items = $query
			->paginated('limitstart', 'limit')
			->rows();*/

		$query2 = clone $query;
		$total = count($query2->rows());

		$items = $query
			->limit($filters['limit'])
			->start($filters['start'])
			->rows();

		// Check if there are no matching items
		if (!count($items))
		{
			Notify::warning(Lang::txt('COM_MODULES_MSG_MANAGE_NO_MODULES'));
		}

		$this->view
			->set('filters', $filters)
			->set('items', $items)
			->set('total', $total)
			->display();
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   object  $model
	 * @return  void
	 */
	public function editTask($model = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($model))
		{
			// Incoming
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the record
			$model = Module::oneOrNew($id);
		}

		// Fail if checked out not by 'me'
		if ($model->get('checked_out') && $model->get('checked_out') != User::get('id'))
		{
			Notify::warning(Lang::txt('COM_MODULES_CHECKED_OUT'));
			return $this->cancelTask();
		}

		if ($eid = Request::getInt('eid', 0))
		{
			$db = App::get('db');
			$query = $db->getQuery()
				->select('element')
				->select('client_id')
				->from('#__extensions')
				->whereEquals('extension_id', $eid)
				->whereEquals('type', 'module');
			$db->setQuery($query->toString());

			$ext = $db->loadObject();

			if ($ext)
			{
				$model->set('module', $ext->element);
				$model->set('client_id', $ext->client_id);
			}
		}

		if (!$model->isNew())
		{
			// Checkout the record
			$model->checkout();
			// Check-out failed, display a notice but allow the user to see the record.
			//Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			//return $this->cancelTask();
		}

		// Load the language file
		$model->loadLanguage();

		$this->view
			->set('item', $model)
			->set('form', $model->getForm())
			->setLayout('edit')
			->display();
	}

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
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
		if (!isset($fields['id']))
		{
			$fields['id'] = 0;
		}

		if (isset($fields['publish_up']) && $fields['publish_up'] != '')
		{
			$fields['publish_up']   = Date::of($fields['publish_up'], Config::get('offset'))->toSql();
		}
		if (isset($fields['publish_down']) && $fields['publish_down'] != '')
		{
			$fields['publish_down'] = Date::of($fields['publish_down'], Config::get('offset'))->toSql();
		}

		// Initiate extended database class
		$model = Module::oneOrNew($fields['id'])->set($fields);

		// Get parameters
		$params = Request::getArray('params', array(), 'post');

		$p = $model->params;

		if (is_array($params))
		{
			foreach ($params as $k => $v)
			{
				$p->set($k, $v);
			}
			$model->set('params', $p->toString());
		}

		// Trigger before save event
		$result = Event::trigger('extension.onExtensionBeforeSave', array($this->_option . '.module', &$model, $model->isNew()));

		if (in_array(false, $result, true))
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Store content
		if (!$model->save())
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Update menu assignments
		$menu = Request::getArray('menu', array(), 'post');
		$assignment = (isset($menu['assignment']) ? $menu['assignment'] : 0);
		$assigned   = (isset($menu['assigned']) ? $menu['assigned'] : array());

		if (!$model->saveAssignment($assignment, $assigned))
		{
			Notify::error($model->getError());
			return $this->editTask($model);
		}

		// Trigger after save event
		Event::trigger('extension.onExtensionAfterSave', array($this->_option . '.module', &$model, $model->isNew()));

		// Clean the cache.
		$this->cleanCache();

		// Success message
		Notify::success(Lang::txt('COM_MODULES_SAVE_SUCCESS'));

		// Display the edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($model);
		}

		// Check the record back in
		$model->checkin();

		/*if (!$model->checkin())
		{
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
		}*/

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @return  void
	 */
	public function selectTask()
	{
		// Filter by client.
		$filters = array(
			'client_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.client_id',
				'filter_client_id',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => 'name',
			'sort_Dir' => 'ASC'
		);

		$db = App::get('db');

		// Select the required fields from the table.
		$query = $db->getQuery()
			->select('a.extension_id')
			->select('a.name')
			->select('a.element', 'module')
			->from('#__extensions', 'a')
			->whereEquals('a.type', 'module')
			->whereEquals('a.client_id', (int) $filters['client_id'])
			->whereEquals('a.enabled', 1)
			->order($filters['sort'], $filters['sort_Dir']);

		$db->setQuery($query->toString());
		$items = $db->loadObjectList();

		// Loop through the results to add the XML metadata,
		// and load language support.
		$lang = App::get('language');

		foreach ($items as &$item)
		{
			$path  = \Hubzero\Filesystem\Util::normalizePath(PATH_APP . '/modules/' . $item->module . '/' . $item->module . '.xml');
			$path2 = \Hubzero\Filesystem\Util::normalizePath(PATH_CORE . '/modules/' . $item->module . '/' . $item->module . '.xml');
			if (file_exists($path))
			{
				$item->xml = simplexml_load_file($path);
			}
			else if (file_exists($path2))
			{
				$item->xml = simplexml_load_file($path2);
			}
			else
			{
				$item->xml = null;
			}

			// 1.5 Format; Core files or language packs then
			// 1.6 3PD Extension Support
			$lang->load($item->module . '.sys', PATH_APP . '/modules/' . $item->module, null, false, true) ||
			$lang->load($item->module . '.sys', PATH_CORE . '/modules/' . $item->module, null, false, true);

			$item->name = Lang::txt($item->name);

			if (isset($item->xml) && $text = trim($item->xml->description))
			{
				$item->desc = Lang::txt($text);
			}
			else
			{
				$item->desc = Lang::txt('COM_MODULES_NODESCRIPTION');
			}
		}
		$items = \Hubzero\Utility\Arr::sortObjects($items, 'name', 1, true, $lang->getLocale());

		$this->view
			->set('items', $items)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @return  void
	 */
	public function positionsTask()
	{
		$filters = array(
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.limit',
				'limit',
				\Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.limitstart',
				'limitstart',
				0,
				'int'
			),
			'type' => Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.type',
				'filter_type',
				''
			),
			'template' => Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.template',
				'filter_template',
				''
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.search',
				'filter_search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.state',
				'filter_state',
				''
			),
			'client_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.client_id',
				'filter_client_id',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.sort',
				'filter_order',
				'position'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.positions.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);
		$client = \Hubzero\Base\ClientManager::client($filters['client_id']);

		$positions = array();

		if ($filters['type'] != 'template')
		{
			// Get the database object and a new query object.
			$query = Module::all()
				->select('DISTINCT(position)', 'value')
				->whereEquals('client_id', (int) $filters['client_id']);
			if ($filters['search'])
			{
				$query->whereLike('position', $filters['search']);
			}

			$rows = $query->rows();

			foreach ($rows as $position)
			{
				$positions[$position->get('value')] = array();
			}
		}

		// Load the positions from the installed templates.
		foreach (ModulesHelper::getTemplates($filters['client_id']) as $template)
		{
			$path = \Hubzero\Filesystem\Util::normalizePath(PATH_APP.'/templates/'.$template->element.'/templateDetails.xml');

			if (!file_exists($path))
			{
				$path = \Hubzero\Filesystem\Util::normalizePath(PATH_CORE.'/templates/'.$template->element.'/templateDetails.xml');
			}

			if (file_exists($path))
			{
				$xml = simplexml_load_file($path);
				if (isset($xml->positions[0]))
				{
					Lang::load('tpl_'.$template->element.'.sys', PATH_APP.'/templates/'.$template->element, null, false, true) ||
					Lang::load('tpl_'.$template->element.'.sys', PATH_CORE.'/templates/'.$template->element, null, false, true);

					foreach ($xml->positions[0] as $position)
					{
						$value = (string)$position['value'];
						$label = (string)$position;
						if (!$value)
						{
							$value    = $label;
							$label    = preg_replace('/[^a-zA-Z0-9_\-]/', '_', 'TPL_'.$template->element.'_POSITION_'.$value);
							$altlabel = preg_replace('/[^a-zA-Z0-9_\-]/', '_', 'COM_MODULES_POSITION_'.$value);
							if (!Lang::hasKey($label) && Lang::hasKey($altlabel))
							{
								$label = $altlabel;
							}
						}
						if ($filters['type'] =='user' || ($filters['state'] != '' && $filters['state'] != $template->enabled))
						{
							unset($positions[$value]);
						}
						elseif (preg_match(chr(1).$filters['search'].chr(1).'i', $value) && ($filters['template']=='' || $filters['template']==$template->element))
						{
							if (!isset($positions[$value]))
							{
								$positions[$value] = array();
							}
							$positions[$value][$template->name] = $label;
						}
					}
				}
			}
		}

		$total = count($positions);

		if ($filters['start'] >= $total)
		{
			$filters['start'] = $filters['start'] < $filters['limit'] ? 0 : $filters['start'] - $filters['limit'];
		}

		if ($filters['sort'] == 'value')
		{
			if ($filters['sort_Dir'] == 'asc')
			{
				ksort($positions);
			}
			else
			{
				krsort($positions);
			}
		}
		else
		{
			if ($filters['sort_Dir'] == 'asc')
			{
				asort($positions);
			}
			else
			{
				arsort($positions);
			}
		}

		$items = array_slice($positions, $filters['start'], $filters['limit'] ? $filters['limit'] : null);

		$this->view
			->set('filters', $filters)
			->set('items', $items)
			->set('total', $total)
			->display();
	}

	/**
	 * Removes an item.
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

		// Incoming
		$ids = Request::getArray('cid', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$success = 0;

		foreach ($ids as $id)
		{
			// Load the record
			$model = Module::oneOrFail(intval($id));
			$model->set('published', -2);

			if (!$model->save())
			{
				Notify::error($model->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			Notify::success(Lang::txt('COM_MODULES_N_ITEMS_TRASHED', $success));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('cid', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$success = 0;

		foreach ($ids as $id)
		{
			// Load the record
			$model = Module::oneOrFail(intval($id));

			// Trigger before delete event
			Event::trigger('extension.onExtensionBeforeDelete', array('com_modules.module', $model->getTableName()));

			// Attempt to delete the record
			if (!$model->destroy())
			{
				Notify::error($model->getError());
				continue;
			}

			// Trigger after delete event
			Event::trigger('extension.onExtensionAfterDelete', array('com_modules.module', $model->getTableName()));

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			Notify::success(Lang::txt('COM_MODULES_N_ITEMS_DELETED', $success));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function duplicateTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('cid', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$success = 0;

		foreach ($ids as $id)
		{
			// Load the record
			$model = Module::oneOrFail(intval($id));

			// Attempt to delete the record
			if (!$model->duplicate())
			{
				Notify::error($model->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			Notify::success(Lang::txt('COM_MODULES_N_MODULES_DUPLICATED', $success));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get items to publish from the request.
		$cid   = Request::getArray('cid', array());
		$data  = array(
			'publish'   => 1,
			'unpublish' => 0,
			'archive'   => 2,
			'trash'     => -2,
			'report'    => -3
		);
		$task  = $this->getTask();
		$value = Arr::getValue($data, $task, 0, 'int');

		$success = 0;

		foreach ($cid as $id)
		{
			// Load the record
			$model = Module::oneOrFail(intval($id));

			// Set state
			$model->set('published', $value);

			if (!$model->save())
			{
				Notify::error($model->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			if ($value == 1)
			{
				$ntext = 'COM_MODULES_N_ITEMS_PUBLISHED';
			}
			elseif ($value == 0)
			{
				$ntext = 'COM_MODULES_N_ITEMS_UNPUBLISHED';
			}
			elseif ($value == 2)
			{
				$ntext = 'COM_MODULES_N_ITEMS_ARCHIVED';
			}
			else
			{
				$ntext = 'COM_MODULES_N_ITEMS_TRASHED';
			}

			Notify::success(Lang::txts($ntext, $success));
		}

		// Redirect back to the listing
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

		foreach ($ids as $id)
		{
			// Load the record and reorder it
			$model = Module::oneOrFail(intval($id));

			if (!$model->move($inc))
			{
				Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError()));
				continue;
			}

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED'));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 */
	public function saveorderTask()
	{
		// Check for request forgeries.
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get the input
		$pks   = Request::getArray('cid', null, 'post');
		$order = Request::getArray('order', null, 'post');

		// Sanitize the input
		Arr::toInteger($pks);
		Arr::toInteger($order);

		// Save the ordering
		$return = Module::saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_REORDER_FAILED'));
		}
		else
		{
			// Clean the cache.
			$this->cleanCache();

			// Reorder succeeded.
			Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return  void
	 */
	public function checkinTask()
	{
		// Check for request forgeries.
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('cid', null, 'post');

		foreach ($ids as $id)
		{
			$model = Module::oneOrFail(intval($id));
			$model->checkin();

			/*if (!$model->checkin())
			{
				Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				continue;
			}*/
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string   $key  The name of the primary key of the URL variable.
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancelTask()
	{
		// Attempt to check-in the current record.
		if ($id = Request::getInt('id', 0))
		{
			$model = Module::oneOrNew($id);

			if ($model->get('checked_out') && $model->get('checked_out') == User::get('id'))
			{
				$model->checkin();
				// Check-in failed, go back to the record and display a notice.
				/*if (!$model->checkin())
				{
					Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				}*/
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . $this->getRedirectToListAppend(), false)
		);
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToListAppend()
	{
		$append = '';

		if ($tmpl = Request::getCmd('tmpl'))
		{
			$append .= '&tmpl=' . $tmpl;
		}

		return $append;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 * @return  string   The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl   = Request::getCmd('tmpl');
		$layout = Request::getCmd('layout', 'edit');
		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout)
		{
			$append .= '&task=' . $layout;
		}

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	/**
	 * Clean cached data
	 *
	 * @return  void
	 */
	public function cleanCache()
	{
		Cache::clean($this->_option);
	}

	/**
	 * Batch process records
	 *
	 * @return  void
	 */
	public function batchTask()
	{
		$commands = Request::getArray('batch', array(), 'post');

		// Sanitize user ids.
		$pks = array_unique($pks);
		\Hubzero\Utility\Arr::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			Notify::error(Lang::txt('JGLOBAL_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		$done = false;

		if (!empty($commands['position_id']))
		{
			$cmd = \Hubzero\Utility\Arr::getValue($commands, 'move_copy', 'c');

			if (!empty($commands['position_id']))
			{
				if ($cmd == 'c')
				{
					$result = $this->batchCopy($commands['position_id'], $pks, $contexts);

					if (is_array($result))
					{
						$pks = $result;
					}
					else
					{
						return $this->cancelTask();
					}
				}
				elseif ($cmd == 'm' && !$this->batchMove($commands['position_id'], $pks, $contexts))
				{
					return $this->cancelTask();
				}

				$done = true;
			}
		}

		if (!empty($commands['assetgroup_id']))
		{
			if (!$this->batchAccess($commands['assetgroup_id'], $pks, $contexts))
			{
				return $this->cancelTask();
			}

			$done = true;
		}

		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return $this->cancelTask();
			}

			$done = true;
		}

		if (!$done)
		{
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return $this->cancelTask();
		}

		// Clear the cache
		$this->cleanCache();

		$this->cancelTask();
	}

	/**
	 * Batch copy modules to a new position or current.
	 *
	 * @param   integer  $value      The new value matching a module position.
	 * @param   array    $pks        An array of row IDs.
	 * @param   array    $contexts   An array of item contexts.
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		// Set the variables
		$table = $this->getTable();
		$i = 0;

		foreach ($pks as $pk)
		{
			if (User::authorise('core.create', 'com_modules'))
			{
				$model = Module::oneOrFail($pk);

				// Set the new position
				if ($value == 'noposition')
				{
					$position = '';
				}
				elseif ($value == 'nochange')
				{
					$position = $model->get('position');
				}
				else
				{
					$position = $value;
				}
				$model->set('position', $position);

				// Alter the title if necessary
				$data = $model->generateNewTitle($model->get('title'), $model->get('position'));
				$model->set('title', $data['0']);

				// Reset the ID because we are making a copy
				$model->set('id', 0);

				// Unpublish the new module
				$model->set('published', 0);

				if (!$model->save())
				{
					$this->setError($model->getError());
					return false;
				}

				// Get the new item ID
				$newId = $model->get('id');

				// Add the new ID to the array
				$newIds[$i]	= $newId;
				$i++;

				// Now we need to handle the module assignments
				$db = App::get('db');
				$query = $db->getQuery()
					->select('menuid')
					->from('#__modules_menu')
					->whereEquals('moduleid', $pk);

				$db->setQuery($query->toString());
				$menus = $db->loadColumn();

				// Insert the new records into the table
				foreach ($menus as $menu)
				{
					$query = $db->getQuery()
						->insert('#__modules_menu')
						->values(array(
							'moduleid' => $newId,
							'menuid'   => $menu
						));

					$db->setQuery($query->toString());
					$db->query();
				}
			}
			else
			{
				$this->setError(Lang::txt('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Batch move modules to a new position or current.
	 *
	 * @param   integer  $value     The new value matching a module position.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 */
	protected function batchMove($value, $pks, $contexts)
	{
		// Set the variables
		$i = 0;

		foreach ($pks as $pk)
		{
			if (User::authorise('core.edit', 'com_modules'))
			{
				$model = Module::oneOrFail($pk);

				// Set the new position
				if ($value == 'noposition')
				{
					$position = '';
				}
				elseif ($value == 'nochange')
				{
					$position = $model->get('position');
				}
				else
				{
					$position = $value;
				}
				$model->set('position', $position);

				// Alter the title if necessary
				$data = $model->generateNewTitle($model->get('title'), $model->get('position'));
				$model->set('title', $data['0']);

				// Unpublish the moved module
				$model->set('published', 0);

				if (!$model->save())
				{
					$this->setError($model->getError());
					return false;
				}
			}
			else
			{
				$this->setError(Lang::txt('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

}
