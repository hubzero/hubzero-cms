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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
