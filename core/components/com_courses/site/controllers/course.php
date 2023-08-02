<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site\Controllers;

use Components\Courses\Models;
use Components\Courses\Tables;
use Hubzero\Component\SiteController;
use Hubzero\Base\Obj;
use Hubzero\Content\Server;
use Exception;
use stdClass;
use Pathway;
use Request;
use Config;
use Notify;
use Route;
use Event;
use Date;
use User;
use Lang;
use App;

/**
 * Courses controller class
 */
class Course extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Load the course page
		$this->course = Models\Course::getInstance(Request::getString('gid', ''));

		$this->registerTask('edit', 'display');

		parent::execute();
	}

	/**
	 * Method to set the document path
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
		if ($this->course->exists())
		{
			Pathway::append(
				stripslashes($this->course->get('title')),
				'index.php?option=' . $this->_option . '&gid=' . $this->course->get('alias')
			);
		}
		else
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return  void
	 */
	protected function _buildTitle()
	{
		//set title used in view
		$this->_title = Lang::txt(strtoupper($this->_option));

		if ($this->course->exists())
		{
			$this->_title .= ': ' . stripslashes($this->course->get('title'));
		}
		else
		{
			if ($this->_task && $this->_task != 'intro')
			{
				$this->_title .= Lang::txt(strtoupper($this->_option . '_' . $this->_task));
			}
		}

		//set title of browser window
		\Document::setTitle($this->_title);
	}

	/**
	 * Redirect to login page
	 *
	 * @param   string  $message
	 * @return  void
	 */
	public function loginTask($message = '')
	{
		$return = base64_encode(Route::url('index.php?option=' . $this->_option . '&gid=' . $this->course->get('id') . '&task=' . $this->_task, false, true));

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . $return, false),
			$message,
			'warning'
		);
	}

	/**
	 * View a course
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (!$this->course->isPublished() && !$this->course->isDraft())
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		if (!$this->course->access('view'))
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		$this->view->active = Request::getString('active', 'overview');

		$this->view->plugins = Event::trigger(
			'courses.onCourseView',
			array(
				$this->course,
				$this->view->active
			)
		);

		if ($pages = $this->course->pages(array('active' => 1)))
		{
			foreach ($pages as $page)
			{
				$plg = with(new Obj)
					->set('name', $page->get('url'))
					->set('title', $page->get('title'));

				if ($page->get('url') == $this->view->active)
				{
					$plg->set('html', $page->content('parsed'));
					$plg->set('isPage', true);
				}

				$this->view->plugins[] = $plg;
			}
		}

		$this->view->course        = $this->course;
		$this->view->config        = $this->config;
		$this->view->notifications = Notify::messages('courses');
		$this->view->display();
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing a course
	 *
	 * @param   object  $model
	 * @return  void
	 */
	public function editTask($model=null)
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
		}

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		if (is_object($model))
		{
			$this->course = $model;
		}

		if ($this->_task != 'new')
		{
			// Ensure we found the course info
			if (!$this->course->exists())
			{
				App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
			}

			// Check authorization
			if (!$this->course->access('edit'))
			{
				App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH'));
			}

			$this->view->title = Lang::txt('COM_COURSES_EDIT_COURSE') . ': ' . $this->course->get('title');
		}
		else
		{
			$this->course->set('state', 3);

			$this->view->title = Lang::txt('COM_COURSES_NEW_COURSE');
		}

		//get directory for course file uploads
		if ($this->lid != '')
		{
			$this->view->lid = $this->lid;
		}
		elseif ($this->course->get('id'))
		{
			$this->view->lid = $this->course->get('id');
		}
		else
		{
			$this->view->lid = time() . rand(0, 1000);
		}

		// Output HTML
		$this->view->course = $this->course;

		$this->view->notifications = Notify::messages('courses');
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a course
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
		}

		// Incoming
		$data   = Request::getArray('course', array(), 'post');
		$params = Request::getArray('params', array(), 'post');

		$course = Models\Course::getInstance($data['id']);

		// Is this a new entry or updating?
		$isNew = false;
		if (!$course->exists())
		{
			$isNew = true;
		}

		// Check authorization
		if (!$isNew && !$course->access('edit', 'course'))
		{
			App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH'));
		}

		// Push back into edit mode if any errors
		if (!$course->bind($data))
		{
			$this->tags = $tags;

			Notify::error($course->getError(), 'courses');
			return $this->newTask($course);
		}

		// Force into draft state
		if ($isNew)
		{
			$course->set('state', 3);
		}

		$p = new \Hubzero\Config\Registry($params);

		$course->set('params', $p->toString());

		// Push back into edit mode if any errors
		if (!$course->store(true))
		{
			$this->tags = $tags;

			Notify::error($course->getError(), 'courses');
			return $this->editTask($course);
		}

		if (isset($_POST['tags']))
		{
			$tags = trim(Request::getString('tags', ''));
			$course->tag($tags, User::get('id'));
		}

		// Rename the temporary upload directory if it exist
		if ($isNew)
		{
			// Set the creator as a manager
			$role_id = 2;
			/*if ($roles = $course->roles())
			{
				foreach ($roles as $role)
				{
					if ($role->alias == 'manager')
					{
						$role_id = $role->id;
						break;
					}
				}
			}*/
			$course->add(User::get('id'), $role_id);
		}

		// Show success message to user
		if ($isNew)
		{
			$msg = Lang::txt('COM_COURSES_COURSE_CREATED', $course->get('title'));
		}
		else
		{
			$msg = Lang::txt('COM_COURSES_COURSE_UPDATED', $course->get('title'));
		}

		// Redirect back to the course page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&gid=' . $course->get('alias')),
			$msg
		);
	}

	/**
	 * Publish a course
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		// Incoming
		$id = Request::getCmd('gid', '');

		$course = Models\Course::getInstance($id);

		// Is this a new entry or updating?
		if (!$course->exists())
		{
			App::abort(404, Lang::txt('COM_COURSES_NOT_FOUND'));
		}

		// Check authorization
		if (!$course->access('edit', 'course'))
		{
			App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH'));
		}

		$course->set('state', 1);

		if ($course->store())
		{
			Notify::success(Lang::txt('COM_COURSES_COURSE_PUBLISHED', $course->get('title')));
		}
		else
		{
			Notify::error($course->getError());
		}

		// Redirect back to the course page
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&gid=' . $course->get('alias'))
		);
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return  void
	 */
	public function instructorsTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
		}

		$this->view
			->set('no_html', Request::getInt('no_html', 0))
			->set('course', $this->course)
			->display();
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return  void
	 */
	public function newofferingTask($offering=null)
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
		}

		$this->view->no_html = Request::getInt('no_html', 0);

		if ($offering instanceof Models\Offering)
		{
			$this->view->offering = $offering;
		}
		else
		{
			$this->view->offering = new Models\Offering(0);
		}

		$this->view->course = $this->course;

		$this->view->title = Lang::txt('COM_COURSES_NEW_OFFERING');
		$this->view->notifications = Notify::messages('courses');

		$this->view->display();
	}

	/**
	 * Show a form for editing a course
	 *
	 * @return  void
	 */
	public function saveofferingTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
		}

		$data    = Request::getArray('offering', array(), 'post');
		$no_html = Request::getInt('no_html', 0);

		$course   = Models\Course::getInstance($data['course_id']);
		$offering = Models\Offering::getInstance($data['id']);

		// Is this a new entry or updating?
		$isNew = false;
		if (!$offering->exists())
		{
			$isNew = true;
		}

		$response = new stdClass;
		$response->success = true;

		// Push back into edit mode if any errors
		if (!$offering->bind($data))
		{
			if ($no_html)
			{
				$response->message = $offering->getError();

				echo json_encode($response);
			}
			else
			{
				Notify::error($offering->getError(), 'courses');
				$this->newofferingTask($offering);
			}
			return;
		}

		// Push back into edit mode if any errors
		if (!$offering->store(true))
		{
			if ($no_html)
			{
				$response->message = $offering->getError();
			}
			else
			{
				Notify::error($offering->getError(), 'courses');
				$this->newTask($offering);
			}
			return;
		}

		$response->message = Lang::txt('COM_COURSES_OFFERING_SAVED');

		if ($no_html)
		{
			echo json_encode($response);
		}
		else
		{
			// Redirect back to the course page
			App::redirect(
				Route::url($course->link()),
				$response->message
			);
		}
	}

	/**
	 * Delete a course
	 * This method initially displays a form for confirming deletion
	 * then deletes course and associated information upon POST
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Build title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
		}

		// Ensure we found the course info
		if (!$this->course->exists())
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		// Check authorization
		if (!$this->course->access('delete'))
		{
			App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH'));
		}

		// Get number of course members
		$managers = $this->course->get('managers');

		// Incoming
		$process = Request::getString('process', '');
		$confirmdel = Request::getString('confirmdel', '');
		$msg = trim(Request::getString('msg', '', 'post'));

		// Did they confirm delete?
		if (!$process || !$confirmdel)
		{
			if ($process && !$confirmdel)
			{
				Notify::error(Lang::txt('COM_COURSES_ERROR_CONFIRM_DELETION'), 'courses');
			}

			$log = Lang::txt('COM_COURSES_MEMBERS_LOG', count($managers));

			// Trigger the functions that delete associated content
			// Should return logs of what was deleted
			$logs = Event::trigger('courses.onCourseDeleteCount', array($course));
			if (count($logs) > 0)
			{
				$log .= '<br />' . implode('<br />', $logs);
			}

			// Output HTML
			$this->view->title  = Lang::txt('COM_COURSES_DELETE_COURSE') . ': ' . $this->course->get('title');
			$this->view->course = $course;
			$this->view->log    = $log;
			$this->view->msg    = $msg;
			$this->view->notifications = Notify::messages('courses');
			$this->view->display();
			return;
		}

		$this->course->set('state', 2);

		// Delete course
		if (!$this->course->update())
		{
			$this->view->setLayout('error');
			$this->view->title = $title;
			if ($this->course->getError())
			{
				Notify::error($this->course->getError(), 'courses');
			}
			$this->view->notifications = Notify::messages('courses');
			$this->view->display();
			return;
		}

		// Get and set some vars
		$date = Date::of('now');

		// Build the "from" info for e-mails
		$from = array(
			'name'  => Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_name)),
			'email' => Config::get('mailfrom')
		);

		// E-mail subject
		$subject = Lang::txt('COM_COURSES_SUBJECT_COURSE_DELETED', $gcn);

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array(
			'name'   => 'emails',
			'layout' => 'deleted'
		));
		$eview->set('option', $this->_option);
		$eview->sitename = Config::get('sitename');
		$eview->user = User::getInstance();
		$eview->gcn = $gcn;
		$eview->msg = $msg;
		$eview->course = $deletedcourse;

		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Send the message
		if (!Event::trigger('xmessage.onSendMessage', array('courses_deleted', $subject, $message, $from, $members, $this->_option)))
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_EMAIL_MEMBERS_FAILED'));
		}

		// Log the deletion
		$xlog = new Tables\Log($this->database);
		$xlog->gid       = $this->course->get('id');
		$xlog->uid       = User::get('id');
		$xlog->timestamp = Date::toSql();
		$xlog->action    = 'course_deleted';
		$xlog->comments  = $log;
		$xlog->actorid   = User::get('id');
		if (!$xlog->store())
		{
			Notify::error($xlog->getError());
		}

		// Redirect back to the courses page
		App::redirect(
			Route::url('index.php?option=' . $this->_option),
			Lang::txt('COM_COURSES_COURSE_DELETED', $this->course->get('title')),
			'passed'
		);
	}

	/**
	 * Change the status of an item
	 *
	 * @param   string  $type    Item being changed
	 * @param   string  $status  Status to set
	 * @param   string  $id      Item ID
	 * @return  void
	 */
	public function savepageTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
		}

		// Incoming
		$page = Request::getArray('page', array(), 'post');

		$course = Models\Course::getInstance($page['course_id']);
		if (!$course->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		$model = new Models\Page($page['id']);

		if (!$model->bind($page))
		{
			// Redirect back to the course page
			App::redirect(
				Route::url($course->link() . '&active=' . $model->get('url') . '&action=' . ($model->get('id') ? 'addpage' : 'editpage')),
				$model->getError(),
				'error'
			);
			return;
		}

		if (!$model->store(true))
		{
			// Redirect back to the course page
			App::redirect(
				Route::url($course->link() . '&active=' . $model->get('url') . '&action=' . ($model->get('id') ? 'addpage' : 'editpage')),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect back to the course page
		App::redirect(
			Route::url($course->link() . '&active=' . $model->get('url')),
			Lang::txt('COM_COURSES_PAGE_SAVED')
		);
	}

	/**
	 * Change the status of an item
	 *
	 * @return  void
	 */
	public function deletepageTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask('COM_COURSES_NOT_LOGGEDIN');
		}

		if (!$this->course->exists())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		if (!$this->course->access('edit', 'course'))
		{
			App::redirect(
				Route::url($this->course->link())
			);
			return;
		}

		$model = $this->course->page(Request::getString('active', ''));

		$msg = null;

		if ($model->exists())
		{
			$model->set('active', 0);

			if (!$model->store(true))
			{
				$msg = $model->getError();
			}
		}

		// Redirect back to the course page
		App::redirect(
			Route::url($this->course->link()),
			($msg ? $msg : Lang::txt('COM_COURSES_PAGE_REMOVED')),
			($msg ? 'error' : null)
		);
	}

	/**
	 * Check if a course alias is valid
	 *
	 * @param   integer  $gid  Course alias
	 * @return  boolean  True if valid, false if not
	 */
	private function _validCn($gid)
	{
		if (preg_match("/^[0-9a-zA-Z]+[_0-9a-zA-Z]*$/i", $gid))
		{
			if (is_numeric($gid) && intval($gid) == $gid && $gid >= 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Generate a random string
	 *
	 * @param   integer  $length  Length of string
	 * @return  string
	 */
	private function randomString($length)
	{
		$str = '';

		for ($i=0; $i<$length; $i++)
		{
			$d = rand(1, 30)%2;
			$str .= $d ? chr(rand(65, 90)) : chr(rand(48, 57));
		}

		return strtoupper($str);
	}

	/**
	 * Get a course's availability
	 *
	 * @param   object  $course  CoursesCourse
	 * @return  string
	 */
	public function courseavailabilityTask($course = null)
	{
		//get the course
		$course = (!is_null($course)) ? $course : Request::getString('course', '');
		$course = strtolower(trim($course));

		if ($course == '')
		{
			return;
		}

		// Ensure the data passed is valid
		$c = Models\Course::getInstance($course);
		if (($course == 'new' || $course == 'browse') || !$this->_validCn($course) || $c->exists())
		{
			$availability = false;
		}
		else
		{
			$availability = true;
		}

		if (Request::getInt('no_html', 0) == 1)
		{
			echo json_encode(array('available' => $availability));
			return;
		}

		return $availability;
	}

	/**
	 * Serve up a course logo
	 *
	 * @return  void
	 */
	public function logoTask()
	{
		$file = PATH_APP . $this->course->logo();

		// Initiate a new content server and serve up the file
		$server = new Server();
		$server->filename($file);
		$server->disposition('inline');
		$server->acceptranges(false);

		if (!$server->serve())
		{
			// Should only get here on error
			App::abort(404, Lang::txt('COM_COURSES_SERVER_ERROR'));
		}

		exit;
	}

	/**
	 * Download a page file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Get some needed libraries
		if (!$this->course->access('view'))
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		// Get the scope of the parent page the file is attached to
		$filename = Request::getString('file', '');
		if (substr(strtolower($filename), 0, strlen('image:')) == 'image:')
		{
			$filename = substr($filename, strlen('image:'));
		}
		else if (substr(strtolower($filename), 0, strlen('file:')) == 'file:')
		{
			$filename = substr($filename, strlen('file:'));
		}
		$filename = urldecode($filename);
		$filename = \Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);

		// Get the configured upload path
		$base_path = DS . trim($this->config->get('filepath', '/site/courses'), DS) . DS . $this->course->get('id') . DS . 'pagefiles';

		// Does the path start with a slash?
		$filename = DS . ltrim($filename, DS);

		// Does the beginning of the $attachment->path match the config path?
		if (substr($filename, 0, strlen($base_path)) == $base_path)
		{
			// Yes - this means the full path got saved at some point
		}
		else
		{
			// No - append it
			$filename = $base_path . $filename;
		}

		// Add PATH_CORE
		$filepath = PATH_APP . $filename;

		// Ensure the file exist
		if (!file_exists($filepath))
		{
			App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND').' '.$filename);
		}

		// Initiate a new content server and serve up the file
		$xserver = new Server();
		$xserver->filename($filepath);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(500, Lang::txt('COM_COURSES_SERVER_ERROR'));
		}

		exit;
	}

	/**
	 * Show a form for providing copy title
	 *
	 * @return  void
	 */
	public function copyTask()
	{
		// Ensure we found the course info
		if (!$this->course->exists())
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		$rtrn = Request::getString('return');

		$this->view
			->set('course', $this->course)
			->set('return', $rtrn)
			->display();
	}

	/**
	 * Copy an entry and all associated data
	 *
	 * @return  void
	 */
	public function docopyTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Ensure we found the course info
		if (!$this->course->exists())
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		// Incoming
		$rtrn = Request::getString('return', '', 'post');
		if ($rtrn)
		{
			$rtrn = base64_decode($rtrn);
		}

		$fields = Request::getArray('fields', array('title' => '', 'alias' => ''), 'post');

		// Copy the course
		if (!$this->course->copy(true, $fields['title'], $fields['alias']))
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_COPY_FAILED') . ': ' . $this->course->getError());

			if (!$rtrn)
			{
				$rtrn = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false);
			}

			App::redirect($rtrn);
		}

		if (!$rtrn)
		{
			$rtrn = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $fields['alias'], false);
		}

		// Success
		Notify::success(Lang::txt('COM_COURSES_ITEM_COPIED'));

		App::redirect($rtrn);
	}

	/**
	 * Show a form for providing formed title
	 *
	 * @return  void
	 */
	public function forkTask()
	{
		// Ensure we found the course info
		if (!$this->course->exists())
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		if (!$this->course->config('allow_forks'))
		{
			App::abort(403, Lang::txt('COM_COURSES_ERROR_FORK_DIASABLED'));
		}

		$rtrn = Request::getString('return');

		$this->view
			->set('course', $this->course)
			->set('return', $rtrn)
			->display();
	}

	/**
	 * Copy an entry and all associated data
	 *
	 * @return  void
	 */
	public function doforkTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Ensure we found the course info
		if (!$this->course->exists())
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		if (!$this->course->isPublished() && !$this->course->isDraft())
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
		}

		if (!$this->course->config('allow_forks'))
		{
			App::abort(403, Lang::txt('COM_COURSES_ERROR_FORK_DIASABLED'));
		}

		// Incoming
		$rtrn = Request::getString('return', '', 'post');
		if ($rtrn)
		{
			$rtrn = base64_decode($rtrn);
		}

		$fields = Request::getArray('fields', array('title' => '', 'alias' => ''), 'post');

		$original = $this->course->get('id');

		// Copy the course
		if (!$this->course->copy(true, $fields['title'], $fields['alias']))
		{
			Notify::error(Lang::txt('COM_COURSES_ERROR_FORK_FAILED') . ': ' . $this->course->getError());

			if (!$rtrn)
			{
				$rtrn = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false);
			}

			App::redirect($rtrn);
		}

		// Remove all the managers
		foreach ($this->course->managers() as $manager)
		{
			if (!$manager->delete())
			{
				Notify::error($manager->getError());
			}
		}

		// Set the current user as a manager
		$role_id = 2;
		$this->course->add(User::get('id'), $role_id);

		// Note where the course was forked from
		$this->course->set('forked_from', $original);
		$this->course->store();

		if (!$rtrn)
		{
			$rtrn = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $fields['alias'], false);
		}

		// Success
		Notify::success(Lang::txt('COM_COURSES_ITEM_FORKED'));

		App::redirect($rtrn);
	}
}
