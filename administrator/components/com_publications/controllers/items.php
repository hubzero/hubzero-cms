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
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'publications.css');

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
		$this->view->total = $model->getCount($this->view->filters);

		// Get publications
		$this->view->rows = $model->getRecords($this->view->filters);

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

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'publications.css');

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
				JText::_('New publications can only be added via projects on the front-end.'),
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
		
		// Load version	
		$vid = $this->view->pub->version_id;						
		$this->view->row->load($vid);
		
		// Check if pub is ready to be released
		$checked = array('content' => 0, 'description' => 0, 'authors' => 0);

		// Fail if checked out not by 'me'
		if ($this->view->objP->checked_out
		 && $this->view->objP->checked_out <> $this->juser->get('id'))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('This resource is currently being edited by another administrator'),
				'notice'
			);
			return;
		}

		// Editing existing
		$this->view->objP->checkout($this->juser->get('id'));

		if (trim($this->view->row->published_down) == '0000-00-00 00:00:00')
		{
			$this->view->row->published_down = JText::_('Never');
		}

		// Get name of resource creator
		$creator = JUser::getInstance($this->view->row->created_by);

		$this->view->row->created_by_name = $creator->get('name');
		$this->view->row->created_by_name = ($this->view->row->created_by_name) ? $this->view->row->created_by_name : JText::_('Unknown');

		// Get name of last person to modify resource
		if ($this->view->row->modified_by)
		{
			$modifier = JUser::getInstance($this->view->row->modified_by);

			$this->view->row->modified_by_name = $modifier->get('name');
			$this->view->row->modified_by_name = ($this->view->row->modified_by_name) ? $this->view->row->modified_by_name : JText::_('Unknown');
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
		$this->view->archPath = JPATH_ROOT . $path . DS . JText::_('Publication').'_'.$id.'.zip';

		// Get params definitions
		$this->view->params  = new JParameter($this->view->row->params, JPATH_COMPONENT . DS . 'publications.xml');

		// Build selects of various categories
		$rt = new PublicationCategory($this->database);
		$this->view->lists['category'] = PublicationsHtml::selectCategory(
			$rt->getContribCategories(), 'category', $this->view->pub->category, '', '', '', ''
		);
	
		// Get master type info
		$mtObj = new PublicationMasterType( $this->database );
		$mtObj->load($this->view->pub->master_type);
		$this->view->typeParams = new JParameter( $mtObj->params );
		
		// Get attachments
		$pContent = new PublicationAttachment( $this->database );
		$primary = $pContent->getAttachments( $this->view->row->id, $filters = array('role' => '1') );
		$secondary = $pContent->getAttachments( $this->view->row->id, $filters = array('role' => '0') );
		if (count($primary) > 0) 
		{
			$checked['content'] = 1;
		}
		$this->view->lists['content'] = PublicationsHtml::selectContent($primary, $secondary, $this->_option);	
		
		// Get pub authors
		$pa = new PublicationAuthor( $this->database );
		$authors = $pa->getAuthors($vid);
		if (count($authors) > 0) 
		{
			$checked['authors'] = 1;
		}
		
		// Get submitter
		$this->view->submitter = $pa->getSubmitter($this->view->row->id, $this->view->row->created_by);
		
		// Build <select> of project owners		
		$this->view->lists['authors'] = PublicationsHtml::selectAuthorsNoEdit($authors, $this->_option);
		
		// Description is there?
		$checked['description'] = $this->view->row->title && $this->view->row->abstract && $this->view->row->description ? 1 : 0;
		$this->view->checked = $checked;
		
		// Is publishing allowed?
		if ($checked['content'] == 1 && $checked['authors'] == 1 && $checked['description'] == 1) 
		{
			$this->view->pub_allowed = 1;
		}
		else 
		{
			$this->view->pub_allowed = 0;
		}
		
		// Get tags on this item
		$tagsHelper = new PublicationTags( $this->database);
		$this->view->lists['tags'] = $tagsHelper->get_tag_string($id, 0, 0, NULL, 0, 1);
				
		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
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
		
		$this->view->author = new PublicationAuthor( $this->database );
		if (!$this->view->author->load($author))
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_ERROR_NO_AUTHOR_RECORD') );
			return;
		}
		
		$this->view->row  = new PublicationVersion( $this->database );
		$this->view->pub  = new Publication( $this->database );
		
		// Load version						
		$this->view->row->load($this->view->author->publication_version_id);
		
		// Load publication
		$this->view->pub->load($this->view->row->publication_id);
		
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
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'publications.css');

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
				JText::_('Cannot load publication author to delete.'),
				'error'
			);
			return;
		}
		
		$url = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		
		// Instantiate Version
		$row = new PublicationVersion($this->database);
		if ($row->load($pAuthor->publication_version_id))
		{
			$url .= '&task=edit' . '&id[]=' . $row->publication_id . '&version=' . $row->version_number;
		}
		
		if (!$pAuthor->delete())
		{
			$this->setRedirect(
				$url,
				JText::_('Failed to delete author information'),
				'error'
			);
			return;
		}
		
		// Redirect back to publication
		$this->setRedirect(
			$url,
			JText::_('Author deleted')
		);
		return;
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
		
		$pAuthor = new PublicationAuthor( $this->database );
		if (!$author || !$pAuthor->load($author))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Cannot load publication author to save.'),
				'error'
			);
			return;
		}
		
		// Save name before changes
		$oldName = $pAuthor->name;
		
		// Set redirect URL
		$url = 'index.php?option=' . $this->_option . '&controller=' 
			. $this->_controller . '&task=edit' . '&id[]=' . $id . '&version=' . $version;
		
		$pAuthor->organization = $org;
		$pAuthor->firstName    = $firstName ? $firstName : $pAuthor->firstName;
		$pAuthor->lastName     = $lastName ? $lastName : $pAuthor->lastName;
		$name 				   = $pAuthor->firstName . ' ' . $pAuthor->lastName;		
		$pAuthor->name    	   = $name ? $name : $pAuthor->name;
		if (!$pAuthor->store())
		{
			$this->setRedirect(
				$url,
				JText::_('Failed to save author information'),
				'error'
			);
			return;
		}
		
		// Instantiate Version
		$row = new PublicationVersion($this->database);
		$row->load($pAuthor->publication_version_id);
		
		// Instantiate publication object
		$objP = new Publication( $this->database );
		$objP->load($row->publication_id);
				
		// Update DOI in case of name change
		if ($row->doi && $oldName != $pAuthor->name)
		{
			// Get updated authors
			$authors = $pAuthor->getAuthors($pAuthor->publication_version_id);
			
			// Collect DOI metadata
			$metadata = $this->_collectMetadata($row, $objP, $authors);
			
			if (!PublicationUtilities::updateDoi($row->doi, $row, $authors, $this->config, $metadata, $doierr))
			{
				$this->setError(JText::_('COM_PUBLICATIONS_ERROR_DOI').' '.$doierr);
			}
		}
		
		// Redirect back to publication
		$this->setRedirect(
			$url,
			JText::_('Author information updated')
		);		
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
				JText::_('Cannot load the publication to save.'),
				'error'
			);
			return;			
		}
					
		// Incoming version
		$version 	= JRequest::getVar( 'version', '' );

		// Instantiate publication object
		$objP = new Publication( $this->database );
		
		// Instantiate Version
		$row = new PublicationVersion($this->database);
						
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
		$description 	= trim(JRequest::getVar( 'description', '', 'post' ));	
		$description 	= stripslashes($description);
		$release_notes 	= stripslashes(trim(JRequest::getVar( 'release_notes', '', 'post' )));
		$metadata 		= '';
		$activity 		= '';
					
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
							echo PublicationsHtml::alert(JText::sprintf('COM_PUBLICATIONS_REQUIRED_FIELD_CHECK', $f[1]));
							exit();
						}
					}
				}
			}
		}
				
		// Save title, abstract and description
		$row->title 		= $title ? $title : $row->title;
		$row->abstract 		= $abstract ? \Hubzero\Utility\String::truncate($abstract, 250) : $row->abstract;
		$row->description 	= $description ? $description : $row->description;
		$row->metadata 		= $metadata ? $metadata : $row->metadata;	
		$row->published_up 	= $published_up ? $published_up : $row->published_up;
		$row->release_notes	= $release_notes;
						
		// Update DOI with latest information
		if ($row->doi && !$action 
			&& ($row->title != $old->title
			|| $row->abstract != $old->abstract))
		{
			// Collect DOI metadata
			$metadata = $this->_collectMetadata($row, $objP, $authors);
			
			if (!PublicationUtilities::updateDoi($row->doi, $row, $authors, $this->config, $metadata, $doierr))
			{
				$this->setError(JText::_('COM_PUBLICATIONS_ERROR_DOI').' '.$doierr);
			}
		}
		
		// Get parameters
		$params = JRequest::getVar('params', '', 'post');
		if (is_array($params))
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode("\n", $txt);
		}
		
		// Email config
		$pubtitle 	= \Hubzero\Utility\String::truncate($row->title, 100);
		$subject 	= JText::_('Version') . ' ' . $row->version_label . ' ' 
					. JText::_('COM_PUBLICATIONS_OF') . ' ' . JText::_('publication') . ' "' . $pubtitle . '" ';
		$sendmail 	= 0;
		$message 	= rtrim(\Hubzero\Utility\Sanitize::clean(JRequest::getVar( 'message', '' )));
		$output 	= JText::_('Item successfully saved.');			
		
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
				    	 
					// MKAIP --------------> 
					$mkaip = JPATH_BASE . '/../cli/mkaip/bin/mkaip';

					if (file_exists($mkaip))
					{
						$row->state = 10;	// preserving (generating AIP)
					}
					else
					{
						$row->state = 1;	// published
					}
					// MKAIP -------------->
					  
				 	$activity = $action == 'publish' 
						? JText::_('COM_PUBLICATIONS_ACTIVITY_ADMIN_PUBLISHED')
						: JText::_('COM_PUBLICATIONS_ACTIVITY_ADMIN_REPUBLISHED');   
					$subject .= $action == 'publish'  
						? JText::_('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
						: JText::_('COM_PUBLICATIONS_MSG_ADMIN_REPUBLISHED');
						
					$row->published_down = '0000-00-00 00:00:00';
					if ( $action == 'publish')
					{
						$row->published_up 	 = $published_up ? $published_up : JFactory::getDate()->toSql();
					}
					
					// Collect DOI metadata
					$metadata = $this->_collectMetadata($row, $objP, $authors);
																								
					// Issue a DOI
					if ($this->config->get('doi_service') && $this->config->get('doi_shoulder'))
					{
						if (!$row->doi) 
						{					
							$doi = PublicationUtilities::registerDoi($row, $authors, $this->config, $metadata, $doierr);

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
					}
					$row->modified = JFactory::getDate()->toSql();
					$row->modified_by = $this->juser->get('id');
					
					// MKAIP -------------->
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
					// MKAIP -------------->
					
					if (!$this->getError()) 
					{
						$output .= ' '.JText::_('COM_PUBLICATIONS_ITEM').' ';
						$output .= $action == 'publish'  
							? JText::_('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
							: JText::_('COM_PUBLICATIONS_MSG_ADMIN_REPUBLISHED');
					}
					break;
					
				case 'revert':
					$row->state 		 	= 4; // revert to draft
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
			
			$subject = $subject ? $subject : JText::_('COM_PUBLICATIONS_STATUS_UPDATE');
			
			// Get message body
			$eview 					= new JView( array('name'=>'emails', 'layout' => 'admin_plain' ) );
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
				$this->setError(JText::_('Failed to message authors.'));
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
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'publications.css');

		// Incoming publication ID
		$id = JRequest::getInt('id', 0);
	
		// Need ID
		if (!$id)
		{		
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Publication not found.'),
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
			JError::raiseError( 404, JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND') );
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
		$id 	 = JRequest::getInt( 'id', 0 );
		$version = JRequest::getVar( 'version', '' );
		
		// Ensure we have some IDs to work with
		if (!$id)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Cannot load the publication to delete.'),
				'notice'
			);
			return;			
		}
		
		// Load publication
		$objP = new Publication( $this->database );
		if (!$objP->load($id)) 
		{
			JError::raiseError( 404, JText::_('COM_PUBLICATIONS_NOT_FOUND') );
			return;
		}

		if ($version != 'all') 
		{			
			// Check that version exists
			$version = $row->checkVersion($id, $version) ? $version : 'default';
			
			// Load version	
			$row = new PublicationVersion($this->database);		
			if (!$row->loadVersion($pid, $version)) 
			{
				JError::raiseError( 404, JText::_('COM_PUBLICATIONS_VERSION_NOT_FOUND') );
				return;
			}

			// Save version ID
			$vid = $row->id;						
		}
		else 
		{
			// Delete all versions
		}
		
		// Redirect
		$output = ($version != 'all') ? JText::_('Publication version deleted.') : JText::_('Publication records deleted.');
		$this->setRedirect(
			$this->buildRedirectURL(),
			$output
		);
		
		return;
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
		$this->_redirect = $this->buildRedirectURL($id);
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

			$this->_message = JText::_('Successfully reset Rating');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id[]=' . $id,
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

			$this->_message = JText::_('Successfully reset Ranking');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id[]=' . $id,
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
		
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'helper.php' );

		if ($pid)
		{
			JPluginHelper::importPlugin( 'projects', 'publications' );
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger( 'archivePub', array($pid, $vid) );

			$this->_message = JText::_('Archival package produced');
		}
		
		// Checkin the resource
		$row = new Publication($this->database);
		$row->load($pid);
		$row->checkin();
		
		$url = 'index.php?option=' . $this->_option . '&controller=' 
			. $this->_controller . '&task=edit' . '&id[]=' . $pid . '&version=' . $version;

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
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}

	/**
	 * Builds the appropriate URL for redirction
	 * 
	 * @param      integer $pid Parent resource ID (optional)
	 * @return     string
	 */
	private function buildRedirectURL($pid=0)
	{
		$url  = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		return $url;
	}

	/**
	 * Builds a select list of users
	 * 
	 * @param      string  $name       Name of the select element
	 * @param      string  $active     Selected value
	 * @param      integer $nouser     Display an empty start option
	 * @param      string  $javascript Any JS to attach to the select element
	 * @param      string  $order      Field to order the users by
	 * @return     string
	 */
	private function userSelect($name, $active, $nouser=0, $javascript=NULL, $order='a.name')
	{
		$database = JFactory::getDBO();

		$group_id = 'g.id';
		$aro_id = 'aro.id';

		$query = "SELECT a.id AS value, a.name AS text, g.name AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"	// map user to aro
			. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = " . $aro_id . ""	// map aro to group
			. "\n INNER JOIN #__core_acl_aro_groups AS g ON " . $group_id . " = gm.group_id"
			. "\n WHERE a.block = '0' AND " . $group_id . "=25"
			. "\n ORDER BY ". $order;

		$database->setQuery($query);
		$result = $database->loadObjectList();

		if ($nouser)
		{
			$users[] = JHTML::_('select.option', '0', 'Do not change', 'value', 'text');
			$users = array_merge($users, $result);
		}
		else
		{
			$users = $result;
		}

		$users = JHTML::_('select.genericlist', $users, $name, ' ' . $javascript, 'value', 'text', $active, false, false);

		return $users;
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

