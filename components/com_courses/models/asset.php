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
		// /site/courses/{course ID}/{asset ID}/{asset file}
		$path = DS . trim($this->_params->get('uploadpath', '/site/courses'), DS) . DS . $course . DS . $this->get('id');
		if ($withUrl)
		{
			$path .= DS . ltrim($this->get('url'), DS);
		}

		// Override path for exam type assets
		// Override path for url/link type assets
		if (in_array(strtolower($this->get('type')), array('exam', 'link', 'url')))
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
			$dt->load($this->get('id'), $this->_scope, $this->get('section_id'));
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
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function render($course=null, $option='com_courses')
	{
		$type = strtolower($this->get('type'));

		$view = new JView(array(
			'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_courses',
			'name'      => 'assets',
			'layout'    => $type
		));
		$view->asset   = $this->_tbl;
		$view->model   = $this;
		$view->course  = $course;
		$view->option  = $option;

		return $view->loadTemplate();
	}
}

