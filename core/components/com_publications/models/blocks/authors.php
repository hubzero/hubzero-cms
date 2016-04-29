<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

namespace Components\Publications\Models\Block;

use Components\Publications\Models\Block as Base;
use stdClass;

/**
 * Authors block
 */
class Authors extends Base
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
	* Numeric block ID
	*
	* @var		integer
	*/
	protected $_blockId 		= 0;

	/**
	 * Display block content
	 *
	 * @return  string  HTML
	 */
	public function display( $pub = NULL, $manifest = NULL, $viewname = 'edit', $blockId = 0)
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Register blockId
		$this->_blockId	= $blockId;

		if ($viewname == 'curator')
		{
			// Output HTML
			$view = new \Hubzero\Component\View(
				array(
					'name'		=> 'curation',
					'layout'	=> 'block'
				)
			);
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
		$view->step			= $blockId;
		$showGroupOwner     = isset($this->_manifest->params->group_owner) ? $this->_manifest->params->group_owner : '';
		$view->showControls = $showGroupOwner ? 4 : 2;

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
	public function save( $manifest = NULL, $blockId = 0, $pub = NULL, $actor = 0, $elementId = 0)
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

		$selections = Request::getVar( 'selecteditems', '');
		$toAttach = explode(',', $selections);
		$added = 0;

		// Load classes
		$pAuthor  = new \Components\Publications\Tables\Author( $this->_parent->_db );
		$objO 	  = new \Components\Projects\Tables\Owner( $this->_parent->_db );

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
					$pAuthor->modified 		= Date::toSql();
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
				$pAuthor->created 					= Date::toSql();
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
			$this->set('_message', Lang::txt('Author selection saved') );
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
		$group_owner = Request::getInt( 'group_owner', 0);

		$saveGroupOwner = isset($this->_manifest->params->group_owner) ? $this->_manifest->params->group_owner : '';

		if ($saveGroupOwner)
		{
			$pub->publication->group_owner = $group_owner;
			$pub->publication->store();
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
			$pAuthors 			= new \Components\Publications\Tables\Author( $this->_parent->_db );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);
			$pub->_submitter 	= $pAuthors->getSubmitter($pub->version_id, $pub->created_by);
		}

		if (!$pub->_authors)
		{
			return false;
		}

		foreach ($pub->_authors as $author)
		{
			$pAuthor 							= new \Components\Publications\Tables\Author( $this->_parent->_db );
			$pAuthor->user_id 					= $author->user_id;
			$pAuthor->ordering 					= $author->ordering;
			$pAuthor->credit 					= $author->credit;
			$pAuthor->role 						= $author->role;
			$pAuthor->status 					= $author->status;
			$pAuthor->organization 				= $author->organization;
			$pAuthor->name 						= $author->name;
			$pAuthor->project_owner_id 			= $author->project_owner_id;
			$pAuthor->publication_version_id 	= $newVersion->id;
			$pAuthor->created 					= Date::toSql();
			$pAuthor->created_by 				= User::get('id');
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
	public function reorder( $manifest = NULL, $blockId = 0, $pub = NULL, $actor = 0, $elementId = 0)
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Incoming
		$list = Request::getVar( 'list', '' );
		$authors = explode("-", $list);

		$o = 1;
		foreach ($authors as $id)
		{
			if (!trim($id))
			{
				continue;
			}

			$pAuthor = new \Components\Publications\Tables\Author( $this->_parent->_db );
			if ($pAuthor->load($id))
			{
				$pAuthor->ordering = $o;
				$o++;

				$pAuthor->store();
			}
		}

		$this->set('_message', Lang::txt('New author order saved') );

		return true;
	}

	/**
	 * Add new author
	 *
	 * @return  void
	 */
	public function addItem ($manifest, $blockId, $pub, $actor = 0, $elementId = 0)
	{
		$email 		= Request::getVar( 'email', '', 'post' );
		$firstName 	= trim(Request::getVar( 'firstName', '', 'post' ));
		$lastName 	= trim(Request::getVar( 'lastName', '', 'post' ));
		$org 		= trim(Request::getVar( 'organization', '', 'post' ));
		$credit 	= trim(Request::getVar( 'credit', '', 'post' ));
		$uid 		= trim(Request::getInt( 'uid', 0, 'post' ));

		$regex 		= '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
		$email 		= preg_match($regex, $email) ? $email : '';
		$name 		= $firstName . ' ' . $lastName;

		$sendInvite = 0;
		$exists 	= 0;
		$code 		= \Components\Projects\Helpers\Html::generateCode();

		if (!$firstName || !$lastName)
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
			return false;
		}

		// Load classes
		$objO = new \Components\Projects\Tables\Owner( $this->_parent->_db );

		// Instantiate a new registration object
		include_once(PATH_CORE . DS . 'components' . DS
			. 'com_members' . DS . 'models' . DS . 'registration.php');
		$xregistration = new \Components\Members\Models\Registration();

		// Do we have a registered user with this email?
		if ($email && !$uid)
		{
			$uid = $xregistration->getEmailId( $email );

			// Check that profile exists
			if ($uid)
			{
				$profile = User::getInstance($uid);
				$uid = $profile->get('id') ? $uid : 0;
			}
		}

		// Do we have an owner with this email/uid?
		$owner = NULL;
		if ($uid)
		{
			$owner = $objO->getOwnerId( $pub->_project->get('id'), $uid );
		}
		elseif ($email)
		{
			$owner = $objO->checkInvited( $pub->_project->get('id'), $email );
		}
		elseif (trim($name))
		{
			// Check by invite name
			$owner = $objO->checkInvitedByName( $pub->_project->get('id'), trim($name));
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
			$objO = new \Components\Projects\Tables\Owner( $this->_parent->_db );

			$objO->projectid 	 = $pub->_project->get('id');
			$objO->userid 		 = $uid;
			$objO->status 		 = $uid ? 1 : 0;
			$objO->added 		 = Date::toSql();
			$objO->role 		 = 2;
			$objO->invited_email = $email;
			$objO->invited_name  = $name;

			if ($email)
			{
				$objO->invited_code = $code;
			}

			$objO->store();

			$owner 				 = $objO->id;
			$sendInvite 		 = ($email || $uid) ? 1 : 0;
		}

		// Now we do need owner record
		if (!$owner)
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_SAVING_AUTHOR_INFO'));
			return false;
		}

		// Get author information
		$pAuthor = new \Components\Publications\Tables\Author( $this->_parent->_db );

		if ($pAuthor->loadAssociationByOwner( $owner, $pub->version_id ))
		{
			$pAuthor->modified 		= Date::toSql();
			$pAuthor->modified_by 	= $actor;
			$exists = 1;
		}
		else
		{
			$pAuthor->created 				 = Date::toSql();
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
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_SAVING_AUTHOR_INFO'));
			return false;
		}

		// Reflect the update in curation record
		$this->_parent->set('_update', 1);

		// (Re)send email invitation
		if ($sendInvite && ($email || $uid))
		{
			// Get project model
			$project = new \Components\Projects\Models\Project($pub->_project->get('id'));

			// Load component language file
			Lang::load('com_projects') || Lang::load('com_projects', PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'site');

			// Plugin params
			$plugin_params = array(
				$uid,
				$email,
				$code,
				2,
				$project,
				'com_projects'
			);

			// Send invite
			$output = Event::trigger( 'projects.sendInviteEmail', $plugin_params);
			$result = json_decode($output[0]);
		}

		$message = $exists
			? Lang::txt('Author already in team, updated author information')
			: Lang::txt('New author added');

		$this->set('_message', $message );
		return true;
	}

	/**
	 * Update attachment record
	 *
	 * @return  void
	 */
	public function saveItem ($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : Request::getInt( 'aid', 0 );

		// Load classes
		$row  = new \Components\Publications\Tables\Author( $this->_parent->_db );
		$objO = new \Components\Projects\Tables\Owner( $this->_parent->_db );

		// We need attachment record
		if (!$aid || !$row->load($aid) || $row->publication_version_id != $pub->version_id)
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_LOAD_AUTHOR'));
			return false;
		}

		// Instantiate a new registration object
		include_once(PATH_CORE . DS . 'components' . DS . 'com_members'
			. DS . 'models' . DS . 'registration.php');
		$xregistration = new \Components\Members\Models\Registration();

		// Get current owners
		$owners = $objO->getIds($pub->_project->get('id'), 'all', 1);

		$email 		= Request::getVar( 'email', '', 'post' );
		$firstName 	= Request::getVar( 'firstName', '', 'post' );
		$lastName 	= Request::getVar( 'lastName', '', 'post' );
		$org 		= Request::getVar( 'organization', '', 'post' );
		$credit 	= Request::getVar( 'credit', '', 'post' );
		$sendInvite = 0;
		$code 		= \Components\Projects\Helpers\Html::generateCode();
		$uid 		= Request::getInt( 'uid', 0, 'post' );

		$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
		$email = preg_match($regex, $email) ? $email : '';

		if (!$firstName || !$lastName)
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
			return false;
		}

		$row->organization  = $org;
		$row->firstName 	= $firstName;
		$row->lastName 		= $lastName;
		$row->name 	 		= $row->firstName . ' ' . $row->lastName;
		$row->credit 		= $credit;
		$row->modified_by 	= $actor;
		$row->modified 		= Date::toSql();

		// Check that profile exists
		if ($uid)
		{
			$profile = User::getInstance($uid);
			$uid = $profile->get('id') ? $uid : 0;
		}

		// Tying author to a user account?
		if ($uid && !$row->user_id)
		{
			// Do we have an owner with this user id?
			$owner = $objO->getOwnerId( $pub->_project->get('id'), $uid );

			if ($owner)
			{
				// Update owner assoc
				$row->project_owner_id = $owner;
			}
			else
			{
				// Update associated project owner account
				if ($objO->load($row->project_owner_id) && !$objO->userid)
				{
					$objO->userid = $uid;
					$objO->status = 1;
					$objO->store();
				}
			}
		}
		$row->user_id = $uid;

		if ($row->store())
		{
			$this->set('_message', Lang::txt('Author record saved') );

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);
		}
		else
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_SAVING_AUTHOR_INFO'));
			return false;
		}

		// Update project owner (invited)
		if ($email && !$row->user_id && $objO->load($row->project_owner_id))
		{
			$invitee = $objO->checkInvited( $pub->_project->get('id'), $email );

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
				$objO->invited_code  = $code;
				$objO->store();
				$sendInvite = 1;
			}
		}

		// (Re)send email invitation
		if ($sendInvite && $email)
		{
			// Get project model
			$project = new \Components\Projects\Models\Project($pub->_project->get('id'));

			// Plugin params
			$plugin_params = array(
				0,
				$email,
				$code,
				2,
				$project,
				'com_projects'
			);

			// Send invite
			$output = Event::trigger( 'projects.sendInviteEmail', $plugin_params);
			$result = json_decode($output[0]);
		}

		return true;
	}

	/**
	 * Delete author record
	 *
	 * @return  void
	 */
	public function deleteItem ($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : Request::getInt( 'aid', 0 );

		// Load classes
		$row  = new \Components\Publications\Tables\Author( $this->_parent->_db );
		$objO = new \Components\Projects\Tables\Owner( $this->_parent->_db );

		// We need attachment record
		if (!$aid || !$row->load($aid) || $row->publication_version_id != $pub->version_id)
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_LOAD_AUTHOR'));
			return false;
		}

		if ($row->deleteAssociationByOwner($row->project_owner_id, $row->publication_version_id))
		{
			$this->set('_message', Lang::txt('Author deleted') );

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
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'team','selector');

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
			$pAuthors 			= new \Components\Publications\Tables\Author( $this->_parent->_db );
			$pub->_authors 		= $pAuthors->getAuthors($pub->version_id);
			$pub->_submitter 	= $pAuthors->getSubmitter($pub->version_id, $pub->created_by);
		}

		// Get creator groups
		$view->groups = \Hubzero\User\Helper::getGroups($pub->_project->get('owned_by_user'), 'members', 1);

		$view->pub		= $pub;
		$view->manifest = $this->_manifest;
		$view->step		= $this->_blockId;

		// Get team members
		$objO = new \Components\Projects\Tables\Owner( $this->_parent->_db );
		$view->teamids = $objO->getIds( $pub->_project->get('id'), 'all', 0, 0 );

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
		$status 	 = new \Components\Publications\Models\Status();

		// Get authors
		if (!isset($pub->_authors))
		{
			$pAuthors 			= new \Components\Publications\Tables\Author( $this->_parent->_db );
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
		$obj = new \Components\Publications\Tables\Block($this->_parent->_db);
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
				'params'		=> array(
					'required'          => 1,
					'published_editing' => 0,
					'submitter'         => 1,
					'group_owner'       => 0
				)
			);

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}
