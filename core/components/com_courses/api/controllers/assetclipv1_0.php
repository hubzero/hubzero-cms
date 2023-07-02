<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Hubzero\Config\Registry;
use Components\Courses\Models\Assetclip;
use App;
use Request;
use Date;

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
		$scope = Request::getString('scope', 'asset_group');
		$scope_id = Request::getInt('scope_id', null);

		// Create an asset clip instance
		$assetClip = new Assetclip($id);
	
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

		// Save the asset clip
		if (!$assetClip->get('title'))
		{
			App::abort(400, 'No title provided');
		}

		$scope_id = Request::getInt('scope_id', null);
		if (!is_null($scope_id))
		{
			$assetClip->set('scope_id', $scope_id);
		}

		// When creating a new asset clip
		if (!$id)
		{
			$assetClip->set('scope', Request::getString('scope', 'asset_group'));
			$assetClip->set('scope_id', Request::getInt('scope_id', 0));
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
				'assetclip_id'    => $assetClip->get('id'),
				'assetclip_scope' => $assetClip->get('scope'),
				'assetclip_title' => $assetClip->get('title'),
				'assetclip_style' => 'display:none'
			], ($id ? 200 : 201)
		);
	}
}
