<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api\Controllers;

use Components\Support\Models\Category;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Component;
use Exception;
use stdClass;
use Request;
use Config;
use Route;
use Lang;
use User;

require_once dirname(dirname(__DIR__)) . '/models/ticket.php';
require_once dirname(dirname(__DIR__)) . '/helpers/acl.php';
require_once dirname(dirname(__DIR__)) . '/helpers/utilities.php';

/**
 * API controller class for support categories
 */
class Categoriesv2_1 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->acl = \Components\Support\Helpers\ACL::getACL();
		$this->acl->setUser(User::get('id'));

		parent::execute();
	}

	/**
	 * Display ticket categories
	 *
	 * @apiMethod GET
	 * @apiUri    /support/categories/list
	 * @apiParameter {
	 * 		"name":          "created_by",
	 * 		"description":   "List categories created by a specific user (by id)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "modified_by",
	 * 		"description":   "List categories modified by a specific user (by id)",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for in the category title.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "created",
	 * 		"description":   "A timestamp (YYYY-MM-DD HH:mm:ss) for items created on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31",
	 * 		"type":          "string|integer",
	 * 		"required":      false,
	 * 		"default":       null,
	 * 		"allowedValues": "YYYY-MM or YYYY-MM-DD or YYYY-MM-DD HH:mm:ss"
	 * }
	 * @apiParameter {
	 * 		"name":          "modified",
	 * 		"description":   "A timestamp (YYYY-MM-DD HH:mm:ss) for items modified on or after the specified date. A time window can be specified adding a second timestamp, separated by a comma. Example: 2018-01-01,2018-12-31",
	 * 		"type":          "string|integer",
	 * 		"required":      false,
	 * 		"default":       null,
	 * 		"allowedValues": "YYYY-MM or YYYY-MM-DD or YYYY-MM-DD HH:mm:ss"
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
	 *      "default":       "id",
	 * 		"allowedValues": "id, created, created_by, modified, modified_by, title, alias"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "asc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return    void
	 */
	public function listTask()
	{
		$this->requiresAuthentication();

		if (!User::authorise('core.manage', $this->_option))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$rows = Category::all();

		if ($created_by = Request::getInt('created_by', 0))
		{
			$rows->whereEquals('created_by', $created_by);
		}

		if ($modified_by = Request::getInt('modified_by', 0))
		{
			$rows->whereEquals('modified_by', $modified_by);
		}

		if ($search = Request::getString('search', ''))
		{
			$rows->whereLike('title', $search);
		}

		$created = $this->toTimestamp(Request::getString('created', ''));
		if ($created)
		{
			if (is_array($created) && count($created) > 1)
			{
				$rows->where('created', '>=', $created[0], 1)
					->orWhere('created', '<', $created[1], 1)
					->resetDepth();
			}
			else
			{
				if (is_array($created))
				{
					$created = implode('', $created);
				}
				$rows->where('created', '>=', $created);
			}
		}

		$modified = $this->toTimestamp(Request::getString('modified', ''));
		if ($modified)
		{
			if (is_array($modified) && count($modified) > 1)
			{
				$rows->where('modified', '>=', $modified[0], 1)
					->orWhere('modified', '<', $modified[1], 1)
					->resetDepth();
			}
			else
			{
				if (is_array($modified))
				{
					$modified = implode('', $modified);
				}
				$rows->where('modified', '>=', $modified);
			}
		}

		$total = clone $rows;

		$sort = Request::getWord('sort', 'id');
		if (!in_array($sort, array('id', 'created', 'created_by', 'title', 'alias', 'modified', 'modified_by')))
		{
			$sort = 'id';
		}
		$sort_dir = Request::getWord('sort_Dir', 'asc');
		if (!in_array($sort_dir, array('asc', 'desc')))
		{
			$sort_dir = 'asc';
		}

		$rows = $rows->order($sort, $sort_dir)
			->limit(Request::getInt('limit', 25))
			->start(Request::getInt('start', 0))
			->rows();

		$response = new stdClass;
		$response->total = $total->total();
		$response->categories = array();
		foreach ($rows as $row)
		{
			$temp = $row->toArray();
			if ($temp['created'] && $temp['created'] != '0000-00-00 00:00:00')
			{
				$temp['created']  = with(new Date($temp['created']))->format('Y-m-d\TH:i:s\Z');
			}
			if ($temp['modified'] && $temp['modified'] != '0000-00-00 00:00:00')
			{
				$temp['modified'] = with(new Date($temp['modified']))->format('Y-m-d\TH:i:s\Z');
			}

			$response->categories[] = $temp;
		}
		$this->send($response);
	}

	/**
	 * Ensure timestamp follows accepted pattern
	 *
	 * @param   string  $val  Timestamp or two timestamps separated by a comma
	 *                        YYYY-MM or YYYY-MM-DD or YYYY-MM-DD HH:mm:ss or YYYY-MM,YYYY-MM
	 * @return  mixed   string or null if not a valid timestamp
	 */
	private function toTimestamp($val=null)
	{
		if ($val)
		{
			$val = strtolower($val);

			if (strstr($val, ','))
			{
				$vals = explode(',', $val);
				foreach ($vals as $i => $v)
				{
					$vals[$i] = $this->toTimestamp(trim($v));
				}
				return $vals;
			}

			if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $val, $regs))
			{
				// Time already matches pattern so do nothing.
			}
			else if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $val, $regs))
			{
				$val .= ' 00:00:00';
			}
			else if (preg_match("/([0-9]{4})-([0-9]{2})/", $val, $regs))
			{
				$val .= '-01 00:00:00';
			}
			else
			{
				// Not an acceptable time
				$val = null;
			}
		}

		return $val;
	}

	/**
	 * Create a new support category
	 *
	 * @apiMethod POST
	 * @apiUri    /support/categories
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Entry title",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Entry alias",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "created",
	 * 		"description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "created_by",
	 * 		"description": "User ID of entry creator",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @return     void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		if (!User::authorise('core.create', $this->_option))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$isNew = true;

		$row = Category::blank();

		$row->set('title', Request::getString('title', '', 'post'));
		$row->set('alias', Request::getString('alias', '', 'post'));
		$row->set('created', Request::getString('created', Date::of('now')->toSql(), 'post'));
		$row->set('created_by', Request::getInt('created_by', User::get('id'), 'post'));

		if (!$row->save())
		{
			throw new Exception($row->getError(), 500);
		}

		// Trigger after save event
		Event::trigger('onSupportAfterSaveCategory', array(&$row, $isNew));

		$response = $row->toObject();
		if ($response->created && $response->created != '0000-00-00 00:00:00')
		{
			$response->created = with(new Date($response->created))->format('Y-m-d\TH:i:s\Z');
		}

		$this->send($response);
	}

	/**
	 * Displays details for a support category
	 *
	 * @apiMethod GET
	 * @apiUri    /support/categories/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function readTask()
	{
		$this->requiresAuthentication();

		if (!User::authorise('core.manage', $this->_option))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		// Initiate class and bind data to database fields
		$id = Request::getInt('id', 0);

		// Initiate class and bind data to database fields
		$row = Category::oneOrFail($id);

		$response = $row->toObject();

		if ($response->created && $response->created != '0000-00-00 00:00:00')
		{
			$response->created = with(new Date($response->created))->format('Y-m-d\TH:i:s\Z');
		}

		if ($response->modified && $response->modified != '0000-00-00 00:00:00')
		{
			$response->modified = with(new Date($response->modified))->format('Y-m-d\TH:i:s\Z');
		}

		$this->send($response);
	}

	/**
	 * Update a support category
	 *
	 * @apiMethod PUT
	 * @apiUri    /support/categories/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Entry title",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "alias",
	 * 		"description": "Entry alias",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "created",
	 * 		"description": "Created timestamp (YYYY-MM-DD HH:mm:ss)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "now"
	 * }
	 * @apiParameter {
	 * 		"name":        "created_by",
	 * 		"description": "User ID of entry creator",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @return    void
	 */
	public function updateTask()
	{
		$this->requiresAuthentication();

		if (!User::authorise('core.edit', $this->_option))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$id = Request::getInt('id', 0);

		$row = Category::oneOrFail($id);

		if ($row->isNew())
		{
			throw new Exception(Lang::txt('Record not found'), 404);
		}

		$isNew = false;

		$row->set('title', Request::getString('title', $row->get('title')));
		$row->set('alias', Request::getString('alias', $row->get('alias')));
		$row->set('created', Request::getString('created', $row->get('created')));
		$row->set('created_by', Request::getInt('created_by', $row->get('created_by')));
		$row->set('modified', Request::getString('modified', Date::of('now')->toSql()));
		$row->set('modified_by', Request::getInt('modified_by', User::get('id')));

		if (!$row->save())
		{
			throw new Exception($row->getError(), 500);
		}

		// Trigger after save event
		Event::trigger('onSupportAfterSaveCategory', array(&$row, $isNew));

		$response = $row->toObject();
		if ($response->created && $response->created != '0000-00-00 00:00:00')
		{
			$response->created = with(new Date($response->created))->format('Y-m-d\TH:i:s\Z');
		}
		if ($response->modified && $response->modified != '0000-00-00 00:00:00')
		{
			$response->modified = with(new Date($response->modified))->format('Y-m-d\TH:i:s\Z');
		}

		$this->send($response);
	}

	/**
	 * Delete a support category
	 *
	 * @apiMethod DELETE
	 * @apiUri    /support/categories/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Entry identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		$this->requiresAuthentication();

		if (!User::authorise('core.delete', $this->_option))
		{
			throw new Exception(Lang::txt('Not authorized'), 403);
		}

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			throw new Exception(Lang::txt('No ID provided'), 500);
		}

		foreach ($ids as $id)
		{
			$row = Category::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				throw new Exception($row->getError(), 500);
			}

			// Trigger before delete event
			Event::trigger('onSupportAfterDeleteCategory', array($id));
		}

		$this->send(null, 204);
	}
}
