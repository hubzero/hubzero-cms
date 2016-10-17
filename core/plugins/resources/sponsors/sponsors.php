<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Display sponsors on a resource page
 */
class plgResourcesSponsors extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object  $resource  Current resource
	 * @return  array
	 */
	public function &onResourcesSubAreas($resource)
	{
		$areas = array(
			'sponsors' => Lang::txt('PLG_RESOURCES_SPONSORS')
		);
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param   object   $resource  Current resource
	 * @param   string   $option    Name of the component
	 * @param   integer  $miniview  View style
	 * @return  array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		require_once(__DIR__ . DS . 'models' . DS . 'sponsor.php');

		$records = \Plugins\Resources\Sponsors\Models\Sponsor::all()
			->whereEquals('state', 1)
			->rows();

		if (!$records)
		{
			return $arr;
		}

		$data = '';
		$sponsors = array();
		foreach ($records As $record)
		{
			$sponsors[$record->alias] = $record;
		}

		$rt = new \Components\Resources\Helpers\Tags($resource->id);
		$tags = $rt->tags();

		if ($tags)
		{
			foreach ($tags as $tag)
			{
				if (isset($sponsors[$tag->get('tag')]))
				{
					$data = $sponsors[$tag->get('tag')]->description;
					break;
				}
			}
		}

		// Instantiate a view
		$view = $this->view('mini', 'display')
			->set('option', $option)
			->set('resource', $resource)
			->set('params', $this->params)
			->set('data', $data)
			->setErrors($this->getErrors());

		if ($miniview)
		{
			$view->setLayout('mini');
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}

	/**
	 * Return plugin name if this plugin has an admin interface
	 *
	 * @return  string
	 */
	public function onCanManage()
	{
		return $this->_name;
	}

	/**
	 * Determine task and execute it
	 *
	 * @param   string  $option      Component name
	 * @param   string  $controller  Controller name
	 * @param   string  $task        Task to perform
	 * @return  void
	 */
	public function onManage($option, $controller='plugins', $task='default')
	{
		if (Request::getCmd('plugin') != $this->_name)
		{
			return;
		}

		$task = ($task) ?  $task : 'default';

		require_once(__DIR__ . DS . 'models' . DS . 'sponsor.php');

		$this->_option     = $option;
		$this->_controller = $controller;
		$this->_task       = $task;
		$this->database    = App::get('db');

		$method = strtolower($task) . 'Task';

		return $this->$method();
	}

	/**
	 * Display a list of sponsors
	 *
	 * @return  void
	 */
	public function defaultTask()
	{
		// Incoming
		$filters = array(
			'limit'    => Request::getState(
				$this->_option . '.plugins.sponsors.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start'    => Request::getState(
				$this->_option . '.plugins.sponsors.limitstart',
				'limitstart',
				0,
				'int'
			),
			'sort'     => Request::getState(
				$this->_option . '.plugins.sponsors.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.plugins.sponsors.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$model = \Plugins\Resources\Sponsors\Models\Sponsor::all();

		$rows = $model
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Instantiate a view
		$view = $this->view('default', 'admin')
			->set('rows', $rows)
			->set('filters', $filters)
			->set('option', $this->_option)
			->set('controller', $this->_controller)
			->set('task', $this->_task);

		return $view
			->setErrors($this->getErrors())
			->loadTemplate();
	}

	/**
	 * Add a new type
	 *
	 * @return  void
	 */
	public function addTask()
	{
		return $this->editTask();
	}

	/**
	 * Edit a type
	 *
	 * @param   object  $row
	 * @return  string
	 */
	public function editTask($row=null)
	{
		if (!is_object($row))
		{
			// Incoming (expecting an array)
			$id = Request::getInt('id', 0);

			// Load the object
			$row = \Plugins\Resources\Sponsors\Models\Sponsor::oneOrNew($id);
		}

		$view = $this->view('edit', 'admin')
			->set('row', $row)
			->set('option', $this->_option)
			->set('controller', $this->_controller)
			->set('task', $this->_task);

		// Output the HTML
		return $view
			->setErrors($this->getErrors())
			->loadTemplate();
	}

	/**
	 * Save a type
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Initiate extended database class
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		$row = \Plugins\Resources\Sponsors\Models\Sponsor::oneOrNew($fields['id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

		$t = \Components\Tags\Models\Tag::oneByTag($row->get('alias'));
		if ($t->isNew())
		{
			// Add new tag!
			$t->set('tag', $row->get('alias'));
			$t->set('raw_tag', addslashes($row->get('title')));

			if (!$t->save())
			{
				$this->setError($t->getError());
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors', false),
			Lang::txt('PLG_RESOURCES_SPONSORS_ITEM_SAVED')
		);
	}

	/**
	 * Remove one or more types
	 *
	 * @return  void  Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());

		// Ensure we have an ID to work with
		if (empty($ids))
		{
			// Redirect with error message
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors', false),
				Lang::txt('PLG_RESOURCES_SPONSORS_NO_ITEM_SELECTED'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Delete the type
			$row = \Plugins\Resources\Sponsors\Models\Sponsor::oneOrFail((int)$id);

			if (!$row->destroy())
			{
				Notify::error($row->getError());
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors', false),
			Lang::txt('PLG_RESOURCES_SPONSORS_ITEM_REMOVED')
		);
	}

	/**
	 * Calls stateTask to publish entries
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return  void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param   integer  The state to set entries to
	 * @return  void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$ids = Request::getVar('id', array());

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($state == 1) ? Lang::txt('PLG_RESOURCES_SPONSORS_UNPUBLISH') : Lang::txt('PLG_RESOURCES_SPONSORS_PUBLISH');

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors', false),
				Lang::txt('PLG_RESOURCES_SPONSORS_SELECT_ITEM_TO', $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$row = \Plugins\Resources\Sponsors\Models\Sponsor::oneOrFail((int)$id);
			$row->set('state', $state);

			if (!$row->save())
			{
				$this->setError($row->getError());
				return $this->defaultTask();
			}
		}

		// set message
		if ($state == 1)
		{
			$message = Lang::txt('PLG_RESOURCES_SPONSORS_ITEMS_PUBLISHED', count($ids));
		}
		else
		{
			$message = Lang::txt('PLG_RESOURCES_SPONSORS_ITEMS_UNPUBLISHED', count($ids));
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors', false),
			$message
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors', false)
		);
	}
}
