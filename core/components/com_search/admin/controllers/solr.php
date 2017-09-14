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
use Components\Search\Models\Solr\Blacklist;
use Components\Search\Models\Solr\Facet;
use \Hubzero\Search\Query;
use Components\Search\Helpers\SolrHelper;
use Components\Developer\Models\Application;
use Hubzero\Access\Group as Accessgroup;
use stdClass;

require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'blacklist.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'facet.php';
require_once Component::path('com_developer') . DS . 'models' . DS . 'application.php';

/**
 * Search AdminController Class
 */
class Solr extends AdminController
{
	public function execute()
	{
		$solrUser = User::oneByUsername('hubzerosolrworker')->get('id');
		if (file_exists(PATH_APP . '/config/solr.json') && $solrUser >= 0)
		{
			parent::execute();
		}
		else
		{
			$this->configure();
		}
	}

	/**
	 * configure - Adds solr index user and creates json file 
	 * 
	 * @access private
	 * @return void
	 */
	private function configure()
	{
		$user = User::oneByUsername('hubzerosolrworker');
		if ($user->get('username') == '')
		{
			// Automatically set email which passes validation
			$hostname = Request::host();
			if ($hostname != 'localhost')
			{
				$user->set('email', 'hubzero-solr@'. $hostname);
			}
			else
			{
				$config = App::get('config');
				$email = $config->get('mail')->mailfrom;
				$email = explode('@', $email);
				$user->set('email', 'solr@' . $email[1] . '.local');
			}

			// Set name
			$user->set('username', 'hubzerosolrworker');
			$user->set('name', 'HUBzero Solr Worker');
			$user->set('loginShell', '/bin/nologin');
			$user->set('ftpShell', '/usr/bin/sftp-server');

			// Give the Solr user full permissions
			$accessgroups = Accessgroup::all();
			$accessgroups = $accessgroups->rows()->toObject();
			$userAccessGroups = array();

			foreach ($accessgroups as $ag)
			{
				array_push($userAccessGroups, $ag->id);
			}

			$user->set('accessgroups', $userAccessGroups);
			$newpass = \JUserHelper::genRandomPassword();
			$user->set('password', \Hubzero\User\Password::getPasshash($newpass));

			// Save the User
			if ($user->save())
			{
				// Change password
				$result = \Hubzero\User\Password::changePassword($user->get('id'), $newpass);

				if (!$result)
				{
					\Notify::error($result->getError());
				}

				// Make an application
				$application = Application::oneOrNew(0);
				$application->set('name', 'HUBzero - Solr Indexing');
				$application->set('description', 'DO NOT DELETE! Application used by Solr indexer.');
				if (!$application->save())
				{
					\Notify::error($application->getError());
				}

				$comConfig = Component::params('com_search');
				$application = $application->toObject();
				$config = array();
				$config['solr_client_id'] = $application->client_id;
				$config['solr_client_secret'] = $application->client_secret;
				$config['solr_username'] = $user->get('username');
				$config['solr_password'] = $newpass;
				$config['solr_host'] = $comConfig->get('solr_host', 'localhost');
				$config['solr_port'] = $comConfig->get('solr_port', '8445');
				$json = json_encode($config);

				$filesystem = App::get('filesystem');
				$filesystem->write(PATH_APP . '/config/solr.json', $json);
			}
			else
			{
				\Notify::error($user->getError());
			}
		}

		\Notify::success(Lang::txt('COM_SEARCH_SOLR_CONFIGURATION_MADE'));

		return $this->displayTask();
	}

	/**
	 * Display the overview
	 */
	public function displayTask()
	{
		$this->view = new \Hubzero\Component\View;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Instantiate Search class
		$config = Component::params('com_search');

		// Get the last insert date
		try
		{
			$index = new \Hubzero\Search\Index($config);
			$timestamp = $insertTime = $index->lastInsert();
			$insertTime = Date::of($timestamp)->format('relative');
			$status = $index->status();

			// Get the last 10 entries
			$logs = array_slice($index->getLogs(), -10, 10, true);

		}
		catch (\Solarium\Exception\HttpException $e)
		{
			$this->view->setError($e->getMessage());
			$insertTime = '';
			$status = 'failed';
			$logs = array();
		}

		// Explicitly set the view since it may be called by another method
		$this->view->setLayout('overview');
		$this->view->setName('solr');
		$this->view->option = $this->_option;

		$this->view->mechanism = $config->get('engine');
		$this->view->status = $status;
		$this->view->logs = $logs;
		$this->view->lastInsert = $insertTime;

		// Display the view
		$this->view->display();
	}

	/**
	 * documentByTypeTask - view a type's records
	 * 
	 * @access public
	 * @return void
	 */
	public function documentListingTask()
	{
		$facet = Request::getVar('facet', '');
		$filter = Request::getVar('filter', '');
		$limitstart = Request::getInt('limitstart', 0);
		$limit = Request::getInt('limit', 10);

		// Display CMS errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Get the blacklist to indidicate marked for removal
		$blacklistEntries = Blacklist::all()
			->select('doc_id')
			->rows()
			->toObject();

		// @TODO: PHP 5.5+ supports array_column()
		$blacklist = array();
		foreach ($blacklistEntries as $entry)
		{
			array_push($blacklist, $entry->doc_id);
		}

		// Temporary override to get all matching documents
		if ($filter == '')
		{
			$filter = '*:*';
		}

		// Instantitate and get all results for a particular document type
		try
		{
			$config = Component::params('com_search');
			$query = new \Hubzero\Search\Query($config);
			$results = $query->query($filter)
				->addFilter('facet', $facet)
				->limit($limit)->start($limitstart)->run()->getResults();

			// Get the total number of records
			$total = $query->getNumFound();
		}
		catch (\Solarium\Exception\HttpException $e)
		{
			App::redirect(
				Route::url('index.php?option=com_search&task=display', false)
			);
		}

		// Create the pagination
		$pagination = new \Hubzero\Pagination\Paginator($total, $limitstart, $limit);
		$pagination->setLimits(array('5','10','15','20','50','100','200'));
		$this->view->pagination = $pagination;

		// Pass the filters and documents to the display
		$this->view->filter = ($filter == '') || $filter = '*:*' ? '' : $filter;
		$this->view->facet = !isset($facet) ? '' : $facet;
		$this->view->documents = isset($results) ? $results : array();
		$this->view->blacklist = $blacklist;

		// Display the view
		$this->view->display();
	}

	/**
	 * manageBlacklistTask 
	 * 
	 * @access public
	 * @return Hubzero\View\View 
	 */
	public function manageBlacklistTask()
	{
		$this->view->blacklist = Blacklist::all()->rows();
		$this->view->display();
	}

	/**
	 * addToBlacklistTask - Makes a database entry and removes from index
	 * 
	 * @access public
	 * @return void
	 */
	public function addToBlacklistTask()
	{
		$id = Request::getVar('id', '');
		$facet = Request::getVar('facet', '');
		$filter = Request::getVar('filter', '');
		$limitstart = Request::getInt('limitstart', 0);
		$limit = Request::getInt('limit', 10);

		// Make entry on blacklist
		$entry = Blacklist::oneOrNew(0);
		$entry->set('doc_id', $id);
		$entry->set('created', \Date::of()->toSql());
		$entry->set('created_by', User::getInstance()->get('id', 0));
		$entry->save();

		$helper = new SolrHelper;

		// Remove from index
		if ($helper->removeDocument($id, 'delete'))
		{
			// Redirect back to the search page.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=documentlisting&facet='.$facet.'&limitstart='.$limitstart.'&limit='.$limit.'&filter='.$filter, false),
				'Submitted ' . $id . ' for removal.',
				'success'
			);
		}
		else
		{
			// Redirect back to the search page.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=documentByType&type='.$scope, false),
				'Failed to remove '. $id,
				'error'
			);
		}
	}

	/**
	 * removeBlacklistEntryTask 
	 * 
	 * @access public
	 * @return void
	 */
	public function removeBlacklistEntryTask()
	{
		$entryID = Request::getInt('entryID', 0);

		$entry = Blacklist::one($entryID);
		$entry->destroy();

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller. '&task=manageBlacklist', false),
			'Successfully removed entry #' . $entryID,
			'success'
		);
	}

	/**
	 * Manage facets
	 * 
	 * @return  void
	 */
	public function searchIndexTask()
	{
		if (in_array('parent_id', array_keys($_GET)))
		{
			Request::checkToken(["post", "get"]);
		}

		// Check if we are adding a parent
		$parentID = Request::getInt('parent_id', 0);

		$parent = Facet::oneOrNew($parentID);

		// Load the subfacets, if applicable
		$facets = Facet::all()
			->whereEquals('parent_id', $parentID)
			->rows();

		foreach ($facets as $facet)
		{
			$facet->set('count', -1);

			// Instantitate and get all results for a particular document type
			try
			{
				$query = new \Hubzero\Search\Query($this->config);
				$results = $query->query($facet->facet)->run()->getResults();

				// Get the total number of records
				$total = $query->getNumFound();
				$facet->set('count', $total);
			}
			catch (\Solarium\Exception\HttpException $e)
			{
				Notify::error(Lang::txt($e->getMessage()));
			}
		}

		$this->view
			->set('parent', $parent)
			->set('facets', $facets)
			->display();
	}

	/**
	 * saveFacetTask 
	 * 
	 * @access public
	 * @return void
	 */
	public function saveFacetTask()
	{
		Request::checkToken(["post", "get"]);

		$fields = Request::getVar('fields', array());
		$action = Request::getCmd('action', '');
		$id     = Request::getInt('id', 0);

		$facet = Facet::oneOrNew($id);

		switch ($action)
		{
			case 'togglestate':
				$facet->set('state', !$facet->state);
				if (!$facet->save())
				{
					Notify::error(Lang::txt('COM_SEARCH_FAILURE_TO_SAVE'));
				}
			break;

			case 'editfacet':
			default:
				$new = $facet->isNew();
				$facet->set($fields);

				if (!$facet->save())
				{
					Notify::error(Lang::txt('COM_SEARCH_FAILURE_TO_SAVE'));
				}
				else
				{
					if ($new)
					{
						Notify::success(Lang::txt('COM_SEARCH_FACET_CREATED', $facet->name));
					}
					else
					{
						Notify::success(Lang::txt('COM_SEARCH_FACET_UPDATED', $facet->name));
					}
				}
			break;
		}

		//@FIXME: Redirect back to the child if selected
		$return = Route::url('index.php?option=com_search&task=searchIndex', false);

		App::redirect(
			Route::url($return, false)
		);
	}

	/**
	 * Add a facet
	 * 
	 * @return  void
	 */
	public function addFacetTask()
	{
		$parentID = Request::getInt('parent_id', 0);

		return $this->editFacetTask($parentID);
	}

	/**
	 * Edit a facet
	 * 
	 * @param   integer  $parentID
	 * @return  void
	 */
	public function editFacetTask($parentID = 0)
	{
		$id = Request::getInt('id', 0);

		$facet = Facet::oneOrNew($id);

		if ($facet->isNew())
		{
			$facet->set('parent_id', $parentID);
		}

		$this->view
			->set('facet', $facet)
			->setLayout('editfacet')
			->display();
	}

	/**
	 * Delete a facet
	 * 
	 * @return  void
	 */
	public function deleteFacetTask()
	{
		$ids = Request::getArray('id', 0);
		$message = '';

		foreach ($ids as $id)
		{
			$facet = Facet::one($id);
			$protected = $facet->get('protected');
			$name = $facet->get('name');

			if ($protected != 1)
			{
				if ($facet->children()->count() == 0)
				{
					if ($facet->destroy())
					{
						$message .= Lang::txt('COM_SEARCH_FACET_DELETE', $name);
						$success = 'success';
					}
					else
					{
						$message .= Lang::txt('COM_SEARCH_FACET_DELETE_FAILED', $name);
						$success = 'error';
					}
				}
				else
				{
					$message .= Lang::txt('COM_SEARCH_FACET_HAS_CHILDREN', $name);
					$success = 'warning';
				}
			}
			else
			{
				$message .= Lang::txt('COM_SEARCH_FACET_DELETE_PROTECTED', $name);
				$success = 'warning';
			}
		}

		$return = Route::url('index.php?option=com_search&task=searchIndex', false);

		App::redirect(
			Route::url($return, false),
			$message,
			$success
		);
	}
}
