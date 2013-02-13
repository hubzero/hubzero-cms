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
				if($a->get('type') != 'exam' || $a->get('state') != COURSES_ASSET_PUBLISHED)
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

				// Get the form deployment based on crumb
				$dep = PdfFormDeployment::fromCrumb($crumb);

				// Loop through the results of the deployment
				foreach($dep->getResults() as $result)
				{
					// Create a per unit, per student count to track total items taken (needed when calculating weekly/unit averages)
					if(!isset($progress[$result['user_id']][$unit->get('id')]['form_count']))
					{
						$progress[$result['user_id']][$unit->get('id')]['sum']        = 0;
						$progress[$result['user_id']][$unit->get('id')]['form_count'] = 0;
					}

					// Store the score
					$progress[$result['user_id']][$unit->get('id')][$dep->getId()]['score'] = $result['score'];

					// Track the sum of scores for this unit and iterate the count
					$progress[$result['user_id']][$unit->get('id')]['sum'] += $result['score'];
					++$progress[$result['user_id']][$unit->get('id')]['form_count'];
				}
			}
		}
	}
}

?>

<table>
	<caption>Student Progress</caption>
	<thead>
		<tr>
			<td>Name</td>
			<? $i = 1 ?>
			<? foreach($this->course->offering()->units() as $unit) : ?>
				<td>Unit <?= $i ?></td>
				<? ++$i ?>
			<? endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<? foreach($members as $m) : ?>
			<tr>
				<td><?= JFactory::getUser($m->get('user_id'))->get('name'); ?></td>
				<? foreach($this->course->offering()->units() as $unit) : ?>
					<td>
						<? if(isset($progress[$m->get('user_id')]) && isset($progress[$m->get('user_id')][$unit->get('id')])) : ?>
							<?= round($progress[$m->get('user_id')][$unit->get('id')]['sum'] / $progress[$m->get('user_id')][$unit->get('id')]['form_count'], 2) . '%' ?>
						<? endif; ?>
					</td>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
	</tbody>
</table>