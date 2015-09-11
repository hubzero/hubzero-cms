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
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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