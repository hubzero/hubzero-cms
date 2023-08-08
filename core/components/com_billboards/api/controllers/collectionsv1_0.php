<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$limit      = Request::getInt('limit', 25);
		$limitstart = Request::getInt('limitstart', 0);

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
			throw new \Exception(Lang::txt('Collection not found'), 404);
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
