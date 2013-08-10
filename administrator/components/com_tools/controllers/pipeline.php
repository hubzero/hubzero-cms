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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Tool');
ximport('Hubzero_Tool_Version');
ximport('Hubzero_Controller');

/**
 * Short description for 'ContribtoolController'
 * 
 * Long description (if any) ...
 */
class ToolsControllerPipeline extends Hubzero_Controller
{
	/**
	 * Display entries in the pipeline
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'tools.css');

		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		$this->view->filters = array();

		// Get filters
		$this->view->filters['search']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search', 
			'search', 
			''
		));
		$this->view->filters['search_field'] = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search_field', 
			'search_field', 
			'all'
		));

		// Sorting
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'toolname'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'ASC'
		));
		$this->view->filters['sortby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

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

		// Get a record count
		$this->view->total = Hubzero_Tool::getToolCount($this->view->filters, true);

		// Get records
		$this->view->rows = Hubzero_Tool::getToolSummaries($this->view->filters, true);

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
	 * Edit an entry
	 * 
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);
		
		$this->view->setLayout('edit');
		
		// Incoming instance ID
		$id = JRequest::getInt('id', 0);

		// Do we have an ID?
		if (!$id) 
		{
			$this->cancelTask();
			return;
		}

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			$this->view->row = Hubzero_Tool::getInstance($id);
		}

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
	 * Save an entry and show the edit form
	 * 
	 * @return     void
	 */
	public function applyTask()
	{
	    $this->saveTask();
	}

	/**
	 * Save an entry
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		JRequest::checkToken() or die('Invalid Token');
		
		// Incoming instance ID
		$fields = JRequest::getVar('fields', array(), 'post');

		// Do we have an ID?
		if (!$fields['id']) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Missing ID'),
				'error'
			);
			return;
		}

		$row = Hubzero_Tool::getInstance(intval($fields['id']));
		if (!$row)
		{
			JRequest::setVar('id', $fields['id']);
			$this->addComponentMessage(JText::_('Tool not found'), 'error');
			$this->editTask();
			return;
		}

		$row->title = trim($fields['title']);

		if (!$row->title)
		{
			$this->addComponentMessage(JText::_('Missing title'), 'error');
			$this->editTask($row);
			return;
		}

		$row->update();

		if ($this->_task == 'apply') 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $fields['id']
			);
			return;
		}
		else 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('TOOL_SAVED')
			);
		}
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

	/**
	 * Temp function to issue new service DOIs for tool versions published previously
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function batchdoiTask()
	{
		$juser =& JFactory::getUser();

		$yearFormat = '%Y';
		$tz = null;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$yearFormat = 'Y';
			$tz = false;
		}

		//  Limit one-time batch size
		$limit = JRequest::getInt('limit', 2);

		// Store output	
		$created = array();
		$failed = array();

		// Initiate extended database classes
		$resource = new ResourcesResource($this->database);
		$objDOI = new ResourcesDoi ($this->database);
		$objV = new ToolVersion($this->database);
		$objA = new ToolAuthor($this->database);

		$jconfig =& JFactory::getConfig();
		$live_site = rtrim(JURI::base(),'/');
		$sitename 	= $jconfig->getValue('config.sitename');
		
		// Get config
		$config =& JComponentHelper::getParams($this->_option);

		// Get all tool publications without new DOI
		$this->database->setQuery("SELECT * FROM #__doi_mapping WHERE doi='' OR doi IS NULL ");
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
					echo '<p>Registered '.count($created).' dois, failed '.count($failed).'</p>';
					return;
				}

				// Skip entries with no resource information loaded / non-tool resources
				if (!$resource->load($row->rid) || !$row->alias) 
				{
					continue;
				}

				// Get version info
				$this->database->setQuery("SELECT * FROM #__tool_version WHERE toolname='".$row->alias."' AND revision='".$row->local_revision."' AND state!=3 LIMIT 1");
				$results = $this->database->loadObjectList();

				if ($results) 
				{
					$title = $results[0]->title ? $results[0]->title : $resource->title;
					$pubyear = $results[0]->released ? trim(JHTML::_('date', $results[0]->released, $yearFormat, $tz)) : date('Y');
				}
				else 
				{
					// Skip if version not found
					continue;
				}

				// Collect metadata
				$metadata = array();
				$metadata['targetURL'] = $live_site . '/resources/' . $row->rid . '/?rev='.$row->local_revision;
				$metadata['title'] = htmlspecialchars($title);
				$metadata['pubYear'] = $pubyear;
				
				// Get authors
				$objA = new ToolAuthor($this->database);
				$authors = $objA->getAuthorsDOI($row->rid);

				// Register DOI
				$doiSuccess = $objDOI->registerDOI($authors, $config, $metadata, $doierr);
				if ($doiSuccess) 
				{
					$this->database->setQuery("UPDATE #__doi_mapping SET doi='$doiSuccess' WHERE rid=$row->rid AND local_revision=$row->local_revision");
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
				echo '<p>'.$cr.'</p>';
			}
		}
		echo '<p>Registered '.count($created).' dois, failed '.count($failed).'</p>';
		return;
	}

	/**
	 * Temp function to ensure jos_doi_mapping table is updated
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function setupdoiTask()
	{
		$fields = $this->database->getTableFields('jos_doi_mapping');

		if (!array_key_exists('versionid', $fields['jos_doi_mapping'])) 
		{
			$this->database->setQuery("ALTER TABLE `jos_doi_mapping` ADD `versionid` int(11) default '0'");
			if (!$this->database->query()) 
			{
				echo $this->database->getErrorMsg();
				return false;
			}
		}
		if (!array_key_exists('doi', $fields['jos_doi_mapping'])) 
		{
			$this->database->setQuery("ALTER TABLE `jos_doi_mapping` ADD `doi` varchar(50) default NULL");
			if (!$this->database->query()) 
			{
				echo $this->database->getErrorMsg();
				return false;
			}
		}
		return;
	}
}
