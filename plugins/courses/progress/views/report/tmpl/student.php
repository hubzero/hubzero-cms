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

$quizzes_total       = 0;
$homeworks_total     = 0;
$exams_total         = 0;
$quizzes_taken       = 0;
$homeworks_submitted = 0;
$exams_taken         = 0;

$forms           = array();
$current_score   = array();
$current_score_i = 0;

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
						$forms[] = array('title'=>$asset->get('title'), 'score'=>'Not yet open', 'date'=>'N/A', 'url'=>$asset->get('url'));
					break;

					// Form availability has expired
					case 'expired':
						// Get whether or not we should show scores at this point
						$results_closed = $dep->getResultsClosed();

						// Grab the response
						$resp = $dep->getRespondent();

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

						$forms[] = array('title'=>$asset->get('title'), 'score'=>$score, 'date'=>$date, 'url'=>$asset->get('url'));
					break;

					// Form is still active
					case 'active':
						$resp = $dep->getRespondent();

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

						$forms[] = array('title'=>$asset->get('title'), 'score'=>$score, 'date'=>$date, 'url'=>$asset->get('url'));
					break;
				}

				// Increment total count for this type
				// @FIXME: probably need a better way of identifying types of form/exam assets
				if(strpos(strtolower($asset->get('title')), 'quiz'))
				{
					++$quizzes_total;

					// If increment is set (i.e. they completed the from), increment the taken number as well
					if($increment_count_taken)
					{
						++$quizzes_taken;
					}
				}
				elseif(strpos(strtolower($asset->get('title')), 'homework'))
				{
					++$homeworks_total;

					// If increment is set (i.e. they completed the from), increment the taken number as well
					if($increment_count_taken)
					{
						++$homeworks_submitted;
					}
				}
				elseif(strpos(strtolower($asset->get('title')), 'exam'))
				{
					++$exams_total;

					// If increment is set (i.e. they completed the from), increment the taken number as well
					if($increment_count_taken)
					{
						++$exams_taken;
					}
				}
			}
		}
	}
}

// @FIXME: order assets

// Calculate the student's current score
$current_score = ($current_score_i > 0) ? round(array_sum($current_score) / $current_score_i, 2) : 0;

// Get the status of the course (e.x. not started, in progress, completed, etc...)
$section = $this->course->offering()->section();
if(!$section->available() && !$section->ended())
{
	$h3 = JText::_('Course begins ') . date('M jS, Y', strtotime($section->get('start_date')));
}
elseif ($section->available())
{
	$h3 = JText::_('Course currently in progress');
}
else
{
	$h3 = JText::_('Course ended ') . date('M jS, Y', strtotime($section->get('end_date')));
}

// Get the number of units in the course and figure out which is the current one
$units     = $this->course->offering()->units();
$num_units = $units->total();
$index     = 1;
$current_i = 0;

// Build the progress timeline bar
$progress_timeline  = "<div class=\"progress-timeline length_{$num_units}\">";
$progress_timeline .= '<div class="start"><div class="start-inner"></div></div>';
foreach ($units as $unit)
{
	$first   = ($index == 1) ? ' first' : '';
	$last    = ($index == $num_units) ? ' last' : '';
	$past    = ($unit->started()) ? ' past' : '';
	$current = '';

	if($unit->available())
	{
		$current   = ' current';
		// Set the index for the currently available unit (this will result in the latter of the available units if multiple are available)
		// @FIXME: how do we handle an asynchrones course?  Current would be at spot of farthest form taken?
		$current_i = $index;
	}

	$progress_timeline .= "<div class=\"unit{$current}\"><div class=\"unit-inner{$first}{$last}{$past}\">Unit {$index}</div></div>";

	++$index;
}
$progress_timeline .= '<div class="end"><div class="end-inner"></div></div>';
$progress_timeline .= '</div>';

// Check/get info about whether or not a badge is offerred for this course
// @TODO: attach badge to offering?

?>

<div class="progress">
	<h3><?= $h3 ?></h3>
	<h4><?= JText::sprintf('Unit %d of %d', $current_i, $num_units) ?></h4>

	<?= $progress_timeline ?>

	<div class="clear"></div>

	<div class="grades">
		<div class="current-score">
			<div class="current-score-inner">
				<p class="title"><?= JText::_('Your current score') ?></p>
				<p class="score"><?= $current_score . '%' ?></p>
				<a href="#" class="toggle-grade-details toggle-grade-details-open"><?= JText::_('grade details') ?></a>
			</div>
		</div>

		<div class="quizzes">
			<div class="quizzes-inner">
				<p class="title"><?= JText::_('Quizzes taken') ?></p>
				<p class="score"><?= $quizzes_taken ?></p>
				<p><?= JText::sprintf('out of %d', $quizzes_total) ?></p>
			</div>
		</div>

		<div class="homeworks">
			<div class="homeworks-inner">
				<p class="title"><?= JText::_('Homeworks submitted') ?></p>
				<p class="score"><?= $homeworks_submitted ?></p>
				<p><?= JText::sprintf('out of %d', $homeworks_total) ?></p>
			</div>
		</div>

		<div class="exams">
			<div class="exams-inner">
				<p class="title"><?= JText::_('Exams taken') ?></p>
				<p class="score"><?= $exams_taken ?></p>
				<p><?= JText::sprintf('out of %d', $exams_total) ?></p>
			</div>
		</div>

		<div class="clear"></div>

		<div class="grade-details">
			<table class="entries">
				<caption><?= ucfirst(JText::_('grade details')) ?></caption>
				<thead>
					<tr>
						<td class="grade-details-title"><?= JText::_('Assignment') ?></td>
						<td class="grade-details-score"><?= JText::_('Score') ?></td>
						<td class="grade-details-date"><?= JText::_('Date taken') ?></td>
					</tr>
				</thead>
				<tbody>
					<? foreach($forms as $form) : ?>
						<? 
							if(is_numeric($form['score']) && $form['score'] < 60)
							{
								$class = 'stop';
							}
							elseif(is_numeric($form['score']) && $form['score'] >= 60 && $form['score'] < 70)
							{
								$class = 'yield';
							}
							elseif(is_numeric($form['score']) && $form['score'] >= 70)
							{
								$class = 'go';
							}
							else
							{
								$class = 'neutral';
							}
						?>
						<tr class="<?= $class ?>">
							<td class="grade-details-title"><a href="<?= $form['url'] ?>"><?= $form['title'] ?></a></td>
							<td class="grade-details-score"><?= $form['score'] . (is_numeric($form['score']) ? '%' : '') ?></td>
							<td class="grade-details-date"><?= $form['date'] ?></td>
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="clear"></div>

	<div class="badge">
		<h3>Work hard. Earn a badge.</h3>
		<p>
			Upon successful completion of this course, you will be awarded a special <?= $this->course->get('title') ?> badge.
			This badge can be saved to your Mozzila Open Badges Backpack. To learn more about Mozilla Open
			Badges, please visit the <a href="http://openbadges.org/" target="_blank">Open Badges website</a>.
		</p>
	</div>
</div>