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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'grade.book.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'form.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'formRespondent.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'formDeployment.php');

/**
 * Courses model class for grade book
 */
class CoursesModelGradeBook extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableGradeBook';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'gradebook';

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @return     void
	 */
	public function __construct($oid)
	{
		parent::__construct($oid);
	}

	/**
	 * Get student gradebook
	 * 
	 * @param      int or array $user_id, user id for which to pull grades
	 * @param      int or array $scope, scope for which to pull grades (unit, course, asset)
	 * @return     array $grades
	 */
	public function getGrades($user_id=null, $scope=null)
	{
		// If no user provided, use the current user
		if(is_null($user_id))
		{
			$user_id = JFactory::getUser()->get('id');
		}

		// Get the grades themselves
		$filters = array('user_id'=>$user_id, 'scope'=>$scope);
		$results = $this->_tbl->find($filters);

		$grades = array();

		// Restructure data
		foreach ($results as $grade)
		{
			if ($grade->scope == 'unit')
			{
				$grades[$grade->user_id]['units'][$grade->scope_id] = $grade->score;
			}
			if ($grade->scope == 'course')
			{
				$grades[$grade->user_id]['course'][$grade->scope_id] = $grade->score;
			}
			if ($grade->scope == 'asset')
			{
				$grades[$grade->user_id]['assets'][$grade->scope_id] = $grade->score;
			}
		}

		return $grades;
	}

	/**
	 * Get current progress
	 * 
	 * @param      object $course - course object
	 * @return     array $progress
	 */
	public function getProgress($course)
	{
		// Get the assets
		$asset  = new CoursesTableAsset(JFactory::getDBO());
		$assets = $asset->find(
			array(
				'w' => array(
					'section_id' => $course->offering()->section()->get('id'),
					'asset_type' => 'exam',
					'state'      => 1
				)
			)
		);

		// Loop through all assets
		foreach($assets as $a)
		{
			// Check for result for given student on form
			preg_match('/\?crumb=([-a-zA-Z0-9]{20})/', $a->url, $matches);

			$crumb = false;

			if(isset($matches[1]))
			{
				$crumb = $matches[1];
			}

			if(!$crumb)
			{
				// Break foreach, this is not a valid form!
				continue;
			}

			// Count total number of forms
			$form_count = (isset($form_count)) ? ++$form_count : 1;

			// Get the unit model
			$unit = $course->offering()->unit($a->unit_id);

			// Also count total forms through current unit
			if($unit->isAvailable() || $unit->ended())
			{
				$form_count_current = (isset($form_count_current)) ? ++$form_count_current : 1;
			}

			// Get the form deployment based on crumb
			$dep = PdfFormDeployment::fromCrumb($crumb);

			// Loop through the results of the deployment
			foreach($dep->getResults() as $result)
			{
				// Create a per student form count
				if(!isset($progress[$result['user_id']]['form_count']))
				{
					$progress[$result['user_id']]['form_count'] = 0;
				}

				// Store the score
				$progress[$result['user_id']][$unit->get('id')]['forms'][$dep->getId()]['score']    = $result['score'];
				$progress[$result['user_id']][$unit->get('id')]['forms'][$dep->getId()]['finished'] = $result['finished'];
				$progress[$result['user_id']][$unit->get('id')]['forms'][$dep->getId()]['title']    = $a->title;

				// Track the sum of scores for this unit and iterate the count
				++$progress[$result['user_id']]['form_count'];
			}
		}

		$progress['form_count']     = (isset($form_count)) ? $form_count : 1;
		$progress['current_marker'] = (isset($form_count_current) && isset($form_count)) ? (round(($form_count_current / $form_count)*100, 2)) : 0;

		return $progress;
	}

	/**
	 * Resave/refresh grades for a user(s)
	 * 
	 * @param      int or array $user_id, user id for which to pull grades
	 * @return     boolean true on success, false otherwise
	 */
	public function refresh($user_id=null, $section_id=null)
	{
		if (is_null($user_id) && is_null($section_id))
		{
			return false;
		}

		$asset = new CoursesTableAsset(JFactory::getDBO());

		$filters = array(
			'w' => array(
				'section_id' => $section_id,
				'asset_type' => 'exam',
				'state'      => 1
			)
		);

		// Get published assets for this section
		$assets = $asset->find($filters);

		// If we have a user_id, make sure user_id is an array
		if (!is_null($user_id) && !is_array($user_id))
		{
			$user_id = array($user_id);
		}

		foreach ($assets as $a)
		{
			// Check for result for given student on form
			preg_match('/\?crumb=([-a-zA-Z0-9]{20})/', $a->url, $matches);

			$crumb = false;

			if(isset($matches[1]))
			{
				$crumb = $matches[1];
			}

			if(!$crumb)
			{
				// Break foreach, this is not a valid form!
				continue;
			}

			// Get the form deployment based on crumb
			$dep = PdfFormDeployment::fromCrumb($crumb);

			// Loop through the results of the deployment
			foreach($dep->getResults(true) as $result)
			{
				// If we have a user_id, only change given users
				if (!is_null($user_id) && !in_array($result['user_id'], $user_id))
				{
					continue;
				}
				// If form hasn't been completed, and time hasn't expired, skip it
				if (is_null($result['score'])
					&& (is_null($dep->getEndTime()) || $dep->getEndTime() == '0000-00-00 00:00:00' || $dep->getEndTime() > date("Y-m-d H:i:s")))
				{
					continue;
				}
				// If form hasn't been completed, but time has expired, result is 0
				elseif (is_null($result['score']) && $dep->getEndTime() < date("Y-m-d H:i:s"))
				{
					var_dump($dep->getEndTime());die();
					$result['score'] = '0.00';
				}

				$this->saveScore($result['score'], $a->id, $result['user_id']);
			}
		}

		return true;
	}

	/**
	 * Save score to grade book
	 * 
	 * @param      decimal $score, score to save
	 * @param      int $asset_id, asset id of item being saved
	 * @param      int $user_id, user id of user gradebook entry
	 * @return     boolean true on success, false otherwise
	 */
	public function saveScore($score, $asset_id, $user_id=null)
	{
		// If not user is given, assume the current user
		if (is_null($user_id))
		{
			$user_id = JFactory::getUser()->get('id');
		}

		// First, check to see if a score for this asset and user already exists
		$results = $this->_tbl->find(array('user_id'=>$user_id, 'scope_id'=>$asset_id, 'scope'=>'asset'));
		$gb_id   = ($results) ? $results[0]->id : null;

		// Set values
		$gradebook = new CoursesModelGradeBook($gb_id);
		$gradebook->set('user_id', $user_id);
		$gradebook->set('score', round($score, 2));
		$gradebook->set('scope', 'asset');
		$gradebook->set('scope_id', $asset_id);

		// Save
		if(!$gradebook->store())
		{
			return false;
		}

		// Compute unit and course averages
		$this->unitAverage($asset_id, $user_id);
		$this->courseAverage($asset_id, $user_id);

		// Success
		return true;
	}

	/**
	 * Calculate unit average
	 * 
	 * @param      int $asset_id, id of asset that was updated
	 * @param      int $user_id, user id of user gradebook entry
	 * @return     boolean true on success, false otherwise
	 */
	public function unitAverage($asset_id, $user_id=null)
	{
		// If not user is given, assume the current user
		if (is_null($user_id))
		{
			$user_id = JFactory::getUser()->get('id');
		}

		// Figure out what unit we're in
		$asset   = new CoursesTableAsset(JFactory::getDBO());
		$unit    = $asset->find(array('w'=>array('asset_id'=>$asset_id), 'start'=>0, 'limit'=>1));
		$unit_id = $unit[0]->unit_id;

		// Get the asset_ids of all assets in this unit
		$this->_db->setQuery(
			'SELECT ca.id
			FROM #__courses_assets as ca
			INNER JOIN #__courses_asset_associations as caa ON caa.asset_id = ca.id
			INNER JOIN #__courses_asset_groups as cag ON cag.id = caa.scope_id
			INNER JOIN #__courses_units as u ON u.id = cag.unit_id
			WHERE u.id = ' . $this->_db->Quote($unit_id)
		);
		$assets = $this->_db->loadResultArray();

		// Get the average unit grade
		$average = $this->_tbl->average(array('user_id'=>$user_id, 'scope'=>'asset', 'scope_id'=>$assets));

		// First, check to see if a score for this asset and user already exists
		$results = $this->_tbl->find(array('user_id'=>$user_id, 'scope_id'=>$unit_id, 'scope'=>'unit'));
		$gb_id   = ($results) ? $results[0]->id : null;

		// Save the score to the grade book
		$gradebook = new CoursesModelGradeBook($gb_id);
		$gradebook->set('user_id', $user_id);
		$gradebook->set('score', round($average, 2));
		$gradebook->set('scope', 'unit');
		$gradebook->set('scope_id', $unit_id);

		if(!$gradebook->store())
		{
			return false;
		}

		return true;
	}

	/**
	 * Calculate course average
	 * 
	 * @param      int $asset_id, id of asset that was updated
	 * @param      int $user_id, user id of user gradebook entry
	 * @return     boolean true on success, false otherwise
	 */
	public function courseAverage($asset_id, $user_id=null)
	{
		// If not user is given, assume the current user
		if (is_null($user_id))
		{
			$user_id = JFactory::getUser()->get('id');
		}

		// Get the course id
		$asset = new CoursesTableAsset(JFactory::getDBO());
		$asset->load($asset_id);
		$course_id = $asset->course_id;

		// Get the asset_ids of all assets in this course
		$this->_db->setQuery(
			'SELECT id
			FROM #__courses_assets
			WHERE course_id = ' . $this->_db->Quote($course_id)
		);
		$assets = $this->_db->loadResultArray();

		// Get the average course grade
		$average = $this->_tbl->average(array('user_id'=>$user_id, 'scope'=>'asset', 'scope_id'=>$assets));

		// First, check to see if a score for this asset and user already exists
		$results = $this->_tbl->find(array('user_id'=>$user_id, 'scope_id'=>$course_id, 'scope'=>'course'));
		$gb_id   = ($results) ? $results[0]->id : null;

		// Save the score to the grade book
		$gradebook = new CoursesModelGradeBook($gb_id);
		$gradebook->set('user_id', $user_id);
		$gradebook->set('score', round($average, 2));
		$gradebook->set('scope', 'course');
		$gradebook->set('scope_id', $course_id);

		if(!$gradebook->store())
		{
			return false;
		}

		return true;
	}
}