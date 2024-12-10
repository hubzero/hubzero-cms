<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Members\Models\Member;
use Component;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;
use User;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'member.php';

/**
 * Mentions API controller class
 */
class Mentionsv1_0 extends ApiController
{
	/**
	 * Display a list of members used for mentions in text area
	 *
	 * @apiMethod GET
	 * @apiUri    /api/members/mentions/list
	 * @return  void
	 */
	public function listTask() {
		$this->requiresAuthentication();

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getString('search', ''),
			'sortby'     => Request::getWord('sort', 'name'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'DESC')),
			'activation' => 1,
			'access'     => User::getAuthorisedViewLevels()
		);

		// Build query
		$entries = Member::all()
			->whereEquals('block', 0)
			->whereEquals('activation', 1)
			->where('approved', '>', 0);

		if ($filters['search']) {
			$entries->whereLike('name', strtolower((string)$filters['search']), 1)
				->orWhereLike('username', strtolower((string)$filters['search']), 1)
				->orWhereLike('email', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if (!empty($filters['access'])) {
			$entries->whereIn('access', $filters['access']);
		}

		switch ($filters['sortby']) {
			case 'id':
				$filters['sort'] = 'id';
				$filters['sort_Dir'] = 'asc';
			break;

			case 'name':
			default:
				$filters['sort'] = 'surname';
				$filters['sort_Dir'] = 'asc';
			break;
		}

		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$response = array();

        foreach ($rows as $entry) {
            $obj = new stdClass;
            $obj->id        = $entry->get('id');
            $obj->picture   = $entry->picture();
            $obj->username  = $entry->get('username');
            $obj->name      = $entry->get('name');
            $obj->email     = $entry->get('email');

            $response[] = $obj;
        }

		$this->send($response);
	}
}
