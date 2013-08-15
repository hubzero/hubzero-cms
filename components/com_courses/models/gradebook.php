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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'memberBadge.php');
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
				if ($grade->override)
				{
					$grades[$grade->user_id]['assets'][$grade->scope_id] = array('score'=>$grade->override, 'override'=>true);
				}
				else
				{
					$grades[$grade->user_id]['assets'][$grade->scope_id] = array('score'=>$grade->score, 'override'=>false);
				}
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

		// Calculate course grades, start by getting all grades
		$filters = array('scope'=>'asset', 'user_id'=>$user_id, 'course_id'=>$course_id);
		$results = $this->_tbl->find($filters);
		$grades  = array();
		$scores  = array();

		foreach ($results as $grade)
		{
			if (is_null($grade->score) && is_null($grade->override))
			{
				continue;
			}

			// Check for overrides
			if ($grade->override)
			{
				$grades[$grade->user_id][$grade->unit_id][$grade->scope_id] = array('score'=>$grade->override, 'type'=>$grade->subtype);
			}
			else
			{
				$grades[$grade->user_id][$grade->unit_id][$grade->scope_id] = array('score'=>$grade->score, 'type'=>$grade->subtype);
			}
		}

		if (count($grades) > 0)
		{
			foreach ($grades as $user_id=>$values)
			{
				$scores[$user_id]['course_exam_count']     = 0;
				$scores[$user_id]['course_quiz_count']     = 0;
				$scores[$user_id]['course_homework_count'] = 0;
				$scores[$user_id]['course_exam_sum']       = 0;
				$scores[$user_id]['course_quiz_sum']       = 0;
				$scores[$user_id]['course_homework_sum']   = 0;

				// Loop through units and compute scores
				foreach ($values as $unit_id=>$val)
				{
					$scores[$user_id]['units'][$unit_id]['exam_count']     = 0;
					$scores[$user_id]['units'][$unit_id]['quiz_count']     = 0;
					$scores[$user_id]['units'][$unit_id]['homework_count'] = 0;
					$scores[$user_id]['units'][$unit_id]['exam_sum']       = 0;
					$scores[$user_id]['units'][$unit_id]['quiz_sum']       = 0;
					$scores[$user_id]['units'][$unit_id]['homework_sum']   = 0;

					foreach ($val as $grade)
					{
						switch ($grade['type'])
						{
							case 'exam':
								$scores[$user_id]['course_exam_count']++;
								$scores[$user_id]['course_exam_sum'] += $grade['score'];
								$scores[$user_id]['units'][$unit_id]['exam_count']++;
								$scores[$user_id]['units'][$unit_id]['exam_sum'] += $grade['score'];
							break;
							case 'quiz':
								$scores[$user_id]['course_quiz_count']++;
								$scores[$user_id]['course_quiz_sum'] += $grade['score'];
								$scores[$user_id]['units'][$unit_id]['quiz_count']++;
								$scores[$user_id]['units'][$unit_id]['quiz_sum'] += $grade['score'];
							break;
							case 'homework':
								$scores[$user_id]['course_homework_count']++;
								$scores[$user_id]['course_homework_sum'] += $grade['score'];
								$scores[$user_id]['units'][$unit_id]['homework_count']++;
								$scores[$user_id]['units'][$unit_id]['homework_sum'] += $grade['score'];
							break;
						}
					}

					$hasScores = false;

					if ($scores[$user_id]['units'][$unit_id]['exam_count'] > 0)
					{
						$scores[$user_id]['units'][$unit_id]['exam_score']    = round(($scores[$user_id]['units'][$unit_id]['exam_sum'] / $scores[$user_id]['units'][$unit_id]['exam_count']), 2);
						$scores[$user_id]['units'][$unit_id]['exam_weighted'] = round($scores[$user_id]['units'][$unit_id]['exam_score'] * $gradePolicy->get('exam_weight'), 2);

						if ($gradePolicy->get('exam_weight') > 0)
						{
							$hasScores = true;
						}
					}
					else
					{
						$scores[$user_id]['units'][$unit_id]['exam_score']    = null;
						$scores[$user_id]['units'][$unit_id]['exam_weighted'] = $gradePolicy->get('exam_weight') * 100;
					}
					if ($scores[$user_id]['units'][$unit_id]['quiz_count'] > 0)
					{
						$scores[$user_id]['units'][$unit_id]['quiz_score']    = round(($scores[$user_id]['units'][$unit_id]['quiz_sum'] / $scores[$user_id]['units'][$unit_id]['quiz_count']), 2);
						$scores[$user_id]['units'][$unit_id]['quiz_weighted'] = round($scores[$user_id]['units'][$unit_id]['quiz_score'] * $gradePolicy->get('quiz_weight'), 2);

						if ($gradePolicy->get('quiz_weight') > 0)
						{
							$hasScores = true;
						}
					}
					else
					{
						$scores[$user_id]['units'][$unit_id]['quiz_score']    = null;
						$scores[$user_id]['units'][$unit_id]['quiz_weighted'] = $gradePolicy->get('quiz_weight') * 100;
					}
					if ($scores[$user_id]['units'][$unit_id]['homework_count'] > 0)
					{
						$scores[$user_id]['units'][$unit_id]['homework_score']    = round(($scores[$user_id]['units'][$unit_id]['homework_sum'] / $scores[$user_id]['units'][$unit_id]['homework_count']), 2);
						$scores[$user_id]['units'][$unit_id]['homework_weighted'] = round($scores[$user_id]['units'][$unit_id]['homework_score'] * $gradePolicy->get('homework_weight'), 2);

						if ($gradePolicy->get('homework_weight') > 0)
						{
							$hasScores = true;
						}
					}
					else
					{
						$scores[$user_id]['units'][$unit_id]['homework_score']    = null;
						$scores[$user_id]['units'][$unit_id]['homework_weighted'] = $gradePolicy->get('homework_weight') * 100;
					}

					if ($hasScores)
					{
						// Finally, compute unit weighted score
						$scores[$user_id]['units'][$unit_id]['unit_weighted']     =
							$scores[$user_id]['units'][$unit_id]['exam_weighted'] +
							$scores[$user_id]['units'][$unit_id]['quiz_weighted'] +
							$scores[$user_id]['units'][$unit_id]['homework_weighted'];
					}
					else
					{
						$scores[$user_id]['units'][$unit_id]['unit_weighted'] = NULL;
					}
				}

				$hasScores = false;

				// Now calculate overall course scores
				if ($scores[$user_id]['course_exam_count'] > 0)
				{
					$scores[$user_id]['course_exam_score']    = round(($scores[$user_id]['course_exam_sum'] / $scores[$user_id]['course_exam_count']), 2);
					$scores[$user_id]['course_exam_weighted'] = round($scores[$user_id]['course_exam_score'] * $gradePolicy->get('exam_weight'), 2);

					if ($gradePolicy->get('exam_weight') > 0)
					{
						$hasScores = true;
					}
				}
				else
				{
					$scores[$user_id]['course_exam_score']    = null;
					$scores[$user_id]['course_exam_weighted'] = $gradePolicy->get('exam_weight') * 100;
				}
				if ($scores[$user_id]['course_quiz_count'] > 0)
				{
					$scores[$user_id]['course_quiz_score']    = round(($scores[$user_id]['course_quiz_sum'] / $scores[$user_id]['course_quiz_count']), 2);
					$scores[$user_id]['course_quiz_weighted'] = round($scores[$user_id]['course_quiz_score'] * $gradePolicy->get('quiz_weight'), 2);

					if ($gradePolicy->get('quiz_weight') > 0)
					{
						$hasScores = true;
					}
				}
				else
				{
					$scores[$user_id]['course_quiz_score']    = null;
					$scores[$user_id]['course_quiz_weighted'] = $gradePolicy->get('quiz_weight') * 100;
				}
				if ($scores[$user_id]['course_homework_count'] > 0)
				{
					$scores[$user_id]['course_homework_score']    = round(($scores[$user_id]['course_homework_sum'] / $scores[$user_id]['course_homework_count']), 2);
					$scores[$user_id]['course_homework_weighted'] = round($scores[$user_id]['course_homework_score'] * $gradePolicy->get('homework_weight'), 2);

					if ($gradePolicy->get('homework_weight') > 0)
					{
						$hasScores = true;
					}
				}
				else
				{
					$scores[$user_id]['course_homework_score']    = null;
					$scores[$user_id]['course_homework_weighted'] = $gradePolicy->get('homework_weight') * 100;
				}

				if ($hasScores)
				{
					// Get course weighted average
					$scores[$user_id]['course_weighted']          =
						$scores[$user_id]['course_exam_weighted'] +
						$scores[$user_id]['course_quiz_weighted'] +
						$scores[$user_id]['course_homework_weighted'];
				}
				else
				{
					$scores[$user_id]['course_weighted'] = NULL;
				}
			}
		}
		else
		{
			// Make sure nothing is lingering around...given that there shouldn't be any grades there
			$this->_tbl->clearGrades($user_id, $course);
		}

		$this->_tbl->saveGrades($scores, $course_id);

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
		$this->_tbl->syncGrades($this->course, $user_id);

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

		$passing = array();
		$filters = array('scope'=>'course', 'scope_id'=>$this->course->get('id'));

		// Get a grade policy object
		$gradePolicy = new CoursesModelGradePolicies($this->course->offering()->section()->get('grade_policy_id'));

		// If section only, add appropriate joins to limit by section
		if ($section)
		{
			// Only compute section
			$filters['section_id'] = $this->course->offering()->section()->get('id');
		}

		// Add the user_id to the query if it's set
		if (!is_null($user_id) && is_numeric($user_id))
		{
			// Only include requested user_id
			$filters['user_id'] = $user_id;
		}

		// Calculate course passing info
		$results = $this->_tbl->passing($filters, 'user_id');

		foreach ($results as $result)
		{
			$passing[$result->user_id] = ($result->score >= $gradePolicy->get('threshold') * 100) ? 1 : 0;
		}

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
				if ($r === 1)
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
				if ($r === 0)
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
	 * Check whether or not the student(s) have earned a badge
	 *
	 * @param      int $user_id
	 * @return     bool
	 **/
	public function hasEarnedBadge($user_id=null)
	{
		// Check whether or not their eligable for a badge at this point
		// First, does this course even offers a badge
		if (!is_null($this->course->offering()->badge()->get('id')))
		{
			// Get a grade policy object
			$gradePolicy = new CoursesModelGradePolicies($this->course->offering()->section()->get('grade_policy_id'));

			// Get count of forms take
			$results = $this->_tbl->getFormCompletionCount($this->course->get('id'), $user_id);

			// Restructure data
			foreach ($results as $r)
			{
				$counts[$r->user_id][$r->subtype] = $r->count;
			}

			// Get weights to determine what counts toward the final grade
			$exam_weight     = $gradePolicy->get('exam_weight');
			$quiz_weight     = $gradePolicy->get('quiz_weight');
			$homework_weight = $gradePolicy->get('homework_weight');

			// Get count of total forms
			$totals = $this->_tbl->getFormCount();

			if (isset($counts))
			{
				if (!is_null($user_id) && !is_array($user_id))
				{
					$user_id = (array)$user_id;
				}
				else
				{
					$user_id = array();
					foreach ($this->course->offering()->section()->members() as $m)
					{
						$user_id[] = $m->get('id');
					}
				}

				// Loop though the users
				foreach ($user_id as $u)
				{
					$passing = $this->passing(true, $u);

					// Now make sure they've taken all required exams/quizzes/homeworks, and that they passed
					if (
						($exam_weight     == 0 || ($exam_weight     > 0 && $totals['exam']->count     == $counts[$u]['exam']))     &&
						($quiz_weight     == 0 || ($quiz_weight     > 0 && $totals['quiz']->count     == $counts[$u]['quiz']))     &&
						($homework_weight == 0 || ($homework_weight > 0 && $totals['homework']->count == $counts[$u]['homework'])) &&
						$passing[$u]
						)
					{
						// Mark student as having earned badge
						$member_id = $this->course->offering()->section()->member($u)->get('id');
						$badge = CoursesModelMemberBadge::loadByMemberId($member_id);
						if (!$badge->hasEarned())
						{
							$badge->set('member_id', $member_id);
							$badge->set('earned', 1);
							$badge->set('earned_on', date("Y-m-d H:i:s"));
							$badge->store();
						}
					}
				}
			}
		}
		else
		{
			return false;
		}
	}
}