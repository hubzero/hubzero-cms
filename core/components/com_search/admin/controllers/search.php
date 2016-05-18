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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Search\Models\NoIndex;
use \Hubzero\Search\Query;
use stdClass;

/**
 * Search AdminController Class
 */
class Search extends AdminController
{
	/**
	 * Display the overview
	 */
	public function displayTask()
	{
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Instantiate Search class
		$config = Component::params('com_search');
		$index = new \Hubzero\Search\Index($config);

		// Get the last 10 entries
		$this->view->logs = array_slice($index->getLogs(), -10, 10, true);

		// Get the last insert date
		$insertTime = Date::of($index->lastInsert())->format('relative');

		$this->view->status = $index->status();
		$this->view->mechanism = $config->get('engine');
		$this->view->lastInsert = $insertTime;
		$this->view->setLayout('overview');

		// Display the view
		$this->view->display();
	}

	/**
	 * searchIndexTask - displays all indexed hubtypes and the number of records
	 * 
	 * @access public
	 * @return void
	 */
	public function searchIndexTask()
	{
		// Display CMS errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$config = Component::params('com_search');
		$hubtypes = Event::trigger('search.onGetTypes');

		$stats = array();
		foreach ($hubtypes as $type)
		{
			$query = new \Hubzero\Search\Query($config);
			$result = $query->query('*:*')->addFilter('hubtype', array('hubtype', '=', $type))->run()->getNumFound();

			$stats[$type] = $result;
		}

		// Display the view
		$this->view->types = $hubtypes;
		$this->view->stats = $stats;
		$this->view->display();
	}

	/**
	 * documentByTypeTask - view a type's records
	 * 
	 * @access public
	 * @return void
	 */
	public function documentByTypeTask()
	{
		$type = Request::getVar('type', '');
		$filter = Request::getVar('filter', '*:*');
		$limitstart = Request::getInt('limitstart', 0);
		$limit = Request::getInt('limit', 10);

		// Display CMS errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$config = Component::params('com_search');
		$query = new \Hubzero\Search\Query($config);
		$results = $query->query($filter)->addFilter('hubtype', array('hubtype', '=', $type))
			->limit($limit)->start($limitstart)->run()->getResults();

		$total = $query->getNumFound();

		$this->view->type = $type;

		$pagination = new \Hubzero\Pagination\Paginator($total, $limitstart, $limit);
		$pagination->setLimits(array('5','10','15','20','50','100','200'));
		$this->view->pagination = $pagination;


		$this->view->filter = isset($filter) ? $filter : '';
		$this->view->documents = isset($results) ? $results : array();
		$this->view->display();

	}

	public function manageBlacklistTask()
	{
		$this->view->blacklist = NoIndex::all()->rows();
		$this->view->display();
	}
	public function removeBlacklistEntryTask()
	{
		$entryID = Request::getInt('entryID', 0);
		$entry = NoIndex::one($entryID);
		$entry->destroy();
		App::redirect(
		Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=manageBlacklist', false), 'Successfully removed entry #' . $entryID, 'success');
	}
	public function viewLogsTask()
	{
		// Instantiate Search class
		$config = Component::params('com_search');
		$index = new \Hubzero\Search\Index($config);

		echo "<pre>";
		print_r($index->getLogs());
		exit();
	}
}
