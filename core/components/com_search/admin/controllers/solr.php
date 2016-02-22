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
use \Hubzero\Search\Search;
use \Hubzero\Search\SearchDocument;
use Filesystem;
use \Components\Search\Models\Noindex;
use stdClass;

/**
 * Solr AdminController Class
 */
class Solr extends AdminController
{
	/**
	 * adapterClass - the class path for the adapter used for this controller
	 *
	 * @var string
	 * @access protected
	 */
	protected $adapterClass = 'Solr';

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
		$search = new Search($this->adapterClass);

		$this->view->logs = array_slice($search->getLog(), -10, 10, true);

		$insertTime = Date::of($search->lastInsert())->format('relative');

		$this->view->status = $search->status();
		$this->view->lastInsert = $insertTime;
		$this->view->setLayout('overview');

		// Display the view
		$this->view->display();
	}

	public function searchIndexTask()
	{
		// Display CMS errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$search = new Search;
		$searchDocument = new SearchDocument;
		// Get all of the registered hubtypes
		$this->view->types = $searchDocument->getHubTypes();

		// Count indexed documents
		foreach ($this->view->types as &$type)
		{
			// Search across everything
			$queryString = '*:*';

			// Perform an overall search
			$search = $search->setQuery($queryString)->addFacet('count', 'hubtype:'.$type['hubtype']);

			// Get the result, but don't chain it back
			$result = $search->runQuery();

			// Get facet count
			$type['docCount'] = $search->getFacetCount('count');
		}

		// Display the view
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
		$hubSearch = new SearchEngine();
		$hubSearch->getLog();
		echo "<pre>";
		print_r($hubSearch->logs);
		exit();
	}
}
