<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Block;

use Components\Publications\Models\Block as Base;
use stdClass;

/**
 * Review block
 */
class Review extends Base
{
	/**
  * Element name
  *
  * @var		string
  */
	protected	$_name 			= 'review';

	/**
  * Parent block name
  *
  * @var		string
  */
	protected	$_parentname 	= null;

	/**
  * Default manifest
  *
  * @var		string
  */
	protected	$_manifest 		= null;

	/**
  * Numeric block ID
  *
  * @var		integer
  */
	protected	$_blockId 		= 0;

	/**
	 * Display block content
	 *
	 * @return  string  HTML
	 */
	public function display( $pub = null, $manifest = null, $viewname = 'review', $blockId = 0)
	{
		// Set block manifest
		if ($this->_manifest === null)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Register blockId
		$this->_blockId	= $blockId;

		if ($viewname == 'curator')
		{
			// Do not show
			return;
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
		$view->showControls	= 0;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Build panel content
	 *
	 * @return  string  HTML
	 */
	public function buildContent( $pub = null, $viewname = 'edit' )
	{
		$name = $viewname == 'freeze' || $viewname == 'curator' ? 'freeze' : 'draft';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'publications',
				'name'		=> $name,
				'layout'	=> 'review'
			)
		);

		\Hubzero\Document\Assets::addComponentStylesheet('com_projects', 'css/calendar');

		$view->pub		= $pub;
		$view->manifest = $this->_manifest;
		$view->step		= $this->_blockId;

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
	public function save( $manifest = null, $blockId = 0, $pub = null, $actor = 0, $elementId = 0)
	{
		// Set block manifest
		if ($this->_manifest === null)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		return true;
	}

	/**
	 * Check completion status
	 *
	 * @return  object
	 */
	public function getStatus( $pub = null, $manifest = null, $elementId = null )
	{
		// Start status
		$status 	 	= new \Components\Publications\Models\Status();
		$status->status = 1;
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
				'name' 			=> 'review',
				'label' 		=> 'Review',
				'title' 		=> 'Publication Review',
				'draftHeading' 	=> 'Review Publication',
				'draftTagline'	=> 'Here is your publication at a glance:',
				'about'			=> '',
				'adminTips'		=> '',
				'elements' 		=> array(),
				'params'		=> array(  'required' => 1, 'published_editing' => 0 )
			);

			return json_decode(json_encode($manifest), false);
		}

		return $manifest;
	}
	
	/**
	 * Update author record
	 *
	 * @param   object   $manifest
	 * @param   integer  $blockId
	 * @param   object   $pub
	 * @param   integer  $actor
	 * @param   integer  $elementId
	 * @param   integer  $aid
	 * @return  void
	 */
	public function saveItem($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : Request::getInt('aid', 0);

		// Load classes
		$row  = new \Components\Publications\Tables\Author($this->_parent->_db);
		$objO = new \Components\Projects\Tables\Owner($this->_parent->_db);

		// We need attachment record
		if (!$aid || !$row->load($aid) || $row->publication_version_id != $pub->version_id)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_LOAD_AUTHOR'));
			return false;
		}

		// Instantiate a new registration object
		include_once \Component::path('com_members') . DS . 'models' . DS . 'registration.php';
		$xregistration = new \Components\Members\Models\Registration();

		// Get current owners
		$owners = $objO->getIds($pub->_project->get('id'), 'all', 1);

		$config = Component::params('com_publications');

		$emailConfig = $config->get('email');
		$email      = Request::getString('email', '', 'post');
		$firstName  = Request::getString('firstName', '', 'post');
		$lastName   = Request::getString('lastName', '', 'post');
		$org        = Request::getString('organization', '', 'post');
		$credit     = Request::getString('credit', '', 'post');
		$sendInvite = 0;
		$code       = \Components\Projects\Helpers\Html::generateCode();
		$uid        = Request::getInt('uid', 0, 'post');

		$regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
		$email = preg_match($regex, $email) ? $email : '';

		if (!$firstName || !$lastName)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
			return false;
		}

		$row->organization  = $org;
		$row->firstName     = $firstName;
		$row->lastName      = $lastName;
		$row->name          = $row->firstName . ' ' . $row->lastName;
		$row->credit        = $credit;
		$row->modified_by   = $actor;
		$row->modified      = Date::toSql();

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
			$owner = $objO->getOwnerId($pub->_project->get('id'), $uid);

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
			$this->set('_message', Lang::txt('Author record saved'));

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);
		}
		else
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_ERROR_SAVING_AUTHOR_INFO'));
			return false;
		}

		// Update project owner (invited)
		if ($email && !$row->user_id && $objO->load($row->project_owner_id))
		{
			$invitee = $objO->checkInvited($pub->_project->get('id'), $email);

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
				$objO->userid        = $row->user_id;
				$objO->invited_code  = $code;
				$objO->store();
			}
		}

		return true;
	}
}
