<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses Plugin class for pages
 */
class plgCoursesPages extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param   object   $course    Current course
	 * @param   object   $offering  Name of the component'
	 * @param   boolean  $describe  Return plugin description only?
	 * @return  object
	 */
	public function onCourse($course, $offering, $describe=false)
	{
		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', JText::_('PLG_COURSES_' . strtoupper($this->_name)))
			->set('default_access', $this->params->get('plugin_access', 'members'))
			->set('display_menu_tab', true)
			->set('icon', 'f05a');

		if ($describe)
		{
			return $response;
		}

		if (!($active = JRequest::getVar('active')))
		{
			JRequest::setVar('active', ($active = $this->_name));
		}

		// Section specific pages
		$total = $offering->pages(array(
			'count'       => true,
			'section_id'  => $offering->section()->get('id'),
			'active'      => 1
		), true);

		// Offering specific pages
		$total += $offering->pages(array(
			'count'       => true,
			'section_id'  => 0,
			'active'      => 1
		), true);

		// All course pages
		$total += $offering->pages(array(
			'count'       => true,
			'course_id'   => 0,
			'offering_id' => 0,
			'active'      => 1
		), true);

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($response->get('name') == $active)
		{
			$action = strtolower(JRequest::getWord('group', ''));
			if ($action && $action != 'edit' && $action != 'delete')
			{
				$action = 'download';
			}

			$active = strtolower(JRequest::getWord('unit', ''));

			if ($active == 'add')
			{
				$action = 'add';
			}
			if ($active == 'download')
			{
				$action = 'download';
			}
			if ($act = strtolower(JRequest::getWord('action', '')))
			{
				$action = $act;
			}

			$this->view = $this->view('default', 'pages');
			$this->view->option     = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = JRequest::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $course->config();
			$this->view->juser      = JFactory::getUser();

			switch ($action)
			{
				case 'add':
				case 'edit':     $this->_edit();         break;
				case 'save':     $this->_save();         break;
				case 'delete':   $this->_delete();       break;

				case 'upload':   $this->_fileUpload();   break;
				case 'download': $this->_fileDownload(); break;
				case 'list':     $this->_fileList();     break;
				case 'remove':   $this->_fileDelete();   break;

				default: $this->_list(); break;
			}

			if (JRequest::getInt('no_html', 0))
			{
				ob_clean();
				header('Content-type: text/plain');
				echo $this->view->loadTemplate();
				exit();
			}
			$response->set('html', $this->view->loadTemplate());
		}

		$response->set('meta_count', $total);

		// Return the output
		return $response;
	}

	/**
	 * Set layout and data for main page
	 *
	 * @return  void
	 */
	public function _list()
	{
		$this->view->setLayout('default');

		$active = JRequest::getVar('unit', '');

		// Section specific pages
		$spages = $this->view->offering->pages(array(
			'section_id'  => $this->view->offering->section()->get('id'),
			'active'      => 1
		), true);

		// Offering specific pages
		$opages = $this->view->offering->pages(array(
			'section_id'  => 0,
			'active'      => 1
		), true);

		// All course pages
		$gpages = $this->view->offering->pages(array(
			'course_id'   => 0,
			'offering_id' => 0,
			'active'      => 1
		), true);

		$pages = array_merge($spages, $opages);
		$pages = array_merge($pages, $gpages);

		if ($active)
		{
			foreach ($pages as $p)
			{
				if ($p->get('url') == $active)
				{
					$page = $p;
					break;
				}
			}
		}
		if (!$active || !$page->exists())
		{
			$page = (is_array($pages) && isset($pages[0])) ? $pages[0] : null;
		}
		$this->view->pages = $pages;
		$this->view->page  = $page;
	}

	/**
	 * Set layout to the edit form
	 *
	 * @param   mixed  $model
	 * @return  void
	 */
	public function _edit($model=null)
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_($this->view->offering->link() . '&active=' . $this->_name, false, true);
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}
		if (!$this->view->offering->access('manage', 'section'))
		{
			return $this->_list();
		}

		$this->view->setLayout('edit');

		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else
		{
			$page = JRequest::getVar('unit', '');

			$this->view->model = $this->view->offering->page($page);
		}
		if (!$this->view->model)
		{
			$this->view->model =  new CoursesModelPage($page);
		}
		$this->view->notifications = $this->getPluginMessage();

		if ($this->view->model->exists())
		{
			// Ensure section managers can only edit section pages
			if (!$this->view->model->get('section_id') && !$this->view->offering->access('manage'))
			{
				return $this->_list();
			}
		}

		// Section specific pages
		$spages = $this->view->offering->pages(array(
			'section_id' => $this->view->offering->section()->get('id')
		), true);

		// Offering specific pages
		$opages = $this->view->offering->pages(array(
			'section_id' => 0
		), true);

		// All course pages
		$gpages = $this->view->offering->pages(array(
			'course_id'   => 0,
			'offering_id' => 0
		), true);

		$pages = array_merge($spages, $opages);
		$pages = array_merge($pages, $gpages);

		$this->view->pages = $pages;
	}

	/**
	 * Save a record
	 *
	 * @return  void
	 */
	public function _save()
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_($this->view->offering->link() . '&active=' . $this->_name, false, true);
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}
		if (!$this->view->offering->access('manage', 'section'))
		{
			return $this->_list();
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$page = JRequest::getVar('fields', array(), 'post', 'none', 2);

		$model = new CoursesModelPage($page['id']);

		if (!$model->bind($page))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->_edit($model);
		}

		// Ensure section managers can only edit section pages
		if (!$model->get('section_id') && !$this->view->offering->access('manage'))
		{
			return $this->_list();
		}

		if (!$model->store(true))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->_edit($model);
		}

		$this->setRedirect(
			JRoute::_($this->view->offering->link() . '&active=' . $this->_name . '&unit=' . $model->get('url'))
		);
	}

	/**
	 * Delete a record
	 *
	 * @return  void
	 */
	public function _delete()
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_($this->view->offering->link() . '&active=' . $this->_name, false, true);
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $return, false)
			);
			return;
		}
		if (!$this->view->offering->access('manage'))
		{
			return $this->_list();
		}

		$model = $this->view->offering->page(JRequest::getVar('unit', ''));

		if ($model->exists())
		{
			$model->set('active', 0);

			if (!$model->store(true))
			{
				$this->addPluginMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect(
			JRoute::_($this->view->offering->link() . '&active=pages')
		);
	}

	/**
	 * Set redirect and message
	 *
	 * @param   string  $url   URL to redirect to
	 * @param   string  $msg   Message to send
	 * @param   string  $type  Message type (message, error, warning, info)
	 * @return  void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  string
	 */
	public function _ajaxUpload()
	{
		// Check if they're logged in
		if ($this->view->juser->get('guest'))
		{
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::_('PLG_COURSES_PAGES_ERROR_LOGIN_NOTICE')));
			exit();
		}

		//max upload size
		$sizeLimit = $this->params->get('maxAllowed', 40000000);

		// get the file
		if (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::_('PLG_COURSES_PAGES_ERROR_NO_FILE_PROVIDED')));
			exit();
		}

		//define upload directory and make sure its writable
		$path = $this->_path();

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				ob_clean();
				header('Content-type: text/plain');
				echo json_encode(array('error' => JText::_('PLG_COURSES_PAGES_ERROR_UNABLE_TO_UPLOAD')));
				exit();
			}
		}

		if (!is_writable($path))
		{
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::_('PLG_COURSES_PAGES_ERROR_UPLOAD_DIR_NOT_WRITABLE')));
			exit();
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::_('File is empty')));
			exit();
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::sprintf('PLG_COURSES_PAGES_ERROR_FILE_TOO_LARG', $max)));
			exit();
		}

		// Don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$filename = urldecode($filename);
		$filename = JFile::makeSafe($filename);
		$filename = str_replace(' ', '_', $filename);

		$ext = $pathinfo['extension'];
		while (file_exists($path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		$file = $path . DS . $filename . '.' . $ext;

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			//move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		ob_clean();
		header('Content-type: text/plain');
		echo json_encode(array(
			'success'   => true,
			'file'      => $filename . '.' . $ext,
			'directory' => str_replace(JPATH_ROOT, '', $path)
		));
		exit();
	}

	/**
	 * Upload a file to the wiki
	 *
	 * @return  void
	 */
	public function _fileUpload()
	{
		// Check if they're logged in
		if ($this->view->juser->get('guest'))
		{
			return $this->_files();
		}

		if (JRequest::getVar('no_html', 0))
		{
			return $this->_ajaxUpload();
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Ensure we have an ID to work with
		$listdir = JRequest::getInt('listdir', 0, 'post');
		if (!$listdir)
		{
			$this->setError(JText::_('PLG_COURSES_PAGES_ERROR_NO_ID_PROVIDED'));
			return $this->_files();
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('PLG_COURSES_PAGES_ERROR_NO_FILE_PROVIDED'));
			return $this->_files();
		}

		// Build the upload path if it doesn't exist
		$path = $this->_path();

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('PLG_COURSES_PAGES_ERROR_UNABLE_TO_MAKE_PATH'));
				return $this->_files();
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = urldecode($file['name']);
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Upload new files
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(JText::_('PLG_COURSES_PAGES_ERROR_UNABLE_TO_UPLOAD'));
		}

		// Push through to the media view
		return $this->_files();
	}

	/**
	 * Build and return the file path
	 *
	 * @return  string
	 */
	private function _path($page=null)
	{
		$path = JPATH_ROOT . DS . trim($this->view->config->get('filepath', '/site/courses'), DS) . DS;
		if (is_object($page))
		{
			if (!$page->get('offering_id'))
			{
				$path .= 'pagefiles';
			}
			else
			{
				$path .= $this->view->course->get('id') . DS;

				if ($page->get('section_id'))
				{
					$path .= 'sections' . DS . $page->get('section_id') . DS . 'pagefiles';
				}
				else
				{
					$path .= 'pagefiles' . DS . $this->view->offering->get('id');
				}
			}
		}
		else
		{
			if (!$this->view->offering->access('manage') && $this->view->offering->access('manage', 'section'))
			{
				$path .= $this->view->course->get('id') . DS . 'sections' . DS . $this->view->offering->section()->get('id') . DS . 'pagefiles';
			}
			else
			{
				if ($section = JRequest::getInt('section_id', 0))
				{
					$path .= $this->view->course->get('id') . DS . 'sections' . DS . $section . DS . 'pagefiles';
				}
				else
				{
					$path .= $this->view->course->get('id') . DS . 'pagefiles' . DS . $this->view->offering->get('id');
				}
			}
		}
		return $path;
	}

	/**
	 * Delete a file in the wiki
	 *
	 * @return  void
	 */
	public function _fileDelete()
	{
		// Check if they're logged in
		if ($this->view->juser->get('guest'))
		{
			return $this->_files();
		}

		$no_html = JRequest::getVar('no_html', 0);

		// Incoming file
		$file = trim(JRequest::getVar('file', '', 'get'));
		if (!$file)
		{
			$this->setError(JText::_('PLG_COURSES_PAGES_ERROR_NO_FILE_PROVIDED'));
			if ($no_html)
			{
				ob_clean();
				header('Content-type: text/plain');
				echo json_encode(array(
					'success'   => false,
					'error'     => $this->getError()
				));
				exit();
			}
			return $this->_files();
		}

		// Build the file path
		$path = $this->_path();

		// Delete the file
		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(JText::_('PLG_COURSES_PAGES_ERROR_FILE_NOT_FOUND'));
			if ($no_html)
			{
				ob_clean();
				header('Content-type: text/plain');
				echo json_encode(array(
					'success' => false,
					'error'   => $this->getError()
				));
				exit();
			}
			return $this->_files();
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file))
			{
				$this->setError(JText::_('PLG_COURSES_PAGES_ERROR_UNABLE_TO_DELETE_FILE'));
				if ($no_html)
				{
					ob_clean();
					header('Content-type: text/plain');
					echo json_encode(array(
						'success' => false,
						'error'   => $this->getError()
					));
					exit();
				}
			}
		}

		if ($no_html)
		{
			return $this->_fileList();
		}

		// Push through to the media view
		return $this->_files();
	}

	/**
	 * Display a form for uploading files
	 *
	 * @return  void
	 */
	public function _files()
	{
		$this->view->setLayout('files');

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->page = new CoursesModelPage(JRequest::getInt('page', 0));
	}

	/**
	 * Display a list of files
	 *
	 * @return  void
	 */
	public function _fileList()
	{
		$page = new CoursesModelPage(JRequest::getInt('page', 0));
		if (!$page->exists())
		{
			$page->set('offering_id', $this->view->offering->get('id'));
			$page->set('section_id', JRequest::getInt('section_id', 0));
		}

		$path = $this->_path($page);

		$folders = array();
		$docs    = array();

		if (is_dir($path))
		{
			// Loop through all files and separate them into arrays of images, folders, and other
			$dirIterator = new DirectoryIterator($path);
			foreach ($dirIterator as $file)
			{
				if ($file->isDot())
				{
					continue;
				}

				if ($file->isDir())
				{
					$name = $file->getFilename();
					$folders[$path . DS . $name] = $name;
					continue;
				}

				if ($file->isFile())
				{
					$name = $file->getFilename();
					if (('cvs' == strtolower($name))
					 || ('.svn' == strtolower($name)))
					{
						continue;
					}

					$docs[$path . DS . $name] = $name;
				}
			}

			ksort($folders);
			ksort($docs);
		}

		$this->view->docs    = $docs;
		$this->view->folders = $folders;

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->setLayout('list');
	}

	/**
	 * Download a wiki file
	 *
	 * @return  void
	 */
	public function _fileDownload()
	{
		if (!$this->view->course->access('view'))
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Get the scope of the parent page the file is attached to
		$filename = JRequest::getVar('group', '');

		if (substr(strtolower($filename), 0, strlen('image:')) == 'image:')
		{
			$filename = substr($filename, strlen('image:'));
		}
		else if (substr(strtolower($filename), 0, strlen('file:')) == 'file:')
		{
			$filename = substr($filename, strlen('file:'));
		}
		$filename = urldecode($filename);

		// Ensure we have a path
		if (empty($filename))
		{
			JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND') . '[r]' . $filename);
			return;
		}

		$page = $this->view->offering->page(JRequest::getVar('unit', ''));
		if (!$page->exists())
		{
			$pages = $this->view->offering->pages(array(
				'url'         => JRequest::getVar('unit', ''),
				'offering_id' => array(0, $this->view->offering->get('id')),
				'section_id'  => array(0, $this->view->offering->section()->get('id')),
				'limit'       => 1,
				'start'       => 0
			), true);
			$page = isset($pages[0]) ? $pages[0] : null;
		}

		// Add JPATH_ROOT
		$filepath = $this->_path($page) . DS . ltrim($filename, DS);

		// Ensure the file exist
		$found = true;
		if (!file_exists($filepath))
		{
			if (!$page)
			{
				JRequest::setVar('section_id', $this->view->offering->section()->get('id'));
				$filepath = $this->_path($page) . DS . ltrim($filename, DS);
				if (!file_exists($filepath))
				{
					$found = false;
				}
			}
			else
			{
				$found = false;
			}

			if (!$found)
			{
				JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND') . '[j]' . $filepath);
				return;
			}
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filepath);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('COM_COURSES_SERVER_ERROR') . '[x]' . $filepath);
		}
		else
		{
			exit;
		}
		return;
	}
}
