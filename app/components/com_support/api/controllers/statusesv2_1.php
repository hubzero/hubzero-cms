<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api\Controllers;

use Components\Support\Models\Status;
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
 * API controller class for support statuses
 */
class Statusesv2_1 extends ApiController
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
	 * Display ticket statuses
	 *
	 * @apiMethod GET
	 * @apiUri    /support/statuses/list
	 * @apiParameter {
	 * 		"name":          "open",
	 * 		"description":   "List statuses by open (1) or closed (0) state",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       null,
	 * 		"allowedValues": "0, 1"
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
	 * 		"description":   "A word or phrase to search for in the title.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
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

		$rows = Status::all();

		$open = Request::getInt('open', -1);

		if ($open >= 0)
		{
			$rows->whereEquals('open', $open);
		}

		if ($search = Request::getString('search', ''))
		{
			$rows->whereLike('title', $search);
		}

		$total = clone $rows;

		$sort = Request::getWord('sort', 'id');
		if (!in_array($sort, array('id', 'open', 'title', 'alias')))
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
		$response->statuses = array();
		foreach ($rows as $row)
		{
			$response->statuses[] = $row->toArray();
		}
		$this->send($response);
	}

	/**
	 * Create a new support status
	 *
	 * @apiMethod POST
	 * @apiUri    /support/statuses
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
	 * 		"name":        "color",
	 * 		"description": "Hexcadecimal color code (ex: #AA3300)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "open",
	 * 		"description": "The associated Open (1) or Closed (0) state",
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

		$row = Status::blank();

		$row->set('title', Request::getString('title', '', 'post'));
		$row->set('alias', Request::getString('alias', '', 'post'));
		$row->set('color', Request::getString('color', '', 'post'));
		$row->set('status', Request::getInt('status', 0, 'post'));

		if (!$row->save())
		{
			throw new Exception($row->getError(), 500);
		}

		// Trigger after save event
		Event::trigger('onSupportAfterSaveStatus', array(&$row, $isNew));

		$response = $row->toObject();

		$this->send($response);
	}

	/**
	 * Displays details for a support status
	 *
	 * @apiMethod GET
	 * @apiUri    /support/statuses/{id}
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
		$row = Status::oneOrFail($id);

		$response = $row->toObject();

		$this->send($response);
	}

	/**
	 * Update a support status
	 *
	 * @apiMethod PUT
	 * @apiUri    /support/statuses/{id}
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
	 * 		"required":    false,
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
	 * 		"name":        "color",
	 * 		"description": "Hexcadecimal color code (ex: #AA3300)",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "open",
	 * 		"description": "The associated Open (1) or Closed (0) state",
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

		$row = Status::oneOrFail($id);

		if ($row->isNew())
		{
			throw new Exception(Lang::txt('Record not found'), 404);
		}

		$isNew = false;

		$row->set('title', Request::getString('title', $row->get('title')));
		$row->set('alias', Request::getString('alias', $row->get('alias')));
		$row->set('color', Request::getString('color', $row->get('color')));
		$row->set('open', Request::getInt('open', $row->get('open')));

		if (!$row->save())
		{
			throw new Exception($row->getError(), 500);
		}

		// Trigger after save event
		Event::trigger('onSupportAfterSaveStatus', array(&$row, $isNew));

		$response = $row->toObject();

		$this->send($response);
	}

	/**
	 * Delete a support status
	 *
	 * @apiMethod DELETE
	 * @apiUri    /support/statuses/{id}
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
			$row = Status::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				throw new Exception($row->getError(), 500);
			}

			// Trigger before delete event
			Event::trigger('onSupportAfterDeleteStatus', array($id));
		}

		$this->send(null, 204);
	}
}
