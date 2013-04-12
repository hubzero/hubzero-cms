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
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'gradepolicies.php');
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
	 * Retrieve single or group of student grades from the grade book.  You could also 
	 * provide a scope, limiting the grades to an array of items, such as unit, course, or asset.
	 * The results will be returned as an array with student id as the uppermost key.
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
	 * At this point, this method only takes into account what students have viewed.
	 * This makes sense on a PDF download, for example, but not on a quiz.  This 
	 * should be expanded to account for views on simpler asset types, and more complex
	 * criteria on assets where it can be tracked (ex: videos, where we can track an
	 * entire view, or an exam, where we know when they've actually finished it).
	 * 
	 * @param      object $course - course object
	 * @return     array $progress
	 */
	public function getProgress($course)
	{
		// Get the asset views
		$assetViews  = new CoursesTableAssetViews(JFactory::getDBO());
		$views = $assetViews->find(
			array(
				'section_id' => $course->offering()->section()->get('id')
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
	 * Save a form score to the grade book
	 *
	 * Consider marking for deprecation?
	 * This used to be called from the save score method of the forms model.
	 * It has been, in essence, replaced by the use of the refresh method,
	 * which inserts scores into the grade book as needed.
	 * 
	 * @param      decimal $score, score to save
	 * @param      int $asset_id, asset id of item being saved
	 * @param      int $user_id, user id of user gradebook entry
	 * @param      bool $calculateScores, whether or not to compute unit and course averages
	 * @return     boolean true on success, false otherwise
	 */
	public function saveAssetResult($score, $asset_id, $user_id=null, $calculateScores=true)
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
		if (!$gradebook->store())
		{
			return false;
		}

		if ($calculateScores)
		{
			// Compute unit and course scores as well
			$this->calculateScores($user_id, null, $asset_id);
		}

		// Success
		return true;
	}

	/**
	 * Calculate scores for each unit and the course as a whole
	 *
	 * This should be expanded to account for a scenario where only
	 * a midterm and final count toward the grade (as an example).
	 * 
	 * @param      int $user_id
	 * @param      obj $course
	 * @param      int $asset_id
	 * @return     boolean true on success, false otherwise
	 */
	public function calculateScores($user_id, $course=null, $asset_id=null)
	{
		// We need one of $course or $asset_id
		if (is_null($course) && is_null($asset_id))
		{
			return false;
		}

		// Get the course id
		if (!is_null($course) && is_object($course))
		{
			$course_id = $course->get('id');
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
		$policy  = $course->offering()->section()->get('grade_policy_id');
		$gradePolicy = new CoursesModelGradePolicies($policy);

		// Get the grading policy score criteria
		$score_criteria = $gradePolicy->replacePlaceholders('score_criteria', array('course_id'=>$course_id, 'user_id'=>$user_id));
		$score_criteria = json_decode($score_criteria);

		// Compute course grade
		$grade = $this->_tbl->calculateScore($score_criteria, 'loadResult');

		// First, check to see if a score for this asset and user already exists
		$results = $this->_tbl->find(array('user_id'=>$user_id, 'scope_id'=>$course_id, 'scope'=>'course'));
		$gb_id   = ($results) ? $results[0]->id : null;

		// Save the score to the grade book
		$gradebook = new CoursesModelGradeBook($gb_id);
		$gradebook->set('user_id', $user_id);
		$gradebook->set('score', round($grade, 2));
		$gradebook->set('scope', 'course');
		$gradebook->set('scope_id', $course_id);

		if (!$gradebook->store())
		{
			return false;
		}

		// Now, get unit scores
		$score_criteria = $gradePolicy->replacePlaceholders('score_criteria', array('course_id'=>$course_id, 'user_id'=>$user_id));
		$score_criteria = json_decode($score_criteria);

		// Add a few things to the select, from, and group by clauses to correctly calculate unit scores
		$score_criteria->select[] = (object) array('value'=>'cag.unit_id as unit_id');
		$score_criteria->from[]   = (object) array('value'=>'LEFT JOIN #__courses_asset_associations AS caa ON ca.id = caa.asset_id');
		$score_criteria->from[]   = (object) array('value'=>'LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id');
		$score_criteria->group[]  = (object) array('value'=>'cag.unit_id');

		// Compute unit grades
		$grades = $this->_tbl->calculateScore($score_criteria, 'loadObjectList');

		// Now, loop through the course units and save unit scores
		foreach ($grades as $g)
		{
			// First, check to see if a score for this asset and user already exists
			$results = $this->_tbl->find(array('user_id'=>$user_id, 'scope_id'=>$g->unit_id, 'scope'=>'unit'));
			$gb_id   = ($results) ? $results[0]->id : null;

			// Save the score to the grade book
			$gradebook = new CoursesModelGradeBook($gb_id);
			$gradebook->set('user_id', $user_id);
			$gradebook->set('score', round($g->average, 2));
			$gradebook->set('scope', 'unit');
			$gradebook->set('scope_id', $g->unit_id);

			if (!$gradebook->store())
			{
				return false;
			}
		}

		// Success
		return true;
	}

	/**
	 * Method to check for expired exams that students did not take and add 0's to the gradebook as appropriate
	 *
	 * @param      course $course
	 * @param      int $user_id (optional)
	 * @return     void
	 **/
	public function refresh($course, $user_id=null)
	{
		$this->_tbl->syncGrades($course->get('id'), $user_id);

		// Get course section members and update their unit and course numbers
		$members = $course->offering()->section()->members();

		if (is_null($user_id))
		{
			foreach ($members as $m)
			{
				// Compute unit and course scores as well
				$this->calculateScores($m->get('user_id'), $course);
			}
		}
		else
		{
			$this->calculateScores($user_id, $course);
		}
	}

	/**
	 * Determine whether or not a student or group of students is passing
	 *
	 * @param      object $course
	 * @param      int $user_id (optional)
	 * @param      bool $section only (optional)
	 * @return     array
	 **/
	public function passing($course, $section=true, $user_id=null)
	{
		// Get the course id
		if (!is_object($course))
		{
			return false;
		}

		// Get a grade policy object
		$policy  = $course->offering()->section()->get('grade_policy_id');
		$gradePolicy = new CoursesModelGradePolicies($policy);

		// Get the grading policy score criteria
		$grade_criteria = $gradePolicy->replacePlaceholders('grade_criteria', array('course_id'=>$course->get('id')));
		$grade_criteria = json_decode($grade_criteria);
		$grade_criteria->select[] = (object) array('value'=>'cgb.user_id AS user_id');

		// If section only, add appropriate joins to limit by section
		if ($section)
		{
			$grade_criteria->from[]  = (object) array('value'=>'LEFT JOIN #__courses_members AS cm ON cgb.user_id = cm.user_id');
			$grade_criteria->where[] = (object) array('field'=>'cm.section_id','operator'=>'=','value'=>$course->offering()->section()->get('id'));
		}

		// Add the user_id to the query if it's set
		if (!is_null($user_id) && is_numeric($user_id))
		{
			$grade_criteria->where[] = (object) array('field'=>'user_id','operator'=>'=','value'=>$user_id);
		}

		// Get passing data
		$passing = $this->_tbl->calculateScore($grade_criteria, 'loadObjectList', 'user_id');

		return $passing;
	}

	/**
	 * Get count of passing and failing
	 *
	 * @param      obj $course
	 * @param      bool $section only (optional)
	 * @return     array((int)passing, (int)failing)
	 **/
	public function getCountPassingFailing($course, $section=true)
	{
		$rows = $this->passing($course, $section);

		$count_passing = 0;
		$count_failing = 0;

		foreach ($rows as $row)
		{
			if ($row->passing)
			{
				$count_passing++;
			}
			else
			{
				$count_failing++;
			}
		}

		return array('passing'=>$count_passing, 'failing'=>$count_failing);
	}

	/**
	 * Check whether or not the student has earned a badge
	 *
	 * @param      obj $course
	 * @param      int $user_id
	 * @return     bool
	 **/
	public function hasEarnedBadge($course, $user_id)
	{
	}
}