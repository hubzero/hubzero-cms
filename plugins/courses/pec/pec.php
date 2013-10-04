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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Courses Plugin class for PEC
 */
class plgCoursesPec extends JPlugin
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
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function onOfferingEdit()
	{
		$area = array(
			'name'  => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name))
		);
		return $area;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onCourseEnrollLink($course, $offering, $section)
	{
		if (!$course->exists() || !$offering->exists())
		{
			return;
		}

		$url = 'index.php?option=com_courses&controller=offering&gid=' . $course->get('alias') . '&offering=' . $offering->get('alias') . ($section->get('alias') != '__default' ? ':' . $section->get('alias') : '') . '&task=enroll';

		if ($offering->params('pec_register', 0) && $offering->params('pec_course', 0))
		{
			$url = str_replace('{{course}}', $offering->params('pec_course', 0), $offering->params('url', 'https://www.distance.purdue.edu/{{course}}'));
		}

		return $url;
	}

	/**
	 * Actions to perform after saving an offering
	 * 
	 * @param      object  $model CoursesModelSection
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onSectionSave($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 * 
	 * @param      object  $model CoursesModelSection
	 * @return     void
	 */
	public function onSectionDelete($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}
}
