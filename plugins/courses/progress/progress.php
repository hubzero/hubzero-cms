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
			'display_menu_tab' => true
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

		$layout = ($course->offering()->access('manage')) ? 'instructor' : 'student';

		// Create user object
		$this->juser  = JFactory::getUser();
		$this->course = $course;

		// If this is an instructor, see if they want the overall view, or an individual student
		if($layout == 'instructor')
		{
			if($student_id = JRequest::getInt('id', false))
			{
				$layout = 'student';
				$this->juser  = JFactory::getUser($student_id);
			}
		}

		// Check to see if user is member and plugin access requires members
		if (!$course->offering()->access('view')) 
		{
			$arr['html'] = '<p class="info">' . JText::sprintf('COURSES_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
			return $arr;
		}

		// Add some styles to the view
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('courses', 'progress');
		Hubzero_Document::addPluginScript('courses', 'progress', $layout.'progress');

		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'report',
				'layout'  => $layout
			)
		);
		$view->course  = $course;
		$view->juser   = $this->juser;
		$view->option  = 'com_courses';
		$view->details = $this->getGradeDetails();

		$arr['html'] = $view->loadTemplate();

		// Return the output
		return $arr;
	}

	private function getGradeDetails()
	{
		$details = array();

		$details['quizzes_total']       = 0;
		$details['homeworks_total']     = 0;
		$details['exams_total']         = 0;
		$details['quizzes_taken']       = 0;
		$details['homeworks_submitted'] = 0;
		$details['exams_taken']         = 0;
		$details['forms']               = array();
		$current_score       = array();
		$current_score_i     = 0;

		foreach($this->course->offering()->units() as $unit)
		{
			foreach($unit->assetgroups() as $agt)
			{
				foreach($agt->children() as $ag)
				{
					foreach($ag->assets() as $asset)
					{
						$increment_count_taken = false;
						$crumb                 = false;

						// Check for result for given student on form
						preg_match('/\?crumb=([-a-zA-Z0-9]{20})/', $asset->get('url'), $matches);

						if(isset($matches[1]))
						{
							$crumb = $matches[1];
						}

						if(!$crumb || $asset->get('state') != COURSES_STATE_PUBLISHED)
						{
							// Break foreach, this is not a valid form!
							continue;
						}

						require_once(JPATH_COMPONENT . DS . 'models' . DS . 'form.php');
						require_once(JPATH_COMPONENT . DS . 'models' . DS . 'formRespondent.php');
						require_once(JPATH_COMPONENT . DS . 'models' . DS . 'formDeployment.php');

						$dep = PdfFormDeployment::fromCrumb($crumb);

						switch ($dep->getState())
						{
							// Form isn't available yet
							case 'pending':
								$details['forms'][] = array('title'=>$asset->get('title'), 'score'=>'Not yet open', 'date'=>'N/A', 'url'=>$asset->get('url'));
							break;

							// Form availability has expired
							case 'expired':
								// Get whether or not we should show scores at this point
								$results_closed = $dep->getResultsClosed();

								// Grab the response
								$resp = $dep->getRespondent($this->juser->get('id'));

								// Form is still active and they are allowed to see their score
								if($results_closed == 'score' || $results_closed == 'details')
								{
									$record          = $resp->getAnswers();
									$score           = $record['summary']['score'];
									$current_score[] = $score;
									++$current_score_i;
								}
								else
								{
									// Score has been withheld by form creator
									$score = 'Withheld';
								}

								// Get the date of the completion
								$date = date('r', strtotime($resp->getEndTime()));

								// They have completed this form, therefore set increment_count_taken equal to true
								$increment_count_taken = true;

								$details['forms'][] = array('title'=>$asset->get('title'), 'score'=>$score, 'date'=>$date, 'url'=>$asset->get('url'));
							break;

							// Form is still active
							case 'active':
								$resp = $dep->getRespondent($this->juser->get('id'));

								// Form is active and they have completed it!
								if($resp->getEndTime() && $resp->getEndTime() != '')
								{
									// Get whether or not we should show scores at this point
									$results_open = $dep->getResultsOpen();

									// Form is still active and they are allowed to see their score
									if($results_open == 'score' || $results_open == 'details')
									{
										$record          = $resp->getAnswers();
										$score           = $record['summary']['score'];
										$current_score[] = $score;
										++$current_score_i;
									}
									else
									{
										// Score is not yet available at this point
										$score = 'Not yet available';
									}

									// Get the date of the completion
									$date = date('r', strtotime($resp->getEndTime()));

									// They have completed this form, therefor set increment_count_taken equal to true
									$increment_count_taken = true;
								}
								// Form is active and they haven't finished it yet!
								else
								{
									$score = 'Not taken';
									$date  = 'N/A';

									// For sanities sake - they have NOT completed the form yet!
									$increment_count_taken = false;
								}

								$details['forms'][] = array('title'=>$asset->get('title'), 'score'=>$score, 'date'=>$date, 'url'=>$asset->get('url'));
							break;
						}

						// Increment total count for this type
						// @FIXME: probably need a better way of identifying types of form/exam assets
						if(strpos(strtolower($asset->get('title')), 'quiz'))
						{
							++$details['quizzes_total'];

							// If increment is set (i.e. they completed the from), increment the taken number as well
							if($increment_count_taken)
							{
								++$details['quizzes_taken'];
							}
						}
						elseif(strpos(strtolower($asset->get('title')), 'homework'))
						{
							++$details['homeworks_total'];

							// If increment is set (i.e. they completed the from), increment the taken number as well
							if($increment_count_taken)
							{
								++$details['homeworks_submitted'];
							}
						}
						elseif(strpos(strtolower($asset->get('title')), 'exam'))
						{
							++$details['exams_total'];

							// If increment is set (i.e. they completed the from), increment the taken number as well
							if($increment_count_taken)
							{
								++$details['exams_taken'];
							}
						}
					}
				}
			}
		}

		// @FIXME: order assets

		// Calculate the student's current score
		$details['current_score'] = ($current_score_i > 0) ? round(array_sum($current_score) / $current_score_i, 2) : 0;

		return $details;
	}
}
