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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Models\Tool;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

/**
 * Tools controller class
 */
class Pipeline extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display entries in the pipeline
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->filters = array(
			// Get filters
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'search_field' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search_field',
				'search_field',
				'all'
			)),
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'toolname'
			),
			'sort_Dir' => strtoupper(Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)),
			// Get paging variables
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

		$this->view->filters['sortby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get a record count
		$this->view->total = Tool::getToolCount($this->view->filters, true);

		// Get records
		$this->view->rows = Tool::getToolSummaries($this->view->filters, true);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Edit an entry
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming instance ID
		$id = Request::getInt('id', 0);

		// Do we have an ID?
		if (!$id)
		{
			return $this->cancelTask();
		}

		if (!is_object($row))
		{
			$row = Tool::getInstance($id);
		}

		$this->view->row = $row;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		// Incoming instance ID
		$fields = Request::getVar('fields', array(), 'post');

		// Do we have an ID?
		if (!$fields['id'])
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_TOOLS_ERROR_MISSING_ID'),
				'error'
			);
			return;
		}

		$row = Tool::getInstance(intval($fields['id']));
		if (!$row)
		{
			Request::setVar('id', $fields['id']);

			Notify::error(Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			return $this->editTask();
		}

		$row->title = trim($fields['title']);

		if (!$row->title)
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_TITLE'), 'error');
			return $this->editTask($row);
		}

		$row->update();

		if ($this->getTask() == 'apply')
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $fields['id'], false)
			);
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_ITEM_SAVED')
		);
	}

	/**
	 * Temp function to issue new service DOIs for tool versions published previously
	 *
	 * @return  void
	 */
	public function batchdoiTask()
	{
		$yearFormat = 'Y';

		//  Limit one-time batch size
		$limit = Request::getInt('limit', 2);

		// Store output
		$created = array();
		$failed = array();

		// Initiate extended database classes
		$resource = new \Components\Resources\Tables\Resource($this->database);
		$objDOI   = new \Components\Resources\Tables\Doi($this->database);
		$objV     = new \Components\Tools\Tables\Version($this->database);
		$objA     = new \Components\Tools\Tables\Author($this->database);

		$live_site = rtrim(Request::base(),'/');
		$sitename = Config::get('sitename');

		// Get config
		$config = \Component::params($this->_option);

		// Get all tool publications without new DOI
		$this->database->setQuery("SELECT * FROM `#__doi_mapping` WHERE doi='' OR doi IS NULL ");
		$rows = $this->database->loadObjectList();

		if ($rows)
		{
			$i = 0;
			foreach ($rows as $row)
			{
				if ($limit && $i == $limit)
				{
					// Output status message
					if ($created)
					{
						foreach ($created as $cr)
						{
							echo '<p>'.$cr.'</p>';
						}
					}
					echo '<p>' . Lang::txt('COM_TOOLS_REGISTERED_DOIS', count($created), count($failed)) . '</p>';
					return;
				}

				// Skip entries with no resource information loaded / non-tool resources
				if (!$resource->load($row->rid) || !$row->alias)
				{
					continue;
				}

				// Get version info
				$this->database->setQuery("SELECT * FROM `#__tool_version` WHERE toolname='" . $row->alias . "' AND revision='" . $row->local_revision . "' AND state!=3 LIMIT 1");
				$results = $this->database->loadObjectList();

				if ($results)
				{
					$title   = $results[0]->title ? $results[0]->title : $resource->title;
					$pubyear = $results[0]->released ? trim(Date::of($results[0]->released)->toLocal($yearFormat)) : date('Y');
				}
				else
				{
					// Skip if version not found
					continue;
				}

				// Collect metadata
				$metadata = array();
				$metadata['targetURL'] = $live_site . '/resources/' . $row->rid . '/?rev=' . $row->local_revision;
				$metadata['title']     = htmlspecialchars($title);
				$metadata['pubYear']   = $pubyear;

				// Get authors
				$objA = new \Components\Tools\Tables\Author($this->database);
				$authors = $objA->getAuthorsDOI($row->rid);

				// Register DOI
				$doiSuccess = $objDOI->registerDOI($authors, $config, $metadata, $doierr);
				if ($doiSuccess)
				{
					$this->database->setQuery("UPDATE `#__doi_mapping` SET doi='$doiSuccess' WHERE rid=$row->rid AND local_revision=$row->local_revision");
					if (!$this->database->query())
					{
						$failed[] = $doiSuccess;
					}
					else
					{
						$created[] = $doiSuccess;
					}
				}
				else
				{
					print_r($doierr);
					echo '<br />';
					print_r($metadata);
					echo '<br />';
				}

				$i++;
			}
		}

		// Output status message
		if ($created)
		{
			foreach ($created as $cr)
			{
				echo '<p>' . $cr . '</p>';
			}
		}
		echo '<p>' . Lang::txt('COM_TOOLS_REGISTERED_DOIS', count($created), count($failed)) . '</p>';
		return;
	}

	/**
	 * Temp function to ensure #__doi_mapping table is updated
	 *
	 * @return  boolean
	 */
	public function setupdoiTask()
	{
		$fields = $this->database->getTableFields(Config::get('dbprefix') . 'doi_mapping');

		if (!array_key_exists('versionid', $fields[Config::get('dbprefix') . 'doi_mapping']))
		{
			$this->database->setQuery("ALTER TABLE `#__doi_mapping` ADD `versionid` int(11) default '0'");
			if (!$this->database->query())
			{
				echo $this->database->getErrorMsg();
				return false;
			}
		}
		if (!array_key_exists('doi', $fields[Config::get('dbprefix') . 'doi_mapping']))
		{
			$this->database->setQuery("ALTER TABLE `#__doi_mapping` ADD `doi` varchar(50) default NULL");
			if (!$this->database->query())
			{
				echo $this->database->getErrorMsg();
				return false;
			}
		}
		return true;
	}
}
