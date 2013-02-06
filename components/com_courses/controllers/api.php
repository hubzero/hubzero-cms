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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('Hubzero.Api.Controller');

/**
 * API controller for the time component
 */
class CoursesApiController extends Hubzero_Api_Controller
{
	/**
	 * Execute!
	 * 
	 * @return void
	 */
	function execute()
	{
		// Import some Joomla libraries
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		// Get the request type
		$this->format = JRequest::getVar('format', 'application/json');

		// Get a database object
		$this->db = JFactory::getDBO();

		// Switch based on task (i.e. "/api/courses/xxxxx")
		switch($this->segments[0])
		{
			// Units
			case 'unitsave':               $this->unitSave();                 break;

			// Asset groups
			case 'assetgroupsave':         $this->assetGroupSave();           break;
			case 'assetgroupreorder':      $this->assetGroupReorder();        break;

			// Assets
			case 'assethandlers':          $this->assetHandlers();            break;
			case 'assetnew':               $this->assetNew();                 break;
			case 'assetsave':              $this->assetSave();                break;
			case 'assetdelete':            $this->assetDelete();              break;
			case 'assetsreorder':          $this->assetsReorder();            break;
			case 'assettogglepublished':   $this->assetTogglePublished();     break;

			default:                       $this->method_not_found();         break;
		}
	}

	//--------------------------
	// Units functions
	//--------------------------

	/**
	 * Save a course unit
	 * 
	 * @return 201 created on success
	 */
	private function unitSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Import needed courses JTable libraries
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'unit.php');

		$unitObj = new CoursesTableUnit($this->db);

		if($id = JRequest::getInt('id', false))
		{
			if (!$unitObj->load($id))
			{
				$this->setMessage("Loading unit $id failed", 500, 'Internal server error');
				return;
			}
		}

		// We'll always save the title again, even if it's just to the same thing
		$title      = (!empty($unitObj->title)) ? $unitObj->title : 'New Unit';
		$row->title = JRequest::getString('title', $title);
		$row->title = preg_replace("/[^a-zA-Z0-9 \-\:\.]/", "", $row->title);
		$row->alias = strtolower(str_replace(' ', '', $row->title));

		// If we have dates coming in, save those
		if($start_date = JRequest::getCmd('start_date', false))
		{
			$row->start_date = $start_date;
		}
		if($end_date = JRequest::getCmd('end_date', false))
		{
			$row->end_date = $end_date;
		}

		// When creating a new unit
		if(!$id)
		{
			$row->offering_id = JRequest::getInt('offering_id', 0);
			$row->created     = date('Y-m-d H:i:s');
			$row->created_by  = JFactory::getApplication()->getAuthn('user_id');
		}

		// Save the unit
		if (!$unitObj->save($row))
		{
			$this->setMessage("Saving unit $id failed", 500, 'Internal server error');
			return;
		}

		// Create a placeholder for our return object
		$assetGroups = array();

		// If this is a new unit, give it some default asset groups
		// Create a top level asset group for each of lectures, homework, and exam
		if(!$id)
		{
			// Get our asset group object
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');

			foreach (array('Lectures', 'Homework', 'Exam') as $key)
			{
				$assetGroupObj = new CoursesTableAssetGroup($this->db);

				$row = new stdclass();

				$row->id          = null;
				$row->title       = $key;
				$row->alias       = strtolower(str_replace(' ', '', $row->title));
				$row->unit_id     = $unitObj->id;
				$row->parent      = 0;
				$row->created     = date('Y-m-d H:i:s');
				$row->created_by  = JFactory::getApplication()->getAuthn('user_id');

				// Save the asset group
				if (!$assetGroupObj->save($row))
				{
					$this->setMessage("Asset group save failed", 500, 'Internal server error');
					return;
				}

				$return = new stdclass();
				$return->assetgroup_id      = $assetGroupObj->id;
				$return->assetgroup_title   = $assetGroupObj->title;
				$return->course_id          = $this->course_id;
				$return->assetgroup_style   = '';

				$assetGroups[] = $return;
			}
		}

		// Return message
		$this->setMessage(
			array(
				'unit_id'        => $unitObj->id,
				'unit_title'     => $unitObj->title,
				'course_id'      => $this->course_id,
				'assetgroups'    => $assetGroups,
				'course_alias'   => $this->course->get('alias'),
				'offering_alias' => $this->offering_alias
			),
			201, 'Created');
	}

	//--------------------------
	// Asset group functions
	//--------------------------

	/**
	 * Save an asset group
	 * 
	 * @return 201 created on success
	 */
	private function assetGroupSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Get our asset group object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');
		$assetGroupObj = new CoursesTableAssetGroup($this->db);

		if($id = JRequest::getInt('id', false))
		{
			if (!$assetGroupObj->load($id))
			{
				$this->setMessage("Loading asset group $id failed", 500, 'Internal server error');
				return;
			}
		}

		// We'll always save the title again, even if it's just to the same thing
		$title            = (!empty($assetGroupObj->title)) ? $assetGroupObj->title : 'New asset group';
		$row->title       = JRequest::getString('title', $title);
		$row->title       = preg_replace("/[^a-zA-Z0-9 \-\:\.]/", "", $row->title);
		$row->alias       = strtolower(str_replace(' ', '', $row->title));
		$row->description = '';

		// When creating a new asset group
		if(!$id)
		{
			$row->unit_id     = JRequest::getInt('unit_id', 0);
			$row->parent      = JRequest::getInt('parent', 0);
			$row->created     = date('Y-m-d H:i:s');
			$row->created_by  = JFactory::getApplication()->getAuthn('user_id');
		}

		// Save the asset group
		if (!$assetGroupObj->save($row))
		{
			$this->setMessage("Asset group save failed", 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage(
			array(
				'assetgroup_id'    => $assetGroupObj->id,
				'assetgroup_title' => $assetGroupObj->title,
				'assetgroup_style' => 'display:none',
				'course_id'        => $this->course_id,
				'offering_alias'   => $this->offering_alias),
			201, 'Created');
	}

	/**
	 * Reorder assets
	 * 
	 * @return 201 created on success
	 */
	private function assetGroupReorder()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		// @FIXME: implement this!  Need to add course_id and offering_alias to form submission
		/*$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}*/

		// Get our asset group object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.group.php');
		$assetGroupObj = new CoursesTableAssetGroup($this->db);

		$groups = JRequest::getVar('assetgroupitem', array());

		$order = 1;

		foreach ($groups as $id)
		{
			if (!$assetGroupObj->load($id))
			{
				$this->setMessage("Loading asset group $id failed", 500, 'Internal server error');
				return;
			}

			// Save the asset group
			if (!$assetGroupObj->save(array('ordering'=>$order)))
			{
				$this->setMessage("Asset group save failed", 500, 'Internal server error');
				return;
			}

			$order++;
		}


		// Return message
		$this->setMessage('New order saved', 201, 'Created');
	}

	//--------------------------
	// Asset functions
	//--------------------------

	/**
	 * Get the asset handlers for a given extension
	 * 
	 * @return 200 ok
	 */
	private function assetHandlers()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Get the incomming file name
		$name = JRequest::getCmd('name');

		// Get the file extension
		$ext = strtolower(array_pop(explode('.', $name)));

		// Initiate our file handler
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assets' . DS . 'assethandler.php');
		$assetHandler = new AssetHandler($this->db, $ext);

		// Get the handlers
		$handlers = $assetHandler->getHandlers();

		// Also check the PHP max post and upload values
		$max_upload = min((int)(ini_get('upload_max_filesize')), (int)(ini_get('post_max_size')));

		// Return message
		$this->setMessage(array('ext'=>$ext, 'handlers'=>$handlers, 'max_upload'=>$max_upload), 200, 'OK');
	}

	/**
	 * Create a new asset
	 * 
	 * @return 201 created on success
	 */
	private function assetNew()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// @FIXME: not all assets will be files...
		// Grab the incoming file
		if (isset($_FILES['files']))
		{
			$file_name = $_FILES['files']['name'][0];
			$file_size = (int) $_FILES['files']['size'];

			// Get the extension
			$pathinfo = pathinfo($file_name);
			$ext      = $pathinfo['extension'];

			// Initiate our file handler
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assets' . DS . 'assethandler.php');
			$assetHandler = new AssetHandler($this->db, $ext);

			// Create the new asset
			$return = $assetHandler->create(JRequest::getWord('handler', null));

			if(array_key_exists('error', $return))
			{
				$this->setMessage($return['error'], 500, 'Internal server error');
				return;
			}
		}
		else
		{
			$this->setMessage("No files given", 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage(array('assets'=>$return), 201, 'Created');
	}

	/**
	 * Save an asset
	 * 
	 * @return 201 created on success
	 */
	private function assetSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Get our asset object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
		$assetObj = new CoursesTableAsset($this->db);

		if($id = JRequest::getInt('id', false))
		{
			if (!$assetObj->load($id))
			{
				$this->setMessage("Loading asset $id failed", 500, 'Internal server error');
				return;
			}
		}

		// We'll always save the title again, even if it's just to the same thing
		$title            = (!empty($assetObj->title)) ? $assetObj->title : 'New asset';
		$row->title       = JRequest::getString('title', $title);
		$row->title       = preg_replace("/[^a-zA-Z0-9 \_\-\:\.]/", "", $row->title);

		// If we have an incoming url, update the url, otherwise, leave it alone
		if($url = JRequest::getVar('url', false))
		{
			$row->url = urldecode($url);
		}

		// When creating a new asset (which probably won't happen via this method, but rather the assetNew method above)
		if(!$id)
		{
			$row->type        = JRequest::getWord('type', 'file');
			$row->state       = 0;
			$row->course_id   = JRequest::getInt('course_id', 0);
			$row->created     = date('Y-m-d H:i:s');
			$row->created_by  = JFactory::getApplication()->getAuthn('user_id');
		}

		// Save the asset
		if (!$assetObj->save($row))
		{
			$this->setMessage("Asset group save failed", 500, 'Internal server error');
			return;
		}

		$files = array();

		// If we're creating a new asset, we should also create a new asset association
		if(!$id)
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');

			// Create asset assoc object
			$assocObj = new CoursesTableAssetAssociation($this->db);

			$row2->asset_id  = $assetObj->get('id');
			$row2->scope     = JRequest::getCmd('scope', 'asset_group');
			$row2->scope_id  = JRequest::getInt('scope_id', 0);

			// Save the asset association
			if (!$assocObj->save($row2))
			{
				$this->setMessage("Asset association save failed", 500, 'Internal server error');
				return;
			}

			$files = array(
				'asset_id'       => $row2->asset_id,
				'asset_title'    => $row->title,
				'asset_type'     => $row->type,
				'asset_url'      => $assetObj->url,
				'scope_id'       => $row2->scope_id,
				'course_id'      => $row->course_id,
				'offering_alias' => JRequest::getCmd('offering', '')
			);
		}

		// Return message
		$this->setMessage(
			array(
				'asset_id'    => $assetObj->id,
				'asset_title' => $assetObj->title,
				'course_id'   => $this->course_id,
				'files'       => array($files)),
			201, 'Created');
	}

	/**
	 * Delete an asset
	 * 
	 * @return 200 ok on success
	 */
	private function assetDelete()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Include needed files
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');

		// First, delete the asset association
		$assocObj = new CoursesTableAssetAssociation($this->db);

		// Get vars
		$asset_id  = JRequest::getInt('asset_id', 0);
		$scope     = JRequest::getCmd('scope', 'asset_group');
		$scope_id  = JRequest::getInt('scope_id', 0);

		// Make sure we're not missing anything
		if(!$asset_id || !$scope || !$scope_id)
		{
			// Missing needed variables to identify asset association
			$this->setMessage("Missing one of asset id, scope, or scope id", 422, 'Unprocessable Entity');
			return;
		}
		else
		{
			// Try to load the association
			if (!$assocObj->loadByAssetScope($asset_id, $scope_id, $scope))
			{
				$this->setMessage("Loading asset association failed", 500, 'Internal server error');
				return;
			}
			else
			{
				// Delete the association
				if (!$assocObj->delete())
				{
					$this->setMessage($assocObj->getError(), 500, 'Internal server error');
					return;
				}
			}
		}

		// Then, lookup whether or not there are other assocations connected to this asset
		$assetObj = new CoursesTableAsset($this->db);

		if (!$assetObj->load($asset_id))
		{
			$this->setMessage("Loading asset $id failed", 500, 'Internal server error');
			return;
		}

		// See if the asset is orphaned
		if (!$assetObj->isOrphaned())
		{
			// Asset isn't an orphan (i.e. it's still being used elsewhere), so we're done
			$this->setMessage(array('asset_id' => $assetObj->id), 200, 'OK');
			return;
		}

		// If no other associations exist, we'll delete the asset file and folder on the file system
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$deleted = array();
		$params  =& JComponentHelper::getParams('com_courses');
		$path    = DS . trim($params->get('uploadpath', '/site/courses'), DS) . DS . $this->course_id . DS . $assetObj->id;

		// If the path exists, delete it!
		if(JFolder::exists($path))
		{
			$deleted = JFolder::listFolderTree($path);
			JFolder::delete($path);
		}

		// Then we'll delete the asset entry itself
		if (!$assetObj->delete())
		{
			$this->setMessage($assetObj->getError(), 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage(
			array(
				'asset_id' => $assetObj->id,
				'deleted'  => $deleted
			),
			200, 'OK');
		return;
	}

	/**
	 * Reorder assets
	 * 
	 * @return 201 created on success
	 */
	private function assetsReorder()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		/*$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}*/

		// Get our asset group object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
		$assetAssocationObj = new CoursesTableAssetAssociation($this->db);

		$assets   = JRequest::getVar('asset', array());
		$scope_id = JRequest::getInt('scope_id', 0);
		$scope    = JRequest::getWord('scope', 'asset_group');

		$order = 1;

		foreach ($assets as $asset_id)
		{
			if (!$assetAssocationObj->loadByAssetScope($asset_id, $scope_id, $scope))
			{
				$this->setMessage("Loading asset association $asset_id failed", 500, 'Internal server error');
				return;
			}

			// Save the asset group
			if (!$assetAssocationObj->save(array('ordering'=>$order)))
			{
				$this->setMessage("Asset asssociation save failed", 500, 'Internal server error');
				return;
			}

			$order++;
		}


		// Return message
		$this->setMessage('New asset order saved', 201, 'Created');
	}

	/**
	 * Toggle the published state of an asset
	 * 
	 * @return 201 created on success
	 */
	private function assetTogglePublished()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// @TODO: log who makes the change

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Get the asset id
		$id = JRequest::getInt('id', 0);

		// Get our asset object
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
		$assetObj = new CoursesTableAsset($this->db);

		// Load the asset
		if (!$assetObj->load($id))
		{
			$this->setMessage("Loading asset $id failed", 500, 'Internal server error');
			return;
		}

		$state = ($assetObj->state == 1) ? 0 : 1;

		// Save the asset state
		if (!$assetObj->save(array('state'=>$state)))
		{
			$this->setMessage("Saving asset $id state failed", 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage('Asset state successfully saved', 201, 'Created');
	}

	//--------------------------
	// Miscelaneous methods
	//--------------------------

	/**
	 * Default method - not found
	 * 
	 * @return 404, method not found error
	 */
	private function method_not_found()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Set the error message
		$this->_response->setErrorMessage(404, 'Not found');
		return;
	}

	/**
	 * Helper function to check whether or not someone is using oauth and authorized
	 * 
	 * @return bool - true if in group, false otherwise
	 */
	private function authorize()
	{
		// Get the user id
		$user_id = JFactory::getApplication()->getAuthn('user_id');

		$authorized           = array();
		$authorized['view']   = false;
		$authorized['create'] = false;
		$authorized['manage'] = false;
		$authorized['admin']  = false;

		// Not logged in and/or not using OAuth
		if(!is_numeric($user_id))
		{
			return $authorized;
		}
		else
		{
			$authorized['create'] = true;
		}

		// Get the course id
		$this->course_id      = JRequest::getInt('course_id', 0);
		$this->offering_alias = JRequest::getCmd('offering', '');

		// Load the course page
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
		$course = CoursesModelCourse::getInstance($this->course_id);
		$this->course = $course;

		if ($course->access('manage') || $course->offering($this->offering_alias)->access('manage'))
		{
			$authorized['view']   = true;
			$authorized['manage'] = true;
			$authorized['admin']  = true;
		}
		elseif ($course->offering($this->offering_alias)->access('view'))
		{
			$authorized['view'] = true;
		}

		return $authorized;
	}
}