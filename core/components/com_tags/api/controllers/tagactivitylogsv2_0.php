<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tags\Api\Controllers;

$componentPath = Component::path('com_tags');

require_once "$componentPath/helpers/activityLogPresenter.php";
require_once "$componentPath/models/log.php";

use Components\Tags\Helpers\ActivityLogPresenter;
use Components\Tags\Models\Log;
use Hubzero\Component\ApiController;

class TagActivityLogsv2_0 extends ApiController
{

	protected static $version = '2.0';

	/**
	 * Retrieve tag's logs that preceeded the given log
	 *
	 * @apiMethod GET
	 * @apiUri    /tags/tagactivitylogs/previouslogs
	 * @apiParameter {
	 * 		"name":          "tagId",
	 * 		"description":   "ID of associated tag",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * }
	 * @apiParameter {
	 * 		"name":          "logId",
	 * 		"description":   "ID of log to compare against",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Limit of logs to retrieve",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       100
	 * }
	 * @return  void
	 */
	public function previousLogsTask()
	{
		$this->requiresAuthentication();

		$tagId = Request::getInt('tagId');
		$logId = Request::getInt('logId');
		$limit = Request::getInt('limit', 100);

		$response = [
			'logs' => []
		];

		if ($tagId && $logId)
		{
			$parser = new ActivityLogPresenter();

			$previousLogs = Log::all()
				->ordered()
				->whereEquals('tag_id', $tagId)
				->where('id', '<', $logId)
				->limit($limit);

			foreach ($previousLogs as $log)
			{
				$parsedLog = $parser->parse($log);

				$logArray = $log->toArray();
				$logArray['htmlClass'] = $parsedLog->class;
				$logArray['parsedDescription'] = $parsedLog->activityDescription;

				$response['logs'][] = $logArray;
			}
		}

		$this->send($response);
	}

}
