<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Tags\Api\Controllers;

use Components\Tags\Models\Tag;
use Hubzero\Component\ApiController;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'cloud.php');

/**
 * API controller class for tags
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('autocomplete', 'list');

		parent::execute();
	}

	/**
	 * Documents available API tasks and their options
	 *
	 * @return  void
	 */
	public function indexTask()
	{
		$response = new stdClass();
		$response->component = 'tags';
		$response->tasks = array(
			'tags' => array(
				'description' => Lang::txt('Get a list of tags.'),
				'parameters'  => array(
					'search' => array(
						'description' => Lang::txt('A word or phrase to search for.'),
						'type'        => 'string',
						'default'     => 'null'
					),
					'scope' => array(
						'description' => Lang::txt('Object type to retrieve tags for (ex: resource, answer, etc).'),
						'type'        => 'string',
						'default'     => 'null',
						'requires'    => 'scope_id'
					),
					'scope_id' => array(
						'description' => Lang::txt('ID of object to retrieve tags for.'),
						'type'        => 'integer',
						'default'     => 'null',
						'requires'    => 'scope'
					),
					'sort' => array(
						'description' => Lang::txt('Sorting to be applied to the records.'),
						'type'        => 'string',
						'default'     => 'date',
						'accepts'     => array('raw_tag', 'id')
					),
					'sortDir' => array(
						'description' => Lang::txt('Direction to sort records by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'limit' => array(
						'description' => Lang::txt('Number of result to return.'),
						'type'        => 'integer',
						'default'     => '25'
					),
					'limitstart' => array(
						'description' => Lang::txt('Number of where to start returning results.'),
						'type'        => 'integer',
						'default'     => '0'
					),
				),
			),
			'tag' => array(
				'description' => Lang::txt('Get information for a tag.'),
				'parameters'  => array(
					'tag' => array(
						'description' => Lang::txt('The tag to retrieve information for.'),
						'type'        => 'string',
						'default'     => 'null'
					),
				),
			),
		);

		$this->send($response);
	}

	/**
	 * Displays a list of tags
	 *
	 * @return  void
	 */
	public function listTask()
	{
		$cloud = new \Components\Tags\Models\Cloud();

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'tbl'        => Request::getword('scope', ''),
			'objectid'   => Request::getInt('scope_id', 0),
			'taggerid'   => Request::getVar('tagger', ''),
			'sort'       => Request::getWord('sort', 'raw_tag'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'ASC'))
		);

		$response = new stdClass;
		$response->tags  = array();
		$response->total = $cloud->tags('count', $filters);
		//$response->showing = ($filters['start'] + 1) . ' - ' . ($filters['start'] + $filters['limit']);
		$response->start = $filters['start'];
		$response->limit = $filters['limit'];

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			foreach ($cloud->tags('list', $filters) as $i => $tag)
			{
				$obj = new stdClass;
				$obj->id      = $tag->get('id');
				$obj->raw_tag = $tag->get('raw_tag');
				$obj->tag     = $tag->get('tag');
				$obj->uri     = str_replace('/api', '', $base . '/' . ltrim(Route::url($tag->link()), '/'));

				$obj->substitutes_count = $tag->get('substitutes');
				$obj->objects_count = $tag->get('total');

				$response->tags[] = $obj;
			}
		}

		$response->success = true;

		$this->send($response);
	}

	/**
	 * Create a new entry
	 *
	 * @return  void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$tag   = Request::getVar('tag', null, 'post');
		$raw   = Request::getVar('raw_tag', null, 'post');
		$label = Request::getVar('label', null, 'post');
		$admin = Request::getInt('admin', 0, 'post');
		$subs  = Request::getVar('substitutes', null, 'post');

		if (!$tag && !$raw_tag)
		{
			throw new Exception(Lang::txt('COM_TAGS_ERROR_MISSING_DATA'), 500);
		}

		$tag = ($tag ? $tag : $raw_tag);

		$record = new Tag($tag);
		if (!$record->exists())
		{
			$record->set('admin', ($admin ? 1 : 0));

			if ($raw_tag)
			{
				$record->set('raw_tag', $raw_tag);
			}
			if ($tag)
			{
				$record->set('tag', $tag);
			}

			$record->set('label', $label);
			$record->set('substitutions', $subs);

			if (!$record->store(true))
			{
				throw new Exception($record->getError(), 500);
			}
		}

		$this->send($record->toObject());
	}

	/**
	 * Display info for a tag
	 *
	 * @return  void
	 */
	public function readTask()
	{
		$name = Request::getWord('tag', '');
		$id   = Request::getInt('id', 0);
		$id   = ($id ? $id : $name);

		$tag = new Tag($id);
		if (!$tag->exists())
		{
			throw new Exception(Lang::txt('Specified tag does not exist.'), 404);
		}

		$this->send($tag->toObject());
	}

	/**
	 * Update an entry
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		$id    = Request::getInt('id', 0);
		$tag   = Request::getVar('tag', null);
		$raw   = Request::getVar('raw_tag', null);
		$label = Request::getVar('label', null);
		$admin = Request::getInt('admin', 0);
		$subs  = Request::getVar('substitutes', null);

		if (!$id)
		{
			throw new Exception(Lang::txt('COM_TAGS_ERROR_MISSING_DATA'), 500);
		}

		$record = new Tag($id);
		if (!$record->exists())
		{
			$record->set('admin', ($admin ? 1 : 0));

			if ($raw_tag)
			{
				$record->set('raw_tag', $raw_tag);
			}
			if ($tag)
			{
				$record->set('tag', $tag);
			}

			$record->set('label', $label);
			$record->set('substitutions', $subs);

			if (!$record->store(true))
			{
				throw new Exception($record->getError(), 500);
			}
		}

		$this->send($record->toObject());
	}

	/**
	 * Remove tag from an item
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		$name = Request::getWord('tag', '');
		$id   = Request::getInt('id', 0);
		$id   = ($id ? $id : $tag);

		$tag = new Tag($id);
		if (!$tag->exists())
		{
			throw new Exception(Lang::txt('Specified tag does not exist.'), 404);
		}

		if (!$tag->delete())
		{
			throw new Exception(Lang::txt('Failed to delete tag.'), 500);
		}

		$this->send(null, 202);
	}

	/**
	 * Remove tag from an item
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		$this->requiresAuthentication();

		$name = Request::getWord('tag', '');
		$id   = Request::getInt('id', 0);
		$id   = ($id ? $id : $name);

		$tag = new Tag($id);
		if (!$tag->exists())
		{
			throw new Exception(Lang::txt('Specified tag does not exist.'), 404);
		}

		$scope    = Request::getWord('scope', '');
		$scope_id = Request::getInt('scope_id', 0);
		$tagger   = Request::getInt('tagger', 0);

		if (!$scope || !$scope_id)
		{
			throw new Exception(Lang::txt('Invalid scope and/or scope_id.'), 500);
		}

		if (!$tag->removeFrom($scope, $scope_id, $tagger))
		{
			throw new Exception(Lang::txt('Failed to remove tag from object.'), 500);
		}

		$this->send(null, 202);
	}

	/**
	 * Add a tag to an item
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->requiresAuthentication();

		$name = Request::getWord('tag', '');
		$id   = Request::getInt('id', 0);
		$id   = ($id ? $id : $name);

		$tag = new Tag($id);
		if (!$tag->exists())
		{
			throw new Exception(Lang::txt('Specified tag does not exist.'), 404);
		}

		$scope    = Request::getWord('scope', '');
		$scope_id = Request::getInt('scope_id', 0);
		$tagger   = Request::getInt('tagger', 0);

		if (!$scope || !$scope_id)
		{
			throw new Exception(Lang::txt('Invalid scope and/or scope_id.'), 500);
		}

		if (!$tag->addTo($scope, $scope_id, $tagger))
		{
			throw new Exception(Lang::txt('Failed to add tag to object.'), 500);
		}

		$this->send(null, 202);
	}
}
