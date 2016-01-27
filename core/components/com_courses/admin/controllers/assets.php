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

namespace Components\Courses\Admin\Controllers;

use Components\Courses\Tables;
use Hubzero\Component\AdminController;
use Exception;
use Filesystem;
use Request;
use Route;
use Lang;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'asset.php');

/**
 * Courses controller class for managing course pages
 */
class Assets extends AdminController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		parent::execute();
	}

	/**
	 * Manage course pages
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'tmpl' => Request::getState(
				$this->_option . '.' . $this->_controller . '.tmpl',
				'tmpl',
				''
			),
			'asset_scope' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scope',
				'scope',
				'asset_group'
			),
			'asset_scope_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scope_id',
				'scope_id',
				0,
				'int'
			),
			'course_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.course_id',
				'course_id',
				0,
				'int'
			),
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
			)
		);

		$tbl = new Tables\Asset($this->database);

		$rows = $tbl->find(array(
			'w' => $this->view->filters
		));
		$r = array();
		if ($rows)
		{
			foreach ($rows as $row)
			{
				$r[$row->id] = $row;
			}
		}
		$this->view->rows = $r;

		$assets = $tbl->find(array(
			'w' => array('course_id' => $this->view->filters['course_id'])
		));
		$a = array();
		if ($assets)
		{
			foreach ($assets as $row)
			{
				$a[$row->id] = $row;
			}
		}
		$this->view->assets = $a;

		$this->view->total = count($this->view->rows);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Link an asset to an object
	 *
	 * @return  void
	 */
	public function linkTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$asset_id  = Request::getInt('asset', 0);
		$tmpl      = Request::getVar('tmpl', '');
		$scope     = Request::getVar('scope', 'asset_group');
		$scope_id  = Request::getInt('scope_id', 0);
		$course_id = Request::getInt('course_id', 0);

		// Get the element moving down - item 1
		$tbl = new Tables\AssetAssociation($this->database);
		$tbl->scope    = $scope;
		$tbl->scope_id = $scope_id;
		$tbl->asset_id = $asset_id;
		if (!$tbl->check())
		{
			$this->setError($tbl->getError());
		}
		if (!$tbl->store())
		{
			$this->setError($tbl->getError());
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id, false),
			($this->getError() ? $this->getError() : null),
			($this->getError() ? 'error' : 'message')
		);
	}

	/**
	 * Unlink an asset from an object
	 *
	 * @return  void
	 */
	public function unlinkTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$asset_id  = Request::getInt('asset', 0);
		$tmpl      = Request::getVar('tmpl', '');
		$scope     = Request::getVar('scope', 'asset_group');
		$scope_id  = Request::getInt('scope_id', 0);
		$course_id = Request::getInt('course_id', 0);

		// Load association
		$tbl = new Tables\AssetAssociation($this->database);
		$tbl->loadByAssetScope($asset_id, $scope_id, $scope);

		// Remove association
		if (!$tbl->delete())
		{
			$this->setError($tbl->getError());
		}

		$model = new \Components\Courses\Models\Asset($asset_id);
		// Is this asset linked anywhere else?
		if ($model->parents(array('count' => true)) <= 0)
		{
			// No -- Asset no longer used. Delete it.
			if (!$model->delete())
			{
				$this->setError($model->delete());
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id, false),
			($this->getError() ? $this->getError() : null),
			($this->getError() ? 'error' : 'message')
		);
	}

	/**
	 * Edit a course page
	 *
	 * @return void
	 */
	public function editTask($model = null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($model))
		{
			// Incoming
			$ids = Request::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($ids))
			{
				$id = (!empty($ids)) ? $ids[0] : 0;
			}
			else
			{
				$id = ($ids ? $ids : 0);
			}

			$model = new Tables\Asset($this->database);
			$model->load($id);
		}

		$this->view->row = $model;

		$this->view->tmpl      = Request::getVar('tmpl', '');
		$this->view->scope     = Request::getVar('scope', 'asset_group');
		$this->view->scope_id  = Request::getInt('scope_id', 0);
		$this->view->course_id = Request::getInt('course_id', 0);
		$this->view->config    = $this->config;

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
	 * Save a course page
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// load the request vars
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);
		$tmpl   = Request::getVar('tmpl', '');

		// instatiate course page object for saving
		$row = new Tables\Asset($this->database);

		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->editTask($row);
			return;
		}

		$fields['asset_id'] = $row->get('id');

		$row2 = new Tables\AssetAssociation($this->database);
		$row2->loadByAssetScope($fields['asset_id'], $fields['scope_id'], $fields['scope']);
		if (!$row2->id)
		{
			if (!$row2->bind($fields))
			{
				$this->setError($row2->getError());
				$this->editTask($row);
				return;
			}

			if (!$row2->check())
			{
				$this->setError($row2->getError());
				$this->editTask($row);
				return;
			}

			if (!$row2->store())
			{
				$this->setError($row2->getError());
				$this->editTask($row);
				return;
			}
		}

		// Rename the temporary upload directory if it exist
		$lid = $fields['lid'];
		if ($lid != $row->get('id'))
		{
			$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $fields['course_id'];
			if (is_dir($path . DS . $lid))
			{
				if (!Filesystem::move($path . DS . $lid, $path . DS . $row->get('id')))
				{
					$this->setError(Lang::txt('UNABLE_TO_MOVE_PATH'));
				}
			}
		}

		// Incoming file
		/*$file = Request::getVar('upload', '', 'files', 'array');
		if ($file['name'])
		{
			$path = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $fields['course_id'] . DS . $row->id;
			// Make sure the upload path exist
			if (!is_dir($path))
			{
				if (!\Filesystem::makeDirectory($path))
				{
					$this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH').' '.$path);
					$this->editTask($row);
					return;
				}
			}

			// Make the filename safe
			$file['name'] = Filesystem::clean($file['name']);
			// Ensure file names fit.
			$ext = Filesystem::extension($file['name']);
			$file['name'] = str_replace(' ', '_', $file['name']);
			if (strlen($file['name']) > 230)
			{
				$file['name'] = substr($file['name'], 0, 230);
				$file['name'] .= '.' . $ext;
			}

			// Perform the upload
			if (!\Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
			{
				$this->setError(Lang::txt('ERROR_UPLOADING'));
			}
			else
			{
				if (strtolower($ext) == 'zip')
				{
					require_once(PATH_CORE . DS . 'includes' . DS . 'pcl' . DS . 'pclzip.lib.php');

					if (!extension_loaded('zlib'))
					{
						$this->setError(Lang::txt('ZLIB_PACKAGE_REQUIRED'));
					}
					else
					{
						$zip = new PclZip($path . DS . $file['name']);

						// unzip the file
						if (!($do = $zip->extract($path)))
						{
							$this->setError(Lang::txt('UNABLE_TO_EXTRACT_PACKAGE'));
						}
						else
						{
							@unlink($path . DS . $file['name']);
							$file['name'] = 'presentation.json';
						}
					}
				}

				// Set the url
				$row->set('url', $file['name']);
				$row->store();
			}
		}*/

		if ($tmpl == 'component')
		{
			if ($this->getError())
			{
				echo '<p class="error">' . $this->getError() . '</p>';
			}
			else
			{
				echo '<p class="message">' . Lang::txt('COM_COURSES_ITEM_SAVED') . '</p>';
			}
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $fields['scope'] . '&scope_id=' . $fields['scope_id'] . '&course_id=' . $fields['course_id'], false)
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$move = $this->_task == 'orderup' ? -1 : 1;

		// Incoming
		$id = Request::getVar('id', array());
		$id = $id[0];

		$tmpl      = Request::getVar('tmpl', '');
		$scope     = Request::getVar('scope', 'asset_group');
		$scope_id  = Request::getInt('scope_id', 0);
		$course_id = Request::getInt('course_id', 0);

		// Get the element moving down - item 1
		$tbl = new Tables\AssetAssociation($this->database);
		$tbl->loadByAssetScope($id, $scope_id, $scope);

		if (!$tbl->move($move, "scope=" . $this->database->Quote($scope) . " AND scope_id=" . $this->database->Quote(intval($scope_id))))
		{
			echo $tbl->getError();
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id, false)
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$tmpl      = Request::getVar('tmpl', '');
		$scope     = Request::getVar('scope', 'asset_group');
		$scope_id  = Request::getInt('scope_id', 0);
		$course_id = Request::getInt('course_id', 0);

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id, false)
		);
	}
}
