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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
class Entriesv1_1 extends ApiController
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

		$admin = false;
		if (User::authorise('core.admin', 'com_citations'))
		{
			$admin = true;
			$searchable = Request::getBool('searchable', false);
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

				if (!isset($searchable) && $admin == false)
				{
					$response->citations[] = array_filter($entry->getAttributes());
				}
				elseif (isset($searchable) && $admin == true)
				{
					$citation = new stdClass;
					$citation->title = $entry->title;
					$citation->hubtype = 'citation';
					$citation->id = 'citation-' . $entry->id;
					$citation->description = $entry->abstract;
					$citation->doi = $entry->doi;

					$tags = explode(',', $entry->keywords);
					foreach ($tags as $key => &$tag)
					{
						$tag = \Hubzero\Utility\Sanitize::stripAll($tag);
						if ($tag == '')
						{
							unset($tags[$key]);
						}
					}
					$citation->tags = $tags;

					$citation->author = explode(';', $entry->author);

					if ($entry->published == 1)
					{
						$citation->access_level = 'public';
					}
					else
					{
						$citation->access_level = 'private';
					}

					if ($entry->scope == 'member')
					{
						$citation->owner_type = 'user';
						$citation->owner = $entry->uid;
						$citation->url = '/members/' . $entry->uid . '/citations';
					}
					elseif ($entry->scope == 'group')
					{
						$citation->owner_type = 'group';
						$citation->owner = $entry->scope_id;
						$group = \Hubzero\User\Group::getInstance($entry->scope_id);
						if ($group)
						{
							$group = $group->get('cn');
							$citation->url = '/groups/' . $group . '/citations';
						}
						else
						{
							$citation->url = '/citations/' . $entry->id;
						}
					}
					else
					{
						$citation->owner_type = 'user';
						$citation->owner = $entry->uid;
						$citation->url = '/citations/view/' . $entry->id;
					}
					$response->citations[] = $citation;
				}
			}
		}
		$response->success = true;
		$this->send($response);
	}
}
