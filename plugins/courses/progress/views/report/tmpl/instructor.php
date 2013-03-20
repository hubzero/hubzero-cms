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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradebook.php');

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias');

// Get all section members
$members = $this->course->offering()->section()->members();

$member_ids = array();

foreach ($members as $m)
{
	$member_ids[] = $m->get('user_id');
}

$gradebook = new CoursesModelGradeBook(null);
$grades    = $gradebook->getGrades($member_ids, array('unit', 'course'));

// Loop through all assets
foreach($this->course->offering()->units() as $unit)
{
	foreach($unit->assetgroups() as $agt)
	{
		foreach($agt->children() as $ag)
		{
			// @FIXME: also grab assets from asset groups and units, add them to an array, and merge that in with array below to iterate over
			foreach($ag->assets() as $a)
			{
				// Only interested in forms/exams
				if($a->get('type') != 'exam' || !$a->isPublished())
				{
					continue;
				}

				// Check for result for given student on form
				preg_match('/\?crumb=([-a-zA-Z0-9]{20})/', $a->get('url'), $matches);

				$crumb = false;

				if(isset($matches[1]))
				{
					$crumb = $matches[1];
				}

				if(!$crumb)
				{
					// Break foreach, this is not a valid form!
					continue;
				}

				// Count total number of forms
				$form_count = (isset($form_count)) ? ++$form_count : 1;

				// Also count total forms through current unit
				if($unit->isAvailable() || $unit->ended())
				{
					$form_count_current = (isset($form_count_current)) ? ++$form_count_current : 1;
				}

				// Get the form deployment based on crumb
				$dep = PdfFormDeployment::fromCrumb($crumb);

				// Loop through the results of the deployment
				foreach($dep->getResults() as $result)
				{
					// Create a per student form count
					if(!isset($progress[$result['user_id']]['form_count']))
					{
						$progress[$result['user_id']]['form_count'] = 0;
					}

					// Store the score
					$progress[$result['user_id']][$unit->get('id')]['forms'][$dep->getId()]['score']    = $result['score'];
					$progress[$result['user_id']][$unit->get('id')]['forms'][$dep->getId()]['finished'] = $result['finished'];
					$progress[$result['user_id']][$unit->get('id')]['forms'][$dep->getId()]['title']    = $a->get('title');

					// Track the sum of scores for this unit and iterate the count
					++$progress[$result['user_id']]['form_count'];
				}
			}
		}
	}
}

$form_count     = (isset($form_count)) ? $form_count : 1;
$current_marker = (isset($form_count_current) && isset($form_count)) ? (round(($form_count_current / $form_count)*100, 2)) : 0;

?>

<div class="instructor">
	<? if(count($members) > 0) : ?>
		<div class="flag"><div class="flag-inner" style="left:<?= $current_marker ?>%;"></div></div>
		<? foreach($members as $m) : ?>
			<div class="student">
				<a href="<?= JRoute::_($base . '&active=progress&id=' . $m->get('user_id')) ?>">
					<div class="student-name"><?= JFactory::getUser($m->get('user_id'))->get('name'); ?></div>
					<div class="progress-bar-container">
						<? if(isset($progress[$m->get('user_id')])) : ?>
							<?
								$studentProgress = ($progress[$m->get('user_id')]['form_count'] / $form_count)*100;
								$studentStatus   = ($studentProgress / $current_marker)*100;
								$cls = '';
								if($studentStatus < 60)
								{
									$cls = ' stop';
								}
								elseif($studentStatus >= 60 && $studentStatus < 70)
								{
									$cls = ' yield';
								}
								elseif($studentStatus >= 70 && $studentStatus <= 100)
								{
									$cls = ' go';
								}
							?>
							<div class="student-progress-bar <?= $cls ?>" style="width:<?= $studentProgress ?>%;"></div>
						<? endif; ?>
					</div>
				</a>
				<div class="clear"></div>
				<div class="student-details grades">
					<div class="units">
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
					<a class="more-details btn" href="<?= JRoute::_($base . '&active=progress&id=' . $m->get('user_id')) ?>">More details</a>
				</div>
			</div>
			<div class="clear"></div>
		<? endforeach; ?>
	<? else : ?>
		<p class="info">The section does not currently have anyone enrolled</p>
	<? endif; ?>
</div>
<div class="refresh">
	<p>
		Does something look incorrect above? Try <a href="<?= JRoute::_($base . '&active=progress&action=refresh') ?>">refreshing</a> the scores!
	</p>
</div>