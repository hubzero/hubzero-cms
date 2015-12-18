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
use Request;
use Config;
use Route;
use App;

/**
 * Update controller class
 */
class Database extends AdminController
{
	/**
	 * Display the database migration log
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

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

		$this->view->rows  = array();
		$this->view->total = 0;
		$migrations = json_decode(Cli::migration(true, true));
		if ($migrations && count($migrations) > 0)
		{
			foreach ($migrations as $status => $files)
			{
				$files = array_reverse($files);
				foreach ($files as $entry)
				{
					$row = array('entry'=>$entry, 'status'=>$status);
					$this->view->rows[] = $row;
				}
			}
			$this->view->total = count($this->view->rows);
			$this->view->rows  = array_splice($this->view->rows, $this->view->filters['start'], $this->view->filters['limit']);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Perform rollback
	 *
	 * @return  void
	 */
	public function migrateTask()
	{
		$file     = Request::getVar('file', null);
		$response = Cli::migration(false, true, $file);
		$response = json_decode($response);
		$message  = 'Migration complete!';
		$type     = 'success';

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message,
			$type
		);
	}
}