<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cache\Api\Controllers;

use Components\Cache\Models\Manager;
use Hubzero\Component\ApiController;
use Exception;
use stdClass;
use Request;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'manager.php';

/**
 * API controller class for cache manager
 */
class Cachev1_0 extends ApiController
{
	/**
	 * Display a list of entries
	 *
	 * @apiMethod GET
	 * @apiUri    /cache/list
	 * @apiParameter {
	 * 		"name":          "clientId",
	 * 		"description":   "Client to manage cache data for",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 *      "default":       0
	 * }
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
	 * 		"allowedValues": "group, count, size"
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

		$model = new Manager();

		$response = new stdClass;
		$response->data  = $model->data();
		$response->total = $model->total();

		$this->send($response);
	}

	/**
	 * Clean one or more cache groups
	 *
	 * @apiMethod DELETE
	 * @apiUri    /cache/clean
	 * @apiParameter {
	 * 		"name":        "group",
	 * 		"description": "Cache groups to clean",
	 * 		"type":        "string|array",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function cleanTask()
	{
		$this->requiresAuthentication();

		$ids = Request::getArray('group', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			throw new Exception(Lang::txt('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'), 500);
		}

		$model = new Manager();

		if (!$model->cleanlist($ids))
		{
			throw new Exception($model->getError(), 500);
		}

		$this->send(null, 204);
	}

	/**
	 * Purge expired data
	 *
	 * @apiMethod DELETE
	 * @apiUri    /cache/purge
	 * @return    void
	 */
	public function purgeTask()
	{
		$this->requiresAuthentication();

		$model = new Manager();

		if (!$model->purge())
		{
			throw new Exception($model->getError(), 500);
		}

		$this->send(null, 204);
	}
}
