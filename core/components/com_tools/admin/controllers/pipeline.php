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
use Components\Resources\Models\Entry;
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
		$filters = array(
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
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
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

		$filters['sortby'] = $filters['sort'] . ' ' . $filters['sort_Dir'];

		// In case limit has been changed, adjust limitstart accordingly
		$filters['start'] = ($filters['limit'] != 0 ? (floor($filters['start'] / $filters['limit']) * $filters['limit']) : 0);

		// Get a record count
		$total = Tool::getToolCount($filters, true);

		// Get records
		$rows = Tool::getToolSummaries($filters, true);

		// Display results
		$this->view
			->set('total', $total)
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming instance ID
			$id = Request::getInt('id', 0);

			// Do we have an ID?
			if (!$id)
			{
				return $this->cancelTask();
			}

			$row = Tool::getInstance($id);
		}

		// Display results
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->setErrors($this->getErrors())
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
			Notify::warning(Lang::txt('COM_TOOLS_ERROR_MISSING_ID'));
			return $this->cancelTask();
		}

		$row = Tool::getInstance(intval($fields['id']));
		if (!$row)
		{
			Request::setVar('id', $fields['id']);

			Notify::error(Lang::txt('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
			return $this->editTask();
		}

		$row->title = trim($fields['title']);
		$row->ticketid = intval($fields['ticketid']);
		$row->state = intval($fields['state']);

		if (!$row->title)
		{
			Notify::error(Lang::txt('COM_TOOLS_ERROR_MISSING_TITLE'));
			return $this->editTask($row);
		}

		$row->update();

		Notify::success(Lang::txt('COM_TOOLS_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
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
		require_once \Component::path('com_resources') . DS . 'models' . DS . 'entry.php';
		require_once \Component::path('com_resources') . DS . 'models' . DS . 'doi.php';

		$objDOI   = \Components\Resources\Models\Doi::blank();
		$objV     = new \Components\Tools\Tables\Version($this->database);
		$objA     = new \Components\Tools\Tables\Author($this->database);

		$live_site = rtrim(Request::base(), '/');
		$sitename = Config::get('sitename');

		// Get config
		$config = \Component::params($this->_option);

		// Get all tool publications without new DOI
		$this->database->setQuery("SELECT * FROM `#__doi_mapping` WHERE doi='' OR doi IS NULL");
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
				$resource = Entry::oneOrNew($row->rid);
				if (!$resource->get('id') || !$row->alias)
				{
					continue;
				}

				// Get version info
				$this->database->setQuery("SELECT * FROM `#__tool_version` WHERE toolname=" . $this->database->quote($row->alias) . " AND revision=" . $this->database->quote($row->local_revision) . " AND state!=3 LIMIT 1");
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
				$doiSuccess = $objDOI->register($authors, $config, $metadata);
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
					$o = $objDOI->getError() . '<br />';
					foreach ($metadata as $key => $val)
					{
						$o .= $key . ': ' . $val . '<br />';
					}

					echo $o;
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
	}
}
