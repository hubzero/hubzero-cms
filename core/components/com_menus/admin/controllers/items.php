<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Menus\Helpers\Menus as MenusHelper;
use Components\Menus\Models\Menu;
use Components\Menus\Models\Item;
use Filesystem;
use Component;
use Request;
use Notify;
use Html;
use Lang;
use User;
use App;

/**
 * The Menu Item Controller
 */
class Items extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('apply', 'save');
		$this->registerTask('save2copy', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');
		$this->registerTask('trash', 'state');
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');
		$this->registerTask('unsetDefault', 'setDefault');

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
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'filter_search',
				''
			),
			'published' => Request::getState(
				$this->_option . '.' . $this->_controller . '.published',
				'filter_published',
				''
			),
			'language' => Request::getState(
				$this->_option . '.' . $this->_controller . '.language',
				'filter_language',
				''
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'filter_access',
				0,
				'int'
			),
			'level' => Request::getState(
				$this->_option . '.' . $this->_controller . '.level',
				'filter_level',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'lft'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'asc'
			)
		);

		$menuType = Request::getString('menutype', null);

		if ($menuType)
		{
			if ($menuType != User::getState($this->_option . '.' . $this->_controller . '.menutype'))
			{
				User::setState($this->_option . '.' . $this->_controller . '.menutype', $menuType);
				Request::setVar('limitstart', 0);
			}
		}
		else
		{
			$menuType = User::getState($this->_option . '.' . $this->_controller . '.menutype');

			if (!$menuType)
			{
				$menuType = Menu::all()
					->order('title', 'asc')
					->start(0)
					->limit(0)
					->row()
					->get('menutype');

				User::setState($this->_option . '.' . $this->_controller . '.menutype', $menuType);
			}
		}

		$filters['menutype'] = $menuType;

		$query = Item::all();

		$a = $query->getTableName();

		// Select all fields from the table.
		$query->select($a . '.id')
			->select($a . '.menutype')
			->select($a . '.title')
			->select($a . '.alias')
			->select($a . '.note')
			->select($a . '.path')
			->select($a . '.link')
			->select($a . '.type')
			->select($a . '.parent_id')
			->select($a . '.level')
			->select($a . '.published', 'apublished')
			->select($a . '.component_id')
			->select($a . '.ordering')
			->select($a . '.checked_out')
			->select($a . '.checked_out_time')
			->select($a . '.browserNav')
			->select($a . '.access')
			->select($a . '.img')
			->select($a . '.template_style_id')
			->select($a . '.params')
			->select($a . '.lft')
			->select($a . '.rgt')
			->select($a . '.home')
			->select($a . '.language')
			->select($a . '.client_id');
		$query->select('CASE ' . $a . '.type' .
			' WHEN \'component\' THEN ' . $a . '.published+2*(e.enabled-1) ' .
			' WHEN \'url\' THEN ' . $a . '.published+2 ' .
			' WHEN \'alias\' THEN ' . $a . '.published+4 ' .
			' WHEN \'separator\' THEN ' . $a . '.published+6 ' .
			' END', 'published');
		//$query->from($query->getTableName(), 'a');

		// Join over the language
		$query->select('l.title', 'language_title');
		$query->select('l.image', 'image');
		$query->join('`#__languages` AS l', 'l.lang_code', $a . '.language', 'left');

		// Join over the users.
		$query->select('u.name', 'editor');
		$query->join('`#__users` AS u', 'u.id', $a . '.checked_out', 'left');

		// Join over components
		$query->select('c.element', 'componentname');
		$query->join('`#__extensions` AS c', 'c.extension_id', $a . '.component_id', 'left');

		// Join over the asset groups.
		$query->select('ag.title', 'access_level');
		$query->join('`#__viewlevels` AS ag', 'ag.id', $a . '.access', 'left');

		// Join over the associations.
		$assoc = isset($app->menu_associations) ? $app->menu_associations : 0;
		if ($assoc)
		{
			$query->select('COUNT(asso2.id)>1', 'association');
			$query->joinRaw('`#__associations` AS asso', 'asso.id = ' . $a . '.id AND asso.context=\'com_menus.item\'', 'left');
			$query->join('`#__associations` AS asso2', 'asso2.key', 'asso.key', 'left');
			$query->group($a . '.id');
		}

		// Join over the extensions
		$query->select('e.name', 'name');
		$query->join('`#__extensions` AS e', 'e.extension_id', $a . '.component_id', 'left');

		// Exclude the root category.
		$query->where($a . '.id', '>', 1);
		$query->whereEquals($a . '.client_id', 0);

		// Filter on the published state.
		$published = $filters['published'];
		if (is_numeric($published))
		{
			$query->whereEquals($a . '.published', (int) $published);
		}
		elseif ($published === '')
		{
			$query->whereIn($a . '.published', array(0, 1));
		}

		// Filter by search in title, alias or id
		if ($search = trim($filters['search']))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->whereEquals('a.id', (int) substr($search, 3));
			}
			elseif (stripos($search, 'link:') === 0)
			{
				if ($search = substr($search, 5))
				{
					$query->whereLink($a . '.link', $search);
				}
			}
			else
			{
				$query->whereLike($a . '.title', $search, 1)
					->orWhereLike($a . '.alias', $search, 1)
					->orWhereLike($a . '.note', $search, 1)
					->resetDepth();
			}
		}

		// Filter the items over the parent id if set.
		$parentId = $filters['parent_id'];
		if (!empty($parentId))
		{
			$query->whereEquals('p.id', (int)$parentId);
		}

		// Filter the items over the menu id if set.
		$menuType = $filters['menutype'];
		if (!empty($menuType))
		{
			$query->whereEquals($a . '.menutype', $menuType);
		}

		// Filter on the access level.
		if ($access = $filters['access'])
		{
			$query->whereEquals($a . '.access', (int) $access);
		}

		// Implement View Level Access
		if (!User::authorise('core.admin'))
		{
			$query->whereIn($a . '.access', User::getAuthorisedViewLevels());
		}

		// Filter on the level.
		if ($level = $filters['level'])
		{
			$query->where($a . '.level', '<=', (int) $level);
		}

		// Filter on the language.
		if ($language = $filters['language'])
		{
			$query->whereEquals($a . '.language', $language);
		}

		// Get records
		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$ordering = array();
		$lang = App::get('language');

		// Preprocess the list of items to find ordering divisions.
		foreach ($rows as $item)
		{
			$ordering[$item->get('parent_id')][] = $item->get('id');

			// item type text
			switch ($item->get('type'))
			{
				case 'url':
					$value = Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL');
					break;

				case 'alias':
					$value = Lang::txt('COM_MENUS_TYPE_ALIAS');
					break;

				case 'separator':
					$value = Lang::txt('COM_MENUS_TYPE_SEPARATOR');
					break;

				case 'component':
				default:
					// load language
						$lang->load($item->get('componentname') . '.sys', PATH_APP, null, false, true)
					||	$lang->load($item->get('componentname') . '.sys', Component::path($item->get('componentname')) . '/admin', null, false, true);

					if (!empty($item->get('componentname')))
					{
						$value = Lang::txt($item->get('componentname'));
						$vars  = null;

						parse_str($item->get('link'), $vars);

						if (isset($vars['view']))
						{
							// Attempt to load the view xml file.
							$file = Component::path($item->get('componentname')) . '/site/views/' . $vars['view'] . '/metadata.xml';

							if (Filesystem::exists($file) && $xml = simplexml_load_file($file))
							{
								// Look for the first view node off of the root node.
								if ($view = $xml->xpath('view[1]'))
								{
									if (!empty($view[0]['title']))
									{
										$vars['layout'] = isset($vars['layout']) ? $vars['layout'] : 'default';

										// Attempt to load the layout xml file.
										// If Alternative Menu Item, get template folder for layout file
										if (strpos($vars['layout'], ':') > 0)
										{
											// Use template folder for layout file
											$temp = explode(':', $vars['layout']);
											$file = PATH_CORE . '/templates/' . $temp[0] . '/html/' . $item->get('componentname') . '/' . $vars['view'] . '/' . $temp[1] . '.xml';

											// Load template language file
												$lang->load('tpl_' . $temp[0] . '.sys', PATH_CORE, null, false, true)
											||	$lang->load('tpl_' . $temp[0] . '.sys', PATH_APP . '/templates/' . $temp[0], null, false, true)
											||	$lang->load('tpl_' . $temp[0] . '.sys', PATH_CORE . '/templates/' . $temp[0], null, false, true);
										}
										else
										{
											// Get XML file from component folder for standard layouts
											$file = Component::path($item->get('componentname')) . '/site/views/' . $vars['view'] . '/tmpl/' . $vars['layout'] . '.xml';
										}

										if (Filesystem::exists($file) && $xml = simplexml_load_file($file))
										{
											// Look for the first view node off of the root node.
											if ($layout = $xml->xpath('layout[1]'))
											{
												if (!empty($layout[0]['title']))
												{
													$value .= ' » ' . Lang::txt(trim((string) $layout[0]['title']));
												}
											}
											if (!empty($layout[0]->message[0]))
											{
												$item->set('item_type_desc', Lang::txt(trim((string) $layout[0]->message[0])));
											}
										}
									}
								}
								unset($xml);
							}
							else
							{
								// Special case for absent views
								$value .= ' » ' . Lang::txt($item->get('componentname') . '_' . $vars['view'] . '_VIEW_DEFAULT_TITLE');
							}
						}
					}
					else
					{
						if (preg_match("/^index.php\?option=([a-zA-Z\-0-9_]*)/", $item->get('link'), $result))
						{
							$value = Lang::txt('COM_MENUS_TYPE_UNEXISTING', $result[1]);
						}
						else
						{
							$value = Lang::txt('COM_MENUS_TYPE_UNKNOWN');
						}
					}
					break;
			}
			$item->set('item_type', $value);
		}

		// Levels filter.
		$options = array();
		$options[] = Html::select('option', '1', Lang::txt('J1'));
		$options[] = Html::select('option', '2', Lang::txt('J2'));
		$options[] = Html::select('option', '3', Lang::txt('J3'));
		$options[] = Html::select('option', '4', Lang::txt('J4'));
		$options[] = Html::select('option', '5', Lang::txt('J5'));
		$options[] = Html::select('option', '6', Lang::txt('J6'));
		$options[] = Html::select('option', '7', Lang::txt('J7'));
		$options[] = Html::select('option', '8', Lang::txt('J8'));
		$options[] = Html::select('option', '9', Lang::txt('J9'));
		$options[] = Html::select('option', '10', Lang::txt('J10'));

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('ordering', $ordering)
			->set('f_levels', $options)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function addTask()
	{
		User::setState('com_menus.edit.item.type', null);
		User::setState('com_menus.edit.item.link', null);

		$this->editTask();
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
			$row = Item::oneOrNew($id);
		}

		// Fail if checked out not by 'me'
		if ($row->get('checked_out') && $row->get('checked_out') != User::get('id'))
		{
			Notify::warning(Lang::txt('JLIB_HTML_CHECKED_OUT'));
			return $this->cancelTask();
		}

		if (!$row->isNew())
		{
			// Checkout the record
			$row->checkout();
		}

		$data = User::getState('com_menus.edit.item.data', array());
		if (!empty($data))
		{
			$row->set($data);
			User::setState('com_menus.edit.item.data', array());
		}

		if ($type = Request::getState('com_menus.edit.item.type', 'type'))
		{
			$row->set('type', $type);
			User::setState('com_menus.edit.item.type', null);
		}

		if ($link = Request::getState('com_menus.edit.item.link', 'link'))
		{
			$row->set('link', $link);
			User::setState('com_menus.edit.item.link', null);
		}

		if ($row->isNew())
		{
			$row->set('parent_id', Request::getState('com_menus.edit.item.parent_id', 'parent_id'));
			$row->set('menutype', Request::getState('com_menus.edit.item.menutype', 'menutype'));
			$row->set('params', '{}');

			User::setState('com_menus.edit.item.parent_id', null);
			User::setState('com_menus.edit.item.menutype', null);
		}

		User::setState('com_menus.edit.item.type', $row->get('type'));

		switch ($row->get('type'))
		{
			case 'alias':
				$row->set('component_id', 0);
				$args = array();

				parse_str(parse_url($row->get('link'), PHP_URL_QUERY), $args);
				break;

			case 'separator':
				$row->set('link', '');
				$row->set('component_id', 0);
				break;

			case 'url':
				$row->set('component_id', 0);

				parse_str(parse_url($row->get('link'), PHP_URL_QUERY));
				break;

			case 'component':
			default:
				// Enforce a valid type.
				$row->set('type', 'component');

				// Ensure the integrity of the component_id field is maintained, particularly when changing the menu item type.
				$args = array();
				parse_str(parse_url($row->get('link'), PHP_URL_QUERY), $args);

				if (isset($args['option']))
				{
					// Load the language file for the component.
					$lang = Lang::getRoot();
						$lang->load($args['option'], PATH_APP, null, false, true)
					||	$lang->load($args['option'], Component::path($args['option']) . '/admin', null, false, true);

					// Determine the component id.
					$component = Component::load($args['option']);
					if (isset($component->id))
					{
						$row->set('component_id', $component->id);
					}
				}
				break;
		}

		$params = $row->params->toArray();

		// Merge the request arguments in to the params for a component.
		if ($row->get('type') == 'component')
		{
			// Note that all request arguments become reserved parameter names.
			$row->set('request', $args);
			$params = array_merge($params, $args);
		}

		if ($row->get('type') == 'alias')
		{
			// Note that all request arguments become reserved parameter names.
			$args = array();
			parse_str(parse_url($row->get('link'), PHP_URL_QUERY), $args);
			$params = array_merge($params, $args);
		}

		if ($row->get('type') == 'url')
		{
			// Note that all request arguments become reserved parameter names.
			$args = array();
			parse_str(parse_url($row->get('link'), PHP_URL_QUERY), $args);
			$params = array_merge($params, $args);
		}

		$row->set('params', $params);
		$row->paramsRegistry = null;
		$row->set('menuordering', $row->get('id'));

		if (App::has('menu_associations') && App::get('menu_associations') != 0)
		{
			if ($pk != null)
			{
				$row->set('associations', MenusHelper::getAssociations($row->get('id')));
			}
			else
			{
				$row->set('associations', array());
			}
		}

		$form = $row->getForm();

		$modules = \Components\Menus\Models\Module::getModules($row->get('id'));

		// Output the HTML
		$this->view
			->set('item', $row)
			->set('form', $form)
			->set('modules', $modules)
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

		// Initiate extended database class
		$row = Item::oneOrNew($fields['id']);

		// Trigger before save event
		$isNew  = $row->isNew();
		$parent_id = $row->get('parent_id');

		// Check for the special 'request' entry.
		if ($fields['type'] == 'component'
		 && isset($fields['request'])
		 && is_array($fields['request'])
		 && !empty($fields['request']))
		{
			// Parse the submitted link arguments.
			$args = array();
			parse_str(parse_url($fields['link'], PHP_URL_QUERY), $args);

			// Merge in the user supplied request arguments.
			$args = array_merge($args, $fields['request']);
			$fields['link'] = 'index.php?' . urldecode(http_build_query($args, '', '&'));
			unset($fields['request']);
		}

		$row->set($fields);

		// Fail if checked out not by 'me'
		if (!$isNew && $row->get('checked_out') && $row->get('checked_out') != User::get('id'))
		{
			Notify::warning(Lang::txt('COM_MENUS_CHECKED_OUT'));
			return $this->cancelTask();
		}

		if ($this->getTask() == 'save2copy' && $row->get('id'))
		{
			$row->set('id', 0);
			$row->set('title', $row->get('title') . ' (copy)');
			$row->set('alias', $row->get('alias') . '_copy');
		}

		$result = Event::trigger('onMenuBeforeSaveItem', array(&$row, $isNew));

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

		if (!$isNew && $parent_id != $row->get('parent_id'))
		{
			if (!$row->moveByReference($row->get('parent_id'), 'last-child', $row->get('id')))
			{
				Notify::error($row->getError());
				return $this->editTask($row);
			}
		}

		// Trigger after save event
		Event::trigger('onMenuAfterSaveItem', array(&$row, $isNew));

		// Notify of success
		Notify::success(Lang::txt('COM_MENUS_SAVE_SUCCESS'));

		// Redirect to main listing or go back to edit form
		if ($this->getTask() == 'apply')
		{
			User::setState('com_menus.edit.item.data', null);
			User::setState('com_menus.edit.item.type', null);
			User::setState('com_menus.edit.item.link', null);

			return $this->editTask($row);
		}

		$row
			->purgeCache()
			->checkin();

		User::setState('com_menus.edit.item.data', null);
		User::setState('com_menus.edit.item.type', null);
		User::setState('com_menus.edit.item.link', null);

		if ($this->getTask() == 'save2new')
		{
			User::setState('com_menus.edit.item.menutype', $row->get('menutype'));

			$nw = Item::blank();
			$nw->set('menutype', $row->get('menutype'));

			return $this->editTask($nw);
		}

		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 * 
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('cid', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_MENUS_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		$success = 0;
		$state = $this->getTask() == 'publish' ? Item::STATE_PUBLISHED : Item::STATE_UNPUBLISHED;
		switch ($this->getTask())
		{
			case 'publish':
				$state = Item::STATE_PUBLISHED;
			break;

			case 'unpublish':
				$state = Item::STATE_UNPUBLISHED;
			break;

			case 'trash':
				$state = Item::STATE_TRASHED;
			break;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			$row = Item::oneOrFail(intval($id));
			$row->set('published', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		// Set message
		if ($success)
		{
			if ($state == Item::STATE_PUBLISHED)
			{
				Notify::success(Lang::txt('COM_MENUS_N_ITEMS_PUBLISHED', $success));
			}
			elseif ($state == Item::STATE_TRASHED)
			{
				Notify::success(Lang::txt('COM_MENUS_N_ITEMS_TRASHED', $success));
			}
			else
			{
				Notify::success(Lang::txt('COM_MENUS_N_ITEMS_UNPUBLISHED', $success));
			}
		}

		// Set the redirect
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
		$ids = Request::getVar('cid', array(), 'post');

		foreach ($ids as $id)
		{
			$model = Item::oneOrFail(intval($id));
			$model->checkin();
		}

		// Redirect back to the listing
		$this->cancelTask();
	}

	/**
	 * Reorder entries
	 * 
	 * @return  void
	 */
	public function reorderTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$id = Request::getArray('cid', array());
		if (is_array($id))
		{
			$id = (!empty($id) ? $id[0] : 0);
		}

		// Ensure we have an ID to work with
		if (!$id)
		{
			Notify::warning(Lang::txt('COM_MENUS_NO_ITEM_SELECTED'));
			return $this->cancelTask();
		}

		// Get the element being moved
		$model = Item::oneOrFail($id);

		$move = ($this->getTask() == 'orderup') ? -1 : +1;

		if (!$model->move($move))
		{
			Notify::error($model->getError());
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return  bool  False on failure or error, true on success.
	 */
	public function rebuildTask()
	{
		Request::checkToken();

		// Initialise variables.
		$model = Item::blank();

		if ($model->rebuild(1))
		{
			// Reorder succeeded.
			Notify::success(Lang::txt('COM_MENUS_ITEMS_REBUILD_SUCCESS'));
		}
		else
		{
			// Rebuild failed.
			Notify::error(Lang::txt('COM_MENUS_ITEMS_REBUILD_FAILED'));
		}

		$this->cancelTask();
	}

	/**
	 * Save the order for items
	 *
	 * @return  void
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
		$pks   = Request::getArray('cid', array(), 'post');
		$order = Request::getArray('order', array(), 'post');

		// Sanitize the input
		Arr::toInteger($pks);
		Arr::toInteger($order);

		// Save the ordering
		$return = Item::saveorder($pks, $order);

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
	 * Method to set the home property for a list of items
	 *
	 * @return  void
	 */
	public function setDefaultTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Get items to publish from the request.
		$cid   = Request::getArray('cid', array(), '');
		$data  = array('setDefault' => 1, 'unsetDefault' => 0);
		$task  = $this->getTask();
		$value = \Hubzero\Utility\Arr::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			Notify::warning(Lang::txt('COM_MENUS_NO_ITEM_SELECTED'));
		}
		else
		{
			// Make sure the item ids are integers
			\Hubzero\Utility\Arr::toInteger($cid);

			$model = Item::oneOrFail($cid);

			// Publish the items.
			if (!$model->setHome($value))
			{
				Notify::error($model->getError());
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_MENUS_ITEMS_SET_HOME';
				}
				else
				{
					$ntext = 'COM_MENUS_ITEMS_UNSET_HOME';
				}
				Notify::success(Lang::txts($ntext, count($cid)));
			}
		}

		$this->cancelTask();
	}

	/**
	 * Sets the type of the menu item currently being edited.
	 *
	 * @return  void
	 */
	public function setTypeTask()
	{
		// Get the posted values from the request.
		$data = Request::getArray('fields', array(), 'post');
		$recordId = Request::getInt('id');

		// Get the type.
		$type = $data['type'];

		$type = json_decode(base64_decode($type));
		$title = isset($type->title) ? $type->title : null;
		$recordId = isset($type->id) ? $type->id : 0;

		if ($title != 'alias' && $title != 'separator' && $title != 'url')
		{
			$title = 'component';
		}

		User::setState('com_menus.edit.item.type', $title);
		if ($title == 'component')
		{
			if (isset($type->request))
			{
				$component = Component::load($type->request->option);
				$data['component_id'] = $component->id;

				User::setState('com_menus.edit.item.link', 'index.php?' . with(new \Hubzero\Utility\Uri())->buildQuery((array) $type->request));
			}
		}
		// If the type is alias you just need the item id from the menu item referenced.
		elseif ($title == 'alias')
		{
			User::setState('com_menus.edit.item.link', 'index.php?Itemid=');
		}

		unset($data['request']);
		$data['type'] = $title;
		if (Request::getCmd('fieldtype') == 'type')
		{
			$data['link'] = User::getState('com_menus.edit.item.link');
		}

		//Save the data in the session.
		User::setState('com_menus.edit.item.data', $data);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&controller=' . $this->_controller . '&task=edit&cid[]=' . $recordId, false)
		);
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
		$ids = Request::getVar('cid', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$success = 0;

		foreach ($ids as $id)
		{
			// Load the record
			$model = Item::oneOrFail(intval($id));

			// Trigger before delete event
			Event::trigger('onMenuBeforeDeleteItem', array('com_menues.item', $model->getTableName()));

			// Attempt to delete the record
			if (!$model->destroy())
			{
				Notify::error($model->getError());
				continue;
			}

			// Trigger after delete event
			Event::trigger('onMenuAfterDeleteItem', array('com_menus.item', $model->getTableName()));

			$success++;
		}

		if ($success)
		{
			// Clean the cache.
			$this->cleanCache();

			// Set the success message
			Notify::success(Lang::txt('COM_MENUS_N_ITEMS_DELETED', $success));
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
		// Incoming
		$fields = Request::getArray('fields', array(), 'post');

		// Initiate extended database class
		if (isset($fields['id']) && $fields['id'])
		{
			$row = Item::oneOrNew($fields['id']);
			if (!$row->isNew())
			{
				$row->checkin();
			}
		}

		// Clear the ancillary data from the session.
		User::setState('com_menus.edit.item.data', null);
		User::setState('com_menus.edit.item.type', null);
		User::setState('com_menus.edit.item.link', null);

		$menutype = Request::getString('menutype');

		$append   = array();
		$append[] = ($menutype ? '&menutype=' . $menutype : '');

		$append = implode('', $append);

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . ($this->_controller ? '&controller=' . $this->_controller : '') . $append, false)
		);
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @return  void
	 */
	public function batchTask()
	{
		$vars = Request::getArray('batch', array());
		$pks  = Request::getArray('cid', array());

		// Build an array of item contexts to check
		$contexts = array();
		foreach ($pks as $id)
		{
			$contexts[$id] = $this->_option . '.item.' . $id;
		}

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
			Notify::warning(Lang::txt('COM_MENUS_NO_ITEM_SELECTED'));
		}
		else
		{
			$model = Item::blank();

			// Attempt to run the batch operation.
			if ($model->batch($vars, $pks, $contexts))
			{
				// Clear the cache
				$this->cleanCache();

				Notify::success(Lang::txt('JLIB_APPLICATION_SUCCESS_BATCH'));
			}
			else
			{
				Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()));
			}
		}

		$this->cancelTask();
	}

	/**
	 * Clean cached data
	 *
	 * @return  void
	 */
	public function cleanCache()
	{
		\Cache::clean($this->_option);
	}
}
