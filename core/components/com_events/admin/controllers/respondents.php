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

use Components\Events\Tables\Respondent;
use Components\Events\Tables\Event;
use Components\Events\Helpers\Csv;
use Hubzero\Component\AdminController;
use Exception;
use Request;
use Config;
use Route;
use Lang;
use App;

/**
 * Events controller class for respondents
 */
class Respondents extends AdminController
{
	/**
	 * View respondent details
	 *
	 * @return     void
	 */
	public function respondentTask()
	{
		$this->view->resp = new Respondent(array(
			'respondent_id' => Request::getInt('id', 0)
		));

		// Incoming
		$id = Request::getInt('event_id', 0);

		$this->view->event = new Event($this->database);
		$this->view->event->load($id);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display a list of respondents
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->resp = $this->getRespondents();

		// Incoming
		$ids = Request::getVar('id', array(0));
		$id = $ids[0];

		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false)
			);
			return;
		}

		$this->view->event = new Event($this->database);
		$this->view->event->load($id);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Get respondents for an event
	 *
	 * @return     object
	 */
	private function getRespondents()
	{
		$sorting = Request::getVar('sortby', 'registered DESC');
		$filters = array(
			'search' => urldecode(Request::getString('search')),
			'id'     => Request::getVar('id', array()),
			'sortby' => $sorting == 'registerby DESC' ? 'registered DESC' : $sorting,
			'limit'  => Request::getState($this->_option . '.limit', 'limit', Config::get('list_limit'), 'int'),
			'offset' => Request::getInt('limitstart', 0)
		);
		if (!$filters['limit'])
		{
			$filters['limit'] = 30;
		}
		return new Respondent($filters);
	}

	/**
	 * Download a list of respondents
	 *
	 * @return     void
	 */
	public function downloadTask()
	{
		Csv::downloadlist($this->getRespondents(), $this->_option);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$workshop = Request::getInt('workshop', 0);
		$ids = Request::getVar('rid', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			$r = new Respondent(array());

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				// Remove the profile
				$r->delete($id);
			}
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&id[]=' . $workshop, false),
			Lang::txt('COM_EVENTS_RESPONDENT_REMOVED')
		);
	}
}

