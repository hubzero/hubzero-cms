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
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.views.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradepolicies.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'form.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'formRespondent.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'formDeployment.php');

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
	 * Course object
	 * 
	 * @var string
	 */
	protected $course = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  $course
	 * @return     void
	 */
	public function __construct($oid=null, $course=null)
	{
		// Save course for quick reference
		$this->course = $course;

		parent::__construct($oid);
	}

	/**
	 * Get student grades
	 *
	 * Retrieve single or group of student grades from the grade book.  You could also 
	 * provide a scope, limiting the grades to an array of items, such as unit, course, or asset.
	 * The results will be returned as an array with student id as the uppermost key.
	 * 
	 * @param      int or array $scope, scope for which to pull grades (unit, course, asset)
	 * @param      int or array $user_id, user id for which to pull grades
	 * @return     array $grades
	 */
	public function grades($scope=null, $user_id=null)
	{
		// If no user provided, assume section
		if(is_null($user_id))
		{
			$members = $this->course->offering()->section()->members();

			$user_id = array();

			foreach ($members as $m)
			{
				$user_id[] = $m->get('user_id');
			}
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
	 * At this point, this method only takes into account what students have viewed.
	 * This makes sense on a PDF download, for example, but not on a quiz.  This 
	 * should be expanded to account for views on simpler asset types, and more complex
	 * criteria on assets where it can be tracked (ex: videos, where we can track an
	 * entire view, or an exam, where we know when they've actually finished it).
	 * 
	 * @param      int $user_id 
	 * @return     array $progress
	 */
	public function progress($user_id=null)
	{
		// Get the asset views
		$assetViews  = new CoursesTableAssetViews(JFactory::getDBO());
		$views = $assetViews->find(
			array(
				'section_id' => $this->course->offering()->section()->get('id'),
				'user_id'    => $user_id
			)
		);

		$progress = array();

		// Restructure array
		foreach ($views as $v)
		{
			$progress[$v->user_id][$v->unit_id][$v->asset_id] = $v->viewed;
		}

		// Calculate unit completion percentage for each student
		// Note: this is not their score, but rather, simply how many items within the unit they have viewed
		foreach ($progress as $user_id=>$user)
		{
			foreach ($user as $unit_id=>$unit)
			{
				$progress[$user_id][$unit_id]['percentage_complete'] = round((array_sum($unit) / count($unit)) * 100, 2);
			}
		}

		return $progress;
	}

	/**
	 * Calculate scores for each unit and the course as a whole
	 * 
	 * @param      int $user_id
	 * @param      int $asset_id
	 * @return     boolean true on success, false otherwise
	 */
	public function calculateScores($user_id=null, $asset_id=null)
	{
		// We need one of $course or $asset_id
		if (is_null($this->course) && is_null($asset_id))
		{
			return false;
		}

		// Get the course id
		if (!is_null($this->course) && is_object($this->course))
		{
			// Get our course model as well (to retrieve grade policy)
			$course = $this->course;
			$course_id = $this->course->get('id');
		}
		elseif (!is_null($asset_id) && is_numeric($asset_id))
		{
			$asset = new CoursesTableAsset(JFactory::getDBO());
			$asset->load($asset_id);
			$course_id = $asset->course_id;

			// Get our course model as well (to retrieve grade policy)
			$course = new CoursesModelCourse($course_id);
		}
		else
		{
			// Could not determine course id
			return false;
		}

		// Get a grade policy object
		$gradePolicy = new CoursesModelGradePolicies($course->offering()->section()->get('grade_policy_id'));

		// Get the grading policy score criteria
		$placeholders   = array('course_id'=>$course_id, 'scope'=>'course', 'user_id'=>$user_id);
		$score_criteria = $gradePolicy->replacePlaceholders('score_criteria', $placeholders);

		// Compute course grades
		if (!$this->_tbl->updateScores($score_criteria))
		{
			return false;
		}

		// Get the grading policy score criteria
		$placeholders   = array('course_id'=>$course_id, 'scope'=>'unit', 'user_id'=>$user_id, 'unit'=>true);
		$score_criteria = $gradePolicy->replacePlaceholders('score_criteria', $placeholders);

		// Compute unit grades
		if (!$grades = $this->_tbl->updateScores($score_criteria))
		{
			return false;
		}

		// Success
		return true;
	}

	/**
	 * Method to check for expired exams that students did not take and add 0's to the gradebook as appropriate
	 *
	 * @param      int $user_id (optional)
	 * @return     void
	 **/
	public function refresh($user_id=null)
	{
		$this->_tbl->syncGrades($this->course->get('id'), $user_id);

		// Compute unit and course scores as well
		$this->calculateScores($user_id);
	}

	/**
	 * Determine whether or not a student or group of students is passing
	 *
	 * @param      int $user_id (optional)
	 * @param      bool $section only (optional)
	 * @return     array
	 **/
	public function passing($section=true, $user_id=null)
	{
		// Get the course id
		if (!is_object($this->course))
		{
			return false;
		}

		$key       = 'user_id';
		$queryType = 'loadObjectList';

		// Get a grade policy object
		$gradePolicy = new CoursesModelGradePolicies($this->course->offering()->section()->get('grade_policy_id'));

		// Get the grading policy score criteria
		$placeholders = array('scope_id'=>$this->course->get('id'));

		// If section only, add appropriate joins to limit by section
		if ($section)
		{
			$placeholders['section_id'] = $this->course->offering()->section()->get('id');
		}

		// Add the user_id to the query if it's set
		if (!is_null($user_id) && is_numeric($user_id))
		{
			$placeholders['user_id'] = $user_id;
			$queryType = 'loadObject';
			$key       = '';
		}

		// Get passing data
		$grade_criteria = $gradePolicy->replacePlaceholders('grade_criteria', $placeholders);
		$passing = $this->_tbl->getPassing($grade_criteria, $queryType, $key);

		return $passing;
	}

	/**
	 * Get count of passing
	 *
	 * @param      bool $section only (optional)
	 * @return     object((int)passing, (int)failing)
	 **/
	public function countPassing($section=true)
	{
		if ($rows = $this->passing($section))
		{
			$countPassing = 0;

			foreach ($rows as $r)
			{
				if ($r->passing === '1')
				{
					$countPassing++;
				}
			}

			return $countPassing;
		}
		else
		{
			return '--';
		}
	}

	/**
	 * Get count of failing
	 *
	 * @param      bool $section only (optional)
	 * @return     object((int)passing, (int)failing)
	 **/
	public function countFailing($section=true)
	{
		if ($rows = $this->passing($section))
		{
			$countFailing = 0;

			foreach ($rows as $r)
			{
				if ($r->passing === '0')
				{
					$countFailing++;
				}
			}

			return $countFailing;
		}
		else
		{
			return '--';
		}
	}

	/**
	 * Check whether or not the student has earned a badge
	 *
	 * @param      int $user_id
	 * @return     bool
	 **/
	public function hasEarnedBadge($user_id=null)
	{
	}
}