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
class plgCoursesGuide extends Hubzero_Plugin
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
	 * Event call after course outline
	 * 
	 * @param      object $course   Current course
	 * @param      object $offering Current offering
	 * @return     void
	 */
	public function onCourseAfterOutline($course, $offering)
	{
		$member = $offering->member(JFactory::getUser()->get('id'));
		if ($member->get('first_visit') && $member->get('first_visit') != '0000-00-00 00:00:00')
		{
			return;
		}

		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('courses', $this->_name);
		Hubzero_Document::addPluginScript('courses', $this->_name, 'guide.overlay');

		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => $this->_name,
				'layout'  => 'overlay'
			)
		);
		$this->view->option     = JRequest::getCmd('option', 'com_courses');
		$this->view->controller = JRequest::getWord('controller', 'course');
		$this->view->course     = $course;
		$this->view->offering   = $offering;
		$this->view->juser      = JFactory::getUser();
		$this->view->plugin     = $this->_name;

		return $this->view->loadTemplate();
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

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('courses', $this->_name);
			Hubzero_Document::addPluginScript('courses', $this->_name);

			/*$action = strtolower(JRequest::getWord('unit', ''));
			if ($action && $action != 'edit' && $action != 'save' && $action != 'mark')
			{
				$action = 'download';
			}

			if ($act = strtolower(JRequest::getWord('action', '')))
			{
				$action = $act;
			}*/
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
					'name'    => $this->_name
				)
			);
			$this->view->option     = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = JRequest::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $config;
			$this->view->juser      = JFactory::getUser();
			$this->view->plugin     = $this->_name;

			switch ($action)
			{
				case 'edit':   $this->_edit();   break;
				case 'save':   $this->_save();   break;
				case 'mark':   $this->_mark();   break;

				case 'download': $this->_fileDownload(); break;

				default: $this->_default(); break;
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

		// Return the output
		return $arr;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _default()
	{
		$this->view->setLayout('default');

		$active = JRequest::getVar('unit', '');

		$pages = $this->view->offering->pages(array(
			'course_id'   => 0,
			'offering_id' => 0
		));
		$page = $this->view->offering->page($active);
		if (!$active || !$page->exists())
		{
			$page = (is_array($pages) && isset($pages[0])) ? $pages[0] : null;
		}
		//$this->view->pages = $pages;
		$this->view->page  = $page;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @return     string
	 */
	public function _mark()
	{
		$this->view->setLayout('mark');

		$member = $this->view->offering->member(JFactory::getUser()->get('id'));
		if ($member->get('first_visit') && $member->get('first_visit') != '0000-00-00 00:00:00')
		{
			return;
		}
		$member->set('first_visit', date('Y-m-d H:i:s', time()));
		$member->store();
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
	 * Build and return the file path
	 * 
	 * @return     string
	 */
	private function _path()
	{
		return JPATH_ROOT . DS . trim($this->view->config->get('filepath', '/site/courses'), DS) . DS . 'pagefiles';
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
