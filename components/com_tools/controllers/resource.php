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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Tool classes
 */
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'tool.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'group.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'author.php');

/**
 * Resource classes
 */
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'assoc.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'contributor.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'tags.php');

/**
 * Controller class for contributing a tool
 */
class ToolsControllerResource extends Hubzero_Controller
{
	/**
	 * Determines task being called and attempts to execute it
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();

		// Load the com_resources component config
		$rconfig =& JComponentHelper::getParams('com_resources');
		$this->rconfig = $rconfig;

		parent::execute();
	}

	/**
	 * Display forms for editing/creating a reosurce
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		ximport('Hubzero_Tool_Version');

		// Incoming
		$alias   = JRequest::getVar('app', '');
		$version = JRequest::getVar('editversion', 'dev');
		$step    = JRequest::getInt('step', 1);

		// Load the tool
		$obj = new Tool($this->database);
		$this->_toolid = $obj->getToolId($alias);

		if (!$this->_toolid) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=pipeline')
			);
			return;
		}

		// make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid)) 
		{
			JError::raiseError(403, JText::_('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		$nextstep = $step + 1;

		// get tool version (dev or current) information
		$obj->getToolStatus($this->_toolid, $this->_option, $status, $version);

		// get resource information
		$row = new ResourcesResource($this->database);
		$row->loadAlias($alias);
		$row->alias = ($row->alias) ? $row->alias : $alias;
		if (!$status['fulltxt']) 
		{ 
			$status['fulltxt'] = $row->fulltxt;
		}

		// process first step
		if ($nextstep == 3 && isset($_POST['nbtag'])) 
		{
		    $hztv = Hubzero_Tool_VersionHelper::getToolRevision($this->_toolid, $version);

			$objV = new ToolVersion($this->database);
			if (!$objV->bind($_POST)) 
			{
				$this->setError($objV->getError());
				return;
			}

			$body = $this->txtClean($_POST['fulltxt']);
			if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $body)) 
			{
				// Do nothing
				$status['fulltxt'] = trim(stripslashes($body));
			} 
			else 
			{
				// Wiki format will be used
				$status['fulltxt'] = JRequest::getVar('fulltxt', $status['fulltxt'], 'post');
			}

			// Get custom areas, add wrapper tags, and compile into fulltxt
			$type = new ResourcesType($this->database);
			$type->load($row->type);

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
			$elements = new ResourcesElements(array(), $type->customFields);
			$schema = $elements->getSchema();

			$fields = array();
			foreach ($schema->fields as $field)
			{
				$fields[$field->name] = $field;
			}

			$nbtag = $_POST['nbtag'];
			$found = array();
			foreach ($nbtag as $tagname => $tagcontent)
			{
				$f = '';

				$status['fulltxt'] .= "\n" . '<nb:' . $tagname . '>';
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
						$status['fulltxt'] .= '<' . $key . '>' . trim($val) . '</' . $key . '>';
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
						$status['fulltxt'] .= trim($tagcontent);
					}
				}
				$status['fulltxt'] .= '</nb:' . $tagname . '>' . "\n";

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

			ximport('Hubzero_View_Helper_Html');
			$hztv->fulltxt    = $objV->fulltxt    = $status['fulltxt'];
			$hztv->description = $objV->description = Hubzero_View_Helper_Html::shortenText(JRequest::getVar('description', $status['description'], 'post'), 500, 0);
			$hztv->title       = $objV->title       = Hubzero_View_Helper_Html::shortenText(preg_replace('/\s+/', ' ', JRequest::getVar('title', $status['title'], 'post')), 500, 0);

			if (!$hztv->update()) 
			{
				JError::raiseError(500, JText::_('COM_TOOLS_Error updating tool tables.'));
				return;
			} 
			else 
			{
				// get updated tool status
				$obj->getToolStatus($this->_toolid, $this->_option, $status, $version);
			}

			if ($version == 'dev') 
			{
				// update resource page
				$this->updatePage($row->id, $status);
			}
		}

		// Group access
		//$accesses = array('Public', 'Registered', 'Special', 'Protected', 'Private');
		//$lists = array();
		//$lists['access'] = ToolsHelperHtml::selectAccess($accesses, $row->access);
		//ximport('Hubzero_User_Helper');
		//$groups = Hubzero_User_Helper::getGroups($this->juser->get('id'), 'members');

		// get authors
		$objA = new ToolAuthor($this->database);
		$authors = ($version == 'current') ? $objA->getToolAuthors($version, $row->id, $status['toolname']) : array();

		// Tags
		$tags  = JRequest::getVar('tags', '', 'post');
		$tagfa = JRequest::getVar('tagfa', '', 'post');

		// Get any HUB focus areas
		// These are used where any resource is required to have one of these tags
		$tconfig =& JComponentHelper::getParams('com_tags');
		$fa1  = $tconfig->get('focus_area_01');
		$fa2  = $tconfig->get('focus_area_02');
		$fa3  = $tconfig->get('focus_area_03');
		$fa4  = $tconfig->get('focus_area_04');
		$fa5  = $tconfig->get('focus_area_05');
		$fa6  = $tconfig->get('focus_area_06');
		$fa7  = $tconfig->get('focus_area_07');
		$fa8  = $tconfig->get('focus_area_08');
		$fa9  = $tconfig->get('focus_area_09');
		$fa10 = $tconfig->get('focus_area_10');

		// Instantiate our tag object
		$tagcloud = new ResourcesTags($this->database);

		// Normalize the focus areas
		$tagfa1  = $tagcloud->normalize_tag($fa1);
		$tagfa2  = $tagcloud->normalize_tag($fa2);
		$tagfa3  = $tagcloud->normalize_tag($fa3);
		$tagfa4  = $tagcloud->normalize_tag($fa4);
		$tagfa5  = $tagcloud->normalize_tag($fa5);
		$tagfa6  = $tagcloud->normalize_tag($fa6);
		$tagfa7  = $tagcloud->normalize_tag($fa7);
		$tagfa8  = $tagcloud->normalize_tag($fa8);
		$tagfa9  = $tagcloud->normalize_tag($fa9);
		$tagfa10 = $tagcloud->normalize_tag($fa10);

		// process new tags
		if ($tags or $tagfa) 
		{
			$newtags = '';
			if ($tagfa) 
			{ 
				$newtags = $tagfa . ', ';
			}
			if ($tags) 
			{ 
				$newtags .= $tags;
			}
			$tagcloud->tag_object($this->juser->get('id'), $row->id, $newtags, 1, 0);
		}

		// Get all the tags on this resource
		$tags_men = $tagcloud->get_tags_on_object($row->id, 0, 0, 0, 0);
		$mytagarray = array();
		$fas = array($tagfa1, $tagfa2, $tagfa3, $tagfa4, $tagfa5, $tagfa6, $tagfa7, $tagfa8, $tagfa9, $tagfa10);
		$fats = array();
		if ($fa1) 
		{
			$fats[$fa1] = $tagfa1;
		}
		if ($fa2) 
		{
			$fats[$fa2] = $tagfa2;
		}
		if ($fa3) 
		{
			$fats[$fa3] = $tagfa3;
		}
		if ($fa4) 
		{
			$fats[$fa4] = $tagfa4;
		}
		if ($fa5) 
		{
			$fats[$fa5] = $tagfa5;
		}
		if ($fa6) 
		{
			$fats[$fa6] = $tagfa6;
		}
		if ($fa7) 
		{
			$fats[$fa7] = $tagfa7;
		}
		if ($fa8) 
		{
			$fats[$fa8] = $tagfa8;
		}
		if ($fa9) 
		{
			$fats[$fa9] = $tagfa9;
		}
		if ($fa10) 
		{
			$fats[$fa10] = $tagfa10;
		}

		// Loop through all the tags and pull out the focus areas - those will be displayed differently
		foreach ($tags_men as $tag_men)
		{
			if (in_array($tag_men['tag'], $fas)) 
			{
				$tagfa = $tag_men['tag'];
			} 
			else 
			{
				$mytagarray[] = $tag_men['raw_tag'];
			}
		}
		$tags = implode(', ', $mytagarray);

		// Push CSS to the document
		$this->_getStyles($this->_option, 'assets/css/' . $this->_controller . '.css');
		// Push some scripts to the document
		$this->_getScripts('assets/js/' . $this->_controller);

		// Set the document title
		$this->view->title = JText::_(strtoupper($this->_name)) . ': ' . JText::_('COM_TOOLS_EDIT_TOOL_PAGE') . ' (' . $status['toolname'] . ')';
		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the document pathway (breadcrumbs)
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}
		if (count($pathway->getPathWay()) <= 1) 
		{
			$pathway->addItem(
				JText::_('COM_TOOLS_STATUS') . ' ' . JText::_('COM_TOOLS_FOR') . ' ' . $status['toolname'], 
				'index.php?option=' . $this->_option . '&controller=pipeline&task=status&app=' . $alias
			);
			$pathway->addItem(
				JText::_('COM_TOOLS_EDIT_TOOL_PAGE'), 
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $alias . '&task=start&step=1'
			);
		}

		$this->view->row     = $row;
		$this->view->step    = $step;
		$this->view->version = $version;
		$this->view->status  = $status;
		$this->view->tags    = $tags;
		$this->view->tagfa   = $tagfa;
		$this->view->fats    = $fats;
		$this->view->authors = $authors;

		// Pass error messages to the view
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Update the associated resource page for this tool
	 * 
	 * @param      integer $rid       Resource ID
	 * @param      array   $status    Fields to update
	 * @param      integer $published Published state
	 * @param      integer $newtool   Updating for a new tool?
	 * @return     boolean True if no errors
	 */
	public function updatePage($rid, $status=array(), $published=0, $newtool=0)
	{
		if ($rid === NULL) 
		{
			return false;
		}

		$resource = new ResourcesResource($this->database);
		$resource->load($rid);
		if (count($status) > 0) 
		{
			$resource->fulltxt    = addslashes($status['fulltxt']);
			$resource->introtext   = $status['description'];
			$resource->title       = preg_replace('/\s+/', ' ', $status['title']);
			$resource->modified    = date("Y-m-d H:i:s");
			$resource->modified_by = $this->juser->get('id');
		}
		if ($published) 
		{
			$resource->published = $published;
		}
		if ($newtool && $published == 1) 
		{
			$resource->publish_up = date("Y-m-d H:i:s");
		}

		if (!$resource->store()) 
		{
			$this->setError($row->getError());
			return false;
		}
		else if ($newtool) 
		{
			$this->addComponentMessage(JText::_('COM_TOOLS_NOTICE_RES_PUBLISHED'));
			//$this->setMessage(JText::_('COM_TOOLS_NOTICE_RES_PUBLISHED'));
		}
		else 
		{
			$this->addComponentMessage(JText::_('COM_TOOLS_NOTICE_RES_UPDATED'));
			//$this->setMessage(JText::_('COM_TOOLS_NOTICE_RES_UPDATED'));
		}

		return true;
	}

	/**
	 * Generate a resource page from tool data
	 * 
	 * @param      integer $toolid Tool ID
	 * @param      array   $tool   Tool info to generate resource from
	 * @return     mixed False if error, integer if success
	 */
	public function createPage($toolid, $tool)
	{
		$tool['title'] = preg_replace('/\s+/', ' ', $tool['title']);

		$params = array();
		$params[] = 'pageclass_sfx=';
		$params[] = 'show_title=1';
		$params[] = 'show_authors=1';
		$params[] = 'show_assocs=1';
		$params[] = 'show_type=1';
		$params[] = 'show_logicaltype=1';
		$params[] = 'show_rating=1';
		$params[] = 'show_date=1';
		$params[] = 'show_parents=1';
		$params[] = 'series_banner=';
		$params[] = 'show_banner=1';
		$params[] = 'show_footer=3';
		$params[] = 'show_stats=0';
		$params[] = 'st_appname=' . strtolower($tool['toolname']);
		$params[] = 'st_appcaption=' . $tool['title'] . $tool['version'];
		$params[] = 'st_method=com_narwhal';

		// Initiate extended database class
		$row = new ResourcesResource($this->database);
		$row->created_by = $this->juser->get('id');
		$row->created    = date('Y-m-d H:i:s');
		$row->published  = 2;  // draft state
		$row->params     = implode("\n", $params);
		$row->attribs    = 'marknew=0';
		$row->standalone = 1;
		$row->type       = 7;
		$row->title      = $tool['title'];
		$row->introtext  = $tool['description'];
		$row->alias      = $tool['toolname'];

		if (!$row->store()) 
		{
			$this->setError($row->getError());
			return false;
		}

		return $row->id;
	}

	/**
	 * Preview the resource
	 * 
	 * @return     void
	 */
	public function previewTask()
	{
		// Incoming
		$alias   = JRequest::getVar('app', '');
		$version = JRequest::getVar('editversion', 'dev');
		$rid     = JRequest::getInt('rid', 0);

		// Load the tool
		$obj = new Tool($this->database);
		$this->_toolid = $obj->getToolId($alias);

		if (!$this->_toolid) 
		{
			// not a tool resource page
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=pipeline')
			);
			return;
		}

		// Make sure user is authorized to go further
		if (!$this->_checkAccess($this->_toolid)) 
		{
			JError::raiseError(403, JText::_('COM_TOOLS_ALERTNOTAUTH'));
			return;
		}

		// Get tool version (dev or current) information
		$obj->getToolStatus($this->_toolid, $this->_option, $status, $version);

		// Instantiate our tag object
		$tagcloud = new ResourcesTags($this->database);
		$tags  = JRequest::getVar('tags', '', 'post');
		$tagfa = JRequest::getVar('tagfa', '', 'post');
		// Process new tags
		$newtags = '';
		if ($tagfa) 
		{
			$newtags = $tagfa . ', ';
		}
		if ($tags) 
		{
			$newtags .= $tags;
		}
		$tagcloud->tag_object($this->juser->get('id'), $rid, $newtags, 1, 1);

		// Get some needed libraries
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		// Load the resource object
		$resource = new ResourcesResource($this->database);
		$resource->loadAlias($alias);

		if (!$this->juser->get('guest')) 
		{
			ximport('Hubzero_User_Helper');
			$xgroups = Hubzero_User_Helper::getGroups($this->juser->get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = $this->_getUsersGroups($xgroups);
		} 
		else 
		{
			$usersgroups = array();
		}

		// Get updated version
		$objV = new ToolVersion($this->database);

		$thistool = $objV->getVersionInfo('', $version, $resource->alias, '');
		$thistool = $thistool ? $thistool[0] : '';

		// Replace resource info with requested version
		$objV->compileResource($thistool, '', $resource, 'dev', $this->rconfig);

		// get language library
		$lang =& JFactory::getLanguage();
		if (!$lang->load(strtolower('com_resources'), JPATH_BASE)) 
		{
			$this->setError(JText::_('COM_TOOLS_Failed to load language file'));
		}

		// Push CSS to the document
		$this->_getStyles($this->_option, 'assets/css/' . $this->_controller . '.css');

		// Push some scripts to the document
		$this->_getScripts('assets/js/' . $this->_controller);
		$this->_getStyles('com_resources');

		// Set the document title
		$this->view->title = JText::_(strtoupper($this->_name)) . ': ' . JText::_('COM_TOOLS_PREVIEW_TOOL_PAGE') . ' (' . $resource->alias . ')';
		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the document pathway (breadcrumbs)
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)), 
				'index.php?option=' . $this->_option
			);
		}
		if (count($pathway->getPathWay()) <= 1) 
		{
			$pathway->addItem(
				JText::_('COM_TOOLS_STATUS') . ' ' . JText::_('COM_TOOLS_FOR') . ' ' . $thistool->toolname, 
				'index.php?option=' . $this->_option . '&controller=pipeline&task=status&app=' . $alias
			);
			$pathway->addItem(
				JText::_('COM_TOOLS_EDIT_TOOL_PAGE'), 
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&app=' . $alias . '&task=start&step=1'
			);
		}

		$this->view->toolid      = $this->_toolid;
		$this->view->step        = 5;
		$this->view->version     = $version;
		$this->view->resource    = $resource;
		$this->view->config      = $this->rconfig;
		$this->view->usersgroups = $usersgroups;
		$this->view->status      = $status;

		// Pass error messages to the view
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Strip some unwanted and potentially harmful items out of text
	 * 
	 * @param      string &$text Text to clean
	 * @return     string
	 */
	public function txtClean(&$text)
	{
		$text = preg_replace('/{kl_php}(.*?){\/kl_php}/s', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace("'<style[^>]*>.*?</style>'si", '', $text);
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		return $text;
	}

	/**
	 * Push a user's groups' alias to an array for easier searching
	 * 
	 * @param      array $groups User's groups
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
	 * Check if the current user has access to this tool
	 * 
	 * @param      unknown $toolid       Tool ID
	 * @param      integer $allowAdmins  Allow admins access?
	 * @param      boolean $allowAuthors Allow authors access?
	 * @return     boolean True if they have access
	 */
	private function _checkAccess($toolid, $allowAdmins=1, $allowAuthors=false)
	{
		// Create a Tool object
		$obj = new Tool($this->database);

		// allow to view if admin
		if ($this->config->get('access-manage-component') && $allowAdmins) 
		{
			return true;
		}

		// check if user in tool dev team
		if ($developers = $obj->getToolDevelopers($toolid)) 
		{
			foreach ($developers as $dv) 
			{
				if ($dv->uidNumber == $this->juser->get('id')) 
				{
					return true;
				}
			}
		}

		// allow access to tool authors
		if ($allowAuthors) 
		{
			// Nothing here?
		}

		return false;
	}

	/**
	 * Authorization checks
	 * 
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if ($this->juser->get('guest')) 
		{
			return;
		}

		// if no admin group is defined, allow superadmin to act as admin
		// otherwise superadmins can only act if they are also a member of the component admin group
		if (($admingroup = trim($this->config->get('admingroup', '')))) 
		{
			ximport('Hubzero_User_Helper');
			// Check if they're a member of admin group
			$ugs = Hubzero_User_Helper::getGroups($this->juser->get('id'));
			if ($ugs && count($ugs) > 0) 
			{
				$admingroup = strtolower($admingroup);
				foreach ($ugs as $ug)
				{
					if (strtolower($ug->cn) == $admingroup) 
					{
						$this->config->set('access-manage-' . $assetType, true);
						$this->config->set('access-admin-' . $assetType, true);
						$this->config->set('access-create-' . $assetType, true);
						$this->config->set('access-delete-' . $assetType, true);
						$this->config->set('access-edit-' . $assetType, true);
					}
				}
			}
		}
		else 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$asset  = $this->_option;
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				// Permissions
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if ($this->juser->authorize($this->_option, 'manage'))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
		}
	}
}
