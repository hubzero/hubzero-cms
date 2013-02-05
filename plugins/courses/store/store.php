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
 * Courses Plugin class for store
 */
class plgCoursesStore extends JPlugin
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

		//$this->loadLanguage();
	}

	/**
	 * Actions to perform after saving a course
	 * 
	 * @param      object  $model CoursesModelCourse
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterSaveCourse($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting a course
	 * 
	 * @param      object  $model CoursesModelCourse
	 * @return     void
	 */
	public function onAfterDeleteCourse($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after saving an offering
	 * 
	 * @param      object  $model CoursesModelOffering
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterSaveOffering($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 * 
	 * @param      object  $model CoursesModelOffering
	 * @return     void
	 */
	public function onAfterDeleteOffering($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after saving an offering
	 * 
	 * @param      object  $model CoursesModelSection
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterSaveSection($model, $isNew=false)
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
	public function onAfterDeleteSection($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}
}
