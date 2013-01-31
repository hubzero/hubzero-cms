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

$tbl = new CoursesTableAsset(JFactory::getDBO());

$assets = $tbl->find(array(
	'w' => array(
		'course_id'  => $this->course->get('id'),
		'asset_type' => 'exam'
	)
));

?>

<div class="progress">
	<h3>Course in progress</h3>
	<h4>Unit 3 of 7</h4>

	<div class="progress-timeline">
		<div class="start"><div class="start-inner"></div></div>
		<div class="unit"><div class="unit-inner first past">Unit 1</div></div>
		<div class="unit"><div class="unit-inner past">Unit 2</div></div>
		<div class="unit current"><div class="unit-inner past">Unit 3</div></div>
		<div class="unit"><div class="unit-inner">Unit 4</div></div>
		<div class="unit"><div class="unit-inner">Unit 5</div></div>
		<div class="unit"><div class="unit-inner">Unit 6</div></div>
		<div class="unit"><div class="unit-inner last">Unit 7</div></div>
		<div class="end"><div class="end-inner"></div></div>
	</div>

	<div class="clear"></div>

	<div class="grades">
		<div class="current-score">
			<div class="current-score-inner">
				<p class="title">Your current score</p>
				<p class="score">70%</p>
				<a href="#" class="toggle-grade-details toggle-grade-details-open">grade details</a>
			</div>
		</div>

		<div class="quizzes">
			<div class="quizzes-inner">
				<p class="title">Quizzes taken</p>
				<p class="score">4</p>
				<p>out of 25</p>
			</div>
		</div>

		<div class="homeworks">
			<div class="homeworks-inner">
				<p class="title">Homeworks submitted</p>
				<p class="score">3</p>
				<p>out of 8</p>
			</div>
		</div>

		<div class="exams">
			<div class="exams-inner">
				<p class="title">Exams taken</p>
				<p class="score">1</p>
				<p>out of 3</p>
			</div>
		</div>

		<div class="clear"></div>

		<div class="grade-details">
			<table class="entries">
				<caption>Grade Details</caption>
				<thead>
					<tr>
						<td class="grade-details-title">Assignment</td>
						<td class="grade-details-score">Score</td>
						<td class="grade-details-date">Date taken</td>
					</tr>
				</thead>
				<tbody>
					<? foreach($assets as $a) : ?>
						<? 
							$grade = rand(50, 100);
							if($grade < 60)
							{
								$class = 'stop';
							}
							elseif($grade >= 60 && $grade <70)
							{
								$class = 'yield';
							}
							else
							{
								$class = 'go';
							}
						?>
						<tr class="<?= $class ?>">
							<td class="grade-details-title"><a href="<?= $a->url ?>"><?= $a->title ?></a></td>
							<td class="grade-details-score"><?= $grade . '%' ?></td>
							<td class="grade-details-date"><?= date('M jS, Y') ?></td>
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