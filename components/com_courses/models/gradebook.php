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
	 * @param      int or array $member_id, user id for which to pull grades
	 * @return     array $grades
	 */
	public function grades($scope=null, $member_id=null)
	{
		// If no user provided, assume section
		if(is_null($member_id))
		{
			$members = $this->course->offering()->section()->members();

			$member_id = array();

			foreach ($members as $m)
			{
				$member_id[] = $m->get('id');
			}
		}

		// Get the grades themselves
		$filters = array('member_id'=>$member_id, 'scope'=>$scope);
		$results = $this->_tbl->find($filters);

		$grades = array();

		// Restructure data
		foreach ($results as $grade)
		{
			if ($grade->scope == 'unit')
			{
				$grades[$grade->member_id]['units'][$grade->scope_id] = $grade->score;
			}
			if ($grade->scope == 'course')
			{
				$grades[$grade->member_id]['course'][$grade->scope_id] = $grade->score;
			}
			if ($grade->scope == 'asset')
			{
				if ($grade->override)
				{
					$grades[$grade->member_id]['assets'][$grade->scope_id] = array('score'=>$grade->override, 'override'=>true);
				}
				else
				{
					$grades[$grade->member_id]['assets'][$grade->scope_id] = array('score'=>$grade->score, 'override'=>false);
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
	 * @param      int $member_id 
	 * @return     array $progress
	 */
	public function progress($member_id=null)
	{
		$progress_calculation = $this->course->config()->get('progress_calculation', 'all');

		switch ($progress_calculation)
		{
			case 'forms':
				$views = $this->_tbl->getFormCompletions($this->course->get('id'), $member_id);
			break;

			default:
				// Get the asset views
				$assetViews = new CoursesTableAssetViews(JFactory::getDBO());
				$filters    = array(
					'section_id' => $this->course->offering()->section()->get('id'),
					'member_id'  => $member_id
				);

				$views = $assetViews->find($filters);
			break;
		}

		$progress = array();

		// Restructure array
		foreach ($views as $v)
		{
			$progress[$v->member_id][$v->unit_id][$v->asset_id] = 1;
		}

		$counts = array();

		// Calculate unit completion percentage for each student
		// Note: this is not their score, but rather, simply how many items within the unit they have viewed
		foreach ($progress as $member_id=>$m)
		{
			foreach ($m as $unit_id=>$unit)
			{
				if (!isset($counts[$unit_id]))
				{
					// Get the assets
					$asset = new CoursesTableAsset(JFactory::getDBO());
					$filters = array(
						'w' => array(
							'course_id'  => $this->course->get('id'),
							'unit_id'    => $unit_id,
							'state'      => 1
						)
					);
					if ($progress_calculation == 'forms')
					{
						$filters['w']['asset_type'] = 'form';
					}
					$counts[$unit_id] = $asset->count($filters);
				}

				$progress[$member_id][$unit_id]['percentage_complete'] = round((array_sum($unit) / $counts[$unit_id]) * 100, 2);
			}
		}

		return $progress;
	}

	/**
	 * Calculate scores for each unit and the course as a whole
	 * 
	 * @param      int $member_id
	 * @param      int $asset_id
	 * @return     boolean true on success, false otherwise
	 */
	public function calculateScores($member_id=null, $asset_id=null)
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
		$filters = array('scope'=>'asset', 'member_id'=>$member_id, 'course_id'=>$course_id);
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
				$grades[$grade->member_id][$grade->unit_id][$grade->scope_id] = array('score'=>$grade->override, 'type'=>$grade->subtype);
			}
			else
			{
				$grades[$grade->member_id][$grade->unit_id][$grade->scope_id] = array('score'=>$grade->score, 'type'=>$grade->subtype);
			}
		}

		if (count($grades) > 0)
		{
			foreach ($grades as $member_id=>$values)
			{
				$scores[$member_id]['course_exam_count']     = 0;
				$scores[$member_id]['course_quiz_count']     = 0;
				$scores[$member_id]['course_homework_count'] = 0;
				$scores[$member_id]['course_exam_sum']       = 0;
				$scores[$member_id]['course_quiz_sum']       = 0;
				$scores[$member_id]['course_homework_sum']   = 0;

				// Loop through units and compute scores
				foreach ($values as $unit_id=>$val)
				{
					$scores[$member_id]['units'][$unit_id]['exam_count']     = 0;
					$scores[$member_id]['units'][$unit_id]['quiz_count']     = 0;
					$scores[$member_id]['units'][$unit_id]['homework_count'] = 0;
					$scores[$member_id]['units'][$unit_id]['exam_sum']       = 0;
					$scores[$member_id]['units'][$unit_id]['quiz_sum']       = 0;
					$scores[$member_id]['units'][$unit_id]['homework_sum']   = 0;

					foreach ($val as $grade)
					{
						switch ($grade['type'])
						{
							case 'exam':
								$scores[$member_id]['course_exam_count']++;
								$scores[$member_id]['course_exam_sum'] += $grade['score'];
								$scores[$member_id]['units'][$unit_id]['exam_count']++;
								$scores[$member_id]['units'][$unit_id]['exam_sum'] += $grade['score'];
							break;
							case 'quiz':
								$scores[$member_id]['course_quiz_count']++;
								$scores[$member_id]['course_quiz_sum'] += $grade['score'];
								$scores[$member_id]['units'][$unit_id]['quiz_count']++;
								$scores[$member_id]['units'][$unit_id]['quiz_sum'] += $grade['score'];
							break;
							case 'homework':
								$scores[$member_id]['course_homework_count']++;
								$scores[$member_id]['course_homework_sum'] += $grade['score'];
								$scores[$member_id]['units'][$unit_id]['homework_count']++;
								$scores[$member_id]['units'][$unit_id]['homework_sum'] += $grade['score'];
							break;
						}
					}

					$hasScores = false;

					if ($scores[$member_id]['units'][$unit_id]['exam_count'] > 0)
					{
						$scores[$member_id]['units'][$unit_id]['exam_score']    = round(($scores[$member_id]['units'][$unit_id]['exam_sum'] / $scores[$member_id]['units'][$unit_id]['exam_count']), 2);
						$scores[$member_id]['units'][$unit_id]['exam_weighted'] = round($scores[$member_id]['units'][$unit_id]['exam_score'] * $gradePolicy->get('exam_weight'), 2);

						if ($gradePolicy->get('exam_weight') > 0)
						{
							$hasScores = true;
						}
					}
					else
					{
						$scores[$member_id]['units'][$unit_id]['exam_score']    = null;
						$scores[$member_id]['units'][$unit_id]['exam_weighted'] = $gradePolicy->get('exam_weight') * 100;
					}
					if ($scores[$member_id]['units'][$unit_id]['quiz_count'] > 0)
					{
						$scores[$member_id]['units'][$unit_id]['quiz_score']    = round(($scores[$member_id]['units'][$unit_id]['quiz_sum'] / $scores[$member_id]['units'][$unit_id]['quiz_count']), 2);
						$scores[$member_id]['units'][$unit_id]['quiz_weighted'] = round($scores[$member_id]['units'][$unit_id]['quiz_score'] * $gradePolicy->get('quiz_weight'), 2);

						if ($gradePolicy->get('quiz_weight') > 0)
						{
							$hasScores = true;
						}
					}
					else
					{
						$scores[$member_id]['units'][$unit_id]['quiz_score']    = null;
						$scores[$member_id]['units'][$unit_id]['quiz_weighted'] = $gradePolicy->get('quiz_weight') * 100;
					}
					if ($scores[$member_id]['units'][$unit_id]['homework_count'] > 0)
					{
						$scores[$member_id]['units'][$unit_id]['homework_score']    = round(($scores[$member_id]['units'][$unit_id]['homework_sum'] / $scores[$member_id]['units'][$unit_id]['homework_count']), 2);
						$scores[$member_id]['units'][$unit_id]['homework_weighted'] = round($scores[$member_id]['units'][$unit_id]['homework_score'] * $gradePolicy->get('homework_weight'), 2);

						if ($gradePolicy->get('homework_weight') > 0)
						{
							$hasScores = true;
						}
					}
					else
					{
						$scores[$member_id]['units'][$unit_id]['homework_score']    = null;
						$scores[$member_id]['units'][$unit_id]['homework_weighted'] = $gradePolicy->get('homework_weight') * 100;
					}

					if ($hasScores)
					{
						// Finally, compute unit weighted score
						$scores[$member_id]['units'][$unit_id]['unit_weighted']     =
							$scores[$member_id]['units'][$unit_id]['exam_weighted'] +
							$scores[$member_id]['units'][$unit_id]['quiz_weighted'] +
							$scores[$member_id]['units'][$unit_id]['homework_weighted'];
					}
					else
					{
						$scores[$member_id]['units'][$unit_id]['unit_weighted'] = NULL;
					}
				}

				$hasScores = false;

				// Now calculate overall course scores
				if ($scores[$member_id]['course_exam_count'] > 0)
				{
					$scores[$member_id]['course_exam_score']    = round(($scores[$member_id]['course_exam_sum'] / $scores[$member_id]['course_exam_count']), 2);
					$scores[$member_id]['course_exam_weighted'] = round($scores[$member_id]['course_exam_score'] * $gradePolicy->get('exam_weight'), 2);

					if ($gradePolicy->get('exam_weight') > 0)
					{
						$hasScores = true;
					}
				}
				else
				{
					$scores[$member_id]['course_exam_score']    = null;
					$scores[$member_id]['course_exam_weighted'] = $gradePolicy->get('exam_weight') * 100;
				}
				if ($scores[$member_id]['course_quiz_count'] > 0)
				{
					$scores[$member_id]['course_quiz_score']    = round(($scores[$member_id]['course_quiz_sum'] / $scores[$member_id]['course_quiz_count']), 2);
					$scores[$member_id]['course_quiz_weighted'] = round($scores[$member_id]['course_quiz_score'] * $gradePolicy->get('quiz_weight'), 2);

					if ($gradePolicy->get('quiz_weight') > 0)
					{
						$hasScores = true;
					}
				}
				else
				{
					$scores[$member_id]['course_quiz_score']    = null;
					$scores[$member_id]['course_quiz_weighted'] = $gradePolicy->get('quiz_weight') * 100;
				}
				if ($scores[$member_id]['course_homework_count'] > 0)
				{
					$scores[$member_id]['course_homework_score']    = round(($scores[$member_id]['course_homework_sum'] / $scores[$member_id]['course_homework_count']), 2);
					$scores[$member_id]['course_homework_weighted'] = round($scores[$member_id]['course_homework_score'] * $gradePolicy->get('homework_weight'), 2);

					if ($gradePolicy->get('homework_weight') > 0)
					{
						$hasScores = true;
					}
				}
				else
				{
					$scores[$member_id]['course_homework_score']    = null;
					$scores[$member_id]['course_homework_weighted'] = $gradePolicy->get('homework_weight') * 100;
				}

				if ($hasScores)
				{
					// Get course weighted average
					$scores[$member_id]['course_weighted']          =
						$scores[$member_id]['course_exam_weighted'] +
						$scores[$member_id]['course_quiz_weighted'] +
						$scores[$member_id]['course_homework_weighted'];
				}
				else
				{
					$scores[$member_id]['course_weighted'] = NULL;
				}
			}
		}
		else
		{
			// Make sure nothing is lingering around...given that there shouldn't be any grades there
			$this->_tbl->clearGrades($member_id, $course);
		}

		$this->_tbl->saveGrades($scores, $course_id);

		// Success
		return true;
	}

	/**
	 * Method to check for expired exams that students did not take and add 0's to the gradebook as appropriate
	 *
	 * @param      int $member_id (optional)
	 * @return     void
	 **/
	public function refresh($member_id=null)
	{
		$this->_tbl->syncGrades($this->course, $member_id);

		// Compute unit and course scores as well
		$this->calculateScores($member_id);
	}

	/**
	 * Determine whether or not a student or group of students is passing
	 *
	 * @param      int $member_id (optional)
	 * @param      bool $section only (optional)
	 * @return     array
	 **/
	public function passing($section=true, $member_id=null)
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

		// Add the member_id to the query if it's set
		if (!is_null($member_id) && is_numeric($member_id))
		{
			// Only include requested member_id
			$filters['member_id'] = $member_id;
		}

		// Calculate course passing info
		$results = $this->_tbl->passing($filters, 'member_id');

		foreach ($results as $result)
		{
			if (!is_null($result->score))
			{
				$passing[$result->member_id] = ($result->score >= $gradePolicy->get('threshold') * 100) ? 1 : 0;
			}
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
	 * @param      int $member_id
	 * @return     bool
	 **/
	public function hasEarnedBadge($member_id=null)
	{
		// Check whether or not their eligable for a badge at this point
		// First, does this course even offers a badge
		if (!is_null($this->course->offering()->badge()->get('id')))
		{
			// Get a grade policy object
			$gradePolicy = new CoursesModelGradePolicies($this->course->offering()->section()->get('grade_policy_id'));

			// Get count of forms take
			$results = $this->_tbl->getFormCompletionCount($this->course->get('id'), $member_id);

			// Restructure data
			foreach ($results as $r)
			{
				$counts[$r->member_id][$r->subtype] = $r->count;
			}

			// Get weights to determine what counts toward the final grade
			$exam_weight     = $gradePolicy->get('exam_weight');
			$quiz_weight     = $gradePolicy->get('quiz_weight');
			$homework_weight = $gradePolicy->get('homework_weight');

			// Get count of total forms
			$totals = $this->_tbl->getFormCount();

			if (isset($counts))
			{
				if (!is_null($member_id) && !is_array($member_id))
				{
					$member_id = (array)$member_id;
				}
				else
				{
					$member_id = array();
					foreach ($this->course->offering()->section()->members() as $m)
					{
						$member_id[] = $m->get('id');
					}
				}

				// Loop though the users
				foreach ($member_id as $m)
				{
					$passing = $this->passing(true, $m);

					// Now make sure they've taken all required exams/quizzes/homeworks, and that they passed
					if (
						($exam_weight     == 0 || ($exam_weight     > 0 && $totals['exam']->count     == $counts[$m]['exam']))     &&
						($quiz_weight     == 0 || ($quiz_weight     > 0 && $totals['quiz']->count     == $counts[$m]['quiz']))     &&
						($homework_weight == 0 || ($homework_weight > 0 && $totals['homework']->count == $counts[$m]['homework'])) &&
						$passing[$m]
						)
					{
						// Mark student as having earned badge
						$badge = CoursesModelMemberBadge::loadByMemberId($member_id);
						if (!$badge->hasEarned())
						{
							$badge->set('member_id', $member_id);
							$badge->set('earned', 1);
							$badge->set('earned_on', date("Y-m-d H:i:s"));
							$badge->store();

							// Tell passport
							/*ximport('Hubzero_Badges_Passport_BadgesProvider');
							$badgeInfo->id = $this->course->offering()->badge()->get('id');
							$badgeInfo->evidenceUrl = '';
							Hubzero_Badges_Passport_BadgesProvider::grantBadge($badgeInfo, JFactory::getUser($m)->get('email'));*/
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