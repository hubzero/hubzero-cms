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

/**
 * Manage publications
 */
class PublicationsControllerItems extends \Hubzero\Component\AdminController
{
	/**
	 * Executes a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->_task = strtolower(JRequest::getVar('task', '','request'));
		parent::execute();
	}

	/**
	 * Lists publications
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
			. 'assets' . DS . 'css' . DS . 'publications.css');

		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.publications.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.publications.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']   = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.publications.search',
			'search',
			''
		)));
		$this->view->filters['sortby']     = trim($app->getUserStateFromRequest(
			$this->_option . '.publications.sortby',
			'filter_order',
			'created'
			));
		$this->view->filters['sortdir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.publications.sortdir',
			'filter_order_Dir',
			'DESC'
		));
		$this->view->filters['status']   = trim($app->getUserStateFromRequest(
			$this->_option . '.publications.status',
			'status',
			'all'
		));
		$this->view->filters['dev'] = 1;
		$this->view->filters['category']  = trim($app->getUserStateFromRequest(
			$this->_option . '.publications.category',
			'category',
			''
		));

		$model = new Publication($this->database);

		// Get record count
		$this->view->total = $model->getCount($this->view->filters, NULL, true);

		// Get publications
		$this->view->rows = $model->getRecords($this->view->filters, NULL, true);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			count($this->view->total),
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Get component config
		$pconfig = JComponentHelper::getParams( $this->_option );
		$this->view->config = $pconfig;

		// Get <select> of types
		// Get types
		$rt = new PublicationCategory( $this->database );
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
		$this->view->useBlocks  = $this->view->config->get('curation', 0);

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
			. 'assets' . DS . 'css' . DS . 'publications.css');

		// Incoming publication ID
		$id = JRequest::getVar('id', array(0));
		if (is_array($id))
		{
			$id = $id[0];
		}

		// Is this a new publication? TBD
		if (!$id)
		{
			$this->view->isnew = 1;
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_CREATE_FRONT_END'),
				'notice'
			);
			return;
		}

		// Incoming version
		$version 	= JRequest::getVar( 'version', '' );

		// Grab some filters for returning to place after editing
		$this->view->return = array();
		$this->view->return['category']   	= JRequest::getVar('category', '');
		$this->view->return['sortby']   	= JRequest::getVar('sortby', '');
		$this->view->return['status'] 		= JRequest::getVar('status', '');

		// Instantiate publication object
		$objP = new Publication( $this->database );

		// Instantiate Version
		$this->view->row = new PublicationVersion($this->database);

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
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
			return;
		}

		// Load publication project
		$this->view->pub->_project = new Project($this->database);
		$this->view->pub->_project->load($this->view->pub->project_id);

		// Load version
		$vid = $this->view->pub->version_id;
		$this->view->row->load($vid);

		// Fail if checked out not by 'me'
		if ($this->view->objP->checked_out
		 && $this->view->objP->checked_out <> $this->juser->get('id'))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_CHECKED_OUT'),
				'notice'
			);
			return;
		}

		// Editing existing
		$this->view->objP->checkout($this->juser->get('id'));

		if (trim($this->view->row->published_down) == '0000-00-00 00:00:00')
		{
			$this->view->row->published_down = JText::_('COM_PUBLICATIONS_NEVER');
		}

		// Get name of resource creator
		$creator = JUser::getInstance($this->view->row->created_by);

		$this->view->row->created_by_name = $creator->get('name');
		$this->view->row->created_by_name = ($this->view->row->created_by_name)
			? $this->view->row->created_by_name : JText::_('COM_PUBLICATIONS_UNKNOWN');

		// Get name of last person to modify resource
		if ($this->view->row->modified_by)
		{
			$modifier = JUser::getInstance($this->view->row->modified_by);

			$this->view->row->modified_by_name = $modifier->get('name');
			$this->view->row->modified_by_name = ($this->view->row->modified_by_name)
				? $this->view->row->modified_by_name : JText::_('COM_PUBLICATIONS_UNKNOWN');
		}
		else
		{
			$this->view->row->modified_by_name = '';
		}

		// Get publications helper
		$helper = new PublicationHelper($this->database, $this->view->row->id, $id);

		// Build publication path
		$base_path = $this->view->config->get('webpath');
		$path = $helper->buildPath($id, $this->view->row->id, $base_path);

		// Archival package?
		$this->view->archPath = JPATH_ROOT . $path . DS
			. JText::_('Publication').'_'.$id.'.zip';

		// Get params definitions
		$this->view->params  = new JParameter(
			$this->view->row->params,
			JPATH_COMPONENT . DS . 'publications.xml'
		);

		// Get category
		$this->view->pub->_category = new PublicationCategory( $this->database );
		$this->view->pub->_category->load($this->view->pub->category);
		$this->view->pub->_category->_params = new JParameter( $this->view->pub->_category->params );

		$this->view->lists['category'] = PublicationsAdminHtml::selectCategory(
			$this->view->pub->_category->getContribCategories(),
			'category',
			$this->view->pub->category,
			'',
			'',
			'',
			''
		);

		// Get master type info
		$mt = new PublicationMasterType( $this->database );
		$this->view->pub->_type = $mt->getType($this->view->pub->base);
		$this->view->typeParams = new JParameter( $this->view->pub->_type->params );

		// Get attachments
		$pContent = new PublicationAttachment( $this->database );
		$this->view->pub->_attachments = $pContent->sortAttachments ( $this->view->pub->version_id );

		// Curation
		if ($this->view->useBlocks)
		{
			// Get manifest from either version record (published) or master type
			$manifest   = $this->view->pub->curation
						? $this->view->pub->curation
						: $this->view->pub->_type->curation;

			// Get curation model
			$this->view->pub->_curationModel = new PublicationsCuration($this->database, $manifest);

			// Set pub assoc and load curation
			$this->view->pub->_curationModel->setPubAssoc($this->view->pub);
		}

		// Draw content
		$this->view->lists['content'] = PublicationsAdminHtml::selectContent(
			$this->view->pub,
			$this->_option,
			$this->view->useBlocks,
			$this->database
		);

		// Get pub authors
		$pAuthors 			= new PublicationAuthor( $this->database );
		$this->view->pub->_authors 		= $pAuthors->getAuthors($this->view->pub->version_id);
		$this->view->pub->_submitter 	= $pAuthors->getSubmitter($this->view->pub->version_id, $this->view->pub->created_by);

		// Draw publication authors
		$this->view->lists['authors'] = PublicationsAdminHtml::selectAuthors($this->view->pub->_authors, $this->_option);

		// Get tags on this item
		$tagsHelper = new PublicationTags( $this->database );
		$tags_men = $tagsHelper->get_tags_on_object($this->view->pub->id, 0, 0, 0, 0);

		$mytagarray = array();
		foreach ($tags_men as $tag_men)
		{
			$mytagarray[] = $tag_men['raw_tag'];
		}
		$this->view->tags = implode(', ', $mytagarray);

		// Get selected license
		$objL = new PublicationLicense( $this->database );
		$this->view->license = $objL->getPubLicense( $this->view->pub->version_id );
		$this->view->lists['licenses'] = PublicationsAdminHtml::selectLicense(
			$objL->getLicenses(),
			$this->view->license
		);

		// Get access
		$this->view->lists['access'] = PublicationsAdminHtml::selectAccess('Public,Registered,Private', $this->view->pub->access);

		// Get groups
		$filters = array(
			'authorized' => 'admin',
			'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
			'type'       => array(1, 3),
			'sortby'     => 'description'
		);
		$groups = \Hubzero\User\Group::find($filters);

		// Build <select> of groups
		$this->view->lists['groups'] = PublicationsAdminHtml::selectGroup($groups, $this->view->pub->group_owner, $this->view->pub->_project->owned_by_group);

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
		$id 	= JRequest::getInt( 'id', 0 );
		$el 	= JRequest::getInt( 'el', 0 );
		$v 		= JRequest::getInt( 'v', 0 );

		$objP = new Publication( $this->database );

		// Get publication information
		$this->view->pub = $objP->getPublication($id, $v);

		// If publication not found, raise error
		if (!$this->view->pub)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
			return;
		}

		// Get the publications component config
		$this->view->config = $this->config;

		// Use new curation flow?
		$this->view->useBlocks  = $this->view->config->get('curation', 0);

		if (!$this->view->useBlocks)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_ERROR_CURATION_NEEDED'));
		}
		else
		{
			// Load publication project
			$this->view->pub->_project = new Project($this->database);
			$this->view->pub->_project->load($this->view->pub->project_id);

			// Get master type info
			$mt = new PublicationMasterType( $this->database );
			$this->view->pub->_type = $mt->getType($this->view->pub->base);
			$this->view->typeParams = new JParameter( $this->view->pub->_type->params );

			// Get attachments
			$pContent = new PublicationAttachment( $this->database );
			$this->view->pub->_attachments = $pContent->sortAttachments ( $this->view->pub->version_id );

			// Get manifest from either version record (published) or master type
			$manifest   = $this->view->pub->curation
						? $this->view->pub->curation
						: $this->view->pub->_type->curation;

			// Get curation model
			$this->view->pub->_curationModel = new PublicationsCuration($this->database, $manifest);

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

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
			. 'assets' . DS . 'css' . DS . 'publications.css');

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
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$el 	 = JRequest::getInt( 'el', 0 );
		$id 	 = JRequest::getInt( 'id', 0 );
		$version = JRequest::getVar( 'version', '' );
		$params  = JRequest::getVar( 'params', array(), 'request', 'array' );
		$attachments = JRequest::getVar( 'attachments', array(), 'request', 'array' );

		$row = new PublicationVersion($this->database);

		$objP = new Publication( $this->database );

		// Get publication information
		$pub = $objP->getPublication($id, $version);

		// If publication not found, raise error
		if (!$pub)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
			return;
		}

		// Use new curation flow?
		$useBlocks  = $this->config->get('curation', 0);

		if (!$useBlocks)
		{
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_ERROR_CURATION_NEEDED'),
				'error'
			);
			return;
		}
		else
		{
			// Load publication project
			$pub->_project = new Project($this->database);
			$pub->_project->load($pub->project_id);

			// Get master type info
			$mt = new PublicationMasterType( $this->database );
			$pub->_type = $mt->getType($pub->base);
			$typeParams = new JParameter( $pub->_type->params );

			// Get attachments
			$pContent = new PublicationAttachment( $this->database );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );

			// Get manifest from either version record (published) or master type
			$manifest   = $pub->curation
						? $pub->curation
						: $pub->_type->curation;

			// Get curation model
			$pub->_curationModel = new PublicationsCuration($this->database, $manifest);

			// Set pub assoc and load curation
			$pub->_curationModel->setPubAssoc($pub);

			if (!empty($params))
			{
				foreach ($params as $param => $value )
				{
					$row->saveParam($pub->version_id, $param, $value);
				}
			}
			if (!empty($attachments))
			{
				foreach ($attachments as $attachId => $attach )
				{
					$pContent = new PublicationAttachment( $this->database );
					if ($pContent->load($attachId))
					{
						$pContent->title = $attach['title'];
						$pContent->store();
					}
				}
			}
		}

		// Set redirect URL
		$url = 'index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version;

		// Redirect back to publication
		$this->setRedirect(
			$url,
			JText::_('COM_PUBLICATIONS_SUCCESS_SAVED_CONTENT')
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
		$author = JRequest::getInt( 'author', 0 );

		$this->view->setLayout('editauthor');

		$this->view->author = new PublicationAuthor( $this->database );
		if ($this->_task == 'editauthor' && !$this->view->author->load($author))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_NO_AUTHOR_RECORD') );
			return;
		}

		// Version ID
		$vid = JRequest::getInt( 'vid', $this->view->author->publication_version_id );

		$this->view->row = new PublicationVersion( $this->database );
		$this->view->pub = new Publication( $this->database );

		// Load version
		if (!$this->view->row->load($vid))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
			return;
		}

		// Load publication
		$pid = JRequest::getInt( 'pid', $this->view->row->publication_id );
		if (!$this->view->pub->load($pid))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
			return;
		}

		// Instantiate project owner
		$objO = new ProjectOwner($this->database);
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

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
			. 'assets' . DS . 'css' . DS . 'publications.css');

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
		$aid = JRequest::getInt( 'aid', 0 );

		$pAuthor = new PublicationAuthor( $this->database );
		if (!$pAuthor->load($aid))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_AUTHOR'),
				'error'
			);
			return;
		}

		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;

		// Instantiate Version
		$row = new PublicationVersion($this->database);
		if ($row->load($pAuthor->publication_version_id))
		{
			$url .= '&task=edit' . '&id[]=' . $row->publication_id
				. '&version=' . $row->version_number;
		}

		if (!$pAuthor->delete())
		{
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_ERROR_FAILED_TO_DELETE_AUTHOR'),
				'error'
			);
			return;
		}

		// Redirect back to publication
		$this->setRedirect(
			$url,
			JText::_('COM_PUBLICATIONS_SUCCESS_DELETE_AUTHOR')
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
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id       = JRequest::getInt('id', 0);
		$version  = JRequest::getVar( 'version', '' );
		$neworder = JRequest::getVar('list', '');

		// Set redirect URL
		$url = 'index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version;

		if (!$neworder)
		{
			// Nothing to save
			$this->setRedirect(
				$url
			);
			return;
		}

		// Instantiate publication object
		$objP = new Publication( $this->database );
		if (!$objP->load($id))
		{
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_NOT_FOUND'),
				'error'
			);
			return;
		}

		$pub = $objP->getPublication($id, $version);

		// Load publication project
		$pub->_project = new Project($this->database);
		$pub->_project->load($pub->project_id);

		// Get language file
		$lang = JFactory::getLanguage();
		$lang->load('plg_projects_publications');

		// Save via block
		$blocksModel = new PublicationsModelBlocks($this->database);
		$block = $blocksModel->loadBlock('authors');

		$block->reorder(NULL, 0, $pub, $this->juser->get('id'));
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
			// Instantiate Version
			$row = new PublicationVersion($this->database);
			$row->load($pub->version_id);

			// Update DOI in case of name change
			if ($row && $row->doi)
			{
				// Get updated authors
				$pAuthor = new PublicationAuthor( $this->database );
				$authors = $pAuthor->getAuthors($row->id);

				// Collect DOI metadata
				$metadata = $this->_collectMetadata($row, $pub, $authors);

				if (!PublicationUtilities::updateDoi($row->doi, $row, $authors, $this->config, $metadata, $doierr))
				{
					$this->setError(JText::_('COM_PUBLICATIONS_ERROR_DOI') . ' ' . $doierr);
				}
			}

			// Redirect back to publication
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_SUCCESS_SAVED_AUTHOR')
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
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$author  = JRequest::getInt( 'author', 0 );
		$id 	 = JRequest::getInt( 'id', 0 );
		$version = JRequest::getVar( 'version', '' );

		$firstName 	= JRequest::getVar( 'firstName', '', 'post' );
		$lastName 	= JRequest::getVar( 'lastName', '', 'post' );
		$org 		= JRequest::getVar( 'organization', '', 'post' );
		$uid 		= JRequest::getInt( 'user_id', 0, 'post' );

		// Set redirect URL
		$url = 'index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version;

		// Instantiate publication object
		$objP = new Publication( $this->database );
		if (!$objP->load($id))
		{
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_NOT_FOUND'),
				'error'
			);
			return;
		}

		$pub = $objP->getPublication($id, $version);

		// Load publication project
		$pub->_project = new Project($this->database);
		$pub->_project->load($pub->project_id);

		// Get language file
		$lang = JFactory::getLanguage();
		$lang->load('plg_projects_publications');

		// Save via block
		$blocksModel = new PublicationsModelBlocks($this->database);
		$block = $blocksModel->loadBlock('authors');

		if ($author)
		{
			$block->saveItem(NULL, 0, $pub, $this->juser->get('id'), 0 , $author);
		}
		else
		{
			$block->addItem(NULL, 0, $pub, $this->juser->get('id'));
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
			// Instantiate Version
			$row = new PublicationVersion($this->database);
			$row->load($pub->version_id);

			// Update DOI in case of name change
			if ($row && $row->doi)
			{
				// Get updated authors
				$pAuthor = new PublicationAuthor( $this->database );
				$authors = $pAuthor->getAuthors($row->id);

				// Collect DOI metadata
				$metadata = $this->_collectMetadata($row, $pub, $authors);

				if (!PublicationUtilities::updateDoi($row->doi, $row, $authors, $this->config, $metadata, $doierr))
				{
					$this->setError(JText::_('COM_PUBLICATIONS_ERROR_DOI') . ' ' . $doierr);
				}
			}

			// Redirect back to publication
			$this->setRedirect(
				$url,
				JText::_('COM_PUBLICATIONS_SUCCESS_SAVED_AUTHOR')
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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id 			= JRequest::getInt( 'id', 0 );
		$action 		= JRequest::getVar( 'admin_action', '' );
		$published_up 	= JRequest::getVar( 'published_up', '' );

		// Is this a new publication? TBD
		$isnew = $id ? 0 : 1;
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION'),
				'error'
			);
			return;
		}

		// Use new curation flow?
		$useBlocks  = $this->config->get('curation', 0);

		// Incoming version
		$version 	= JRequest::getVar( 'version', '' );

		// Instantiate publication object
		$objP = new Publication( $this->database );

		// Instantiate Version
		$row = new PublicationVersion($this->database);

		if (!$row->bind($_POST))
		{
			echo PublicationsAdminHtml::alert($row->getError());
			exit();
		}

		// Check that version exists
		$version = $row->checkVersion($id, $version) ? $version : 'default';

		// Get publication information
		$pub = $objP->getPublication($id, $version);
		$objP->load($id);

		// If publication not found, raise error
		if (!$pub)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
			return;
		}

		// Set redirect URL
		$url = 'index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version;

		// Load version	for editing
		$vid = $pub->version_id;
		$row->load($vid);

		// Load version before changes
		$old = new PublicationVersion($this->database);
		$old->load($vid);

		// Get authors
		$pa = new PublicationAuthor( $this->database );
		$authors = $pa->getAuthors($vid);

		// Checkin resource
		$objP->checkin();

		// Get pub project
		$project = new Project($this->database);
		$project->load($pub->project_id);

		// Incoming updates
		$title 			= trim(JRequest::getVar( 'title', '', 'post' ));
		$title 			= htmlspecialchars($title);
		$abstract 		= trim(JRequest::getVar( 'abstract', '', 'post' ));
		$abstract 		= \Hubzero\Utility\Sanitize::clean(htmlspecialchars($abstract));
		$description 	= trim(JRequest::getVar( 'description', '', 'post', 'none', 2 ));
		$release_notes 	= stripslashes(trim(JRequest::getVar( 'release_notes', '', 'post', 'none', 2 )));
		$group_owner	= JRequest::getInt( 'group_owner', 0, 'post' );
		$metadata 		= '';
		$activity 		= '';

		// Save publication record
		$objP->alias    = trim(JRequest::getVar( 'alias', '', 'post' ));
		$objP->category = trim(JRequest::getInt( 'category', 0, 'post' ));
		if (!$project->owned_by_group)
		{
			$objP->group_owner = $group_owner;
		}
		$objP->store();

		// Get metadata
		if (isset($_POST['nbtag']))
		{
			$type = new PublicationCategory($this->database);
			$type->load($pub->category);

			$fields = array();
			if (trim($type->customFields) != '')
			{
				$fs = explode("\n", trim($type->customFields));
				foreach ($fs as $f)
				{
					$fields[] = explode('=', $f);
				}
			}

			$nbtag = JRequest::getVar( 'nbtag', array(), 'request', 'array' );
			foreach ($nbtag as $tagname => $tagcontent)
			{
				$tagcontent = trim(stripslashes($tagcontent));
				if ($tagcontent != '')
				{
					$metadata .= "\n".'<nb:'.$tagname.'>'.$tagcontent.'</nb:'.$tagname.'>'."\n";
				}
				else
				{
					foreach ($fields as $f)
					{
						if ($f[0] == $tagname && end($f) == 1)
						{
							echo PublicationsAdminHtml::alert(JText::sprintf('COM_PUBLICATIONS_REQUIRED_FIELD_CHECK', $f[1]));
							exit();
						}
					}
				}
			}
		}

		// Save incoming
		$row->title 		= $title ? $title : $row->title;
		$row->abstract 		= $abstract
							? \Hubzero\Utility\String::truncate($abstract, 250)
							: $row->abstract;
		$row->description 	= $description ? $description : $row->description;
		$row->metadata 		= $metadata ? $metadata : $row->metadata;
		$row->published_up 	= $published_up ? $published_up : $row->published_up;
		$row->release_notes	= $release_notes;
		$row->license_text	= trim(JRequest::getVar( 'license_text', '', 'post' ));
		$row->license_type	= JRequest::getInt( 'license_type', 0, 'post' );
		$row->access		= JRequest::getInt( 'access', 0, 'post' );

		// publish up
		$published_up 		= trim(JRequest::getVar( 'published_up', '', 'post' ));
		$published_down 	= trim(JRequest::getVar( 'published_down', '', 'post' ));

		$row->published_up  = $published_up
							? JFactory::getDate($published_up, JFactory::getConfig()->get('offset'))->toSql()
							: '0000-00-00 00:00:00';
		$row->published_down= $published_down && trim($published_down) != 'Never'
							? JFactory::getDate($published_down, JFactory::getConfig()->get('offset'))->toSql()
							: '0000-00-00 00:00:00';
		$row->doi		    = trim(JRequest::getVar( 'doi', '', 'post' ));

		// Determine action (if status is flipped)
		$state = JRequest::getInt( 'state', 0 );
		if ($old->state != $state)
		{
			switch ($state)
			{
				case 1:
					$action = $old->state == 0 ? 'republish' : 'publish';
					break;
				case 0:
					$action = 'unpublish';
					break;
				case 3:
				case 4:
					$action = 'revert';
					break;
			}

			$row->state = $state;
		}

		// Update DOI with latest information
		if ($row->doi && !$action
			&& $row->title != $old->title)
		{
			// Collect DOI metadata
			$metadata = $this->_collectMetadata($row, $objP, $authors);

			if (!PublicationUtilities::updateDoi(
				$row->doi,
				$row,
				$authors,
				$this->config,
				$metadata,
				$doierr
			))
			{
				$this->setError(JText::_('COM_PUBLICATIONS_ERROR_DOI').' '.$doierr);
			}
		}

		// Email config
		$pubtitle 	= \Hubzero\Utility\String::truncate($row->title, 100);
		$subject 	= JText::_('Version') . ' ' . $row->version_label . ' '
					. JText::_('COM_PUBLICATIONS_OF') . ' '
					. strtolower(JText::_('COM_PUBLICATIONS_PUBLICATION'))
					. ' "' . $pubtitle . '" ';
		$sendmail 	= 0;
		$message 	= rtrim(\Hubzero\Utility\Sanitize::clean(JRequest::getVar( 'message', '' )));
		$output 	= JText::_('COM_PUBLICATIONS_SUCCESS_SAVED_ITEM');

		// Admin actions
		if ($action)
		{
			$output = '';
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
				. DS . 'com_projects'. DS . 'tables' . DS . 'project.activity.php');
			$objAA = new ProjectActivity ( $this->database );
			switch ($action)
			{
				case 'publish':
				case 'republish':

					// Need '/..' because we are an administrator component
					$mkaip = JPATH_BASE . '/../cli/mkaip/bin/mkaip';

					if (file_exists($mkaip))
					{
						$row->state = 10;	// preserving (generating AIP)
					}
					else
					{
						$row->state = 1;	// published
					}

					$activity = $action == 'publish'
						? JText::_('COM_PUBLICATIONS_ACTIVITY_ADMIN_PUBLISHED')
						: JText::_('COM_PUBLICATIONS_ACTIVITY_ADMIN_REPUBLISHED');
					$subject .= $action == 'publish'
						? JText::_('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
						: JText::_('COM_PUBLICATIONS_MSG_ADMIN_REPUBLISHED');

					$row->published_down = '0000-00-00 00:00:00';
					if ( $action == 'publish')
					{
						$row->published_up 	 = $published_up
							? $published_up : JFactory::getDate()->toSql();
					}

					// Collect DOI metadata
					$metadata = $this->_collectMetadata($row, $objP, $authors);

					// Issue a DOI
					if ($this->config->get('doi_service')
						&& $this->config->get('doi_shoulder'))
					{
						if (!$row->doi)
						{
							$doi = PublicationUtilities::registerDoi(
								$row,
								$authors,
								$this->config,
								$metadata,
								$doierr
							);

							if ($doi)
							{
								$row->doi = $doi;
								if ($doierr)
								{
									$this->setError(JText::_('COM_PUBLICATIONS_ERROR_DOI').' '.$doierr);
								}
							}
							else
							{
								$this->setRedirect(
									$url, JText::_('COM_PUBLICATIONS_ERROR_DOI').' '.$doierr, 'error'
								);
								return;
							}
						}
						else
						{
							// Update DOI with latest information
							if (!PublicationUtilities::updateDoi($row->doi, $row,
								$authors, $this->config, $metadata, $doierr))
							{
								$this->setError(JText::_('COM_PUBLICATIONS_ERROR_DOI').' '.$doierr);
							}
						}
					}

					// Save date accepted
					if ($action == 'publish')
					{
						$row->accepted = JFactory::getDate()->toSql();

						if ($useBlocks)
						{
							// Get master type info
							$mt = new PublicationMasterType( $this->database );
							$pub->_type = $mt->getType($pub->base);

							// Get curation model
							$pub->_curationModel = new PublicationsCuration(
								$this->database,
								$pub->_type->curation
							);

							// Store curation manifest
							$row->curation = json_encode($pub->_curationModel->_manifest);
						}
					}
					$row->modified = JFactory::getDate()->toSql();
					$row->modified_by = $this->juser->get('id');

					// Create OAIS Archival Information Package
					if (!$this->getError() && file_exists($mkaip))
					{
						$mkaipOutput =
							'mkaip-'
							. str_replace(
								'/',
								'__',
								$row->doi
							)
							. '.out';

						// "fire and forget" mkaip --
						// must use proc_open / proc_close()
						// or we cannot run mkaip in the
						// background on:
						//     Debian GNU/Linux 6.0.7 (squeeze)
						// [ Mark Leighton Fisher, 2014-04-28 ]
						$handles = array();
						$pipes	 = array();
						proc_close(
							proc_open(
								'( /usr/bin/nohup '
								. '/usr/bin/php -q '
								. $mkaip . ' ' . $row->doi . ' '
								. '2>&1 > '
								. "/www/tmp/$mkaipOutput & ) &",
								$handles,
								$pipes
							)
						);
					}

					if (!$this->getError())
					{
						$output .= ' '.JText::_('COM_PUBLICATIONS_ITEM').' ';
						$output .= $action == 'publish'
							? JText::_('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
							: JText::_('COM_PUBLICATIONS_MSG_ADMIN_REPUBLISHED');
					}
					break;

				case 'revert':
					$row->state 		 	= $state ? $state : 4;
					$activity = JText::_('COM_PUBLICATIONS_ACTIVITY_ADMIN_REVERTED');
					$subject .= JText::_('COM_PUBLICATIONS_MSG_ADMIN_REVERTED');
					$output .= ' '.JText::_('COM_PUBLICATIONS_ITEM').' ';
					$output .= JText::_('COM_PUBLICATIONS_MSG_ADMIN_REVERTED');
					break;

				case 'unpublish':
					$row->state 		 	= 0;
					$row->published_down    = JFactory::getDate()->toSql();
					$activity = JText::_('COM_PUBLICATIONS_ACTIVITY_ADMIN_UNPUBLISHED');
					$subject .= JText::_('COM_PUBLICATIONS_MSG_ADMIN_UNPUBLISHED');

					$output .= ' '.JText::_('COM_PUBLICATIONS_ITEM').' ';
					$output .= JText::_('COM_PUBLICATIONS_MSG_ADMIN_UNPUBLISHED');
					break;
			}

			// Add activity
			$activity .= ' '.strtolower(JText::_('version')).' '.$row->version_label.' '
			.JText::_('COM_PUBLICATIONS_OF').' '.strtolower(JText::_('publication')).' "'
			.$pubtitle.'" ';

			// Build return url
			$link 	= '/projects/' . $project->alias . '/publications/'
					. $id . '/?version=' . $row->version_number;

			if ($action != 'message' && !$this->getError())
			{
				$aid = $objAA->recordActivity( $pub->project_id, $this->juser->get('id'),
					$activity, $id, $pubtitle, $link, 'publication', 0, $admin = 1 );
				$sendmail = $this->config->get('email') ? 1 : 0;

				// Append comment to activity
				if ($message && $aid)
				{
					require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
						. DS . 'com_projects' . DS . 'tables' . DS . 'project.comment.php');
					$objC = new ProjectComment( $this->database );

					$comment = \Hubzero\Utility\String::truncate($message, 250);
					$comment = \Hubzero\Utility\Sanitize::stripAll($comment);

					$objC->itemid = $aid;
					$objC->tbl = 'activity';
					$objC->parent_activity = $aid;
					$objC->comment = $comment;
					$objC->admin = 1;
					$objC->created = JFactory::getDate()->toSql();
					$objC->created_by = $this->juser->get('id');
					$objC->store();

					// Get new entry ID
					if (!$objC->id)
					{
						$objC->checkin();
					}

					$objAA = new ProjectActivity ( $this->database );

					if ( $objC->id )
					{
						$what = JText::_('COM_PROJECTS_AN_ACTIVITY');
						$curl = '#tr_'.$aid; // same-page link
						$caid = $objAA->recordActivity( $pub->project_id, $this->juser->get('id'),
						JText::_('COM_PROJECTS_COMMENTED') . ' ' . JText::_('COM_PROJECTS_ON')
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

		// Do we have a message to send?
		if ($message)
		{
			$subject .= ' - '.JText::_('COM_PUBLICATIONS_MSG_ADMIN_NEW_MESSAGE');
			$sendmail = 1;
			$output .= ' '.JText::_('COM_PUBLICATIONS_MESSAGE_SENT');
		}

		// Updating entry if anything changed
		if ($row != $old && !$this->getError())
		{
			$row->modified    = JFactory::getDate()->toSql();
			$row->modified_by = $this->juser->get('id');

			// Store content
			if (!$row->store())
			{
				$this->setRedirect(
					$url,  $row->getError(), 'error'
				);
				return;
			}

			// Mark as curated/non-curated
			if ($action == 'publish')
			{
				$curated = $useBlocks ? 1 : 2;
				$row->saveParam($row->id, 'curated', $curated);
			}
		}

		// Save parameters
		$params = JRequest::getVar('params', '', 'post');
		if (is_array($params))
		{
			foreach ($params as $k => $v)
			{
				$row->saveParam($row->id, $k, $v);
			}
		}

		// Incoming tags
		$tags = JRequest::getVar('tags', '', 'post');

		// Save the tags
		$rt = new PublicationTags($this->database);
		$rt->tag_object($this->juser->get('id'), $id, $tags, 1, 1);

		// Get ids of publication authors with accounts
		$notify = $pa->getAuthors($row->id, 1, 1, 1, true);
		$notify[] = $row->created_by;
		$notify = array_unique($notify);

		// Send email
		if ($sendmail && !$this->getError())
		{
			$this->_emailContributors($row, $project, $subject, $message, $notify, $action);
		}

		// Append any errors
		if ($this->getError())
		{
			$output .= ' '.$this->getError();
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
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$output
			);
		}
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
		$objT = new PublicationCategory($this->database);
		$objT->load($objP->category);
		$typetitle = ucfirst($objT->alias);

		// Collect metadata
		$metadata = array();
		$metadata['typetitle'] 		= $typetitle ? $typetitle : 'Dataset';
		$metadata['resourceType'] 	= isset($objT->dc_type) && $objT->dc_type ? $objT->dc_type : 'Dataset';
		$metadata['language'] 		= 'en';

		// Get dc:contibutor
		$project = new Project($this->database);
		$project->load($objP->project_id);
		$profile = \Hubzero\User\Profile::getInstance(JFactory::getUser()->get('id'));
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
		$objL = new PublicationLicense( $this->database);
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
			$pa = new PublicationAuthor( $this->database );
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
			$jconfig = JFactory::getConfig();

			// E-mail "from" info
			$from = array();
			$from['email'] = $jconfig->getValue('config.mailfrom');
			$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_('PUBLICATIONS');

			$subject = $subject
				? $subject : JText::_('COM_PUBLICATIONS_STATUS_UPDATE');

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
			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array(
				'publication_status_changed',
				$subject,
				$body,
				$from,
				$authors,
				$this->_option)
			))
			{
				$this->setError(JText::_('COM_PUBLICATIONS_ERROR_FAILED_MESSAGE_AUTHORS'));
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

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
			. 'assets' . DS . 'css' . DS . 'publications.css');

		// Incoming publication ID
		$id = JRequest::getInt('id', 0);

		// Need ID
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION'),
				'notice'
			);
			return;
		}

		// Grab some filters for returning to place after editing
		$this->view->return = array();
		$this->view->return['cat']   = JRequest::getVar('cat', '');
		$this->view->return['sortby']   = JRequest::getVar('sortby', '');
		$this->view->return['status'] = JRequest::getVar('status', '');

		// Instantiate project publication
		$objP = new Publication( $this->database );
		$objV = new PublicationVersion( $this->database );

		$this->view->pub = $objP->getPublication($id);
		if (!$this->view->pub)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION') );
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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		$erase = JRequest::getInt('erase', 1);

		// Ensure we have some IDs to work with
		if (count($ids) < 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION'),
				'notice'
			);
			return;
		}

		$version = count($ids) == 1 ? JRequest::getVar( 'version', 'all' ) : 'all';

		jimport('joomla.filesystem.folder');

		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
			. DS . 'com_projects' . DS . 'tables' . DS . 'project.activity.php');

		foreach ($ids as $id)
		{
			// Load publication
			$objP = new Publication( $this->database );
			if (!$objP->load($id))
			{
				JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
				return;
			}

			$projectId = $objP->project_id;

			$row = new PublicationVersion( $this->database );

			// Get versions
			$versions = $row->getVersions( $id, $filters = array('withdev' => 1));

			if ($version != 'all' && count($versions) > 1)
			{
				// Check that version exists
				$version = $row->checkVersion($id, $version) ? $version : 'dev';

				// Load version
				if (!$row->loadVersion($id, $version))
				{
					JError::raiseError( 404, JText::_('COM_PUBLICATIONS_VERSION_NOT_FOUND') );
					return;
				}

				// Cannot delete main version if other versions exist
				if ($row->main)
				{
					JError::raiseError( 404, JText::_('COM_PUBLICATIONS_VERSION_MAIN_ERROR_DELETE') );
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
					$objV = new PublicationVersion( $this->database );
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
					$objAA = new ProjectActivity( $this->database );
					$objAA->deleteActivityByReference($projectId, $id, 'publication');

					// Build publication path
					$path    =  JPATH_ROOT . DS . trim($this->config->get('webpath'), DS)
							. DS .  \Hubzero\Utility\String::pad( $id );

					// Delete all files
					if (is_dir($path))
					{
						JFolder::delete($path);
					}
				}
			}
		}

		// Redirect
		$output = ($version != 'all')
			? JText::_('COM_PUBLICATIONS_SUCCESS_VERSION_DELETED')
			: JText::_('COM_PUBLICATIONS_SUCCESS_RECORDS_DELETED') . ' (' . count($ids) . ')';
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
		$pa = new PublicationAuthor( $this->database );
		$authors = $pa->deleteAssociations($vid);

		// Delete attachments
		$pContent = new PublicationAttachment( $this->database );
		$pContent->deleteAttachments($vid);

		// Delete screenshots
		$pScreenshot = new PublicationScreenshot( $this->database );
		$pScreenshot->deleteScreenshots($vid);

		// Delete access accosiations
		$pAccess = new PublicationAccess( $this->database );
		$pAccess->deleteGroups($vid);

		// Delete audience
		$pAudience = new PublicationAudience( $this->database );
		$pAudience->deleteAudience($vid);

		// Get publications helper
		$helper = new PublicationHelper($this->database);

		// Build publication path
		$path = $helper->buildPath($pid, $vid, $this->config->get('webpath'), '', 1);

		// Delete all files
		if (is_dir($path))
		{
			JFolder::delete($path);
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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id  = JRequest::getInt('id', 0);

		// Checkin the resource
		$row = new Publication($this->database);
		$row->load($id);
		$row->checkin();

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option
			. '&controller=' . $this->_controller
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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = new Publication($this->database);
			$row->load($id);
			$row->rating = '0.0';
			$row->times_rated = '0';
			$row->store();
			$row->checkin();

			$this->_message = JText::_('COM_PUBLICATIONS_SUCCESS_RATING_RESET');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option
			. '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $id,
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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id  = JRequest::getInt('id', 0);

		if ($id)
		{
			// Load the object, reset the ratings, save, checkin
			$row = new Publication($this->database);
			$row->load($id);
			$row->ranking = '0';
			$row->store();
			$row->checkin();

			$this->_message = JText::_('COM_PUBLICATIONS_SUCCESS_RANKING_RESET');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option
			. '&controller=' . $this->_controller
			. '&task=edit&id[]=' . $id,
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
		$pid 		= JRequest::getInt('pid', 0);
		$vid 		= JRequest::getInt('vid', 0);
		$version 	= JRequest::getVar( 'version', '' );

		require_once( JPATH_ROOT . DS . 'components' . DS
			. 'com_projects' . DS . 'helpers' . DS . 'helper.php' );

		// Load publication & version classes
		$objP  = new Publication( $this->database );
		$objV  = new PublicationVersion( $this->database );
		$mt    = new PublicationMasterType( $this->database );

		// Use new curation flow?
		$useBlocks  = $this->config->get('curation', 0);

		if (!$objP->load($pid) || !$objV->load($vid))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
			return;
		}
		$pub = $objP->getPublication($pid, $objV->version_number, $objP->project_id);
		if (!$pub)
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_LOAD_PUBLICATION') );
			return;
		}

		$url = 'index.php?option=' . $this->_option . '&controller='
			. $this->_controller . '&task=edit' . '&id[]=' . $pid . '&version=' . $version;

		if ($useBlocks)
		{
			$pub->version 	= $pub->version_number;

			// Load publication project
			$pub->_project = new Project($this->database);
			$pub->_project->load($pub->project_id);

			// Get master type info
			$mt = new PublicationMasterType( $this->database );
			$pub->_type = $mt->getType($pub->base);
			$typeParams = new JParameter( $pub->_type->params );

			// Get attachments
			$pContent = new PublicationAttachment( $this->database );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );

			// Get authors
			$pAuthors 			= new PublicationAuthor( $this->database );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);

			// Get manifest from either version record (published) or master type
			$manifest   = $pub->curation
						? $pub->curation
						: $pub->_type->curation;

			// Get curation model
			$pub->_curationModel = new PublicationsCuration($this->database, $manifest);

			// Set pub assoc and load curation
			$pub->_curationModel->setPubAssoc($pub);

			// Produce archival package
			if (!$pub->_curationModel->package())
			{
				// Checkin the resource
				$objP->checkin();

				// Redirect
				$this->setRedirect( $url, JText::_('COM_PUBLICATIONS_ERROR_ARCHIVAL'), 'error');
				return;
			}
		}
		else
		{
			// Archival for non-curated publications
			JPluginHelper::importPlugin( 'projects', 'publications' );
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger( 'archivePub', array($pid, $vid) );
		}

		$this->_message = JText::_('COM_PUBLICATIONS_SUCCESS_ARCHIVAL');

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
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id  = JRequest::getInt('id', 0);

		if ($id)
		{
			// Load the object and checkin
			$row = new Publication($this->database);
			$row->load($id);
			$row->checkin();
		}

		// Redirect
		$this->_redirect = 'index.php?option=' . $this->_option
			. '&controller=' . $this->_controller;
	}

	/**
	 * Builds the appropriate URL for redirction
	 *
	 * @return     string
	 */
	private function buildRedirectURL()
	{
		$url  = 'index.php?option=' . $this->_option
			. '&controller=' . $this->_controller;

		// Incoming
		$id  = JRequest::getInt('id', 0);
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
		$u = JRequest::getInt('u', 0);

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
