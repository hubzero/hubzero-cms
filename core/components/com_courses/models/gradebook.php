<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Config\Registry;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'grade.book.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'asset.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'asset.views.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'member.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'progress.factors.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'gradepolicies.php');
require_once(__DIR__ . DS . 'memberBadge.php');
require_once(__DIR__ . DS . 'section' . DS .'badge.php');
require_once(__DIR__ . DS . 'form.php');
require_once(__DIR__ . DS . 'formRespondent.php');
require_once(__DIR__ . DS . 'formDeployment.php');

/**
 * Courses model class for grade book
 */
class GradeBook extends Base
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\GradeBook';

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
		if (is_null($member_id))
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
		$results = $this->_grades($filters);

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
					$grades[$grade->member_id]['assets'][$grade->scope_id] = array(
						'score'    => $grade->override,
						'date'     => $grade->override_recorded,
						'override' => true
					);
				}
				else
				{
					$grades[$grade->member_id]['assets'][$grade->scope_id] = array(
						'score'    => $grade->score,
						'date'     => $grade->score_recorded,
						'override' => false
					);
				}
			}
		}

		return $grades;
	}

	/**
	 * Generate summary statistics
	 *
	 * @param      bool  $section - section only?
	 * @return     array $stats
	 */
	public function summaryStats($section = true)
	{
		// Get the grades themselves
		$filters = array('scope'=>'asset', 'course_id'=>$this->course->get('course_id'));

		if ($section)
		{
			$filters['section_id'] = $this->course->offering()->section()->get('id');
		}

		$results = $this->_grades($filters);
		$grades  = array();

		// Restructure data
		foreach ($results as $r)
		{
			if ($r->override)
			{
				$grades[$r->scope_id][] = $r->override;
			}
			else if (!is_null($r->score))
			{
				$grades[$r->scope_id][] = $r->score;
			}
		}

		// Compute stats
		$stats = array();
		foreach ($grades as $asset_id => $grade)
		{
			$stats[$asset_id]['responses'] = count($grade);
			$stats[$asset_id]['average']   = (count($grade) > 0) ? round(array_sum($grade) / count($grade), 2) : NULL;
			$stats[$asset_id]['min']       = (count($grade) > 0) ? round(min($grade)) : NULL;
			$stats[$asset_id]['max']       = (count($grade) > 0) ? round(max($grade)) : NULL;
		}

		return $stats;
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
		static $instances;

		$key = (!is_null($member_id)) ? serialize($member_id) : 'all';

		if (isset($instances[$key]))
		{
			return $instances[$key];
		}

		$offeringParams = new Registry($this->course->offering()->get('params'));
		$sectionParams  = new Registry($this->course->offering()->section()->get('params'));

		$progress_calculation = $this->course->config()->get('progress_calculation', 'all');
		$progress_calculation = ($offeringParams->get('progress_calculation', false)) ? $offeringParams->get('progress_calculation') : $progress_calculation;
		$progress_calculation = ($sectionParams->get('progress_calculation', false)) ? $sectionParams->get('progress_calculation') : $progress_calculation;

		$filters = array(
			'section_id' => $this->course->offering()->section()->get('id'),
			'member_id'  => $member_id
		);

		$dbo = \App::get('db');

		switch ($progress_calculation)
		{
			// Support legacy label of 'forms', as well as new, more accurate label of 'graded'
			case 'forms':
			case 'graded':
				// Get count of graded items taken
				$filters = array('member_id'=>$member_id, 'scope'=>'asset', 'graded'=>true);
				$grades  = $this->_grades($filters);

				$views = array();
				foreach ($grades as $g)
				{
					if (!is_null($g->score) || !is_null($g->override))
					{
						$views[] = (object)array(
							'member_id'    => $g->member_id,
							'grade_weight' => $g->grade_weight,
							'unit_id'      => $g->unit_id,
							'asset_id'     => $g->scope_id
						);
					}
				}
			break;

			case 'manual':
				$filters['progress_calculation'] = true;

				// Get the asset views
				$assetViews = new Tables\AssetViews($dbo);
				$views      = $assetViews->find($filters);
			break;

			case 'videos':
				// Add another filter
				$filters['asset_type'] = 'video';

				// Get the asset views
				$assetViews = new Tables\AssetViews($dbo);
				$views      = $assetViews->find($filters);
			break;

			case 'all':
			default:
				// Get the asset views
				$assetViews = new Tables\AssetViews($dbo);
				$views      = $assetViews->find($filters);
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
		// Note: this is not their score, but rather, simply how many items within the unit they have viewed/completed
		foreach ($progress as $member_id => $m)
		{
			foreach ($m as $unit_id => $unit)
			{
				if (!isset($counts[$unit_id]))
				{
					// Get the assets
					$asset = new Tables\Asset($dbo);
					$filters = array(
						'w' => array(
							'course_id'   => $this->course->get('id'),
							'unit_id'     => $unit_id,
							'state'       => 1,
							'asset_scope' => 'asset_group'
						)
					);

					switch ($progress_calculation)
					{
						// Support legacy label of 'forms', as well as new, more accurate label of 'graded'
						case 'forms':
						case 'graded':
							$filters['w']['graded'] = true;
						break;

						case 'manual':
							$filters['w']['section_id'] = $this->course->offering()->section()->get('id');
							$filters['w']['progress_calculation'] = true;
						break;

						case 'videos':
							$filters['w']['asset_type'] = 'video';
						break;
					}

					$counts[$unit_id] = $asset->count($filters);
					$counts[$unit_id] = $counts[$unit_id] ? $counts[$unit_id] : 1; // Enforce 1. Can't divide by zero.
				}

				$progress[$member_id][$unit_id]['percentage_complete'] = round((array_sum($unit) / $counts[$unit_id]) * 100, 2);
			}
		}

		$instances[$key] = $progress;

		return $progress;
	}

	/**
	 * Get asset views/completions
	 *
	 * @param  (int) $member_id
	 * @return (array)
	 **/
	public function views($member_id)
	{
		$filters = array(
			'section_id' => $this->course->offering()->section()->get('id'),
			'member_id'  => $member_id
		);

		// Get the asset views
		$database   = \App::get('db');
		$assetViews = new Tables\AssetViews($database);
		$results    = $assetViews->find($filters);

		$views = array();

		if ($results && count($results) > 0)
		{
			foreach ($results as $result)
			{
				$views[$result->member_id][] = $result->asset_id;
			}
		}

		return $views;
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
			$asset = new Tables\Asset(\App::get('db'));
			$asset->load($asset_id);
			$course_id = $asset->course_id;

			// Get our course model as well (to retrieve grade policy)
			$course = new Course($course_id);
		}
		else
		{
			// Could not determine course id
			return false;
		}

		if (!is_null($member_id) && !empty($member_id))
		{
			if (!is_array($member_id))
			{
				$member_id = (array) $member_id;
			}
		}
		else
		{
			// Pull all offering members
			$members = $course->offering()->students();
			$member_id = array();

			// Get member id's for refresh filter
			foreach ($members as $member)
			{
				$member_id[] = $member->get('id');
			}
		}

		// Get our units and track which units have grades that might need to be cleared
		$unit_ids = array();
		$units    = $course->offering()->units();

		foreach ($units as $unit)
		{
			$unit_ids[$unit->get('id')] = $member_id;
		}

		// Get a grade policy object
		$gradePolicy = new GradePolicies($course->offering()->section()->get('grade_policy_id'), $course->offering()->section()->get('id'));

		// Calculate course grades, start by getting all grades
		$filters = array('member_id'=>$member_id, 'scope'=>'asset', 'graded'=>true);
		$results = $this->_grades($filters);
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
				$grades[$grade->member_id][$grade->unit_id][$grade->scope_id] = array('score'=>$grade->override, 'weighting'=>$grade->grade_weight);
			}
			else
			{
				$grades[$grade->member_id][$grade->unit_id][$grade->scope_id] = array('score'=>$grade->score, 'weighting'=>$grade->grade_weight);
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
					// We're processing this unit/member, thus it doesn't need to be cleared - so remove it from the list of potentials
					if (isset($unit_ids[$unit_id]) && ($key = array_search($member_id, $unit_ids[$unit_id])) !== false)
					{
						unset($unit_ids[$unit_id][$key]);
					}

					$scores[$member_id]['units'][$unit_id]['exam_count']     = 0;
					$scores[$member_id]['units'][$unit_id]['quiz_count']     = 0;
					$scores[$member_id]['units'][$unit_id]['homework_count'] = 0;
					$scores[$member_id]['units'][$unit_id]['exam_sum']       = 0;
					$scores[$member_id]['units'][$unit_id]['quiz_sum']       = 0;
					$scores[$member_id]['units'][$unit_id]['homework_sum']   = 0;

					foreach ($val as $grade)
					{
						switch ($grade['weighting'])
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

					if ($scores[$member_id]['units'][$unit_id]['exam_count'] > 0)
					{
						$scores[$member_id]['units'][$unit_id]['exam_score']  = $scores[$member_id]['units'][$unit_id]['exam_sum'] / $scores[$member_id]['units'][$unit_id]['exam_count'];
						$scores[$member_id]['units'][$unit_id]['exam_weight'] = ($gradePolicy->get('exam_weight') > 0) ? $gradePolicy->get('exam_weight') : 0;
					}
					else
					{
						$scores[$member_id]['units'][$unit_id]['exam_score']  = null;
						$scores[$member_id]['units'][$unit_id]['exam_weight'] = null;
					}

					if ($scores[$member_id]['units'][$unit_id]['quiz_count'] > 0)
					{
						$scores[$member_id]['units'][$unit_id]['quiz_score']  = $scores[$member_id]['units'][$unit_id]['quiz_sum'] / $scores[$member_id]['units'][$unit_id]['quiz_count'];
						$scores[$member_id]['units'][$unit_id]['quiz_weight'] = ($gradePolicy->get('quiz_weight') > 0) ? $gradePolicy->get('quiz_weight') : 0;
					}
					else
					{
						$scores[$member_id]['units'][$unit_id]['quiz_score']  = null;
						$scores[$member_id]['units'][$unit_id]['quiz_weight'] = null;
					}

					if ($scores[$member_id]['units'][$unit_id]['homework_count'] > 0)
					{
						$scores[$member_id]['units'][$unit_id]['homework_score']  = $scores[$member_id]['units'][$unit_id]['homework_sum'] / $scores[$member_id]['units'][$unit_id]['homework_count'];
						$scores[$member_id]['units'][$unit_id]['homework_weight'] = ($gradePolicy->get('homework_weight') > 0) ? $gradePolicy->get('homework_weight') : 0;
					}
					else
					{
						$scores[$member_id]['units'][$unit_id]['homework_score']  = null;
						$scores[$member_id]['units'][$unit_id]['homework_weight'] = null;
					}

					$numerator = array_sum(
									array(
										$scores[$member_id]['units'][$unit_id]['exam_score']     * $gradePolicy->get('exam_weight'),
										$scores[$member_id]['units'][$unit_id]['quiz_score']     * $gradePolicy->get('quiz_weight'),
										$scores[$member_id]['units'][$unit_id]['homework_score'] * $gradePolicy->get('homework_weight')
									)
								);

					$denominator = array_sum(
									array(
										$scores[$member_id]['units'][$unit_id]['exam_weight'],
										$scores[$member_id]['units'][$unit_id]['quiz_weight'],
										$scores[$member_id]['units'][$unit_id]['homework_weight']
									)
								);

					if ($denominator)
					{
						$scores[$member_id]['units'][$unit_id]['unit_weighted'] = $numerator / $denominator;
					}
					else
					{
						$scores[$member_id]['units'][$unit_id]['unit_weighted'] = NULL;
					}
				}

				// Now calculate overall course scores
				if ($scores[$member_id]['course_exam_count'] > 0)
				{
					$scores[$member_id]['course_exam_score']  = $scores[$member_id]['course_exam_sum'] / $scores[$member_id]['course_exam_count'];
					$scores[$member_id]['course_exam_weight'] = ($gradePolicy->get('exam_weight') > 0) ? $gradePolicy->get('exam_weight') : 0;
				}
				else
				{
					$scores[$member_id]['course_exam_score']  = null;
					$scores[$member_id]['course_exam_weight'] = null;
				}

				if ($scores[$member_id]['course_quiz_count'] > 0)
				{
					$scores[$member_id]['course_quiz_score']  = $scores[$member_id]['course_quiz_sum'] / $scores[$member_id]['course_quiz_count'];
					$scores[$member_id]['course_quiz_weight'] = ($gradePolicy->get('quiz_weight') > 0) ? $gradePolicy->get('quiz_weight') : 0;
				}
				else
				{
					$scores[$member_id]['course_quiz_score']  = null;
					$scores[$member_id]['course_quiz_weight'] = null;
				}

				if ($scores[$member_id]['course_homework_count'] > 0)
				{
					$scores[$member_id]['course_homework_score']  = $scores[$member_id]['course_homework_sum'] / $scores[$member_id]['course_homework_count'];
					$scores[$member_id]['course_homework_weight'] = ($gradePolicy->get('homework_weight') > 0) ? $gradePolicy->get('homework_weight') : 0;
				}
				else
				{
					$scores[$member_id]['course_homework_score']  = null;
					$scores[$member_id]['course_homework_weight'] = null;
				}

				$numerator = array_sum(
								array(
									$scores[$member_id]['course_exam_score']     * $gradePolicy->get('exam_weight'),
									$scores[$member_id]['course_quiz_score']     * $gradePolicy->get('quiz_weight'),
									$scores[$member_id]['course_homework_score'] * $gradePolicy->get('homework_weight')
								)
							);

				$denominator = array_sum(
								array(
									$scores[$member_id]['course_exam_weight'],
									$scores[$member_id]['course_quiz_weight'],
									$scores[$member_id]['course_homework_weight']
								)
							);

				if ($denominator)
				{
					$scores[$member_id]['course_weighted'] = $numerator / $denominator;
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
		$this->_tbl->clearUnits($unit_ids);

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
		$gradePolicy = new GradePolicies($this->course->offering()->section()->get('grade_policy_id'), $this->course->offering()->section()->get('id'));

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
	 * Check whether or not the student is passing the course and has completed all items
	 *
	 * @param      int $member_id
	 * @return     bool
	 **/
	public function isEligibleForRecognition($member_id=null)
	{
		static $assets = null;
		static $grades = null;

		// Get a grade policy object
		$gradePolicy = new GradePolicies($this->course->offering()->section()->get('grade_policy_id'), $this->course->offering()->section()->get('id'));

		if (!isset($assets))
		{
			// Get the graded assets
			$asset  = new Tables\Asset(\App::get('db'));
			$assets = $asset->find(
				array(
					'w' => array(
						'course_id'   => $this->course->get('id'),
						'section_id'  => $this->course->offering()->section()->get('id'),
						'offering_id' => $this->course->offering()->get('id'),
						'graded'      => true,
						'state'       => 1,
						'asset_scope' => 'asset_group'
					),
					'order_by'  => 'title',
					'order_dir' => 'ASC'
				)
			);

			// Get gradebook auxiliary assets
			$auxiliary = $asset->findByScope(
				'offering',
				$this->course->offering()->get('id'),
				array(
					'asset_type'    => 'gradebook',
					'asset_subtype' => 'auxiliary',
					'graded'        => true,
					'state'         => 1
				)
			);

			$assets = array_merge($assets, $auxiliary);
		}

		// Get totals by type
		$totals = array('exam'=>0, 'quiz'=>0, 'homework'=>0);
		$counts = array();

		if ($assets && count($assets) > 0)
		{
			foreach ($assets as $asset)
			{
				++$totals[$asset->grade_weight];
			}
		}

		if (!isset($grades))
		{
			// Get count of graded items taken
			$filters = array('member_id'=>$member_id, 'scope'=>'asset', 'graded'=>true);
			$grades = $this->_grades($filters);
		}

		// Restructure data
		foreach ($grades as $g)
		{
			if (!is_null($g->score) || !is_null($g->override))
			{
				if (isset($counts[$g->member_id][$g->grade_weight]))
				{
					++$counts[$g->member_id][$g->grade_weight];
				}
				else
				{
					$counts[$g->member_id][$g->grade_weight] = 1;
				}
			}
		}

		// Get weights to determine what counts toward the final grade
		$exam_weight     = $gradePolicy->get('exam_weight');
		$quiz_weight     = $gradePolicy->get('quiz_weight');
		$homework_weight = $gradePolicy->get('homework_weight');

		$return = false;

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
					($exam_weight     == 0 || ($exam_weight     > 0 && isset($counts[$m]['exam'])     && $totals['exam']     == $counts[$m]['exam']))     &&
					($quiz_weight     == 0 || ($quiz_weight     > 0 && isset($counts[$m]['quiz'])     && $totals['quiz']     == $counts[$m]['quiz']))     &&
					($homework_weight == 0 || ($homework_weight > 0 && isset($counts[$m]['homework']) && $totals['homework'] == $counts[$m]['homework'])) &&
					$passing[$m]
					)
				{
					$return[] = $m;
				}
			}
		}

		return $return;
	}

	/**
	 * Check whether or not the student(s) have earned a badge
	 *
	 * @param      int $member_id
	 * @return     bool
	 **/
	public function hasEarnedBadge($member_id=null)
	{
		// Check whether or not they're eligable for a badge at this point
		// First, does this course even offers a badge
		if ($this->course->offering()->section()->badge()->isAvailable())
		{
			$members = $this->isEligibleForRecognition($member_id);

			if ($members && count($members) > 0)
			{
				foreach ($members as $m)
				{
					// Mark student as having earned badge
					$badge = MemberBadge::loadByMemberId($m);
					$sb    = Section\Badge::loadBySectionId($this->course->offering()->section()->get('id'));
					if (is_object($badge) && !$badge->hasEarned())
					{
						$badge->set('member_id', $m);
						$badge->set('section_badge_id', $sb->get('id'));
						$badge->set('earned', 1);
						$badge->set('earned_on', \Date::toSql());
						$badge->set('criteria_id', $sb->get('criteria_id'));
						$badge->store();

						// Get courses config
						$cconfig = \Component::params('com_courses');

						// Tell the badge provider that they've earned the badge
						$request_type   = $cconfig->get('badges_request_type', 'oauth');
						$badgesHandler  = new \Hubzero\Badges\Wallet(strtoupper($sb->get('provider_name')), $request_type);
						$badgesProvider = $badgesHandler->getProvider();

						$credentials = new \stdClass();
						$credentials->consumer_key    = $this->config()->get($sb->get('provider_name').'_consumer_key');
						$credentials->consumer_secret = $this->config()->get($sb->get('provider_name').'_consumer_secret');
						$credentials->clientId        = $this->config()->get($sb->get('provider_name').'_client_id');
						$badgesProvider->setCredentials($credentials);

						$dbo = \App::get('db');
						$memberTbl = new Tables\Member($dbo);
						$memberTbl->loadByMemberId($m);
						$user_id = $memberTbl->get('user_id');

						$data               = new \stdClass();
						$data->id           = $sb->get('provider_badge_id');
						$data->evidenceUrl  = rtrim(\Request::root(), '/') . '/courses/badge/' . $sb->get('id') . '/validation/' . $badge->get('validation_token');
						$users              = array();
						$users[]            = \User::getInstance($user_id)->get('email');

						// Publish assertion
						$badgesProvider->grantBadge($data, $users);
					}
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get grades for this course
	 *
	 * @return array
	 **/
	private function _grades($filters=array())
	{
		static $grades;
		$key = serialize($filters);

		if (!isset($grades[$key]))
		{
			$asset_ids = array();
			$units_map = array();

			if ($this->course->offering()->assets() && count($this->course->offering()->assets()) > 0)
			{
				foreach ($this->course->offering()->assets() as $a)
				{
					$asset_ids[] = $a->get('id');
				}
			}

			// Get asset ids
			if ($this->course->offering()->units())
			{
				foreach ($this->course->offering()->units() as $unit)
				{
					foreach ($unit->assetgroups() as $agt)
					{
						foreach ($agt->children() as $ag)
						{
							foreach ($ag->assets() as $a)
							{
								if ($a->isPublished())
								{
									$units_map[$a->get('id')] = $unit->get('id');
									$asset_ids[] = $a->get('id');
								}
							}
						}
					}
				}
			}

			$results = $this->_tbl->find($filters);

			if ($results && count($results) > 0)
			{
				foreach ($results as $grade)
				{
					$grade->unit_id = isset($units_map[$grade->scope_id]) ? $units_map[$grade->scope_id] : 0;
				}
			}

			$grades[$key] = $results;
		}

		return $grades[$key];
	}
}