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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradebook.php');
ximport('Hubzero_User_Profile_Helper');

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias');

// Get all section members
$members = $this->course->offering()->section()->members();
$member_ids = array();

foreach ($members as $m)
{
	$member_ids[] = $m->get('user_id');
}

// Get Grades
$gradebook = new CoursesModelGradeBook(null);
// @FIXME: refresh grades here?
//$gradebook->refresh(null, $this->course->offering()->section()->get('id'));
$grades    = $gradebook->getGrades($member_ids, array('unit', 'course'));
$progress  = $gradebook->getProgress($this->course);

?>

<div class="instructor">
	<div class="headers">
		<div class="header-student-name">Name</div>
		<div class="header-sub">
			<div class="header-progress">Unit Progress</div>
			<div class="header-score">Current Score</div>
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
						<?= JFactory::getUser($m->get('user_id'))->get('name'); ?>
					</div>
					<div class="progress-container">
						<div class="student-progress-timeline">
							<div class="student-progress-timeline-inner length_<?= count($this->course->offering()->units()) ?>">
								<? foreach($this->course->offering()->units() as $unit) : ?>
									<? $height = $progress[$m->get('user_id')][$unit->get('id')]['percentage_complete']; ?>
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
								<? if (isset($grades[$m->get('user_id')]['course'][$this->course->get('id')])) : ?>
									<?
										$studentStatus = $grades[$m->get('user_id')]['course'][$this->course->get('id')];
										$cls = '';
										if($studentStatus < 70)
										{
											$cls = ' stop';
										}
										elseif($studentStatus >= 70 && $studentStatus < 75)
										{
											$cls = ' yield';
										}
										elseif($studentStatus >= 75 && $studentStatus <= 100)
										{
											$cls = ' go';
										}
									?>
									<div class="student-progress-bar <?= $cls ?>" style="width:<?= $studentStatus ?>%;">
										<div class="score-text"><?= $studentStatus ?></div>
									</div>
								<? endif; ?>
							</div>
						</div>
					</div>
				</a>
				<div class="clear"></div>
				<div class="student-details grades">
					<div class="picture">
						<?
							$src = '/components/com_members/assets/img/profile.gif';
							$src = Hubzero_User_Profile_Helper::getMemberPhoto($m->get('user_id'), 0, false);
						?>
						<img src="<?= $src ?>" />
						<a class="more-details" href="<?= JRoute::_($base . '&active=progress&id=' . $m->get('user_id')) ?>">More details</a>
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
												: '0.00%'
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
												: '0.00%'
										?>
									</div>
								</div>
							</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		<? endforeach; ?>
	<? else : ?>
		<p class="info">The section does not currently have anyone enrolled</p>
	<? endif; ?>
</div>
<!--<div class="refresh">
	<p>
		Does something look incorrect above? Try <a href="<? //JRoute::_($base . '&active=progress&action=refresh') ?>">refreshing</a> the scores!
	</p>
</div>-->