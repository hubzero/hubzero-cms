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

namespace Components\Members\Admin\Controllers;

use Components\Members\Helpers;
use Hubzero\Component\AdminController;
use Hubzero\Utility\Validate;
use Hubzero\User\Profile;
use Filesystem;
use Request;
use Config;
use Route;
use User;
use Date;
use Lang;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'registration.php');

/**
 * Manage site members
 */
class Members extends AdminController
{
	/**
	 * Display a list of site members
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'search_field' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search_field',
				'search_field',
				'name'
			)),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'registerDate'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			),
			'registerDate' => Request::getState(
				$this->_option . '.' . $this->_controller . '.registerDate',
				'registerDate',
				''
			),
			'emailConfirmed' => Request::getState(
				$this->_option . '.' . $this->_controller . '.emailConfirmed',
				'emailConfirmed',
				0,
				'int'
			),
			'public' => Request::getState(
				$this->_option . '.' . $this->_controller . '.public',
				'public',
				-1,
				'int'
			),
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$this->view->filters['sortby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$obj = new \Components\Members\Tables\Profile($this->database);

		// Get a record count
		$this->view->total = $obj->getRecordCount($this->view->filters, true);

		// Get records
		$this->view->rows = $obj->getRecordEntries($this->view->filters, true);

		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new member
	 *
	 * @return     void
	 */
	public function addTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			\Notify::error($error);
		}

		// Output the HTML
		$this->view
			->setLayout('add')
			->display();
	}

	/**
	 * Create a new user
	 *
	 * @param      integer $redirect Redirect to main listing?
	 * @return     void
	 */
	public function newTask($redirect=1)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming profile edits
		$p = Request::getVar('profile', array(), 'post', 'none', 2);

		// Initialize new usertype setting
		$usersConfig = \Component::params('com_users');
		$newUsertype = $usersConfig->get('new_usertype');
		if (!$newUsertype)
		{
			$db = \App::get('db');
			$query = $db->getQuery(true)
				->select('id')
				->from('#__usergroups')
				->where('title = "Registered"');
			$db->setQuery($query);
			$newUsertype = $db->loadResult();
		}

		// check that username & password are filled
		if (!Validate::username($p['username']))
		{
			$this->setError(Lang::txt('COM_MEMBERS_MEMBER_USERNAME_INVALID'));
			$this->addTask();
			return;
		}

		// check email is valid
		if (!Validate::email($p['email']))
		{
			$this->setError(Lang::txt('COM_MEMBERS_MEMBER_EMAIL_INVALID'));
			$this->addTask();
			return;
		}

		$name  = trim($p['givenName']).' ';
		$name .= (trim($p['middleName']) != '') ? trim($p['middleName']).' ' : '';
		$name .= trim($p['surname']);

		$user = new \JUser();
		$user->set('username', trim($p['username']));
		$user->set('name', $name);
		$user->set('email', trim($p['email']));
		$user->set('id', 0);
		$user->set('groups', array($newUsertype));
		$user->set('registerDate', Date::toSql());
		$user->set('password', trim($p['password']));
		$user->set('password_clear', trim($p['password']));
		$user->save();
		$user->set('password_clear', '');

		// Attempt to get the new user
		$profile = Profile::getInstance($user->get('id'));
		$result  = is_object($profile);

		// Did we successfully create an account?
		if ($result)
		{
			// Set the new info
			$profile->set('givenName', trim($p['givenName']));
			$profile->set('middleName', trim($p['middleName']));
			$profile->set('surname', trim($p['surname']));
			$profile->set('name', $name);
			$profile->set('emailConfirmed', -rand(1, pow(2, 31)-1));
			$profile->set('public', 0);
			$profile->set('password', '');
			$result = $profile->store();
		}

		if ($result)
		{
			$result = \Hubzero\User\Password::changePassword($profile->get('uidNumber'), $p['password']);
			// Set password back here in case anything else down the line is looking for it
			$profile->set('password', $p['password']);
			$profile->store();
		}

		// Did we successfully create/update an account?
		if (!$result)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$user->getError(),
				'error'
			);
			return;
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option='.$this->_option.'&controller='.$this->_controller.'&task=edit&id[]='.$profile->get('uidNumber'), false),
			Lang::txt('COM_MEMBERS_MEMBER_SAVED')
		);
	}

	/**
	 * Edit a member's information
	 *
	 * @param      integer $id ID of member to edit
	 * @return     void
	 */
	public function editTask($id=0)
	{
		Request::setVar('hidemainmenu', 1);

		if (!$id)
		{
			// Incoming
			$id = Request::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}
		}

		// Initiate database class and load info
		$this->view->profile = new Profile();
		$this->view->profile->load($id);

		$this->view->password = \Hubzero\User\Password::getInstance($id);

		// Get password rules
		$password_rules = \Hubzero\Password\Rule::getRules();

		// Get the password rule descriptions
		$this->view->password_rules = array();
		foreach ($password_rules as $rule)
		{
			if (!empty($rule['description']))
			{
				$this->view->password_rules[] = $rule['description'];
			}
		}

		// Validate the password
		$this->view->validated = (isset($this->validated)) ? $this->validated : false;

		// Get the user's interests (tags)
		include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'tags.php');

		$mt = new \Components\Members\Models\Tags($id);
		$this->view->tags = $mt->render('string');

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry and return to edit form
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(0);
	}

	/**
	 * Save an entry and return to main listing
	 *
	 * @param      integer $redirect Redirect to main listing?
	 * @return     void
	 */
	public function saveTask($redirect=1)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming user ID
		$id = Request::getInt('id', 0, 'post');

		// Do we have an ID?
		if (!$id)
		{
			App::abort(500, Lang::txt('COM_MEMBERS_NO_ID'));
			return;
		}

		// Incoming profile edits
		$p = Request::getVar('profile', array(), 'post', 'none', 2);

		// Load the profile
		$profile = new Profile();
		$profile->load($id);

		// Set the new info
		$profile->set('givenName', preg_replace('/\s+/', ' ', trim($p['givenName'])));
		$profile->set('middleName', preg_replace('/\s+/', ' ', trim($p['middleName'])));
		$profile->set('surname', preg_replace('/\s+/', ' ', trim($p['surname'])));

		$name  = trim($p['givenName']).' ';
		$name .= (trim($p['middleName']) != '') ? trim($p['middleName']).' ' : '';
		$name .= trim($p['surname']);
		$name  = preg_replace('/\s+/', ' ', $name);

		$profile->set('name', $name);
		if (isset($p['vip']))
		{
			$profile->set('vip',$p['vip']);
		}
		else
		{
			$profile->set('vip',0);
		}
		$profile->set('usageAgreement', 0);
		if (isset($p['usageAgreement']))
		{
			$profile->set('usageAgreement',$p['usageAgreement']);
		}
		$profile->set('orcid', trim($p['orcid']));
		$profile->set('url', trim($p['url']));
		$profile->set('phone', trim($p['phone']));
		$profile->set('orgtype', trim($p['orgtype']));
		$profile->set('organization', trim($p['organization']));
		$profile->set('bio', trim($p['bio']));
		if (isset($p['public']))
		{
			$profile->set('public',$p['public']);
		}
		else
		{
			$profile->set('public',0);
		}
		$profile->set('modifiedDate', Date::toSql());

		$profile->set('homeDirectory', trim($p['homeDirectory']));

		$profile->set('loginShell', trim($p['loginShell']));

		$ec = Request::getInt('emailConfirmed', 0, 'post');
		if ($ec)
		{
			$profile->set('emailConfirmed', $ec);
		}
		else
		{
			$confirm = Helpers\Utility::genemailconfirm();
			$profile->set('emailConfirmed', $confirm);
		}

		if (isset($p['email']))
		{
			$profile->set('email', trim($p['email']));
		}
		if (isset($p['mailPreferenceOption']))
		{
			$profile->set('mailPreferenceOption', trim($p['mailPreferenceOption']));
		}
		else
		{
			$profile->set('mailPreferenceOption', -1);
		}

		if (!empty($p['gender']))
		{
			$profile->set('gender', trim($p['gender']));
		}

		if (!empty($p['disability']))
		{
			if ($p['disability'] == 'yes')
			{
				if (!is_array($p['disabilities']))
				{
					$p['disabilities'] = array();
				}
				if (count($p['disabilities']) == 1
				 && isset($p['disabilities']['other'])
				 && empty($p['disabilities']['other']))
				{
					$profile->set('disability',array('no'));
				}
				else
				{
					$profile->set('disability',$p['disabilities']);
				}
			}
			else
			{
				$profile->set('disability',array($p['disability']));
			}
		}

		if (!empty($p['hispanic']))
		{
			if ($p['hispanic'] == 'yes')
			{
				if (!is_array($p['hispanics']))
				{
					$p['hispanics'] = array();
				}
				if (count($p['hispanics']) == 1
				 && isset($p['hispanics']['other'])
				 && empty($p['hispanics']['other']))
				{
					$profile->set('hispanic', array('no'));
				}
				else
				{
					$profile->set('hispanic',$p['hispanics']);
				}
			}
			else
			{
				$profile->set('hispanic',array($p['hispanic']));
			}
		}

		if (isset($p['race']) && is_array($p['race']))
		{
			$profile->set('race',$p['race']);
		}

		// Save the changes
		if (!$profile->update())
		{
			App::abort(500, $profile->getError());
			return false;
		}

		// Do we have a new pass?
		$newpass = trim(Request::getVar('newpass', '', 'post'));
		if ($newpass != '')
		{
			// Get password rules and validate
			$password_rules = \Hubzero\Password\Rule::getRules();
			$validated      = \Hubzero\Password\Rule::validate($newpass, $password_rules, $profile->get('uidNumber'));

			if (!empty($validated))
			{
				// Set error
				$this->setError(Lang::txt('COM_MEMBERS_PASSWORD_DOES_NOT_MEET_REQUIREMENTS'));
				$this->validated = $validated;
				$redirect = false;
			}
			else
			{
				// Save password
				\Hubzero\User\Password::changePassword($profile->get('username'), $newpass);
			}
		}

		$passinfo = \Hubzero\User\Password::getInstance($id);

		if (is_object($passinfo))
		{
			// Do we have shadow info to change?
			$shadowMax     = Request::getInt('shadowMax', false, 'post');
			$shadowWarning = Request::getInt('shadowWarning', false, 'post');
			$shadowExpire  = Request::getVar('shadowExpire', '', 'post');

			if ($shadowMax || $shadowWarning || (!is_null($passinfo->get('shadowExpire')) && empty($shadowExpire)))
			{
				if ($shadowMax)
				{
					$passinfo->set('shadowMax', $shadowMax);
				}
				if ($shadowExpire || (!is_null($passinfo->get('shadowExpire')) && empty($shadowExpire)))
				{
					if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $shadowExpire))
					{
						$shadowExpire = strtotime($shadowExpire) / 86400;
						$passinfo->set('shadowExpire', $shadowExpire);
					}
					elseif (preg_match("/[0-9]+/", $shadowExpire))
					{
						$passinfo->set('shadowExpire', $shadowExpire);
					}
					elseif (empty($shadowExpire))
					{
						$passinfo->set('shadowExpire', NULL);
					}
				}
				if ($shadowWarning)
				{
					$passinfo->set('shadowWarning', $shadowWarning);
				}

				$passinfo->update();
			}
		}

		// Get the user's interests (tags)
		$tags = trim(Request::getVar('tags', ''));

		// Process tags
		include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'tags.php');

		$mt = new \Components\Members\Models\Tags($id);
		$mt->setTags($tags, $id);

		// Make sure certain changes make it back to the user table
		$user = User::getInstance($id);
		$user->set('name', $name);
		$user->set('email', $profile->get('email'));
		if (!$user->save())
		{
			App::abort('', Lang::txt($user->getError()));
			return false;
		}

		if ($redirect)
		{
			// Redirect
			App::redirect(
				Route::url('index.php?option='.$this->_option),
				Lang::txt('COM_MEMBERS_MEMBER_SAVED')
			);
		}
		else
		{
			$this->editTask($id);
		}
	}

	/**
	 * Removes a profile entry, associated picture, and redirects to main listing
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('ids', array());

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				// Delete any associated pictures
				$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . \Hubzero\Utility\String::pad($id);
				if (!file_exists($path . DS . $file) or !$file)
				{
					$this->setError(Lang::txt('COM_MEMBERS_FILE_NOT_FOUND'));
				}
				else
				{
					unlink($path . DS . $file);
				}

				// Remove any contribution associations
				$assoc = new \Components\Members\Tables\Association($this->database);
				$assoc->authorid = $id;
				$assoc->deleteAssociations();

				// Remove the profile
				$profile = new Profile();
				$profile->load($id);
				$profile->delete();
			}
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_MEMBERS_REMOVED')
		);
	}

	/**
	 * Set a member's emailConfirmed to confirmed
	 *
	 * @return     void
	 */
	public function confirmTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Set a member's emailConfirmed to unconfirmed
	 *
	 * @return     void
	 */
	public function unconfirmTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Sets the emailConfirmed state of a member
	 *
	 * @return     void
	 */
	public function stateTask($state=1)
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming user ID
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have an ID?
		if (empty($ids))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_MEMBERS_NO_ID'),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Load the profile
			$profile = new Profile();
			$profile->load(intval($id));

			if ($state)
			{
				$profile->set('emailConfirmed', $state);
			}
			else
			{
				$confirm = Helpers\Utility::genemailconfirm();
				$profile->set('emailConfirmed', $confirm);
			}

			if (!$profile->update())
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
					$profile->getError(),
					'error'
				);
				return;
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_MEMBERS_CONFIRMATION_CHANGED')
		);
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

		$filters = array();
		$filters['limit']  = 20;
		$filters['start']  = 0;
		$filters['search'] = strtolower(trim(Request::getString('value', '')));

		// Fetch results
		$query = "SELECT xp.uidNumber, xp.name, xp.username, xp.organization, xp.picture, xp.public
				FROM `#__xprofiles` AS xp
				INNER JOIN `#__users` u ON u.id = xp.uidNumber AND u.block = 0
				WHERE LOWER(xp.name) LIKE " . $this->database->quote('%' . $filters['search'] . '%') . " AND xp.emailConfirmed>0 $restrict
				ORDER BY xp.name ASC";

		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();

		$base = str_replace('/administrator', '', rtrim(Request::base(true), '/'));

		// Output search results in JSON format
		$json = array();
		if (count($rows) > 0)
		{
			$default = DS . trim($this->config->get('defaultpic', '/core/components/com_members/site/assets/img/profile.gif'), DS);
			$default = Profile\Helper::thumbit($default);
			foreach ($rows as $row)
			{
				$picture = $default;

				$name = str_replace("\n", '', stripslashes(trim($row->name)));
				$name = str_replace("\r", '', $name);
				$name = str_replace('\\', '', $name);

				if ($row->public && $row->picture)
				{
					$thumb  = $base . DS . trim($this->config->get('webpath', '/site/members'), DS);
					$thumb .= DS . Profile\Helper::niceidformat($row->uidNumber);
					$thumb .= DS . ltrim($row->picture, DS);
					$thumb = Profile\Helper::thumbit($thumb);

					if (file_exists(PATH_APP . $thumb))
					{
						$picture = $thumb;
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

		echo json_encode($json);
	}

	/**
	 * Download a picture
	 *
	 * @return  void
	 */
	public function pictureTask()
	{
		//get vars
		$id = Request::getInt('id', 0);

		//check to make sure we have an id
		if (!$id || $id == 0)
		{
			return;
		}

		//Load member profile
		$member = Profile::getInstance($id);

		// check to make sure we have member profile
		if (!$member)
		{
			return;
		}

		$file  = DS . trim($this->config->get('webpath', '/site/members'), DS);
		$file .= DS . Profile\Helper::niceidformat($member->get('uidNumber'));
		$file .= DS . Request::getVar('image', $member->get('picture'));

		// Ensure the file exist
		if (!file_exists(PATH_APP . DS . $file))
		{
			App::abort(404, Lang::txt('COM_MEMBERS_FILE_NOT_FOUND') . ' ' . $file);
			return;
		}

		// Serve up the image
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename(PATH_APP . DS . $file);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support

		//serve up file
		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(404, Lang::txt('COM_MEMBERS_MEDIA_ERROR_SERVING_FILE'));
		}
		else
		{
			exit;
		}
		return;
	}
}

