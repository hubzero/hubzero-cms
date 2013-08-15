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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'contributor.php');

/**
 * Resources controller for creating a resource
 */
class ResourcesControllerCreate extends Hubzero_Controller
{
	/**
	 * Container for steps
	 * 
	 * @var array
	 */
	public $steps = array('Type', 'Compose', 'Attach', 'Authors', 'Tags', 'Review');

	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->registerTask('discard', 'delete');
		$this->registerTask('remove', 'delete');
		$this->registerTask('start', 'draft');

		// Load the com_resources component config
		$this->config = JComponentHelper::getParams('com_resources');

		// Get the task at hand
		$task = JRequest::getVar('task', '');
		$this->step = JRequest::getInt('step', 0);
		if ($this->step && !$task) 
		{
			JRequest::setVar('task', 'draft');
		}

		if ($this->juser->get('guest')) 
		{
			JRequest::setVar('task', 'login');
		}

		// Push some scripts to the template
		$this->_getScripts('assets/js/create');

		// Build the title
		$this->_buildTitle();

		// Build the pathway
		$this->_buildPathway();

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	public function _buildPathway()
	{
		$pathway =& JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			JText::_('COM_CONTRIBUTE_NEW'),
			'index.php?option=' . $this->_option . '&task=new'
		);
		if ($this->_task) 
		{
			$pathway->addItem(
				JText::_('COM_CONTRIBUTE' . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
		if ($this->step) 
		{
			$pathway->addItem(
				JText::sprintf('COM_CONTRIBUTE_STEP_NUMBER', $this->step) . ': ' . JText::_('COM_CONTRIBUTE_STEP_' . strtoupper($this->steps[$this->step])),
				'index.php?option=' . $this->_option . '&task=' . $this->_task . '&step=' . $this->step
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	public function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option)) . ': ' . JText::_('COM_CONTRIBUTE');
		if ($this->_task) 
		{
			$this->_title .= ': ' . JText::_('COM_CONTRIBUTE' . '_' . strtoupper($this->_task));
		}
		if ($this->step) 
		{
			$this->_title .= ': ' . JText::sprintf('COM_CONTRIBUTE_STEP_NUMBER', $this->step) . ': ' . JText::_('COM_CONTRIBUTE_STEP_' . strtoupper($this->steps[$this->step]));
		}

		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Redirect to the login page with the return set
	 * 
	 * @return     void
	 */
	public function loginTask()
	{
		$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_controller), 'server');
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn))
		);
		return;
	}

	/**
	 * Component landing page
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Push some styles to the template
		$this->_getStyles($this->_option, 'introduction.css', true);
		$this->_getStyles($this->_option, $this->_controller . '.css');

		$this->view->title = $this->_title;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Check how many steps have been completed for a resource
	 * 
	 * @param      integer $id Resource ID
	 * @return     void
	 */
	protected function _checkProgress($id)
	{
		$steps = $this->steps;
		$laststep = (count($steps) - 1);
		$stepchecks = array();

		$progress['submitted'] = 0;
		for ($i=1, $n=count($steps); $i < $n; $i++)
		{
			$check = 'step_' . $steps[$i] . '_check';
			$stepchecks[$steps[$i]] = $this->$check($id);

			if ($stepchecks[$steps[$i]]) 
			{
				$progress[$steps[$i]] = 1;
				if ($i == $laststep) 
				{
					$progress['submitted'] = 1;
				}
			} 
			else 
			{
				$progress[$steps[$i]] = 0;
			}
		}
		$this->progress = $progress;
	}

	/**
	 * Call the current step
	 * 
	 * @return     void
	 */
	public function draftTask()
	{
		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Determine the current step
		$steps = $this->steps;
		$step  = $this->step;
		if ($step > count($steps)) 
		{
			$step = count($steps);
		}

		// Determine the previous step
		$pre = ($step > 0) ? $step - 1 : 0;

		// Build name for methods
		$preprocess = 'step_' . strtolower($steps[$pre]) . '_process';
		$activestep = 'step_' . strtolower($steps[$step]);

		// Set the layout to the current step
		$this->setView('steps', strtolower($steps[$step]));

		// assign some commonly used vars
		$this->view->config   = $this->config;
		$this->view->database = $this->database;
		$this->view->title    = $this->_title;
		$this->view->step     = $this->step;
		$this->view->steps    = $this->steps;

		// Is it a POST and the step field was set?
		// If so, it means we're at least past step 1
		if (isset($_POST['step'])) 
		{
			// Perform any preprocessing
			$this->$preprocess();
		}

		// Any errors?
		if (!$this->getError()) 
		{
			// Check the progress
			$this->_checkProgress(JRequest::getInt('id', 0));

			$this->view->progress = $this->progress;

			// Call current step
			$this->$activestep();
		}
	}

	/**
	 * Display a list of contributable resource types and let the user pick
	 * 
	 * @return     void
	 */
	public function step_type()
	{
		$this->view->group = JRequest::getVar('group', '');
		$this->view->step = $this->step;
		$this->view->step++;

		// Get available resource types
		$rt = new ResourcesType($this->database);
		$this->view->types = $rt->getMajorTypes();

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Display a form for composing the title, abstract, etc.
	 * 
	 * @param      object $row ResourcesResource
	 * @return     void
	 */
	public function step_compose($row=null)
	{
		$group = JRequest::getVar('group', '');
		$type = JRequest::getVar('type', '');

		if ($type == '7') 
		{
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_tools&task=create'), '', 'message', true);
		}

		$this->view->next_step = $this->step + 1;

		// Incoming
		$this->view->id = JRequest::getInt('id', 0);

		if (!is_object($row))
		{
			// Instantiate a new resource object
			$row = new ResourcesResource($this->database);
			if ($this->view->id) 
			{
				// Load the resource
				$row->load($this->view->id);
			} 
			else 
			{
				// Load the type and set the state
				$row->type = $type;
				$row->published = 2;
				$row->group_owner = $group;
			}
		}

		// Output HTML
		$this->view->row  = $row;
		$this->view->task = 'draft';
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Show form for adding attachments to a resource
	 * 
	 * @return     void
	 */
	public function step_attach()
	{
		//$step = $this->step;
		//$next_step = $step+1;

		// Incoming
		$this->view->id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$this->view->id) 
		{
			JError::raiseError(500, JText::_('COM_CONTRIBUTE_NO_ID'));
			return;
		}

		// Load the resource
		$this->view->row = new ResourcesResource($this->database);
		$this->view->row->load($this->view->id);

		// Output HTML
		$this->view->next_step = $this->step + 1;
		$this->view->task = 'draft';
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Show form for adding authors to a resource
	 * 
	 * @return     void
	 */
	public function step_authors()
	{
		// Incoming
		$this->view->id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$this->view->id) 
		{
			JError::raiseError(500, JText::_('COM_CONTRIBUTE_NO_ID'));
			return;
		}

		// Load the resource
		$this->view->row = new ResourcesResource($this->database);
		$this->view->row->load($this->view->id);

		// Get groups
		ximport('Hubzero_User_Profile');
		$profile = Hubzero_User_Profile::getInstance($this->juser->get('id'));
		$this->view->groups = $profile->getGroups('members');

		// Output HTML
		$this->view->next_step = $this->step + 1;
		$this->view->task = 'draft';
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Recursive method for loading hierarchical focus areas (tags)
	 * 
	 * @param      integer $id           Resource type ID
	 * @param      array   $labels       Tags
	 * @param      integer $parent_id    Tag ID
	 * @param      string  $parent_label Tag
	 * @return     void
	 */
	private function _loadFocusAreas($type, $labels = null, $parent_id = NULL, $parent_label = NULL) 
	{
		if (is_null($labels)) 
		{
			$this->database->setQuery(
				'SELECT DISTINCT tag 
				FROM #__focus_area_resource_type_rel fr 
				INNER JOIN #__focus_areas f ON f.id = fr.focus_area_id
				INNER JOIN #__tags t ON t.id = f.tag_id
				WHERE fr.resource_type_id = ' . $type
			);
			if (!($labels = $this->database->loadResultArray())) 
			{
				return array();
			}
			$labels = '\'' . implode('\', \'', array_map(array($this->database, 'getEscaped'), $labels)) . '\'';
		}

		$this->database->setQuery(
			$parent_id 
				// get tags labeled focus area and parented by the tag identified by $parent_id
				? 'SELECT DISTINCT t.raw_tag AS label, t2.id, t2.tag, t2.raw_tag 
					FROM #__tags t
					INNER JOIN #__tags_object to1 ON to1.tbl = \'tags\' AND to1.tagid = t.id AND to1.label = \'label\'
					INNER JOIN #__tags_object to2 ON to2.tbl = \'tags\' AND to2.label = \'parent\' AND to2.objectid = to1.objectid 
						AND to2.tagid = ' . $parent_id . ' 
					INNER JOIN #__tags t2 ON t2.id = to1.objectid
					WHERE t.raw_tag = ' . $this->database->quote($parent_label) . '
					ORDER BY CASE WHEN t2.raw_tag LIKE \'other%\' THEN 1 ELSE 0 END, t2.raw_tag'
				// get tags that are labeled focus areas that are not also a parent of another tag labeled as a focus area
				: 'SELECT DISTINCT t.raw_tag AS label, t2.id, t2.tag, t2.raw_tag 
					FROM #__tags t
					LEFT JOIN #__tags_object to1 ON to1.tagid = t.id AND to1.label = \'label\' AND to1.tbl = \'tags\'
					INNER JOIN #__tags t2 ON t2.id = to1.objectid
					WHERE t.tag IN (' . $labels . ') AND (
						SELECT COUNT(*) 
						FROM #__tags_object to2
						INNER JOIN #__tags_object to3 ON to3.tbl = \'tags\' AND to3.label = \'label\' AND to3.objectid = to2.tagid
						INNER JOIN #__tags t3 ON t3.id = to3.tagid AND t3.tag IN (' . $labels . ')
						WHERE to2.tbl = \'tags\' AND to2.label = \'parent\' AND to2.objectid = t2.id 
						LIMIT 1
					) = 0
					ORDER BY t.tag, CASE WHEN t2.raw_tag LIKE \'other%\' THEN 1 ELSE 0 END, t2.raw_tag'
		);
		$fas = $this->database->loadAssocList('raw_tag');
		foreach ($fas as &$fa) 
		{
			$fa['children'] = $this->_loadFocusAreas($type, $labels, $fa['id'], $fa['label']);
		}
		return $fas;
	}

	/**
	 * Show form for adding tags to an entry
	 * 
	 * @return     void
	 */
	public function step_tags($existing = array()) 
	{
		if ($this->view->getName() != 'steps')
		{
			$this->setView('steps', 'tags');
		}
		// Incoming
		$this->view->id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$this->view->id) 
		{
			JError::raiseError(500, JText::_('COM_CONTRIBUTE_NO_ID'));
			return;
		}

		if (!isset($this->progress))
		{
			// Check the progress
			$this->_checkProgress($this->view->id);

			$this->view->progress = $this->progress;
		}

		// Load the resource
		$this->view->row = new ResourcesResource($this->database);
		$this->view->row->load($this->view->id);

		$this->database->setQuery('SELECT type FROM #__resources WHERE id = ' . $this->view->id);
		$fas = $this->_loadFocusAreas($this->database->loadResult());
		$this->view->fas = array();
		foreach ($fas as $tag => $fa) 
		{
			if (!isset($this->view->fas[$fa['label']])) 
			{
				$this->view->fas[$fa['label']] = array();
			}
			$this->view->fas[$fa['label']][$tag] = $fa;
		}


		// Get all the tags on this resource
		$tagcloud = new ResourcesTags($this->database);
		$tags_men = $tagcloud->get_tags_on_object($this->view->id, 0, 0, 0, 0);

		$mytagarray = array();
		foreach ($tags_men as $tag_men) 
		{
			$mytagarray[] = $tag_men['raw_tag'];
		}
		$tags = implode(', ', $mytagarray);

		$etags = JRequest::getVar('tags', '');
		if (!$tags) 
		{
			$tags = $etags;
		}

		if (($err = JRequest::getInt('err', 0))) 
		{
			$this->setError(JText::_('Please select one of the focus areas.'));
		}

		// Output HTML
		$this->view->tags      = $tags;
		$this->view->next_step = $this->step + 1;
		$this->view->task      = 'draft';
		$this->view->existing  = $existing;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Show final review form for setting license and agreeing to terms of submission
	 * 
	 * @return     void
	 */
	public function step_review()
	{
		if ($this->view->getName() != 'steps')
		{
			$this->setView('steps', 'review');
		}
		if (!isset($this->view->database))
		{
			$this->view->config   = $this->config;
			$this->view->database = $this->database;
			$this->view->title    = $this->_title;
			$this->view->step     = $this->step;
			$this->view->steps    = $this->steps;
			$this->view->progress = $this->progress;
		}

		// Incoming
		$this->view->id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$this->view->id) 
		{
			JError::raiseError(500, JText::_('COM_CONTRIBUTE_NO_ID'));
			return;
		}

		// Push some needed styles to the tmeplate
		$this->_getStyles('com_resources');

		// Get some needed libraries
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'license.php');

		// Load resource info
		$this->view->resource = new ResourcesResource($this->database);
		$this->view->resource->load($this->view->id);

		if (!$this->juser->get('guest')) 
		{
			ximport('Hubzero_User_Helper');
			$xgroups = Hubzero_User_Helper::getGroups($this->juser->get('id'), 'all');
			// Get the groups the user has access to
			$this->view->usersgroups = $this->_getUsersGroups($xgroups);
		} 
		else 
		{
			$this->view->usersgroups = array();
		}

		// Output HTML
		$this->view->next_step = $this->step + 1;
		$this->view->task = 'submit';

		$rl = new ResourcesLicense($this->database);
		$this->view->licenses = $rl->getLicenses($this->view->id);

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Generate an array of just group aliases
	 * 
	 * @param      array $groups Array of group objects
	 * @return     array
	 */
	private function _getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) 
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) 
				{
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}

	/**
	 * Process the type step
	 * 
	 * @return     void
	 */
	public function step_type_process()
	{
		// do nothing
	}

	/**
	 * Process the compose step
	 * 
	 * @return     void
	 */
	public function step_compose_process()
	{
		// Initiate extended database class
		$row = new ResourcesResource($this->database);
		if (!$row->bind($_POST)) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		$isNew = $row->id < 1;

		$row->created    = ($row->created)    ? $row->created    : date('Y-m-d H:i:s');
		$row->created_by = ($row->created_by) ? $row->created_by : $this->juser->get('id');

		// Set status to "composing"
		if ($isNew) 
		{
			$row->published = 2;
		} 
		else 
		{
			$row->published = ($row->published) ? $row->published : 2;
		}
		$row->publish_up   = ($row->publish_up) ? $row->publish_up : date('Y-m-d H:i:s');
		$row->publish_down = '0000-00-00 00:00:00';
		$row->modified     = date('Y-m-d H:i:s');
		$row->modified_by  = $this->juser->get('id');

		$row->introtext = (trim($row->fulltxt)) ? Hubzero_View_Helper_Html::shortenText(trim($row->fulltxt), 500, 0) : trim($row->fulltxt);
		$row->fulltxt  = $this->_txtAutoP(trim($row->fulltxt), 1);

		// Get custom areas, add wrapper tags, and compile into fulltxt
		$type = new ResourcesType($this->database);
		$type->load($row->type);

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
		$elements = new ResourcesElements(array(), $type->customFields);
		$schema = $elements->getSchema();

		$fields = array();
		if (is_object($schema))
		{
			foreach ($schema->fields as $field)
			{
				$fields[$field->name] = $field;
			}
		}

		$nbtag = (isset($_POST['nbtag'])) ? $_POST['nbtag'] : array();
		$found = array();
		foreach ($nbtag as $tagname => $tagcontent)
		{
			$f = '';

			$row->fulltxt .= "\n" . '<nb:' . $tagname . '>';
			if (is_array($tagcontent))
			{
				$c = count($tagcontent);
				$num = 0;
				foreach ($tagcontent as $key => $val)
				{
					if (trim($val))
					{
						$num++;
					}
					$row->fulltxt .= '<' . $key . '>' . trim($val) . '</' . $key . '>';
				}
				if ($c == $num)
				{
					$f = 'found';
				}
			}
			else 
			{
				$f = trim($tagcontent);
				if ($f)
				{
					$row->fulltxt .= (isset($fields[$tagname]) && $fields[$tagname]->type == 'textarea') ? $this->_txtAutoP(trim($tagcontent), 1) : trim($tagcontent);
				}
			}
			$row->fulltxt .= '</nb:' . $tagname . '>' . "\n";

			if (!$f && isset($fields[$tagname]) && $fields[$tagname]->required) 
			{
				$this->setError(JText::sprintf('COM_CONTRIBUTE_REQUIRED_FIELD_CHECK', $fields[$tagname]->label));
			}

			$found[] = $tagname;
		}

		foreach ($fields as $field)
		{
			if (!in_array($field->name, $found) && $field->required)
			{
				$found[] = $field->name;
				$this->setError(JText::sprintf('COM_CONTRIBUTE_REQUIRED_FIELD_CHECK', $field->label));
			}
		}

		$row->title = preg_replace('/\s+/', ' ', $row->title);
		$row->title = $this->_txtClean($row->title);

		// Strip any scripting there may be
		if (trim($row->fulltxt)) 
		{
			$row->fulltxt   = $this->_txtClean($row->fulltxt);
			//$row->fulltxt   = $this->_txtAutoP($row->fulltxt, 1);
			$row->footertext = $this->_txtClean($row->footertext);
			//$row->introtext  = Hubzero_View_Helper_Html::shortenText($row->fulltxt, 500, 0);
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
		}

		// Fall back to step if any errors found
		if ($this->getError())
		{
			$this->step--;
			$this->view->step = $this->step;
			$this->view->setLayout('compose');
			$this->step_compose($row);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError(JText::_('Error: Failed to store changes.'));
			$this->step--;
			$this->view->step = $this->step;
			$this->view->setLayout('compose');
			$this->step_compose($row);
			return;
		}

		// Checkin the resource
		$row->checkin();

		// Is it a new resource?
		if ($isNew) 
		{
			// Get the resource ID
			if (!$row->id) 
			{
				$row->id = $row->insertid();
			}

			// Automatically attach this user as the first author
			JRequest::setVar('pid', $row->id);
			JRequest::setVar('id', $row->id);
			JRequest::setVar('authid', $this->juser->get('id'));

			include_once(JPATH_COMPONENT . DS . 'controllers' . DS . 'authors.php');
			$authors = new ResourcesControllerAuthors();
			$authors->saveTask(0);
		}
	}

	/**
	 * Process the attach step
	 * 
	 * @return     void
	 */
	public function step_attach_process()
	{
		// do nothing
	}

	/**
	 * Process the authors step
	 * 
	 * @return     void
	 */
	public function step_authors_process()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id) 
		{
			return;
		}

		// Load the resource
		$row = new ResourcesResource($this->database);
		$row->load($id);

		// Set the group and access level
		$row->group_owner = JRequest::getVar('group_owner', '');
		$row->access = JRequest::getInt('access', 0);

		if ($row->access > 0 && !$row->group_owner) 
		{
			$this->setError(JText::_('Please select a group to restrict access to.'));
			$this->step--;
			$this->view->step = $this->step;
			$this->view->setLayout('authors');
			$this->step_authors();
			return;
		}

		// Check content
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			$this->step--;
			$this->view->step = $this->step;
			$this->view->setLayout('authors');
			$this->step_authors();
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError(JText::_('Error: Failed to store changes.'));
			$this->step--;
			$this->view->step = $this->step;
			$this->view->setLayout('authors');
			$this->step_authors();
			return;
		}
	}

	/**
	 * Process the tags step
	 * 
	 * @return     void
	 */
	public function step_tags_process() 
	{
		$id = JRequest::getInt('id', 0);

		$user =& JFactory::getUser();
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$this->database->setQuery(
				'SELECT 1 FROM #__author_assoc WHERE authorid = ' . $this->juser->id . ' AND subtable = \'resources\' AND subid = ' . $id . '
				UNION 
				SELECT 1 FROM #__resources WHERE id = ' . $id . ' AND (created_by = ' . $this->juser->id . ' OR modified_by = ' . $this->juser->id . ')
				UNION
				SELECT 1 FROM #__users u 
				INNER JOIN #__core_acl_aro caa ON caa.section_value = \'users\' AND caa.value = u.id 
				INNER JOIN #__core_acl_groups_aro_map cagam ON cagam.aro_id = caa.id 
				INNER JOIN #__core_acl_aro_groups caag ON caag.id = cagam.group_id AND (caag.name = \'Super Administrator\' OR caag.name = \'Administrator\')
				WHERE u.id = ' . $this->juser->id
			);
		}
		else
		{
			$this->database->setQuery(
				'SELECT 1 FROM #__author_assoc WHERE authorid = ' . $this->juser->id . ' AND subtable = \'resources\' AND subid = ' . $id . '
				UNION 
				SELECT 1 FROM #__resources WHERE id = ' . $id . ' AND (created_by = ' . $this->juser->id . ' OR modified_by = ' . $this->juser->id . ')
				UNION
				SELECT 1 FROM #__users u 
				INNER JOIN #__user_usergroup_map cagam ON cagam.user_id = u.id 
				INNER JOIN #__usergroups caag ON caag.id = cagam.group_id AND (caag.title = \'Super Administrator\' OR caag.title = \'Administrator\')
				WHERE u.id = ' . $this->juser->id
			);
		}
		if (!$this->database->loadResult()) 
		{
			JError::raiseError(403, 'Forbidden');
			return;
		}

		$tags = preg_split('/,\s*/', $_POST['tags']);
		$push = array();
		$map  = array();

		$this->database->setQuery(
			'SELECT fa.tag_id, t.raw_tag, fa.mandatory_depth AS minimum_depth, 0 AS actual_depth
			FROM #__focus_areas fa
			INNER JOIN #__tags t ON t.id = fa.tag_id
			INNER JOIN #__focus_area_resource_type_rel rtr ON rtr.focus_area_id = fa.id
			INNER JOIN #__resource_types rt ON rt.id = rtr.resource_type_id
			INNER JOIN #__resources r ON r.type = rt.id AND r.id = ' . $id . '
			WHERE fa.mandatory_depth IS NOT NULL AND fa.mandatory_depth > 0'
		);
		$fas = $this->database->loadAssocList('raw_tag');
		foreach ($_POST as $k => $vs) 
		{
			if (!preg_match('/^tagfa/', $k)) 
			{
				continue;
			}
			if (!is_array($vs)) 
			{
				$vs = array($vs);
			}
			foreach ($vs as $v) 
			{
				$norm_tag = preg_replace('/[^-_a-z0-9]/', '', strtolower($v));
				if (isset($map[$norm_tag])) 
				{
					continue;
				}
				$this->database->setQuery(
					'SELECT t2.raw_tag AS fa, t2.id AS label_id, t.id FROM #__tags t
					INNER JOIN #__tags_object to1 ON to1.tbl = \'tags\' AND to1.label = \'label\' AND to1.objectid = t.id
					INNER JOIN #__tags t2 ON t2.id = to1.tagid
					INNER JOIN #__focus_areas fa ON fa.tag_id = to1.tagid
					WHERE t.tag = ' . $this->database->quote($v)
				);
				if (($row = $this->database->loadAssoc())) 
				{
					$push[] = array($v, $norm_tag, $row['fa'], $row['id'], $row['label_id']);
					$map[$norm_tag] = true;
				}
			}
		}

		$filtered = array();
		// only accept focus areas with parents if their parent is also checked
		foreach ($push as $idx => $tag) 
		{
			$this->database->setQuery(
				'SELECT t.tag, t.id
				FROM #__tags_object to1
				INNER JOIN #__tags t ON t.id = to1.tagid
				INNER JOIN #__tags_object to2 ON to2.tagid = ' . $tag[4] . ' AND to2.tbl = \'tags\' AND to2.objectid = to1.tagid
				WHERE to1.objectid = ' . $tag[3] . ' AND to1.tbl = \'tags\' AND to1.label = \'parent\''
			);
			$any_match = false;
			$parent = array();
			$possible_parents = $this->database->loadAssocList();
			foreach ($possible_parents as $par) 
			{
				if (isset($map[$par['tag']])) 
				{
					$parent[] = $par;
					$any_match = true;
				}
			}
			if (!$possible_parents || $any_match) 
			{
				$filtered[] = $tag;
				$parent_id = array();
				foreach ($parent as $par) 
				{
					$parent_id[] = $par['id'];
				}
				if (isset($fas[$tag[2]]) && $fas[$tag[2]]['actual_depth'] < $fas[$tag[2]]['minimum_depth']) 
				{
					// count depth if necessary to determine whether focus area constraints are satisified
					for ($depth = $parent ? 2 : 1; $parent_id && $fas[$tag[2]]['actual_depth'] < $fas[$tag[2]]['minimum_depth'] && $depth < $fas[$tag[2]]['minimum_depth']; ++$depth) 
					{
						$this->database->setQuery(
							'SELECT t.id
							FROM #__tags_object to1
							INNER JOIN #__tags t ON t.id = to1.tagid
							INNER JOIN #__tags_object to2 ON to2.tagid = ' . $tag[4] . ' AND to2.tbl = \'tags\' AND to2.objectid = to1.tagid
							WHERE to1.objectid IN (' . implode(',', $parent_id) . ') AND to1.tbl = \'tags\' AND to1.label = \'parent\''
						);
						$parent_id = $this->database->loadResultArray();
					}
					$fas[$tag[2]]['actual_depth'] = max($depth, $fas[$tag[2]]['actual_depth']);
				}
			}
			else 
			{
				unset($map[$tag[1]]);
			}
		}
		$push = $filtered;

		foreach ($tags as $tag) 
		{
			$norm_tag = preg_replace('/[^-_a-z0-9]/', '', strtolower($tag));

			if (!$norm_tag || isset($map[$norm_tag])) 
			{
				continue;
			}
			$push[] = array($tag, $norm_tag, null);
			$map[$norm_tag] = true;
		}
		foreach ($push as $idx => $tag) 
		{
			$this->database->setQuery('SELECT raw_tag FROM #__tags WHERE tag = \'' . $tag[1] . '\'');
			if (($raw_tag = $this->database->loadResult())) 
			{
				$push[$idx][0] = $raw_tag;
			}
		}

		foreach ($fas as $lbl => $fa) 
		{
			if ($fa['actual_depth'] < $fa['minimum_depth']) 
			{
				$this->setError(
					$fa['minimum_depth'] == 1 
						? 'Please ensure you have made a ' . $lbl . ' selection'
						: 'Please make selections for "' . $lbl . '" to a depth of at least ' . $fa['minimum_depth']
				);
				--$this->step;
				$this->view->step = $this->step;
				$this->view->setLayout('tags');
				return $this->step_tags($push);
			}
		}

		$tags = array();
		foreach ($push as $tag) 
		{
			$tags[] = $tag[0];
		}
		$tags = implode(', ', $tags);
		
		//$rt = new ResourcesTags($this->database);
		//$rt->tag_object($this->juser->get('id'), $id, $tags, 1, 1);
		$this->database->execute('DELETE FROM #__tags_object WHERE tbl = \'resources\' AND objectid = ' . $id);
		foreach ($push as $tag) 
		{
			$this->database->setQuery('SELECT id FROM #__tags WHERE tag = ' . $this->database->quote($tag[1]));
			if (!($tid = $this->database->loadResult())) 
			{
				$this->database->setQuery('SELECT tag_id FROM #__tags_substitute WHERE tag = ' . $this->database->quote($tag[1]));
				if (!($tid = $this->database->loadResult())) 
				{
					$this->database->execute('INSERT INTO #__tags(tag, raw_tag) VALUES (' . $this->database->quote($tag[1]) . ', ' . $this->database->quote($tag[0]) . ')');
					$tid = $this->database->insertid();
				}
			}
			$this->database->execute('INSERT INTO #__tags_object(tbl, objectid, tagid, label) VALUES (\'resources\', ' . $id . ', ' . $tid . ', ' . ($tag[2] ? $this->database->quote($tag[2]) : 'NULL') . ')');
		}
	}

	/**
	 * Final submission
	 * 
	 * @return     void
	 */
	public function submitTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id) 
		{
			JError::raiseError(500, JText::_('COM_CONTRIBUTE_NO_ID'));
			return;
		}

		// Load resource info
		$resource = new ResourcesResource($this->database);
		$resource->load($id);

		// Set a flag for if the resource was already published or not
		$published = 0;
		if ($resource->published != 2) 
		{
			$published = 1;
		}

		// Check if a newly submitted resource was authorized to be published
		$authorized = JRequest::getInt('authorization', 0);
		if (!$authorized && !$published) 
		{
			$this->setError(JText::_('COM_CONTRIBUTE_CONTRIBUTION_NOT_AUTHORIZED'));
			$this->_checkProgress($id);
			$this->step_review();
			return;
		}

		// Is this a newly submitted resource?
		if (!$published) 
		{
			// 0 = unpublished, 1 = published, 2 = composing, 3 = pending (submitted), 4 = deleted
			// Are submissions auto-approved?
			if ($this->config->get('autoapprove') == 1) 
			{
				// Set status to published
				$resource->published = 1;
			} 
			else 
			{
				$apu = $this->config->get('autoapproved_users');
				$apu = explode(',', $apu);
				$apu = array_map('trim', $apu);

				if (in_array($this->juser->get('username'), $apu)) 
				{
					// Set status to published
					$resource->published = 1;
				} 
				else 
				{
					// Set status to pending review (submitted)
					$resource->published = 3;
				}
			}

			// Get the resource's contributors
			$helper = new ResourcesHelper($id, $this->database);
			$helper->getCons();

			$contributors = $helper->_contributors;

			if (!$contributors || count($contributors) <= 0) 
			{
				$this->setError(JText::_('COM_CONTRIBUTE_CONTRIBUTION_HAS_NO_AUTHORS'));
				$this->_checkProgress($id);
				$this->step_review();
				return;
			}
		}

		// Is this resource licensed under Creative Commons?
		if ($this->config->get('cc_license')) 
		{
			if (($license = JRequest::getVar('license', ''))) 
			{
				if ($license == 'custom')
				{
					$license .= $resource->id;

					$licenseText = JRequest::getVar('license-text', '');
					if ($licenseText == '[ENTER LICENSE HERE]') 
					{
						$this->setError(JText::_('Please enter a license.'));
						$this->_checkProgress($id);
						$this->step_review();
						return;
					}

					include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'license.php');

					$rl = new ResourcesLicense($this->database);
					$rl->load($license);
					$rl->name = $license;
					$rl->text = $licenseText;
					$rl->info = $resource->id;
					$rl->check();
					$rl->store();
				}
				
				$params = explode("\n", $resource->params);
				$newparams = array();
				$flag = 0;

				// Loop through the params and check if a license param exist
				foreach ($params as $param)
				{
					$p = explode('=', $param);
					if ($p[0] == 'license') 
					{
						$flag = 1;
						$p[1] = $license;
					}
					$param = implode('=', $p);
					$newparams[] = $param;
				}

				// No license param so add it
				if ($flag == 0) 
				{
					$newparams[] = 'license=' . $license;
				}

				// Overwrite the resource's params with the new params
				$resource->params = implode("\n", $newparams);
			}
		}

		// Save and checkin the resource
		$resource->store();
		$resource->checkin();

		// If a previously published resource, redirect to the resource page
		if ($published == 1) 
		{
			if ($resource->alias) 
			{
				$url = JRoute::_('index.php?option=com_resources&alias=' . $resource->alias);
			} 
			else 
			{
				$url = JRoute::_('index.php?option=com_resources&id=' . $resource->id);
			}
			$this->setRedirect($url);
			return;
		}

		$this->_getStyles($this->_option, $this->_controller . '.css');

		// Output HTML
		$this->setView($this->_controller, 'thanks');
		$this->view->title    = $this->_title;
		$this->view->config   = $this->config;
		$this->view->resource = $resource;
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Show a confirmation form for deleting a contribution
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=new')
			);
			return;
		}

		// Incoming step
		$step = JRequest::getVar('step', 1);

		// Perform step
		switch ($step)
		{
			case 1:
				$this->_getStyles($this->_option, $this->_controller . '.css');

				// Check progress
				$this->_checkProgress($id);

				// Load the resource
				$this->view->row = new ResourcesResource($this->database);
				$this->view->row->load($id);
				$this->view->row->typetitle = $this->view->row->getTypeTitle(0);

				// Output HTML
				$this->view->title    = $this->_title;
				$this->view->step     = 'discard';
				$this->view->steps    = $this->steps;
				$this->view->id       = $id;
				$this->view->progress = $this->progress;
				if ($this->getError()) 
				{
					foreach ($this->getErrors() as $error)
					{
						$this->view->setError($error);
					}
				}
				$this->view->display();
			break;

			case 2:
				// Incoming confirmation flag
				$confirm = JRequest::getVar('confirm', '', 'post');

				// Did they confirm the deletion?
				if ($confirm != 'confirmed') 
				{
					$this->setError(JText::_('Please confirm.'));
					/*$this->setRedirect(
						JRoute::_('index.php?option=' . $this->_option)
					);*/
					$this->_getStyles($this->_option, $this->_controller . '.css');

					// Check progress
					$this->_checkProgress($id);

					// Load the resource
					$this->view->row = new ResourcesResource($this->database);
					$this->view->row->load($id);
					$this->view->row->typetitle = $this->view->row->getTypeTitle(0);

					// Output HTML
					$this->view->title    = $this->_title;
					$this->view->step     = 'discard';
					$this->view->steps    = $this->steps;
					$this->view->id       = $id;
					$this->view->progress = $this->progress;
					if ($this->getError()) 
					{
						foreach ($this->getErrors() as $error)
						{
							$this->view->setError($error);
						}
					}
					$this->view->display();
					return;
				}

				// Load the resource
				$resource = new ResourcesResource($this->database);
				$resource->load($id);

				// Check if the resource was "published"
				if ($resource->published == 1) 
				{
					// It was, so we can only mark it as "deleted"
					if (!$this->_markRemovedContribution($id)) 
					{
						JError::raiseError(500, $this->getError());
						return;
					}
				} 
				else 
				{
					// It wasn't. Attempt to delete the resource
					if (!$this->_deleteContribution($id)) 
					{
						JError::raiseError(500, $this->getError());
						return;
					}
				}

				// Redirect to the start page
				$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_option . '&task=new')
				);
			break;
		}
	}

	/**
	 * Retract a submission
	 * 
	 * @return     void
	 */
	public function retractTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&task=new')
			);
			return;
		}

		// Load the resource
		$resource = new ResourcesResource($this->database);
		$resource->load($id);

		// Check if it's in pending status
		if ($resource->published == 3) 
		{
			// Set it back to "draft" status
			$resource->published = 2;
			// Save changes
			$resource->store();
		}

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&task=new')
		);
	}

	/**
	 * Set the state on an item to "deleted" (4)
	 * 
	 * @param      integer $id Resource ID
	 * @return     boolean False if errors, True on success
	 */
	private function _markRemovedContribution($id)
	{
		// Make sure we have a record to pull
		if (!$id) 
		{
			$this->setError(JText::_('COM_CONTRIBUTE_NO_ID'));
			return false;
		}

		// Load resource info
		$row = new ResourcesResource($this->database);
		$row->load($id);

		// Mark resource as deleted
		$row->published = 4;
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return false;
		}

		// Return success
		return true;
	}

	/**
	 * Delete a contribution and associated content
	 * 
	 * @param      integer $id Resource ID
	 * @return     boolean False if errors, True on success
	 */
	private function _deleteContribution($id)
	{
		// Make sure we have a record to pull
		if (!$id) 
		{
			$this->setError(JText::_('COM_CONTRIBUTE_NO_ID'));
			return false;
		}

		jimport('joomla.filesystem.folder');

		// Load resource info
		$row = new ResourcesResource($this->database);
		$row->load($id);

		// Get the resource's children
		$helper = new ResourcesHelper($id, $this->database);
		$helper->getChildren();
		$children = $helper->children;

		// Were there any children?
		if ($children) 
		{
			// Loop through each child and delete its files and associations
			foreach ($children as $child)
			{
				// Skip standalone children
				if ($child->standalone == 1) 
				{
					continue;
				}

				// Get path and delete directories
				if ($child->path != '') 
				{
					$listdir = $child->path;
				} 
				else 
				{
					// No stored path, derive from created date
					$listdir = $this->_buildPathFromDate($child->created, $child->id, '');
				}

				// Build the path
				$path = $this->_buildUploadPath($listdir, '');

				// Check if the folder even exists
				if (!is_dir($path) or !$path) 
				{
					$this->setError(JText::_('COM_CONTRIBUTE_DIRECTORY_NOT_FOUND'));
				} 
				else 
				{
					// Attempt to delete the folder
					if (!JFolder::delete($path)) 
					{
						$this->setError(JText::_('COM_CONTRIBUTE_UNABLE_TO_DELETE_DIRECTORY'));
					}
				}

				// Delete associations to the resource
				$row->deleteExistence($child->id);

				// Delete the resource
				$row->delete($child->id);
			}
		}

		// Get path and delete directories
		if ($row->path != '') 
		{
			$listdir = $row->path;
		} 
		else 
		{
			// No stored path, derive from created date		
			$listdir = $this->_buildPathFromDate($row->created, $id, '');
		}

		// Build the path
		$path = $this->_buildUploadPath($listdir, '');

		// Check if the folder even exists
		if (!is_dir($path) or !$path) 
		{
			$this->setError(JText::_('COM_CONTRIBUTE_DIRECTORY_NOT_FOUND'));
		} 
		else 
		{
			// Attempt to delete the folder
			if (!JFolder::delete($path)) 
			{
				$this->setError(JText::_('COM_CONTRIBUTE_UNABLE_TO_DELETE_DIRECTORY'));
			}
		}

		$row->id = $id;

		// Delete associations to the resource
		$row->deleteExistence();

		// Delete the resource
		$row->delete();

		// Return success (null)
		return true;
	}

	/**
	 * Build the absolute path to a resource's file upload
	 * 
	 * @param      string $listdir Primary upload directory
	 * @param      string $subdir  Sub directory of $listdir
	 * @return     string 
	 */
	private function _buildUploadPath($listdir, $subdir='')
	{
		if ($subdir) 
		{
			$subdir = DS . trim($subdir, DS);
		}

		// Get the configured upload path
		$base = DS . trim($this->config->get('uploadpath', '/site/resources'), DS);

		// Make sure the path doesn't end with a slash
		$listdir = DS . trim($listdir, DS);

		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base)) == $base) 
		{
			// Yes - ... this really shouldn't happen
		} 
		else 
		{
			// No - append it
			$listdir = $base . $listdir;
		}

		// Build the path
		return JPATH_ROOT . $listdir . $subdir;
	}

	/**
	 * Check if the type step is completed
	 * 
	 * @param      integer $id Resource ID
	 * @return     void
	 */
	public function step_type_check($id)
	{
		// do nothing
	}

	/**
	 * Check if the compose step is completed
	 * 
	 * @param      integer $id Resource ID
	 * @return     integer # > 1 = step completed, 0 = not completed
	 */
	public function step_compose_check($id)
	{
		return $id;
	}

	/**
	 * Check if the attach step is completed
	 * 
	 * @param      integer $id Resource ID
	 * @return     integer # > 1 = step completed, 0 = not completed
	 */
	public function step_attach_check($id)
	{
		if ($id) 
		{
			$ra = new ResourcesAssoc($this->database);
			$total = $ra->getCount($id);
		} 
		else 
		{
			$total = 0;
		}
		return $total;
	}

	/**
	 * Check if the authors step is completed
	 * 
	 * @param      integer $id Resource ID
	 * @return     integer # > 1 = step completed, 0 = not completed
	 */
	public function step_authors_check($id)
	{
		if ($id) 
		{
			$rc = new ResourcesContributor($this->database);
			$contributors = $rc->getCount($id, 'resources');
		} 
		else 
		{
			$contributors = 0;
		}

		return $contributors;
	}

	/**
	 * Check if the tags step is completed
	 * 
	 * @param      integer $id Resource ID
	 * @return     integer 1 = step completed, 0 = not completed
	 */
	public function step_tags_check($id)
	{
		$rt = new ResourcesTags($this->database);
		$tags = $rt->getTags($id);

		if (count($tags) > 0) 
		{
			return 1;
		} 
		else 
		{
			return 0;
		}
	}

	/**
	 * Check if the review step is completed
	 * 
	 * @param      integer $id Resource ID
	 * @return     integer 1 = step completed, 0 = not completed
	 */
	public function step_review_check($id)
	{
		$row = new ResourcesResource($this->database);
		$row->load($id);

		if ($row->published == 1) 
		{
			return 1;
		} 
		else 
		{
			return 0;
		}
	}

	/**
	 * Convert Microsoft characters and strip disallowed content
	 * This includes script tags, HTML comments, xhubtags, and style tags
	 * 
	 * @param      string &$text Text to clean
	 * @return     string
	 */
	private function _txtClean(&$text)
	{
		// Handle special characters copied from MS Word
		$text = str_replace('“','"', $text);
		$text = str_replace('”','"', $text);
		$text = str_replace("’","'", $text);
		$text = str_replace("‘","'", $text);

		$text = preg_replace('/{kl_php}(.*?){\/kl_php}/s', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace("'<style[^>]*>.*?</style>'si", '', $text);
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);

		return $text;
	}

	/**
	 * Try to determine and convert groups of text to paragraphs
	 * Performs a little entity conversion first to normalize everything to UTF8
	 * 
	 * @param      string  $pee Text to convert
	 * @param      integer $br  Preserve break tags?
	 * @return     string 
	 */
	private function _txtAutoP($pee, $br = 1)
	{
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		foreach ($trans_tbl as $k => $v)
		{
			if ($k != '<' && $k != '>' && $k != '"' && $k != "'") 
			{
				$ttr[utf8_encode($k)] = $v;
			}
		}
		$pee = strtr($pee, $ttr);

		$ent = array(
			'Ć'=>'&#262;',
			'ć'=>'&#263;',
			'Č'=>'&#268;',
			'č'=>'&#269;',
			'Đ'=>'&#272;',
			'đ'=>'&#273;',
			'Š'=>'&#352;',
			'š'=>'&#353;',
			'Ž'=>'&#381;',
			'ž'=>'&#382;'
		);

		$pee = strtr($pee, $ent);

		// converts paragraphs of text into xhtml
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		$pee = preg_replace('!(<(?:table|ul|ol|li|pre|form|blockquote|h[1-6])[^>]*>)!', "\n$1", $pee); // Space things out a little
		$pee = preg_replace('!(</(?:table|ul|ol|li|pre|form|blockquote|h[1-6])>)!', "$1\n", $pee); // Space things out a little
		$pee = preg_replace("/(\r\n|\r)/", "\n", $pee); // cross-platform newlines 
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end 
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace 
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $pee);
		if ($br)
		{
			$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		}
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|th|pre|td|ul|ol)>)!', '$1', $pee);
		//$pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);

		return $pee;
	}

	/**
	 * Remove paragraph tags and break tags
	 * 
	 * @param      string $pee Text to unparagraph
	 * @return     string
	 */
	public function _txtUnpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}

	/**
	 * Build a path from a creation date (0000-00-00 00:00:00)
	 * 
	 * @param      string  $date Resource created date
	 * @param      integer $id   Resource ID
	 * @param      string  $base Base path to prepend
	 * @return     string
	 */
	private function _buildPathFromDate($date, $id, $base='')
	{
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs)) 
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		if ($date) 
		{
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		} 
		else 
		{
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = Hubzero_View_Helper_Html::niceidformat($id);

		$path = $base . DS . $dir_year . DS . $dir_month . DS . $dir_id;

		return $path;
	}
}

