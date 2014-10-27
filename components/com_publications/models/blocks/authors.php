<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * Authors block
 */
class PublicationsBlockAuthors extends PublicationsModelBlock
{
	/**
	* Block name
	*
	* @var		string
	*/
	protected $_name 			= 'authors';

	/**
	* Parent block name
	*
	* @var		string
	*/
	protected $_parentname 		= 'authors';

	/**
	* Default manifest
	*
	* @var		string
	*/
	protected $_manifest 		= NULL;

	/**
	* Step number
	*
	* @var		integer
	*/
	protected $_sequence 		= 0;

	/**
	 * Display block content
	 *
	 * @return  string  HTML
	 */
	public function display( $pub = NULL, $manifest = NULL, $viewname = 'edit', $sequence = 0)
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Register sequence
		$this->_sequence	= $sequence;

		if ($viewname == 'curator')
		{
			// Output HTML
			$view = new JView( array('name'=>'curation', 'layout'=> 'block' ) );
		}
		else
		{
			$name = $viewname == 'freeze' ? 'freeze' : 'draft';

			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=> 'projects',
					'element'	=> 'publications',
					'name'		=> $name,
					'layout'	=> 'wrapper'
				)
			);
		}

		$view->manifest 	= $this->_manifest;
		$view->content 		= self::buildContent( $pub, $viewname );
		$view->pub			= $pub;
		$view->active		= $this->_name;
		$view->step			= $sequence;
		$view->showControls	= 2;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Save block content
	 *
	 * @return  string  HTML
	 */
	public function save( $manifest = NULL, $sequence = 0, $pub = NULL, $actor = 0, $elementId = 0)
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Make sure changes are allowed
		if ($this->_parent->checkFreeze($this->_manifest->params, $pub))
		{
			return false;
		}

		$selections = JRequest::getVar( 'selecteditems', '');
		$toAttach = explode(',', $selections);
		$added = 0;

		// Load classes
		$pAuthor  = new PublicationAuthor( $this->_parent->_db );
		$objO 	  = new ProjectOwner( $this->_parent->_db );

		$order = $pAuthor->getLastOrder($pub->version_id) + 1;

		foreach ($toAttach as $owner)
		{
			if (!trim($owner))
			{
				continue;
			}

			if ($pAuthor->loadAssociationByOwner($owner, $pub->version_id))
			{
				// Restore deleted author
				if ($pAuthor->status == 2 || $pAuthor->status == 0)
				{
					$pAuthor->status 		= 1;
					$pAuthor->modified 		= JFactory::getDate()->toSql();
					$pAuthor->modified_by 	= $actor;

					if ($pAuthor->updateAssociationByOwner())
					{
						// Reflect the update in curation record
						$this->_parent->set('_update', 1);
					}
				}
			}
			else
			{
				$profile = $pAuthor->getProfileInfoByOwner($owner);
				$invited = $profile->invited_name ? $profile->invited_name : $profile->invited_email;

				$pAuthor->project_owner_id 			= $owner;
				$pAuthor->publication_version_id 	= $pub->version_id;
				$pAuthor->user_id 					= $profile->uidNumber ? $profile->uidNumber : 0;
				$pAuthor->ordering 					= $order;
				$pAuthor->status 					= 1;
				$pAuthor->organization 				= $profile->organization ? $profile->organization : '';
				$pAuthor->name 						= $profile && $profile->name ? $profile->name : $invited;
				$pAuthor->firstName 				= $profile->givenName ? $profile->givenName : '';
				$pAuthor->lastName 					= $profile->surname ? $profile->surname : '';
				$pAuthor->created 					= JFactory::getDate()->toSql();
				$pAuthor->created_by 				= $actor;

				if (!$pAuthor->createAssociation())
				{
					continue;
				}
				else
				{
					// Update ordering
					$order++;

					// Reflect the update in curation record
					$this->_parent->set('_update', 1);

					$added++;
				}
			}
		}

		if ($added)
		{
			$this->set('_message', JText::_('Author selection saved') );
		}

		// Save group owner
		if (!$selections)
		{
			$this->saveGroupOwner($pub);
		}

		return true;
	}

	/**
	 * Save group owner
	 *
	 * @return  void
	 */
	public function saveGroupOwner( $pub )
	{
		// Incoming
		$group_owner = JRequest::getInt( 'group_owner', 0);

		$saveGroupOwner = isset($this->_manifest->params->group_owner) ? $this->_manifest->params->group_owner : '';

		if ($saveGroupOwner)
		{
			$objP = new Publication( $this->_parent->_db );

			if ($objP->load($pub->id))
			{
				$objP->group_owner = $group_owner;
				$objP->store();
			}
		}
	}

	/**
	 * Transfer data from one version to another
	 *
	 * @return  boolean
	 */
	public function transferData( $manifest, $pub, $oldVersion, $newVersion )
	{
		// Get authors
		if (!isset($pub->_authors))
		{
			$pAuthors 			= new PublicationAuthor( $this->_parent->_db );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);
			$pub->_submitter 	= $pAuthors->getSubmitter($pub->version_id, $pub->created_by);
		}

		$juser = JFactory::getUser();

		if (!$pub->_authors)
		{
			return false;
		}

		foreach ($pub->_authors as $author)
		{
			$pAuthor 							= new PublicationAuthor( $this->_parent->_db );
			$pAuthor->user_id 					= $author->user_id;
			$pAuthor->ordering 					= $author->ordering;
			$pAuthor->credit 					= $author->credit;
			$pAuthor->role 						= $author->role;
			$pAuthor->status 					= $author->status;
			$pAuthor->organization 				= $author->organization;
			$pAuthor->name 						= $author->name;
			$pAuthor->project_owner_id 			= $author->project_owner_id;
			$pAuthor->publication_version_id 	= $newVersion->id;
			$pAuthor->created 					= JFactory::getDate()->toSql();
			$pAuthor->created_by 				= $juser->get('id');
			if (!$pAuthor->createAssociation())
			{
				continue;
			}
		}

		return true;
	}

	/**
	 * Save block content
	 *
	 * @return  string  HTML
	 */
	public function reorder( $manifest = NULL, $sequence = 0, $pub = NULL, $actor = 0, $elementId = 0)
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Incoming
		$list = JRequest::getVar( 'list', '' );
		$authors = explode("-", $list);

		$o = 1;
		foreach ($authors as $id)
		{
			if (!trim($id))
			{
				continue;
			}

			$pAuthor = new PublicationAuthor( $this->_parent->_db );
			if ($pAuthor->load($id))
			{
				$pAuthor->ordering = $o;
				$o++;

				$pAuthor->store();
			}
		}

		$this->set('_message', JText::_('New author order saved') );

		return true;
	}

	/**
	 * Add new author
	 *
	 * @return  void
	 */
	public function addItem ($manifest, $sequence, $pub, $actor = 0, $elementId = 0)
	{
		$email 		= JRequest::getVar( 'email', '', 'post' );
		$firstName 	= trim(JRequest::getVar( 'firstName', '', 'post' ));
		$lastName 	= trim(JRequest::getVar( 'lastName', '', 'post' ));
		$org 		= trim(JRequest::getVar( 'organization', '', 'post' ));
		$credit 	= trim(JRequest::getVar( 'credit', '', 'post' ));
		$uid 		= trim(JRequest::getInt( 'uid', 0, 'post' ));

		$regex 		= '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
		$email 		= preg_match($regex, $email) ? $email : '';
		$name 		= $firstName . ' ' . $lastName;

		$sendInvite = 0;
		$exists 	= 0;
		$code 		= ProjectsHtml::generateCode();

		if (!$firstName || !$lastName || !$org)
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
			return false;
		}

		// Load classes
		$objO = new ProjectOwner( $this->_parent->_db );

		// Instantiate a new registration object
		include_once(JPATH_ROOT . DS . 'components' . DS
			. 'com_members' . DS . 'models' . DS . 'registration.php');
		$xregistration = new MembersModelRegistration();

		// Do we have a registered user with this email?
		if ($email && !$uid)
		{
			$uid = $xregistration->getEmailId( $email );

			// Check that profile exists
			if ($uid)
			{
				$profile = \Hubzero\User\Profile::getInstance($uid);
				$uid = $profile->get('uidNumber') ? $uid : 0;
			}
		}

		// Do we have an owner with this email/uid?
		$owner = NULL;
		if ($uid)
		{
			$owner = $objO->getOwnerId( $pub->_project->id, $uid );
		}
		elseif ($email)
		{
			$owner = $objO->checkInvited( $pub->_project->id, $email );
		}

		if ($owner && $objO->load($owner))
		{
			if ($email && $objO->invited_email != $email)
			{
				$sendInvite = 1;
			}
			$objO->status 			= $objO->userid ? 1 : 0;
			$objO->invited_name 	= $objO->userid ? $objO->invited_name : $name;
			$objO->invited_email 	= $objO->userid ? $objO->invited_email : $email;
			$objO->store();
		}
		elseif ($email || trim($name))
		{
			$objO = new ProjectOwner( $this->_parent->_db );

			$objO->projectid 	 = $pub->_project->id;
			$objO->userid 		 = $uid;
			$objO->status 		 = $uid ? 1 : 0;
			$objO->added 		 = JFactory::getDate()->toSql();
			$objO->role 		 = 2;
			$objO->invited_email = $email;
			$objO->invited_name  = $name;

			if ($email)
			{
				$objO->invited_code = $code;
			}

			$objO->store();

			$owner 				 = $objO->id;
			$sendInvite 		 = $email ? 1 : 0;
		}

		// Now we do need owner record
		if (!$owner)
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_SAVING_AUTHOR_INFO'));
			return false;
		}

		// Get author information
		$pAuthor = new PublicationAuthor( $this->_parent->_db );

		if ($pAuthor->loadAssociationByOwner( $owner, $pub->version_id ))
		{
			$pAuthor->modified 		= JFactory::getDate()->toSql();
			$pAuthor->modified_by 	= $actor;
			$exists = 1;
		}
		else
		{
			$pAuthor->created 				 = JFactory::getDate()->toSql();
			$pAuthor->created_by 			 = $actor;
			$pAuthor->publication_version_id = $pub->version_id;
			$pAuthor->project_owner_id 		 = $owner;
			$pAuthor->user_id                = intval($uid);
			$pAuthor->ordering 	             = $pAuthor->getLastOrder($pub->version_id) + 1;
			$pAuthor->role 				 	 = '';
		}

		$pAuthor->status 		= 1;
		$pAuthor->name   		= $name;
		$pAuthor->firstName 	= $firstName;
		$pAuthor->lastName  	= $lastName;
		$pAuthor->organization  = $org;

		if (!$pAuthor->store())
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_SAVING_AUTHOR_INFO'));
			return false;
		}

		// Reflect the update in curation record
		$this->_parent->set('_update', 1);

		// (Re)send email invitation
		if ($sendInvite && $email)
		{
			// Get team plugin
			JPluginHelper::importPlugin( 'projects', 'team' );
			$dispatcher = JDispatcher::getInstance();

			// Plugin params
			$plugin_params = array(
				$uid,
				$email,
				$code,
				2,
				$pub->_project,
				'com_projects'
			);

			// Send invite
			$output = $dispatcher->trigger( 'sendInviteEmail', $plugin_params);
			$result = json_decode($output[0]);
		}

		$message = $exists
			? JText::_('Author already in team, updated author information')
			: JText::_('New author added');

		$this->set('_message', $message );
		return true;
	}

	/**
	 * Update attachment record
	 *
	 * @return  void
	 */
	public function saveItem ($manifest, $sequence, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : JRequest::getInt( 'aid', 0 );

		// Load classes
		$row  = new PublicationAuthor( $this->_parent->_db );
		$objO = new ProjectOwner( $this->_parent->_db );

		// We need attachment record
		if (!$aid || !$row->load($aid) || $row->publication_version_id != $pub->version_id)
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_LOAD_AUTHOR'));
			return false;
		}

		// Instantiate a new registration object
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members'
			. DS . 'models' . DS . 'registration.php');
		$xregistration = new MembersModelRegistration();

		// Get current owners
		$owners = $objO->getIds($pub->_project->id, 'all', 1);

		$email 		= JRequest::getVar( 'email', '', 'post' );
		$firstName 	= JRequest::getVar( 'firstName', '', 'post' );
		$lastName 	= JRequest::getVar( 'lastName', '', 'post' );
		$org 		= JRequest::getVar( 'organization', '', 'post' );
		$credit 	= JRequest::getVar( 'credit', '', 'post' );
		$sendInvite = 0;
		$code 		= ProjectsHtml::generateCode();

		$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
		$email = preg_match($regex, $email) ? $email : '';

		if (!$firstName || !$lastName || !$org)
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
			return false;
		}

		$row->organization  = $org;
		$row->firstName 	= $firstName;
		$row->lastName 		= $lastName;
		$row->name 	 		= $row->firstName . ' ' . $row->lastName;
		$row->credit 		= $credit;
		$row->modified_by 	= $actor;
		$row->modified 		= JFactory::getDate()->toSql();

		if ($row->store())
		{
			$this->set('_message', JText::_('Author record saved') );

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);
		}
		else
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_SAVING_AUTHOR_INFO'));
			return false;
		}

		// Update project owner (invited)
		if ($email && !$row->user_id && $objO->load($row->project_owner_id))
		{
			$invitee = $objO->checkInvited( $pub->_project->id, $email );

			// Do we have a registered user with this email?
			$user = $xregistration->getEmailId($email);

			if ($invitee && $invitee != $row->project_owner_id)
			{
				// Stop, must have owner record
			}
			elseif (in_array($user, $owners))
			{
				// Stop, already in team
			}
			elseif ($email != $objO->invited_email)
			{
				$objO->invited_email = $email;
				$objO->invited_name  = $row->name;
				$objO->userid 		 = $row->user_id;
				$sendInvite 		 = 1;
				$objO->invited_code = $code;
				$objO->store();
			}
		}

		// (Re)send email invitation
		if ($sendInvite && $email)
		{
			// Get team plugin
			JPluginHelper::importPlugin( 'projects', 'team' );
			$dispatcher = JDispatcher::getInstance();

			// Plugin params
			$plugin_params = array(
				0,
				$email,
				$code,
				2,
				$pub->_project,
				'com_projects'
			);

			// Send invite
			$output = $dispatcher->trigger( 'sendInviteEmail', $plugin_params);
			$result = json_decode($output[0]);
		}

		return true;
	}

	/**
	 * Delete author record
	 *
	 * @return  void
	 */
	public function deleteItem ($manifest, $sequence, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : JRequest::getInt( 'aid', 0 );

		// Load classes
		$row  = new PublicationAuthor( $this->_parent->_db );
		$objO = new ProjectOwner( $this->_parent->_db );

		// We need attachment record
		if (!$aid || !$row->load($aid) || $row->publication_version_id != $pub->version_id)
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_LOAD_AUTHOR'));
			return false;
		}

		if ($row->deleteAssociationByOwner($row->project_owner_id, $row->publication_version_id))
		{
			$this->set('_message', JText::_('Author deleted') );

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);
		}

		return true;
	}

	/**
	 * Build panel content
	 *
	 * @return  string  HTML
	 */
	public function buildContent( $pub = NULL, $viewname = 'edit' )
	{
		$name = $viewname == 'freeze' || $viewname == 'curator' ? 'freeze' : 'draft';

		// Get selector styles
		$document = JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'team' . DS . 'css' . DS . 'selector.css');

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'publications',
				'name'		=> $name,
				'layout'	=> 'authors'
			)
		);

		// Get authors
		if (!isset($pub->_authors))
		{
			$pAuthors 			= new PublicationAuthor( $this->_parent->_db );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);
			$pub->_submitter 	= $pAuthors->getSubmitter($pub->version_id, $pub->created_by);
		}

		// Get creator groups
		$view->groups = \Hubzero\User\Helper::getGroups($pub->_project->owned_by_user, 'members', 1);

		$view->pub		= $pub;
		$view->manifest = $this->_manifest;
		$view->step		= $this->_sequence;

		// Get team members
		$objO = new ProjectOwner( $this->_parent->_db );
		$view->teamids = $objO->getIds( $pub->_project->id, 'all', 0, 0 );

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Check completion status
	 *
	 * @return  object
	 */
	public function getStatus( $pub = NULL, $manifest = NULL, $elementId = NULL )
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Start status
		$status 	 = new PublicationsModelStatus();

		// Get authors
		if (!isset($pub->_authors))
		{
			$pAuthors 			= new PublicationAuthor( $this->_parent->_db );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);
			$pub->_submitter 	= $pAuthors->getSubmitter($pub->version_id, $pub->created_by);
		}

		// Are authors required?
		$required 	 	= $this->_manifest->params->required;
		$status->status = $required && (!$pub->_authors || count($pub->_authors) == 0) ? 0 : 1;

		if ($status->status == 0)
		{
			$status->setError('Missing authors');
		}

		return $status;
	}

	/**
	 * Get default manifest for the block
	 *
	 * @return  void
	 */
	public function getManifest($new = false)
	{
		// Load config from db
		$obj = new PublicationBlock($this->_parent->_db);
		$manifest = $obj->getManifest($this->_name);

		// Fall back
		if (!$manifest)
		{
			$manifest = array(
				'name' 			=> 'authors',
				'label' 		=> 'Authors',
				'title' 		=> 'Publication Authors',
				'draftHeading' 	=> 'Who are the authors?',
				'draftTagline'	=> 'Build the author list',
				'about'			=> '<p>Publication authors get selected from your current project team. Anyone you add as an author will also be added to your team as a project collaborator.</p>',
				'adminTips'		=> '',
				'elements' 		=> array(),
				'params'		=> array( 'required' => 1, 'published_editing' => 0, 'submitter' => 1, 'group_owner' => 0 )
			);

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}