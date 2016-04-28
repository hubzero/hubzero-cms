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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for resumes
 */
class plgMembersResume extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  Event observer
	 * @param   array   $config    Optional config values
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		Lang::load('com_jobs');

		$path = Component::path('com_jobs');

		include_once($path . DS . 'tables' . DS . 'admin.php');
		include_once($path . DS . 'tables' . DS . 'application.php');
		include_once($path . DS . 'tables' . DS . 'category.php');
		include_once($path . DS . 'tables' . DS . 'employer.php');
		include_once($path . DS . 'tables' . DS . 'job.php');
		include_once($path . DS . 'tables' . DS . 'prefs.php');
		include_once($path . DS . 'tables' . DS . 'resume.php');
		include_once($path . DS . 'tables' . DS . 'seeker.php');
		include_once($path . DS . 'tables' . DS . 'shortlist.php');
		include_once($path . DS . 'tables' . DS . 'stats.php');
		include_once($path . DS . 'tables' . DS . 'type.php');

		$this->config = Component::params('com_jobs');
	}

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		// default areas returned to nothing
		$areas = array();

		// if this is the logged in user show them
		if ($user->get('id') == $member->get('id') || $this->isEmployer($user, $member))
		{
			$areas['resume'] = Lang::txt('PLG_MEMBERS_RESUME');
			$areas['icon'] = 'f016';
		}

		return $areas;
	}

	/**
	 * Check if a user has employer authorization
	 *
	 * @param      object $user       User
	 * @param      object $member     \Hubzero\User\Profile
	 * @return     integer 1 = authorized, 0 = not
	 */
	public function isEmployer($user=null, $member=null)
	{
		$database = App::get('db');
		$employer = new \Components\Jobs\Tables\Employer($database);

		// Check if they're a site admin
		if (User::authorise('core.admin', 'com_members.component'))
		{
			return 1;
		}

		// determine who is veiwing the page
		$emp = 0;
		$emp = $employer->isEmployer(User::get('id'));

		// check if they belong to a dedicated admin group
		if ($this->config->get('admingroup') && User::get('id') != 0)
		{
			$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
			$ugs = $profile->getGroups('all');
			if ($ugs && count($ugs) > 0)
			{
				foreach ($ugs as $ug)
				{
					if ($ug->cn == $this->config->get('admingroup'))
					{
						$emp = 1;
					}
				}
			}
		}

		if ($member)
		{
			$my =  $member->get('id') == User::get('id') ? 1 : 0;
			$emp = $my && $emp ? 0 : $emp;
		}

		return $emp;
	}

	/**
	 * Check if the user is part of the administration group
	 *
	 * @param   integer  $admin  Var to set
	 * @return  integer  1 = authorized, 0 = not
	 */
	public function isAdmin($admin = 0)
	{
		// check if they belong to a dedicated admin group
		if ($this->config->get('admingroup'))
		{
			$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
			$ugs = $profile->getGroups('all');
			if ($ugs && count($ugs) > 0)
			{
				foreach ($ugs as $ug)
				{
					if ($ug->cn == $this->config->get('admingroup'))
					{
						$admin = 1;
					}
				}
			}
		}

		return $admin;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @param   string  $option  Component name
	 * @param   array   $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$return = 'html';
		$active = 'resume';

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				// do nothing
			}
		}

		// The output array we're returning
		$arr = array(
			'html' => '',
			'metadata' => '',
			'searchresult' => ''
		);

		// Do we need to return any data?
		if ($return != 'html' && $return != 'metadata')
		{
			return $arr;
		}

		// Jobs component needs to be enabled
		if (!$this->config->get('component_enabled'))
		{
			$arr['html'] = '<p class="warning">' . Lang::txt('PLG_MEMBERS_RESUME_WARNING_DISABLED') . '</p>';
			return $arr;
		}

		// Get authorization
		$emp = $this->isEmployer($user, $member);

		// Are we returning HTML?
		if ($return == 'html'  && $areas[0] == 'resume')
		{
			$database = App::get('db');

			$task = Request::getVar('action','');

			switch ($task)
			{
				case 'uploadresume': $arr['html'] = $this->_upload($database, $option, $member); break;
				case 'deleteresume': $arr['html'] = $this->_deleteresume($database, $option, $member, $emp);   break;
				case 'edittitle':    $arr['html'] = $this->_view($database, $option, $member, $emp, 1);   break;
				case 'savetitle':    $arr['html'] = $this->_save($database, $option, $member, $task, $emp);   break;
				case 'saveprefs':    $arr['html'] = $this->_save($database, $option, $member, $task, $emp);   break;
				case 'editprefs':    $arr['html'] = $this->_view($database, $option, $member, $emp, 0, $editpref = 2); break;
				case 'activate':     $arr['html'] = $this->_activate($database, $option, $member, $emp); break;
				case 'download':     $arr['html'] = $this->_download($member); break;
				case 'view':
				default: $arr['html'] = $this->_view($database, $option, $member, $emp, $edittitle = 0); break;
			}
		}
		else if ($emp)
		{
			$arr['metadata'] = '';
		}

		return $arr;
	}

	/**
	 * Save data
	 *
	 * @param   object   $database  Database
	 * @param   string   $option    Component name
	 * @param   object   $member    Profile
	 * @param   string   $task      Task to perform
	 * @param   integer  $emp       Is user employer?
	 * @return  string
	 */
	protected function _save($database, $option, $member, $task, $emp)
	{
		$lookingfor = Request::getVar('lookingfor','');
		$tagline    = Request::getVar('tagline','');
		$active     = Request::getInt('activeres', 0);
		$author     = Request::getInt('author', 0);
		$title      = Request::getVar('title','');

		if ($task == 'saveprefs')
		{
			$js = new \Components\Jobs\Tables\JobSeeker($database);

			if (!$js->loadSeeker($member->get('id')))
			{
				$this->setError(Lang::txt('PLG_MEMBERS_RESUME_ERROR_PROFILE_NOT_FOUND'));
				return '';
			}

			if (!$js->bind($_POST))
			{
				echo $this->alert($js->getError());
				exit();
			}

			$js->active = $active;
			$js->updated = Date::toSql();

			if (!$js->store())
			{
				echo $this->alert($js->getError());
				exit();
			}
		}
		else if ($task == 'savetitle' && $author && $title)
		{
			$resume = new \Components\Jobs\Tables\Resume($database);
			if ($resume->loadResume($author))
			{
				$resume->title = $title;
				if (!$resume->store())
				{
					echo $this->alert($resume->getError());
					exit();
				}
			}
		}

		return $this->_view($database, $option, $member, $emp);
	}

	/**
	 * Set a user as being a 'job seeker'
	 *
	 * @param   object   $database  Database
	 * @param   string   $option    Component name
	 * @param   object   $member    Profile
	 * @param   integer  $emp       Is user employer?
	 * @return  string
	 */
	protected function _activate($database, $option, $member, $emp)
	{
		// are we activating or disactivating?
		$active = Request::getInt('on', 0);

		$js = new \Components\Jobs\Tables\JobSeeker($database);

		if (!$js->loadSeeker($member->get('id')))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_RESUME_ERROR_PROFILE_NOT_FOUND'));
			return '';
		}
		else if (!$active)
		{
			$js->active = $active;
			$js->updated = Date::toSql();

			// store new content
			if (!$js->store())
			{
				echo $js->getError();
				exit();
			}

			return $this->_view($database, $option, $member, $emp);
		}
		else
		{
			// ask to confirm/add search preferences
			return $this->_view($database, $option, $member, $emp, 0, 1);
		}
	}

	/**
	 * View user's resumes
	 *
	 * @param   object   $database   Database
	 * @param   string   $option     Component name
	 * @param   object   $member     Profile
	 * @param   integer  $emp        Is user employer?
	 * @param   integer  $edittitle  Parameter description (if any) ...
	 * @param   integer  $editpref   Parameter description (if any) ...
	 * @return  string
	 */
	protected function _view($database, $option, $member, $emp, $edittitle = 0, $editpref = 0)
	{
		$out = '';

		$self = $member->get('id') == User::get('id') ? 1 : 0;

		// get job seeker info on the user
		$js = new \Components\Jobs\Tables\JobSeeker($database);
		if (!$js->loadSeeker($member->get('id')))
		{
			// make a new entry
			$js = new \Components\Jobs\Tables\JobSeeker($database);
			$js->uid = $member->get('id');
			$js->active = 0;

			// check content
			if (!$js->check())
			{
				echo $js->getError();
				exit();
			}

			// store new content
			if (!$js->store())
			{
				echo $js->getError();
				exit();
			}
		}

		$jt = new \Components\Jobs\Tables\JobType($database);
		$jc = new \Components\Jobs\Tables\JobCategory($database);

		// get active resume
		$resume = new \Components\Jobs\Tables\Resume($database);
		$file = '';
		$path = $this->build_path($member->get('id'));

		if ($resume->loadResume($member->get('id')))
		{
			$file = PATH_APP . $path . DS . $resume->filename;
			if (!is_file($file))
			{
				$file = '';
			}
		}

		// get seeker stats
		$jobstats = new \Components\Jobs\Tables\JobStats($database);
		$stats = $jobstats->getStats($member->get('id'), 'seeker');

		$view = $this->view('default', 'resume');
		$view->self   = $self;
		$view->js     = $js;
		$view->jt     = $jt;
		$view->jc     = $jc;
		$view->resume = $resume;
		$view->file   = $file;
		$view->stats  = $stats;
		$view->config = $this->config;
		$view->member = $member;
		$view->option = $option;
		$view->edittitle = $edittitle;
		$view->emp    = $emp;
		$view->editpref = $editpref;
		$view->path = $path;
		$view->params = $this->params;

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Build the path for uploading a resume to
	 *
	 * @param   integer  $uid  User ID
	 * @return  mixed    False if errors, string otherwise
	 */
	public function build_path($uid)
	{
		// Get the configured upload path
		$base_path = $this->params->get('webpath', '/site/members');
		$base_path = DS . trim($base_path, DS);

		$dir = \Hubzero\Utility\String::pad($uid);

		$listdir = $base_path . DS . $dir;

		if (!is_dir(PATH_APP . $listdir))
		{
			if (!Filesystem::makeDirectory(PATH_APP . $listdir))
			{
				return false;
			}
		}

		// Build the path
		return $listdir;
	}

	/**
	 * Upload a resume
	 *
	 * @param   object  $database  Database
	 * @param   string  $option    Component name
	 * @param   object  $member    Profile
	 * @return  string
	 */
	protected function _upload($database, $option, $member)
	{
		$path = $this->build_path($member->get('id'));
		$emp = Request::getInt('emp', 0);

		if (!$path)
		{
			$this->setError(Lang::txt('PLG_MEMBERS_RESUME_SUPPORT_NO_UPLOAD_DIRECTORY'));
			return $this->_view($database, $option, $member, $emp);
		}

		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming file
		$file = Request::getVar('uploadres', '', 'files', 'array');

		if (!$file['name'])
		{
			$this->setError(Lang::txt('PLG_MEMBERS_RESUME_SUPPORT_NO_FILE'));
			return $this->_view($database, $option, $member, $emp);
		}

		// Incoming
		$title = Request::getVar('title', '');
		$default_title = $member->get('firstname') ? $member->get('firstname') . ' ' . $member->get('lastname') . ' ' . ucfirst(Lang::txt('PLG_MEMBERS_RESUME_RESUME')) : $member->get('name') . ' ' . ucfirst(Lang::txt('PLG_MEMBERS_RESUME_RESUME'));
		$path = PATH_APP . $path;

		// Replace file title with user name
		$file_ext = substr($file['name'], strripos($file['name'], '.'));
		$file['name']  = $member->get('firstname') ? $member->get('firstname') . ' ' . $member->get('lastname') . ' ' . ucfirst(Lang::txt('PLG_MEMBERS_RESUME_RESUME')) : $member->get('name') . ' ' . ucfirst(Lang::txt('PLG_MEMBERS_RESUME_RESUME'));
		$file['name'] .= $file_ext;

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		$ext = strtolower(Filesystem::extension($file['name']));
		if (!in_array($ext, explode(',', $this->params->get('file_ext', 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,txt,rtf,doc,docx,ppt'))))
		{
			$this->setError(Lang::txt('Disallowed file type.'));
			return $this->_view($database, $option, $member, $emp);
		}

		$row = new \Components\Jobs\Tables\Resume($database);

		if (!$row->loadResume($member->get('id')))
		{
			$row = new \Components\Jobs\Tables\Resume($database);
			$row->id   = 0;
			$row->uid  = $member->get('id');
			$row->main = 1;
		}
		else if (file_exists($path . DS . $row->filename)) // remove prev file first
		{
			Filesystem::delete($path . DS . $row->filename);

			// Remove stats for prev resume
			$jobstats = new \Components\Jobs\Tables\JobStats($database);
			$jobstats->deleteStats($member->get('id'), 'seeker');
		}

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('ERROR_UPLOADING'));
		}
		else
		{
			$fpath = $path . DS . $file['name'];

			if (!Filesystem::isSafe($fpath))
			{
				Filesystem::delete($fpath);

				$this->setError(Lang::txt('File rejected because the anti-virus scan failed.'));
				return $this->_view($database, $option, $member, $emp);
			}

			// File was uploaded, create database entry
			$title = htmlspecialchars($title);
			$row->created = Date::toSql();
			$row->filename = $file['name'];
			$row->title = $title ? $title : $default_title;

			if (!$row->check())
			{
				$this->setError($row->getError());
			}
			if (!$row->store())
			{
				$this->setError($row->getError());
			}
		}
		return $this->_view($database, $option, $member, $emp);
	}

	/**
	 * Delete a resume
	 *
	 * @param   object   $database  Database
	 * @param   string   $option    Component name
	 * @param   object   $member    Profile
	 * @param   integer  $emp       Is user employer?
	 * @return  string
	 */
	protected function _deleteresume($database, $option, $member, $emp)
	{
		$row = new \Components\Jobs\Tables\Resume($database);
		if (!$row->loadResume($member->get('id')))
		{
			$this->setError(Lang::txt('Resume ID not found.'));
			return '';
		}

		// Incoming file
		$file = $row->filename;

		$path = $this->build_path($member->get('id'));

		if (!file_exists(PATH_APP . $path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete(PATH_APP . $path . DS . $file))
			{
				$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
			}
			else
			{
				$row->delete();

				// Remove stats for prev resume
				$jobstats = new \Components\Jobs\Tables\JobStats($database);
				$jobstats->deleteStats($member->get('id'), 'seeker');

				// Do not include profile in search without a resume
				$js = new \Components\Jobs\Tables\JobSeeker($database);
				$js->loadSeeker($member->get('id'));
				$js->bind(array('active' => 0));
				if (!$js->store())
				{
					$this->setError($js->getError());
				}
			}
		}

		// Push through to the main view
		return $this->_view($database, $option, $member, $emp);
	}

	/**
	 * Show a shortlist
	 *
	 * @return  void
	 */
	public function onMembersShortlist()
	{
		$oid = Request::getInt('oid', 0);

		if ($oid)
		{
			$this->shortlist($oid, $ajax=1);
		}
	}

	/**
	 * Retrieve a shortlist for a user
	 *
	 * @param   integer  $oid   List ID
	 * @param   integer  $ajax  Being displayed via AJAX?
	 * @return  void
	 */
	public function shortlist($oid, $ajax=0)
	{
		if (!User::isGuest())
		{
			$database = App::get('db');

			$shortlist = new \Components\Jobs\Tables\Shortlist($database);
			$shortlist->loadEntry(User::get('id'), $oid, 'resume');

			if (!$shortlist->id)
			{
				$shortlist->emp      = User::get('id');
				$shortlist->seeker   = $oid;
				$shortlist->added    = Date::toSql();
				$shortlist->category = 'resume';
				$shortlist->check();
				$shortlist->store();
			}
			else
			{
				$shortlist->delete();
			}

			if ($ajax)
			{
				// get seeker info
				$js = new \Components\Jobs\Tables\JobSeeker($database);
				$seeker = $js->getSeeker($oid, User::get('id'));

				$view = $this->view('seeker', 'resume');
				$view->seeker = $seeker[0];
				$view->emp    = 1;
				$view->admin  = 0;
				$view->option = 'com_members';
				$view->list   = 1;
				$view->params = $this->params;
				$view->display();
			}
		}
	}

	/**
	 * Return javascript to generate an alert prompt
	 *
	 * @param   string  $msg  Message to show
	 * @return  string  HTML
	 */
	public function alert($msg)
	{
		return "<script type=\"text/javascript\"> alert('" . $msg . "'); window.history.go(-1); </script>\n";
	}

	/**
	 * Generate a select form
	 *
	 * @param   string  $name   Field name
	 * @param   array   $array  Data to populate select with
	 * @param   mixed   $value  Value to select
	 * @param   string  $class  Class to add
	 * @return  string  HTML
	 */
	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="' . $name . '" id="' . $name . '"';
		$out .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		foreach ($array as $avalue => $alabel)
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="' . $avalue . '"' . $selected . '>' . $alabel . '</option>' . "\n";
		}
		$out .= '</select>' . "\n";
		return $out;
	}

	/**
	 * Convert a timestamp to a more human readable string such as "3 days ago"
	 *
	 * @param   string  $date  Timestamp
	 * @return  string
	 */
	public static function nicetime($date)
	{
		if (empty($date))
		{
			return 'No date provided';
		}

		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
		$lengths = array('60', '60', '24', '7', '4.35', '12', '10');

		$now = strtotime(Date::getRoot());
		$unix_date = strtotime($date);

		// check validity of date
		if (empty($unix_date))
		{
			return Lang::txt('Bad date');
		}

		// is it future date or past date
		if ($now > $unix_date)
		{
			$difference = $now - $unix_date;
			$tense = 'ago';
		}
		else
		{
			$difference = $unix_date - $now;
			//$tense = "from now";
			$tense = '';
		}

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++)
		{
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if ($difference != 1)
		{
			$periods[$j] .= 's';
		}

		return "$difference $periods[$j] {$tense}";
	}

	/**
	 * Download a file
	 *
	 * @param   object  $member
	 * @return  void
	 */
	protected function _download($member)
	{
		$database = App::get('db');

		// Ensure we have a database object
		if (!$database)
		{
			App::abort(500, Lang::txt('DATABASE_NOT_FOUND'));
			return;
		}

		// Incoming
		$uid = $member->get('id');

		// Load the resume
		$resume = new \Components\Jobs\Tables\Resume($database);
		$file = '';
		$path = $this->build_path($uid);

		if ($resume->loadResume($uid))
		{
			$file = PATH_APP . $path . DS . $resume->filename;
		}

		if (!is_file($file))
		{
			App::abort(404, Lang::txt('FILE_NOT_FOUND'));
			return;
		}

		// Use user name as file name
		$default_title  = $member->get('givenName') ? $member->get('givenName') . ' ' . $member->get('surname') . ' ' . ucfirst(Lang::txt('Resume')) : $member->get('name') . ' ' . ucfirst(Lang::txt('Resume'));
		$default_title .= substr($resume->filename, strripos($resume->filename, '.'));

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file);

		// record view
		$stats = new \Components\Jobs\Tables\JobStats($database);
		if (User::get('id') != $uid)
		{
			$stats->saveView($uid, 'seeker');
		}

		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas(stripslashes($resume->title));
		$result = $xserver->serve_attachment($file, stripslashes($default_title), false); // @TODO fix byte range support

		if (!$result)
		{
			App::abort(500, Lang::txt('SERVER_ERROR'));
		}

		exit;
	}
}

