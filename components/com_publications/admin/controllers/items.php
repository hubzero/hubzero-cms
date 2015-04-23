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

namespace Components\Publications\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Publications\Tables;
use Components\Publications\Helpers;
use Components\Publications\Models;
use Exception;

/**
 * Manage publications
 */
class Items extends AdminController
{
	/**
	 * Executes a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Curation?
		$this->_curated = $this->config->get('curation', 0);

		$this->_task = strtolower(Request::getVar('task', '','request'));
		parent::execute();
	}

	/**
	 * Lists publications
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = Request::getState(
			$this->_option . '.publications.limit',
			'limit',
			Config::get('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = Request::getState(
			$this->_option . '.publications.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']   = urldecode(trim(Request::getState(
			$this->_option . '.publications.search',
			'search',
			''
		)));
		$this->view->filters['sortby']     = trim(Request::getState(
			$this->_option . '.publications.sortby',
			'filter_order',
			'created'
			));
		$this->view->filters['sortdir'] = trim(Request::getState(
			$this->_option . '.publications.sortdir',
			'filter_order_Dir',
			'DESC'
		));
		$this->view->filters['status']   = trim(Request::getState(
			$this->_option . '.publications.status',
			'status',
			'all'
		));
		$this->view->filters['dev'] = 1;
		$this->view->filters['category']  = trim(Request::getState(
			$this->_option . '.publications.category',
			'category',
			''
		));

		$model = new Tables\Publication($this->database);

		// Get record count
		$this->view->total = $model->getCount($this->view->filters, NULL, true);

		// Get publications
		$this->view->rows = $model->getRecords($this->view->filters, NULL, true);

		$this->view->config = $this->config;

		// Get <select> of types
		// Get types
		$rt = new Tables\Category( $this->database );
		$this->view->categories = $rt->getContribCategories();

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit form for a publication
	 *
	 * @param      integer $isnew Flag for editing (0) or creating new (1)
	 * @return     void
	 */
	public function editTask($isnew=0)
	{
		$this->view->isnew = $isnew;

		// Get the publications component config
		$this->view->config = $this->config;

		// Use new curation flow?
		$this->view->useBlocks  = $this->_curated;

		// Incoming publication ID
		$id = Request::getVar('id', array(0));
		if (is_array($id))
		{
			$id = $id[0];
		}

		// Is this a new publication? TBD
		if (!$id)
		{
			$this->view->isnew = 1;
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_CREATE_FRONT_END'),
				'notice'
			);
			return;
		}

		// Incoming version
		$version = Request::getVar( 'version', '' );

		// Grab some filters for returning to place after editing
		$this->view->return = array();
		$this->view->return['category'] = Request::getVar('category', '');
		$this->view->return['sortby']   = Request::getVar('sortby', '');
		$this->view->return['status']   = Request::getVar('status', '');

		// Instantiate publication object
		$objP = new Tables\Publication( $this->database );

		// Instantiate Version
		$this->view->row = new Tables\Version($this->database);

		// Check that version exists
		$version = $this->view->row->checkVersion($id, $version) ? $version : 'default';
		$this->view->version = $version;

		// Get publication information
		$this->view->pub = $objP->getPublication($id, $version);
		$objP->load($id);
		$this->view->objP = $objP;

		// If publication not found, raise error
		if (!$this->view->pub)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
			return;
		}

		// Load publication project
		$this->view->pub->_project = new \Components\Projects\Models\Project($this->view->pub->project_id);

		// Load version
		$vid = $this->view->pub->version_id;
		$this->view->row->load($vid);

		// Fail if checked out not by 'me'
		if ($this->view->objP->checked_out
		 && $this->view->objP->checked_out <> User::get('id'))
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_CHECKED_OUT'),
				'notice'
			);
			return;
		}

		// Editing existing
		$this->view->objP->checkout(User::get('id'));

		if (trim($this->view->row->published_down) == '0000-00-00 00:00:00')
		{
			$this->view->row->published_down = Lang::txt('COM_PUBLICATIONS_NEVER');
		}

		// Get name of resource creator
		$creator = User::getInstance($this->view->row->created_by);

		$this->view->row->created_by_name = $creator->get('name');
		$this->view->row->created_by_name = ($this->view->row->created_by_name)
			? $this->view->row->created_by_name : Lang::txt('COM_PUBLICATIONS_UNKNOWN');

		// Get name of last person to modify resource
		if ($this->view->row->modified_by)
		{
			$modifier = User::getInstance($this->view->row->modified_by);

			$this->view->row->modified_by_name = $modifier->get('name');
			$this->view->row->modified_by_name = ($this->view->row->modified_by_name)
				? $this->view->row->modified_by_name : Lang::txt('COM_PUBLICATIONS_UNKNOWN');
		}
		else
		{
			$this->view->row->modified_by_name = '';
		}

		// Build publication path
		$base_path = $this->view->config->get('webpath');
		$path = Helpers\Html::buildPubPath($id, $this->view->row->id, $base_path);

		// Archival package?
		$this->view->archPath = PATH_APP . $path . DS
			. Lang::txt('Publication').'_'.$id.'.zip';

		// Get params definitions
		$this->view->params  = new \JParameter(
			$this->view->row->params,
			JPATH_COMPONENT . DS . 'publications.xml'
		);

		// Get category
		$this->view->pub->_category = new Tables\Category( $this->database );
		$this->view->pub->_category->load($this->view->pub->category);
		$this->view->pub->_category->_params = new \JParameter( $this->view->pub->_category->params );

		// Get master type info
		$mt = new Tables\MasterType( $this->database );
		$this->view->pub->_type = $mt->getType($this->view->pub->base);
		$this->view->typeParams = new \JParameter( $this->view->pub->_type->params );

		// Get attachments
		$pContent = new Tables\Attachment( $this->database );
		$this->view->pub->_attachments = $pContent->sortAttachments ( $this->view->pub->version_id );

		// Curation
		if ($this->view->useBlocks)
		{
			// Get manifest from either version record (published) or master type
			$manifest   = $this->view->pub->curation
						? $this->view->pub->curation
						: $this->view->pub->_type->curation;

			// Get curation model
			$this->view->pub->_curationModel = new Models\Curation($manifest);

			// Set pub assoc and load curation
			$this->view->pub->_curationModel->setPubAssoc($this->view->pub);
		}

		// Get pub authors
		$pAuthors 			= new Tables\Author( $this->database );
		$this->view->pub->_authors 		= $pAuthors->getAuthors($this->view->pub->version_id);
		$this->view->pub->_submitter 	= $pAuthors->getSubmitter($this->view->pub->version_id, $this->view->pub->created_by);

		// Get tags on this item
		$tagsHelper = new Helpers\Tags( $this->database );
		$tags_men = $tagsHelper->get_tags_on_object($this->view->pub->id, 0, 0, 0, 0, true);

		$mytagarray = array();
		foreach ($tags_men as $tag_men)
		{
			$mytagarray[] = $tag_men['raw_tag'];
		}
		$this->view->tags = implode(', ', $mytagarray);

		// Get selected license
		$objL = new Tables\License( $this->database );
		$this->view->license = $objL->getPubLicense( $this->view->pub->version_id );
		$this->view->licenses = $objL->getLicenses();

		// Get groups
		$filters = array(
			'authorized' => 'admin',
			'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
			'type'       => array(1, 3),
			'sortby'     => 'description'
		);
		$this->view->groups = \Hubzero\User\Group::find($filters);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit content item
	 *
	 * @return     void
	 */
	public function editcontentTask()
	{
		// Incoming
		$id = Request::getInt( 'id', 0 );
		$el = Request::getInt( 'el', 0 );
		$v  = Request::getInt( 'v', 0 );

		$objP = new Tables\Publication( $this->database );

		// Get publication information
		$this->view->pub = $objP->getPublication($id, $v);

		// If publication not found, raise error
		if (!$this->view->pub)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
			return;
		}

		// Get the publications component config
		$this->view->config = $this->config;

		// Use new curation flow?
		$this->view->useBlocks  = $this->_curated;

		if (!$this->_curated)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_CURATION_NEEDED'));
		}
		else
		{
			// Load publication project
			$this->view->pub->_project = new \Components\Projects\Models\Project($this->view->pub->project_id);

			// Get master type info
			$mt = new Tables\MasterType( $this->database );
			$this->view->pub->_type = $mt->getType($this->view->pub->base);
			$this->view->typeParams = new \JParameter( $this->view->pub->_type->params );

			// Get attachments
			$pContent = new Tables\Attachment( $this->database );
			$this->view->pub->_attachments = $pContent->sortAttachments ( $this->view->pub->version_id );

			// Get manifest from either version record (published) or master type
			$manifest   = $this->view->pub->curation
						? $this->view->pub->curation
						: $this->view->pub->_type->curation;

			// Get curation model
			$this->view->pub->_curationModel = new Models\Curation($manifest);

			// Set pub assoc and load curation
			$this->view->pub->_curationModel->setPubAssoc($this->view->pub);

			if (!$el)
			{
				$this->setError();
			}
			else
			{
				$this->view->elementId = $el;
				$this->view->element = $this->view->pub->_curationModel->getElementManifest($el, 'content');
			}
		}

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
	 * Save content item details
	 *
	 * @return     void
	 */
	public function savecontentTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$el 	 = Request::getInt( 'el', 0 );
		$id 	 = Request::getInt( 'id', 0 );
		$version = Request::getVar( 'version', '' );
		$params  = Request::getVar( 'params', array(), 'request', 'array' );
		$attachments = Request::getVar( 'attachments', array(), 'request', 'array' );

		// Load publication model
		$this->model  = new Models\Publication( $id, $version);

		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
			return;
		}

		if (!$this->_curated)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_CURATION_NEEDED'), 404);
			return;
		}
		else
		{
			// Set curation
			$this->model->setCuration();

			// Save attachments
			if (!empty($attachments))
			{
				foreach ($attachments as $attachId => $attach )
				{
					$pContent = new Tables\Attachment( $this->database );
					if ($pContent->load($attachId))
					{
						$pContent->title = $attach['title'];
						$pContent->store();
					}
				}
			}
		}

		// Set redirect URL
		$url = Route::url('index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version, false);

		// Redirect back to publication
		$this->setRedirect(
			$url,
			Lang::txt('COM_PUBLICATIONS_SUCCESS_SAVED_CONTENT')
		);
	}

	/**
	 * Add author form
	 *
	 * @return void
	 */
	public function addauthorTask()
	{
		$this->editauthorTask();
	}

	/**
	 * Edit author name and details
	 *
	 * @return     void
	 */
	public function editauthorTask()
	{
		// Incoming
		$author = Request::getInt( 'author', 0 );

		$this->view->setLayout('editauthor');

		$this->view->author = new Tables\Author( $this->database );
		if ($this->_task == 'editauthor' && !$this->view->author->load($author))
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_NO_AUTHOR_RECORD'), 404);
			return;
		}

		// Version ID
		$vid = Request::getInt( 'vid', $this->view->author->publication_version_id );

		$this->view->row = new Tables\Version( $this->database );
		$this->view->pub = new Tables\Publication( $this->database );

		// Load version
		if (!$this->view->row->load($vid))
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
			return;
		}

		// Load publication
		$pid = Request::getInt( 'pid', $this->view->row->publication_id );
		if (!$this->view->pub->load($pid))
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
			return;
		}

		// Instantiate project owner
		$objO = new \Components\Projects\Tables\Owner($this->database);
		$filters 					= array();
		$filters['limit']    		= 0;
		$filters['start']    		= 0;
		$filters['sortby']   		= 'name';
		$filters['sortdir']  		= 'ASC';
		$filters['status']   		= 'active';

		// Get all active team members
		$this->view->team = $objO->getOwners($this->view->pub->project_id, $filters);

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
	 * Delete author
	 *
	 * @return     void
	 */
	public function deleteauthorTask()
	{
		// Incoming
		$aid = Request::getInt( 'aid', 0 );

		$pAuthor = new Tables\Author( $this->database );
		if (!$pAuthor->load($aid))
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_AUTHOR'),
				'error'
			);
			return;
		}

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;

		// Instantiate Version
		$row = new Tables\Version($this->database);
		if ($row->load($pAuthor->publication_version_id))
		{
			$url .= '&task=edit' . '&id[]=' . $row->publication_id
				. '&version=' . $row->version_number;
		}

		if (!$pAuthor->delete())
		{
			$this->setRedirect(
				Route::url($url, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_FAILED_TO_DELETE_AUTHOR'),
				'error'
			);
			return;
		}

		// Redirect back to publication
		$this->setRedirect(
			Route::url($url, false),
			Lang::txt('COM_PUBLICATIONS_SUCCESS_DELETE_AUTHOR')
		);
		return;
	}

	/**
	 * Save author order
	 *
	 * @return     void
	 */
	public function saveauthororderTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$id       = Request::getInt('id', 0);
		$version  = Request::getVar( 'version', '' );
		$neworder = Request::getVar('list', '');

		// Set redirect URL
		$url = Route::url('index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version, false);

		if (!$neworder)
		{
			// Nothing to save
			$this->setRedirect(
				$url
			);
			return;
		}

		// Load publication model
		$model  = new Models\Publication( $id, $version);

		if (!$model->exists())
		{
			$this->setRedirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Load publication project
		$model->project();

		// Get language file
		Lang::load('plg_projects_publications');

		// Save via block
		$blocksModel = new Models\Blocks($this->database);
		$block = $blocksModel->loadBlock('authors');

		$block->reorder(NULL, 0, $model, User::get('id'));
		if ($block->getError())
		{
			$this->setRedirect(
				$url,
				$block->getError(),
				'error'
			);
			return;
		}
		else
		{
			// Update DOI in case changes
			if ($model->version->doi)
			{
				// Get DOI service
				$doiService = new Models\Doi($model);

				// Get updated authors
				$pAuthor = new Tables\Author( $this->database );
				$authors = $pAuthor->getAuthors($model->version->id);
				$doiService->set('authors', $authors);

				// Update DOI
				if (preg_match("/" . $doiService->_configs->shoulder . "/", $model->version->doi->doi))
				{
					$doiService->update($model->version->doi, true);
				}
			}

			// Redirect back to publication
			$this->setRedirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_SUCCESS_SAVED_AUTHOR')
			);
			return;
		}
	}

	/**
	 * Save author name and details
	 *
	 * @return     void
	 */
	public function saveauthorTask()
	{
		// Check for request forgeries
		Request::checkToken('get') or Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$author  = Request::getInt( 'author', 0 );
		$id 	 = Request::getInt( 'id', 0 );
		$version = Request::getVar( 'version', '' );

		$firstName 	= Request::getVar( 'firstName', '', 'post' );
		$lastName 	= Request::getVar( 'lastName', '', 'post' );
		$org 		= Request::getVar( 'organization', '', 'post' );

		// Set redirect URL
		$url = Route::url('index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version, false);

		// Load publication model
		$model  = new Models\Publication( $id, $version);

		if (!$model->exists())
		{
			$this->setRedirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Load publication project
		$model->project();

		// Get language file
		Lang::load('plg_projects_publications');

		// Save via block
		$blocksModel = new Models\Blocks($this->database);
		$block = $blocksModel->loadBlock('authors');

		if ($author)
		{
			$block->saveItem(NULL, 0, $model, User::get('id'), 0 , $author);
		}
		else
		{
			$block->addItem(NULL, 0, $model, User::get('id'));
		}

		if ($block->getError())
		{
			$this->setRedirect(
				$url,
				$block->getError(),
				'error'
			);
			return;
		}
		else
		{
			// Update DOI in case changes
			if ($model->version->doi)
			{
				// Get DOI service
				$doiService = new Models\Doi($model);

				// Get updated authors
				$pAuthor = new Tables\Author( $this->database );
				$authors = $pAuthor->getAuthors($model->version->id);
				$doiService->set('authors', $authors);

				// Update DOI
				if (preg_match("/" . $doiService->_configs->shoulder . "/", $model->version->doi->doi))
				{
					$doiService->update($model->version->doi, true);
				}
			}

			// Redirect back to publication
			$this->setRedirect(
				$url,
				Lang::txt('COM_PUBLICATIONS_SUCCESS_SAVED_AUTHOR')
			);
			return;
		}
	}

	/**
	 * Save a publication and fall through to edit view
	 *
	 * @return void
	 */
	public function applyTask()
	{
		$this->saveTask(true);
	}

	/**
	 * Saves a publication
	 * Redirects to main listing
	 *
	 * @return     void
	 */
	public function saveTask($redirect = false)
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$id 			= Request::getInt( 'id', 0 );
		$action 		= Request::getVar( 'admin_action', '' );
		$published_up 	= Request::getVar( 'published_up', '' );
		$version 	    = Request::getVar( 'version', 'default' );

		// Is this a new publication? Cannot create via back-end
		$isnew = $id ? 0 : 1;
		if (!$id)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION'),
				'error'
			);
			return;
		}

		// Load publication model
		$this->model  = new Models\Publication( $id, $version);

		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
			return;
		}

		// Checkin resource
		$this->model->publication->checkin();

		// Set redirect URL
		$url = Route::url('index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version, false);

		$authors = $this->model->authors();
		$project = $this->model->project();

		// Curation?
		if ($this->_curated)
		{
			$this->model->setCuration();
			$requireDoi = isset($this->model->_curationModel->_manifest->params->require_doi)
						? $this->model->_curationModel->_manifest->params->require_doi : 0;
		}
		else
		{
			$requireDoi = true;
		}

		// Incoming updates
		$title 			= trim(Request::getVar( 'title', '', 'post' ));
		$title 			= htmlspecialchars($title);
		$abstract 		= trim(Request::getVar( 'abstract', '', 'post' ));
		$abstract 		= \Hubzero\Utility\Sanitize::clean(htmlspecialchars($abstract));
		$description 	= trim(Request::getVar( 'description', '', 'post', 'none', 2 ));
		$release_notes 	= stripslashes(trim(Request::getVar( 'release_notes', '', 'post', 'none', 2 )));
		$group_owner	= Request::getInt( 'group_owner', 0, 'post' );
		$published_up 	= trim(Request::getVar( 'published_up', '', 'post' ));
		$published_down = trim(Request::getVar( 'published_down', '', 'post' ));
		$state 			= Request::getInt( 'state', 0 );
		$metadata 		= '';
		$activity 		= '';

		// Save publication record
		$this->model->publication->alias    = trim(Request::getVar( 'alias', '', 'post' ));
		$this->model->publication->category = trim(Request::getInt( 'category', 0, 'post' ));
		if (!$project->owned_by_group)
		{
			$this->model->publication->group_owner = $group_owner;
		}
		$this->model->publication->store();

		// Get metadata
		if (isset($_POST['nbtag']))
		{
			$category = $this->model->category();

			$fields = array();
			if (trim($category->customFields) != '')
			{
				$fs = explode("\n", trim($category->customFields));
				foreach ($fs as $f)
				{
					$fields[] = explode('=', $f);
				}
			}

			$nbtag = Request::getVar( 'nbtag', array(), 'request', 'array' );
			foreach ($nbtag as $tagname => $tagcontent)
			{
				$tagcontent = trim(stripslashes($tagcontent));
				if ($tagcontent != '')
				{
					$metadata .= "\n".'<nb:' . $tagname . '>' . $tagcontent . '</nb:' . $tagname . '>' . "\n";
				}
				else
				{
					foreach ($fields as $f)
					{
						if ($f[0] == $tagname && end($f) == 1)
						{
							echo Helpers\Html::alert(Lang::txt('COM_PUBLICATIONS_REQUIRED_FIELD_CHECK', $f[1]));
							exit();
						}
					}
				}
			}
		}

		// Save incoming
		$this->model->version->title        = $title;
		$this->model->version->abstract     = \Hubzero\Utility\String::truncate($abstract, 250);
		$this->model->version->description  = $description;
		$this->model->version->metadata     = $metadata;
		$this->model->version->release_notes= $release_notes;
		$this->model->version->license_text = trim(Request::getVar( 'license_text', '', 'post' ));
		$this->model->version->license_type = Request::getInt( 'license_type', 0, 'post' );
		$this->model->version->access       = Request::getInt( 'access', 0, 'post' );

		// DOI manually entered?
		$doi = trim(Request::getVar( 'doi', '', 'post' ));
		if ($doi && (!$this->model->version->doi
			|| !preg_match("/" . $doiService->_configs->shoulder
			. "/", $this->model->version->doi)))
		{
			$this->model->version->doi = $doi;
		}

		$this->model->version->published_up  = $published_up
							? Date::of($published_up, Config::get('offset'))->toSql()
							: '0000-00-00 00:00:00';
		$this->model->version->published_down= $published_down && trim($published_down) != 'Never'
							? Date::of($published_down, Config::get('offset'))->toSql()
							: '0000-00-00 00:00:00';

		// Determine action (if status is flipped)
		if ($this->model->version->state != $state)
		{
			switch ($state)
			{
				case 1:
					$action = $this->model->version->state == 0 ? 'republish' : 'publish';
					break;
				case 0:
					$action = 'unpublish';
					break;
				case 3:
				case 4:
					$action = 'revert';
					break;
			}

			$this->model->version->state = $state;
		}

		// Update DOI with latest information
		if ($this->model->version->doi && !$action)
		{
			// Get DOI service
			$doiService = new Models\Doi($this->model);

			// Update DOI if locally issued
			if (preg_match("/" . $doiService->_configs->shoulder . "/", $this->model->version->doi))
			{
				$doiService->update($this->model->version->doi, true);
			}
		}

		// Incoming tags
		$tags = Request::getVar('tags', '', 'post');

		// Save the tags
		$rt = new Helpers\Tags($this->database);
		$rt->tag_object(User::get('id'), $id, $tags, 1, true);

		// Email config
		$pubtitle 	= \Hubzero\Utility\String::truncate($this->model->version->title, 100);
		$subject 	= Lang::txt('Version') . ' ' . $this->model->version->version_label . ' '
					. Lang::txt('COM_PUBLICATIONS_OF') . ' '
					. strtolower(Lang::txt('COM_PUBLICATIONS_PUBLICATION'))
					. ' "' . $pubtitle . '" ';
		$sendmail 	= 0;
		$message 	= rtrim(\Hubzero\Utility\Sanitize::clean(Request::getVar( 'message', '' )));
		$output 	= Lang::txt('COM_PUBLICATIONS_SUCCESS_SAVED_ITEM');

		// Admin actions
		if ($action)
		{
			$output = '';
			require_once( PATH_CORE . DS . 'components'
				. DS . 'com_projects'. DS . 'tables' . DS . 'activity.php');
			$objAA = new \Components\Projects\Tables\Activity ( $this->database );
			switch ($action)
			{
				case 'publish':
				case 'republish':

					$activity = $action == 'publish'
						? Lang::txt('COM_PUBLICATIONS_ACTIVITY_ADMIN_PUBLISHED')
						: Lang::txt('COM_PUBLICATIONS_ACTIVITY_ADMIN_REPUBLISHED');
					$subject .= $action == 'publish'
						? Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
						: Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_REPUBLISHED');

					$this->model->version->state = 1;

					// Get DOI service
					$doiService = new Models\Doi($this->model);

					// Is service enabled? - Issue/update a DOI
					if ($doiService->on())
					{
						if ($this->model->version->doi
							&& preg_match("/" . $doiService->_configs->shoulder
							. "/", $this->model->version->doi))
						{
							// Update
							$doiService->update($this->model->version->doi, true);
							if ($doiService->getError())
							{
								$this->setError($doiService->getError());
							}
						}
						elseif ($requireDoi)
						{
							// Register
							$doi = $doiService->register(true);

							if (!$doi)
							{
								$this->setRedirect(
									$url, Lang::txt('COM_PUBLICATIONS_ERROR_DOI')
									. ' ' . $doiService->getError(), 'error');
								return;
							}
							else
							{
								$this->model->version->doi = $doi;
							}
						}
					}

					// Save date accepted
					if ($action == 'publish')
					{
						$this->model->version->accepted = Date::toSql();

						if ($this->_curated)
						{
							// Store curation manifest
							$this->model->version->curation = json_encode($this->model->_curationModel->_manifest);

							// Mark as curated/non-curated
							$curated = $this->_curated ? 1 : 2;
							$this->model->version->saveParam($this->model->version->id, 'curated', $curated);
						}

						// Check if publication is within grace period (published status)
						$gracePeriod = $this->config->get('graceperiod', 0);
						$allowArchive = $gracePeriod ? false : true;
						if ($allowArchive && $this->model->version->accepted && $this->model->version->accepted != '0000-00-00 00:00:00')
						{
							$monthFrom = \JFactory::getDate($this->model->version->accepted . '+1 month')->toSql();
							if (strtotime($monthFrom) < strtotime(\JFactory::getDate()))
							{
								$allowArchive = true;
							}
						}

						// Run mkAIP if no grace period set or passed
						if (!$this->getError() && $this->model->version->doi
							&& $allowArchive == true && (!$this->model->version->archived
							|| $this->model->version->archived == '0000-00-00 00:00:00')
							&& Helpers\Utilities::mkAip($this->model->version))
						{
							$this->model->version->archived = Date::toSql();
						}
					}

					if (!$this->getError())
					{
						$output .= ' ' . Lang::txt('COM_PUBLICATIONS_ITEM').' ';
						$output .= $action == 'publish'
							? Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
							: Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_REPUBLISHED');
					}
					break;

				case 'revert':
					$this->model->version->state = $state ? $state : 4;
					$activity = Lang::txt('COM_PUBLICATIONS_ACTIVITY_ADMIN_REVERTED');
					$subject .= Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_REVERTED');
					$output .= ' '.Lang::txt('COM_PUBLICATIONS_ITEM').' ';
					$output .= Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_REVERTED');
					break;

				case 'unpublish':
					$this->model->version->state = 0;
					$this->model->version->published_down = Date::toSql();
					$activity = Lang::txt('COM_PUBLICATIONS_ACTIVITY_ADMIN_UNPUBLISHED');
					$subject .= Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_UNPUBLISHED');

					$output .= ' '.Lang::txt('COM_PUBLICATIONS_ITEM').' ';
					$output .= Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_UNPUBLISHED');
					break;
			}
		}

		// Updating entry if anything changed
		if (!$this->getError())
		{
			$this->model->version->modified    = Date::toSql();
			$this->model->version->modified_by = User::get('id');

			// Store content
			if (!$this->model->version->store())
			{
				$this->setRedirect(
					$url, $this->model->version->getError(), 'error'
				);
				return;
			}
			elseif ($action)
			{
				// Add activity
				$activity .= ' ' . strtolower(Lang::txt('version'))
						  . ' ' . $this->model->version->version_label .' '
						  . Lang::txt('COM_PUBLICATIONS_OF') . ' '
						  . strtolower(Lang::txt('publication')) . ' "'
						  . $pubtitle.'" ';

				// Build return url
				$link 	= '/projects/' . $project->alias . '/publications/'
						. $id . '/?version=' . $this->model->version->version_number;

				if ($action != 'message' && !$this->getError())
				{
					$aid = $objAA->recordActivity( $project->id, User::get('id'),
						$activity, $id, $pubtitle, $link, 'publication', 0, $admin = 1 );
					$sendmail = $this->config->get('email') ? 1 : 0;

					// Append comment to activity
					if ($message && $aid)
					{
						require_once( PATH_CORE . DS . 'components'
							. DS . 'com_projects' . DS . 'tables' . DS . 'comment.php');
						$objC = new \Components\Projects\Tables\Comment( $this->database );

						$comment = \Hubzero\Utility\String::truncate($message, 250);
						$comment = \Hubzero\Utility\Sanitize::stripAll($comment);

						$objC->itemid           = $aid;
						$objC->tbl              = 'activity';
						$objC->parent_activity  = $aid;
						$objC->comment          = $comment;
						$objC->admin            = 1;
						$objC->created          = Date::toSql();
						$objC->created_by       = User::get('id');
						$objC->store();

						// Get new entry ID
						if (!$objC->id)
						{
							$objC->checkin();
						}

						$objAA = new \Components\Projects\Tables\Activity ( $this->database );

						if ( $objC->id )
						{
							$what = Lang::txt('COM_PROJECTS_AN_ACTIVITY');
							$curl = '#tr_'.$aid; // same-page link
							$caid = $objAA->recordActivity( $pub->project_id, User::get('id'),
							Lang::txt('COM_PROJECTS_COMMENTED') . ' ' . Lang::txt('COM_PROJECTS_ON')
								. ' ' . $what, $objC->id, $what, $curl, 'quote', 0, 1 );

							// Store activity ID
							if ($caid)
							{
								$objC->activityid = $aid;
								$objC->store();
							}
						}
					}
				}
			}
		}

		// Save parameters
		$params = Request::getVar('params', '', 'post');
		if (is_array($params))
		{
			foreach ($params as $k => $v)
			{
				$this->model->version->saveParam($this->model->version->id, $k, $v);
			}
		}

		// Do we have a message to send?
		if ($message)
		{
			$subject .= ' - '.Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_NEW_MESSAGE');
			$sendmail = 1;
			$output  .= ' ' . Lang::txt('COM_PUBLICATIONS_MESSAGE_SENT');
		}

		// Send email
		if ($sendmail && !$this->getError())
		{
			// Get ids of publication authors with accounts
			$objA   = new Tables\Author( $this->_db );
			$notify = $objA->getAuthors($this->model->version->id, 1, 1, 1, true);
			$notify[] = $this->model->version->created_by;
			$notify = array_unique($notify);

			$this->_emailContributors($this->model->version, $project, $subject, $message, $notify, $action);
		}

		// Append any errors
		if ($this->getError())
		{
			$output .= ' ' . $this->getError();
		}

		// Redirect to edit view?
		if ($redirect)
		{
			$this->setRedirect(
				$url,
				$output
			);
		}
		else
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option
				. '&controller=' . $this->_controller, false),
				$output
			);
		}

		return;
	}

	/**
	 * Collect DOI metadata
	 *
	 * @param      object $row      Publication
	 * @return     void
	 */
	private function _collectMetadata($row, $objP, $authors)
	{
		// Get type
		$objT = new Tables\Category($this->database);
		$objT->load($objP->category);
		$typetitle = ucfirst($objT->alias);

		// Collect metadata
		$metadata = array();
		$metadata['typetitle'] 		= $typetitle ? $typetitle : 'Dataset';
		$metadata['resourceType'] 	= isset($objT->dc_type) && $objT->dc_type ? $objT->dc_type : 'Dataset';
		$metadata['language'] 		= 'en';

		// Get dc:contibutor
		$project = new \Components\Projects\Tables\Project($this->database);
		$project->load($objP->project_id);
		$profile = \Hubzero\User\Profile::getInstance(\JFactory::getUser()->get('id'));
		$owner 	 = $project->owned_by_user ? $project->owned_by_user : $project->created_by_user;
		if ($profile->load( $owner ))
		{
			$metadata['contributor'] = $profile->get('name');
		}

		// Get previous version DOI
		$lastPub = $row->getLastPubRelease($objP->id);
		if ($lastPub && $lastPub->doi)
		{
			$metadata['relatedDoi'] = $row->version_number > 1 ? $lastPub->doi : '';
		}

		// Get license type
		$objL = new Tables\License( $this->database);
		if ($objL->loadLicense($row->license_type))
		{
			$metadata['rightsType'] = isset($objL->dc_type) && $objL->dc_type ? $objL->dc_type : 'other';
			$metadata['license'] = $objL->title;
		}

		return $metadata;
	}

	/**
	 * Sends a message to authors (or creator) of a publication
	 *
	 * @param      object $row      Publication
	 * @param      object $project  Project
	 * @return     void
	 */
	private function _emailContributors($row, $project, $subject = '', $message = '',
		$authors = array(), $action = 'publish')
	{
		if (!$row || !$project)
		{
			return false;
		}

		// Get pub authors' ids
		if (empty($authors))
		{
			$pa = new Tables\Author( $this->database );
			$authors = $pa->getAuthors($row->id, 1, 1, 1);
		}

		// No authors – send to publication creator
		if (count($authors) == 0)
		{
			$authors = array($row->created_by);
		}

		// Make sure there are no duplicates
		$authors = array_unique($authors);

		if ($authors && count($authors) > 0)
		{
			// Email all the contributors
			$from = array();
			$from['email'] = Config::get('config.mailfrom');
			$from['name']  = Config::get('config.sitename') . ' ' . Lang::txt('PUBLICATIONS');

			$subject = $subject
				? $subject : Lang::txt('COM_PUBLICATIONS_STATUS_UPDATE');

			// Get message body
			$eview 					= new \Hubzero\Component\View( array('name'=>'emails', 'layout' => 'admin_plain' ) );
			$eview->option 			= $this->_option;
			$eview->subject 		= $subject;
			$eview->action 			= $action;
			$eview->row 			= $row;
			$eview->message			= $message;
			$eview->project			= $project;

			$body = array();
			$body['plaintext'] 	= $eview->loadTemplate();
			$body['plaintext'] 	= str_replace("\n", "\r\n", $body['plaintext']);

			// HTML email
			$eview->setLayout('admin_html');
			$body['multipart'] = $eview->loadTemplate();
			$body['multipart'] = str_replace("\n", "\r\n", $body['multipart']);

			// Send message
			if (!Event::trigger('xmessage.onSendMessage', array(
				'publication_status_changed',
				$subject,
				$body,
				$from,
				$authors,
				$this->_option)
			))
			{
				$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_FAILED_MESSAGE_AUTHORS'));
			}
		}
	}

	/**
	 * Displays versions of a publication
	 *
	 * @return     void
	 */
	public function versionsTask()
	{
		// Get the publications component config
		$this->view->config = $this->config;

		// Incoming publication ID
		$id = Request::getInt('id', 0);

		// Need ID
		if (!$id)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION'),
				'notice'
			);
			return;
		}

		// Grab some filters for returning to place after editing
		$this->view->return = array();
		$this->view->return['cat']   = Request::getVar('cat', '');
		$this->view->return['sortby']   = Request::getVar('sortby', '');
		$this->view->return['status'] = Request::getVar('status', '');

		// Instantiate project publication
		$objP = new Tables\Publication( $this->database );
		$objV = new Tables\Version( $this->database );

		$this->view->pub = $objP->getPublication($id);
		if (!$this->view->pub)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION'), 404);
			return;
		}

		// Get versions
		$this->view->versions = $objV->getVersions( $id, $filters = array('withdev' => 1));

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Removes a publication
	 * Redirects to main listing
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = Request::getVar('id', array(0));
		$erase = Request::getInt('erase', 1);

		// Ensure we have some IDs to work with
		if (count($ids) < 1)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION'),
				'notice'
			);
			return;
		}

		$version = count($ids) == 1 ? Request::getVar( 'version', 'all' ) : 'all';

		jimport('joomla.filesystem.folder');

		require_once(PATH_CORE . DS . 'components'
			. DS . 'com_projects' . DS . 'tables' . DS . 'activity.php');

		foreach ($ids as $id)
		{
			// Load publication
			$objP = new Tables\Publication( $this->database );
			if (!$objP->load($id))
			{
				throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
				return;
			}

			$projectId = $objP->project_id;

			$row = new Tables\Version( $this->database );

			// Get versions
			$versions = $row->getVersions( $id, $filters = array('withdev' => 1));

			if ($version != 'all' && count($versions) > 1)
			{
				// Check that version exists
				$version = $row->checkVersion($id, $version) ? $version : 'dev';

				// Load version
				if (!$row->loadVersion($id, $version))
				{
					throw new Exception(Lang::txt('COM_PUBLICATIONS_VERSION_NOT_FOUND'), 404);
					return;
				}

				// Cannot delete main version if other versions exist
				if ($row->main)
				{
					throw new Exception(Lang::txt('COM_PUBLICATIONS_VERSION_MAIN_ERROR_DELETE'), 404);
					return;
				}
				if ($erase == 1)
				{
					// Delete the version
					if ($row->delete())
					{
						// Delete associations to the version
						$this->deleteVersionExistence($row->id, $id);
					}
				}
				else
				{
					$row->state = 2;
					$row->store();
				}
			}
			else
			{
				// Delete all versions
				$i = 0;
				foreach ($versions as $v)
				{
					$objV = new Tables\Version( $this->database );
					if ($objV->loadVersion($id, $v->version_number))
					{
						if ($erase == 1)
						{
							// Delete the version
							if ($objV->delete())
							{
								// Delete associations to the version
								$this->deleteVersionExistence($v->id, $id);
								$i++;
							}
						}
						else
						{
							$objV->state = 2;
							$objV->store();
						}
					}
				}

				// All versions deleted?
				if ($i == count($versions))
				{
					// Delete pub record and all associations
					$objP->delete($id);
					$objP->deleteExistence($id);

					// Delete related publishing activity from feed
					$objAA = new \Components\Projects\Tables\Activity( $this->database );
					$objAA->deleteActivityByReference($projectId, $id, 'publication');

					// Build publication path
					$path    =  PATH_APP . DS . trim($this->config->get('webpath'), DS)
							. DS .  \Hubzero\Utility\String::pad( $id );

					// Delete all files
					if (is_dir($path))
					{
						\JFolder::delete($path);
					}
				}
			}
		}

		// Redirect
		$output = ($version != 'all')
			? Lang::txt('COM_PUBLICATIONS_SUCCESS_VERSION_DELETED')
			: Lang::txt('COM_PUBLICATIONS_SUCCESS_RECORDS_DELETED') . ' (' . count($ids) . ')';
		$this->setRedirect(
			$this->buildRedirectURL(),
			$output
		);

		return;
	}

	/**
	 * Deletes assoc with pub version
	 *
	 * @return     void
	 */
	public function deleteVersionExistence($vid, $pid)
	{
		// Delete authors
		$pa = new Tables\Author( $this->database );
		$authors = $pa->deleteAssociations($vid);

		// Delete attachments
		$pContent = new Tables\Attachment( $this->database );
		$pContent->deleteAttachments($vid);

		// Delete screenshots
		$pScreenshot = new Tables\Screenshot( $this->database );
		$pScreenshot->deleteScreenshots($vid);

		// Delete access accosiations
		$pAccess = new Tables\Access( $this->database );
		$pAccess->deleteGroups($vid);

		// Delete audience
		$pAudience = new Tables\Audience( $this->database );
		$pAudience->deleteAudience($vid);

		// Build publication path
		$path = Helpers\Html::buildPubPath($pid, $vid, $this->config->get('webpath'), '', 1);

		// Delete all files
		if (is_dir($path))
		{
			\JFolder::delete($path);
		}

		return true;
	}

	/**
	 * Checks in a checked-out publication and redirects
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$id  = Request::getInt('id', 0);

		// Checkin the resource
		$row = new Tables\Publication($this->database);
		$row->load($id);
		$row->checkin();

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option
			. '&controller=' . $this->_controller, false)
		);

		return;
	}

	/**
	 * Resets the rating of a resource
	 * Redirects to edit task for the resource
	 *
	 * @return     void
	 */
	public function resetratingTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = new Tables\Publication($this->database);
			$row->load($id);
			$row->rating = '0.0';
			$row->times_rated = '0';
			$row->store();
			$row->checkin();

			$this->_message = Lang::txt('COM_PUBLICATIONS_SUCCESS_RATING_RESET');
		}

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option
			. '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $id, false),
			$this->_message
		);
	}

	/**
	 * Resets the ranking of a resource
	 * Redirects to edit task for the resource
	 *
	 * @return     void
	 */
	public function resetrankingTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$id  = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = new Tables\Publication($this->database);
			$row->load($id);
			$row->ranking = '0';
			$row->store();
			$row->checkin();

			$this->_message = Lang::txt('COM_PUBLICATIONS_SUCCESS_RANKING_RESET');
		}

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option
			. '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $id, false),
			$this->_message
		);
	}

	/**
	 * Produces archival package for publication
	 * Redirects to edit task for the resource
	 *
	 * @return     void
	 */
	public function archiveTask()
	{
		// Incoming
		$pid 		= Request::getInt('pid', 0);
		$vid 		= Request::getInt('vid', 0);
		$version 	= Request::getVar( 'version', '' );

		// Load publication & version classes
		$objP  = new Tables\Publication( $this->database );
		$objV  = new Tables\Version( $this->database );
		$mt    = new Tables\MasterType( $this->database );

		if (!$objP->load($pid) || !$objV->load($vid))
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
			return;
		}
		$pub = $objP->getPublication($pid, $objV->version_number, $objP->project_id);
		if (!$pub)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION'), 404);
			return;
		}

		$url = Route::url('index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $pid . '&version=' . $version, false);

		if ($this->_curated)
		{
			$pub->version 	= $pub->version_number;

			// Load publication project
			$pub->_project = new \Components\Projects\Models\Project($pub->project_id);

			// Get master type info
			$mt = new Tables\MasterType( $this->database );
			$pub->_type = $mt->getType($pub->base);
			$typeParams = new \JParameter( $pub->_type->params );

			// Get attachments
			$pContent = new Tables\Attachment( $this->database );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );

			// Get authors
			$pAuthors 			= new Tables\Author( $this->database );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);

			// Get manifest from either version record (published) or master type
			$manifest   = $pub->curation
						? $pub->curation
						: $pub->_type->curation;

			// Get curation model
			$pub->_curationModel = new Models\Curation($manifest);

			// Set pub assoc and load curation
			$pub->_curationModel->setPubAssoc($pub);

			// Produce archival package
			if (!$pub->_curationModel->package())
			{
				// Checkin the resource
				$objP->checkin();

				// Redirect
				$this->setRedirect( $url, Lang::txt('COM_PUBLICATIONS_ERROR_ARCHIVAL'), 'error');
				return;
			}
		}
		else
		{
			// Archival for non-curated publications
			$result = Event::trigger( 'projects.archivePub', array($pid, $vid) );
		}

		$this->_message = Lang::txt('COM_PUBLICATIONS_SUCCESS_ARCHIVAL');

		// Checkin the resource
		$objP->checkin();

		// Redirect
		$this->setRedirect( $url );
	}

	/**
	 * Checks-in one or more resources
	 * Redirects to the main listing
	 *
	 * @return     void
	 */
	public function checkinTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$id  = Request::getInt('id', 0);

		if ($id)
		{
			// Load the object and checkin
			$row = new Tables\Publication($this->database);
			$row->load($id);
			$row->checkin();
		}

		// Redirect
		$this->_redirect = Route::url('index.php?option=' . $this->_option
			. '&controller=' . $this->_controller, false);
	}

	/**
	 * Builds the appropriate URL for redirction
	 *
	 * @return     string
	 */
	private function buildRedirectURL()
	{
		$url  = Route::url('index.php?option=' . $this->_option
			. '&controller=' . $this->_controller, false);

		// Incoming
		$id  = Request::getInt('id', 0);
		$url .= $id ? '&task=edit&id[]=' . $id : '';
		return $url;
	}

	/**
	 * Gets the full name of a user from their ID #
	 *
	 * @return     string
	 */
	public function authorTask()
	{
		$u = Request::getInt('u', 0);

		// Get the member's info
		$profile = \Hubzero\User\Profile::getInstance($u);

		if (!$profile->get('name'))
		{
			$name  = $profile->get('givenName') . ' ';
			$name .= ($profile->get('middleName')) ? $profile->get('middleName') . ' ' : '';
			$name .= $profile->get('surname');
		}
		else
		{
			$name  = $profile->get('name');
		}

		echo $name . ' (' . $profile->get('uidNumber') . ')';
	}
}
