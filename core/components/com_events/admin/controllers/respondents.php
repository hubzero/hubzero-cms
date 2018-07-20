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

namespace Components\Events\Admin\Controllers;

use Components\Events\Models\Orm\Respondent;
use Components\Events\Models\Orm\Event;
use Components\Events\Helpers\Csv;
use Hubzero\Component\AdminController;
use Exception;
use Request;
use Notify;
use Route;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . '/models/orm/event.php';

/**
 * Events controller class for respondents
 */
class Respondents extends AdminController
{
	/**
	 * Display a list of respondents
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Incoming
		$ids = Request::getArray('id', array(0));
		$id = $ids[0];

		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false)
			);
			return;
		}

		// Get filters
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'registered'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$event = Event::oneOrFail($id);

		$rows = $event->respondents()
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('event', $event)
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * View respondent details
	 *
	 * @return  void
	 */
	public function respondentTask()
	{
		$event = Event::oneOrFail(Request::getInt('event_id', 0));

		$resp = Respondent::oneOrFail(Request::getInt('id', 0));

		// Output the HTML
		$this->view
			->set('event', $event)
			->set('resp', $respondent)
			->display();
	}

	/**
	 * Download a list of respondents
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		$filters = array(
			'event_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.event_id',
				'id',
				array()
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'registered'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$query = Respondent::all();

		if ($filters['search'])
		{
			$query->whereLike('first_name', strtolower((string)$filters['search']), 1)
				->orWhereLike('last_name', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if ($filters['event_id'])
		{
			if (!is_array($filters['event_id']))
			{
				$filters['event_id'] = array($filters['event_id']);
			}
			if (!empty($filters['event_id']))
			{
				$query->whereIn('event_id', $filters['event_id']);
			}
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		Csv::downloadlist($this->getRespondents(), $this->_option);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('rid', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		foreach ($ids as $id)
		{
			$entry = Respondent::oneOrFail(intval($id));

			// Delete the entry
			if (!$entry->destroy())
			{
				Notify::error($entry->getError());
				continue;
			}

			// Trigger before delete event
			\Event::trigger('onEventsAfterDeleteRespondent', array($id));

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_EVENTS_RESPONDENT_REMOVED'));
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id[]=' . Request::getInt('event', 0), false)
		);
	}
}
