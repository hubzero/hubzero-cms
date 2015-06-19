<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Billboards\Api\Controllers;

use Components\Billboards\Models\Collection;
use Components\Billboards\Models\Billboard;
use Hubzero\Component\ApiController;
use Request;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'collection.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'billboard.php';

/**
 * Billboards collections API controller
 */
class Collectionsv1_0 extends ApiController
{
	/**
	 * Lists an index of existing billboard collections
	 *
	 * @apiMethod GET
	 * @apiUri    /billboards/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		// Get the request vars
		$limit      = Request::getVar('limit', 25);
		$limitstart = Request::getVar('limitstart', 0);

		// Load up the entries
		$collections = Collection::start($limitstart)->limit($limit)->rows();

		$this->send(array('collections' => $collections->toArray()));
	}

	/**
	 * Lists all billboards for a given collection
	 *
	 * @apiMethod GET
	 * @apiUri    /billboards/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Collection identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return  void
	 */
	public function readTask()
	{
		// Get the collection id
		$collection = Request::getInt('id', 0);

		// Load up the collection
		$collection = Collection::oneOrNew($collection);

		// Make sure we found a collection
		if ($collection->isNew())
		{
			throw new Exception(Lang::txt('Collection not found'), 404);
		}

		$billboards = $collection->billboards()
		                         ->select('name')
		                         ->select('learn_more_target')
		                         ->select('background_img')
		                         ->whereEquals('published', 1)
		                         ->rows();

		foreach ($billboards as $billboard)
		{
			$image = $billboard->get('background_img');
			$billboard->set('retina_background_img', $image);

			if (is_file(PATH_APP . DS . $image))
			{
				$image_info   = pathinfo($image);
				$retina_image = $image_info['dirname'] . DS . $image_info['filename'] . "@2x." . $image_info['extension'];
				if (file_exists(PATH_APP . DS . $retina_image))
				{
					$billboard->set('retina_background_img', $retina_image);
				}
			}
		}

		// Get the collection and its billboards
		$collection = array(
			'id'         => $collection->id,
			'name'       => $collection->name,
			'billboards' => $billboards->toArray()
		);

		$this->send(array('collection' => $collection));
	}
}
