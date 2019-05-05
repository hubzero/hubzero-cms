<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Api\Controllers;

use Hubzero\Component\ApiController;
use Component;
use Exception;
use stdClass;
use Request;
use App;

/**
 * API controller class for resources
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Get a list of resources
	 *
	 * @apiMethod GET
	 * @apiUri    /content/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		// Incoming
		$filters = array(
			'limit'  => Request::getInt('limit', Config::get('list_limit')),
			'start'  => Request::getInt('limitstart', 0),
		);

		$filters['limit'] = \Hubzero\Utility\Sanitize::paranoid($filters['limit']);
		$filters['start'] = \Hubzero\Utility\Sanitize::paranoid($filters['start']);

		if (User::authorise('core.admin', 'com_content'))
		{
			$db = App::get('db');
			$totalQuery = 'SELECT COUNT(*) FROM `#__content`;';
			$db->setQuery($totalQuery);
			$total = $db->loadResult();

			$query = 'SELECT * FROM `#__content` LIMIT ' . $filters['start'] . ', ' . $filters['limit'] . ';';
			$db->setQuery($query);
			$pages = $db->loadObjectList();
			return false;

			foreach ($pages as &$page)
			{
				// Build the path
				$sql1 = "SELECT path FROM `#__categories` WHERE id={$page->catid};";
				$path = $db->setQuery($sql1)->query()->loadResult();

				if (strpos($path, 'uncategorized') === false && strpos($path, 'uncategorised') === false)
				{
					$url = $path . '/' . $page->alias;
				}
				else
				{
					$url = '/' . $page->alias;
				}

				$page->url = $url;

				if ($page->state == 1 && $page->access == 1)
				{
					$access_level = 'public';
				}
				// Registered condition
				elseif ($page->state == 1 && $page->access == 2)
				{
					$access_level = 'registered';
				}
				// Default private
				else
				{
					$access_level = 'private';
				}

				$page->access_level = $access_level;
				$page->owner_type = 'user';
				$page->owner = $page->created_by;
				$page->id = 'content-' . $page->id;
				$page->hubtype = 'content';
				$page->description = \Hubzero\Utility\Sanitize::stripAll($page->introtext);
				$page->raw_content = $page->introtext . ' ' . $page->fulltext;
			}

			$response = new stdClass;
			$response->content = $pages;
			$response->total = $total;
			$response->success = true;
			$this->send($response);
		}
		else
		{
			$response = new stdClass;
			$response->success = false;
			$this->send($response);
		}
	}
}
