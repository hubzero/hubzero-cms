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

// @TODO: implement for instructors, not just managers (i.e. manager sees all, instructor only sees their section)
// @TODO: implement method for getting actual exam scores from form controller

// Get all section members
$members = $this->course->offering()->section()->members();

$tbl = new CoursesTableAsset(JFactory::getDBO());

$assets = $tbl->find(array(
	'w' => array(
		'course_id'  => $this->course->get('id'),
		'asset_type' => 'exam'
	)
));

?>

<table class="entries">
	<caption>Student Progress</caption>
	<thead>
		<tr>
			<td>Name</td>
			<? foreach($assets as $a) : ?>
				<td><?= $a->title; ?></td>
			<? endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<? foreach($members as $m) : ?>
			<tr>
				<td><?= JFactory::getUser($m->get('user_id'))->get('name'); ?></td>
				<? foreach($assets as $a) : ?>
					<td><?= rand(0, 100) . '%' ?></td>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
	</tbody>
</table>