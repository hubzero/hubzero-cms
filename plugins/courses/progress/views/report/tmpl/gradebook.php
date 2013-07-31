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

$base  = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias');
$base .= ($this->course->offering()->section()->get('alias') != '__default' ? ':' . $this->course->offering()->section()->get('alias') : '');

// Get all section members
$members = $this->course->offering()->section()->members(array('student'=>1));

// Refresh the grades
$this->course->offering()->gradebook()->refresh();

// Get the grades
$grades = $this->course->offering()->gradebook()->grades();

// Get the assets
$asset  = new CoursesTableAsset(JFactory::getDBO());
$assets = $asset->find(
	array(
		'w' => array(
			'course_id'  => $this->course->get('id'),
			'section_id' => $this->course->offering()->section()->get('id'),
			'asset_type' => 'form',
			'state'      => 1
		),
		'order_by'  => 'title',
		'order_dir' => 'ASC'
	)
);

$base = '/index.php?option='.$this->option.'&controller=offering&active=progress&gid='.$this->course->get('alias');
$base .= '&offering='.$this->course->offering()->get('alias').'&section='.$this->course->offering()->section()->get('alias');

?>

<div class="gradebook">
	<form action="<?php echo JRoute::_($base); ?>" class="gradebook-form">
		<table>
			<thead>
				<tr>
					<td class="search-box"><input type="text" placeholder="Search students" /></td>
					<?php foreach ($assets as $a) : ?>
						<td class="form-name" title="<?php echo $a->title; ?>">
							<div class="form-name-inner">
								<div class="form-title">
									<?php echo (strlen($a->title) < 10) ? $a->title : substr($a->title, 0, 10) . '...'; ?>
								</div>
								<div class="form-type">
									<select name="type" disabled="disabled">
										<option value="exam"<?php echo ($a->subtype == 'exam') ? ' selected="selected"' : ''; ?>>Exam</option>
										<option value="quiz"<?php echo ($a->subtype == 'quiz') ? ' selected="selected"' : ''; ?>>Quiz</option>
										<option value="homework"<?php echo ($a->subtype == 'homework') ? ' selected="selected"' : ''; ?>>Homework</option>
									</select>
								</div>
							</div>
						</td>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php $cls = 'even'; ?>
				<?php foreach ($members as $m) : ?>
					<tr class="row-entry <?php echo $cls; ?>">
						<?php $name = JFactory::getUser($m->get('user_id'))->get('name'); ?>
						<td class="cell-title" title="<?php echo $name; ?>">
							<?php echo (strlen($name) < 25) ? $name : substr($name, 0, 25) . '...'; ?>
						</td>
						<?php foreach ($assets as $a) : ?>
							<td class="cell-entry" data-asset-id="<?php echo $a->id; ?>" data-student-id="<?php echo $m->get('user_id'); ?>">
								<div class="cell-score"><?php echo $grades[$m->get('user_id')]['assets'][$a->id]['score']; ?></div>
								<div class="override<?php echo ($grades[$m->get('user_id')]['assets'][$a->id]['override']) ? ' active' : '';?>"></div>
							</td>
						<?php endforeach; ?>
					</tr>
					<?php $cls = ($cls == 'even') ? 'odd' : 'even'; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<!--<div class="add">Add a new grade entry</div>-->
	</form>
</div>