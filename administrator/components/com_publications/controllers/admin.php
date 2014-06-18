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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.type.php');

/**
 * Publication administrative support
 */
class PublicationsControllerAdmin extends \Hubzero\Component\AdminController
{
	/**
	 * List available admin tasks
	 *
	 * @return     void
	 */
	public function displayTask()
	{

		// Redirect to Publication Manager for now
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=items'
		);
		return;

		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		$obj = new Project( $this->database );

		// Get admin type id
		$objPT = new ProjectType( $this->database );
		$admintype = $objPT->getIdByTitle('Administrative');
		$admintype = $admintype ? $admintype : 4;

		// Set filters
		$this->view->filters = array();
		$this->view->filters['authorized'] = true;
		$this->view->filters['type'] = $admintype; // admin-type

		// Get administrative project(s)
		$this->view->projects = $obj->getRecords( $this->view->filters, true, 0, 1 );

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'publications.css');

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Transfer svn tool into a project
	 *
	 * @return     void
	 */
	public function workspaceTask()
	{
		// Redirect to Publication Manager for now
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=items'
		);
		return;

		$toolname = 'workspace';

		// Check if workspace publication already exists
		$model = new Publication($this->database);
		$workspace = $model->getPublication(NULL, 'default', NULL, $toolname);

		if ($workspace)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Workspace publication already exists'),
				'error'
			);
			return;
		}

		// Get necessary classes
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'tool.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'author.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_resources' . DS . 'tables' . DS . 'doi.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources'
			. DS . 'helpers' . DS . 'tags.php');

		// Get tool library
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.instance.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.log.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.view.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_tools' . DS . 'tables' . DS . 'project.tool.status.php');

		// Get workspace tool info
		$objT = new Tool($this->database);
		if (!$objT->loadFromName($toolname))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'No workspace tool on this hub', 'error'
			);
			return;
		}

		// Load the tool version
		$tv = new ToolVersion($this->database);
		if (!$tv->loadFromName($toolname))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Error loading workspace tool version', 'error'
			);
			return;
		}

		// Create admin project type if not exists
		$objPT = new ProjectType( $this->database );
		$admintype = $objPT->getIdByTitle('Administrative');

		if (!$admintype)
		{
			$objPT->type = 'Administrative';
			$objPT->description = 'Administrative project';
			$objPT->store();

			$admintype = $objPT->id;
		}

		// Project class
		$obj = new Project( $this->database );
		$projectid = 0;

		// Set filters
		$filters = array();
		$filters['authorized'] = true;
		$filters['type'] = $admintype; // admin-type

		// Get administrative project(s)
		$projects = $obj->getRecords( $filters, true, 0, 1 );

		// Get admin user
		$query   =  "SELECT uidNumber FROM #__xprofiles WHERE username = 'admin' OR username = 'hubadmin' LIMIT 1";
		$this->database->setQuery( $query );
		$owner = $this->database->loadResult();

		if (!$owner)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Cannot determine admin user for admin project', 'error'
			);
			return;
		}

		// Create admin project if not exists
		if (count($projects) == 0)
		{
			$obj->alias = 'admin';
			$obj->title = 'Administrative project';
			$obj->state = 1;
			$obj->type  = $admintype;
			$obj->created = JFactory::getDate()->toSql();
			$obj->created_by_user = $owner;
			$obj->owned_by_user = $owner;
			$obj->setup_stage = 3;
			$obj->store();

			$projectid = $obj->id;
		}
		else
		{
			$projectid = $projects[0]->id;
		}

		if (!$projectid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Admin project doesn\'t exist and could not be created', 'error'
			);
			return;
		}

		// Do we have a workspace resource? (then copy metadata)
		$resource = new ResourcesResource($this->database);

		if (!$resource->load($toolname))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Cannot load original resource data', 'error'
			);
			return;
		}

		// Get tools category and master type
		$objMasterType = new PublicationMasterType( $this->database );
		$appMasterType = $objMasterType->getTypeId('tools');

		$objCat = new PublicationCategory( $this->database );
		$appCat = $objCat->getCatId('tool');

		if (!$appMasterType || !$appCat)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Missing apps master type or tool category', 'error'
			);
			return;
		}

		// Create project tool record
		$

		// Create publication
		$model->category    = $appCat;
		$model->master_type = $appMasterType;
		$model->project_id  = $projectid;
		$model->created 	= JFactory::getDate()->toSql();
		$model->created_by 	= $owner;
		$model->alias 		= $toolname;

		if (!$model->store())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Failed to save publication entry', 'error'
			);
			return;
		}

		if (!$model->id)
		{
			$model->checkin();
		}

		// Publication ID
		$pid = $model->id;

		if (!$pid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Missing publication id for version creation, cannot continue', 'error'
			);
			return;
		}

		// Create publication version
		$pubVersion = new PublicationVersion( $this->database );
		$pubVersion->publication_id = $pid;
		$pubVersion->main 			= 1;
		$pubVersion->state 			= 1;
		$pubVersion->created 		= JFactory::getDate()->toSql();
		$pubVersion->created_by 	= $owner;

		$pubVersion->title 		 	= $tv->title ? $tv->title : 'Workspace';
		$pubVersion->abstract 		= $tv->description ? $tv->description : 'Development workspace';

		$description = 'A workspace is a Linux desktop in your browser. It provides access to HUBzero\'s Rappture toolkit, along with computational resources available at Purdue, on the Open Science Grid, and on the TeraGrid. You can use workspaces as a development area for new tools--as a way to test out the functionality that comes with any hub based on the HUBzero platform. You can upload code, compile it, test it, and debug it. Once your code is working properly in a workspace, it is ready to be deployed as a tool on this or any other hub.';

		// Ween out metadata tags
		if ($tv->fulltxt)
		{
			$type = new ResourcesType($this->database);
			$type->load($resource->type);

			$data = array();
			preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $tv->fulltxt, $matches, PREG_SET_ORDER);
			if (count($matches) > 0)
			{
				foreach ($matches as $match)
				{
					$data[$match[1]] = stripslashes($match[2]);
				}
			}
			$fulltxt 	 = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $tv->fulltxt);
			$fulltxt 	 = trim($fulltxt);
			$description = ($fulltxt) ? trim(stripslashes($fulltxt)): trim(stripslashes($pubVersion->abstract));
			$metadata	 = NULL;

			// Pick up metadata
			if (!empty($data))
			{
				foreach ($data as $label => $value)
				{
					$metadata = '<nb:' . $label . '>' . $value . '</nb:' . $label . '>';
				}

				$pubVersion->metadata = $metadata;
			}

		}

		$pubVersion->description   	= $description;
		$pubVersion->version_label 	= $tv->version ? $tv->version : '1.0';
		$pubVersion->version_number	= 1;
		$pubVersion->license_text	= $tv->license;
		$pubVersion->access			= $resource->access;
		$pubVersion->rating			= $resource->rating;
		$pubVersion->ranking		= $resource->ranking;
		$pubVersion->times_rated	= $resource->times_rated;
		$pubVersion->published_up   = $tv->released ? $tv->released : $resource->publish_up;
		$pubVersion->params			= $resource->params;
		$pubVersion->secret			= strtolower(ProjectsHtml::generateCode(10, 10, 0, 1, 1));

		// Get resource DOI
		$objDoi = new ResourcesDoi($this->database);
		$doi = $objDoi->getDoi($resource->id, $tv->revision, '', 1);

		$juri = JURI::getInstance();
		$url = $juri->base() . ltrim(JRoute::_('index.php?option=com_publications&id=' . $pid . '&v=1'), DS);

		// Collect metadata
		$metadata = array(
			'targetURL' 	=> $url,
			'title'     	=> $pubVersion->title,
			'version'   	=> $pubVersion->version_label,
			'abstract'  	=> htmlspecialchars(stripslashes($pubVersion->abstract)),
			'language'		=> 'en',
			'resourceType' 	=> 'Software',
			'typetitle' 	=> 'Simulation Tool'
		);

		// Get authors
		$objA = new ToolAuthor($this->database);
		$authors = $objA->getAuthorsDOI($resource->id);

		// Update DOI with new URL (TBD)
		if ($doi)
		{
			$doi = $this->config->get('doi_shoulder') . DS . $doi;
			PublicationUtilities::updateDoi($doi, $pubVersion, $authors, $this->config, $metadata, $doierr);
		}
		else
		{
			// Get a DOI
			$doi = PublicationUtilities::registerDoi($pubVersion, $authors, $this->config, $metadata, $doierr);
		}

		if (!$doi)
		{
			// Revert
			$model->delete();

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				'Failed to update or register a DOI, could not proceed:' . $doierr, 'error'
			);
			return;
		}

		// Save DOI
		$pubVersion->doi = $doi;

		if (!$pubVersion->store())
		{
			// Revert
			$model->delete();

			$this->addComponentMessage('Failed to save publication version entry', 'error');
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		if (!$pubVersion->id)
		{
			$pubVersion->checkin();
		}

		// Attach tool to publication
		$pContent = new PublicationAttachment($this->database);
		$pContent->publication_id 		  = $pid;
		$pContent->publication_version_id = $pubVersion->id;
		$pContent->type					  = 'tool';
		$pContent->role					  = 1;
		$pContent->ordering				  = 1;

		$v = (!isset($tv->revision) or $tv->revision == 'dev') ? 'test' : $tv->revision;
		$pContent->path					  = 'tools/' . $toolname . '/invoke/' . $v;

		// References to tool tables
		$pContent->object_id 			  = $objT->id;
		$pContent->object_name 			  = $toolname;
		$pContent->object_instance 		  = $tv->id;
		$pContent->object_revision 		  = $v;

		$pContent->params				  = 'serveas=invoke' . "\n";
		$pContent->created 				  = JFactory::getDate()->toSql();
		$pContent->created_by 			  = $owner;

		if (!$pContent->store())
		{
			// Revert
			$model->delete();
			$pubVersion->delete();

			$this->addComponentMessage('Failed to attach tool to publication', 'error');
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		// Attach author(s)
		// None for workspace tool

		// Transfer reviews
		// N/A for workspace

		// Attach tags if original resource has any
		$rt = new ResourcesTags($this->database);
		$tags = $rt->getTags($resource->id);

		if (!$tags && count($tags) > 0)
		{
			foreach ($tags as $tag)
			{
				$this->database->execute('INSERT INTO #__tags_object(tbl, objectid, tagid) VALUES (\'publications\', ' . $pid . ', ' . $tag->id . ')');
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			'success! workspace publication created'
		);
		return;
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect('index.php?option=' . $this->_option . '&controller=' . $this->_controller);
	}
}
