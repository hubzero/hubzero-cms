<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Hubzero\Config\Registry;
use Components\Courses\Models\Assetclip as AssetclipModel;
use Components\Courses\Tables\Assetclip as AssetclipTable;
use App;
use Request;
use Date;
use stdClass;

require_once __DIR__ . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assetclip.php';

/**
 * API controller for course asset clips
 */
class Assetclipv1_0 extends base
{
	/**
	 * Saves an asset clip
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/assetclip/save
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Asset clip ID to save",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Asset clip title",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "New asset clip"
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_,
	 * 		"description": "Scope of asset",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     "asset_group"
	 * }
	 * @apiParameter {
	 * 		"name":        "scope_id",
	 * 		"description": "ID of asset",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "params",
	 * 		"description": "Parameters related to the asset clip",
	 * 		"type":        "array",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function saveTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Check for an incoming 'id'
		$id = Request::getInt('id', null);

		// Create an asset clip model instance
		$assetClip = new AssetclipModel($id);
	
		// Check to make sure we have an asset group object
		if (!is_object($assetClip))
		{
			App::abort(500, 'Failed to create an asset clip object');
		}

		// We'll always save the title again, even if it's just to the same thing
		$title = $assetClip->get('title');
		$title = (!empty($title)) ? $title : 'New asset clip';

		// Set our variables
		$assetClip->set('title', Request::getString('title', $title));

		if (!$assetClip->get('title'))
		{
			App::abort(400, 'No title provided');
		}

		$scope = Request::getString('scope', 'asset_group');
		if (!is_null($scope))
		{
			$assetClip->set('scope', $scope);
		}

		$scope_id = Request::getInt('scope_id', null);
		if (!is_null($scope_id))
		{
			$assetClip->set('scope_id', $scope_id);
		}

		$type = Request::getString('type', null);
		if (!is_null($type))
		{
			$assetClip->set('type', $type);
		}

		// When creating a new asset clip
		if (!$id)
		{
			$assetClip->set('scope', Request::getString('scope', 'asset_group'));
			$assetClip->set('scope_id', Request::getInt('scope_id', 0));
			$assetClip->set('type', Request::getString('type', 'Lectures'));
			$assetClip->set('title', Request::getString('title', 'New asset clip'));
			$assetClip->set('created', Date::toSql());
			$assetClip->set('created_by', App::get('authn')['user_id']);
		}

		if (($params = Request::getVar('params', false, 'post')) || !$id)
		{
			$p     = new Registry('');
			$db    = App::get('db');
			$query = $db->getQuery(true);

			$query->select('folder AS type, element AS name, params')
			      ->from('#__extensions')
			      ->where('enabled >= 1')
			      ->where('type =' . $db->quote('plugin'))
			      ->where('state >= 0')
			      ->where('folder =' . $db->quote('courses'))
			      ->order('ordering');

			if ($plugins = $db->setQuery($query)->loadObjectList())
			{
				foreach ($plugins as $plugin)
				{
					$default = new Registry($plugin->params);
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
				$p->parse($params);
			}

			$assetClip->set('params', $p->toString());
		}

		// Save the asset clip
		if (!$assetClip->store())
		{
			App::abort(500, 'Asset clip save failed');
		}

		// Return message
		$this->send(
			[
				'assetclip_id'       => $assetClip->get('id'),
				'assetclip_scope'    => $assetClip->get('scope'),
				'assetclip_scope_id' => $assetClip->get('scope_id'),
				'assetclip_title'    => $assetClip->get('title'),
				'assetclip_type'     => $assetClip->get('type'),
				'assetclip_style'    => 'display:none'
			], ($id ? 200 : 201)
		);
	}
	/**
	 * Lists asset clips
	 *
	 * @apiMethod GET
	 * @apiUri    /courses/assetcip/list
	 * @apiParameter {
	 * 		"name":        "limit",
	 * 		"description": "Number of records to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     25 
	 * }
	 * @apiParameter {
	 * 		"name":        "limitstart",
	 * 		"description": "Offset of Records to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0 
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$filters = array(
			'limit' => Request::getInt('limit', 25),
			'start' => Request::getInt('limitstart', 0)
		);

		$db = App::get('db');
		$assetClips = new AssetclipTable($db);
		$total   = $assetClips->count($filters);
		$assetClips = $assetClips->find($filters);

		$records = array();
		foreach ($assetClips as $assetClip)
		{
			$entry = new stdClass;
			$entry->id = $assetClip->id;
			$entry->scope = $assetClip->scope;
			$entry->scope_id = $assetClip->scope_id;
			$entry->type = $assetClip->type;
			$entry->title = $assetClip->title;
			array_push($records, $entry);
		}

		$response = new stdClass;
		$response->assetClips = $records;
		$response->total = $total;
		$response->success = true;
		$this->send($response);
	}

	/**
	 * Search asset clips
	 *
	 * @apiMethod GET
	 * @apiUri    /courses/assetcip/search
	 * @apiParameter {
	 * 		"name":        "scope",
	 * 		"description": "Scope of clip",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     'asset_group' 
	 * }
	 * 	 * @apiParameter {
	 * 		"name":        "type",
	 * 		"description": "Type of clip",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     none 
	 * }
	 * @apiParameter {
	 * 		"name":        "user",
	 * 		"description": "Id of user who created clip",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     App::get('authn')['user_id']
	 * }
	 * @apiParameter {
	 * 		"name":        "search",
	 * 		"description": "word/phrase search for in clip title",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     none
	 * }
	 * @apiParameter {
	 * 		"name":        "limit",
	 * 		"description": "Number of records to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     25 
	 * }
	 * @return    void
	 */
	public function searchTask()
	{
		$user = App::get('authn')['user_id'];
		$filters = array(
			'scope' => Request::getString('scope', 'asset_group'),
			'user' => Request::getInt('user', $user),
			'limit' => Request::getInt('limit', 25)
		);
		$type = Request::getString('type','');
		if (!empty($type)) {
			$filters['type'] = $type;
		}
		$search = Request::getString('search','');
		if (!empty($search)) {
			$filters['search'] = $search;
		}

		$db = App::get('db');
		$assetClips = new AssetclipTable($db);
		$total   = $assetClips->count($filters);
		$assetClips = $assetClips->find($filters);

		$records = array();
		foreach ($assetClips as $assetClip)
		{
			$entry = new stdClass;
			$entry->id = $assetClip->id;
			$entry->scope = $assetClip->scope;
			$entry->scope_id = $assetClip->scope_id;
			$entry->type = $assetClip->type;
			$entry->title = $assetClip->title;
			array_push($records, $entry);
		}

		$response = new stdClass;
		$response->assetClips = $records;
		$response->total = $total;
		$response->success = true;
		$this->send($response);
	}
}
