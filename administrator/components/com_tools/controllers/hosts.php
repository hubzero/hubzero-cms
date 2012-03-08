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

ximport('Hubzero_Controller');

/**
 * Short description for 'ToolsController'
 * 
 * Long description (if any) ...
 */
class ToolsControllerHosts extends Hubzero_Controller
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
		$app =& JFactory::getApplication();
		
		// Get filters
		$this->view->filters = array();
		$this->view->filters['hostname']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.hostname', 
			'hostname', 
			''
		));
		$this->view->filters['hosttype']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.hosttype', 
			'hosttype', 
			''
		));

		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		// Form the query and retrieve list of hosts
		if ($this->view->filters['hosttype']) 
		{
			$query = "SELECT host.* 
					FROM host 
					JOIN hosttype ON host.provisions & hosttype.value != 0 
					WHERE hosttype.name = " . $mwdb->Quote($this->view->filters['hosttype']) . " 
					ORDER BY hostname";
		} 
		else 
		{
			$query = "SELECT * FROM host ORDER BY hostname";
		}
		$mwdb->setQuery($query);
		$this->view->rows = $mwdb->loadObjectList();

		// Get a list of hosttypes
		$mwdb->setQuery("SELECT * FROM hosttype ORDER BY value");
		$this->view->results = $mwdb->loadObjectList();

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		// Display results
		$this->view->display();
	}
	
	/**
	 * Short description for 'apply'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function statusTask()
	{
		// Incoming
		$hostname = JRequest::getVar('hostname', '', 'get');
		
		// $hostname is eventually used in a string passed to an exec call, we gotta 
		// clean at least some of it. See RFC 1034 for valid character set info
		$hostname = preg_replace("[^A-Za-z0-9-.]", "", $hostname);
		
		$this->view->status = $this->_middleware("check $hostname yes", $output);
		$this->view->hostname = $hostname;
		$this->view->output = $output;
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		// Display results
		$this->view->display();
	}
	
	/**
	 * Short description for 'middleware'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $comm Parameter description (if any) ...
	 * @param      array &$fnoutput Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	protected function _middleware($comm, &$fnoutput)
	{
		$retval = 1; // Assume success.
		$fnoutput = array();

		exec("/bin/sh ../components/".$this->_option."/mw $comm 2>&1 </dev/null",$output,$status);

		$outln = 0;
		if ($status != 0) 
		{
			$retval = 0;
		}

		// Print out the applet tags or the error message, as the case may be.
		foreach ($output as $line)
		{
			// If it's a new session, catch the session number...
			if (($retval == 1) && preg_match("/^Session is ([0-9]+)/",$line,$sess)) 
			{
				$retval = $sess[1];
			} 
			else 
			{
				if ($status != 0) 
				{
					$fnoutput[$outln] = $line;
				} 
				else 
				{
					$fnoutput[$outln] = $line;
				}
				$outln++;
			}
		}

		return $retval;
	}

	/**
	 * Edit a record
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		// Incoming
		$hostname = JRequest::getVar( 'hostname', '', 'get' );
		
		// $hostname is eventually used in a string passed to an exec call, we gotta 
		// clean at least some of it. See RFC 1034 for valid character set info
		$hostname = preg_replace("[^A-Za-z0-9-.]", "", $hostname);
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		$mwdb->setQuery("SELECT * FROM host WHERE hostname='$hostname'");
		$this->view->row = $mwdb->loadObject();
		
		$mwdb->setQuery("SELECT * FROM hosttype ORDER BY value");
		$this->view->results = $mwdb->loadObjectList();
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		// Display results
		$this->view->display();
	}
	
	/**
	 * Save changes to a record
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		// Incoming
		$hostname = JRequest::getVar('hostname', '', 'post');
		$id = JRequest::getVar('id', '', 'post');
		
		// $hostname is eventually used in a string passed to an exec call, we gotta 
		// clean at least some of it. See RFC 1034 for valid character set info
		$hostname = preg_replace("[^A-Za-z0-9-.]", "", $hostname);
		$id = preg_replace("[^A-Za-z0-9-.]", "", $id);
		if (!$hostname) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				Jtext::_('You must specify a valid hostname.'),
				'error'
			);
			return;
		} 
		
		$status = JRequest::getVar('status', '', 'post');
		
		// Figure out the hosttype stuff.
		$hosttype = JRequest::getVar('hosttype', array(), 'post');	
		$harr = array();
		foreach ($hosttype as $name => $value)
		{
			$harr[$value] = 1;
		}
		$h = 0;

		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		$mwdb->setQuery("SELECT name,value FROM hosttype");
		if ($rows = $mwdb->loadObjectList())
		{
			for ($i=0; $i < count($rows); $i++)
			{
				$row = $rows[$i];
				if (isset($harr[$row->name])) 
				{
					$h += $row->value;
				}
			}
		}

		if ($id) 
		{
			$query = "UPDATE host SET hostname=".$mwdb->Quote($hostname).",provisions=".$mwdb->Quote($h).",status=".$mwdb->Quote($status) . " WHERE hostname=" . $mwdb->Quote($id) .";";
			$mwdb->setQuery($query);
			if (!$mwdb->query())
			{
				$this->setError($mwdb->getError());
			}
		} 
		else 
		{
			$query = "INSERT INTO host(hostname,provisions,status) VALUES(" . $mwdb->Quote($hostname) . ", " . $mwdb->Quote($h) . ", " . $mwdb->Quote($status) .");";
			$mwdb->setQuery($query);
			if (!$mwdb->query())
			{
				$this->setError($mwdb->getError());
			}
		}
		
		if ($this->getError())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$this->getError(),
				'error'
			);
			return;
		}
		
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			Jtext::_('Hostname successfully saved.'),
			'message'
		);
	}
	
	/**
	 * Toggle a hostname provision
	 * 
	 * @return     void
	 */
	public function toggleTask()
	{
		// Incoming
		$hostname = JRequest::getVar('hostname', '', 'get');
		$item = JRequest::getVar('item', '', 'get');
		// $hostname is eventually used in a string passed to an exec call, we gotta 
		// clean at least some of it. See RFC 1034 for valid character set info
		$hostname = preg_replace("[^A-Za-z0-9-.]", "", $hostname);
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		
		$query = "SELECT @value:=value FROM hosttype WHERE name=" . $mwdb->Quote($item) .
				" UPDATE host SET provisions = provisions ^ @value WHERE hostname = " . $mwdb->Quote($hostname) . ";";
		$mwdb->setQuery($query);
		if (defined('_JEXEC')) 
		{
			$result = $mwdb->queryBatch();
		} 
		else 
		{
			$result = $mwdb->query_batch();
		}
		
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
	
	/**
	 * Delete a hostname record
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		$hostname = JRequest::getVar('hostname', '', 'get');
		// $hostname is eventually used in a string passed to an exec call, we gotta 
		// clean at least some of it. See RFC 1034 for valid character set info
		$hostname = preg_replace("[^A-Za-z0-9-.]", "", $hostname);
		
		// Get the middleware database
		$mwdb =& MwUtils::getMWDBO();
		$mwdb->setQuery("DELETE FROM host WHERE hostname=" . $mwdb->Quote($hostname));
		if (!$mwdb->query()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$mwdb->getErrorMsg(),
				'error'
			);
			return;
		}
		
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Hostname successfully deleted.'),
			'message'
		);
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
