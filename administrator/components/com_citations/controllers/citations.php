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
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for citations
 */
class CitationsControllerCitations extends Hubzero_Controller
{
	/**
	 * List citations
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		$this->view->filters = array();

		// Get paging variables
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart', 
			'limitstart', 
			0, 
			'int'
		);

		// Get filters
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'sort', 
			'created DESC'
		));
		$this->view->filters['search']     = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search', 
			'search', 
			''
		)));

		$obj = new CitationsCitation($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Get records
		$this->view->rows = $obj->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		//get the dynamic citation types
		$ct = new CitationsType($this->database);
		$this->view->types = $ct->getType();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new citation
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a citation
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		//stop menu from working?
		JRequest::setVar('hidemainmenu', 1);
		
		//force edit layout
		$this->view->setLayout('edit');
		
		//get request vars - expecting an array id[]=4232
		$id = JRequest::getVar('id', array());
		$id = (is_array($id) && !empty($id)) ? $id[0] : 0;
		
		//get all citations sponsors
		$cs = new CitationsSponsor($this->database);
		$this->view->sponsors = $cs->getSponsor();
		
		//get all citation types
		$ct = new CitationsType($this->database);
		$this->view->types = $ct->getType();
		
		//empty citation object
		$this->view->row = new CitationsCitation($this->database);
		
		//params class object
		$paramsClass = (version_compare(JVERSION, '1.6', 'ge')) ? 'JRegistry' : 'JParameter';
		
		//if we have an id load that citation data
		if (isset($id) && $id != '' && $id != 0)
		{
			// Load the citation object
			$this->view->row->load( $id );
			
			// Get the associations
			$assoc = new CitationsAssociation($this->database);
			$this->view->assocs = $assoc->getRecords(array('cid' => $id));
			
			//get sponsors for citation
			$this->view->row_sponsors = $cs->getCitationSponsor($this->view->row->id);
			
			//get the citations tags
			$sql = "SELECT t.*
					FROM #__tags_object to1 
					INNER JOIN #__tags t ON t.id = to1.tagid 
					WHERE to1.tbl='citations' 
					AND to1.objectid=" . $this->database->quote( $id ) . "
					AND to1.label=''";
			$this->database->setQuery($sql);
			$this->view->tags = $this->database->loadAssocList();

			//get the badges
			$sql = "SELECT t.*
					FROM #__tags_object to1 
					INNER JOIN #__tags t ON t.id = to1.tagid 
					WHERE to1.tbl='citations' 
					AND to1.objectid=" . $this->database->quote( $id ) . "
					AND to1.label='badge'";
			$this->database->setQuery($sql);
			$this->view->badges = $this->database->loadAssocList();
			
			//parse citation params
			$this->view->params = new $paramsClass($this->view->row->params);
		}
		else
		{
			//set the creator
			$this->view->row->uid = $this->juser->get('id');
			
			// It's new - no associations to get
			$this->view->assocs = array();
			
			//array of sponsors - empty
			$this->view->row_sponsors = array();
			
			//empty tags and badges arrays
			$this->view->tags = array();
			$this->view->badges = array();
			
			//empty params object
			$this->view->params = new $paramsClass('');
		}
		
		//are we padding back the citation data
		if (isset($this->row))
		{
			$this->view->row = $this->row;
		}
		
		//are we passing back the tags from edit
		if ($this->tags != '')
		{
			$this->tags = explode(',', $this->tags);
			foreach ($this->tags as $tag)
			{
				$this->view->tags[]['raw_tag'] = $tag;
			}
		}
		
		//are we passing back the tags from edit
		if ($this->badges != '')
		{
			$this->badges = explode(',', $this->badges);
			foreach ($this->badges as $badge)
			{
				$this->view->badges[]['raw_tag'] = $badge;
			}
		}
		
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		//set vars for view
		$this->view->config = $this->config;
		
		// Output the HTML
		$this->view->display();
	}

	/**
	 * Display stats for citations
	 * 
	 * @return     void
	 */
	public function statsTask()
	{
		// Load the object
		$row = new CitationsCitation($this->database);
		$this->view->stats = $row->getStats();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a citation
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$citation = array_map('trim', JRequest::getVar('citation', array(), 'post'));
		$exclude = JRequest::getVar('exclude', '', 'post');
		$rollover = JRequest::getInt("rollover", 0);
		$this->tags = trim(JRequest::getVar('tags', ''));
		$this->badges = trim(JRequest::getVar('badges', ''));
		$this->sponsors = JRequest::getVar('sponsors', array(), 'post');
		
		// Bind incoming data to object
		$row = new CitationsCitation($this->database);
		if (!$row->bind($citation)) 
		{
			$this->row = $row;
			$this->setError( $row->getError() );
			$this->editTask();
			return;
		}
		
		//params class object
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		
		//set params
		$cparams = new $paramsClass($this->_getParams($row->id));
		$cparams->set('exclude', $exclude);
		$cparams->set('rollover', $rollover);
		$row->params = $cparams->toString();
		
		// New entry so set the created date
		if (!$row->id) 
		{
			$row->created = date('Y-m-d H:i:s', time());
		}

		// Check content for missing required data
		if (!$row->check()) 
		{
			$this->row = $row;
			$this->setError( $row->getError() );
			$this->editTask();
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->row = $row;
			$this->setError( $row->getError() );
			$this->editTask();
			return;
		}

		// Incoming associations
		$arr = JRequest::getVar('assocs', array(), 'post');
		$ignored = array();
		foreach ($arr as $a)
		{
			$a = array_map('trim',$a);
			
			// Initiate extended database class
			$assoc = new CitationsAssociation($this->database);
			
			//check to see if we should delete
			if(isset($a['id']) && $a['tbl'] == '' && $a['oid'] == '')
			{
				// Delete the row
				if (!$assoc->delete($a['id'])) 
				{
					JError::raiseError(500, $assoc->getError());
					return;
				}
			}
			else if($a['tbl'] != '' || $a['oid'] != '')
			{
				$a['cid'] = $row->id;
				
				// bind the data
				if (!$assoc->bind($a)) 
				{
					JError::raiseError(500, $assoc->getError());
					return;
				}

				// Check content
				if (!$assoc->check()) 
				{
					JError::raiseError(500, $assoc->getError());
					return;
				}

				// Store new content
				if (!$assoc->store()) 
				{
					JError::raiseError(500, $assoc->getError());
					return;
				}
			}
		}
		
		//save sponsors on citation
		if ($this->sponsors)
		{
			$cs = new CitationsSponsor($this->database);
			$cs->addSponsors($row->id, $this->sponsors);
		}
		
		//add tags & badges 
		$ct = new CitationTags($this->database);
		$ct->tag_object($this->juser->get("id"), $row->id, $this->tags, 1, false, "");
		$ct->tag_object($this->juser->get("id"), $row->id, $this->badges, 1, false, "badge");
		
		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('CITATION_SAVED')
		);
	}

	/**
	 * Check if an array has any values set other than $ignored values
	 * 
	 * @param      array $b       Array to check
	 * @param      array $ignored Values to ignore
	 * @return     boolean True if empty
	 */
	private function _isEmpty($b, $ignored=array())
	{
		foreach ($ignored as $ignore)
		{
			if (array_key_exists($ignore, $b)) 
			{
				$b[$ignore] = NULL;
			}
		}
		if (array_key_exists('id',$b)) 
		{
			$b['id'] = NULL;
		}
		$values = array_values($b);
		$e = true;
		foreach ($values as $v)
		{
			if ($v) 
			{
				$e = false;
			}
		}
		return $e;
	}

	/**
	 * Remove one or more citations
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) 
		{
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation($this->database);
			$assoc = new CitationsAssociation($this->database);
			$author = new CitationsAuthor($this->database);
			foreach ($ids as $id)
			{
				// Fetch and delete all the associations to this citation
				$assocs = $assoc->getRecords(array('cid'=>$id));
				foreach ($assocs as $a)
				{
					$assoc->delete($a->id);
				}

				// Fetch and delete all the authors to this citation
				$authors = $author->getRecords(array('cid'=>$id));
				foreach ($authors as $a)
				{
					$author->delete($a->id);
				}

				// Delete the citation
				$citation->delete($id);

				//citation tags
				$ct = new CitationTags($this->database);
				$ct->remove_all_tags($id);
			}

			$message = JText::_('CITATION_REMOVED');
		} 
		else 
		{
			$message = JText::_('NO_SELECTION');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message
		);
	}
	
	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Get the params for a citation
	 *
	 * @param      integer $citation Citation ID
	 * @return     integer
	 */
	private function _getParams($citation = 0)
	{
		$this->database->setQuery("SELECT c.params from #__citations c WHERE id=" . $this->database->quote( $citation ));
		return $this->database->loadResult();
	}
}

