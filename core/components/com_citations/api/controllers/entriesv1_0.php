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
 */

namespace Components\Citations\Api\Controllers;

use Hubzero\Component\ApiController;
use stdClass;
use Request;
use Route;

$base = dirname(dirname(__DIR__)) . DS . 'tables' . DS;

require_once($base . 'citation.php');
require_once($base . 'association.php');
require_once($base . 'author.php');
require_once($base . 'secondary.php');
require_once($base . 'tags.php');
require_once($base . 'type.php');
require_once($base . 'sponsor.php');
require_once($base . 'format.php');

/**
 * API controller class for Citations
 */
class Entriesv1_0 extends ApiController
{
	/**
	 * Display a list of citations
	 *
	 * @apiMethod GET
	 * @apiUri    /citations/list
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
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "created",
	 * 		"allowedValues": "created, title, id"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$database = \App::get('db');

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'sort'       => Request::getVar('sort', 'created'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'DESC')),
			'state'      => 1
		);

		//get the earliest year we have citations for
		$query = "SELECT c.year FROM `#__citations` as c WHERE c.published=1 AND c.year <> 0 AND c.year IS NOT NULL ORDER BY c.year ASC LIMIT 1";
		$database->setQuery($query);
		$earliest_year = $database->loadResult();
		$earliest_year = ($earliest_year) ? $earliest_year : 1990;

		$filters['id']              = Request::getInt('id', 0);
		$filters['tag']             = Request::getVar('tag', '', 'request', 'none', 2);
		$filters['type']            = Request::getVar('type', '');
		$filters['author']          = Request::getVar('author', '');
		$filters['publishedin']     = Request::getVar('publishedin', '');
		$filters['year_start']      = Request::getInt('year_start', $earliest_year);
		$filters['year_end']        = Request::getInt('year_end', date("Y"));
		$filters['filter']          = Request::getVar('filter', '');
		$filters['reftype']         = Request::getVar('reftype', array('research' => 1, 'education' => 1, 'eduresearch' => 1, 'cyberinfrastructure' => 1));
		$filters['geo']             = Request::getVar('geo', array('us' => 1, 'na' => 1,'eu' => 1, 'as' => 1));
		$filters['aff']             = Request::getVar('aff', array('university' => 1, 'industry' => 1, 'government' => 1));
		$filters['startuploaddate'] = Request::getVar('startuploaddate', '0000-00-00');
		$filters['enduploaddate']   = Request::getVar('enduploaddate', '0000-00-00');

		$filters['sort'] = $filters['sort'] . ' ' . $filters['sort_Dir'];

		if ($collection = Request::getInt('collection', 0))
		{
			$filters['collection_id'] = $collection;
		}

		$response = new stdClass;
		$response->citations = array();

		// Instantiate a new citations object
		$obj = new \Components\Citations\Tables\Citation($database);

		// Get a record count
		$response->total = $obj->getCount($filters);

		// Get records
		if ($response->total)
		{
			$href = 'index.php?option=com_citations&task=view&id=';
			$base = str_replace('/api', '', rtrim(Request::base(), '/'));

			foreach ($obj->getRecords($filters) as $i => $entry)
			{
				$entry->uri = $base . '/' . ltrim(Route::url($href . $entry->id), '/');

				$response->citations[] = $entry;
			}
		}

		$response->success = true;

		$this->send($response);
	}
}
