<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Citations\Models\Citation;
use stdClass;
use Request;
use Route;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'citation.php';

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
		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getString('search', ''),
			'sort'       => Request::getString('sort', 'created'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'DESC')),
			'state'      => 1
		);

		//get the earliest year we have citations for
		$earliest_year = Citation::all()
			->where('year', '!=', '')
			->where('year', 'IS NOT', null)
			->where('year', '!=', 0)
			->order('year', 'asc')
			->limit(1)
			->row()
			->get('year');
		$earliest_year = !empty($earliest_year) ? $earliest_year : 1990;

		$filters['id']              = Request::getInt('id', 0);
		$filters['tag']             = Request::getString('tag', '', 'request', 'none', 2);
		$filters['type']            = Request::getString('type', '');
		$filters['author']          = Request::getString('author', '');
		$filters['publishedin']     = Request::getString('publishedin', '');
		$filters['year_start']      = Request::getInt('year_start', $earliest_year);
		$filters['year_end']        = Request::getInt('year_end', date("Y"));
		$filters['filter']          = Request::getString('filter', '');
		$filters['reftype']         = Request::getArray('reftype', array('research' => 1, 'education' => 1, 'eduresearch' => 1, 'cyberinfrastructure' => 1));
		$filters['geo']             = Request::getArray('geo', array('us' => 1, 'na' => 1,'eu' => 1, 'as' => 1));
		$filters['aff']             = Request::getArray('aff', array('university' => 1, 'industry' => 1, 'government' => 1));
		$filters['startuploaddate'] = Request::getString('startuploaddate', '0000-00-00');
		$filters['enduploaddate']   = Request::getString('enduploaddate', '0000-00-00');
		$filters['scope']           = 'all';

		$filters['sort'] = $filters['sort'] . ' ' . $filters['sort_Dir'];

		if ($collection = Request::getInt('collection', 0))
		{
			$filters['collection_id'] = $collection;
		}

		$response = new stdClass;
		$response->citations = array();

		$query = Citation::getFilteredRecords($filters);
		$total = clone $query;

		// Get a record count
		$response->total = $total->total();

		// Get records
		if ($response->total)
		{
			$query->limit($filters['limit'])
				->start($filters['start']);

			$href = 'index.php?option=com_citations&task=view&id=';
			$base = str_replace('/api', '', rtrim(Request::base(), '/'));

			foreach ($query->rows() as $entry)
			{
				$entry->set('uri', $base . '/' . ltrim(Route::url($href . $entry->id), '/'));

				$response->citations[] = array_filter($entry->getAttributes());
			}
		}

		$response->success = true;

		$this->send($response);
	}
}
