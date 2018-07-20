<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Installer\Admin\Helpers\Cli;
use Request;
use Config;
use Route;
use App;

require_once dirname(__DIR__) . '/helpers/cli.php';

/**
 * Migrations Controller
 */
class Migrations extends AdminController
{
	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Paging
		$filters = array(
			'folder' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.folder',
				'folder',
				''
			)),
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$breadcrumb = '';
		if ($filters['folder'])
		{
			// Show that a filter is in place
			$breadcrumb = $filters['folder'];

			// If the path does not start with a slash,
			// assume relative to the ROOT path
			if (substr($filters['folder'], 0, 1) != '/')
			{
				$filters['folder'] = PATH_ROOT . DS . $filters['folder'];
			}
		}

		$rows  = array();
		$total = 0;

		$migrations = json_decode(Cli::migration(true, true, null, 'up', $filters['folder']));

		if ($migrations && count($migrations) > 0)
		{
			foreach ($migrations as $status => $files)
			{
				$files = array_reverse($files);
				foreach ($files as $entry)
				{
					$row = array(
						'entry'  => $entry,
						'status' => $status
					);
					$rows[] = $row;
				}
			}
			$total = count($rows);

			if ($total > $filters['limit'])
			{
				$rows = array_splice($rows, $filters['start'], $filters['limit']);
			}
		}

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('breadcrumb', $breadcrumb)
			->set('total', $total)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Perform up migration on single file
	 *
	 * @return  void
	 */
	public function migrateTask()
	{
		// Check authorization
		if (!User::authorise('core.manage'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Check for request forgeries
		Request::checkToken('get');

		$file = Request::getString('file', null);

		$response = Cli::migration(false, true, $file);
		$response = json_decode($response);

		Notify::success(Lang::txt('Migration complete!'));

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Run UP migrations
	 *
	 * @return  void
	 */
	public function runupTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$migrations = Request::getArray('migration', array());

		// Migrations are listed newest to oldest
		// When running UP, we need to make sure everything is run oldest to newest
		$migrations = array_reverse($migrations);

		foreach ($migrations as $migration)
		{
			$response = Cli::migration(false, true, $migration, 'up');
			$response = json_decode($response);
		}

		Notify::success(Lang::txt('Migration complete!'));

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Run DOWN migrations
	 *
	 * @return  void
	 */
	public function rundownTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$migrations = Request::getArray('migration', array());

		foreach ($migrations as $migration)
		{
			$response = Cli::migration(false, true, $migration, 'down');
			$response = json_decode($response);
		}

		Notify::success(Lang::txt('Migration complete!'));

		// Set the redirect
		$this->cancelTask();
	}
}
