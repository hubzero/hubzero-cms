<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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