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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Plugin');

/**
 * Courses Plugin class for pages
 */
class plgCoursesPages extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onCourseAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => true
		);
		return $area;
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 * 
	 * @param      object  $course      Current course
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onCourse($config, $course, $offering, $action='', $areas=null)
	{
		$return = 'html';
		$active = $this->_name;
		$active_real = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'name' => $active
		);

		//get this area details
		$this_area = $this->onCourseAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!in_array($this_area['name'], $areas)) 
			{
				//return $arr;
				$return = 'metadata';
			}
		}

		// Is the user a course manager?
		$total = $offering->pages(array('count' => true));

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') 
		{
			//$document =& JFactory::getDocument();
			//$document->addScript("/media/system/js/jquery.fileuploader.js");
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('courses', $this->_name);
			Hubzero_Document::addPluginScript('courses', $this->_name);

			$action = strtolower(JRequest::getWord('group', ''));
			if ($action && $action != 'edit' && $action != 'delete')
			{
				$action = 'download';
			}//JRequest::getWord('group', '')

			$active = strtolower(JRequest::getWord('unit', ''));

			if ($active == 'add')
			{
				$action = 'add';
			}
			if ($act = strtolower(JRequest::getWord('action', '')))
			{
				$action = $act;
			}

			ximport('Hubzero_Plugin_View');
			$this->view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'courses',
					'element' => $this->_name,
					'name'    => 'pages'
				)
			);
			$this->view->option     = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = JRequest::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $config;
			$this->view->juser      = JFactory::getUser();

			switch ($action)
			{
				case 'add':
				case 'edit':   $this->_edit();   break;
				case 'save':   $this->_save();   break;
				case 'delete': $this->_delete(); break;

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
			$arr['html'] = $this->view->loadTemplate();
		}

		$arr['metadata']['count'] = $total;

		// Return the output
		return $arr;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _list()
	{
		$this->view->setLayout('default');

		$active = JRequest::getVar('unit', '');

		$pages = $this->view->offering->pages();

		$page = $this->view->offering->page($active);
		if (!$active || !$page->exists())
		{
			$page = (is_array($pages) && isset($pages[0])) ? $pages[0] : null;
		}
		$this->view->page  = $page;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _edit($model=null)
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=' . $this->_name);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}
		if (!$this->view->offering->access('manage'))
		{
			return $this->_list();
		}

		Hubzero_Document::addSystemScript('jquery.fileuploader');
		$this->view->setLayout('edit');

		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else
		{
			$page = JRequest::getWord('unit', '');

			$this->view->model = $this->view->offering->page($page); //new CoursesModelPage($page);
		}
		$this->view->notifications = $this->getPluginMessage();
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _save()
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=' . $this->_name);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}
		if (!$this->view->offering->access('manage'))
		{
			return $this->_list();
		}

		$page = JRequest::getVar('fields', array(), 'post');

		$model = new CoursesModelPage($page['id']);

		if (!$model->bind($page))
		{
			//$this->setError($model->getError());
			$this->addPluginMessage($model->getError(), 'error');
			return $this->_edit($model);
		}

		if (!$model->store(true))
		{
			//$this->setError($model->getError());
			$this->addPluginMessage($model->getError(), 'error');
			return $this->_edit($model);
		}

		//return $this->_list();
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=' . $this->_name . '&unit=' . $model->get('url'))
		);
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _delete()
	{
		if ($this->view->juser->get('guest'))
		{
			$return = JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=' . $this->_name);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
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
			JRoute::_('index.php?option=' . $this->view->option . '&gid=' . $this->view->course->get('alias') . '&offering=' . $this->view->offering->get('alias') . '&active=pages')
		);
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      string $url  URL to redirect to
	 * @param      string $msg  Message to send
	 * @param      string $type Message type (message, error, warning, info)
	 * @return     void
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
	 * @return     string
	 */
	public function _ajaxUpload()
	{
		// Check if they're logged in
		if ($this->view->juser->get('guest')) 
		{
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::_('Must be logged in.')));
			exit();
		}

		//allowed extensions for uplaod
		//$allowedExtensions = array("png","jpeg","jpg","gif");
		
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
			//$files = JRequest::getVar('qqfile', '', 'files', 'array');
			
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::_('File not found')));
			exit();
		}

		//define upload directory and make sure its writable
		$path = $this->_path();
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				ob_clean();
				header('Content-type: text/plain');
				echo json_encode(array('error' => JText::_('Error uploading. Unable to create path.')));
				exit();
			}
		}

		if (!is_writable($path))
		{
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::_('Server error. Upload directory isn\'t writable.')));
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
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Hubzero_View_Helper_Html::formatSize($sizeLimit));
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array('error' => JText::sprintf('File is too large. Max file upload size is %s', $max)));
			exit();
		}

		// don't overwrite previous files that were uploaded
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
	 * @return     void
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

		// Ensure we have an ID to work with
		$listdir = JRequest::getInt('listdir', 0, 'post');
		if (!$listdir) 
		{
			$this->setError(JText::_('WIKI_NO_ID'));
			return $this->_files();
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			$this->setError(JText::_('WIKI_NO_FILE'));
			return $this->_files();
		}

		// Build the upload path if it doesn't exist
		$path = $this->_path();

		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('Error uploading. Unable to create path.'));
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
			$this->setError(JText::_('ERROR_UPLOADING'));
		}

		// Push through to the media view
		return $this->_files();
	}

	/**
	 * Build and return the file path
	 * 
	 * @return     string
	 */
	private function _path()
	{
		return JPATH_ROOT . DS . trim($this->view->config->get('filepath', '/site/courses'), DS) . DS . $this->view->course->get('id') . DS . 'pagefiles' . DS . $this->view->offering->get('id');
	}

	/**
	 * Delete a file in the wiki
	 * 
	 * @return     void
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
			$this->setError(JText::_('No file name provided.'));
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
			$this->setError(JText::_('File not found.'));
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
		else 
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file)) 
			{
				$this->setError(JText::_('Unable to delete file.'));
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
			} 
		}

		if ($no_html)
		{
			/*ob_clean();
			header('Content-type: text/plain');
			echo json_encode(array(
				'success'   => true
			));
			exit();*/
			return $this->_fileList();
		}

		// Push through to the media view
		return $this->_files();
	}

	/**
	 * Display a form for uploading files
	 * 
	 * @return     void
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
	}

	/**
	 * Display a list of files
	 * 
	 * @return     void
	 */
	public function _fileList()
	{
		$path = $this->_path();

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
	 * @return     void
	 */
	public function _fileDownload()
	{
		// Get some needed libraries
		ximport('Hubzero_Content_Server');

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
			JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND').'[r]'.$filename);
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH').'[f]'.$filename);
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH').'[e]'.$filename);
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH').'[s]'.$filename);
			return;
		}
		// Disallow \
		if (strpos('\\', $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH').'[g]'.$filename);
			return;
		}
		// Disallow ..
		if (strpos('..', $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH').'[h]'.$filename);
			return;
		}

		// Add JPATH_ROOT
		$filename = $this->_path() . DS . ltrim($filename, DS);

		// Ensure the file exist
		if (!file_exists($filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND').'[j]'.$filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve()) 
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('COM_COURSES_SERVER_ERROR').'[x]'.$filename);
		} 
		else 
		{
			exit;
		}
		return;
	}
}
