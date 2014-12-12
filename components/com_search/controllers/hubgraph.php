<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(dirname(__DIR__) . DS . 'helpers' . DS . 'hubgraph.php');
include_once(dirname(__DIR__) . DS . 'models' . DS . 'hubgraph' . DS . 'inflect.php');
include_once(dirname(__DIR__) . DS . 'models' . DS . 'hubgraph' . DS . 'request.php');
include_once(dirname(__DIR__) . DS . 'models' . DS . 'hubgraph' . DS . 'client.php');
include_once(dirname(__DIR__) . DS . 'models' . DS . 'hubgraph' . DS . 'db.php');

/**
 * Hubgraph controller class
 */
class SearchControllerHubgraph extends \Hubzero\Component\SiteController
{
	/**
	 * Determine task and execute it
	 * 
	 * @return  void
	 */
	public function execute()
	{
		foreach (array('SCRIPT_URL', 'URL', 'REDIRECT_SCRIPT_URL', 'REDIRECT_URL') as $k)
		{
			if (isset($_SERVER[$k]))
			{
				$this->base = $_SERVER[$k];
				break;
			}
		}

		$this->req  = new HubgraphRequest($_GET);
		$this->conf = HubgraphConfiguration::instance();
		$this->perPage = JFactory::getConfig()->get('list_limit', 50);

		$this->registerTask('page', 'index');
		$this->registerTask('__default', 'index');

		// Try to execute
		try
		{
			$path = JFactory::getApplication()->getPathway();
			$path->addItem(JText::_('COM_SEARCH'), 'index.php?option=' . $this->_option);

			parent::execute();
		}
		catch (Exception $ex)
		{
			// Log the error
			JFactory::getLogger()->error($ex->getMessage());

			// If not displaying inline...
			if (!defined('HG_INLINE'))
			{
				// Redirect back to this component wil the fallback flag set
				// so it knows to load the default, basic controller.
				$terms = JRequest::getVar('terms', '', 'get');
				$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_option . '&fallback=true' . ($terms ? '&terms=' . $terms : ''), false),
					(JDEBUG ? $ex->getMessage() : null),
					(JDEBUG ? 'error' : null)
				);
				return;
			}
		}
	}

	/**
	 * Display search form and results (if any)
	 * 
	 * @return  void
	 */
	public function indexTask()
	{
		$this->view->results = $this->req->anyCriteria()
				? json_decode(HubgraphClient::execView('search', $this->req->getTransportCriteria(array('limit' => $this->perPage))), TRUE)
				: NULL;

		$this->view->req       = $this->req;
		$this->view->tags      = $this->req->getTags();
		$this->view->users     = $this->req->getContributors();
		$this->view->groups    = $this->req->getGroup();
		$this->view->domainMap = $this->req->getDomainMap();
		$this->view->loggedIn  = (bool) JFactory::getUser()->get('id');
		$this->view->perPage   = $this->perPage;
		ksort($this->view->domainMap);

		if ($this->_task == 'page')
		{
			define('HG_AJAX', 1);
			$this->view
				->set('base', $this->base)
				->setLayout('page')
				->display();
			exit();
		}

		$this->view
			->set('base', $this->base)
			->setLayout('index-update')
			->display();
	}

	/**
	 * Display complete
	 * 
	 * @return  void
	 */
	public function completeTask()
	{
		$args = array(
			'limit'     => 20,
			'threshold' => 3,
			'tagLimit'  => 100
		);

		header('Content-type: text/json');
		echo HubgraphClient::execView('complete', array_merge($_GET, $args));
		exit();
	}

	/**
	 * Display related
	 * 
	 * @return  void
	 */
	public function getrelatedTask()
	{
		$args = $this->req->getTransportCriteria(array(
			'limit'  => 5,
			'domain' => $_GET['domain'],
			'id'     => $_GET['id']
		));

		header('Content-type: text/json');
		echo HubgraphClient::execView('related', array_merge($_GET, $args));
		exit();
	}

	/**
	 * Update
	 * 
	 * @return  void
	 */
	public function updateTask()
	{
		$this->view->results = $this->req->anyCriteria()
				? json_decode(HubgraphClient::execView('search', $this->req->getTransportCriteria(array('limit' => $this->perPage))), TRUE)
				: NULL;

		define('HG_AJAX', 1);

		$this->view->req       = $this->req;
		$this->view->tags      = $this->req->getTags();
		$this->view->users     = $this->req->getContributors();
		$this->view->groups    = $this->req->getGroup();
		$this->view->domainMap = $this->req->getDomainMap();
		$this->view->loggedIn  = (bool) JFactory::getUser()->get('id');
		$this->view->perPage   = $this->perPage;
		ksort($this->view->domainMap);

		$this->view
			->set('base', $this->base)
			->setLayout('index-update')
			->display();
	}
}

