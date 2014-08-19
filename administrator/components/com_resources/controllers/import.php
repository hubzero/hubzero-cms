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

/**
 * Resource importer
 */
class ResourcesControllerImport extends \Hubzero\Component\AdminController
{
	/**
	 * Display imports
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// set layout
		$this->view->setLayout('display');

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// get all imports from archive
		$importArchive = \Resources\Model\Import\Archive::getInstance();
		$imports = $importArchive->imports('list', array(
			'state'   => array(1),
			'orderby' => 'created_at DESC'
		));

		// pass vars to view
		$this->view->imports = $imports;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add an Import
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an Import
	 *
	 * @return     void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);

		// get request vars
		$ids = JRequest::getVar('id', array());
		$id  = (isset($ids[0])) ? $ids[0] : null;

		// get the import object
		$this->view->import = new Resources\Model\Import( $id );

		// import params
		$this->view->params = new JParameter($this->view->import->get('params'));

		// get all files in import filespace
		$this->view->files = JFolder::files($this->view->import->fileSpacePath(), '.');

		// get all imports from archive
		$hooksArchive = \Resources\Model\Import\Hook\Archive::getInstance();
		$this->view->hooks = $hooksArchive->hooks('list', array(
			'state' => array(1)
		));

		// Get groups
		$this->view->groups = Hubzero\User\Group::find(array(
			'authorized' => 'admin',
			'fields'     => array('cn','description','published','gidNumber','type'),
			'type'       => array(1,3),
			'sortby'     => 'description'
		));

		// Set any errors
		if ($this->getErrors())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Save an Import
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// check token
		JSession::checkToken() or die( 'Invalid Token' );

		// get request vars
		$import = JRequest::getVar('import', array());
		$hooks  = JRequest::getVar('hooks', array());
		$params = JRequest::getVar('params', array());
		$file   = JRequest::getVar('file', array(), 'FILES');

		// create import model object
		$this->import = new Resources\Model\Import();

		// set our hooks
		$this->import->set('hooks', json_encode($hooks));

		// load current params
		$iparams = new JParameter($this->import->get('params'));

		// bind incoming params
		$iparams->bind($params);

		// set params on import object
		$this->import->set('params', $iparams->toString());

		// bind input to model
		if (!$this->import->bind( $import ))
		{
			$this->setError($this->import->getError());
			return $this->editTask();
		}

		// is this a new import
		$isNew = false;
		if (!$this->import->get('id'))
		{
			$isNew = true;

			// set the created by/at
			$this->import->set('created_by', JFactory::getUser()->get('id'));
			$this->import->set('created_at', JFactory::getDate()->toSql());
		}

		// do we have a data file
		if ($this->import->get('file'))
		{
			// get record count
			$importImporter = new \Resources\Import\Importer();
			$count = $importImporter->count($this->import);
			$this->import->set('count', $count);
		}

		// attempt to save
		if (!$this->import->store(true))
		{
			$this->setError($this->import->getError());
			return $this->editTask();
		}

		// is this a new import
		if ($isNew)
		{
			// create folder for files
			$this->_createImportFilespace($this->import);
		}

		// if we have a file
		if (is_array($file) && $file['size'] > 0 && $file['error'] == 0)
		{
			move_uploaded_file($file['tmp_name'], $this->import->fileSpacePath() . DS . $file['name']);
			$this->import->set('file', $file['name']);
		}

		// do we have a data file
		if ($this->import->get('file'))
		{
			// get record count
			$importImporter = new \Resources\Import\Importer();
			$count = $importImporter->count($this->import);
			$this->import->set('count', $count);
		}

		// save again with import count
		if (!$this->import->store(true))
		{
			$this->setError($this->import->getError());
			return $this->editTask();
		}

		//inform user & redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display',
			JText::_('COM_RESOURCES_IMPORT_CREATED'),
			'passed'
		);
	}

	/**
	 * Delete Import
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// check token
		JSession::checkToken() or die( 'Invalid Token' );

		// get request vars
		$ids = JRequest::getVar('id', array());

		// loop through all ids posted
		foreach ($ids as $id)
		{
			// make sure we have an object
			if (!$resourceImport = new \Resources\Model\Import($id))
			{
				continue;
			}

			// attempt to delete import
			if (!$resourceImport->delete())
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display',
					$resourceImport->getError(),
					'error'
				);
				return;
			}
		}

		//inform user & redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display',
			JText::_('COM_RESOURCES_IMPORT_REMOVED'),
			'passed'
		);
	}

	/**
	 * Run Import as Dry Run
	 *
	 * @return     void
	 */
	public function runTestTask()
	{
		$this->runTask(1);
	}

	/**
	 * Run Import
	 *
	 * @param   integer $dryRun
	 * @return  void
	 */
	public function runTask($dryRun = 0)
	{
		// get request vars
		$ids = JRequest::getVar('id', array());
		$id  = (isset($ids[0])) ? $ids[0] : null;

		// are we test mode
		$this->view->dryRun = $dryRun;

		// create import model object
		$this->view->import = new \Resources\Model\Import($id);

		//get jquery plugin & parse params
		$jqueryPlugin = JPluginHelper::getPlugin('system', 'jquery');
		$jqueryPluginParams = new JParameter( $jqueryPlugin->params );

		//add jquery if we dont have the jquery plugin enabled or not active on admin
		if (!JPluginHelper::isEnabled('system', 'jquery') || !$jqueryPluginParams->get('activateAdmin'))
		{
			JError::raiseError('500', JText::_('COM_RESOURCES_IMPORT_WARNING_ADMIN_REQUIREMENTS'));
		}

		// Set any errors
		if ($this->getErrors())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// force layout
		$this->view->setLayout('run');

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Actually Run Import
	 * 
	 * @return  string JSON encoded records that just got inserted or would be
	 */
	public function doRunTask()
	{
		// check token
		JSession::checkToken() or die( 'Invalid Token' );

		// start of import
		$start = microtime(true);

		// get request vars
		$id = JRequest::getInt('id', 0);

		// test mode
		$dryRun = JRequest::getBool('dryrun', 0);

		// create import model object
		$import = new Resources\Model\Import($id);

		// make import importer
		$importImporter = Resources\Import\Importer::getInstance();

		// run process task on importer
		// passed the import model, array or callbacks, and test mode flag
		$resourceData = $importImporter->process($import, array(
			'postparse'   => $this->_hooks('postparse', $import),
			'postmap'     => $this->_hooks('postmap', $import),
			'postconvert' => $this->_hooks('postconvert', $import)
		), $dryRun);

		// calculate execution time
		$end  = microtime(true);
		$time = round($end - $start, 3);

		// outputted with html entities to allow browser json formatter
		if (JRequest::getInt('format', 0) == 1)
		{
			echo htmlentities(json_encode(array('import'=>'success', 'time' => $time, 'records' => $resourceData)));
			exit();
		}

		// return results to user
		echo json_encode(array('import'=>'success', 'time' => $time, 'records' => $resourceData));
		exit();
	}

	/**
	 * Get progress of import task
	 * 
	 * @return  string JSON encoded total and position
	 */
	public function progressTask()
	{
		// get request vars
		$id = JRequest::getInt('id', 0);

		// create import model object
		$import = new \Resources\Model\Import($id);

		// get the lastest run
		$run = $import->runs('current');

		// build array of data to return
		$data = array(
			'processed' => $run->get('processed'),
			'total'     => $run->get('count')
		);

		// return progress update
		echo json_encode($data);
		exit();
	}

	/**
	 * Return Hook for Post Parsing or Post Convert
	 *
	 * @param     $type      Hook we want
	 * @param     $import    Resource Import Model
	 * @return    Closure
	 */
	private function _hooks($type, $import)
	{
		// array to hold callbacks
		$callbacks = array();

		// get hooks on import
		$hooks = json_decode($import->get('hooks'));

		// make sure we have this type of hook
		if (!isset($hooks->$type))
		{
			return $callbacks;
		}

		// loop through each hook
		foreach ($hooks->$type as $hook)
		{
			// load hook object
			$importHook = new Resources\Model\Import\Hook($hook);

			// make sure we have an object
			if (!$importHook)
			{
				continue;
			}

			// build path to script
			$hookFile = $importHook->fileSpacePath() . DS . $importHook->get('file');

			// make sure we have a file
			if (!is_file($hookFile))
			{
				continue;
			}

			// add callback
			$callbacks[] = function($data, $dryRun) use ($hookFile) {
				return include $hookFile;
			};
		}

		// return closures as callbacks
		return $callbacks;
	}

	/**
	 * Method to create import filespace if needed
	 *
	 * @param   object  $import Resources\Model\Import
	 * @return  boolean
	 */
	private function _createImportFilespace(Resources\Model\Import $import)
	{
		// upload path
		$uploadPath = $import->fileSpacePath();

		// if we dont have a filespace, create it
		if (!is_dir($uploadPath))
		{
			JFolder::create($uploadPath, 0775);
		}

		// all set
		return true;
	}
}
