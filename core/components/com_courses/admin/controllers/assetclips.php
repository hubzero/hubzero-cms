<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin\Controllers;

use Components\Courses\Tables;
use Hubzero\Component\AdminController;
use Exception;
use Request;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.clip.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assetclip.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'asset.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assetgroup.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'unit.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'offering.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php';

/**
 * Courses controller class for managing asset clipboard (clips)
 */
class Assetclips extends AdminController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		parent::execute();
	}

	/**
	 * Displays a list of clipboard entries (clips)
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'scope' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scope',
				'scope',
				'asset_group'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			// Filters for returning results
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			)),
			'sort_Dir' => trim(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			))
		);

		// Instantiate the model
		$model = new Tables\Assetclip($this->database);

		// Get a record count
		$this->view->total = $model->count($this->view->filters);

		// Get records
		$this->view->rows = $model->find($this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Displays an edit form
	 *
	 * @return  void
	 */
	public function editTask($model=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($model))
		{
			// Incoming
			$id = Request::getArray('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : '';
			}

			$model = new \Components\Courses\Models\Assetclip($id);
		}

		$this->view->row = $model;

		$scope = $this->view->row->get('scope');
		if (!$scope)
		{
			$scope = Request::getString('scope', 'asset_group');
		}
		$this->view->row->set('scope', $scope);

		$scope_id = $this->view->row->get('scope_id');
		if (!$scope_id)
		{
			$scope_id = Request::getInt('scope_id', 0);
		}
		$this->view->row->set('scope_id', $scope_id);

		switch($scope)
		{
			default:
			case 'asset_group':
				$assetgroup = \Components\Courses\Models\Assetgroup::getInstance($scope_id);
				$this->view->assetgroup = $assetgroup;
				$unit_id = $assetgroup->get('unit_id');
				$unit = \Components\Courses\Models\Unit::getInstance($unit_id);
				$this->view->unit = $unit;
				$offering_id = $unit->get('offering_id');
				$offering = \Components\Courses\Models\Offering::getInstance($offering_id);
				$this->view->offering = $offering;
				$course_id = $offering->get('course_id');
				$course = \Components\Courses\Models\Course::getInstance($course_id);
				break;
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			\Notify::error($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Saves changes to a course or saves a new entry if creating
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');

		// Instantiate an asset clip object
		$model = new \Components\Courses\Models\Assetclip($fields['id']);

		if (!$model->bind($fields))
		{
			$this->setError($model->getError());
			$this->editTask($model);
			return;
		}

		$p = new \Hubzero\Config\Registry(Request::getArray('params', array(), 'post'));

		$model->set('params', $p->toString());

		if (!$model->store(true))
		{
			$this->setError($model->getError());
			$this->editTask($model);
			return;
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_COURSES_ITEM_SAVED')
		);
	}

	/**
	 * Removes a clip from asset clipboard
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$num = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			foreach ($ids as $id)
			{
				// Load the asset clip 
				$clip = new \Components\Courses\Models\Assetclip($id);

				// Ensure we found the course info
				if (!$clip->exists())
				{
					continue;
				}

				// Delete asset clip
				if (!$clip->delete())
				{
					throw new Exception(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'), 500);
				}

				$num++;
			}
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_COURSES_ITEMS_REMOVED', $num)
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
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}
