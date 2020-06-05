<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Checkin\Api\Controllers;

use Components\Checkin\Models\Inspector;
use Hubzero\Component\ApiController;
use Exception;
use stdClass;
use Request;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'inspector.php';

/**
 * API controller class for checkin
 */
class Checkinv1_0 extends ApiController
{
	/**
	 * Display a list of entries
	 *
	 * @apiMethod GET
	 * @apiUri    /checkin/list
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
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "created",
	 * 		"allowedValues": "table, count"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$this->requiresAuthentication();

		$model = new Inspector();

		$response = new stdClass;
		$response->tables = $model->items();
		$response->total = $model->total();

		$this->send($response);
	}

	/**
	 * Checkin entries on a table
	 *
	 * @apiMethod DELETE
	 * @apiUri    /checkin/checkin
	 * @apiParameter {
	 * 		"name":        "table",
	 * 		"description": "Table(s) to checkin",
	 * 		"type":        "string|array",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function checkinTask()
	{
		$this->requiresAuthentication();

		$ids = Request::getArray('table', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			throw new Exception(Lang::txt('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'), 500);
		}

		$model = new Inspector();

		if (!$model->checkin($ids))
		{
			throw new Exception($model->getError(), 500);
		}

		$this->send(null, 204);
	}
}
