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

namespace Components\Time\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Time\Models\Record;
use Components\Time\Models\Permissions;
use App;
use Request;

require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'record.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'permissions.php';

/**
 * API records controller for the time component
 */
class Recordsv1_0 extends ApiController
{
	/**
	 * Deletes a time record
	 *
	 * @apiMethod DELETE
	 * @apiUri    /api/time/records/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Record id",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function deleteTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();

		// Create object and store content
		$record = Record::oneOrFail(Request::getInt('id'));

		if (!$record->isMine())
		{
			App::abort(401, 'Unauthorized');
		}

		// Do the actual save
		if (!$record->destroy())
		{
			App::abort(500, 'Record deletion failed');
		}

		// Return message
		$this->send('Record successfully deleted', 200);
	}

	/**
	 * Checks to ensure appropriate authorization
	 *
	 * @return  bool
	 * @throws  Exception
	 */
	private function authorizeOrFail()
	{
		$permissions = new Permissions('com_time');

		// Make sure action can be performed
		if (!$permissions->can('api'))
		{
			App::abort(401, 'Unauthorized');
		}

		return true;
	}
}