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
 */

namespace Components\Search\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Search\Helpers\SolrHelper;
use Component;
use stdClass;
use Request;
use Event;
use User;

/**
 * API controller class for search
 */
class Searchv1_0 extends ApiController
{
	/**
	 * Add to message queue
	 *
	 * @apiMethod POST
	 * @apiUri    /search/queue
	 * @apiParameter {
	 * 		"name":          "hubtype",
	 * 		"description":   "The type of document to index",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"allowedValues": "group,member,project,resource,publication",
	 *		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "id",
	 * 		"description":   "Comma-separated list of IDs to index",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "action",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "index",
	 *		"allowedValues": "index, delete"
	 * }
	 */
	public function queueTask()
	{
		$this->requiresAuthentication();
		$success = false;

		if (User::authorise('core.admin', 'com_search'))
		{
			$idString = \Hubzero\Utility\Sanitize::clean(Request::getVar('id', ''));
			$ids = explode(",", $idString); 
			$action = \Hubzero\Utility\Sanitize::paranoid(Request::getCmd('action', 'index'));
			$type = \Hubzero\Utility\Sanitize::paranoid(Request::getVar('hubtype', ''));
			
			if (in_array($action, array('index', 'delete')))
			{
				require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';
				if (SolrHelper::enqueueDB($type, $ids, $action))
				{
					$success = true;
				}
			}
		}

		$response = new stdClass;
		$response->success = $success;
		$this->send($response);
	}

	public function fetchQueueTask()
	{
		$this->requiresAuthentication();
		Request::getInt('limit', 100);

		if (User::authorise('core.admin', 'com_search'))
		{
			
		}
	}
}
