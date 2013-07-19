<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');

/**
 * Courses controller class for managing course pages
 */
class CoursesControllerAssets extends Hubzero_Controller
{
	/**
	 * Manage course pages
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['tmpl']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.tmpl',
			'tmpl',
			''
		);
		$this->view->filters['asset_scope']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.scope',
			'scope',
			'asset_group'
		);
		$this->view->filters['asset_scope_id']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.scope_id',
			'scope_id',
			0,
			'int'
		);
		$this->view->filters['course_id']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.course_id',
			'course_id',
			0,
			'int'
		);

		// Filters for returning results
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$tbl = new CoursesTableAsset($this->database);

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

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Link an asset to an object
	 *
	 * @return void
	 */
	public function linkTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$asset_id  = JRequest::getInt('asset', 0);
		$tmpl      = JRequest::getVar('tmpl', '');
		$scope     = JRequest::getVar('scope', 'asset_group');
		$scope_id  = JRequest::getInt('scope_id', 0);
		$course_id = JRequest::getInt('course_id', 0);

		// Get the element moving down - item 1
		$tbl = new CoursesTableAssetAssociation($this->database);
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

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id,
			($this->getError() ? $this->getError() : null),
			($this->getError() ? 'error' : 'message')
		);
	}

	/**
	 * Unlink an asset from an object
	 *
	 * @return void
	 */
	public function unlinkTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$asset_id  = JRequest::getInt('asset', 0);
		$tmpl      = JRequest::getVar('tmpl', '');
		$scope     = JRequest::getVar('scope', 'asset_group');
		$scope_id  = JRequest::getInt('scope_id', 0);
		$course_id = JRequest::getInt('course_id', 0);

		// Load association
		$tbl = new CoursesTableAssetAssociation($this->database);
		$tbl->loadByAssetScope($asset_id, $scope_id, $scope);

		// Remove association
		if (!$tbl->delete())
		{
			$this->setError($tbl->getError());
		}

		$model = new CoursesModelAsset($asset_id);
		// Is this asset linked anywhere else?
		if ($model->parents(array('count' => true)) <= 0)
		{
			// No -- Asset no longer used. Delete it.
			if (!$model->delete())
			{
				$this->setError($model->delete());
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id,
			($this->getError() ? $this->getError() : null),
			($this->getError() ? 'error' : 'message')
		);
	}

	/**
	 * Create a course page
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a course page
	 *
	 * @return void
	 */
	public function editTask($model = null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if (is_object($model))
		{
			$this->view->row = $model;
		}
		else
		{
			// Incoming
			$ids = JRequest::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($ids))
			{
				$id = (!empty($ids)) ? $ids[0] : 0;
			}
			else
			{
				$id = 0;
			}

			$this->view->row = new CoursesTableAsset($this->database);
			$this->view->row->load($id);
		}

		/*if (!$this->view->row->get('offering_id'))
		{
			$this->view->row->set('offering_id', JRequest::getInt('offering', 0));
		}

		$this->view->offering = CoursesModelOffering::getInstance($this->view->row->get('offering_id'));*/
		$this->view->tmpl      = JRequest::getVar('tmpl', '');
		$this->view->scope     = JRequest::getVar('scope', 'asset_group');
		$this->view->scope_id  = JRequest::getInt('scope_id', 0);
		$this->view->course_id = JRequest::getInt('course_id', 0);

		$this->view->config = $this->config;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a course page
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// load the request vars
		$fields = JRequest::getVar('fields', array(), 'post');
		$tmpl   = JRequest::getVar('tmpl', '');

		// instatiate course page object for saving
		$row = new CoursesTableAsset($this->database);

		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$fields['asset_id'] = $row->get('id');

		$row2 = new CoursesTableAssetAssociation($this->database);
		$row2->loadByAssetScope($fields['asset_id'], $fields['scope_id'], $fields['scope']);
		if (!$row2->id)
		{
			if (!$row2->bind($fields))
			{
				$this->addComponentMessage($row2->getError(), 'error');
				$this->editTask($row);
				return;
			}

			if (!$row2->check())
			{
				$this->addComponentMessage($row2->getError(), 'error');
				$this->editTask($row);
				return;
			}

			if (!$row2->store())
			{
				$this->addComponentMessage($row2->getError(), 'error');
				$this->editTask($row);
				return;
			}
		}

		// Rename the temporary upload directory if it exist
		$lid = $fields['lid'];
		if ($lid != $row->get('id')) 
		{
			$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $fields['course_id'];
			if (is_dir($path . DS . $lid)) 
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::move($path . DS . $lid, $path . DS . $row->get('id'))) 
				{
					$this->setError(JFolder::move($path . DS . $lid, $path . DS . $row->get('id')));
				}
			}
		}

		// Incoming file
		/*$file = JRequest::getVar('upload', '', 'files', 'array');
		if ($file['name'])
		{
			$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . $fields['course_id'] . DS . $row->id;
			// Make sure the upload path exist
			if (!is_dir($path))
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::create($path, 0777))
				{
					$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH').' '.$path);
					$this->editTask($row);
					return;
				}
			}

			// Make the filename safe
			jimport('joomla.filesystem.file');
			$file['name'] = JFile::makeSafe($file['name']);
			// Ensure file names fit.
			$ext = JFile::getExt($file['name']);
			$file['name'] = str_replace(' ', '_', $file['name']);
			if (strlen($file['name']) > 230)
			{
				$file['name'] = substr($file['name'], 0, 230);
				$file['name'] .= '.' . $ext;
			}

			// Perform the upload
			if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
			{
				$this->setError(JText::_('ERROR_UPLOADING'));
			}
			else
			{
				if (strtolower($ext) == 'zip')
				{
					require_once(JPATH_ROOT . DS . 'administrator' . DS . 'includes' . DS . 'pcl' . DS . 'pclzip.lib.php');

					if (!extension_loaded('zlib'))
					{
						$this->setError(JText::_('ZLIB_PACKAGE_REQUIRED'));
					}
					else
					{
						$zip = new PclZip($path . DS . $file['name']);

						// unzip the file
						if (!($do = $zip->extract($path)))
						{
							$this->setError(JText::_('UNABLE_TO_EXTRACT_PACKAGE'));
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
				echo '<p class="message">' . JText::_('Entry successfully saved') . '</p>';
			}
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $fields['scope'] . '&scope_id=' . $fields['scope_id'] . '&course_id=' . $fields['course_id']
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function orderdownTask()
	{
		$this->reorderTask(1);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function orderupTask()
	{
		$this->reorderTask(-1);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function reorderTask($move=1)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array());
		$id = $id[0];

		$tmpl      = JRequest::getVar('tmpl', '');
		$scope     = JRequest::getVar('scope', 'asset_group');
		$scope_id  = JRequest::getInt('scope_id', 0);
		$course_id = JRequest::getInt('course_id', 0);

		// Get the element moving down - item 1
		$tbl = new CoursesTableAssetAssociation($this->database);
		$tbl->loadByAssetScope($id, $scope_id, $scope);

		if (!$tbl->move($move, "scope=" . $this->database->Quote($scope) . " AND scope_id=" . $this->database->Quote(intval($scope_id))))
		{
			echo $tbl->getError();
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id
		);
	}

	/**
	 * Cancel a course page task
	 *
	 * @return void
	 */
	public function cancelTask()
	{
		$tmpl      = JRequest::getVar('tmpl', '');
		$scope     = JRequest::getVar('scope', 'asset_group');
		$scope_id  = JRequest::getInt('scope_id', 0);
		$course_id = JRequest::getInt('course_id', 0);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=' . $tmpl . '&scope=' . $scope . '&scope_id=' . $scope_id . '&course_id=' . $course_id
		);
	}
}
