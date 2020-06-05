<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Components\Courses\Tables\Prerequisites;
use Request;
use App;

require_once __DIR__ . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'prerequisite.php';

/**
 * API controller for the time component
 */
class Prerequisitev1_0 extends base
{
	/**
	 * Adds a new prerequisite
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/prerequisite/new
	 * @apiParameter {
	 * 		"name":        "item_scope",
	 * 		"description": "Items having prerequisites",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "asset"
	 * }
	 * @apiParameter {
	 * 		"name":        "item_id",
	 * 		"description": "Item ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "requisite_scope",
	 * 		"description": "Items that are prerequisites",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "asset"
	 * }
	 * @apiParameter {
	 * 		"name":        "requisite_id",
	 * 		"description": "Requisite ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "section_id",
	 * 		"description": "Section ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function newTask()
	{
		$db  = App::get('db');
		$tbl = new Prerequisites($db);
		$tbl->set('item_scope', Request::getWord('item_scope', 'asset'));
		$tbl->set('item_id', Request::getInt('item_id', 0));
		$tbl->set('requisite_scope', Request::getWord('requisite_scope', 'asset'));
		$tbl->set('requisite_id', Request::getInt('requisite_id', 0));
		$tbl->set('section_id', Request::getInt('section_id', 0));

		if (!$tbl->store())
		{
			App::abort(500, 'Failed to save new prerequisite');
		}
		else
		{
			$this->send(['success'=>true, 'id'=>$tbl->get('id')], 201);
		}
	}

	/**
	 * Deletes a prerequisite
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/prerequisite/delete
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Prerequisite ID",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function deleteTask()
	{
		if (!$id = Request::getInt('id', false))
		{
			App::abort(404, 'No ID provided');
		}

		$db  = App::get('db');
		$tbl = new Prerequisites($db);
		$tbl->load($id);
		$tbl->delete();

		$this->send('Item successfully deleted');
	}
}
