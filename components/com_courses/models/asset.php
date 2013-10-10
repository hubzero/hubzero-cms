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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section' . DS . 'date.php');

/**
 * Courses model class for a course
 */
class CoursesModelAsset extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableAsset';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'asset';

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	protected $_params = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid)
	{
		parent::__construct($oid);

		$this->_params = JComponentHelper::getParams('com_courses');
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property})) 
		{
			return $this->_tbl->{'__' . $property};
		}
		else if (in_array($property, self::$_section_keys))
		{
			$tbl = new CoursesTableSectionDate($this->_db);
			$tbl->load($this->get('id'), 'asset', $this->get('section_id'));

			$this->set('publish_up', $tbl->get('publish_up'));
			$this->set('publish_down', $tbl->get('publish_down'));

			return $tbl->get($property, $default);
		}
		return $default;
	}

	/**
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function path($course=0, $withUrl=true)
	{
		if (!$this->get('id'))
		{
			return false;
		}

		// /site/courses/{course ID}/{asset ID}/{asset file}
		$path = DS . trim($this->_params->get('uploadpath', '/site/courses'), DS) . DS . $course . DS . $this->get('id');
		if ($withUrl)
		{
			$path .= DS . ltrim($this->get('url'), DS);
		}

		// Override path for exam type assets
		// Override path for url/link type assets
		if (in_array(strtolower($this->get('type')), array('form', 'link', 'url')))
		{
			$path = $this->get('url');
		}

		return $path;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$value = parent::store($check);

		if ($value && $this->get('section_id'))
		{
			$dt = new CoursesTableSectionDate($this->_db);
			$dt->load(
				$this->get('id'), 
				$this->_scope, 
				$this->get('section_id')
			);
			$dt->set('publish_up', $this->get('publish_up'));
			$dt->set('publish_down', $this->get('publish_down'));
			if (!$dt->store())
			{
				$this->setError($dt->getError());
			}
		}

		return $value;
	}

	/**
	 * Delete an asset
	 *   Deleted asset_associations until there is only one
	 *   association left, then it deletes the association,
	 *   the asset record, and asset file(s)
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove dates
		if ($this->get('section_id'))
		{
			$dt = new CoursesTableSectionDate($this->_db);
			$dt->load($this->get('id'), $this->_scope, $this->get('section_id'));
			if (!$dt->delete())
			{
				$this->setError($dt->getError());
			}
		}

		// Remove this record from the database and log the event
		return parent::delete();
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view', $item='section')
	{
		return $this->config()->get('access-' . strtolower($action) . '-' . $item);
	}

	/**
	 * Track asset views
	 *
	 * @return    void
	 */
	public function logView($course=null)
	{
		require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.views.php');

		if (!$course || !is_object($course))
		{
			$gid      = JRequest::getVar('gid');
			$offering = JRequest::getVar('offering');
			$section  = JRequest::getVar('section');

			$course = new CoursesModelCourse($gid);
			$course->offering($offering);
			$course->offering()->section($section);
		}

		$member = $course->offering()->section()->member(JFactory::getUser()->get('id'));

		if (!$member->get('id'))
		{
			$member = $course->offering()->member(JFactory::getUser()->get('id'));
		}

		if (!$member || !is_object($member) || !$member->get('id'))
		{
			return false;
		}

		$view = new CoursesTableAssetViews($this->_db);
		$view->asset_id          = $this->_tbl->id;
		$view->course_id         = $this->get('course_id');
		$view->viewed            = date('Y-m-d H:i:s', time());
		$view->viewed_by         = $member->get('id');
		$view->ip                = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
		$view->url               = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
		$view->referrer          = (isset($_SERVER['HTTP_REFERRER']) ? $_SERVER['HTTP_REFERRER'] : '');
		$view->user_agent_string = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$view->session_id        = JFactory::getSession()->getId();
		if (!$view->store()) 
		{
			$this->setError($view->getError());
		}
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function render($course=null, $option='com_courses')
	{
		$type = strtolower($this->get('type'));
		$subtype = strtolower($this->get('subtype'));
		$layout = 'default';

		$this->logView($course);

		// Check to see that the view template exists, otherwise, use the default
		if (file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'views' . DS . 'assets' . DS . 'tmpl' . DS . $type . '_' . $subtype . '.php'))
		{
			$layout = $type . '_' . $subtype;
		}
		elseif (file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'views' . DS . 'assets' . DS . 'tmpl' . DS . $type . '.php'))
		{
			$layout = $type;
		}

		$view = new JView(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_courses',
			'name'      => 'assets',
			'layout'    => $layout
		));
		$view->asset   = $this->_tbl;
		$view->model   = $this;
		$view->course  = $course;
		$view->option  = $option;

		return $view->loadTemplate();
	}

	/**
	 * Download a wiki file
	 * 
	 * @return     void
	 */
	public function download($course)
	{
		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		if (!$course->access('view'))
		{
			JError::raiseError(404, JText::_('COM_COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Get the scope of the parent page the file is attached to
		$filename = JRequest::getVar('file', '');
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
			JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND'));
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH'));
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH'));
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH'));
			return;
		}
		// Disallow \
		if (strpos('\\', $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH'));
			return;
		}
		// Disallow ..
		if (strpos('..', $filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_BAD_FILE_PATH'));
			return;
		}

		// Get the configured upload path
		$config = JComponentHelper::getParams('com_courses');
		$base_path = DS . trim($config->get('filepath', '/site/courses'), DS) . DS . $course->get('id') . DS . $this->get('id');

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

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $filename;

		// Ensure the file exist
		if (!file_exists($filename)) 
		{
			JError::raiseError(404, JText::_('COM_COURSES_FILE_NOT_FOUND').' '.$filename);
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
			JError::raiseError(404, JText::_('COM_COURSES_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function parents($filters=array())
	{
		if (!isset($filters['asset_id']))
		{
			$filters['asset_id'] = (int) $this->get('id');
		}

		$tbl = new CoursesTableAssetAssociation($this->_db);

		if (isset($filters['count']) && $filters['count'])
		{
			return $tbl->count($filters);
		}

		if (!($results = $tbl->find($filters)))
		{
			$results = array();
		}

		return $results;
	}
}

