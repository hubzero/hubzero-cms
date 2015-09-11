<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 */

namespace Components\Resources\Api\Controllers;

use Hubzero\Component\ApiController;
use Component;
use stdClass;
use Request;

/**
 * API controller class for resources
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Get a list of new content for a given time period
	 *
	 * @apiMethod GET
	 * @apiUri    /newsletters/list
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
	 * 		"name":          "period",
	 * 		"description":   "Time period.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "month"
	 * }
	 * @apiParameter {
	 * 		"name":          "category",
	 * 		"description":   "Type of resource to filter results.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "resources"
	 * }
	 * @return    void
	 */
	public function whatsnewTask()
	{
		$limit    = Request::getVar('limit', 25);
		$period   = Request::getVar('period', 'month');
		$category = Request::getVar('category', 'resources');

		require_once(Component::path('com_whatsnew') . DS . 'helpers' . DS . 'finder.php');

		$whatsnew = \Components\Whatsnew\Helpers\Finder::getBasedOnPeriodAndCategory($period, $category, $limit);

		// encode results and return response
		$object = new stdClass();
		$object->whatsnew = $whatsnew;

		$this->send($object);
	}
}
