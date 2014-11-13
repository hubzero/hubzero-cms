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
class CoursesControllerApi extends \Hubzero\Component\ApiController
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
		switch ($this->segments[0])
		{
			// Units
			case 'unit':
				switch ($this->segments[1])
				{
					case 'save': $this->unitSave();         break;
					default:     $this->method_not_found(); break;
				}
			break;

			// Asset groups
			case 'assetgroup':
				switch ($this->segments[1])
				{
					case 'save':    $this->assetGroupSave();    break;
					case 'reorder': $this->assetGroupReorder(); break;
					default:        $this->method_not_found();  break;
				}
			break;

			// Assets
			case 'asset':
				switch ($this->segments[1])
				{
					case 'handlers':        $this->assetHandlers();        break;
					case 'new':             $this->assetNew();             break;
					case 'edit':            $this->assetEdit();            break;
					case 'preview':         $this->assetPreview();         break;
					case 'save':            $this->assetSave();            break;
					case 'delete':          $this->assetDelete();          break;
					case 'deletefile':      $this->assetDeleteFile();      break;
					case 'reorder':         $this->assetReorder();         break;
					case 'togglepublished': $this->assetTogglePublished(); break;
					case 'getformid':       $this->assetGetFormId();       break;
					case 'getformanddepid': $this->assetGetFormAndDepId(); break;
					default:                $this->method_not_found();     break;
				}
			break;

			// Forms
			case 'form':
				switch ($this->segments[1])
				{
					case 'image': $this->formImage();        break;
					default:      $this->method_not_found(); break;
				}
			break;

			// Forms
			case 'prerequisite':
				switch ($this->segments[1])
				{
					case 'new':    $this->prerequisiteNew();    break;
					case 'delete': $this->prerequisiteDelete(); break;
					default:       $this->method_not_found();   break;
				}
			break;

			// Passport
			case 'passport':                $this->passport();             break;

			// Unity
			case 'unityscoresave':          $this->unityScoreSave();       break;

			default:                       	$this->method_not_found();     break;
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
		if (!$authorized['manage'])
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

		$config = new \JConfig();
		$offset = $config->offset;

		// If we have dates coming in, save those
		if ($publish_up = JRequest::getVar('publish_up', false))
		{
			$unit->set('publish_up', JFactory::getDate($publish_up, $offset)->toSql());
		}
		if ($publish_down = JRequest::getVar('publish_down', false))
		{
			$unit->set('publish_down', JFactory::getDate($publish_down, $offset)->toSql());
		}

		// When creating a new unit
		if (!$id)
		{
			$unit->set('offering_id', JRequest::getInt('offering_id', 0));
			$unit->set('created', JFactory::getDate()->toSql());
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
		if (!$id)
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
				$assetGroup->set('created', JFactory::getDate()->toSql());
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

		// Need to return the content of the prerequisites view (not sure of a better way to do this at the moment)
		$view = new \Hubzero\Plugin\View(array(
			'folder'  => 'courses',
			'element' => 'outline',
			'name'    => 'outline',
			'layout'  => '_prerequisites'
		));

		$view->set('scope', 'unit')
		     ->set('scope_id', $unit->get('id'))
		     ->set('section_id', $this->course->offering()->section()->get('id'))
		     ->set('items', clone($this->course->offering()->units()));

		ob_start();
		$view->display();
		$prerequisites = ob_get_contents();
		ob_end_clean();

		// Return message
		$this->setMessage(
			array(
				'unit_id'        => $unit->get('id'),
				'unit_title'     => $unit->get('title'),
				'course_id'      => $this->course_id,
				'assetgroups'    => $assetGroups,
				'course_alias'   => $this->course->get('alias'),
				'offering_alias' => $this->offering_alias,
				'section_id'     => (isset($section_id) ? $section_id : $this->course->offering()->section()->get('id')),
				'prerequisites'  => $prerequisites
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
		if (!$authorized['manage'])
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
		if (!$id)
		{
			$assetGroup->set('unit_id', JRequest::getInt('unit_id', 0));
			$assetGroup->set('parent', JRequest::getInt('parent', 0));
			$assetGroup->set('created', JFactory::getDate()->toSql());
			$assetGroup->set('created_by', JFactory::getApplication()->getAuthn('user_id'));
		}

		if (($params = JRequest::getVar('params', false, 'post')) || !$id)
		{
			$p = new JRegistry('');

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('folder AS type, element AS name, params')
				->from('#__extensions')
				->where('enabled >= 1')
				->where('type =' . $db->Quote('plugin'))
				->where('state >= 0')
				->where('folder =' . $db->Quote('courses'))
				->order('ordering');

			if ($plugins = $db->setQuery($query)->loadObjectList())
			{
				foreach ($plugins as $plugin)
				{
					$default = new JRegistry($plugin->params);
					foreach ($default->toArray() as $k => $v)
					{
						if (substr($k, 0, strlen('default_')) == 'default_')
						{
							$p->set(substr($k, strlen('default_')), $default->get($k, $v));
						}
					}
				}
			}

			if ($params)
			{
				$p->loadArray($params);
			}

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
			$status['text']
		);
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
		$exts = explode('.', $name);
		$ext  = strtolower(array_pop($exts));

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
		if (!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Authorized');
			return;
		}

		// Grab the incoming file (incoming type overrides files)
		if (isset($_FILES['files']) && !JRequest::getWord('type', false))
		{
			$file_name = $_FILES['files']['name'][0];
			$file_size = (int) $_FILES['files']['size'];

			// Get the extension
			$pathinfo = pathinfo($file_name);
			$ext      = $pathinfo['extension'];
		}
		elseif ($contentType = JRequest::getWord('type', false))
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
		$return = $assetHandler->doCreate(JRequest::getWord('handler', null));

		// Check for errors in response
		if (array_key_exists('error', $return))
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
		if (!$authorized['manage'])
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
		if ($url = JRequest::getVar('url', false))
		{
			$asset->set('url', urldecode($url));
		}

		// If we have a state coming in as a word
		if ($published = JRequest::getWord('published', false))
		{
			$published = ($published == 'on') ? 1 : $asset->get('state');
			$asset->set('state', $published);
		}

		// If we have a state coming in as an int
		if ($published = JRequest::getInt('published', false))
		{
			$asset->set('state', $published);
		}

		// If we have a state coming in as an int
		if ($state = JRequest::getInt('state', false))
		{
			$asset->set('state', $state);
		}

		// If we have a state coming in as an int
		if ($graded = JRequest::getInt('graded', false))
		{
			$asset->set('graded', $graded);
			// By default, weight asset as a 'homework' type
			$grade_weight = $asset->get('grade_weight');
			if (empty($grade_weight))
			{
				$asset->set('grade_weight', 'homework');
			}
		}
		elseif ($graded = JRequest::getInt('edit_graded', false))
		{
			$asset->set('graded', 0);
		}

		// If we're saving progress calculation var
		if ($progress = JRequest::getInt('progress_factors', false))
		{
			$asset->set('progress_factors', array('asset_id'=>$asset->get('id'), 'section_id'=>$this->course->offering()->section()->get('id')));
		}
		elseif (JRequest::getInt('edit_progress_factors', false))
		{
			$asset->set('section_id', $this->course->offering()->section()->get('id'));
			$asset->set('progress_factors', 'delete');
		}

		// If we have content
		if ($content = JRequest::getVar('content', false, 'default', 'none', 2))
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

		// Check to see if the asset should be a link to a tool
		if ($tool_param = JRequest::getInt('tool_param', false))
		{
			$config = JComponentHelper::getParams('com_courses');

			// Make sure the tool path parameter is set
			if ($config->get('tool_path'))
			{
				$tool_alias = JRequest::getCmd('tool_alias');
				$tool_path  = DS . trim($config->get('tool_path'), DS) . DS;
				$asset_path = DS . trim($config->get('uploadpath'), DS) . DS . $this->course_id . DS . $asset->get('id');
				$file       = JFolder::files(JPATH_ROOT . $asset_path);

				// We're assuming there's only one file there...
				if (isset($file[0]) && !empty($file[0]))
				{
					$param_path = $tool_path . $asset->get('id') . DS . $file[0];

					// See if the file exists, and if not, copy the file there
					if (!is_dir(dirname($param_path)))
					{
						mkdir(dirname($param_path));
						copy(JPATH_ROOT . $asset_path . DS . $file[0], $param_path);
					}
					else
					{
						if (!is_file(JPATH_ROOT . $asset_path . DS . $file[0], $param_path))
						{
							copy(JPATH_ROOT . $asset_path . DS . $file[0], $param_path);
						}
					}

					// Set the type and build the invoke url with file param
					$asset->set('type',    'url');
					$asset->set('subtype', 'tool');
					$asset->set('url',     '/tools/'.$tool_alias.'/invoke?params=file:'.$param_path);
				}
			}
		}
		else if ($asset->get('type') == 'url' && $asset->get('subtype') == 'tool' && JRequest::getInt('edit_tool_param', false))
		{
			// This is the scenario where it was a tool launch link, but the box was unchecked
			$config     = JComponentHelper::getParams('com_courses');
			$tool_path  = DS . trim($config->get('tool_path'), DS) . DS;
			$asset_path = DS . trim($config->get('uploadpath'), DS) . DS . $this->course_id . DS . $asset->get('id');
			$file       = JFolder::files(JPATH_ROOT . $asset_path);
			$param_path = $tool_path . $asset->get('id') . DS . $file[0];

			// Delete the file (it still exists in the site directory)
			unlink($param_path);

			// Reset type and subtype to file
			$asset->set('type',    'file');
			$asset->set('subtype', 'file');
			$asset->set('url',     $file[0]);
		}

		// When creating a new asset (which probably won't happen via this method, but rather the assetNew method above)
		if (!$id)
		{
			$asset->set('type', JRequest::getWord('type', 'file'));
			$asset->set('subtype', JRequest::getWord('subtype', 'file'));
			$asset->set('state', 0);
			$asset->set('course_id', JRequest::getInt('course_id', 0));
			$asset->set('created', JFactory::getDate()->toSql());
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
		if (!$id)
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
		if (!$authorized['manage'])
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
		if (!$asset_id || !$scope || !$scope_id)
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
		$params  = JComponentHelper::getParams('com_courses');
		$path    = DS . trim($params->get('uploadpath', '/site/courses'), DS) . DS . $this->course_id . DS . $assetObj->id;

		// If the path exists, delete it!
		if (JFolder::exists($path))
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
	 * Delete an asset file
	 *
	 * @return 200 ok on success
	 */
	private function assetDeleteFile()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		// Require authorization
		$authorized = $this->authorize();
		if (!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Unauthorized');
			return;
		}

		// Include needed file(s)
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');

		// Grab incoming id, if applicable
		$id       = JRequest::getInt('id', null);
		$filename = JRequest::getVar('filename', null);

		// Create our object
		$asset = new CoursesModelAsset($id);

		if ($asset->get('course_id') != $this->course->get('id'))
		{
			$this->setMessage('Asset is not a part of this course.', 500, 'Internal server error');
			return;
		}

		$basePath = $asset->path($this->course->get('id'));
		$path     = $basePath . $filename;
		$dirname  = dirname($path);

		if (!is_file(JPATH_ROOT . $path) || $dirname != rtrim($basePath, DS))
		{
			$this->setMessage('Illegal file path', 500, 'Internal server error');
			return;
		}

		unlink(JPATH_ROOT . $path);

		// Return message
		$this->setMessage('File deleted', 200, 'OK');
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
		if (!$authorized['manage'])
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Authorized');
			return;
		}

		// Get the asset id
		if (!$id = JRequest::getInt('id', false))
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
		if (!$id = JRequest::getInt('id', false))
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
	 * Get form image
	 *
	 * @return 200 OK on success
	 */
	private function formImage()
	{
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'form.php';

		$id = JRequest::getInt('id', 0);
		$version = JRequest::getInt('version', false);

		$filename = JRequest::getVar('file', '');
		$filename = urldecode($filename);
		$filename = JPATH_ROOT . DS . 'site' . DS . 'courses' . DS . 'forms' . DS . $id . DS . (($version) ? $version . DS : '') . ltrim($filename, DS);

		// Ensure the file exist
		if (!file_exists($filename))
		{
			// Return message
			$this->setMessage('Image not found', 404, 'Not Found');
			return;
		}

		// Add silly simple security check
		$token      = JRequest::getString('token', false);
		$session_id = JFactory::getSession()->getId();
		$secret     = JFactory::getConfig()->getValue('secret');
		$hash       = hash('sha256', $session_id . ':' . $secret);

		if ($token !== $hash)
		{
			$this->setMessage('You don\'t have permission to do this', 401, 'Not Authorized');
			return;
		}

		// Initiate a new content server and serve up the file
		header("HTTP/1.1 200 OK");
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false);

		if (!$xserver->serve())
		{
			// Return message
			$this->setMessage('Failed to serve the image', 500, 'Server Error');
			return;
		}
	}

	/**
	 * Add a new prerequisite
	 *
	 * @return 201 Created
	 **/
	private function prerequisiteNew()
	{
		$this->setMessageType($this->format);

		require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'prerequisite.php';

		$tbl = new CoursesTablePrerequisites($this->db);
		$tbl->set('item_scope', JRequest::getWord('item_scope', 'asset'));
		$tbl->set('item_id', JRequest::getInt('item_id', 0));
		$tbl->set('requisite_scope', JRequest::getWord('requisite_scope', 'asset'));
		$tbl->set('requisite_id', JRequest::getInt('requisite_id', 0));
		$tbl->set('section_id', JRequest::getInt('section_id', 0));

		if (!$tbl->store())
		{
			$this->setMessage('Failed to save new prerequisite', 500, 'Server Error');
			return;
		}
		else
		{
			$this->setMessage(array('success'=>true, 'id'=>$tbl->get('id')), 201, 'Created');
			return;
		}
	}

	/**
	 * Delete a prerequisite
	 *
	 * @return 200 Ok
	 **/
	private function prerequisiteDelete()
	{
		$this->setMessageType($this->format);

		require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'prerequisite.php';

		if (!$id = JRequest::getInt('id', false))
		{
			$this->setMessage("No ID provided", 422, 'Unprocessable entity');
			return;
		}

		$tbl = new CoursesTablePrerequisites($this->db);
		$tbl->load($id);
		$tbl->delete();

		$this->setMessage('Item successfully deleted', 200, 'Ok');
		return;
	}

	/**
	 * Passport badges. Placeholder for now.
	 *
	 * @return 200 OK on success
	 */
	private function passport()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		if (!$this->authorize_call())
		{
			$this->setMessage('You don\'t have permission to do this', 403, 'Unauthorized');
			return;
		}

		$action     = JRequest::getVar('action', '');
		$badge_id   = JRequest::getVar('badge_id', '');
		$user_email = JRequest::getVar('user_email', '');

		if (empty($action))
		{
			$this->errorMessage(400, 'Please provide action');
			return;
		}
		if ($action != 'accept' && $action != 'deny')
		{
			$this->errorMessage(400, 'Bad action. Must be either accept or deny');
			return;
		}
		if (empty($badge_id))
		{
			$this->errorMessage(400, 'Please provide badge ID');
			return;
		}
		if (empty($user_email))
		{
			$this->errorMessage(400, 'Please provide user email');
			return;
		}

		// Find user by email
		$user_email = \Hubzero\User\Profile\Helper::find_by_email($user_email);

		if (empty($user_email[0]))
		{
			$this->errorMessage(404, 'User was not found');
			return;
		}
		$user = \Hubzero\User\Profile::getInstance($user_email[0]);
		if ($user === false)
		{
			$this->errorMessage(404, 'User was not found');
			return;
		}

		$user_id = $user->get('uidNumber');

		// Make sure a few things are included
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section' . DS . 'badge.php';
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php';
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'memberBadge.php';

		// Get section from provider badge id
		$section_badge = CoursesModelSectionBadge::loadByProviderBadgeId($badge_id);

		// Check if there is a match
		if (!$section_id = $section_badge->get('section_id'))
		{
			$this->errorMessage(400, 'No matching badge found');
			return;
		}

		// Get member id via user id and section id
		$member = CoursesModelMember::getInstance($user_id, 0, 0, $section_id);

		// Check if there is a match
		if (!$member->get('id'))
		{
			$this->errorMessage(400, 'Matching course member not found');
			return;
		}

		// Now actually load the badge
		$member_badge = CoursesModelMemberBadge::loadByMemberId($member->get('id'));

		// Check if there is a match
		if (!$member_badge->get('id'))
		{
			$this->errorMessage(400, 'This member does not have a matching badge entry');
			return;
		}

		$now = JFactory::getDate()->toSql();

		$member_badge->set('action', $action);
		$member_badge->set('action_on', $now);
		$member_badge->store();

		// Return message
		$this->setMessage('Passport data saved.', 200, 'OK');
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
		if (!$id = JRequest::getInt('id', false))
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
		/*if (!is_numeric($depId))
		{
			$this->setMessage("Failed to retrieve the deployment ID", 500, 'Internal server error');
			return;
		}*/

		// Return message
		$this->setMessage(array('form_id' => $formId, 'deployment_id' => $depId), 200, 'OK');
	}

	/**
	 * Process grade save from unity app
	 *
	 * @return 200 OK on success
	 */
	private function unityScoreSave()
	{
		// Set the responce type
		$this->setMessageType($this->format);

		$user_id = JFactory::getApplication()->getAuthn('user_id');

		if (!$user_id || !is_numeric($user_id))
		{
			$this->setMessage("Unauthorized", 403, 'Unauthorized');
			return;
		}

		// Parse some things out of the referer
		$referer = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : JRequest::getVar('referrer');
		preg_match('/\/asset\/([[:digit:]]*)/', $referer, $matches);

		if (!$asset_id = $matches[1])
		{
			$this->setMessage("Failed to get asset ID", 422, 'Unprocessable Entity');
			return;
		}

		// Get course info...this seems a little wonky
		preg_match('/\/courses\/([[:alnum:]\-\_]*)\/([[:alnum:]\:\-\_]*)/', $referer, $matches);

		$course_alias   = $matches[1];
		$offering_alias = $matches[2];
		$section_alias  = null;

		if (strpos($offering_alias, ":"))
		{
			$parts = explode(":", $offering_alias);
			$offering_alias = $parts[0];
			$section_alias  = $parts[1];
		}

		require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php';

		$course = CoursesModelCourse::getInstance($course_alias);
		$course->offering($offering_alias);
		$course->offering()->section($section_alias);
		$section_id = $course->offering()->section()->get('id');

		$member = CoursesModelMember::getInstance($user_id, 0, 0, $section_id);

		if (!$member_id = $member->get('id'))
		{
			$this->setMessage("Failed to get course member ID", 422, 'Unprocessable Entity');
			return;
		}

		if (!$data = JRequest::getVar('payload', false))
		{
			$this->setMessage("Missing payload", 422, 'Unprocessable Entity');
			return;
		}

		// Get the key and IV - Trim the first xx characters from the payload for IV
		$key  = $course->config()->get('unity_key', 0);
		$iv   = substr($data, 0, 32);
		$data = substr($data, 32);

		$message = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_CBC, $iv);
		$message = trim($message);
		$message = json_decode($message);

		if (!$message || !is_object($message))
		{
			$this->setMessage("Failed to decode message", 500, 'Internal error');
			return;
		}

		// Get timestamp
		$now = JFactory::getDate()->toSql();

		// Save the unity details
		require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.unity.php';
		$unity = new CoursesTableAssetUnity($this->db);
		$unity->set('member_id', $member_id);
		$unity->set('asset_id', $asset_id);
		$unity->set('created', $now);
		$unity->set('passed', (($message->passed) ? 1 : 0));
		$unity->set('details', $message->details);
		if (!$unity->store())
		{
			$this->setMessage($unity->getError(), 500, 'Internal error');
			return;
		}

		// Now set/update the gradebook item
		require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'grade.book.php';
		$gradebook = new CoursesTableGradeBook($this->db);
		$gradebook->loadByUserAndAssetId($member_id, $asset_id);

		// Score is either 100 or 0
		$score = ($message->passed) ? 100 : 0;

		// See if gradebook entry already exists
		if ($gradebook->get('id'))
		{
			// Entry does exist, see if current score is better than previous score
			if ($score > $gradebook->get('score'))
			{
				$gradebook->set('score', $score);
				$gradebook->set('score_recorded', \JFactory::getDate()->toSql());
				if (!$gradebook->store())
				{
					$this->setMessage($gradebook->getError(), 500, 'Internal error');
					return;
				}
			}
		}
		else
		{
			$gradebook->set('member_id', $member_id);
			$gradebook->set('score', $score);
			$gradebook->set('scope', 'asset');
			$gradebook->set('scope_id', $asset_id);
			$gradebook->set('score_recorded', \JFactory::getDate()->toSql());
			if (!$gradebook->store())
			{
				$this->setMessage($gradebook->getError(), 500, 'Internal error');
				return;
			}
		}

		// Return message
		$this->setMessage(array('success' => true), 200, 'OK');
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
	 * Helper function to check whether or not someone is using oauth and authorized to use this call
	 *
	 * @return bool - true if in group, false otherwise
	 */
	private function authorize_call()
	{
		$postdata    = ($this->getRequest()->get('postdata'));
		$consumerKey = $postdata['oauth_consumer_key'];

		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$user = \Hubzero\User\Profile::getInstance($userid);
		//make sure we have a user
		if ($user === false)
		{
			/*
			$this->errorMessage(401, 'You don\'t have permission to do this');
			return;
			*/
		}

		// Get the requested path
		$path = ($this->getRequest()->get('path'));

		// Do access check
		// @NOTE: The following assumption is made: the code check only permissions for the closest parent. Parent's parent permissions are not inherited.

		// First find the closest matching permission (closest parent, longest path).
		$sql = 'SELECT `path` FROM `#__api_permissions`
				WHERE INSTR(' . $this->db->quote($path) . ', `path`) = 1
				GROUP BY LENGTH(`path`)
				ORDER BY LENGTH(`path`) DESC
				LIMIT 1';

		$this->db->setQuery($sql);
		$this->db->query();

		// Check if there is a match, if no match, no permissions set, good to go
		if (!$this->db->getNumRows())
		{
			return true;
		}

		$permissions_path = $this->db->loadResult();

		// Get all groups the current user is a member of
		$user_groups = array();
		if (!empty($user))
		{
			$user_groups = $user->getGroups('members');
		}

		// Next see if the user is allowed to make this call
		$sql = 'SELECT `user_id`, `group_id` FROM `#__api_permissions` WHERE `path` = ' . $this->db->quote($permissions_path) . ' AND
				(`user_id` = ' . $this->db->quote($userid) . ' OR `consumer_key` = ' . $this->db->quote($consumerKey) . ' OR 0';

		foreach ($user_groups as $group)
		{
			$sql .= ' OR `group_id` = ' . $this->db->quote($group->gidNumber);
		}

		$sql .= ')';
		$this->db->setQuery($sql);
		$this->db->query();

		// There is a match, permission granted
		if ($this->db->getNumRows())
		{
			return true;
		}

		// No match, too bad. Unauthorized
		$this->errorMessage(401, 'You don\'t have permission to make this call');
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
		if (!is_numeric($user_id))
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
		$this->section_id     = JRequest::getInt('section_id', '');

		// Load the course page
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
		$course = CoursesModelCourse::getInstance($this->course_id);
		$offering = $course->offering($this->offering_alias);
		$course->offering()->section($this->section_id);
		$this->course = $course;

		if ($course->access('manage'))
		{
			$authorized['view']   = true;
			$authorized['manage'] = true;
			$authorized['admin']  = true;
		}

		return $authorized;
	}

	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param	$code		Error Code
	 * @param	$message	Error Message
	 * @param	$format		Error Response Format
	 *
	 * @return     void
	 */
	private function errorMessage($code, $message)
	{
		//build error code and message
		$object = new stdClass();
		$object->error->code = $code;
		$object->error->message = $message;

		//set http status code and reason
		$response = $this->getResponse();
		$response->setErrorMessage($object->error->code, $object->error->message, $object->error->message);

		//add error to message body
		$this->setMessage($object);
	}
}
