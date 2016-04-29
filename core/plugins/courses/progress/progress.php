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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradepolicies.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradebook.php');

/**
 * Courses Plugin class for user progress
 */
class plgCoursesProgress extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param   object   $course    Current course
	 * @param   object   $offering  Name of the component
	 * @param   boolean  $describe  Return plugin description only?
	 * @return  object
	 */
	public function onCourse($course, $offering, $describe=false)
	{
		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)))
			->set('description', Lang::txt('PLG_COURSES_' . strtoupper($this->_name) . '_BLURB'))
			->set('default_access', $this->params->get('plugin_access', 'members'))
			->set('display_menu_tab', true)
			->set('icon', 'f012');

		if ($describe)
		{
			return $response;
		}

		if (!($active = Request::getVar('active')))
		{
			Request::setVar('active', ($active = $this->_name));
		}

		if ($response->get('name') != $active)
		{
			return $response;
		}

		// Check to see if user is member and plugin access requires members
		if (!$course->offering()->section()->access('view'))
		{
			$view = new \Hubzero\Plugin\View(array(
				'folder'  => 'courses',
				'element' => 'outline',
				'name'    => 'shared',
				'layout'  => '_not_enrolled'
			));

			$view->set('course', $course)
			     ->set('option', 'com_courses')
			     ->set('message', 'You must be enrolled to utilize the progress feature.');

			$response->set('html', $view->__toString());
			return $response;
		}

		$this->member = $course->offering()->section()->member(User::get('id'));
		$this->course = $course;
		$this->base   = $course->offering()->link();
		$this->db     = App::get('db');

		// Instantiate a vew
		$this->view = $this->view('student', 'report');
		$this->view->course  = $course;
		$this->view->member  = $this->member;
		$this->view->option  = 'com_courses';
		$this->view->base    = $this->base;

		switch (Request::getWord('action'))
		{
			case 'assessmentdetails':   $this->assessmentdetails();   break;
			case 'getprogressrows':     $this->getprogressrows();     break;
			case 'getprogressdata':     $this->getprogressdata();     break;
			case 'getgradebookdata':    $this->getgradebookdata();    break;
			case 'getreportsdata':      $this->getreportsdata();      break;
			case 'exportcsv':           $this->exportcsv();           break;
			case 'downloadresponses':   $this->downloadresponses();   break;
			case 'savegradebookitem':   $this->savegradebookitem();   break;
			case 'deletegradebookitem': $this->deletegradebookitem(); break;
			case 'savegradebookentry':  $this->savegradebookentry();  break;
			case 'resetgradebookentry': $this->resetgradebookentry(); break;
			case 'policysave':          $this->policysave();          break;
			case 'restoredefaults':     $this->restoredefaults();     break;
			default:                    $this->progress();            break;
		}

		$response->set('html', $this->view->loadTemplate());

		// Return the output
		return $response;
	}

	/**
	 * Save grading policy
	 *
	 * @return  void
	 **/
	private function progress()
	{
		$layout = ($this->course->offering()->section()->access('manage')) ? 'instructor' : 'student';

		// If this is an instructor, see if they want the overall view, or an individual student
		if ($layout == 'instructor')
		{
			if ($student_id = Request::getInt('id', false))
			{
				$layout = 'student';
				$this->view->member = $this->course->offering()->section()->member($student_id);
			}
		}

		// Add some styles to the view
		$this->css($layout.'.css');
		$this->js($layout.'progress');
		$this->js('handlebars', 'system');
		$this->css('contentbox.css', 'system');
		$this->js('contentbox', 'system');
		$this->js('jquery.uniform.min', 'system');

		// Set the layout
		$this->view->setLayout($layout);
	}

	/**
	 * Render assessment details partial view
	 *
	 * @return  void
	 **/
	private function assessmentdetails()
	{
		$layout = 'assessmentdetails_partial';

		$asset_id = Request::getInt('asset_id', false);

		require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'formReport.php';

		$this->view->details = \Components\Courses\Models\FormReport::getLetterResponseCountsForAssetId($this->db, $asset_id, $this->course->offering()->section()->get('id'));

		$this->css('assessmentdetails.css');
		$this->js('assessmentdetails');

		// Set the layout
		$this->view->setLayout($layout);
	}

	/**
	 * Get progress data
	 *
	 * @return void
	 **/
	private function getprogressrows()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Get our limit and limitstart
		$limit = Request::getInt('limit', '10');
		$start = Request::getInt('limitstart', 0);

		// Get all section members
		$members      = $this->course->offering()->section()->members(array('student'=>1, 'limit'=>$limit, 'start'=>$start));
		$member_ids   = array();
		$mems         = array();
		$grades       = null;
		$progress     = null;
		$passing      = null;
		$recognitions = null;

		if (count($members) > 0)
		{
			foreach ($members as $m)
			{
				$member_ids[] = $m->get('id');
				$mems[] = array(
					'id'        => $m->get('id'),
					'user_id'   => $m->get('user_id'),
					'name'      => User::getInstance($m->get('user_id'))->get('name'),
					'thumb'     => ltrim(User::getInstance($m->get('user_id'))->picture(0, true), DS),
					'full'      => ltrim(User::getInstance($m->get('user_id'))->picture(0, false), DS),
					'enrolled'  => (($m->get('enrolled') != '0000-00-00 00:00:00')
										? Date::of($m->get('enrolled'))->format('M j, Y')
										: 'unknown'),
					'lastvisit' => ((User::getInstance($m->get('user_id'))->get('lastvisitDate') != '0000-00-00 00:00:00')
										? Date::of(User::getInstance($m->get('user_id'))->get('lastvisitDate'))->format('M j, Y')
										: 'never')
				);
			}

			// Refresh the grades
			$this->course->offering()->gradebook()->refresh($member_ids);

			// Get the grades
			$grades       = $this->course->offering()->gradebook()->grades(array('unit', 'course'));
			$progress     = $this->course->offering()->gradebook()->progress($member_ids);
			$passing      = $this->course->offering()->gradebook()->passing($member_ids);
			$recognitions = $this->course->offering()->gradebook()->isEligibleForRecognition($member_ids);
		}

		echo json_encode(
			array(
				'members'      => $mems,
				'grades'       => $grades,
				'progress'     => $progress,
				'passing'      => $passing,
				'recognitions' => $recognitions
			)
		);

		exit();
	}

	/**
	 * Get grading policy
	 *
	 * @return void
	 **/
	private function getprogressdata()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Get the grading policy
		$gradePolicy = new \Components\Courses\Models\GradePolicies($this->course->offering()->section()->get('grade_policy_id'), $this->course->offering()->section()->get('id'));
		$policy = new stdClass();
		$policy->description     = $gradePolicy->get('description');
		$policy->exam_weight     = $gradePolicy->get('exam_weight') * 100;
		$policy->quiz_weight     = $gradePolicy->get('quiz_weight') * 100;
		$policy->homework_weight = $gradePolicy->get('homework_weight') * 100;
		$policy->threshold       = $gradePolicy->get('threshold') * 100;
		$policy->editable        = false;

		if ($this->course->config()->get('section_grade_policy', true))
		{
			$policy->editable = true;
		}
		else if ($this->course->offering()->access('manage') && $this->course->offering()->section()->get('is_default'))
		{
			$policy->editable = true;
		}

		// Get our units
		$unitsObj = $this->course->offering()->units();
		$units    = array();
		foreach ($unitsObj as $u)
		{
			$units[] = array('id'=>$u->get('id'), 'title'=>$u->get('title'));
		}

		// Get total number of members
		$members_cnt = $this->course->offering()->section()->members(array('student'=>1, 'count'=>true));

		echo json_encode(
			array(
				'gradepolicy' => $policy,
				'units'       => $units,
				'members_cnt' => $members_cnt,
				'course_id'   => $this->course->get('id')
			)
		);

		exit();
	}

	/**
	 * Get gradebook assets
	 *
	 * @return void
	 **/
	private function getgradebookdata()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Get all section members
		$members = $this->course->offering()->section()->members(array('student'=>1));
		$mems    = array();

		foreach ($members as $m)
		{
			$mems[] = array('id'=>$m->get('id'), 'name'=>User::getInstance($m->get('user_id'))->get('name'));
		}

		// Refresh the grades
		// @FIXME: commenting this out for the time being...no need to refresh grades on both the progress and gradebook views
		//$this->course->offering()->gradebook()->refresh();

		// Get the grades
		$grades = $this->course->offering()->gradebook()->grades();

		// Get the assets
		$asset  = new \Components\Courses\Tables\Asset(App::get('db'));
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

		usort($assets, function($a, $b) {
			return strcasecmp($a->title, $b->title);
		});

		echo json_encode(
			array(
				'assets'    => $assets,
				'members'   => $mems,
				'grades'    => $grades,
				'canManage' => (($this->course->access('manage')) ? true : false)
			)
		);
		exit();
	}

	/**
	 * Get data for reports view
	 *
	 * @return void
	 **/
	private function getreportsdata()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Get the grades
		$stats = $this->course->offering()->gradebook()->summaryStats();

		// Get the assets
		$asset  = new \Components\Courses\Tables\Asset(App::get('db'));
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

		usort($assets, function($a, $b) {
			return strcasecmp($a->title, $b->title);
		});

		echo json_encode(
			array(
				'stats'  => $stats,
				'assets' => $assets
			)
		);
		exit();
	}

	/**
	 * Export gradebook to csv
	 *
	 * @return void
	 **/
	private function exportcsv()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Get all section members
		$members = $this->course->offering()->section()->members(array('student'=>1));

		// Refresh the grades
		// @FIXME: This seems to cause memory problems...neeed trigger based solution, rather than one-time computation
		//$this->course->offering()->gradebook()->refresh();

		// Get the grades
		$grades = $this->course->offering()->gradebook()->grades();

		// Get the assets
		$asset  = new \Components\Courses\Tables\Asset(App::get('db'));
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

		usort($assets, function($a, $b) {
			return strcasecmp($a->title, $b->title);
		});

		$section  = $this->course->offering()->section()->get('alias');
		$filename = $this->course->get('alias') . '.' . $section . '.gradebook.csv';

		// Set content type headers
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Pragma: no-cache");
		header("Expires: 0");

		$row   = array();
		$row[] = 'Student Name';
		$row[] = 'Student Email';
		foreach ($assets as $a)
		{
			$row[] = $a->title;
		}
		echo implode(',', $row) . "\n";

		foreach ($members as $m)
		{
			$row   = array();
			$row[] = User::getInstance($m->get('user_id'))->get('name');
			$row[] = User::getInstance($m->get('user_id'))->get('email');
			foreach ($assets as $a)
			{
				$row[] = (isset($grades[$m->get('id')]['assets'][$a->id]['score'])) ? $grades[$m->get('id')]['assets'][$a->id]['score'] : '-';
			}
			echo implode(',', $row) . "\n";
		}

		// That's all
		exit();
	}

	/**
	 * Generate detailed responses CSV files and zip and offer up as download
	 *
	 * @return void
	 **/
	private function downloadresponses()
	{
		require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'formReport.php';

		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			App::abort(403, 'Sorry, you don\'t have permission to do this');
		}

		if (!$asset_ids = Request::getVar('assets', false))
		{
			App::abort(422, 'Sorry, we don\'t know what results you\'re trying to retrieve');
		}

		$protected = 'site' . DS . 'protected';
		$tmp       = $protected . DS . 'tmp';

		// We're going to temporarily house this in PATH_APP/site/protected/tmp
		if (!Filesystem::exists($protected))
		{
			App::abort(500, 'Missing temporary directory');
		}

		// Make sure tmp folder exists
		if (!Filesystem::exists($tmp))
		{
			Filesystem::makeDirectory($tmp);
		}
		else
		{
			// Folder was already there - do a sanity check and make sure no old responses zips are lying around
			$files = Filesystem::files($tmp);

			if ($files && count($files) > 0)
			{
				foreach ($files as $file)
				{
					if (strstr($file, 'responses.zip') !== false)
					{
						Filesystem::delete($tmp . DS . $file);
					}
				}
			}
		}

		// Get the individual asset ids
		$asset_ids = explode('-', $asset_ids);

		// Set up our zip archive
		$zip       = new ZipArchive();
		$path      = PATH_APP . DS . $tmp . DS . time() . '.responses.zip';
		$zip->open($path, ZipArchive::CREATE);

		// Loop through the assets
		foreach ($asset_ids as $asset_id)
		{
			// Is it a number?
			if (!is_numeric($asset_id))
			{
				continue;
			}

			// Get the rest of the asset row
			$asset = new \Components\Courses\Tables\Asset($this->db);
			$asset->load($asset_id);

			// Make sure asset is a part of this course
			if ($asset->get('course_id') != $this->course->get('id'))
			{
				continue;
			}

			if ($details = \Components\Courses\Models\FormReport::getLetterResponsesForAssetId($this->db, $asset_id, true, $this->course->offering()->section()->get('id')))
			{
				$output = implode(',', $details['headers']) . "\n";
				if (isset($details['responses']) && count($details['responses']) > 0)
				{
					foreach ($details['responses'] as $response)
					{
						$output .= implode(',', $response) . "\n";
					}
				}
				$zip->addFromString($asset_id . '.responses.csv', $output);
			}
			else
			{
				continue;
			}
		}

		// Close the zip archive handler
		$zip->close();

		if (is_file($path))
		{
			// Set up the server
			$xserver = new \Hubzero\Content\Server();
			$xserver->filename($path);
			$xserver->saveas('responses.zip');
			$xserver->disposition('attachment');
			$xserver->acceptranges(false);

			// Serve the file
			$xserver->serve();

			// Now delete the file
			Filesystem::delete($path);
		}

		// All done!
		exit();
	}

	/**
	 * Save a gradebook item
	 *
	 * @return void
	 **/
	private function savegradebookitem()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Now, also make sure either section managers can edit, or user is a course manager
		if (!$this->course->config()->get('section_grade_policy', true) && !$this->course->offering()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		$dbo = App::get('db');

		$asset = new \Components\Courses\Tables\Asset($dbo);

		$new = false;

		// Get request variables
		if ($asset_id = Request::getInt('asset_id', false))
		{
			$asset->load($asset_id);
			$asset->set('title', Request::getVar('title', $asset->get('title')));
			$asset->set('grade_weight', Request::getWord('type', $asset->get('grade_weight')));

			if ($asset->get('type') == 'form')
			{
				$asset->set('subtype', Request::getWord('type', $asset->get('grade_weight')));
			}
		}
		else
		{
			$asset->set('title', 'New Item');
			$asset->set('type', 'gradebook');
			$asset->set('subtype', 'auxiliary');
			$asset->set('created', Date::toSql());
			$asset->set('created_by', User::get('id'));
			$asset->set('state', 1);
			$asset->set('course_id', $this->course->get('id'));
			$asset->set('graded', 1);
			$asset->set('grade_weight', 'exam');

			$new = true;
		}

		if (!$asset->store())
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		if ($new)
		{
			// Create asset assoc object
			$assoc = new \Components\Courses\Tables\AssetAssociation($dbo);
			$assoc->set('asset_id', $asset->get('id'));
			$assoc->set('scope', 'offering');
			$assoc->set('scope_id', $this->course->offering()->get('id'));

			// Save the asset association
			if (!$assoc->store())
			{
				echo json_encode(array('success'=>false));
				exit();
			}
		}

		echo json_encode(array('id'=>$asset->get('id'), 'title'=>$asset->get('title')));
		exit();
	}

	/**
	 * Delete a gradebook item
	 *
	 * @return void
	 **/
	private function deletegradebookitem()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Now, also make sure either section managers can edit, or user is a course manager
		if (!$this->course->config()->get('section_grade_policy', true) && !$this->course->offering()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		$asset = new \Components\Courses\Tables\Asset(App::get('db'));

		// Get request variables
		if ($asset_id = Request::getInt('asset_id', false))
		{
			$asset->load($asset_id);
			$asset->set('graded', 0);

			if ($asset->get('type') == 'gradebook')
			{
				$asset->set('state', 2);
			}
		}
		else
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		if (!$asset->store())
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		echo json_encode(array('success'=>true));
		exit();
	}

	/**
	 * Save a gradebook entry
	 *
	 * @return void
	 **/
	private function savegradebookentry()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Get request variables
		if (!$member_id = Request::getInt('student_id', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}
		if (!$asset_id = Request::getInt('asset_id', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}
		if (!$grade_value = Request::getVar('grade', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		$db    = App::get('db');
		$grade = new \Components\Courses\Tables\GradeBook($db);
		$grade->loadByUserAndAssetId($member_id, $asset_id);

		if (!$grade->id)
		{
			$grade->set('member_id', $member_id);
			$grade->set('scope', 'asset');
			$grade->set('scope_id', $asset_id);
		}

		$grade->set('override', $grade_value);
		$grade->set('override_recorded', Date::toSql());

		if (!$grade->store())
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		echo json_encode(array('success'=>true));
		exit();
	}

	/**
	 * Reset a grade book entry...i.e. remove the override
	 *
	 * @return void
	 **/
	private function resetgradebookentry()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Get request variables
		if (!$member_id = Request::getInt('student_id', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}
		if (!$asset_id = Request::getInt('asset_id', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		$db    = App::get('db');
		$grade = new \Components\Courses\Tables\GradeBook($db);
		$grade->loadByUserAndAssetId($member_id, $asset_id);

		if (!$grade->id)
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		$grade->set('override', NULL);
		$grade->set('override_recorded', NULL);

		// Store (true to update nulls)
		if (!$grade->store(true))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		echo json_encode(array('success'=>true, 'score'=>$grade->score));
		exit();
	}

	/**
	 * Save grading policy
	 *
	 * @return void
	 **/
	private function policysave()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			// Redirect with message
			App::redirect(
				Route::url($this->base, false),
				'You don\'t have permission to do this!',
				'warning'
			);
			return;
		}

		// Now, also make sure either section managers can edit, or user is a course manager
		if (!$this->course->config()->get('section_grade_policy', true) && !$this->course->offering()->access('manage'))
		{
			// Redirect with message
			App::redirect(
				Route::url($this->base . '&active=progress', false),
				'You don\'t have permission to do this!',
				'warning'
			);
			return;
		}

		// Get the grading policy id
		$gpId = $this->course->offering()->section()->get('grade_policy_id');

		$exam_weight     = Request::getInt('exam-weight') / 100;
		$quiz_weight     = Request::getInt('quiz-weight') / 100;
		$homework_weight = Request::getInt('homework-weight') / 100;

		if (($exam_weight + $quiz_weight + $homework_weight) != 1)
		{
			if (Request::getInt('no_html', false))
			{
				echo json_encode(array('error'=>true, 'message'=>'The sum of all weights should be 100.'));
				exit();
			}
			else
			{
				// Redirect with message
				App::redirect(
					Route::url($this->base . '&active=progress', false),
					'The sum of all weights should be 100.',
					'error'
				);
				return;
			}
		}

		$saveSection = false;

		// If the section is using a policy other than the default, just update it
		if ($gpId != 1)
		{
			$gp = new \Components\Courses\Models\GradePolicies($gpId);
		}
		else
		{
			// Create new and save
			$gp = new \Components\Courses\Models\GradePolicies(null);
			$saveSection = true;
		}

		$gp->set('exam_weight',     $exam_weight);
		$gp->set('quiz_weight',     $quiz_weight);
		$gp->set('homework_weight', $homework_weight);
		$gp->set('threshold',       (Request::getInt('threshold') / 100));
		$gp->set('description',     trim(Request::getVar('description')));

		if (!$gp->store())
		{
			// Redirect with message
			App::redirect(
				Route::url($this->base . '&active=progress', false),
				'Something went wrong!',
				'error'
			);
			return;
		}

		if ($saveSection)
		{
			$section = $this->course->offering()->section();
			$section->set('grade_policy_id', $gp->get('id'));
			$section->store();
		}

		if (Request::getInt('no_html', false))
		{
			echo json_encode(array('success'=>true, 'message'=>'Scoring policy successfully saved!'));
			exit();
		}
		else
		{
			// Redirect with message
			App::redirect(
				Route::url($this->base . '&active=progress', false),
				'Scoring policy successfully saved!',
				'passed'
			);
			return;
		}
	}

	/**
	 * Restore grading policy back to default
	 *
	 * @return void
	 **/
	private function restoredefaults()
	{
		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			// Redirect with message
			App::redirect(
				Route::url($this->base, false),
				'You don\'t have permission to do this!',
				'warning'
			);
			return;
		}

		// Now, also make sure either section managers can edit, or user is a course manager
		if (!$this->course->config()->get('section_grade_policy', true) && !$this->course->offering()->access('manage'))
		{
			// Redirect with message
			App::redirect(
				Route::url($this->base . '&active=progress', false),
				'You don\'t have permission to do this!',
				'warning'
			);
			return;
		}

		$section = $this->course->offering()->section();

		// Now set them back to the default
		$section->set('grade_policy_id', 1);
		$section->store();

		// If section managers can't edit, then also make the above change for all sections of this course
		if (!$this->course->config()->get('section_grade_policy', true))
		{
			$sections = $this->course->offering()->sections();

			foreach ($sections as $s)
			{
				$s->set('grade_policy_id', 1);
				$s->store();
			}
		}

		if (Request::getInt('no_html', false))
		{
			$gp = new \Components\Courses\Models\GradePolicies(1);
			$policy->description     = $gp->get('description');
			$policy->exam_weight     = $gp->get('exam_weight') * 100;
			$policy->quiz_weight     = $gp->get('quiz_weight') * 100;
			$policy->homework_weight = $gp->get('homework_weight') * 100;
			$policy->threshold       = $gp->get('threshold') * 100;
			echo json_encode(
				array(
					'success'     => true,
					'message'     => 'Scoring policy successfully restored to the default configuration!',
					'gradepolicy' => $policy
				)
			);
			exit();
		}
		else
		{
			// Redirect with message
			App::redirect(
				Route::url($this->base . '&active=progress', false),
				'Scoring policy successfully restored to the default configuration!',
				'passed'
			);
			return;
		}
	}
}