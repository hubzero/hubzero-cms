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

jimport('joomla.plugin.plugin');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradepolicies.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradebook.php');

/**
 * Courses Plugin class for user progress
 */
class plgCoursesProgress extends JPlugin
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

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onCourseAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => true,
			'icon' => 'f012'
		);
		return $area;
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param      object  $course      Current course
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onCourse($config, $course, $instance, $action='', $areas=null)
	{
		$return = 'html';
		$active = $this->_name;

		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		//get this area details
		$this_area = $this->onCourseAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!in_array($this_area['name'], $areas))
			{
				return $arr;
			}
		}
		else if ($areas != $this_area['name'])
		{
			return $arr;
		}

		// Check to see if user is member and plugin access requires members
		if (!$course->offering()->section()->access('view'))
		{
			$view = new \Hubzero\Plugin\View(array(
				'folder'  => 'courses',
				'element' => 'progress',
				'name'    => 'report',
				'layout'  => '_not_enrolled'
			));
			$view->set('course', $course)
			     ->set('option', 'com_courses')
			     ->set('message', 'You must be enrolled to utilize the progress feature.');
			$arr['html'] = $view->__toString();
			return $arr;
		}

		$this->member = $course->offering()->section()->member(JFactory::getUser()->get('id'));
		$this->course = $course;
		$this->base   = $course->offering()->link();
		$this->db     = JFactory::getDBO();

		// Instantiate a vew
		$this->view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'courses',
				'element' => $active,
				'name'    => 'report',
				'layout'  => 'student'
			)
		);
		$this->view->course  = $course;
		$this->view->member  = $this->member;
		$this->view->option  = 'com_courses';
		$this->view->base    = $this->base;

		switch (JRequest::getWord('action'))
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

		$arr['html'] = $this->view->loadTemplate();

		// Return the output
		return $arr;
	}

	/**
	 * Save grading policy
	 *
	 * @return void
	 **/
	private function progress()
	{
		$layout = ($this->course->offering()->section()->access('manage')) ? 'instructor' : 'student';

		// If this is an instructor, see if they want the overall view, or an individual student
		if ($layout == 'instructor')
		{
			if ($student_id = JRequest::getInt('id', false))
			{
				$layout = 'student';
				$this->view->member = $this->course->offering()->section()->member($student_id);
			}
		}

		// Add some styles to the view
		\Hubzero\Document\Assets::addPluginStylesheet('courses', 'progress', $layout.'.css');
		\Hubzero\Document\Assets::addPluginScript('courses', 'progress', $layout.'progress');
		\Hubzero\Document\Assets::addSystemScript('handlebars');
		\Hubzero\Document\Assets::addSystemStylesheet('contentbox.css');
		\Hubzero\Document\Assets::addSystemScript('contentbox');
		\Hubzero\Document\Assets::addSystemScript('jquery.uniform.min');

		// Set the layout
		$this->view->setLayout($layout);
	}

	/**
	 * Render assessment details partial view
	 *
	 * @return void
	 **/
	private function assessmentdetails()
	{
		$layout = 'assessmentdetails_partial';

		$asset_id = JRequest::getInt('asset_id', false);

		require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'formReport.php';

		$this->view->details = CoursesModelFormReport::getLetterResponseCountsForAssetId($this->db, $asset_id, $this->course->offering()->section()->get('id'));

		\Hubzero\Document\Assets::addPluginStylesheet('courses', 'progress', 'assessmentdetails.css');
		\Hubzero\Document\Assets::addPluginScript('courses', 'progress', 'assessmentdetails');

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
		$limit = JRequest::getInt('limit', '10');
		$start = JRequest::getInt('limitstart', 0);

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
					'name'      => JFactory::getUser($m->get('user_id'))->get('name'),
					'thumb'     => ltrim(\Hubzero\User\Profile\Helper::getMemberPhoto($m->get('user_id'), 0, true), DS),
					'full'      => ltrim(\Hubzero\User\Profile\Helper::getMemberPhoto($m->get('user_id'), 0, false), DS),
					'enrolled'  => (($m->get('enrolled') != '0000-00-00 00:00:00')
										? JFactory::getDate(strtotime($m->get('enrolled')))->format('M j, Y')
										: 'unknown'),
					'lastvisit' => ((JFactory::getUser($m->get('user_id'))->get('lastvisitDate') != '0000-00-00 00:00:00')
										? JFactory::getDate(strtotime(JFactory::getUser($m->get('user_id'))->get('lastvisitDate')))->format('M j, Y')
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
		$gradePolicy = new CoursesModelGradePolicies($this->course->offering()->section()->get('grade_policy_id'), $this->course->offering()->section()->get('id'));
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
			$mems[] = array('id'=>$m->get('id'), 'name'=>JFactory::getUser($m->get('user_id'))->get('name'));
		}

		// Refresh the grades
		// @FIXME: commenting this out for the time being...no need to refresh grades on both the progress and gradebook views
		//$this->course->offering()->gradebook()->refresh();

		// Get the grades
		$grades = $this->course->offering()->gradebook()->grades();

		// Get the assets
		$asset  = new CoursesTableAsset(JFactory::getDBO());
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
		$asset  = new CoursesTableAsset(JFactory::getDBO());
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
		$asset  = new CoursesTableAsset(JFactory::getDBO());
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
			$row[] = JFactory::getUser($m->get('user_id'))->get('name');
			$row[] = JFactory::getUser($m->get('user_id'))->get('email');
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
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'formReport.php';

		// Only allow for instructors
		if (!$this->course->offering()->section()->access('manage'))
		{
			JError::raiseError('403', 'Sorry, you don\'t have permission to do this');
		}

		if (!$asset_ids = JRequest::getVar('assets', false))
		{
			JError::raiseError('422', 'Sorry, we don\'t know what results you\'re trying to retrieve');
		}

		$protected = 'site' . DS . 'protected';
		$tmp       = $protected . DS . 'tmp';

		// We're going to temporarily house this in JPATH_ROOT/site/protected/tmp
		if (!JFolder::exists($protected))
		{
			JError::raiseError('500', 'Missing temporary directory');
		}

		// Make sure tmp folder exists
		if (!JFolder::exists($tmp))
		{
			JFolder::create($tmp);
		}
		else
		{
			// Folder was already there - do a sanity check and make sure no old responses zips are lying around
			$files = JFolder::files($tmp);

			if ($files && count($files) > 0)
			{
				foreach ($files as $file)
				{
					if (strstr($file, 'responses.zip') !== false)
					{
						JFile::delete($tmp . DS . $file);
					}
				}
			}
		}

		// Get the individual asset ids
		$asset_ids = explode('-', $asset_ids);

		// Set up our zip archive
		$zip       = new ZipArchive();
		$path      = JPATH_ROOT . DS . $tmp . DS . time() . '.responses.zip';
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
			$asset = new CoursesTableAsset($this->db);
			$asset->load($asset_id);

			// Make sure asset is a part of this course
			if ($asset->get('course_id') != $this->course->get('id'))
			{
				continue;
			}

			if ($details = CoursesModelFormReport::getLetterResponsesForAssetId($this->db, $asset_id, true, $this->course->offering()->section()->get('id')))
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
			JFile::delete($path);
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

		$dbo = JFactory::getDBO();

		$asset = new CoursesTableAsset($dbo);

		$new = false;

		// Get request variables
		if ($asset_id = JRequest::getInt('asset_id', false))
		{
			$asset->load($asset_id);
			$asset->set('title', JRequest::getVar('title', $asset->get('title')));
			$asset->set('grade_weight', JRequest::getWord('type', $asset->get('grade_weight')));

			if ($asset->get('type') == 'form')
			{
				$asset->set('subtype', JRequest::getWord('type', $asset->get('grade_weight')));
			}
		}
		else
		{
			$asset->set('title', 'New Item');
			$asset->set('type', 'gradebook');
			$asset->set('subtype', 'auxiliary');
			$asset->set('created', JFactory::getDate()->toSql());
			$asset->set('created_by', JFactory::getUser()->get('id'));
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
			$assoc = new CoursesTableAssetAssociation($dbo);
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

		$asset = new CoursesTableAsset(JFactory::getDBO());

		// Get request variables
		if ($asset_id = JRequest::getInt('asset_id', false))
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
		if (!$member_id = JRequest::getInt('student_id', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}
		if (!$asset_id = JRequest::getInt('asset_id', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}
		if (!$grade_value = JRequest::getVar('grade', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		$db    = JFactory::getDBO();
		$grade = new CoursesTableGradeBook($db);
		$grade->loadByUserAndAssetId($member_id, $asset_id);

		if (!$grade->id)
		{
			$grade->set('member_id', $member_id);
			$grade->set('scope', 'asset');
			$grade->set('scope_id', $asset_id);
		}

		$grade->set('override', $grade_value);

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
		if (!$member_id = JRequest::getInt('student_id', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}
		if (!$asset_id = JRequest::getInt('asset_id', false))
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		$db    = JFactory::getDBO();
		$grade = new CoursesTableGradeBook($db);
		$grade->loadByUserAndAssetId($member_id, $asset_id);

		if (!$grade->id)
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		$grade->set('override', NULL);

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
			JFactory::getApplication()->redirect(
				JRoute::_($this->base, false),
				'You don\'t have permission to do this!',
				'warning'
			);
			return;
		}

		// Now, also make sure either section managers can edit, or user is a course manager
		if (!$this->course->config()->get('section_grade_policy', true) && !$this->course->offering()->access('manage'))
		{
			// Redirect with message
			JFactory::getApplication()->redirect(
				JRoute::_($this->base . '&active=progress', false),
				'You don\'t have permission to do this!',
				'warning'
			);
			return;
		}

		// Get the grading policy id
		$gpId = $this->course->offering()->section()->get('grade_policy_id');

		$exam_weight     = JRequest::getInt('exam-weight') / 100;
		$quiz_weight     = JRequest::getInt('quiz-weight') / 100;
		$homework_weight = JRequest::getInt('homework-weight') / 100;

		if (($exam_weight + $quiz_weight + $homework_weight) != 1)
		{
			if (JRequest::getInt('no_html', false))
			{
				echo json_encode(array('error'=>true, 'message'=>'The sum of all weights should be 100.'));
				exit();
			}
			else
			{
				// Redirect with message
				JFactory::getApplication()->redirect(
					JRoute::_($this->base . '&active=progress', false),
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
			$gp = new CoursesModelGradePolicies($gpId);
		}
		else
		{
			// Create new and save
			$gp = new CoursesModelGradePolicies(null);
			$saveSection = true;
		}

		$gp->set('exam_weight',     $exam_weight);
		$gp->set('quiz_weight',     $quiz_weight);
		$gp->set('homework_weight', $homework_weight);
		$gp->set('threshold',       (JRequest::getInt('threshold') / 100));
		$gp->set('description',     trim(JRequest::getVar('description')));

		if (!$gp->store())
		{
			// Redirect with message
			JFactory::getApplication()->redirect(
				JRoute::_($this->base . '&active=progress', false),
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

		if (JRequest::getInt('no_html', false))
		{
			echo json_encode(array('success'=>true, 'message'=>'Scoring policy successfully saved!'));
			exit();
		}
		else
		{
			// Redirect with message
			JFactory::getApplication()->redirect(
				JRoute::_($this->base . '&active=progress', false),
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
			JFactory::getApplication()->redirect(
				JRoute::_($this->base, false),
				'You don\'t have permission to do this!',
				'warning'
			);
			return;
		}

		// Now, also make sure either section managers can edit, or user is a course manager
		if (!$this->course->config()->get('section_grade_policy', true) && !$this->course->offering()->access('manage'))
		{
			// Redirect with message
			JFactory::getApplication()->redirect(
				JRoute::_($this->base . '&active=progress', false),
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

		if (JRequest::getInt('no_html', false))
		{
			$gp = new CoursesModelGradePolicies(1);
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
			JFactory::getApplication()->redirect(
				JRoute::_($this->base . '&active=progress', false),
				'Scoring policy successfully restored to the default configuration!',
				'passed'
			);
			return;
		}
	}
}