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

// Include needed form models
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'form.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'formRespondent.php');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'formDeployment.php');

// @TODO: implement for instructors, not just managers (i.e. manager sees all, instructor only sees their section)

// Get all section members
$members = $this->course->offering()->section()->members();

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
				if($a->get('type') != 'exam' || $a->get('state') != COURSES_STATE_PUBLISHED)
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
	<div class="flag"><div class="flag-inner" style="left:<?= $current_marker ?>%;"></div></div>
	<? foreach($members as $m) : ?>
		<div class="student">
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
			<div class="clear"></div>
			<div class="student-details">
				<table>
					<thead>
						<tr>
							<td>Title</td>
							<td>Score</td>
							<td>Date</td>
						</tr>
					</thead>
					<tbody>
						<? foreach($this->course->offering()->units() as $unit) : ?>
							<? if(isset($progress[$m->get('user_id')]) && isset($progress[$m->get('user_id')][$unit->get('id')])) : ?>
								<? foreach($progress[$m->get('user_id')][$unit->get('id')]['forms'] as $form) : ?>
									<tr>
										<td><?= $form['title'] ?></td>
										<td><?= round($form['score'], 2) . '%' ?></td>
										<td><?= $form['finished'] ?></td>
									</tr>
								<? endforeach; ?>
							<? endif; ?>
						<? endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="clear"></div>
	<? endforeach; ?>
</div>