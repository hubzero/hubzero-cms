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
use Hubzero\Access\Group as Accessgroup;
use Hubzero\Search\Query;
use Hubzero\Search\Index;
use Components\Search\Models\Solr\Blacklist;
use Components\Search\Models\Solr\Facet;
use Components\Search\Models\Solr\SearchComponent;
use Components\Search\Helpers\SolrHelper;
use Components\Search\Helpers\DiscoveryHelper;
use Components\Developer\Models\Application;
use stdClass;
use Component;
use Request;
use Notify;
use Date;
use User;
use Lang;
use App;

require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'blacklist.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'searchcomponent.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'facet.php';
require_once Component::path('com_developer') . DS . 'models' . DS . 'application.php';

/**
 * Search AdminController Class
 */
class Solr extends AdminController
{
	/**
	 * Determine task and execute it
	 * 
	 * @return void
	 */
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
	 * Optimize/defragment solr index
	 *
	 * @return void
	 */
	public function optimizeTask()
	{
		$config = Component::params('com_search');
		$index = new \Hubzero\Search\Index($config);
		try
		{
			$result = $index->optimize();
			if ($result->getStatus() == 0)
			{
				Notify::success('Successfully Optimized');
			}
			else
			{
				Notify::error('Optimization failed');
			}
		}
		catch (\Solarium\Exception\HttpException $e)
		{
			$this->view->setError($e->getMessage());
			$this->displayTask();
		}
		App::redirect(
			Route::url('index.php?option=com_search&task=display', false)
		);
	}



	/**
	 * configure - Adds solr index user and creates json file 
	 * 
	 * @return  void
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
			$newpass = \Hubzero\User\Password::genRandomPassword();
			$user->set('password', \Hubzero\User\Password::getPasshash($newpass));

			// Save the User
			if ($user->save())
			{
				// Change password
				$result = \Hubzero\User\Password::changePassword($user->get('id'), $newpass);

				if (!$result)
				{
					Notify::error($result->getError());
				}

				// Make an application
				$application = Application::oneOrNew(0);
				$application->set('name', 'HUBzero - Solr Indexing');
				$application->set('description', 'DO NOT DELETE! Application used by Solr indexer.');
				if (!$application->save())
				{
					Notify::error($application->getError());
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
				Notify::error($user->getError());
			}
		}

		Notify::success(Lang::txt('COM_SEARCH_SOLR_CONFIGURATION_MADE'));

		return $this->displayTask();
	}

	/**
	 * Display the overview
	 *
	 * @return  void
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
	 * Manage blacklist
	 * 
	 * @return  void
	 */
	public function manageBlacklistTask()
	{
		$this->view->blacklist = Blacklist::all()->rows();
		$this->view->display();
	}

	/**
	 * Makes a database entry and removes from index
	 * 
	 * @return  void
	 */
	public function addToBlacklistTask()
	{
		$id = Request::getString('id', '');
		$facet = Request::getString('facet', '');
		$filter = Request::getString('filter', '');
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
			Notify::success(Lang::txt('COM_SEARCH_SOLR_REMOVE_DOCUMENT', $id));
		}
		else
		{
			Notify::error(Lang::txt('COM_SEARCH_SOLR_REMOVE_DOCUMENT_ERROR', $id));
		}
		// Redirect back to the search page.
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=searchable' . '&task=documentlisting&facet='.$facet.'&limitstart='.$limitstart.'&limit='.$limit.'&filter='.$filter, false)
		);
	}

	/**
	 * Remove a blacklist entry
	 * 
	 * @return  void
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
}
