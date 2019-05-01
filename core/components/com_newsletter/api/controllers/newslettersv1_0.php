<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Api\Controllers;

use Components\Newsletter\Models\Newsletter;
use Hubzero\Component\ApiController;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'newsletter.php';

/**
 * API controller class for newsletters
 */
class Newslettersv1_0 extends ApiController
{
	/**
	 * Return data for the current newsletter
	 *
	 * @apiMethod GET
	 * @apiUri    /newsletters/current
	 * @return    void
	 */
	public function currentTask()
	{
		// get the current newsletter
		$newsletter = Newsletter::current();

		// build the newsletter based on campaign
		$result = array();
		$result['id']      = $newsletter->issue;
		$result['title']   = $newsletter->name;
		$result['content'] = $newsletter->buildNewsletter($newsletter);

		$obj = new stdClass();
		$obj->newsletter = $result;

		$this->send($obj);
	}

	/**
	 * Return data for newsletters
	 *
	 * @apiMethod GET
	 * @apiUri    /newsletters/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       5
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$limit = Request::getInt('limit', 5);
		$start = Request::getInt('start', 0);

		$newsletters = Newsletter::all()
			->ordered()
			->limit($limit)
			->start($start)
			->rows()
			->toArray();

		$obj = new stdClass();
		$obj->newsletters = $newsletters;

		$this->send($obj);
	}

	/**
	 * Return data for past newsletters
	 *
	 * @apiMethod GET
	 * @apiUri    /newsletters/archive
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       5
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return    void
	 */
	public function archiveTask()
	{
		$limit = Request::getInt('limit', 5);
		$start = Request::getInt('start', 0);

		// get newsletters
		$newsletters = Newsletter::all()
			->ordered()
			->limit($limit)
			->start($start)
			->rows();

		$result = array();

		// add newsletter details to return array
		$k = 0;
		foreach ($newsletters as $newsletter)
		{
			$result[$k]['id']      = $newsletter->issue;
			$result[$k]['title']   = $newsletter->name;
			$result[$k]['content'] = $newsletter->buildNewsletter($newsletter);

			$k++;
		}

		$obj = new stdClass();
		$obj->newsletters = $result;

		$this->send($obj);
	}
}
