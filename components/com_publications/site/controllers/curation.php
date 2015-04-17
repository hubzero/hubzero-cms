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

namespace Components\Publications\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Publications\Tables;
use Components\Publications\Models;
use Components\Publications\Helpers;
use stdClass;
use Exception;

include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'publication.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'curation.php');
require_once( PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'html.php' );

/**
 * Primary component controller (extends \Hubzero\Component\SiteController)
 */
class Curation extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->_task  = Request::getVar( 'task', '');
		$this->_id    = Request::getInt( 'id', 0 );
		$this->_pub	  = NULL;

		// View individual curation
		if ($this->_id && !$this->_task)
		{
			$this->_task = 'view';
		}

		// Get language
		$lang = \JFactory::getLanguage();
		$lang->load('plg_projects_publications');

		// Is curation enabled?
		if (!$this->config->get('curation', 0))
		{
			$this->_redirect = Route::url('index.php?option=' . $this->_option);
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
		// Must be logged in to be a curator
		if (User::isGuest())
		{
			$this->_msg = Lang::txt('COM_PUBLICATIONS_CURATION_LOGIN');
			$this->_login();
			return;
		}

		// Get all user groups
		$usergroups = \Hubzero\User\Helper::getGroups(User::get('id'));

		// Check authorization
		$mt  = new Tables\MasterType( $this->database );
		$authorized = $this->_authorize($mt->getCuratorGroups());

		// Incoming
		$assigned = Request::getInt('assigned', 0);

		// Build query
		$filters = array();
		$filters['limit'] 	 		= Request::getInt('limit', 25);
		$filters['start'] 	 		= Request::getInt('limitstart', 0);
		$filters['sortby']   		= Request::getVar( 't_sortby', 'submitted');
		$filters['sortdir']  		= Request::getVar( 't_sortdir', 'DESC');
		$filters['ignore_access']   = 1;

		// Only get types for which authorized
		if ($authorized == 'limited')
		{
			$filters['master_type'] = $mt->getAuthTypes($usergroups, $authorized);
		}

		$filters['dev']   	 		= 1; // get dev versions
		$filters['status']   	 	= array(5, 7); // submitted/pending
		$filters['curator']   		= $assigned || $authorized == false ? 'owner' : NULL;
		$this->view->filters		= $filters;

		// Instantiate project publication
		$objP = new Tables\Publication( $this->database );

		// Get all publications
		$this->view->rows = $objP->getRecords($filters);

		// Get total count
		$results = $objP->getCount($filters);
		$this->view->total = ($results && is_array($results)) ? count($results) : 0;

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new \JPagination(
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
		$this->view->authorized = $authorized;
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
			$this->_title = Lang::txt(strtoupper($this->_option)) . ': '
				. Lang::txt(strtoupper($this->_option . '_' . $this->_controller));
		}
		$document = \JFactory::getDocument();
		$document->setTitle( $this->_title );
	}

	/**
	 * Build the "trail"
	 *
	 * @return void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		Pathway::append(
			Lang::txt('COM_PUBLICATIONS_CURATION'),
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller .  '&task=display'
		);

		if ($this->_pub)
		{
			Pathway::append(
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
		$pid 		= $this->_id ? $this->_id : Request::getInt('id', 0);
		$version 	= Request::getVar( 'version', '' );

		if (!$pid)
		{
			throw new Exception( Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'), 404 );
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
		$objP = new Tables\Publication( $this->database );
		$objV = new Tables\Version( $this->database );
		$mt   = new Tables\MasterType( $this->database );

		// Check that version exists
		$version = $objV->checkVersion($pid, $version) ? $version : 'default';

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $version);

		// If publication not found, raise error
		if (!$pub)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'), 404);
			return;
		}

		// We can only view pending publications
		if ($pub->state != 5)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=curation'),
				Lang::txt('COM_PUBLICATIONS_CURATION_PUB_WRONG_STATUS'),
				'error'
			);
			return;
		}

		// Load publication project
		$pub->_project = new \Components\Projects\Models\Project($pub->project_id);
		$pub->_type    = $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup), $pub->curator);

		if (!$authorized)
		{
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}
			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
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

		// Get type info
		$pub->_category = new Tables\Category( $this->database );
		$pub->_category->load($pub->category);
		$pub->_category->_params = new \JParameter( $pub->_category->params );

		// Get authors
		$pAuthors 			= new Tables\Author( $this->database );
		$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);
		$pub->_submitter 	= $pAuthors->getSubmitter($pub->version_id, $pub->created_by);

		// Get attachments
		$pContent = new Tables\Attachment( $this->database );
		$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new Models\Curation($manifest);

		// Get reviewed Items
		$pub->reviewedItems = $pub->_curationModel->getReviewedItems($pub->version_id);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		$this->_pub = $pub;

		// Get last history record (from author)
		$obj = new Tables\CurationHistory($this->database);
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
		$this->view->authorized		= $authorized;
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
		$pid 		= $this->_id ? $this->_id : Request::getInt('id', 0);
		$version 	= Request::getVar( 'version', '' );
		$ajax 		= Request::getInt( 'ajax', 0 );

		if (!$pid)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'), 404);
			return;
		}

		// Load publication & version classes
		$objP  = new Tables\Publication( $this->database );
		$objV  = new Tables\Version( $this->database );
		$mt    = new Tables\MasterType( $this->database );

		// Check that version exists
		$version = $objV->checkVersion($pid, $version) ? $version : 'default';

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $version);

		if (!$pub)
		{
			if ($ajax)
			{
				$this->view = new \Hubzero\Component\View( array('name'=>'error', 'layout' =>'restricted') );
				$this->view->error  = Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_LOAD');
				$this->view->title = $this->title;
				$this->view->display();
				return;
			}
			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_LOAD'), 404);
			return;
		}

		// Load publication project
		$pub->_project = new \Components\Projects\Models\Project($pub->project_id);
		$pub->_type    = $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup), $pub->curator);

		if (!$authorized)
		{
			if ($ajax)
			{
				$this->view = new \Hubzero\Component\View( array('name'=>'error', 'layout' =>'restricted') );
				$this->view->error  = Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED');
				$this->view->title = $this->title;
				$this->view->display();
				return;
			}
			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
			return;
		}

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new Models\Curation($manifest);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		if (!$ajax)
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
		$this->view->ajax			= $ajax;
		$this->view->display();
	}

	/**
	 * Assign curation
	 *
	 * @return     void
	 */
	public function assignTask()
	{
		// Incoming
		$pid 		= $this->_id ? $this->_id : Request::getInt('id', 0);
		$vid 		= Request::getInt( 'vid', 0 );
		$owner 		= Request::getInt( 'owner', 0 );
		$confirm 	= Request::getInt( 'confirm', 0 );
		$ajax 		= Request::getInt( 'ajax', 0 );

		// Load publication & version classes
		$objP  = new Tables\Publication( $this->database );
		$row  = new Tables\Version( $this->database );
		if (!$vid || !$row->load($vid) || $row->publication_id != $pid || !$objP->load($pid))
		{
			if ($ajax)
			{
				$this->view = new \Hubzero\Component\View( array('name'=>'error', 'layout' =>'restricted') );
				$this->view->error  = Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_LOAD');
				$this->view->title = $this->title;
				$this->view->display();
				return;
			}
			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_LOAD'), 404);
			return;
		}

		// Get all user groups
		$usergroups = \Hubzero\User\Helper::getGroups(User::get('id'));

		// Check authorization
		$mt  = new Tables\MasterType( $this->database );
		$authorized = $this->_authorize($mt->getCuratorGroups());

		// Get all authorized types
		$authtypes = $mt->getAuthTypes($usergroups, $authorized);

		if (!$authorized || ($authorized == 'curator' && (!$authtypes || empty($authtypes))))
		{
			if ($ajax)
			{
				$this->view = new \Hubzero\Component\View( array('name'=>'error', 'layout' =>'restricted') );
				$this->view->error  = Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED');
				$this->view->title = $this->title;
				$this->view->display();
				return;
			}
			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
			return;
		}

		// Perform assignment
		if ($confirm)
		{
			$previousOwner = $row->curator;
			$selected = Request::getInt( 'selected', 0 );

			// Make sure owner profile exists
			if ($owner)
			{
				$ownerProfile  = \Hubzero\User\Profile::getInstance($owner);
				if (!$ownerProfile)
				{
					$this->setError(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_ASSIGN_PROFILE'));
				}
			}
			elseif ($selected && Request::getVar( 'owner', ''))
			{
				$owner = $selected;
			}

			// Assign
			if (!$this->getError())
			{
				$row->curator = $owner;
				if (!$row->store())
				{
					$this->setError(Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_FAILED'));
				}
				// Notify curator
				if ($owner && $owner != $previousOwner)
				{
					$juri 	 = \JURI::getInstance();
					$sef	 = 'publications' . DS . $row->publication_id . DS . $row->version_number;
					$link 	 = rtrim($juri->base(), DS) . DS . trim($sef, DS);

					$item  =  '"' . html_entity_decode($row->title).'"';
					$item .= ' v.' . $row->version_label . ' ';
					$item  = htmlentities($item, ENT_QUOTES, "UTF-8");

					$message = Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_ASSIGNED') . ' ' . $item . "\n" . "\n";
					$message.= Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_ASSIGNED_CURATE') . ' ' . rtrim($juri->base(), DS) . '/publications/curation/' . $row->publication_id . "\n" . "\n";
					$message.= Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_ASSIGNED_PREVIEW') . ' ' . $link;

					// Instantiate project publication
					$pub = $objP->getPublication($row->publication_id, $row->version_number);
					if ($pub)
					{
						Helpers\Html::notify(
							$this->config,
							$pub,
							array($owner),
							Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_ASSIGNED_SUBJECT'),
							$message
						);
					}
				}
				// Log assignment in history
				if (!$this->getError() && $owner != $previousOwner)
				{
					$obj = new Tables\CurationHistory($this->database);
					if (isset($ownerProfile) && $ownerProfile)
					{
						$changelog = '<p>Curation assigned to ' . $ownerProfile->get('name') . ' (' . $ownerProfile->get('username') . ')</p>';
					}
					else
					{
						$changelog = '<p>Curator assignment was removed</p>';
					}

					// Create new record
					$obj->publication_version_id 	= $row->id;
					$obj->created 					= Date::toSql();
					$obj->created_by				= User::get('id');
					$obj->changelog					= $changelog;
					$obj->curator					= 1;
					$obj->newstatus					= $row->state;
					$obj->oldstatus					= $row->state;
					$obj->store();
				}
			}
		}
		else
		{
			if (!$ajax)
			{
				// Set page title
				$this->_buildTitle();

				// Set the pathway
				$this->_buildPathway();

				// Add plugin style
				\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'css/curation.css');
			}
			$this->view->row 		    = $row;
			$this->view->title  		= $this->_title;
			$this->view->option 		= $this->_option;
			$this->view->database 		= $this->database;
			$this->view->config			= $this->config;
			$this->view->ajax			= $ajax;
			$this->view->display();
			return;
		}

		$message = $this->getError() ? $this->getError() : Lang::txt('COM_PUBLICATIONS_CURATION_SUCCESS_ASSIGNED');
		$class   = $this->getError() ? 'error' : 'success';

		// Redirect to main listing
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=curation'),
			$message,
			$class
		);

		return;
	}

	/**
	 * Approve publication
	 *
	 * @return     void
	 */
	public function approveTask()
	{
		// Incoming
		$pid 	= $this->_id ? $this->_id : Request::getInt('id', 0);
		$vid 	= Request::getInt('vid', 0);

		// Load publication model
		$this->model  = new \Components\Publications\Models\Publication( $pid, '', $vid);

		if (!$this->model->exists()
			|| $this->model->version->publication_id != $this->model->publication->id)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
			return;
		}

		// Check authorization
		if (!$this->model->access('curator'))
		{
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}
			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
			return;
		}

		$this->model->version->state       = 1; // published
		$this->model->version->accepted    = Date::toSql();
		$this->model->version->reviewed    = Date::toSql();
		$this->model->version->reviewed_by = User::get('id');

		// Archive (mkAIP) if no grace period and not previously archived
		if (!$this->getError() && !$this->config->get('graceperiod', 0)
			&& $this->model->version->doi && \Components\Publications\Helpers\Utilities::mkAip($this->model->version)
			&& (!$this->model->version->archived
			|| $this->model->version->archived == '0000-00-00 00:00:00')
		)
		{
			$this->model->version->archived = Date::toSql();
		}

		// Set curation
		$this->model->setCuration();

		// Store curation manifest
		$this->model->version->curation = json_encode($this->model->_curationModel->_manifest);

		if (!$this->model->version->store())
		{
			throw new Exception(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_FAILED'), 403);
			return;
		}

		// Get DOI service
		$doiService = new \Components\Publications\Models\Doi($this->model);
		if ($this->model->version->doi)
		{
			$doiService->update($this->model->version->doi, true);
		}

		// Mark as curated
		$this->model->version->saveParam($this->model->version->id, 'curated', 1);

		// On after status change
		$this->onAfterStatusChange( $this->model, $this->model->version->state );

		$message = $this->getError() ? $this->getError()
			: Lang::txt('COM_PUBLICATIONS_CURATION_SUCCESS_APPROVED');
		$class   = $this->getError() ? 'error' : 'success';

		// Redirect to main listing
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=curation'),
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
		$pid 	= $this->_id ? $this->_id : Request::getInt('id', 0);
		$vid 	= Request::getInt('vid', 0);

		// Load publication & version classes
		$objP  = new Tables\Publication( $this->database );
		$row   = new Tables\Version( $this->database );
		$mt    = new Tables\MasterType( $this->database );

		// Load version
		if (!$row->load($vid) || $row->publication_id != $pid)
		{
			throw new Exception(Lang::txt('Error loading version'), 404);
			return;
		}

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $row->version_number);

		if (!$pub)
		{
			throw new Exception(Lang::txt('Error loading publication'), 404);
			return;
		}

		// Load publication project
		$pub->_project = new \Components\Projects\Models\Project($pub->project_id);
		$pub->_type    = $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup), $pub->curator);

		if (!$authorized)
		{
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}
			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
			return;
		}

		// Change publication status
		$row->state 		= 7; // pending author changes
		$row->reviewed 		= Date::toSql();
		$row->reviewed_by 	= User::get('id');

		if (!$row->store())
		{
			throw new Exception(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_FAILED'), 403);
			return;
		}

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new Models\Curation($manifest);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		// On after status change
		$this->onAfterStatusChange( $pub, $row->state );

		// Redirect to main listing
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=curation'),
			Lang::txt('COM_PUBLICATIONS_CURATION_SUCCESS_KICKBACK')
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
		$pid 	= $this->_id ? $this->_id : Request::getInt('id', 0);
		$vid 	= Request::getInt('vid', 0);
		$props  = Request::getVar( 'p', '' );
		$pass 	= Request::getInt( 'pass', 0 );
		$action = $pass ? 'pass' : 'fail';
		$review = Request::getVar( 'review', '' );

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0])) ? $parts[0] : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 0;

		if (!$block || !$step)
		{
			echo json_encode(array('success' => 0, 'error' => Lang::txt('Error parsing publication manifest')));
			return;
		}
		if ($action == 'fail' && !$review)
		{
			echo json_encode(array('success' => 0, 'error' => Lang::txt('Please explain why the item requires changes')));
			return;
		}

		// Load publication & version classes
		$objP  = new Tables\Publication( $this->database );
		$objV  = new Tables\Version( $this->database );
		$mt    = new Tables\MasterType( $this->database );

		if (!$vid || !$objV->load($vid))
		{
			echo json_encode(array('success' => 0, 'error' => Lang::txt('Error loading version')));
			return;
		}

		// Instantiate project publication
		$pub = $objP->getPublication($pid, $objV->version_number);

		// If publication not found, raise error
		if (!$pub)
		{
			echo json_encode(array('success' => 0, 'error' => Lang::txt('Error loading publication')));
			return;
		}

		// Load publication project
		$pub->_project = new \Components\Projects\Models\Project($pub->project_id);
		$pub->_type    = $mt->getType($pub->base);

		// Check authorization
		$authorized   = $this->_authorize(array($pub->_type->curatorgroup), $pub->curator);
		if (!$authorized)
		{
			echo json_encode(array('success' => 0, 'error' => Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED')));
			return;
		}

		// Get manifest from either version record (published) or master type
		$manifest   = $pub->curation
					? $pub->curation
					: $pub->_type->curation;

		// Get curation model
		$pub->_curationModel = new Models\Curation($manifest);

		// Set pub assoc and load curation
		$pub->_curationModel->setPubAssoc($pub);

		$data 					= new stdClass;
		$data->reviewed 		= Date::toSql();
		$data->reviewed_by 		= User::get('id');
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
				'error'  	=> Lang::txt('There was a problem saving curation item'),
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
		require_once( PATH_CORE . DS . 'components'
			. DS . 'com_projects' . DS . 'tables' . DS . 'activity.php');

		$activity = $status == 1
					? Lang::txt('COM_PUBLICATIONS_CURATION_ACTIVITY_PUBLISHED')
					: Lang::txt('COM_PUBLICATIONS_CURATION_ACTIVITY_KICKBACK');

		$pubtitle 	= \Hubzero\Utility\String::truncate($pub->title, 100);

		// Log activity in curation history
		$pub->_curationModel->saveHistory($pub, User::get('id'), $pub->state, $status, 1 );

		// Add activity
		$activity .= ' ' . strtolower(Lang::txt('version')) . ' ' . $pub->version_label . ' '
		. Lang::txt('COM_PUBLICATIONS_OF') . ' ' . strtolower(Lang::txt('publication')) . ' "'
		. $pubtitle . '" ';

		// Build return url
		$link 	= '/projects/' . $pub->_project->get('alias') . '/publications/'
				. $pub->id . '/?version=' . $pub->version_number;

		// Record activity
		$objAA = new \Components\Projects\Tables\Activity ( $this->database );
		$aid   = $objAA->recordActivity(
				$pub->project_id,
				User::get('id'),
				$activity,
				$pub->id,
				$pubtitle,
				$link,
				'publication',
				0,
				$admin = 1
		);

		// Start message
		$juri 	 = \JURI::getInstance();
		$sef	 = 'publications' . DS . $pub->id . DS . $pub->version_number;
		$link 	 = rtrim($juri->base(), DS) . DS . trim($sef, DS);
		$manage  = rtrim($juri->base(), DS) . DS . 'projects' . DS . $pub->_project->get('alias') . DS . 'publications' . DS . $pub->id . DS . $pub->version_number;
		$message  = $status == 1 ? Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_CURATOR_APPROVED') : Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_CURATOR_KICKED_BACK');

		if ($status != 1)
		{
			$message .= "\n" . "\n";
			$message .= Lang::txt('COM_PUBLICATIONS_CURATION_TAKE_ACTION') . ' ' . $manage;
		}
		else
		{
			$message .= ' ' . $link;
		}

		$pubtitle 	= \Hubzero\Utility\String::truncate($pub->title, 100);
		$subject 	= ucfirst(Lang::txt('COM_PUBLICATIONS_CURATION_VERSION'))
					. ' ' . $pub->version_label . ' ' . Lang::txt('COM_PUBLICATIONS_OF') . ' '
					. strtolower(Lang::txt('COM_PUBLICATIONS_PUBLICATION'))
					. ' "' . $pubtitle . '" ';
		$subject .= $status == 1
			? Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
			: Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_KICKED_BACK');

		// Get authors
		$pa = new Tables\Author( $this->database );
		$authors = $pa->getAuthors($pub->version_id, 1, 1, 1);

		// No authors – send to publication creator
		if (count($authors) == 0)
		{
			$authors = array($pub->created_by);
		}

		// Make sure there are no duplicates
		$authors = array_unique($authors);

		// Notify authors
		Helpers\Html::notify(
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
	protected function _authorize( $curatorgroups = array(), $curator = 0 )
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		$authorized = false;

		// Check if they're a site admin (from Joomla)
		if (User::authorize($this->_option, 'manage'))
		{
			$authorized = 'admin';
		}
		if ($curator && $curator == User::get('id'))
		{
			$authorized = 'owner';
			return $authorized;
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
					$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
					if ($ugs && count($ugs) > 0)
					{
						foreach ($ugs as $ug)
						{
							if ($group && $ug->cn == $group->get('cn'))
							{
								$authorized = $ug->cn == $curatorgroup ? 'curator' : 'limited';
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
		$rtrn = Request::getVar('REQUEST_URI',
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task), 'server');
		$this->setRedirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
			$this->_msg,
			'warning'
		);
	}
}