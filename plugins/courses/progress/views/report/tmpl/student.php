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

$this->course->offering()->gradebook()->refresh($this->member->get('id'));
$grades   = $this->course->offering()->gradebook()->grades(null, $this->member->get('id'));
$progress = $this->course->offering()->gradebook()->progress($this->member->get('id'));
$passing  = $this->course->offering()->gradebook()->passing(true, $this->member->get('id'));
$passing  = (isset($passing[$this->member->get('id')])) ? $passing[$this->member->get('id')] : null;

// See if the student has qualified for the badge
$this->course->offering()->gradebook()->hasEarnedBadge($this->member->get('id'));
$student = $this->member;

$gradePolicy = new CoursesModelGradePolicies($this->course->offering()->section()->get('grade_policy_id'));

$details = array();
$details['quizzes_total']       = 0;
$details['homeworks_total']     = 0;
$details['exams_total']         = 0;
$details['quizzes_taken']       = 0;
$details['homeworks_submitted'] = 0;
$details['exams_taken']         = 0;
$details['forms']               = array();

// Get the assets
$asset  = new CoursesTableAsset(JFactory::getDBO());
$assets = $asset->find(
	array(
		'w' => array(
			'course_id'  => $this->course->get('id'),
			'section_id' => $this->course->offering()->section()->get('id'),
			'asset_type' => 'form',
			'state'      => 1
		)
	)
);

foreach($assets as $asset)
{
	$increment_count_taken = false;
	$crumb                 = false;

	// Check for result for given student on form
	$crumb = $asset->url;

	if(!$crumb || strlen($crumb) != 20 || $asset->state != 1)
	{
		// Try seeing if there's an override grade in the gradebook...
		if (!is_null($grades[$this->member->get('id')]['assets'][$asset->id]['score']))
		{
			$details['aux'][] = array('title'=>$asset->title, 'score'=>$grades[$this->member->get('id')]['assets'][$asset->id]['score']);
		}

		// Break foreach, this is not a valid form!
		continue;
	}

	$dep   = PdfFormDeployment::fromCrumb($crumb, $this->course->offering()->section()->get('id'));
	$title = $asset->title;
	$url   = JRoute::_($this->base . '&asset=' . $asset->id);
	$unit  = $this->course->offering()->unit($asset->unit_id);

	switch ($dep->getState())
	{
		// Form isn't available yet
		case 'pending':
			$details['forms'][$unit->get('id')][] = array('title'=>$title, 'score'=>'Not yet open', 'date'=>'N/A', 'url'=>$url);
		break;

		// Form availability has expired
		case 'expired':
			// Get whether or not we should show scores at this point
			$results_closed = $dep->getResultsClosed();

			$resp = $dep->getRespondent($this->member->get('id'));

			// Form is still active and they are allowed to see their score
			if($results_closed == 'score' || $results_closed == 'details')
			{
				$score = $grades[$this->member->get('id')]['assets'][$asset->id]['score'];
			}
			else
			{
				// Score has been withheld by form creator
				$score = 'Withheld';
			}

			// Get the date of the completion
			if (!is_null($resp->getEndTime()))
			{
				$date = date('r', strtotime($resp->getEndTime()));
			}
			else
			{
				$date = "N/A";
			}

			// They have completed this form, therefore set increment_count_taken equal to true
			$increment_count_taken = true;

			$details['forms'][$unit->get('id')][] = array('title'=>$title, 'score'=>$score, 'date'=>$date, 'url'=>$url);
		break;

		// Form is still active
		case 'active':
			$resp = $dep->getRespondent($this->member->get('id'));

			// Form is active and they have completed it!
			if($resp->getEndTime() && $resp->getEndTime() != '')
			{
				// Get whether or not we should show scores at this point
				$results_open = $dep->getResultsOpen();

				// Form is still active and they are allowed to see their score
				if($results_open == 'score' || $results_open == 'details')
				{
					$score = $grades[$this->member->get('id')]['assets'][$asset->id]['score'];
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

				// If there's an override in the gradebook, go ahead and use that, whether or not they've even taken the form yet
				if ($grades[$this->member->get('id')]['assets'][$asset->id]['override']
					&& !is_null($grades[$this->member->get('id')]['assets'][$asset->id]['score']))
				{
					$score = $grades[$this->member->get('id')]['assets'][$asset->id]['score'];
					$increment_count_taken = true;
				}
			}

			$details['forms'][$unit->get('id')][] = array('title'=>$title, 'score'=>$score, 'date'=>$date, 'url'=>$url);
		break;
	}

	// Increment total count for this type
	if($asset->subtype == 'quiz')
	{
		++$details['quizzes_total'];

		// If increment is set (i.e. they completed the from), increment the taken number as well
		if($increment_count_taken)
		{
			++$details['quizzes_taken'];
		}
	}
	elseif($asset->subtype == 'homework')
	{
		++$details['homeworks_total'];

		// If increment is set (i.e. they completed the from), increment the taken number as well
		if($increment_count_taken)
		{
			++$details['homeworks_submitted'];
		}
	}
	elseif($asset->subtype == 'exam')
	{
		++$details['exams_total'];

		// If increment is set (i.e. they completed the from), increment the taken number as well
		if($increment_count_taken)
		{
			++$details['exams_taken'];
		}
	}
}

// Get the status of the course (e.x. not started, in progress, completed, etc...)
$section = $this->course->offering()->section();
if(!$section->isAvailable() && !$section->ended())
{
	$h3 = JText::_('Course begins ') . date('M jS, Y', strtotime($section->get('start_date')));
}
elseif ($section->isAvailable())
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
$finished  = $student->badge()->hasEarned() ? ' finished' : '';

// Build the progress timeline bar
$progress_timeline  = "<div class=\"progress-timeline length_{$num_units}\">";
$progress_timeline .= '<div class="start"><div class="person"></div><div class="start-inner"></div></div>';
if (count($units) > 0)
{
	foreach ($units as $unit)
	{
		$first    = ($index == 1) ? ' first' : '';
		$last     = ($index == $num_units) ? ' last' : '';
		$complete = isset($progress[$this->member->get('id')][$unit->get('id')]['percentage_complete'])
					? $progress[$this->member->get('id')][$unit->get('id')]['percentage_complete']
					: 0;
		$past     = ((!is_null($unit->get('publish_up')) && $unit->get('publish_up') != '0000-00-00 00:00:00' && $unit->started()) || $complete > 0) ? ' past' : '';
		$margin   = 100 - $complete;
		$done     = ($complete == 100) ? ' complete' : '';
		$current  = '';

		if((!is_null($unit->get('publish_up')) && $unit->get('publish_up') != '0000-00-00 00:00:00' && $unit->isAvailable()) || $complete > 0)
		{
			$current   = ' current';
			// Set the index for the currently available unit (this will result in the latter of the available units if multiple are available)
			$current_i = $index;
		}

		$progress_timeline .= "<div class=\"unit unit_{$index}{$current}\">";
		$progress_timeline .= "<div class=\"person\"></div>";
		$progress_timeline .= "<div class=\"unit-inner{$first}{$last}{$past}\">";
		$progress_timeline .= "<div class=\"unit-fill\">";
		$progress_timeline .= "<div class=\"unit-fill-inner{$done}\" style=\"height:{$complete}%;margin-top:{$margin}%;\"></div>";
		$progress_timeline .= "</div>";
		$progress_timeline .= "Unit {$index}";
		$progress_timeline .= "</div></div>";

		++$index;
	}
}
else
{
	$progress_timeline .= "<div class=\"unit unit-empty\"><div class=\"unit-empty-inner\"></div></div>";
}
$progress_timeline .= '<div class="end'.$finished.'"><div class="person"></div><div class="end-inner"></div></div>';
$progress_timeline .= '</div>';

?>

<div class="progress">
	<? if($this->course->access('manage')) : ?>
		<div class="extra">
			<a href="<?= JRoute::_($this->base . '&active=progress') ?>" class="back btn icon-back"><?= JText::_('Back to all students') ?></a>
		</div>
	<? endif; ?>

	<h3>
		<?= (JRequest::getInt('id', false)) ? JFactory::getUser($this->member->get('user_id'))->get('name') . ':' : '' ?>
		<?= $h3 ?>
	</h3>
	<h4><?= JText::sprintf('Unit %d of %d', $current_i, $num_units) ?></h4>

	<?= $progress_timeline ?>

	<div class="clear"></div>

<? if (!is_null($this->course->offering()->badge()->get('id')) && $student->badge()->hasEarned()) : ?>
	<div class="badge earned">
		<img src="<?= $this->course->offering()->badge()->get('img_url') ?>" />
		<h3>Congratulations! You've earned the badge...and you deserve it!</h3>
		<p>
			You've completed all of the requirements of <?= $this->course->get('title') ?>, qualifying you to receive
			a special badge.
		</p>
		<? if ($student->badge()->get('claim_url')) : ?>
			<p>
				<a class="claim-badge" href="<?= $student->badge()->get('claim_url') ?>">Claim your badge!</a>
			</p>
		<? else : ?>
			<p>
				Watch your email in the next few days for details on how to claim your badge.
			</p>
		<? endif; ?>
	</div>
<? endif; ?>

	<div class="grades">
		<div class="current-score">
			<div class="current-score-inner">
				<p class="grading-policy"><?= JText::_('grading policy') ?></p>
				<p class="title"><?= JText::_('Your current score') ?></p>
				<?
					$cls = '';
					if ($passing === 1)
					{
						$cls = ' passing';
					}
					elseif ($passing === 0)
					{
						$cls = ' failing';
					}
				?>
				<p class="score<?= $cls ?>">
					<?=
						(isset($grades[$this->member->get('id')]['course'][$this->course->get('id')]))
							? $grades[$this->member->get('id')]['course'][$this->course->get('id')] . '%'
							: '--'
					?>
				</p>
			</div>
		</div>

		<div class="quizzes">
			<div class="quizzes-inner">
				<p class="title"><?= JText::_('Quizzes taken') ?></p>
				<p class="score"><?= $details['quizzes_taken'] ?></p>
				<p><?= JText::sprintf('out of %d', $details['quizzes_total']) ?></p>
			</div>
		</div>

		<div class="homeworks">
			<div class="homeworks-inner">
				<p class="title"><?= JText::_('Homeworks submitted') ?></p>
				<p class="score"><?= $details['homeworks_submitted'] ?></p>
				<p><?= JText::sprintf('out of %d', $details['homeworks_total']) ?></p>
			</div>
		</div>

		<div class="exams">
			<div class="exams-inner">
				<p class="title"><?= JText::_('Exams taken') ?></p>
				<p class="score"><?= $details['exams_taken'] ?></p>
				<p><?= JText::sprintf('out of %d', $details['exams_total']) ?></p>
			</div>
		</div>
	</div>

	<div class="clear"></div>

	<p class="info grading-policy-explanation">
		<?= $gradePolicy->get('description') ?>
	</p>

	<div class="units">
	<? foreach($this->course->offering()->units() as $unit) : ?>

		<div class="unit-entry">
			<div class="unit-overview">
				<div class="unit-title"><?= $unit->get('title') ?></div>
				<div class="unit-score">
					<?= 
						(isset($grades[$this->member->get('id')]['units'][$unit->get('id')]))
							? $grades[$this->member->get('id')]['units'][$unit->get('id')] . '%'
							: '--'
					?>
				</div>
			</div>
			<div class="unit-details">
				<table>
					<thead>
						<tr>
							<td class="grade-details-title"><?= JText::_('Assignment') ?></td>
							<td class="grade-details-score"><?= JText::_('Score') ?></td>
							<td class="grade-details-date"><?= JText::_('Date taken') ?></td>
						</tr>
					</thead>
					<tbody>
						<? if (isset($details['forms'][$unit->get('id')])) : 
								usort($details['forms'][$unit->get('id')], function ($a, $b) {
									return strcmp($a['title'], $b['title']);
								});
						?>
							<? foreach ($details['forms'][$unit->get('id')] as $form) : ?>
								<?
									if (is_numeric($form['score']) && $form['score'] < 60)
									{
										$class = 'stop';
									}
									elseif (is_numeric($form['score']) && $form['score'] >= 60 && $form['score'] < 70)
									{
										$class = 'yield';
									}
									elseif (is_numeric($form['score']) && $form['score'] >= 70)
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
						<? else : ?>
							<tr class="unit-no-details">
								<td colspan="3">There are currently no results to show for this unit.</td>
							</tr>
						<? endif; ?>
					</tbody>
				</table>
			</div>
		</div>

	<? endforeach; ?>

	<? if (!empty($details['aux'])) : ?>
		<div class="unit-entry">
			<div class="unit-overview">
				<div class="unit-title">Other Grades</div>
				<div class="unit-score">--</div>
			</div>
			<div class="unit-details">
				<table>
					<thead>
						<tr>
							<td class="grade-details-title"><?= JText::_('Assignment') ?></td>
							<td class="grade-details-score"><?= JText::_('Score') ?></td>
							<td class="grade-details-date"><?= JText::_('Date taken') ?></td>
						</tr>
					</thead>
					<tbody>
						<?
							usort($details['aux'], function ($a, $b) {
								return strcmp($a['title'], $b['title']);
							});
						?>
						<? foreach ($details['aux'] as $aux) : ?>
							<?
								if (is_numeric($aux['score']) && $aux['score'] < 60)
								{
									$class = 'stop';
								}
								elseif (is_numeric($aux['score']) && $aux['score'] >= 60 && $aux['score'] < 70)
								{
									$class = 'yield';
								}
								elseif (is_numeric($aux['score']) && $aux['score'] >= 70)
								{
									$class = 'go';
								}
								else
								{
									$class = 'neutral';
								}
							?>
							<tr class="<?= $class ?>">
								<td class="grade-details-title"><?= $aux['title'] ?></td>
								<td class="grade-details-score"><?= $aux['score'] . (is_numeric($aux['score']) ? '%' : '') ?></td>
								<td class="grade-details-date">N/A</td>
							</tr>
						<? endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	<? endif; ?>

	</div>

<? if (!is_null($this->course->offering()->badge()->get('id')) && !$student->badge()->hasEarned()) : ?>
	<div class="badge">
		<img src="<?= $this->course->offering()->badge()->get('img_url') ?>" />
		<h3>Work hard. Earn a badge.</h3>
		<p>
			Upon successful completion of this course, you will be awarded a special <?= $this->course->get('title') ?> badge.
			This badge can be saved to your Purdue Passport Badges Backpack, and subsequently, your Mozilla Open Badges Backpack.
			To learn more about Purdue's Passport initiative, please visit the 
			<a href="https://www.openpassport.org/Login" target="_blank">Open Passport website</a>.
		</p>
	</div>
<? endif; ?>
</div>