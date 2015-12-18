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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Update\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Update\Helpers\Cli;
use Component;
use Request;
use Config;
use Route;
use Event;
use App;

/**
 * Update repository controller class
 */
class Repository extends AdminController
{
	/**
	 * Display the repository details
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->total   = 0;
		$this->view->filters = array();

		// Paging
		$this->view->filters['limit'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$this->view->filters['start'] = Request::getState(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['status'] = trim(Request::getState(
			$this->_option . '.' . $this->_controller . '.status',
			'status',
			'upcoming'
		));
		$this->view->filters['search'] = trim(Request::getState(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		));

		$upcoming  = false;
		$installed = true;
		if ($this->view->filters['status'] == 'upcoming' || $this->view->filters['status'] == 'all')
		{
			$upcoming = true;

			if ($this->view->filters['status'] == 'upcoming')
			{
				$installed = false;
			}
		}

		$source = Component::params('com_update')->get('git_repository_source', null);

		$this->view->rows = json_decode(
			Cli::log(
				$this->view->filters['limit'],
				$this->view->filters['start'],
				$this->view->filters['search'],
				$upcoming,
				$installed,
				false,
				$source
			)
		);
		$this->view->total = json_decode(
			Cli::log(
				$this->view->filters['limit'],
				$this->view->filters['start'],
				$this->view->filters['search'],
				$upcoming,
				$installed,
				true,
				$source
			)
		);
		$this->view->total = $this->view->total[0];

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Perform update
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		$env         = Config::get('application_env', 'production');
		$source      = Component::params('com_update')->get('git_repository_source', null);
		$autoPushRef = Component::params('com_update')->get('git_auto_push_ref', null);
		$allowNonFf  = ($env == 'production') ? false : true;

		// Trigger before update event
		Event::trigger('update.onBeforeRepositoryUpdate');

		// Do the actual update
		$response = Cli::update(false, $allowNonFf, $source, $autoPushRef);
		$response = json_decode($response);
		$response = $response[0];
		$message  = 'Update complete!';
		$type     = 'success';

		if (!empty($response) && stripos($response, 'fix conflicts and then commit the result') === false)
		{
			$type    = 'error';
			$message = ucfirst($response);
		}
		else
		{
			// Also check status again to make sure it's clean (merge conflicts will show up here)
			$status = json_decode(Cli::status());

			if (!empty($status))
			{
				foreach ($status as $type => $files)
				{
					// If anything is left over besides untracked files, something went wrong
					if ($type != 'untracked' && !empty($files))
					{
						$type    = 'error';
						$message = 'Update failed. Rolling back changes.';
						$this->rollbackTask($message, $type);
						break;
					}
				}
			}
		}

		// If success, trigger after update
		if ($type == 'success')
		{
			Event::trigger('update.onAfterRepositoryUpdate');
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message,
			$type
		);
	}

	/**
	 * Perform rollback
	 *
	 * @param   string  $message  The message to display upon rollback completion
	 * @param   string  $type     The message type to use
	 * @return  void
	 */
	public function rollbackTask($message = 'Rollback complete!', $type = 'success')
	{
		$response = Cli::rollback();
		$response = json_decode($response);
		$response = $response[0];

		if (!empty($response))
		{
			$type    = 'error';
			$message = ucfirst($response);
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message,
			$type
		);
	}
}