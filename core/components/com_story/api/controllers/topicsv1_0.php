<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Story\Models\Topic;
use stdClass;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'manager.php';

/**
 * API controller for topics
 *
 * What a client needs to offer a submitter a sensible list to file under.
 */
class Topicsv1_0 extends ApiController
{
	/**
	 * The topics in use
	 *
	 * @apiMethod GET
	 * @apiUri    /story/topics/list
	 * @return  void
	 */
	public function listTask()
	{
		$response = new stdClass;
		$response->topics = array();

		$rows = Topic::all()
			->whereEquals('state', 1)
			->order('ordering', 'asc')
			->rows();

		foreach ($rows as $row)
		{
			$out = new stdClass;
			$out->id          = (int) $row->get('id');
			$out->title       = $row->get('title');
			$out->alias       = $row->get('alias');
			$out->description = $row->get('description');
			$out->submittable = (bool) $row->get('submittable');

			$response->topics[] = $out;
		}

		$response->total = count($response->topics);

		$this->send($response);
	}
}
