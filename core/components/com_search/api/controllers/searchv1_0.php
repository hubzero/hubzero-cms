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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 *
 */

namespace Components\Search\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Search\Models\Solr\Blacklist;
use Component;
use Exception;
use stdClass;
use Request;
use App;

/**
 * API controller class for Solr search
 */
class Searchv1_0 extends ApiController
{
	/**
	 * Returns a list of deleted search entries 
	 *
	 * @apiMethod GET
	 * @return    string JSON-encoded list of blacklisted document IDs 
	 */
	public function blacklistTask()
	{
		$response = new stdClass;
		if (User::authorise('core.admin', 'com_search'))
		{
			require_once Component::path('com_search') . '/models/solr/blacklist.php';
			$model = Blacklist::all()->select('doc_id')->rows()->toObject();
			$blacklist = array();
			foreach ($model as $row)
			{
				array_push($blacklist, $row->doc_id);
			}

			$response->blacklist = $blacklist;
		}

		$this->send($response);
	}
}
