<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Publications\Tables;
use Components\Publications\Models;
use Components\Publications\Helpers;
use stdClass;
use Exception;
use Document;
use Pathway;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'publication.php';
include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'curation.php';
require_once \Component::path('com_projects') . DS . 'helpers' . DS . 'html.php';

/**
 * Primary component controller (extends \Hubzero\Component\SiteController)
 */
class Curation extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->_task  = Request::getCmd('task', '');
		$this->_id    = Request::getInt('id', 0);
		$this->_pub   = null;

		// View individual curation
		if ($this->_id && !$this->_task)
		{
			$this->_task = 'view';
		}

		// Get language
		Lang::load('plg_projects_publications');

		//continue with parent execute method
		parent::execute();
	}

	/**
	 * Display task
	 *
	 * @return  void
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
		$mt  = new Tables\MasterType($this->database);
		$authorized = $this->_authorize($mt->getCuratorGroups());

		// Incoming
		$assigned = Request::getInt('assigned', 0);

		// Build query
		$filters = array();
		$filters['limit']         = Request::getInt('limit', 25);
		$filters['start']         = Request::getInt('limitstart', 0);
		$filters['sortby']        = Request::getString('t_sortby', 'submitted');
		$filters['sortdir']       = Request::getString('t_sortdir', 'DESC');
		$filters['ignore_access'] = 1;

		// Only get types for which authorized
		if ($authorized == 'limited')
		{
			$filters['master_type'] = $mt->getAuthTypes($usergroups, $authorized);
		}

		$filters['dev']     = 1; // get dev versions
		$filters['status']  = array(5, 7); // submitted/pending
		$filters['curator'] = $assigned || $authorized == false ? 'owner' : null;
		$this->view->filters = $filters;

		// Instantiate project publication
		$objP = new Tables\Publication($this->database);

		// Get all publications
		$this->view->rows = $objP->getRecords($filters);

		// Get total count
		$this->view->total = $objP->getCount($filters);

		// Initiate paging
		$this->view->pageNav = new \Hubzero\Pagination\Paginator(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// push the stylesheet to the view
		$this->view->css('curation.css', 'plg_projects_publications');

		$this->view
			->set('option', $this->_option)
			->set('database', $this->database)
			->set('config', $this->config)
			->set('title', $this->_title)
			->set('authorized', $authorized)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Build the title for this component
	 *
	 * @return  void
	 */
	protected function _buildTitle()
	{
		if (!$this->_title)
		{
			$this->_title = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_controller));
		}
		Document::setTitle($this->_title);
	}

	/**
	 * Build the "trail"
	 *
	 * @return  void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
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
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller .  '&task=view&id=' . $this->_pub->id
			);
		}
	}

	/**
	 * View publication
	 *
	 * @return  void
	 */
	public function viewTask()
	{
		// Incoming
		$pid     = $this->_id ? $this->_id : Request::getInt('id', 0);
		$version = Request::getString('version', 'default');
		$vid     = Request::getInt('vid');

		if (!$pid)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'), 404);
		}

		// Load publication model
		$this->_pub = new \Components\Publications\Models\Publication($pid, $version, $vid);

		// If publication not found, raise error
		if (!$this->_pub->exists())
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'), 404);
		}

		// We can only view pending publications
		if ($this->_pub->state != 5)
		{
			Notify::warning(Lang::txt('COM_PUBLICATIONS_CURATION_PUB_WRONG_STATUS'));

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=curation', false)
			);
			return;
		}

		// Check curator authorization
		if (!$this->_pub->access('curator'))
		{
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
		}

		// Get curation model
		$this->_pub->setCuration();

		// Get reviewed Items
		$this->_pub->reviewedItems = $this->_pub->_curationModel->getReviewedItems($this->_pub->version_id);

		// Get last history record (from author)
		$history = $this->_pub->_curationModel->getLastHistoryRecord();

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view
			->set('history', $history)
			->set('pub', $this->_pub)
			->set('title', $this->_title)
			->set('option', $this->_option)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * View curation history
	 *
	 * @return  void
	 */
	public function historyTask()
	{
		// Incoming
		$pid     = $this->_id ? $this->_id : Request::getInt('id', 0);
		$version = Request::getString('version', 'default');
		$ajax    = Request::getInt('ajax', 0);

		if (!$pid)
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_RESOURCE_NOT_FOUND'), 404);
		}

		// Load publication model
		$this->_pub = new \Components\Publications\Models\Publication($pid, $version);

		// Publication version exists?
		if (!$this->_pub->exists())
		{
			if ($ajax)
			{
				$this->view
					->set('title', $this->title)
					->setError(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_LOAD'))
					->setName('error')
					->setLayout('default')
					->display();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_LOAD'), 404);
		}

		// Check authorization
		if (!$this->_pub->access('curator'))
		{
			if ($ajax)
			{
				$this->view
					->set('title', $this->title)
					->setError(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'))
					->setName('error')
					->setLayout('default')
					->display();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
		}

		// Set curation
		$this->_pub->setCuration();

		if (!$ajax)
		{
			// Set page title
			$this->_buildTitle();

			// Set the pathway
			$this->_buildPathway();

			// Add plugin style
			$this->view->css('curation.css', 'plg_projects_publications');
		}

		$this->view
			->set('pub', $this->_pub)
			->set('title', $this->_title)
			->set('option', $this->_option)
			->set('ajax', $ajax)
			->display();
	}

	/**
	 * Assign curation
	 *
	 * @return  void
	 */
	public function assignTask()
	{
		// Incoming
		$pid     = $this->_id ? $this->_id : Request::getInt('id', 0);
		$vid     = Request::getInt('vid', 0);
		$owner   = Request::getInt('owner', 0);
		$confirm = Request::getInt('confirm', 0);
		$ajax    = Request::getInt('ajax', 0);

		// Load publication model
		$this->_pub = new \Components\Publications\Models\Publication($pid, null, $vid);

		if (!$this->_pub->exists())
		{
			if ($ajax)
			{
				$this->view
					->set('title', $this->title)
					->setError(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_LOAD'))
					->setName('error')
					->setLayout('default')
					->display();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_LOAD'), 404);
		}

		if (!$this->_pub->access('curator'))
		{
			if ($ajax)
			{
				$this->view
					->set('title', $this->title)
					->setError(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'))
					->setName('error')
					->setLayout('default')
					->display();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
		}

		// Perform assignment
		if ($confirm)
		{
			$previousOwner = $this->_pub->version->get('curator');
			$selected = Request::getInt('selected', 0);

			// Make sure owner profile exists
			if ($owner)
			{
				$ownerProfile  = User::getInstance($owner);
				if (!$ownerProfile)
				{
					$this->setError(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_ASSIGN_PROFILE'));
				}
			}
			elseif ($selected && Request::getString('owner', ''))
			{
				$owner = $selected;
			}

			// Assign
			if (!$this->getError())
			{
				$this->_pub->version->set('curator', $owner);
				if (!$this->_pub->version->store())
				{
					$this->setError(Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_FAILED'));
				}
				// Notify curator
				if ($owner && $owner != $previousOwner)
				{
					$item  =  '"' . html_entity_decode($this->_pub->version->title).'"';
					$item .= ' v.' . $this->_pub->version->version_label . ' ';
					$item  = htmlentities($item, ENT_QUOTES, "UTF-8");

					$message = Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_ASSIGNED') . ' ' . $item . "\n" . "\n";
					$message.= Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_ASSIGNED_CURATE') . ' ' . rtrim(Request::base(), DS) . DS . trim(Route::url($this->_pub->link('curate')), DS) . "\n" . "\n";
					$message.= Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_ASSIGNED_PREVIEW') . ' ' . rtrim(Request::base(), DS) . DS . trim(Route::url($this->_pub->link('version')), DS);

					Helpers\Html::notify(
						$this->_pub,
						array($owner),
						Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_ASSIGNED_SUBJECT'),
						$message
					);
				}
				// Log assignment in history
				if (!$this->getError() && $owner != $previousOwner)
				{
					$obj = new Tables\CurationHistory($this->database);
					if (!empty($ownerProfile))
					{
						$changelog = '<p>Curation assigned to ' . $ownerProfile->get('name') . ' (' . $ownerProfile->get('username') . ')</p>';
					}
					else
					{
						$changelog = '<p>Curator assignment was removed</p>';
					}

					// Create new record
					$obj->publication_version_id = $this->_pub->version->id;
					$obj->created    = Date::toSql();
					$obj->created_by = User::get('id');
					$obj->changelog  = $changelog;
					$obj->curator    = 1;
					$obj->newstatus  = $this->_pub->version->state;
					$obj->oldstatus  = $this->_pub->version->state;
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
				$this->view->css('curation.css', 'plg_projects_publications');
			}

			$this->view
				->set('pub', $this->_pub)
				->set('title', $this->_title)
				->set('option', $this->_option)
				->set('ajax', $ajax)
				->display();
			return;
		}

		if ($err = $this->getError())
		{
			Notify::error($err);
		}
		else
		{
			Notify::success(Lang::txt('COM_PUBLICATIONS_CURATION_SUCCESS_ASSIGNED'));
		}

		// Redirect to main listing
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Approve publication
	 *
	 * @return  void
	 */
	public function approveTask()
	{
		// Incoming
		$pid = $this->_id ? $this->_id : Request::getInt('id', 0);
		$vid = Request::getInt('vid', 0);

		// Load publication model
		$this->_pub = new \Components\Publications\Models\Publication($pid, null, $vid);

		if (!$this->_pub->exists())
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
		}

		// Check authorization
		if (!$this->_pub->access('curator'))
		{
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
		}

		$this->_pub->version->set('state', 1);
		$this->_pub->version->set('accepted', Date::toSql());
		$this->_pub->version->set('reviewed', Date::toSql());
		$this->_pub->version->set('reviewed_by', User::get('id'));

		// Archive (mkAIP) if no grace period and not previously archived
		if (!$this->getError() && !$this->config->get('graceperiod', 0)
			&& $this->_pub->version->doi
			&& \Components\Publications\Helpers\Utilities::mkAip($this->_pub->version)
			&& !$this->_pub->archived()
		)
		{
			$this->_pub->version->set('archived', Date::toSql());
		}

		// Set curation
		$this->_pub->setCuration();

		$curation = json_encode($this->_pub->_curationModel->_manifest);
		//$curation = $this->_pub->masterType()->curation;

		// Get manifest version
		$versionNumber = $this->_pub->_curationModel->checkCurationVersion();

		// Store curation manifest
		$this->_pub->version->set('curation', $curation);
		$this->_pub->version->set('curation_version_id', $versionNumber);

		if (!$this->_pub->version->store())
		{
			throw new Exception(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_FAILED'), 403);
		}

		// Get DOI service
		$doiService = new \Components\Publications\Models\Doi($this->_pub);

		$updateDoiMetadata = ($this->_pub->version->doi ? preg_match("/" . $doiService->configs()->shoulder . "/", $this->_pub->version->doi) : false);

		if ($updateDoiMetadata)
		{
			$doiService->update($this->_pub->version->doi, true);

			if ($doiService->getError())
			{
				throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UPDATE_METADATA') . ' ' . $doiService->getError(), 403);
			}

			// Register URL and DOI name for DataCite DOI service
			$doiService->register(false, true, $this->_pub->version->doi);

			if ($doiService->getError())
			{
				throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_REGISTER_URL') . ' ' . $doiService->getError(), 403);
			}
		}

		// Mark as curated
		$this->_pub->saveParam('curated', 1);

		// On after status change
		$this->onAfterStatusChange();
		
		// Add publication to author's ORCID record
		$authors = $this->_pub->authors();
		foreach ($authors as $author)
		{
			if (!empty($author->user_id))
			{
				$profile = \Components\Members\Models\Member::oneOrFail($author->user_id);
				
				if ($profile)
				{
					$orcidID = $profile->get('orcid');
					$accessToken = $profile->get('access_token');
					
					if (!empty($orcidID) && !empty($accessToken))
					{
						$putCode = $this->_pub->addPubToORCID($orcidID, $accessToken);
						
						if ($putCode)
						{
							$authorTbl = new Tables\Author($this->database);
							$authorTbl->saveORCIDPutCode($author->id, $putCode);
						}
					}
				}
			}
			else
			{
				$collaborator = $this->_pub->getCollaboratorByName($author->invited_name);
				
				if (!empty($collaborator))
				{
					$orcidID = $collaborator->orcid;
					$accessToken = $collaborator->access_token;
					
					if (!empty($orcidID) && !empty($accessToken))
					{
						$putCode = $this->_pub->addPubToORCID($orcidID, $accessToken);
						
						if (!empty($putCode))
						{
							$authorTbl = new Tables\Author($this->database);
							$authorTbl->saveORCIDPutCode($author->id, $putCode);
						}
					}
				}
				else
				{
					if (!empty($author->invited_email))
					{
						// Send email that includes ORCID permission link to collaborator's email address
						$subject = Lang::txt('COM_PUBLICATIONS_GRANT_ORCID_MANAGEMENT_PERMISSION');
						$message = Lang::txt('COM_PUBLICATIONS_GRANT_ORCID_EMAIL_MESSAGE');
						
						$config = Component::params('com_members');
						$srv = $config->get('orcid_service', 'members');
						$clientID = $config->get('orcid_' . $srv . '_client_id', '');
						$redirectURI = $config->get('orcid_' . $srv . '_permission_uri', '');
						
						if (!empty($srv) && !empty($clientID) && !empty($redirectURI))
						{
							$permissionURL = "https://";
							
							if ($config->get('orcid_service', 'members') == 'sandbox')
							{
								$permissionURL .= 'sandbox.';
							}
							
							$permissionURL .= 'orcid.org/oauth/authorize?client_id=' . $clientID . htmlspecialchars('&') . "response_type=code" . htmlspecialchars('&') . "scope=/read-limited%20/activities/update%20/person/update&redirect_uri=" . urlencode($redirectURI);
							
							$params = Component::params('com_publications');
							$address = $params->get('curatorreplyto');
							$from = array();
							$from['name']  = Config::get('sitename') . ' ' . Lang::txt('COM_PUBLICATIONS');
							
							if (empty($address))
							{
								$from['email'] = Config::get('mailfrom');
							}
							else
							{
								$from['email'] = $address;
							}
							
							$eview = new \Hubzero\Mail\View(array(
								'base_path' => dirname(__DIR__),
								'name'      => 'emails',
								'layout'    => '_html'
							));

							$eview->publication = $this->_pub;
							$eview->message     = $message;
							$eview->subject     = $subject;
							$eview->permissionURL = $permissionURL;
							$eview->permissionTxt = Lang::txt('COM_PUBLICATIONS_GRANT_ORCID_MANAGEMENT_PERMISSION');

							$body = [];
							$body['multipart'] = $eview->loadTemplate();
							$body['multipart'] = str_replace("\n", "\r\n", $body['multipart']);
							
							$mail = new \Hubzero\Mail\Message();
							$mail->setSubject($subject)
								->addTo($author->invited_email, $author->invited_name)
								->addFrom($from['email'], $from['name'])
								->setPriority('normal')
								->addPart($body['multipart'], 'text/html');
							$mail->send();
						}
					}
				}
			}
		}

		if ($err = $this->getError())
		{
			Notify::error($err);
		}
		else
		{
			Notify::success(Lang::txt('COM_PUBLICATIONS_CURATION_SUCCESS_APPROVED'));
		}

		// Redirect to main listing
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Kick back to developers
	 *
	 * @return  void
	 */
	public function kickbackTask()
	{
		// Incoming
		$pid = $this->_id ? $this->_id : Request::getInt('id', 0);
		$vid = Request::getInt('vid', 0);

		// Load publication model
		$this->_pub = new \Components\Publications\Models\Publication($pid, null, $vid);

		if (!$this->_pub->exists())
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_NOT_FOUND'), 404);
		}

		// Check authorization
		if (!$this->_pub->access('curator'))
		{
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_CURATION_LOGIN');
				$this->_login();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED'), 403);
		}

		// Change publication status
		$this->_pub->version->set('state', 7); // pending author changes
		$this->_pub->version->set('reviewed', Date::toSql());
		$this->_pub->version->set('reviewed_by', User::get('id'));

		if (!$this->_pub->version->store())
		{
			throw new Exception(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_FAILED'), 403);
		}

		// Set curation
		$this->_pub->setCuration();

		// On after status change
		$this->onAfterStatusChange();

		Notify::success(Lang::txt('COM_PUBLICATIONS_CURATION_SUCCESS_KICKBACK'));

		// Redirect to main listing
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Save review for curation item (AJAX)
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Incoming
		$pid    = $this->_id ? $this->_id : Request::getInt('id', 0);
		$vid    = Request::getInt('vid', 0);
		$props  = Request::getString('p', '');
		$pass   = Request::getInt('pass', 0);
		$action = $pass ? 'pass' : 'fail';
		$review = Request::getString('review', '');

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = (isset($parts[0])) ? $parts[0] : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 0;

		if (!$block || !$step)
		{
			echo json_encode(array(
				'success' => 0,
				'error'   => Lang::txt('Error parsing publication manifest')
			));
			return;
		}

		if ($action == 'fail' && !$review)
		{
			echo json_encode(array(
				'success' => 0,
				'error'   => Lang::txt('Please explain why the item requires changes')
			));
			return;
		}

		// Load publication model
		$this->_pub = new \Components\Publications\Models\Publication($pid, null, $vid);

		// If publication not found, raise error
		if (!$this->_pub)
		{
			echo json_encode(array(
				'success' => 0,
				'error'   => Lang::txt('Error loading publication')
			));
			return;
		}

		// Check authorization
		if (!$this->_pub->access('curator'))
		{
			echo json_encode(array(
				'success' => 0,
				'error'  => Lang::txt('COM_PUBLICATIONS_CURATION_ERROR_UNAUTHORIZED')
			));
			return;
		}

		// Set curation model
		$this->_pub->setCuration();

		$data = new stdClass;
		$data->reviewed      = Date::toSql();
		$data->reviewed_by   = User::get('id');
		$data->review_status = $action == 'pass' ? 1 : 2;
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
		if ($this->_pub->_curationModel->saveUpdate($data, $element, $block, $this->_pub, $step))
		{
			echo json_encode(array(
				'success' => 1,
				'error'   => $this->getError(),
				'notice'  => $notice
			));
		}
		else
		{
			echo json_encode(array(
				'success' => 0,
				'error'   => Lang::txt('There was a problem saving curation item'),
				'notice'  => ''
			));
		}
	}

	/**
	 * On after approve/kickback
	 *
	 * @return  void
	 */
	public function onAfterStatusChange()
	{
		if ($this->getError())
		{
			return;
		}
		$pub    = $this->_pub;
		$status = $this->_pub->version->state;

		$activity = $status == 1
					? Lang::txt('COM_PUBLICATIONS_CURATION_ACTIVITY_PUBLISHED')
					: Lang::txt('COM_PUBLICATIONS_CURATION_ACTIVITY_KICKBACK');

		$pubtitle = \Hubzero\Utility\Str::truncate($pub->title, 100);

		// Create SFTP accessible symlink for package
		if ($status == 1 && !$this->_pub->isEmbargoed())
		{
			$pub->_curationModel->createSymLink();
		}

		// Log activity in curation history
		$pub->_curationModel->saveHistory(User::get('id'), $pub->state, $status, 1);

		// Add activity
		$activity .= ' ' . strtolower(Lang::txt('version')) . ' ' . $pub->version_label . ' '
		. Lang::txt('COM_PUBLICATIONS_OF') . ' ' . strtolower(Lang::txt('publication')) . ' "'
		. $pubtitle . '" ';

		// Record activity
		$aid = $pub->project()->recordActivity(
			$activity,
			$pub->id,
			$pubtitle,
			$pub->link('version'),
			'publication',
			0,
			$admin = 1
		);

		// Start message
		$sef     = 'publications' . DS . $pub->id . DS . $pub->version_number;
		$link    = rtrim(Request::base(), DS) . DS . trim(Route::url($pub->link('version')), DS);
		$manage  = rtrim(Request::base(), DS) . DS . trim(Route::url($pub->link('editversion')), DS);
		$message = $status == 1 ? Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_CURATOR_APPROVED', $link) : Lang::txt('COM_PUBLICATIONS_CURATION_EMAIL_CURATOR_KICKED_BACK');

		if ($status != 1)
		{
			$message .= "\n" . "\n";
			$message .= Lang::txt('COM_PUBLICATIONS_CURATION_TAKE_ACTION') . ' ' . $manage;
		}

		$pubtitle = \Hubzero\Utility\Str::truncate($pub->title, 100);
		$subject  = ucfirst(Lang::txt('COM_PUBLICATIONS_CURATION_VERSION'))
					. ' ' . $pub->version_label . ' ' . Lang::txt('COM_PUBLICATIONS_OF') . ' '
					. strtolower(Lang::txt('COM_PUBLICATIONS_PUBLICATION'))
					. ' "' . $pubtitle . '" ';
		$subject .= $status == 1
			? Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_PUBLISHED')
			: Lang::txt('COM_PUBLICATIONS_MSG_ADMIN_KICKED_BACK');

		// Get authors
		$authors = $pub->table('Author')->getAuthors($pub->version_id, 1, 1, 1);

		// No authors – send to publication creator
		if (count($authors) == 0)
		{
			$authors = array($pub->created_by);
		}

		// New version released?
		if ($status == 1 && $pub->get('version_number') > 1)
		{
			// Notify subscribers
			Event::trigger('publications.onWatch', array($pub));
		}

		// Make sure there are no duplicates
		$authors = array_unique($authors);

		// Notify authors
		Helpers\Html::notify(
			$pub,
			$authors,
			$subject,
			$message,
			true
		);
	}

	/**
	 * Check user access
	 *
	 * @param   array    $curatorgroups
	 * @param   integer  $curator
	 * @return  mixed    False if no access, string if has access
	 */
	protected function _authorize($curatorgroups = array(), $curator = 0)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		$authorized = false;

		// Check if they're a site admin
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

		if (!empty($curatorgroups))
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
	 * @return  void
	 */
	protected function _login()
	{
		$rtrn = Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task), 'server');

		Notify::warning($this->_msg);

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn), false)
		);
	}
}
