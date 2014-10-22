<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.job.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.session.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.view.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.viewperm.php');

/**
 * Controller class for tool sessions
 */
class ToolsControllerSessions extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of hosts
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['username']     = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.username',
			'username',
			''
		));
		$this->view->filters['appname']     = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.appname',
			'appname',
			''
		));
		$this->view->filters['exechost']     = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.exechost',
			'exechost',
			''
		));
		// Sorting
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'start'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'DESC'
		));
		// Get paging variables
		$this->view->filters['limit']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get the middleware database
		$mwdb = ToolsHelperUtils::getMWDBO();

		$model = new MwSession($mwdb);

		$this->view->total = $model->getAllCount($this->view->filters);

		$this->view->rows = $model->getAllRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Delete one or more hostname records
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		$mwdb = ToolsHelperUtils::getMWDBO();

		if (count($ids) > 0)
		{
			$row = new MwSession($mwdb);

			// Get plugins
			JPluginHelper::importPlugin('mw');
			$dispatcher = JDispatcher::getInstance();

			// Loop through each ID
			foreach ($ids as $id)
			{
				$id = intval($id);
				if (!$row->load($id))
				{
					$this->addComponentMessage(JText::sprintf('COM_TOOLS_ERROR_FAILED_TO_LOAD_SESSION', $id), 'error');
					continue;
				}

				// Trigger any events that need to be called before session stop
				$dispatcher->trigger('onBeforeSessionStop', array($row->appname));

				// Stop the session
				$status = $this->middleware("stop $id", $output);
				if ($status)
				{
					$msg = 'Stopping ' . $id . '<br />';
					foreach ($output as $line)
					{
						$msg .= $line . "\n";
					}
					$this->addComponentMessage($msg, 'error');
				}

				// Trigger any events that need to be called after session stop
				$dispatcher->trigger('onAfterSessionStop', array($row->appname));
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_TOOLS_SESSIONS_TERMINATED'),
			'message'
		);
	}

	/**
	 * Invoke the Python script to do real work.
	 *
	 * @param      string  $comm Parameter description (if any) ...
	 * @param      array   &$output Parameter description (if any) ...
	 * @return     integer Session ID
	 */
	public function middleware($comm, &$output)
	{
		$retval = true; // Assume success.
		$output = new stdClass();
		$cmd = "/bin/sh " . JPATH_ROOT . "/components/" . $this->_option . "/scripts/mw $comm 2>&1 </dev/null";

		exec($cmd, $results, $status);

		// Check exec status
		if ($status != 0)
		{
			// Uh-oh. Something went wrong...
			$retval = false;
			$this->setError($results[0]);
		}

		if (is_array($results))
		{
			// HTML
			// Print out the applet tags or the error message, as the case may be.
			foreach ($results as $line)
			{
				$line = trim($line);

				// If it's a new session, catch the session number...
				if ($retval && preg_match("/^Session is ([0-9]+)/", $line, $sess))
				{
					$retval = $sess[1];
					$output->session = $sess[1];
				}
				else
				{
					if (preg_match("/width=\"(\d+)\"/i", $line, $param))
					{
						$output->width = trim($param[1], '"');
					}
					if (preg_match("/height=\"(\d+)\"/i", $line, $param))
					{
						$output->height = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"PORT\" value=\"?(\d+)\"?>/i", $line, $param))
					{
						$output->port = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"ENCPASSWORD\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->password = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"CONNECT\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->connect = trim($param[1], '"');
					}
					if (preg_match("/^<param name=\"ENCODING\" value=\"?(.+)\"?>/i", $line, $param))
					{
						$output->encoding = trim($param[1], '"');
					}
				}
			}
		}
		else
		{
			// JSON
			$output = json_decode($results);
			if ($output == null)
			{
				$retval = false;
			}
		}

		return $retval;
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
