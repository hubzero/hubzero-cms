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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Components\Courses\Tables\AssetAssociation;
use Components\Courses\Tables\Asset as AssetTbl;
use Components\Courses\Models\Asset;
use Components\Courses\Models\Assets\Handler;
use Request;
use Component;
use App;
use Date;
use Filesystem;

require_once __DIR__ . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'asset.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assets' . DS . 'handler.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'asset.association.php';

/**
 * API controller for the course assets
 */
class Assetv1_0 extends base
{
	/**
	 * Gets the asset handlers for a given extension
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/handlers
	 * @apiParameter {
	 * 		"name":        "name",
	 * 		"description": "Name of file to be uploaded",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function handlersTask()
	{
		// Get the incomming file name
		$name = Request::getCmd('name');

		// Get the file extension
		$exts = explode('.', $name);
		$ext  = strtolower(array_pop($exts));

		// Initiate our file handler
		$database     = App::get('db');
		$assetHandler = new Handler($database, $ext);

		// Get the handlers
		$handlers = $assetHandler->getHandlers();

		// Also check the PHP max post and upload values
		$max_upload = min((int)(ini_get('upload_max_filesize')), (int)(ini_get('post_max_size')));

		// Return message
		$this->send(['ext'=>$ext, 'handlers'=>$handlers, 'max_upload'=>$max_upload]);
	}

	/**
	 * Creates a new asset
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/new
	 * @apiParameter {
	 * 		"name":        "files",
	 * 		"description": "Files to upload",
	 * 		"type":        "array",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "type",
	 * 		"description": "Content type being created",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "handler",
	 * 		"description": "The file handler to use",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function newTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Grab the incoming file (incoming type overrides files)
		if (isset($_FILES['files']) && !Request::getWord('type', false))
		{
			$file_name = $_FILES['files']['name'][0];
			$file_size = (int) $_FILES['files']['size'];

			// Get the extension
			$pathinfo = pathinfo($file_name);
			$ext      = $pathinfo['extension'];
		}
		elseif ($contentType = Request::getWord('type', false))
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
			App::abort(400, 'No assets given');
		}

		// Initiate our file handler
		$database     = App::get('db');
		$assetHandler = new Handler($database, $ext);

		// Create the new asset
		$return = $assetHandler->doCreate(Request::getVar('handler', null));

		// Check for errors in response
		if (array_key_exists('error', $return))
		{
			App::abort(400, $return['error']);
		}

		// Return message
		$this->send(['assets'=>$return], 201);
	}

	/**
	 * Retrieves the asset edit page
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/edit
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "ID of asset to edit",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function editTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Make sure we have an asset id
		if (!$asset_id = Request::getInt('id', false))
		{
			App::abort(500, 'No asset id provided');
		}

		// Initiate our file handler
		$database     = App::get('db');
		$assetHandler = new Handler($database);

		// Edit the asset
		$return = $assetHandler->doEdit($asset_id);

		// Check for errors in response
		if (is_array($return) && array_key_exists('error', $return))
		{
			App::abort(400, $return['error']);
		}

		// Return message
		$this->send($return);
	}

	/**
	 * Previews an asset
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/preview
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "ID of asset to preview",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function previewTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Make sure we have an asset id
		if (!$asset_id = Request::getInt('id', false))
		{
			App::abort(500, 'No asset id provided');
		}

		// Initiate our file handler
		$database     = App::get('db');
		$assetHandler = new Handler($database);

		// Edit the asset
		$return = $assetHandler->preview($asset_id);

		// Check for errors in response
		if (is_array($return) && array_key_exists('error', $return))
		{
			App::abort(400, $return['error']);
		}

		// Return message
		$this->send($return);
	}

	/**
	 * Saves an asset
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/save
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "ID of asset to save",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Asset title",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "New asset"
	 * }
	 * @apiParameter {
	 * 		"name":        "published",
	 * 		"description": "Asset state",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "state",
	 * 		"description": "Asset state",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function saveTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Grab incoming id, if applicable
		$id = Request::getInt('id', null);

		// Create our object
		$asset = new Asset($id);

		// Check to make sure we have an asset group object
		if (!is_object($asset))
		{
			App::abort(500, 'Failed to create an asset object');
		}

		// We'll always save the title again, even if it's just to the same thing
		$orgTitle = $asset->get('title');
		$title    = $asset->get('title');
		$title    = (!empty($title)) ? $title : 'New asset';

		// Set or variables
		$asset->set('title', Request::getString('title', $title));
		// @FIXME: do we want any sort of character restrictions on asset group titles?
		//preg_replace("/[^a-zA-Z0-9 \-\:\.]/", "", $asset->get('title'));
		$asset->set('alias', strtolower(str_replace(' ', '', $asset->get('title'))));

		// If we have an incoming url, update the url, otherwise, leave it alone
		if ($url = Request::getVar('url', false))
		{
			$asset->set('url', urldecode($url));
		}

		// If we have a state coming in as a word
		if ($published = Request::getWord('published', false))
		{
			$published = ($published == 'on') ? 1 : $asset->get('state');
			$asset->set('state', $published);
		}

		// If we have a state coming in as an int
		if ($published = Request::getInt('published', false))
		{
			$asset->set('state', $published);
		}

		// If we have a state coming in as an int
		if ($state = Request::getInt('state', false))
		{
			$asset->set('state', $state);
		}

		// If we have a state coming in as an int
		if ($graded = Request::getInt('graded', false))
		{
			$asset->set('graded', $graded);
			// By default, weight asset as a 'homework' type
			$grade_weight = $asset->get('grade_weight');
			if (empty($grade_weight))
			{
				$asset->set('grade_weight', 'homework');
			}
		}
		elseif ($graded = Request::getInt('edit_graded', false))
		{
			$asset->set('graded', 0);
		}

		// If we're saving progress calculation var
		if ($progress = Request::getInt('progress_factors', false))
		{
			$asset->set('progress_factors', array('asset_id'=>$asset->get('id'), 'section_id'=>$this->course->offering()->section()->get('id')));
		}
		elseif (Request::getInt('edit_progress_factors', false))
		{
			$asset->set('section_id', $this->course->offering()->section()->get('id'));
			$asset->set('progress_factors', 'delete');
		}

		// If we have content
		if ($content = Request::getVar('content', false, 'default', 'none', 2))
		{
			$asset->set('content', $content);
		}

		// If we have type or subtype
		if ($type = Request::getWord('type', false))
		{
			$asset->set('type', $type);
		}
		if ($subtype = Request::getWord('subtype', false))
		{
			$asset->set('subtype', $subtype);
		}
		else
		{
			$title = Request::getString('title', false);
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
		if ($tool_param = Request::getInt('tool_param', false))
		{
			$config = Component::params('com_courses');

			// Make sure the tool path parameter is set
			if ($config->get('tool_path'))
			{
				$tool_alias = Request::getCmd('tool_alias');
				$tool_path  = DS . trim($config->get('tool_path'), DS) . DS;
				$asset_path = DS . trim($config->get('uploadpath'), DS) . DS . $this->course_id . DS . $asset->get('id');
				$file       = Filesystem::files(PATH_APP . $asset_path);

				// We're assuming there's only one file there...
				if (isset($file[0]) && !empty($file[0]))
				{
					$param_path = $tool_path . $asset->get('id') . DS . $file[0];

					// See if the file exists, and if not, copy the file there
					if (!is_dir(dirname($param_path)))
					{
						mkdir(dirname($param_path));
						copy(PATH_APP . $asset_path . DS . $file[0], $param_path);
					}
					else
					{
						if (!is_file(PATH_APP . $asset_path . DS . $file[0], $param_path))
						{
							copy(PATH_APP . $asset_path . DS . $file[0], $param_path);
						}
					}

					// Set the type and build the invoke url with file param
					$asset->set('type',    'url');
					$asset->set('subtype', 'tool');
					$asset->set('url',     '/tools/'.$tool_alias.'/invoke?params=file:'.$param_path);
				}
			}
		}
		else if ($asset->get('type') == 'url' && $asset->get('subtype') == 'tool' && Request::getInt('edit_tool_param', false))
		{
			// This is the scenario where it was a tool launch link, but the box was unchecked
			$config     = Component::params('com_courses');
			$tool_path  = DS . trim($config->get('tool_path'), DS) . DS;
			$asset_path = DS . trim($config->get('uploadpath'), DS) . DS . $this->course_id . DS . $asset->get('id');
			$file       = Filesystem::files(PATH_APP . $asset_path);
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
			$asset->set('type', Request::getWord('type', 'file'));
			$asset->set('subtype', Request::getWord('subtype', 'file'));
			$asset->set('state', 0);
			$asset->set('course_id', Request::getInt('course_id', 0));
			$asset->set('created', Date::toSql());
			$asset->set('created_by', App::get('authn')['user_id']);
		}

		// Save the asset
		if (!$asset->store())
		{
			App::abort(500, 'Asset save failed');
		}

		$files    = array();
		$database = App::get('db');
		$row = (object) array();

		// If we're creating a new asset, we should also create a new asset association
		if (!$id)
		{
			// Create asset assoc object
			$assocObj = new AssetAssociation($database);

			$row->asset_id  = $asset->get('id');
			$row->scope     = Request::getCmd('scope', 'asset_group');
			$row->scope_id  = Request::getInt('scope_id', 0);

			// Save the asset association
			if (!$assocObj->save($row))
			{
				App::abort(500, 'Asset association save failed');
			}
		}
		else
		{
			$scope_id          = Request::getInt('scope_id', null);
			$original_scope_id = Request::getInt('original_scope_id', null);
			$scope             = Request::getCmd('scope', 'asset_group');

			// Only worry about this if scope id is changing
			if (!is_null($scope_id) && !is_null($original_scope_id) && $scope_id != $original_scope_id)
			{
				// Create asset assoc object
				$assocObj = new AssetAssociation($database);

				if (!$assocObj->loadByAssetScope($asset->get('id'), $original_scope_id, $scope))
				{
					App::abort(500, 'Failed to load asset association');
				}

				// Set new scope id
				$row->scope_id  = $scope_id;

				// Save the asset association
				if (!$assocObj->save($row))
				{
					App::abort(500, 'Asset association save failed');
				}
			}
		}

		// Build the asset url
		$url = Route::url('index.php?option=com_courses&controller=offering&gid='.$this->course->get('alias').'&offering='.$this->offering_alias.'&asset='.$asset->get('id'));

		$files = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'asset_state'    => $asset->get('state'),
			'scope_id'       => (isset($row) && isset($row->scope_id)) ? $row->scope_id : '',
			'course_id'      => $this->course_id,
			'offering_alias' => Request::getCmd('offering', '')
		);

		// Set the status code
		$status = ($id) ? array('code'=>200, 'text'=>'OK') : array('code'=>201, 'text'=>'Created');

		// Return message
		$this->send(
			[
				'asset_id'    => $asset->get('id'),
				'asset_title' => $asset->get('title'),
				'course_id'   => $this->course_id,
				'files'       => array($files)
			], ($id ? 200 : 201)
		);
	}

	/**
	 * Deletes an asset
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/delete
	 * @apiParameter {
	 * 		"name":        "asset_id",
	 * 		"description": "ID of asset to delete",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Asset scope",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Asset scope ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// First, delete the asset association
		$database = App::get('db');
		$assocObj = new AssetAssociation($database);

		// Get vars
		$asset_id  = Request::getInt('asset_id', 0);
		$scope     = Request::getCmd('scope', 'asset_group');
		$scope_id  = Request::getInt('scope_id', 0);

		// Make sure we're not missing anything
		if (!$asset_id || !$scope || !$scope_id)
		{
			// Missing needed variables to identify asset association
			App::abort(404, 'Missing one of asset id, scope, or scope id');
		}
		else
		{
			// Try to load the association
			if (!$assocObj->loadByAssetScope($asset_id, $scope_id, $scope))
			{
				App::abort(500, 'Loading asset association failed');
			}
			else
			{
				// Delete the association
				if (!$assocObj->delete())
				{
					App::abort(500, $assocObj->getError());
				}
			}
		}

		// Then, lookup whether or not there are other assocations connected to this asset
		$assetObj = new AssetTbl($database);

		if (!$assetObj->load($asset_id))
		{
			App::abort(500, "Loading asset {$id} failed");
		}

		// See if the asset is orphaned
		if (!$assetObj->isOrphaned())
		{
			// Asset isn't an orphan (i.e. it's still being used elsewhere), so we're done
			$this->send(['asset_id' => $assetObj->id]);
			return;
		}

		// If no other associations exist, we'll delete the asset file and folder on the file system
		$deleted = [];
		$params  = Component::params('com_courses');
		$path    = DS . trim($params->get('uploadpath', '/site/courses'), DS) . DS . $this->course_id . DS . $assetObj->id;

		// If the path exists, delete it!
		if (Filesystem::exists($path))
		{
			$deleted = Filesystem::listFolderTree($path);
			Filesystem::deleteDirectory($path);
		}

		// Then we'll delete the asset entry itself
		if (!$assetObj->delete())
		{
			App::abort(500, $assetObj->getError());
		}

		// Return message
		$this->send(
			[
				'asset_id' => $assetObj->id,
				'deleted'  => $deleted
			]
		);
	}

	/**
	 * Deletes an asset file
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/deletefile
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "ID of asset owning file",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "filename",
	 * 		"description": "Name of file to delete",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deletefileTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Grab incoming id, if applicable
		$id       = Request::getInt('id', null);
		$filename = Request::getVar('filename', null);

		// Create our object
		$asset = new Asset($id);

		if ($asset->get('course_id') != $this->course->get('id'))
		{
			App::abort(500, 'Asset is not a part of this course.');
		}

		$basePath = $asset->path($this->course->get('id'));
		$path     = $basePath . $filename;
		$dirname  = dirname($path);

		if (!is_file(PATH_APP . $path) || $dirname != rtrim($basePath, DS))
		{
			App::abort(500, 'Illegal file path');
		}

		unlink(PATH_APP . $path);

		// Return message
		$this->send('File deleted');
	}

	/**
	 * Reorders assets
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/reorder
	 * @apiParameter {
	 * 		"name":        "asset",
	 * 		"description": "Array of IDs of assets to reorder",
	 * 		"type":        "array",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Asset scope",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "Asset scope ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function reorderTask()
	{
		// Get our asset group object
		$database           = App::get('db');
		$assetAssocationObj = new AssetAssociation($database);

		$assets   = Request::getVar('asset', array());
		$scope_id = Request::getInt('scope_id', 0);
		$scope    = Request::getWord('scope', 'asset_group');

		$order = 1;

		foreach ($assets as $asset_id)
		{
			if (!$assetAssocationObj->loadByAssetScope($asset_id, $scope_id, $scope))
			{
				App::abort(500, "Loading asset association {$asset_id} failed");
			}

			// Save the asset group
			if (!$assetAssocationObj->save(array('ordering'=>$order)))
			{
				App::abort(500, 'Asset asssociation save failed');
			}

			$order++;
		}


		// Return message
		$this->send('New asset order saved');
	}

	/**
	 * Toggles the published state of an asset
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/asset/togglepublished
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "ID of asset to toggle state",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function togglepublishedTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Get the asset id
		if (!$id = Request::getInt('id', false))
		{
			App::abort(404, 'No ID provided');
		}

		// Get our asset object
		$asset = new Asset($id);

		// Make sure we have an asset model
		if (!is_object($asset) || !$asset instanceof \Components\Courses\Models\Asset)
		{
			App::abort(500, "Loading asset {$id} failed");
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
			App::abort(500, "Saving asset {$id} state failed");
		}

		// Return message
		$this->send(['asset_state'=>$asset->get('state')]);
	}

	/**
	 * Looks up the form id based on the asset id
	 *
	 * @apiMethod GET
	 * @apiUri    /courses/asset/getformid
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "ID of asset to look up",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function getformidTask()
	{
		// Get the asset id
		if (!$id = Request::getInt('id', false))
		{
			App::abort(404, 'No ID provided');
		}

		$database = App::get('db');

		$database->setQuery("SELECT `id` FROM `#__courses_forms` WHERE `asset_id` = " . $database->quote($id));

		// Get the form ID from the content
		$formId = $database->loadResult();

		// Check
		if (!is_numeric($formId))
		{
			App::abort(500, 'Failed to retrieve the form ID');
		}

		// Now check to see if this exam has already been deployed
		$database->setQuery("SELECT `id` FROM `#__courses_form_deployments` WHERE `form_id` = " . $database->quote($formId));

		// Get the form ID from the content
		$result = $database->loadResult();

		if ($result)
		{
			// Return message
			$this->send('Deployment already exists', 204);
			return;
		}

		// Return message
		$this->send(['form_id' => $formId]);
	}

	/**
	 * Looks up the form id and deployment id based on the asset id
	 * @FIXME: combine this with method above
	 *
	 * @apiMethod GET
	 * @apiUri    /courses/asset/getformanddepid
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "ID of asset to look up",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function getformanddepidTask()
	{
		// Get the asset id
		if (!$id = Request::getInt('id', false))
		{
			App::abort(404, 'No ID provided');
		}

		$database = App::get('db');
		$database->setQuery("SELECT `id` FROM `#__courses_forms` WHERE `asset_id` = " . $database->Quote($id));

		// Get the form ID from the content
		$formId = $database->loadResult();

		// Check
		if (!is_numeric($formId))
		{
			App::abort(500, 'Failed to retrieve the form ID');
		}

		// Now check to see if this exam has already been deployed
		$database->setQuery("SELECT `id` FROM `#__courses_form_deployments` WHERE `form_id` = " . $database->Quote($formId));

		// Get the form ID from the content
		$depId = $database->loadResult();

		// Check
		/*if (!is_numeric($depId))
		{
			$this->setMessage("Failed to retrieve the deployment ID", 500, 'Internal server error');
			return;
		}*/

		// Return message
		$this->send(['form_id' => $formId, 'deployment_id' => $depId]);
	}
}
