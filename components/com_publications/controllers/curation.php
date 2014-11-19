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
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'publication.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'curation.php');
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'helper.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'html.php' );

/**
 * Primary component controller (extends \Hubzero\Component\SiteController)
 */
class PublicationsControllerCuration extends \Hubzero\Component\SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->_task  = JRequest::getVar( 'task', '');
		$this->_id    = JRequest::getInt( 'id', 0 );
		$this->_pub	  = NULL;

		// View individual curation
		if ($this->_id && !$this->_task)
		{
			$this->_task = 'view';
		}

		// Get language
		$lang = JFactory::getLanguage();
		$lang->load('plg_projects_publications');

		// Is curation enabled?
		if (!$this->config->get('curation', 0))
		{
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		//continue with parent execute method
		parent::execute();
	}

	/**
	 * Display task
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get all uer groups
		$usergroups = \Hubzero\User\Helper::getGroups($this->juser->get('id'));

		// Check authorization
		$mt  = new PublicationMasterType( $this->database );
		$authorized   = $this->_authorize($mt->getCuratorGroups());

		// Get all authorized types
		$authtypes = $mt->getAuthTypes($usergroups, $this->config->get('curatorgroup', ''), $authorized);

		if (!$authorized || ($authorized == 'curator' && (!$authtypes || empty($authtypes))))
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}

			JError::raiseError( 403, JText::_('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'));
			return;
		}

		// Build query
		$filters = array();
		$filters['limit'] 	 		= JRequest::getInt('limit', 25);
		$filters['start'] 	 		= JRequest::getInt('limitstart', 0);
		$filters['sortby']   		= JRequest::getVar( 't_sortby', 'status');
		$filters['sortdir']  		= JRequest::getVar( 't_sortdir', 'ASC');
		$filters['ignore_access']   = 1;
		$filters['master_type']     = $authtypes;
		$filters['dev']   	 		= 1; // get dev versions
		$filters['status']   	 	= array(5, 7); // submitted/pending

		$this->view->filters		= $filters;

		// Instantiate project publication
		$objP = new Publication( $this->database );

		// Get all publications
		$this->view->rows = $objP->getRecords($filters);

		// Get total count
		$results = $objP->getCount($filters);
		$this->view->total = ($results && is_array($results)) ? count($results) : 0;

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		//push the stylesheet to the view
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'css/curation.css');

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->option 	= $this->_option;
		$this->view->database 	= $this->database;
		$this->view->config		= $this->config;
		$this->view->title 		= $this->_title;
		$this->view->display();
	}

	/**
	 * Build the title for this component
	 *
	 * @return void
	 */
	protected function _buildTitle()
	{
		if (!$this->_title)
		{
			$this->_title = JText::_(strtoupper($this->_option)) . ': '
				. JText::_(strtoupper($this->_option . '_' . $this->_controller));
		}
		$document = JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	/**
	 * Build the "trail"
	 *
	 * @return void
	 */
	protected function _buildPathway()
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		$pathway->addItem(
			JText::_('COM_PUBLICATIONS_CURATION'),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller .  '&task=display'
		);

		if ($this->_pub)
		{
			$pathway->addItem(
				$this->_pub->title,
				'index.php?option=' . $this->_option . '&controller='
					. $this->_controller .  '&task=view' . '&id=' . $this->_pub->id
			);
		}
	}

	/**
	 * View publication
	 *
	 * @return     void
	 */
	public function viewTask()
	{
		// Incoming
		$pid 		= $this->_id ? $this->_id : JRequest::getInt('id', 0);
		$version 	= JRequest::getVar( 'version', '' );

		if (!$pid)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Load publication & version classes
		$objP = new Publication( $this->database );
		$objV = new PublicationVersion( $this->database );
		$mt   = new PublicationMasterType( $this->database );

		// Check that version exists
		$version = $objV->checkVersion($pid, $version) ? $version : 'default';

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $version);

		// If publication not found, raise error
		if (!$pub)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		// We can only view pending publications
		if ($pub->state != 5)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=curation'),
				JText::_('COM_PUBLICATIONS_CURATION_PUB_WRONG_STATUS'),
				'error'
			);
			return;
		}

		$pub->_project 	= new Project( $this->database );
		$pub->_project->load($pub->project_id);
		$pub->_type    	= $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup));

		if (!$authorized)
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}
			JError::raiseError( 403, JText::_('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'));
			return;
		}

		//push the stylesheet to the view
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'css/curation.css');

		// Main version
		if ($pub->main == 1)
		{
			$version = 'default';
		}

		$pub->version 	= $version;

		// Initialize helpers
		$pub->_helpers = new stdClass();
		$pub->_helpers->pubHelper 		= new PublicationHelper($this->database, $pub->version_id, $pub->id);
		$pub->_helpers->htmlHelper	  	= new PublicationsHtml();
		$pub->_helpers->projectsHelper 	= new ProjectsHelper( $this->database );

		// Get type info
		$pub->_category = new PublicationCategory( $this->database );
		$pub->_category->load($pub->category);
		$pub->_category->_params = new JParameter( $pub->_category->params );

		// Get authors
		$pAuthors 			= new PublicationAuthor( $this->database );
		$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);
		$pub->_submitter 	= $pAuthors->getSubmitter($pub->version_id, $pub->created_by);

		// Get attachments
		$pContent = new PublicationAttachment( $this->database );
		$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new PublicationsCuration($this->database, $manifest);

		// Get reviewed Items
		$pub->reviewedItems = $pub->_curationModel->getReviewedItems($pub->version_id);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		$this->_pub = $pub;

		// Get last history record (from author)
		$obj = new PublicationCurationHistory($this->database);
		$this->view->history = $obj->getLastRecord($pub->version_id);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->pub 		    = $pub;
		$this->view->title  		= $this->_title;
		$this->view->option 		= $this->_option;
		$this->view->database 		= $this->database;
		$this->view->config			= $this->config;
		$this->view->display();
	}

	/**
	 * View curation history
	 *
	 * @return     void
	 */
	public function historyTask()
	{
		// Incoming
		$pid 		= $this->_id ? $this->_id : JRequest::getInt('id', 0);
		$version 	= JRequest::getVar( 'version', '' );

		if (!$pid)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_RESOURCE_NOT_FOUND') );
			return;
		}

		// Load publication & version classes
		$objP  = new Publication( $this->database );
		$objV  = new PublicationVersion( $this->database );
		$mt    = new PublicationMasterType( $this->database );

		// Check that version exists
		$version = $objV->checkVersion($pid, $version) ? $version : 'default';

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $version);

		if (!$pub)
		{
			JError::raiseError( 404, JText::_('Error loading publication') );
			return;
		}

		$pub->_project 	= new Project( $this->database );
		$pub->_project->load($pub->project_id);
		$pub->_type    	= $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup));

		if (!$authorized)
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}
			JError::raiseError( 403, JText::_('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'));
			return;
		}

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new PublicationsCuration($this->database, $manifest);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		if (!JRequest::getInt( 'ajax', 0 ))
		{
			// Set page title
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway();

			// Add plugin style
			\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'css/curation.css');
		}

		$this->view->pub 		    = $pub;
		$this->view->title  		= $this->_title;
		$this->view->option 		= $this->_option;
		$this->view->database 		= $this->database;
		$this->view->config			= $this->config;
		$this->view->ajax			= JRequest::getInt( 'ajax', 0 );
		$this->view->display();
	}

	/**
	 * Approve publication
	 *
	 * @return     void
	 */
	public function approveTask()
	{
		// Incoming
		$pid 	= $this->_id ? $this->_id : JRequest::getInt('id', 0);
		$vid 	= JRequest::getInt('vid', 0);

		// Load publication & version classes
		$objP  = new Publication( $this->database );
		$row   = new PublicationVersion( $this->database );
		$mt    = new PublicationMasterType( $this->database );

		// Load version
		if (!$row->load($vid) || $row->publication_id != $pid)
		{
			JError::raiseError( 404, JText::_('Error loading version') );
			return;
		}

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $row->version_number);

		if (!$pub)
		{
			JError::raiseError( 404, JText::_('Error loading publication') );
			return;
		}

		$pub->_project 	= new Project( $this->database );
		$pub->_project->load($pub->project_id);
		$pub->_type    	= $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup));

		if (!$authorized)
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}
			JError::raiseError( 403, JText::_('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'));
			return;
		}

		$row->state    		= 1; // published
		$row->accepted 		= JFactory::getDate()->toSql();
		$row->reviewed 		= JFactory::getDate()->toSql();
		$row->reviewed_by 	= $this->juser->get('id');

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new PublicationsCuration($this->database, $manifest);

		// Store curation manifest
		$row->curation = json_encode($pub->_curationModel->_manifest);

		if (!$row->store())
		{
			JError::raiseError( 403, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_FAILED') );
			return;
		}

		// Update DOI record if DOI provisioned locally
		$shoulder  = $this->config->get('doi_shoulder');
		if ($row->doi && $shoulder && preg_match("/" . $shoulder . "/", $case))
		{
			// Collect DOI metadata
			$metadata = PublicationUtilities::collectMetadata($pub);
			$doierr   = NULL;

			// Get authors
			$pAuthors 			= new PublicationAuthor( $this->database );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);

			// Update DOI with latest information
			if (!PublicationUtilities::updateDoi($row->doi, $row,
				$pub->_authors, $this->config, $metadata, $doierr))
			{
				$this->setError(JText::_('COM_PUBLICATIONS_ERROR_DOI') . ' ' . $doierr);
			}
		}

		// Mark as curated
		$row->saveParam($row->id, 'curated', 1);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		// On after status change
		$this->onAfterStatusChange( $pub, $row->state );

		$message = $this->getError() ? $this->getError() : JText::_('COM_PUBLICATIONS_CURATION_SUCCESS_APPROVED');
		$class   = $this->getError() ? 'error' : 'success';

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=curation'),
			$message,
			$class
		);

		return;
	}

	/**
	 * Kick back to developers
	 *
	 * @return     void
	 */
	public function kickbackTask()
	{
		// Incoming
		$pid 	= $this->_id ? $this->_id : JRequest::getInt('id', 0);
		$vid 	= JRequest::getInt('vid', 0);

		// Load publication & version classes
		$objP  = new Publication( $this->database );
		$row   = new PublicationVersion( $this->database );
		$mt    = new PublicationMasterType( $this->database );

		// Load version
		if (!$row->load($vid) || $row->publication_id != $pid)
		{
			JError::raiseError( 404, JText::_('Error loading version') );
			return;
		}

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $row->version_number);

		if (!$pub)
		{
			JError::raiseError( 404, JText::_('Error loading publication') );
			return;
		}

		$pub->_project 	= new Project( $this->database );
		$pub->_project->load($pub->project_id);
		$pub->_type    	= $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup));

		if (!$authorized)
		{
			if ($this->juser->get('guest'))
			{
				$this->_msg = JText::_('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}
			JError::raiseError( 403, JText::_('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'));
			return;
		}

		// Change publication status
		$row->state 		= 7; // pending author changes
		$row->reviewed 		= JFactory::getDate()->toSql();
		$row->reviewed_by 	= $this->juser->get('id');

		if (!$row->store())
		{
			JError::raiseError( 403, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_FAILED') );
			return;
		}

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new PublicationsCuration($this->database, $manifest);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		// On after status change
		$this->onAfterStatusChange( $pub, $row->state );

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=curation'),
			JText::_('COM_PUBLICATIONS_CURATION_SUCCESS_KICKBACK')
		);

		return;
	}

	/**
	 * Save review for curation item (AJAX)
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Incoming
		$pid 	= $this->_id ? $this->_id : JRequest::getInt('id', 0);
		$vid 	= JRequest::getInt('vid', 0);
		$props  = JRequest::getVar( 'p', '' );
		$pass 	= JRequest::getInt( 'pass', 0 );
		$action = $pass ? 'pass' : 'fail';
		$review = JRequest::getVar( 'review', '' );

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0])) ? $parts[0] : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 0;

		if (!$block || !$step)
		{
			echo json_encode(array('success' => 0, 'error' => JText::_('Error parsing publication manifest')));
			return;
		}
		if ($action == 'fail' && !$review)
		{
			echo json_encode(array('success' => 0, 'error' => JText::_('Please explain why the item requires changes')));
			return;
		}

		// Load publication & version classes
		$objP  = new Publication( $this->database );
		$objV  = new PublicationVersion( $this->database );
		$mt    = new PublicationMasterType( $this->database );

		if (!$vid || !$objV->load($vid))
		{
			echo json_encode(array('success' => 0, 'error' => JText::_('Error loading version')));
			return;
		}

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $objV->version_number);

		// If publication not found, raise error
		if (!$pub)
		{
			echo json_encode(array('success' => 0, 'error' => JText::_('Error loading publication')));
			return;
		}

		$pub->_project 	= new Project( $this->database );
		$pub->_project->load($pub->project_id);
		$pub->_type    	= $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup));
		if (!$authorized)
		{
			echo json_encode(array('success' => 0, 'error' => JText::_('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED')));
			return;
		}

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new PublicationsCuration($this->database, $manifest);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		$data 					= new stdClass;
		$data->reviewed 		= JFactory::getDate()->toSql();
		$data->reviewed_by 		= $this->juser->get('id');
		$data->review_status 	= $action == 'pass' ? 1 : 2;
		if ($action == 'pass')
		{
			$data->update = '';
		}
		if ($review)
		{
			$data->review   = $review;
		}

		$notice = $action == 'pass' ? '' : $review;

		// Save curation
		if ($pub->_curationModel->saveUpdate($data, $element, $block, $pub, $step))
		{
			echo json_encode(array(
				'success' 	=> 1,
				'error' 	=> $this->getError(),
				'notice' 	=> $notice)
			);
			return;
		}
		else
		{
			echo json_encode(array(
				'success' 	=> 0,
				'error'  	=> JText::_('There was a problem saving curation item'),
				'notice' 	=> '')
			);
			return;
		}
	}

	/**
	 * On after approve/kickback
	 *
	 * @return     void
	 */
	public function onAfterStatusChange( $pub, $status )
	{
		if ($this->getError())
		{
			return;
		}
		// Add message to project
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_projects' . DS . 'tables' . DS . 'project.activity.php');

		$activity = $status == 1
					? JText::_('COM_PUBLICATIONS_CURATION_ACTIVITY_PUBLISHED')
					: JText::_('COM_PUBLICATIONS_CURATION_ACTIVITY_KICKBACK');

		$pubtitle 	= \Hubzero\Utility\String::truncate($pub->title, 100);

		// Log activity in curation history
		$pub->_curationModel->saveHistory($pub, $this->juser->get('id'), $pub->state, $status, 1 );

		// Add activity
		$activity .= ' ' . strtolower(JText::_('version')) . ' ' . $pub->version_label . ' '
		. JText::_('COM_PUBLICATIONS_OF') . ' ' . strtolower(JText::_('publication')) . ' "'
		. $pubtitle . '" ';

		// Build return url
		$link 	= '/projects/' . $pub->_project->alias . '/publications/'
				. $pub->id . '/?version=' . $pub->version_number;

		// Record activity
		$objAA = new ProjectActivity ( $this->database );
		$aid   = $objAA->recordActivity(
				$pub->project_id,
				$this->juser->get('id'),
				$activity,
				$pub->id,
				$pubtitle,
				$link,
				'publication',
				0,
				$admin = 1
		);

		// Start message
		$juri 	 = JURI::getInstance();
		$sef	 = 'publications' . DS . $pub->id . DS . $pub->version_number;
		$link 	 = rtrim($juri->base(), DS) . DS . trim($sef, DS);
		$manage  = rtrim($juri->base(), DS) . DS . 'projects' . DS . $pub->_project->alias . DS . 'publications' . DS . $pub->id . DS . $pub->version_number;
		$message  = $status == 1 ? JText::_('COM_PUBLICATIONS_CURATION_EMAIL_CURATOR_APPROVED') : JText::_('COM_PUBLICATIONS_CURATION_EMAIL_CURATOR_KICKED_BACK');

		if ($status != 1)
		{
			$message .= "\n" . "\n";
			$message .= JText::_('COM_PUBLICATIONS_CURATION_TAKE_ACTION') . ' ' . $manage;
		}
		else
		{
			$message .= ' ' . $link;
		}

		$pubtitle 	= \Hubzero\Utility\String::truncate($pub->title, 100);
		$subject 	= ucfirst(JText::_('COM_PUBLICATIONS_CURATION_VERSION'))
					. ' ' . $pub->version_label . ' ' . JText::_('COM_PUBLICATIONS_OF') . ' '
					. strtolower(JText::_('COM_PUBLICATIONS_PUBLICATION'))
					. ' "' . $pubtitle . '" ';
		$subject .= $status == 1
			? JText::_('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
			: JText::_('COM_PUBLICATIONS_MSG_ADMIN_KICKED_BACK');

		// Get authors
		$pa = new PublicationAuthor( $this->database );
		$authors = $pa->getAuthors($pub->version_id, 1, 1, 1);

		// No authors – send to publication creator
		if (count($authors) == 0)
		{
			$authors = array($pub->created_by);
		}

		// Make sure there are no duplicates
		$authors = array_unique($authors);

		// Notify authors
		PublicationHelper::notify(
			$this->config,
			$pub,
			$authors,
			$subject,
			$message,
			true
		);

		return;
	}

	/**
	 * Check user access
	 *
	 * @param      array $curatorgroups
	 * @return     mixed False if no access, string if has access
	 */
	protected function _authorize( $curatorgroups = array() )
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		$authorized = false;

		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage'))
		{
			$authorized = 'admin';
		}

		$curatorgroup = $this->config->get('curatorgroup', '');
		if ($curatorgroup)
		{
			$curatorgroups[] = $curatorgroup;
		}

		if (!empty($curatorgroups) && $this->config->get('curation', 0))
		{
			foreach ($curatorgroups as $curatorgroup)
			{
				if ($group = \Hubzero\User\Group::getInstance($curatorgroup))
				{
					// Check if they're a member of this group
					$ugs = \Hubzero\User\Helper::getGroups($this->juser->get('id'));
					if ($ugs && count($ugs) > 0)
					{
						foreach ($ugs as $ug)
						{
							if ($group && $ug->cn == $group->get('cn'))
							{
								$authorized = 'curator';
								return $authorized;
							}
						}
					}
				}
			}
		}

		return $authorized;
	}

	/**
	 * Login view
	 *
	 * @return     void
	 */
	protected function _login()
	{
		$rtrn = JRequest::getVar('REQUEST_URI',
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task), 'server');
		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
			$this->_msg,
			'warning'
		);
	}
}