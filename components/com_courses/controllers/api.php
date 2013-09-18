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
class CoursesControllerApi extends Hubzero_Api_Controller
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

		// Switch based on entity type and action
		switch($this->segments[0])
		{
			// Units
			case 'unit':
				switch($this->segments[1])
				{
					case 'save': $this->unitSave();         break;
					default:     $this->method_not_found(); break;
				}
			break;

			// Asset groups
			case 'assetgroup':
				switch($this->segments[1])
				{
					case 'save':    $this->assetGroupSave();    break;
					case 'reorder': $this->assetGroupReorder(); break;
					default:        $this->method_not_found();  break;
				}
			break;

			// Assets
			case 'asset':
				switch($this->segments[1])
				{
					case 'handlers':        $this->assetHandlers();        break;
					case 'new':             $this->assetNew();             break;
					case 'edit':            $this->assetEdit();            break;
					case 'preview':         $this->assetPreview();         break;
					case 'save':            $this->assetSave();            break;
					case 'delete':          $this->assetDelete();          break;
					case 'reorder':         $this->assetReorder();         break;
					case 'togglepublished': $this->assetTogglePublished(); break;
					case 'getformid':       $this->assetGetFormId();       break;
					case 'getformanddepid': $this->assetGetFormAndDepId(); break;
					default:                $this->method_not_found();     break;
				}
			break;

			default:                       $this->method_not_found();         break;
		}
	}

	//--------------------------
	// Units functions
	//--------------------------

	/**
	 * Save a course unit
	 * 
	 * @return '201 Created' on new, '200 OK' otherwise
	 */
	private function unitSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Authorized');
			return;
		}

		// Import needed courses JTable libraries
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');

		// Make sure we have an incoming 'id'
		$id = JRequest::getInt('id', null);

		// Create our unit model
		$unit =& CoursesModelUnit::getInstance($id);

		// Check to make sure we have a unit object
		if (!is_object($unit))
		{
			$this->setMessage("Failed to create a unit object", 500, 'Internal server error');
			return;
		}

		if ($section_id = JRequest::getInt('section_id', false))
		{
			$unit->set('section_id', $section_id);
		}

		// We'll always save the title again, even if it's just to the same thing
		$title = $unit->get('title');
		$title = (!empty($title)) ? $title : 'New Unit';

		// Set our values
		$unit->set('title', JRequest::getString('title', $title));
		$unit->set('alias', strtolower(str_replace(' ', '', $unit->get('title'))));

		// If we have dates coming in, save those
		if($publish_up = JRequest::getVar('publish_up', false))
		{
			$unit->set('publish_up', $publish_up);
		}
		if($publish_down = JRequest::getVar('publish_down', false))
		{
			$unit->set('publish_down', $publish_down);
		}

		// When creating a new unit
		if(!$id)
		{
			$unit->set('offering_id', JRequest::getInt('offering_id', 0));
			$unit->set('created', date('Y-m-d H:i:s'));
			$unit->set('created_by', JFactory::getApplication()->getAuthn('user_id'));
		}

		// Save the unit
		if (!$unit->store())
		{
			$this->setMessage("Saving unit {$id} failed ({$unit->getError()})", 500, 'Internal server error');
			return;
		}

		// Create a placeholder for our return object
		$assetGroups = array();

		// If this is a new unit, give it some default asset groups
		// Create a top level asset group for each of lectures, homework, and exam
		if(!$id)
		{
			// Included needed classes
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assetgroup.php');

			// Get the courses config
			$config = JComponentHelper::getParams('com_courses');
			$asset_groups = explode(',', $config->getValue('default_asset_groups', 'Lectures, Homework, Exam'));
			array_map('trim', $asset_groups);

			foreach ($asset_groups as $key)
			{
				// Get our asset group object
				$assetGroup = new CoursesModelAssetgroup(null);

				$assetGroup->set('title', $key);
				$assetGroup->set('alias', strtolower(str_replace(' ', '', $assetGroup->get('title'))));
				$assetGroup->set('unit_id', $unit->get('id'));
				$assetGroup->set('parent', 0);
				$assetGroup->set('created', date('Y-m-d H:i:s'));
				$assetGroup->set('created_by', JFactory::getApplication()->getAuthn('user_id'));

				// Save the asset group
				if (!$assetGroup->store())
				{
					$this->setMessage("Asset group save failed", 500, 'Internal server error');
					return;
				}

				$return = new stdclass();
				$return->assetgroup_id      = $assetGroup->get('id');
				$return->assetgroup_title   = $assetGroup->get('title');
				$return->course_id          = $this->course_id;
				$return->assetgroup_style   = '';

				$assetGroups[] = $return;
			}
		}

		// Set the status code
		$status = ($id) ? array('code'=>200, 'text'=>'OK') : array('code'=>201, 'text'=>'Created');

		// Return message
		$this->setMessage(
			array(
				'unit_id'        => $unit->get('id'),
				'unit_title'     => $unit->get('title'),
				'course_id'      => $this->course_id,
				'assetgroups'    => $assetGroups,
				'course_alias'   => $this->course->get('alias'),
				'offering_alias' => $this->offering_alias,
				'section_id'     => (isset($section_id) ? $section_id : $this->course->offering()->section()->get('id'))
			),
			$status['code'],
			$status['text']);
	}

	//--------------------------
	// Asset group functions
	//--------------------------

	/**
	 * Save an asset group
	 * 
	 * @return '201 Created' on new, '200 OK' otherwise
	 */
	private function assetGroupSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if(!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Aauthorized');
			return;
		}

		// Include needed classes
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assetgroup.php');

		// Check for an incoming 'id'
		$id = JRequest::getInt('id', null);

		// Create an asset group instance
		$assetGroup = new CoursesModelAssetgroup($id);

		// Check to make sure we have an asset group object
		if (!is_object($assetGroup))
		{
			$this->setMessage("Failed to create an asset group object", 500, 'Internal server error');
			return;
		}

		// We'll always save the title again, even if it's just to the same thing
		$title = $assetGroup->get('title');
		$title = (!empty($title)) ? $title : 'New asset group';

		// Set our variables
		$assetGroup->set('title', JRequest::getString('title', $title));
		$assetGroup->set('alias', strtolower(str_replace(' ', '', $assetGroup->get('title'))));

		$state = JRequest::getInt('state', null);
		if (!is_null($state))
		{
			$assetGroup->set('state', $state);
		}

		$assetGroup->set('description', JRequest::getVar('description', $assetGroup->get('description')));

		// When creating a new asset group
		if(!$id)
		{
			$assetGroup->set('unit_id', JRequest::getInt('unit_id', 0));
			$assetGroup->set('parent', JRequest::getInt('parent', 0));
			$assetGroup->set('created', date('Y-m-d H:i:s'));
			$assetGroup->set('created_by', JFactory::getApplication()->getAuthn('user_id'));
		}

		if ($params = JRequest::getVar('params', false, 'post'))
		{
			$paramsClass = 'JParameter';
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$paramsClass = 'JRegistry';
			}

			$p = new $paramsClass('');
			$p->bind($params);

			$assetGroup->set('params', $p->toString());
		}

		// Save the asset group
		if (!$assetGroup->store())
		{
			$this->setMessage("Asset group save failed", 500, 'Internal server error');
			return;
		}

		// Set the status code
		$status = ($id) ? array('code'=>200, 'text'=>'OK') : array('code'=>201, 'text'=>'Created');

		// Return message
		$this->setMessage(
			array(
				'assetgroup_id'    => $assetGroup->get('id'),
				'assetgroup_title' => $assetGroup->get('title'),
				'assetgroup_state' => (int) $assetGroup->get('state'),
				'assetgroup_style' => 'display:none',
				'course_id'        => $this->course_id,
				'offering_alias'   => $this->offering_alias
			),
			$status['code'],
			$status['text']);
	}

	/**
	 * Reorder assets
	 * 
	 * @return 200 OK on success
	 */
	private function assetGroupReorder()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Include needed classes
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assetgroup.php');

		$groups = JRequest::getVar('assetgroupitem', array());

		$order = 1;

		foreach ($groups as $id)
		{
			if (!$assetGroup = new CoursesModelAssetgroup($id))
			{
				$this->setMessage("Loading asset group {$id} failed", 500, 'Internal server error');
				return;
			}

			// Set the new order
			$assetGroup->set('ordering', $order);

			// Save the asset group
			if (!$assetGroup->store())
			{
				$this->setMessage("Asset group save failed", 500, 'Internal server error');
				return;
			}

			$order++;
		}


		// Return message
		$this->setMessage('New order saved', 200, 'OK');
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
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Authorized');
			return;
		}

		// Grab the incoming file (incoming type overrides files)
		if(isset($_FILES['files']) && !JRequest::getWord('type', false))
		{
			$file_name = $_FILES['files']['name'][0];
			$file_size = (int) $_FILES['files']['size'];

			// Get the extension
			$pathinfo = pathinfo($file_name);
			$ext      = $pathinfo['extension'];
		}
		elseif($contentType = JRequest::getWord('type', false))
		{
			// @FIXME: having this here breaks the responder model idea
			// The content type handlers could respond to a function that assesses the incoming data?
			switch ($contentType)
			{
				case 'link':
					$ext = 'url';
					break;
				case 'object':
					$ext = 'object';
					break;
				case 'wiki':
					$ext = 'wiki';
					break;
			}
		}
		else
		{
			$this->setMessage("No assets given", 500, 'Internal server error');
			return;
		}

		// Initiate our file handler
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assets' . DS . 'assethandler.php');
		$assetHandler = new AssetHandler($this->db, $ext);

		// Create the new asset
		$return = $assetHandler->create(JRequest::getWord('handler', null));

		// Check for errors in response
		if(array_key_exists('error', $return))
		{
			$this->setMessage($return['error'], 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage(array('assets'=>$return), 201, 'Created');
	}

	/**
	 * Retrieve the asset edit page
	 * 
	 * @return 200 OK
	 */
	private function assetEdit()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if (!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Authorized');
			return;
		}

		// Make sure we have an asset id
		if (!$asset_id = JRequest::getInt('id', false))
		{
			$this->setMessage("No asset id provided.", 500, 'Internal server error');
			return;
		}

		// Initiate our file handler
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assets' . DS . 'assethandler.php');
		$assetHandler = new AssetHandler($this->db);

		// Edit the asset
		$return = $assetHandler->doEdit($asset_id);

		// Check for errors in response
		if (is_array($return) && array_key_exists('error', $return))
		{
			$this->setMessage($return['error'], 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage($return, 200, 'OK');
	}

	/**
	 * Preview an asset
	 * 
	 * @return 200 OK
	 */
	private function assetPreview()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if (!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Authorized');
			return;
		}

		// Make sure we have an asset id
		if (!$asset_id = JRequest::getInt('id', false))
		{
			$this->setMessage("No asset id provided.", 500, 'Internal server error');
			return;
		}

		// Initiate our file handler
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'assets' . DS . 'assethandler.php');
		$assetHandler = new AssetHandler($this->db);

		// Edit the asset
		$return = $assetHandler->preview($asset_id);

		// Check for errors in response
		if (is_array($return) && array_key_exists('error', $return))
		{
			$this->setMessage($return['error'], 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage($return, 200, 'OK');
	}

	/**
	 * Save an asset
	 * 
	 * @return '201 Created' on new, '200 OK' otherwise
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

		// Include needed file(s)
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');

		// Grab incoming id, if applicable
		$id = JRequest::getInt('id', null);

		// Create our object
		$asset = new CoursesModelAsset($id);

		// Check to make sure we have an asset group object
		if (!is_object($asset))
		{
			$this->setMessage("Failed to create an asset object", 500, 'Internal server error');
			return;
		}

		// We'll always save the title again, even if it's just to the same thing
		$orgTitle = $asset->get('title');
		$title    = $asset->get('title');
		$title    = (!empty($title)) ? $title : 'New asset';

		// Set or variables
		$asset->set('title', JRequest::getString('title', $title));
		// @FIXME: do we want any sort of character restrictions on asset group titles?
		//preg_replace("/[^a-zA-Z0-9 \-\:\.]/", "", $asset->get('title'));
		$asset->set('alias', strtolower(str_replace(' ', '', $asset->get('title'))));

		// If we have an incoming url, update the url, otherwise, leave it alone
		if($url = JRequest::getVar('url', false))
		{
			$asset->set('url', urldecode($url));
		}

		// If we have a state coming in as a word
		if($published = JRequest::getWord('published', false))
		{
			$published = ($published == 'on') ? 1 : $asset->get('state');
			$asset->set('state', $published);
		}

		// If we have a state coming in as an int
		if($published = JRequest::getInt('published', false))
		{
			$asset->set('state', $published);
		}

		// If we have a state coming in as an int
		if($state = JRequest::getInt('state', false))
		{
			$asset->set('state', $state);
		}

		// If we have content
		if($content = JRequest::getVar('content', false))
		{
			$asset->set('content', $content);
		}

		// If we have type or subtype
		if ($type = JRequest::getWord('type', false))
		{
			$asset->set('type', $type);
		}
		if ($subtype = JRequest::getWord('subtype', false))
		{
			$asset->set('subtype', $subtype);
		}
		else
		{
			$title = JRequest::getString('title', false);
			// If we don't have a subtype incoming, but the type is form, try to guess subtype from title
			if ($asset->get('type') == 'form' && $title && $title != $orgTitle)
			{
				if (strpos(strtolower($title), 'exam') !== false)
				{
					$asset->set('subtype', 'exam');
				}
				elseif (strpos(strtolower($title), 'quiz') !== false)
				{
					$asset->set('subtype', 'quiz');
				}
				elseif (strpos(strtolower($title), 'homework') !== false)
				{
					$asset->set('subtype', 'homework');
				}
			}
		}

		// When creating a new asset (which probably won't happen via this method, but rather the assetNew method above)
		if(!$id)
		{
			$asset->set('type', JRequest::getWord('type', 'file'));
			$asset->set('subtype', JRequest::getWord('subtype', 'file'));
			$asset->set('state', 0);
			$asset->set('course_id', JRequest::getInt('course_id', 0));
			$asset->set('created', date('Y-m-d H:i:s'));
			$asset->set('created_by', JFactory::getApplication()->getAuthn('user_id'));
		}

		// Save the asset
		if (!$asset->store())
		{
			$this->setMessage("Asset save failed", 500, 'Internal server error');
			return;
		}

		$files = array();

		// If we're creating a new asset, we should also create a new asset association
		if(!$id)
		{
			// Create asset assoc object
			$assocObj = new CoursesTableAssetAssociation($this->db);

			$row->asset_id  = $asset->get('id');
			$row->scope     = JRequest::getCmd('scope', 'asset_group');
			$row->scope_id  = JRequest::getInt('scope_id', 0);

			// Save the asset association
			if (!$assocObj->save($row))
			{
				$this->setMessage("Asset association save failed", 500, 'Internal server error');
				return;
			}
		}
		else
		{
			$scope_id          = JRequest::getInt('scope_id', null);
			$original_scope_id = JRequest::getInt('original_scope_id', null);
			$scope             = JRequest::getCmd('scope', 'asset_group');

			// Only worry about this if scope id is changing
			if (!is_null($scope_id) && !is_null($original_scope_id) && $scope_id != $original_scope_id)
			{
				// Create asset assoc object
				$assocObj = new CoursesTableAssetAssociation($this->db);

				if (!$assocObj->loadByAssetScope($asset->get('id'), $original_scope_id, $scope))
				{
					$this->setMessage("Failed to load asset association", 500, 'Internal server error');
					return;
				}

				// Set new scope id
				$row->scope_id  = $scope_id;

				// Save the asset association
				if (!$assocObj->save($row))
				{
					$this->setMessage("Asset association save failed", 500, 'Internal server error');
					return;
				}
			}
		}

		// Build the asset url
		$url = JRoute::_('index.php?option=com_courses&controller=offering&gid='.$this->course->get('alias').'&offering='.$this->offering_alias.'&asset='.$asset->get('id'));

		$files = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'asset_state'    => $asset->get('state'),
			'scope_id'       => (isset($row)) ? $row->scope_id : '',
			'course_id'      => $this->course_id,
			'offering_alias' => JRequest::getCmd('offering', '')
		);

		// Set the status code
		$status = ($id) ? array('code'=>200, 'text'=>'OK') : array('code'=>201, 'text'=>'Created');

		// Return message
		$this->setMessage(
			array(
				'asset_id'    => $asset->get('id'),
				'asset_title' => $asset->get('title'),
				'course_id'   => $this->course_id,
				'files'       => array($files)
			),
			$status['code'],
			$status['text']
		);
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
	 * @return 200 OK on success
	 */
	private function assetReorder()
	{
		// Set the responce type
		$this->setMessageType($this->format);

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
		$this->setMessage('New asset order saved', 200, 'OK');
	}

	/**
	 * Toggle the published state of an asset
	 * 
	 * @return 200 OK on success
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
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Authorized');
			return;
		}

		// Get the asset id
		if(!$id = JRequest::getInt('id', false))
		{
			$this->setMessage("No ID provided", 422, 'Unprocessable entity');
			return;
		}

		// Get our asset object
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');
		$asset = new CoursesModelAsset($id);

		// Make sure we have an asset model
		if (!is_object($asset) || !$asset instanceof CoursesModelAsset)
		{
			$this->setMessage("Loading asset {$id} failed", 500, 'Internal server error');
			return;
		}

		// If the current state is 1 (published), we'll toggle to 0 (unpublished)
		$state = ($asset->get('state') == 1) ? 0 : 1;
		// If the current state is 2 (deleted), we should toggle to 0 (unpublished)
		// i.e. items coming out of trash should always default to unpublished
		$state = ($asset->get('state') == 2) ? 0 : $state;

		// Set the state
		$asset->set('state', $state);

		// Save the asset
		if (!$asset->store())
		{
			$this->setMessage("Saving asset {$id} state failed", 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage(array('asset_state'=>$asset->get('state')), 200, 'OK');
	}

	/**
	 * Look up the form id based on the asset id
	 * 
	 * @return 200 OK on success
	 */
	private function assetGetFormId()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Get the asset id
		if(!$id = JRequest::getInt('id', false))
		{
			$this->setMessage("No ID provided", 422, 'Unprocessable entity');
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__courses_forms` WHERE `asset_id` = " . $this->db->Quote($id));

		// Get the form ID from the content
		$formId = $this->db->loadResult();

		// Check
		if (!is_numeric($formId))
		{
			$this->setMessage("Failed to retrieve the form ID", 500, 'Internal server error');
			return;
		}

		// Now check to see if this exam has already been deployed
		$this->db->setQuery("SELECT `id` FROM `#__courses_form_deployments` WHERE `form_id` = " . $this->db->Quote($formId));

		// Get the form ID from the content
		$result = $this->db->loadResult();

		if ($result)
		{
			// Return message
			$this->setMessage('Deployment already exists', 204, 'No content');
			return;
		}

		// Return message
		$this->setMessage(array('form_id' => $formId), 200, 'OK');
	}

	/**
	 * Look up the form id and deployment id based on the asset id
	 * @FIXME: combine this with method above
	 * 
	 * @return 200 OK on success
	 */
	private function assetGetFormAndDepId()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Get the asset id
		if(!$id = JRequest::getInt('id', false))
		{
			$this->setMessage("No ID provided", 422, 'Unprocessable entity');
			return;
		}

		$this->db->setQuery("SELECT `id` FROM `#__courses_forms` WHERE `asset_id` = " . $this->db->Quote($id));

		// Get the form ID from the content
		$formId = $this->db->loadResult();

		// Check
		if (!is_numeric($formId))
		{
			$this->setMessage("Failed to retrieve the form ID", 500, 'Internal server error');
			return;
		}

		// Now check to see if this exam has already been deployed
		$this->db->setQuery("SELECT `id` FROM `#__courses_form_deployments` WHERE `form_id` = " . $this->db->Quote($formId));

		// Get the form ID from the content
		$depId = $this->db->loadResult();

		// Check
		if (!is_numeric($depId))
		{
			$this->setMessage("Failed to retrieve the deployment ID", 500, 'Internal server error');
			return;
		}

		// Return message
		$this->setMessage(array('form_id' => $formId, 'deployment_id' => $depId), 200, 'OK');
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
		$offering = $course->offering($this->offering_alias);
		$this->course = $course;

		if ($course->access('manage'))
		{
			$authorized['view']   = true;
			$authorized['manage'] = true;
			$authorized['admin']  = true;
		}

		return $authorized;
	}
}