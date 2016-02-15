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
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $resource Current resource
	 * @return     array
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
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      integer $miniview  View style
	 * @return     array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Get recommendations
		$this->database = App::get('db');

		// Instantiate a view
		$this->view = $this->view('mini', 'display');

		if ($miniview)
		{
			$this->view->setLayout('mini');
		}

		// Pass the view some info
		$this->view->option   = $option;
		$this->view->resource = $resource;
		$this->view->params   = $this->params;
		$this->view->data     = '';

		require_once(__DIR__ . DS . 'tables' . DS . 'sponsor.php');

		$this->sponsors = array();

		$model = new \Plugins\Resources\Sponsors\Tables\Sponsor($this->database);
		$records = $model->getRecords(array('state' => 1));
		if (!$records)
		{
			return $arr;
		}

		foreach ($records As $record)
		{
			$this->sponsors[$record->alias] = $record;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');
		$rt = new \Components\Resources\Helpers\Tags($resource->id);
		$tags = $rt->tags();

		if ($tags)
		{
			foreach ($tags as $tag)
			{
				if (isset($this->sponsors[$tag->get('tag')]))
				{
					$this->view->data = $this->sponsors[$tag->get('tag')]->description;
					break;
				}
			}
		}

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Return the output
		$arr['html'] = $this->view->loadTemplate();

		return $arr;
	}

	/**
	 * Return plugin name if this plugin has an admin interface
	 *
	 * @return	string
	 */
	public function onCanManage()
	{
		return $this->_name;
	}

	/**
	 * Determine task and execute it
	 *
	 * @param     string $option     Component name
	 * @param     string $controller Controller name
	 * @param     string $task       Task to perform
	 * @return    void
	 */
	public function onManage($option, $controller='plugins', $task='default')
	{
		$task = ($task) ?  $task : 'default';

		require_once(__DIR__ . DS . 'tables' . DS . 'sponsor.php');

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
	 * @return	void
	 */
	public function defaultTask()
	{
		// Instantiate a view
		$this->view = $this->view('default', 'admin');
		$this->view->option = $this->_option;
		$this->view->controller = $this->_controller;
		$this->view->task = $this->_task;

		// Incoming
		$this->view->filters = array(
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

		$model = new \Plugins\Resources\Sponsors\Tables\Sponsor($this->database);

		// Get a record count
		$this->view->total = $model->getCount($this->view->filters);

		// Get records
		$this->view->rows = $model->getRecords($this->view->filters);

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		return $this->view->loadTemplate();
	}

	/**
	 * Add a new type
	 *
	 * @return     void
	 */
	public function addTask()
	{
		return $this->editTask();
	}

	/**
	 * Edit a type
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		$this->view = $this->view('edit', 'admin');
		$this->view->option = $this->_option;
		$this->view->controller = $this->_controller;
		$this->view->task = $this->_task;

		if ($row)
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming (expecting an array)
			$id = Request::getInt('id', 0);

			// Load the object
			$this->view->row = new \Plugins\Resources\Sponsors\Tables\Sponsor($this->database);
			$this->view->row->load($id);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		return $this->view->loadTemplate();
	}

	/**
	 * Save a type
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Initiate extended database class
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields = array_map('trim', $fields);

		$row = new \Plugins\Resources\Sponsors\Tables\Sponsor($this->database);
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			return $this->editTask($row);
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

		$t = \Components\Tags\Models\Tag::oneByTag($row->alias);
		if ($t->isNew())
		{
			// Add new tag!
			$t->set('tag', $row->alias);
			$t->set('raw_tag', addslashes($row->title));
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
	 * @return     void Redirects back to main listing
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

		$rt = new \Plugins\Resources\Sponsors\Tables\Sponsor($this->database);

		foreach ($ids as $id)
		{
			// Delete the type
			$rt->delete($id);
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
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 *
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @param      integer The state to set entries to
	 * @return     void
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
			$row = new \Plugins\Resources\Sponsors\Tables\Sponsor($this->database);
			$row->load(intval($id));
			$row->state = $state;
			if (!$row->store())
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
	 * @return	void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=manage&plugin=sponsors', false)
		);
	}
}

