<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Site\Controllers;

use Hubzero\Session\Helper as SessionHelper;
use Hubzero\Component\SiteController;
use Component;
use Document;
use Pathway;
use Request;
use Config;
use Route;
use Cache;
use Lang;
use User;
use Date;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'registration.php');

/**
 * Members controller class for profiles
 */
class Profiles extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Get the view
		$this->_view = strtolower(Request::getVar('view', 'members'));

		// Get The task
		$task = strtolower(Request::getVar('task', ''));

		$id = Request::getInt('id', 0);
		if ($id && !$task)
		{
			Request::setVar('task', 'view');
		}

		//$this->registerTask('__default', 'browse');
		$this->registerTask('promo-opt-out', 'incremOptOut');

		parent::execute();
	}

	/**
	 * Opt out of a promotion
	 *
	 * @return     void
	 */
	public function incremOptOutTask()
	{
		$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
		if (!$profile)
		{
			return;
		}

		require_once dirname(dirname(__DIR__)) . '/tables/incremental/awards.php';
		require_once dirname(dirname(__DIR__)) . '/tables/incremental/groups.php';
		require_once dirname(dirname(__DIR__)) . '/tables/incremental/options.php';

		$ia = new \ModIncrementalRegistrationAwards($profile);
		$ia->optOut();

		App::redirect(
			Route::url($profile->getLink() . '&active=profile'),
			Lang::txt('You have been successfully opted out of this promotion.'),
			'passed'
		);
		return;
	}

	/**
	 * Return results for autocompleter
	 *
	 * @return     string JSON
	 */
	public function autocompleteTask()
	{
		if (User::isGuest())
		{
			return;
		}

		$restrict = '';

		$referrer = Request::getVar('HTTP_REFERER', NULL, 'server');
		if ($referrer && preg_match('/members\/\d+\/messages/i', $referrer))
		{
			if (!User::authorise('core.admin', $this->_option)
			 && !User::authorise('core.manage', $this->_option))
			{
				switch ($this->config->get('user_messaging'))
				{
					case 2:
						$restrict = " AND xp.public=1";
					break;

					case 1:
					default:
						$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
						$xgroups = $profile->getGroups('all');
						$usersgroups = array();
						if (!empty($xgroups))
						{
							foreach ($xgroups as $group)
							{
								if ($group->regconfirmed)
								{
									$usersgroups[] = $group->gidNumber;
								}
							}
						}

						$members = null;
						if (!empty($usersgroups))
						{
							$query = "SELECT DISTINCT uidNumber
									FROM `#__xgroups_members`
									WHERE gidNumber IN (" . implode(',', $usersgroups) . ")";

							$this->database->setQuery($query);
							$members = $this->database->loadColumn();
						}

						if (!$members || empty($members))
						{
							$members = array(User::get('id'));
						}

						$restrict = " AND xp.uidNumber IN (" . implode(',', $members) . ")";
					break;
				}
			}
		}

		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = strtolower(trim(Request::getString('value', '')));
		$originalQuery = $filters['search'];

		// match against orcid id
		if (preg_match('/\d{4}-\d{4}-\d{4}-\d{4}/', $filters['search']))
		{
			$query = "SELECT xp.uidNumber, xp.name, xp.username, xp.organization, xp.picture, xp.public
					FROM #__xprofiles AS xp
					INNER JOIN #__users u ON u.id = xp.uidNumber AND u.block = 0
					WHERE orcid= " . $this->database->quote($filters['search']) . " AND xp.emailConfirmed>0 $restrict
					ORDER BY xp.name ASC
					LIMIT " . $filters['start'] . "," . $filters['limit'];
		}
		else
		{
			// add trailing wildcard
			$filters['search'] = $filters['search'] . '*';

			// match member names on all three name parts
			$match = "MATCH(xp.givenName,xp.middleName,xp.surname) AGAINST(" . $this->database->quote($filters['search']) . " IN BOOLEAN MODE)";
			$query = "SELECT xp.uidNumber, xp.name, xp.username, xp.organization, xp.picture, xp.public, $match as rel
					FROM #__xprofiles AS xp
					INNER JOIN #__users u ON u.id = xp.uidNumber AND u.block = 0
					WHERE $match AND xp.emailConfirmed>0 $restrict
					ORDER BY rel DESC, xp.name ASC
					LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0)
		{
			$default = DS . trim($this->config->get('defaultpic', '/core/components/com_members/site/assets/img/profile.gif'), DS);
			if ($default == '/components/com_members/assets/img/profile.gif')
			{
				$default = '/core/components/com_members/site/assets/img/profile.gif';
			}
			$default = \Hubzero\User\Profile\Helper::thumbit($default);
			foreach ($rows as $row)
			{
				$picture = $default;

				$name = str_replace("\n", '', stripslashes(trim($row->name)));
				$name = str_replace("\r", '', $name);
				$name = str_replace('\\', '', $name);

				if ($row->public && $row->picture)
				{
					$thumb  = DS . trim($this->config->get('webpath', '/site/members'), DS);
					$thumb .= DS . \Hubzero\User\Profile\Helper::niceidformat($row->uidNumber);
					$thumb .= DS . ltrim($row->picture, DS);
					$thumb = \Hubzero\User\Profile\Helper::thumbit($thumb);

					if (file_exists(PATH_APP . $thumb))
					{
						$picture = substr(PATH_APP, strlen(PATH_ROOT)) . $thumb;
					}
				}

				$obj = array();
				$obj['id']      = $row->uidNumber;
				$obj['name']    = $name;
				$obj['org']     = ($row->public ? $row->organization : '');
				$obj['picture'] = $picture;

				$json[] = $obj;
			}
		}

		// formats names in the autocompleter
		if (!\Hubzero\Utility\Validate::email($originalQuery) && str_word_count($originalQuery) >= 2)
		{
			$originalQuery = ucwords($originalQuery);
		}


		//original query
		$obj = array();
		$obj['name'] = $originalQuery;
		$obj['id'] = $originalQuery;
		$obj['org'] = '';
		$obj['picture'] = '';
		$obj['orig'] = true;

		//add back original query
		array_unshift($json, $obj);

		echo json_encode($json);
	}

	/**
	 * Display main page
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->title = Lang::txt('MEMBERS');

		$this->view->contribution_counting = $this->config->get('contribution_counting', true);

		// Set the page title
		Document::setTitle($this->view->title);

		// Set the document pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Display a list of members
	 *
	 * @return     void
	 */
	public function browseTask()
	{
		$this->view->contribution_counting = $this->config->get('contribution_counting', true);

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']  = Request::getVar('limit', Config::get('list_limit'), 'request');
		$this->view->filters['start']  = Request::getInt('limitstart', 0, 'get');
		$this->view->filters['show']   = strtolower(Request::getWord('show', $this->_view));
		$this->view->filters['sortby'] = strtolower(Request::getWord('sortby', 'name'));
		$this->view->filters['search'] = Request::getVar('search', '');
		$this->view->filters['index']  = Request::getWord('index', '');

		if ($this->view->contribution_counting == false)
		{
			if ($this->view->filters['show'] = 'contributors')
			{
				$this->view->filters['show'] = 'members';
			}

			if ($this->view->filters['sortby'] == 'contributions')
			{
				$this->view->filters['sortby'] = 'name';
			}
		}
		else
		{
			$this->view->filters['contributions'] = 0;
		}

		// Build the page title
		if ($this->view->filters['show'] == 'contributors')
		{
			$this->view->title = Lang::txt('CONTRIBUTORS');
			$this->view->filters['sortby'] = strtolower(Request::getWord('sortby', 'contributions'));
		}
		else
		{
			$this->view->title = Lang::txt('MEMBERS');
		}
		$this->view->title .= ($this->_task) ? ': ' . Lang::txt(strtoupper($this->_task)) : '';

		if (!in_array($this->view->filters['sortby'], array('name', 'organization', 'contributions')))
		{
			$this->view->filters['sortby'] = ($this->view->filters['show'] == 'contributors') ? 'contributions' : 'name';
		}

		// Set the page title
		Document::setTitle($this->view->title);

		// Set the document pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		// Was a specific index (letter) set?
		if ($this->view->filters['index'])
		{
			// Add to the pathway
			Pathway::append(
				strtoupper($this->view->filters['index']),
				'index.php?option=' . $this->_option . '&index=' . $this->view->filters['index']
			);
		}

		// Check authorization
		$this->view->authorized = $this->_authorize();
		if ($this->view->authorized === 'admin')
		{
			$admin = true;
		}
		else
		{
			$admin = false;
		}

		$this->view->filters['authorized']     = $this->view->authorized;
		$this->view->filters['emailConfirmed'] = true;

		// Initiate a contributor object
		$c = new \Components\Members\Tables\Profile($this->database);

		if (!($stats = Cache::get('members.stats')))
		{
			$stats = $this->stats();

			Cache::put('members.stats', $stats, intval($this->config->get('cache_time', 15)));
		}

		// Get record count of ALL members
		$this->view->total_members = $stats->total_members; //$c->getCount(array('show' => ''), true);

		// Get record count of ALL members
		$this->view->total_public_members = $stats->total_public_members; //$c->getCount(array('show' => '', 'authorized' => false), false);

		// Get record count
		$this->view->total = $c->getCount($this->view->filters, $admin);

		// Get records
		$this->view->rows = $c->getRecords($this->view->filters, $admin);

		//get newly registered members (past day)
		//$this->database->setQuery("SELECT COUNT(*) FROM `#__xprofiles` WHERE registerDate > '" . Date::of(strtotime('-1 DAY'))->toSql() . "'");
		$this->view->past_day_members = $stats->past_day_members; //$this->database->loadResult();

		//get newly registered members (past month)
		//$this->database->setQuery("SELECT COUNT(*) FROM `#__xprofiles` WHERE registerDate > '" . Date::of(strtotime('-1 MONTH'))->toSql() . "'");
		$this->view->past_month_members = $stats->past_month_members; //$this->database->loadResult();

		$this->view->registration = new \Hubzero\Base\Object();
		$this->view->registration->Fullname     = $this->_registrationField('registrationFullname', 'RRRR', $this->_task);
		$this->view->registration->Organization = $this->_registrationField('registrationOrganization', 'HOOO', $this->_task);

		// Instantiate the view
		$this->view->config = $this->config;
		$this->view->view = $this->_view;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Calculate stats
	 *
	 * @return     object
	 */
	public function stats()
	{
		$c = new \Components\Members\Tables\Profile($this->database);

		$stats = new \stdClass;

		// Get record count of ALL members
		$stats->total_members = $c->getCount(array('show' => ''), true);

		// Get record count of ALL members
		$stats->total_public_members = $c->getCount(array('show' => '', 'authorized' => false), false);

		//get newly registered members (past day)
		$this->database->setQuery("SELECT COUNT(*) FROM `#__xprofiles` WHERE registerDate > '" . Date::of(strtotime('-1 DAY'))->toSql() . "'");
		$stats->past_day_members = $this->database->loadResult();

		//get newly registered members (past month)
		$this->database->setQuery("SELECT COUNT(*) FROM `#__xprofiles` WHERE registerDate > '" . Date::of(strtotime('-1 MONTH'))->toSql() . "'");
		$stats->past_month_members = $this->database->loadResult();

		return $stats;
	}

	/**
	 * A shortcut task for displaying a logged-in user's account page
	 *
	 * @return     void
	 */
	public function myaccountTask()
	{
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->_option . '&task=myaccount'))),
				Lang::txt('You must be a logged in to access this area.'),
				'warning'
			);
			return;
		}

		Request::setVar('id', User::get('id'));
		$this->viewTask();
		return;
	}

	/**
	 * Display a user profile
	 *
	 * @return     void
	 */
	public function viewTask()
	{
		$this->view->setLayout('view');

		// Build the page title
		$this->view->title  = Lang::txt(strtoupper($this->_name));
		$this->view->title .= ($this->_task) ? ': ' . Lang::txt(strtoupper($this->_task)) : '';

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}

		// Incoming
		$id  = Request::getVar('id', 0);
		$tab = Request::getVar('active', 'dashboard');  // The active tab (section)

		// Ensure we have an ID
		if (!$id || !is_numeric($id))
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
			App::abort(404, Lang::txt('MEMBERS_NO_ID'));
			return;
		}

		$id = intval($id);

		// Check administrative access
		$this->view->authorized = $this->_authorize($id);

		// Get the member's info
		$profile = \Hubzero\User\Profile::getInstance($id);

		// Ensure we have a member
		if (!is_object($profile) || (!$profile->get('name') && !$profile->get('surname')))
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
			App::abort(404, Lang::txt('MEMBERS_NOT_FOUND'));
			return;
		}

		// Check subscription to Employer Services
		//   NOTE: This must occur after the initial plugins import and
		//   do not specifically call Plugin::import('members', 'resume');
		//   Doing so can have negative affects.
		if ($this->config->get('employeraccess') && $tab == 'resume')
		{
			$checkemp   = Event::trigger('members.isEmployer', array());
			$emp        = is_array($checkemp) ? $checkemp[0] : 0;
			$this->view->authorized = $emp ? 1 : $this->view->authorized;
		}

		// Check if the profile is public/private and the user has access
		if ($profile->get('public') != 1 && !$this->view->authorized)
		{
			// Check if they're logged in
			if (User::isGuest())
			{
				$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task . '&id=' . $profile->get('uidNumber')), 'server');
				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
				);
				return;
			}
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
			App::abort(403, Lang::txt('MEMBERS_NOT_PUBLIC'));
			return;
		}

		// check if unconfirmed
		if ($profile->get('emailConfirmed') < 1 && !$this->view->authorized)
		{
			App::abort(403, Lang::txt('MEMBERS_NOT_CONFIRMED'));
			return;
		}

		// Check for name
		if (!$profile->get('name'))
		{
			$name  = $profile->get('givenName') . ' ';
			$name .= ($profile->get('middleName')) ? $profile->get('middleName') . ' ' : '';
			$name .= $profile->get('surname');
			$profile->set('name', $name);
		}

		// Trigger the functions that return the areas we'll be using
		$this->view->cats = Event::trigger('members.onMembersAreas', array(User::getInstance(), $profile));

		$available = array();
		foreach ($this->view->cats as $cat)
		{
			$name = key($cat);
			if ($name != '')
			{
				$available[] = $name;
			}
		}

		if ($tab != 'profile' && !in_array($tab, $available))
		{
			$tab = 'profile';
		}

		// Get the sections
		$this->view->sections = Event::trigger('members.onMembers', array(User::getInstance(), $profile, $this->_option, array($tab)));

		// Merge profile params (take precendence) with the site config
		//  ** What is this for?
		$rparams = new \Hubzero\Config\Registry($profile->get('params'));
		$params = $this->config;
		$params->merge($rparams);

		// Set the page title
		Document::setTitle($this->view->title . ': ' . stripslashes($profile->get('name')));

		// Set the pathway
		Pathway::append(
			stripslashes($profile->get('name')),
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber')
		);

		// Output HTML
		$this->view->config = $this->config;
		$this->view->tab = $tab;
		$this->view->profile = $profile;
		$this->view->overwrite_content = '';

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Show a form for changing user password
	 *
	 * @return     void
	 */
	public function changepasswordTask()
	{
		if (!isset( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == 'off')
		{
			App::redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		// Set the page title
		$title  = Lang::txt(strtoupper($this->_name));
		$title .= ($this->_task) ? ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task)) : '';

		Document::setTitle($title);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Check if they're logged in
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_controller . '&task=changepassword'), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
			);
			return;
		}

		if (!$id)
		{
			$id = User::get('id');
		}

		// Ensure we have an ID
		if (!$id)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(404, Lang::txt('MEMBERS_NO_ID'));
			return;
		}

		// Check authorization
		$authorized = $this->_authorize($id);
		if (!$authorized)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(403, Lang::txt('MEMBERS_NOT_AUTH'));
			return;
		}

		// Initiate profile class
		$profile = \Hubzero\User\Profile::getInstance($id);

		// Ensure we have a member
		if (!$profile || !$profile->get('name'))
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(404, Lang::txt('MEMBERS_NOT_FOUND'));
			return;
		}

		// Add to the pathway
		Pathway::append(
			stripslashes($profile->get('name')),
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber')
		);
		Pathway::append(
			Lang::txt('COM_MEMBERS_' . strtoupper($this->_task)),
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber') . '&task=' . $this->_task
		);

		// Load some needed libraries
		if (\Hubzero\User\Helper::isXDomainUser(User::get('id')))
		{
			App::abort(403, Lang::txt('MEMBERS_PASS_CHANGE_LINKED_ACCOUNT'));
			return;
		}

		// Incoming data
		$change   = Request::getVar('change', '', 'post');
		$oldpass  = Request::getVar('oldpass', '', 'post');
		$newpass  = Request::getVar('newpass', '', 'post');
		$newpass2 = Request::getVar('newpass2', '', 'post');
		$message  = Request::getVar('message', '');

		if (!empty($message))
		{
			$this->setError($message);
		}

		$this->view->title = $title;
		$this->view->profile = $profile;
		$this->view->change = $change;
		$this->view->oldpass = $oldpass;
		$this->view->newpass = $newpass;
		$this->view->newpass2 = $newpass2;
		$this->view->validated = true;

		$password_rules = \Hubzero\Password\Rule::getRules();

		$this->view->password_rules = array();

		foreach ($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$this->view->password_rules[] = $rule['description'];
			}
		}

		if (!empty($newpass))
		{
			$msg = \Hubzero\Password\Rule::validate($newpass, $password_rules, $profile->get('username'));
		}
		else
		{
			$msg = array();
		}

		// Blank form request (no data submitted)
		if (empty($change))
		{
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			$this->view->display();
			return;
		}

		$passrules = false;

		if (!\Hubzero\User\Password::passwordMatches($profile->get('uidNumber'), $oldpass, true))
		{
			$this->setError(Lang::txt('COM_MEMBERS_PASS_INCORRECT'));
		}
		elseif (!$newpass || !$newpass2)
		{
			$this->setError(Lang::txt('COM_MEMBERS_PASS_MUST_BE_ENTERED_TWICE'));
		}
		elseif ($newpass != $newpass2)
		{
			$this->setError(Lang::txt('COM_MEMBERS_PASS_NEW_CONFIRMATION_MISMATCH'));
		}
		elseif ($oldpass == $newpass)
		{
			// make sure the current password and new password are not the same
			// this should really be done in the password rules validation step
			$this->setError(Lang::txt('Your new password must be different from your current password'));
		}
		elseif (!empty($msg))
		{
			$this->setError(Lang::txt('Password does not meet site password requirements. Please choose a password meeting all the requirements listed below.'));
			$this->view->validated = $msg;
			$passrules = true;
		}

		if ($this->getError())
		{
			$change = array();
			$change['_missing']['password'] = $this->getError();

			if (!empty($msg) && $passrules)
			{
				$change['_missing']['password'] .= '<ul>';
				foreach ($msg as $m)
				{
					$change['_missing']['password'] .= '<li>';
					$change['_missing']['password'] .= $m;
					$change['_missing']['password'] .= '</li>';
				}
				$change['_missing']['password'] .= '</ul>';
			}

			if (Request::getInt('no_html', 0))
			{
				echo json_encode($change);
				exit();
			}
			else
			{
				$this->view->setError($this->getError());
				$this->view->display();
				return;
			}
		}

		// Encrypt the password and update the profile
		$result = \Hubzero\User\Password::changePassword($profile->get('uidNumber'), $newpass);

		// Save the changes
		if (!$result)
		{
			$this->view->setError(Lang::txt('MEMBERS_PASS_CHANGE_FAILED'));
			$this->view->display();
			return;
		}

		// Redirect user back to main account page
		$return = base64_decode(Request::getVar('return', '',  'method', 'base64'));
		$this->_redirect = $return ? $return : Route::url('index.php?option=' . $this->_option . '&id=' . $id);
		$session = App::get('session');

		// Redirect user back to main account page
		if (Request::getInt('no_html', 0))
		{
			if ($session->get('badpassword','0') || $session->get('expiredpassword','0'))
			{
				$session->set('badpassword','0');
				$session->set('expiredpassword','0');
			}

			echo json_encode(array("success" => true));
			exit();
		}
		else
		{
			if ($session->get('badpassword','0') || $session->get('expiredpassword','0'))
			{
				$session->set('badpassword','0');
				$session->set('expiredpassword','0');
			}
		}
	}

	/**
	 * Show a form for raising a user's allowed sessions, storage, etc.
	 *
	 * @return     void
	 */
	public function raiselimitTask()
	{
		// Set the page title
		$this->view->title  = Lang::txt(strtoupper($this->_name));
		$this->view->title .= ($this->_task) ? ': ' . Lang::txt(strtoupper($this->_task)) : '';

		Document::setTitle($this->view->title);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Check if they're logged in
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_controller . '&task=raiselimit'), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
			);
			return;
		}

		// Ensure we have an ID
		if (!$id)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(404, Lang::txt('MEMBERS_NO_ID'));
			return;
		}

		// Check authorization
		$this->view->authorized = $this->_authorize($id);
		if (!$this->view->authorized)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(403, Lang::txt('MEMBERS_NOT_AUTH'));
			return;
		}

		// Initiate profile class
		$profile = \Hubzero\User\Profile::getInstance($id);

		// Ensure we have a member
		if (!$profile->get('name'))
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(404, Lang::txt('MEMBERS_NOT_FOUND'));
			return;
		}

		$this->view->profile = $profile;

		// Add to the pathway
		Pathway::append(
			stripslashes($profile->get('name')),
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber')
		);
		Pathway::append(
			Lang::txt(strtoupper($this->_task)),
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber') . '&task=' . $this->_task
		);

		// Incoming
		$request = Request::getVar('request', null, 'post');
		$raiselimit = Request::getVar('raiselimit', null, 'post');

		if ($raiselimit)
		{
			$k = '';
			if (is_array($raiselimit))
			{
				$k = key($raiselimit);
			}

			switch ($k)
			{
				case 'sessions':
					include_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'preferences.php');

					$preferences = new \Components\Tools\Tables\Preferences($this->database);
					$preferences->loadByUser($profile->get('uidNumber'));
					if (!$preferences || !$preferences->id)
					{
						$default = $preferences->find('one', array('alias' => 'default'));
						$preferences->user_id  = $profile->get('uidNumber');
						$preferences->class_id = $default->id;
						$preferences->jobs     = $default->jobs;
						$preferences->store();
					}

					$oldlimit = $preferences->jobs;
					$newlimit = $oldlimit + 3;

					$resourcemessage = 'session limit from '. $oldlimit .' to '. $newlimit .' sessions ';

					if ($this->view->authorized == 'admin')
					{
						$preferences->class_id = 0;
						$preferences->jobs     = $newlimit;
						$preferences->store();
						$resourcemessage = 'The session limit for [' . $profile->get('username') . '] has been raised from ' . $oldlimit . ' to ' . $newlimit . ' sessions.';
					}
					else if ($request === null)
					{
						$this->view->resource = $k;
						$this->view->setLayout('select');
						$this->view->display();
						return;
					}
				break;

				case 'storage':
					$oldlimit = 'unknown'; // $profile->get('quota');
					$newlimit = 'unknown'; // $profile->get('quota') + 100;

					$resourcemessage = ' storage limit has been raised from '. $oldlimit .' to '. $newlimit .'.';

					if ($this->view->authorized == 'admin')
					{
						// $profile->set('quota', $newlimit);
						// $profile->update();

						$resourcemessage = 'The storage limit for [' . $profile->get('username') . '] has been raised from '. $oldlimit .' to '. $newlimit .'.';
					}
					else
					{
						$this->view->resource = $k;
						$this->view->setLayout('select');
						$this->view->display();
						return;
					}
				break;

				case 'meetings':
					$oldlimit = 'unknown'; // $profile->get('max_meetings');
					$newlimit = 'unknown'; // $profile->get('max_meetings') + 3;

					$resourcemessage = ' meeting limit has been raised from '. $oldlimit .' to '. $newlimit .'.';

					if ($this->view->authorized == 'admin')
					{
						// $profile->set('max_meetings', $newlimit);
						// $profile->update();

						$resourcemessage = 'The meeting limit for [' . $profile->get('username') . '] has been raised from '. $oldlimit .' to '. $newlimit .'.';
					}
					else
					{
						$this->view->resource = $k;
						$this->view->setLayout('select');
						$this->view->display();
						return;
					}
				break;

				default:
					// Show limit selection form
					$this->view->display();
					return;
				break;
			}
		}

		// Do we need to email admin?
		if ($request !== null && !empty($resourcemessage))
		{
			$sitename =  Config::get('sitename');
			$live_site = rtrim(Request::base(),'/');

			// Email subject
			$subject = $hubName . " Account Resource Request";

			// Email message
			$message = 'Name: ' . $profile->get('name');
			if ($profile->get('organization'))
			{
				$message .= " / " . $profile->get('organization');
			}
			$message .= "\r\n";
			$message .= "Email: " . $profile->get('email') . "\r\n";
			$message .= "Username: " . $profile->get('username') . "\r\n\r\n";
			$message .= 'Has requested an increases in their ' . $hubName;
			$message .= $resourcemessage . "\r\n\r\n";
			$message .= "Reason: ";
			if (empty($request))
			{
				$message .= "NONE GIVEN\r\n\r\n";
			}
			else
			{
				$message .= $request . "\r\n\r\n";
			}
			$message .= "Click the following link to grant this request:\r\n";

			$sef = Route::url('index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber') . '&task=' . $this->_task);
			$url = Request::base() . ltrim($sef, DS);

			$message .= $url . "\r\n\r\n";
			$message .= "Click the following link to review this user's account:\r\n";

			$sef = Route::url('index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber'));
			$url = Request::base() . ltrim($sef, DS);

			$message .= $url . "\r\n";

			$msg = new \Hubzero\Mail\Message();
			$msg->setSubject($subject)
			    ->addTo(Config::get('mailfrom'))
			    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
			    ->addHeader('X-Component', $this->_option)
			    ->setBody($message);

			// Send an e-mail to admin
			if (!$msg->send())
			{
				return App::abort(500, 'xHUB Internal Error: Error mailing resource request to site administrator(s).');
			}

			// Output the view
			$this->view->resourcemessage = $resourcemessage;
			$this->view->setLayout('success');
			$this->view->display();
			return;
		}
		else if ($this->view->authorized == 'admin' && !empty($resourcemessage))
		{
			// Output the view
			$this->view->resourcemessage = $resourcemessage;
			$this->view->setLayout('success');
			$this->view->display();
			return;
		}

		// Output the view
		$this->view->resource = null;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Show a form for editing a profile
	 *
	 * @param      object $xregistration \Components\Members\Models\Registration
	 * @param      object $profile       \Hubzero\User\Profile
	 * @return     void
	 */
	public function editTask($xregistration=null, $profile=null)
	{
		// Set the page title
		$this->view->title  = Lang::txt(strtoupper($this->_name));
		$this->view->title .= ($this->_task) ? ': ' . Lang::txt(strtoupper($this->_task)) : '';

		Document::setTitle($this->view->title);

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Check if they're logged in
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_controller . '&task=activity'), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
			);
			return;
		}

		// Ensure we have an ID
		if (!$id)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(404, Lang::txt('MEMBERS_NO_ID'));
			return;
		}
		// Check authorization
		$this->view->authorized = $this->_authorize($id);
		if ($id != User::get('id'))
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(403, Lang::txt('MEMBERS_NOT_AUTH'));
			return;
		}

		// Initiate profile class if we don't already have one and load info
		// Note: if we already have one then we just came from $this->save()
		if (!is_object($profile))
		{
			$profile = \Hubzero\User\Profile::getInstance($id);
		}

		// Ensure we have a member
		if (!$profile->get('name') && !$profile->get('surname'))
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&id=' . $id . '&task=' . $this->_task
			);
			App::abort(404, Lang::txt('MEMBERS_NOT_FOUND'));
			return;
		}

		// Get the user's interests (tags)
		$mt = new \Components\Members\Models\Tags($id);
		$this->view->tags = $mt->render('string');

		// Add to the pathway
		Pathway::append(
			stripslashes($profile->get('name')),
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber')
		);
		Pathway::append(
			Lang::txt(strtoupper($this->_task)),
			'index.php?option=' . $this->_option . '&id=' . $profile->get('uidNumber') . '&task=' . $this->_task
		);

		// Instantiate an xregistration object if we don't already have one
		// Note: if we already have one then we just came from $this->save()
		if (!is_object($xregistration))
		{
			$xregistration = new \Components\Members\Models\Registration();
		}
		$this->view->xregistration = $xregistration;

		// Find out which fields are hidden, optional, or required
		$registration = new \Hubzero\Base\Object();
		$registration->Username        = $this->_registrationField('registrationUsername', 'RROO', $this->_task);
		$registration->Password        = $this->_registrationField('registrationPassword', 'RRHH', $this->_task);
		$registration->ConfirmPassword = $this->_registrationField('registrationConfirmPassword', 'RRHH', $this->_task);
		$registration->Fullname        = $this->_registrationField('registrationFullname', 'RRRR', $this->_task);
		$registration->Email           = $this->_registrationField('registrationEmail', 'RRRR', $this->_task);
		$registration->ConfirmEmail    = $this->_registrationField('registrationConfirmEmail', 'RRRR', $this->_task);
		$registration->URL             = $this->_registrationField('registrationURL', 'HHHH', $this->_task);
		$registration->Phone           = $this->_registrationField('registrationPhone', 'HHHH', $this->_task);
		$registration->Employment      = $this->_registrationField('registrationEmployment', 'HHHH', $this->_task);
		$registration->Organization    = $this->_registrationField('registrationOrganization', 'HHHH', $this->_task);
		$registration->Citizenship     = $this->_registrationField('registrationCitizenship', 'HHHH', $this->_task);
		$registration->Residency       = $this->_registrationField('registrationResidency', 'HHHH', $this->_task);
		$registration->Sex             = $this->_registrationField('registrationSex', 'HHHH', $this->_task);
		$registration->Disability      = $this->_registrationField('registrationDisability', 'HHHH', $this->_task);
		$registration->Hispanic        = $this->_registrationField('registrationHispanic', 'HHHH', $this->_task);
		$registration->Race            = $this->_registrationField('registrationRace', 'HHHH', $this->_task);
		$registration->Interests       = $this->_registrationField('registrationInterests', 'HHHH', $this->_task);
		$registration->Reason          = $this->_registrationField('registrationReason', 'HHHH', $this->_task);
		$registration->OptIn           = $this->_registrationField('registrationOptIn', 'HHHH', $this->_task);
		$registration->TOU             = $this->_registrationField('registrationTOU', 'HHHH', $this->_task);
		$registration->ORCID           = $this->_registrationField('registrationORCID', 'OOOO', $this->_task);

		// Ouput HTML
		$this->view->profile = $profile;
		$this->view->registration = $registration;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Get the settings for a particular field
	 *  H = hidden
	 *  O = optional
	 *  R = required
	 *  U = read only
	 *
	 * @param      string $name    Field name
	 * @param      string $default Default setting
	 * @param      string $task    Task being executed
	 * @return     integer
	 */
	private function _registrationField($name, $default, $task = 'create')
	{
		switch ($task)
		{
			case 'register':
			case 'create': $index = 0; break;
			case 'proxy':  $index = 1; break;
			case 'update': $index = 2; break;
			case 'edit':   $index = 3; break;
			default:       $index = 0; break;
		}

		$hconfig = Component::params('com_members');

		$default = str_pad($default, 4, '-');
		$configured = $hconfig->get($name);
		if (empty($configured))
		{
			$configured = $default;
		}
		$length = strlen($configured);
		if ($length > $index)
		{
			$value = substr($configured, $index, 1);
		}
		else
		{
			$value = substr($default, $index, 1);
		}

		switch ($value)
		{
			case 'R': return(REG_REQUIRED);
			case 'O': return(REG_OPTIONAL);
			case 'H': return(REG_HIDE);
			case '-': return(REG_HIDE);
			case 'U': return(REG_READONLY);
			default : return(REG_HIDE);
		}
	}

	/**
	 * Save changes to a profile
	 * Outputs JSON when called via AJAX, redirects to profile otherwise
	 *
	 * @return     string JSON
	 */
	public function saveTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		Request::checkToken(array('get', 'post'));

		$no_html = Request::getVar("no_html", 0);

		// Incoming user ID
		$id = Request::getInt('id', 0, 'post');

		// Do we have an ID?
		if (!$id)
		{
			App::abort(404, Lang::txt('MEMBERS_NO_ID'));
			return;
		}

		// Incoming profile edits
		$p = Request::getVar('profile', array(), 'post', 'none', 2);
		$n = Request::getVar('name', array(), 'post');
		$a = Request::getVar('access', array(), 'post');

		// Load the profile
		$profile = \Hubzero\User\Profile::getInstance($id);

		$oldemail = $profile->get('email');

		if ($n)
		{
			$profile->set('givenName', trim($n['first']));
			$profile->set('middleName', trim($n['middle']));
			$profile->set('surname', trim($n['last']));
			$name  = trim($n['first']) . ' ';
			$name .= (trim($n['middle']) != '') ? trim($n['middle']) . ' ' : '';
			$name .= trim($n['last']);
			$profile->set('name', $name);
		}

		if (isset($p['bio']))
		{
			$profile->set('bio', trim($p['bio']));
		}

		if (is_array($a) && count($a) > 0)
		{
			foreach ($a as $k => $v)
			{
				$v = intval($v);
				if (!in_array($v, array(0, 1, 2, 3, 4)))
				{
					$v = 0;
				}
				$profile->setParam('access_' . $k, $v);
			}
		}

		if (isset($p['public']))
		{
			$profile->set('public', $p['public']);
		}

		// Set some post data for the xregistration class
		$tags = trim(Request::getVar('tags',''));
		if (isset($tags))
		{
			Request::setVar('interests', $tags, 'post');
		}

		// Instantiate a new \Components\Members\Models\Registration
		$xregistration = new \Components\Members\Models\Registration();
		$xregistration->loadPOST();

		// Push the posted data to the profile
		// Note: this is done before the required fields check so, if we need to display the edit form, it'll show all the new changes
		if (!is_null($xregistration->_registration['email']))
		{
			$profile->set('email', $xregistration->_registration['email']);

			// Unconfirm if the email address changed
			if ($oldemail != $xregistration->_registration['email'])
			{
				// Get a new confirmation code
				$confirm = \Components\Members\Helpers\Utility::genemailconfirm();

				$profile->set('emailConfirmed', $confirm);
			}
		}

		if (!is_null($xregistration->_registration['countryresident']))
		{
			$profile->set('countryresident', $xregistration->_registration['countryresident']);
		}

		if (!is_null($xregistration->_registration['countryorigin']))
		{
			$profile->set('countryorigin', $xregistration->_registration['countryorigin']);
		}

		if (!is_null($xregistration->_registration['nativetribe']))
		{
			$profile->set('nativeTribe', $xregistration->_registration['nativetribe']);
		}

		if ($xregistration->_registration['org'] != '')
		{
			$profile->set('organization', $xregistration->_registration['org']);
		}
		elseif ($xregistration->_registration['orgtext'] != '')
		{
			$profile->set('organization', $xregistration->_registration['orgtext']);
		}

		if (!is_null($xregistration->_registration['web']))
		{
			$profile->set('url', $xregistration->_registration['web']);
		}

		if (!is_null($xregistration->_registration['phone']))
		{
			$profile->set('phone', $xregistration->_registration['phone']);
		}

		if (!is_null($xregistration->_registration['orgtype']))
		{
			$profile->set('orgtype', $xregistration->_registration['orgtype']);
		}

		if (!is_null($xregistration->_registration['sex']))
		{
			$profile->set('gender', $xregistration->_registration['sex']);
		}

		if (!is_null($xregistration->_registration['disability']))
		{
			$profile->set('disability', $xregistration->_registration['disability']);
		}

		if (!is_null($xregistration->_registration['hispanic']))
		{
			$profile->set('hispanic', $xregistration->_registration['hispanic']);
		}

		if (!is_null($xregistration->_registration['race']))
		{
			$profile->set('race', $xregistration->_registration['race']);
		}

		if (!is_null($xregistration->_registration['mailPreferenceOption']))
		{
			$profile->set('mailPreferenceOption', $xregistration->_registration['mailPreferenceOption']);
		}

		if (!is_null($xregistration->_registration['usageAgreement']))
		{
			$profile->set('usageAgreement', $xregistration->_registration['usageAgreement']);
		}

		if (!is_null($xregistration->_registration['orcid']))
		{
			$profile->set('orcid', $xregistration->_registration['orcid']);
		}

		$field_to_check = Request::getVar("field_to_check", array());

		// Check that required fields were filled in properly
		if (!$xregistration->check('edit', $profile->get('uidNumber'), $field_to_check))
		{
			if (!$no_html)
			{
				$this->_task = 'edit';
				$this->editTask($xregistration, $profile);
				return;
			}
			else
			{
				echo json_encode($xregistration);
				exit();
			}
		}

		//are we declining the terms of use
		//if yes we want to set the usage agreement to 0 and profile to private
		$declineTOU = Request::getVar('declinetou', 0);
		if ($declineTOU)
		{
			$profile->set('public', 0);
			$profile->set('usageAgreement', 0);
		}

		// Set the last modified datetime
		$profile->set('modifiedDate', Date::toSql());

		// Save the changes
		if (!$profile->update())
		{
			App::abort(500, $profile->getError());
			return false;
		}

		// Process tags
		if (isset($tags) && in_array('interests', $field_to_check))
		{
			$mt = new \Components\Members\Models\Tags($id);
			$mt->setTags($tags, $id);
		}

		$email = $profile->get('email');
		$name  = $profile->get('name');

		// Make sure certain changes make it back to the user table
		if ($id > 0)
		{
			$user  = User::getInstance($id);
			$jname  = $user->get('name');
			$jemail = $user->get('email');
			if ($name != trim($jname))
			{
				$user->set('name', $name);
			}
			if ($email != trim($jemail))
			{
				$user->set('email', $email);
			}
			if ($name != trim($jname) || $email != trim($jemail))
			{
				if (!$user->save())
				{
					App::abort(500, Lang::txt($user->getError()));
					return false;
				}
			}

			// Update session if name is changing
			if ($n && $user->get('name') != App::get('session')->get('user')->get('name'))
			{
				$suser = App::get('session')->get('user');
				$user->set('name', $suser->get('name'));
			}

			// Update session if email is changing
			if ($user->get('email') != App::get('session')->get('user')->get('email'))
			{
				$suser = App::get('session')->get('user');
				$user->set('email', $suser->get('email'));

				// add item to session to mark that the user changed emails
				// this way we can serve profile images for these users but not all
				// unconfirmed users
				$session = App::get('session');
				$session->set('userchangedemail', 1);
			}
		}

		// Send a new confirmation code AFTER we've successfully saved the changes to the e-mail address
		if ($email != $oldemail)
		{
			$this->_message = $this->_sendConfirmationCode($profile->get('username'), $email, $confirm);
		}

		//if were declinging the terms we want to logout user and tell the javascript
		if ($declineTOU)
		{
			App::get('auth')->logout();
			echo json_encode(array('loggedout' => true));
			return;
		}

		if (!$no_html)
		{
			// Redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . ($id ? '&id=' . $id . '&active=profile' : '')),
				$this->_message
			);
		}
		else
		{
			// Output JSON
			echo json_encode(array('success' => true));
		}
	}

	/**
	 * Send a confirmation code to a user's email address
	 *
	 * @param      strong $login   Username
	 * @param      string $email   User email address
	 * @param      string $confirm Confirmation code
	 * @return     string
	 */
	private function _sendConfirmationCode($login, $email, $confirm)
	{
		// Email subject
		$subject = Config::get('sitename') .' account email confirmation';

		// Email message
		$eview = new \Hubzero\Component\View(array(
			'name'   => 'emails',
			'layout' => 'confirm'
		));
		$eview->option   = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->login    = $login;
		$eview->confirm  = $confirm;
		$eview->baseURL  = Request::base();

		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		$msg = new \Hubzero\Mail\Message();
		$msg->setSubject($subject)
		    ->addTo($email)
		    ->addFrom(Config::get('mailfrom'), Config::get('sitename') . ' Administrator')
		    ->addHeader('X-Component', $this->_option)
		    ->setBody($message);

		// Send the email
		if ($msg->send())
		{
			$msg = 'A confirmation email has been sent to "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'". You must click the link in that email to re-activate your account.';
		}
		else
		{
			$msg = 'An error occurred emailing "'. htmlentities($email,ENT_COMPAT,'UTF-8') .'" your confirmation.';
		}

		return $msg;
	}

	/**
	 * Save profile field access
	 *
	 * @return     void
	 */
	public function saveaccessTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		// Incoming user ID
		$id = Request::getInt('id', 0);

		// Do we have an ID?
		if (!$id)
		{
			App::abort(404, Lang::txt('MEMBERS_NO_ID'));
			return;
		}

		// Incoming profile edits
		$p = Request::getVar('access', array(), 'post');
		if (is_array($p))
		{
			// Load the profile
			$profile = \Hubzero\User\Profile::getInstance($id);

			foreach ($p as $k => $v)
			{
				$v = intval($v);
				if (!in_array($v, array(0, 1, 2, 3, 4)))
				{
					$v = 0;
				}
				$profile->setParam('access_' . $k, $v);
			}

			// Save the changes
			if (!$profile->update())
			{
				\Notify::warning($profile->getError());
				return false;
			}
		}

		// Push through to the profile view
		$this->viewTask();
	}

	/**
	 * Show the current user activity
	 *
	 * @return     void
	 */
	public function activityTask()
	{
		// Set the page title
		Document::setTitle(Lang::txt(strtoupper($this->_name)) . ': ' . Lang::txt(strtoupper($this->_task)));

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		Pathway::append(
			Lang::txt(strtoupper($this->_task)),
			'index.php?option=' . $this->_option . '&task=' . $this->_task
		);

		// Check if they're logged in
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_controller . '&task=activity'), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
			);
			return;
		}
		if (!User::authorize($this->_option, 'manage'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		// Get logged-in users
		$prevuser = '';
		$user     = array();
		$users    = array();
		$guests   = array();

		// get sessions
		$result = SessionHelper::getAllSessions(array(
			'guest' => 0
		));

		if ($result && count($result) > 0)
		{
			foreach ($result as $row)
			{
				$row->idle = time() - $row->time;
				if ($prevuser != $row->username)
				{
					if ($user)
					{
						$xprofile = \Hubzero\User\Profile::getInstance($prevuser);

						$users[$prevuser] = $user;
						$users[$prevuser]['uidNumber'] = $xprofile->get('uidNumber');
						$users[$prevuser]['name'] = $xprofile->get('name');
						$users[$prevuser]['org'] = $xprofile->get('organization');
						$users[$prevuser]['orgtype'] = $xprofile->get('orgtype');
						$users[$prevuser]['countryresident'] = $xprofile->get('countryresident');
					}
					$prevuser = $row->username;
					$user = array();
				}
				array_push($user, array('ip' => $row->ip, 'idle' => $row->idle));
			}
			if ($user)
			{
				$xprofile = \Hubzero\User\Profile::getInstance($prevuser);

				$users[$prevuser] = $user;
				$users[$prevuser]['uidNumber'] = $xprofile->get('uidNumber');
				$users[$prevuser]['name'] = $xprofile->get('name');
				$users[$prevuser]['org'] = $xprofile->get('organization');
				$users[$prevuser]['orgtype'] = $xprofile->get('orgtype');
				$users[$prevuser]['countryresident'] = $xprofile->get('countryresident');
			}
		}

		// get sessions
		$result = SessionHelper::getAllSessions(array(
			'guest' => 1
		));

		if (count($result) > 0)
		{
			foreach ($result as $row)
			{
				$row->idle = time() - $row->time;
				array_push($guests, array('ip' => $row->ip, 'idle' => $row->idle));
			}
		}

		// Output View
		$this->view->title = Lang::txt('Active Users and Guests');
		$this->view->users = $users;
		$this->view->guests = $guests;

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Cancel a task and redirect to profile
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&id=' . $id . '&active=profile')
		);
	}

	/**
	 * Method to check admin access permission
	 *
	 * @param      integer $uid       User ID
	 * @param      string  $assetType Asset type
	 * @param      string  $assetId   Asset ID
	 * @return     boolean True on success
	 */
	protected function _authorize($uid=0, $assetType='component', $assetId=null)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		// Check if they're a site admin
		// Admin
		$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $assetId));
		$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $assetId));

		if ($this->config->get('access-admin-' . $assetType))
		{
			return 'admin';
		}

		// Check if they're the member
		if (is_numeric($uid))
		{
			if (User::get('id') == $uid)
			{
				return true;
			}
		}
		else
		{
			if (User::get('username') == $uid)
			{
				return true;
			}
		}

		return false;
	}
}

