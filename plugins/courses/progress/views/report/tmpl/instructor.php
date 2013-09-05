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

// Make sure required files are included
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradepolicies.php');
ximport('Hubzero_User_Profile_Helper');

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . ($this->course->offering()->section()->get('alias') != '__default' ? ':' . $this->course->offering()->section()->get('alias') : '');

$total = $this->course->offering()->section()->members(array('student'=>1, 'count'=>true));
$start = JRequest::getInt('limitstart', 0);
$limit = JRequest::getInt('limit', 10);

// Get all section members
$members = $this->course->offering()->section()->members(array('student'=>1, 'start'=>$start, 'limit'=>$limit));
$m = array();

// Get member id's for refresh filter
foreach ($members as $member)
{
	$m[] = $member->get('user_id');
}

// Refresh the grades
$this->course->offering()->gradebook()->refresh($m);

// Initiate paging
jimport('joomla.html.pagination');
$pagination = new JPagination(
	$total, 
	$start, 
	$limit
);

// Get the grades
$grades    = $this->course->offering()->gradebook()->grades(array('unit', 'course'));
$progress  = $this->course->offering()->gradebook()->progress($m);
$passing   = $this->course->offering()->gradebook()->passing();

// Get the grading policy
$gradePolicy = new CoursesModelGradePolicies($this->course->offering()->section()->get('grade_policy_id'));
$policy = $gradePolicy->get('description');

?>

<div class="instructor">
	<div class="extra">

	</div>
	<?php if ($this->course->config()->get('section_grade_policy', true) || $this->course->offering()->access('manage')) : ?>
		<div class="grade-policy">
			<div class="grade-policy-header">
				Scoring policy<span class="details">: <?= $gradePolicy->get('description') ?></span>
				<div class="grade-policy-edit">
					edit
				</div>
			</div>
			<div class="grade-policy-inner">
				<form action="<?= JRoute::_($base . '&active=progress') ?>" method="POST">
					<div class="label-input-pair">
						<label for="exam-weight">Exam Weight:</label>
						<input type="text" name="exam-weight" value="<?= $gradePolicy->get('exam_weight') * 100 ?>" class="slider" size="4" />
					</div>
					<div class="label-input-pair">
						<label for="quiz-weight">Quiz Weight:</label>
						<input type="text" name="quiz-weight" value="<?= $gradePolicy->get('quiz_weight') * 100 ?>" class="slider" size="4" />
					</div>
					<div class="label-input-pair">
						<label for="homework-weight">Homework Weight:</label>
						<input type="text" name="homework-weight" value="<?= $gradePolicy->get('homework_weight') * 100 ?>" class="slider" size="4" />
					</div>
					<div class="label-input-pair">
						<label for="threshold">Passing Threshold:</label>
						<input type="text" name="threshold" value="<?= $gradePolicy->get('threshold') * 100 ?>" class="slider" size="4" />
					</div>
					<div class="label-input-pair">
						<label for="description">Policy Description:</label>
						<textarea name="description" cols="50" rows="2"><?= $gradePolicy->get('description') ?></textarea>
					</div>
					<input type="hidden" name="action" value="policysave" />
					<button type="submit">Submit</button>
					<a class="restore-defaults" href="<?= JRoute::_($base. '&active=progress&action=restoredefaults') ?>">Restore Defaults</a>
				</form>
			</div>
		</div>
	<?php endif; ?>
	<div class="headers">
		<div class="header-student-name">Name</div>
		<div class="header-sub">
			<div class="header-progress">Unit Progress
				<span title="This reflects what students have viewed, not the actual scores that they may have received.">details</span>
			</div>
			<div class="header-score">Current Score
				<span title="<?= $policy ?>">details</span>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<? if(count($members) > 0) : ?>
		<? foreach($members as $m) : ?>
			<div class="student">
				<a href="<?= JRoute::_($base . '&active=progress&id=' . $m->get('user_id')) ?>">
					<div class="student-name">
						<div class="picture-thumb">
							<?
								$src = '/components/com_members/assets/img/profile.gif';
								$src = Hubzero_User_Profile_Helper::getMemberPhoto($m->get('user_id'), 0, true);
							?>
							<img src="<?= $src ?>" />
						</div>
						<?= JFactory::getUser($m->get('user_id'))->get('name') ?>
					</div>
					<div class="progress-container">
						<div class="student-progress-timeline">
							<div class="student-progress-timeline-inner length_<?= count($this->course->offering()->units()) ?>">
								<? foreach($this->course->offering()->units() as $unit) : ?>
									<? $height = (isset($progress[$m->get('user_id')][$unit->get('id')]['percentage_complete']))
													? $progress[$m->get('user_id')][$unit->get('id')]['percentage_complete']
													: 0; ?>
									<? $margin = 100 - $height; ?>
									<? $cls    = ($height == 100) ? ' complete' : ''; ?>
									<div class="unit">
										<div class="unit-inner">
											<div class="unit-title"><?= $unit->get('title') ?></div>
											<div class="unit-fill" title="<?= $unit->get('title') ?> (<?= $height ?>%)">
												<div class="unit-fill-inner<?= $cls ?>" style="height:<?= $height ?>%;margin-top:<?= $margin ?>%;"></div>
											</div>
										</div>
									</div>
								<? endforeach; ?>
							</div>
						</div>
						<div class="progress-bar-container">
							<div class="progress-bar-inner">
								<? if (isset($grades[$m->get('user_id')]) && isset($grades[$m->get('user_id')]['course'][$this->course->get('id')])) : ?>
									<?
										$cls = '';

										if(isset($passing[$m->get('user_id')]) && $passing[$m->get('user_id')] === 1)
										{
											$cls = ' go';
										}
										elseif (isset($passing[$m->get('user_id')]) && $passing[$m->get('user_id')] === 0)
										{
											$cls = ' stop';
										}
									?>
									<div class="student-progress-bar <?= $cls ?>" style="width:<?= $grades[$m->get('user_id')]['course'][$this->course->get('id')] ?>%;">
										<div class="score-text"><?= $grades[$m->get('user_id')]['course'][$this->course->get('id')] ?></div>
									</div>
								<? endif; ?>
							</div>
						</div>
					</div>
				</a>
				<div class="clear"></div>
				<div class="student-details grades">
					<div class="extended-info">
						<div class="picture">
							<?
								$src = '/components/com_members/assets/img/profile.gif';
								$src = Hubzero_User_Profile_Helper::getMemberPhoto($m->get('user_id'), 0, false);
							?>
							<img src="<?= $src ?>" />
							<a class="more-details" href="<?= JRoute::_($base . '&active=progress&id=' . $m->get('user_id')) ?>">More details</a>
						</div>
						<div class="extended-info-extra">
							<h6>Joined Course</h6>
							<p><?= date('M j, Y', strtotime($m->get('enrolled'))) ?></p>
							<h6>Last Visit</h6>
							<p><?= date('M j, Y', strtotime(JFactory::getUser($m->get('user_id'))->get('lastvisitDate'))) ?></p>
						</div>
					</div>
					<div class="units">
						<div class="headers">
							<div class="header-units">Unit Scores</div>
						</div>
						<? foreach($this->course->offering()->units() as $unit) : ?>
							<div class="unit-entry">
								<div class="unit-overview">
									<div class="unit-title"><?= $unit->get('title') ?></div>
									<div class="unit-score">
										<?= 
											(isset($grades[$m->get('user_id')]['units'][$unit->get('id')]))
												? $grades[$m->get('user_id')]['units'][$unit->get('id')] . '%'
												: '--'
										?>
									</div>
								</div>
							</div>
						<? endforeach; ?>
							<div class="unit-entry">
								<div class="unit-overview">
									<div class="unit-title">Course Average</div>
									<div class="unit-score">
										<?= 
											(isset($grades[$m->get('user_id')]['course'][$this->course->get('id')]))
												? $grades[$m->get('user_id')]['course'][$this->course->get('id')] . '%'
												: '--'
										?>
									</div>
								</div>
							</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		<? endforeach; ?>
		<?php //if ($total > $limit) : ?>
			<form action="<?= JRoute::_($base . '&active=progress') ?>" method="GET">
				<div class="pagenav">
					<?php echo $pagination->getListFooter() ?>
				</div>
			</form>
		<?php //endif; ?>
	<? else : ?>
		<p class="info">The section does not currently have anyone enrolled</p>
	<? endif; ?>
</div>