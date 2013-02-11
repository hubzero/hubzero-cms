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
defined('_JEXEC') or die( 'Restricted access' );

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias');

define('COURSES_ASSET_UNPUBLISHED', 0);
define('COURSES_ASSET_PUBLISHED',   1);
define('COURSES_ASSET_DELETED',     2);

?>

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="dialog-confirm" class="dialog">This is a dialog box</div>

<div class="error-box">
	<p class="error-close"></p>
	<p class="error-message">There was an error</p>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="course btn" href="<?php echo JRoute::_($base); ?>">
				<?php echo JText::sprintf('MY_COURSE', $this->course->get('title')); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<? foreach($this->notifications as $notification) : ?>
	<p class="<?= $notification['type'] ?>"><?= $notification['message'] ?></p>
<? endforeach; ?>

<div class="outline-main">
	<div class="delete-tray closed">
		<div class="lock unlocked"></div>
		<h4>&nbsp; D e l e t e d &nbsp; A s s e t s</h4>
		<ul class="assets-deleted">

<?php
			foreach ($this->course->offering->units() as $unit) :
				foreach($unit->assetgroups() as $agt) :
					foreach($agt->children() as $ag) :
						if ($ag->assets()->total()) :
							foreach ($ag->assets() as $a) :
								if($a->get('state') == COURSES_ASSET_DELETED) :
									$view = new JView(
											array(
												'name'      => 'manage',
												'layout'    => 'asset_partial')
										);
									$view->base   = $base;
									$view->course = $this->course;
									$view->unit   = $unit;
									$view->ag     = $ag;
									$view->a      = $a;
									$view->display();
								endif;
							endforeach;
						endif;
					endforeach;
				endforeach;
			endforeach;
?>

		</ul>
	</div>

	<ul class="unit">

		<? foreach ($this->course->offering->units() as $unit) : ?>
		<li class="unit-item">
			<div class="unit-title-arrow"></div>
			<div class="title unit-title toggle-editable"><?php echo $unit->get('title'); ?></div>
			<div class="title-edit">
				<form action="/api/courses/unitsave" class="title-form">
					<input class="uniform title-text" name="title" type="text" value="<?php echo $unit->get('title'); ?>" />
					<input class="uniform title-save" type="submit" value="Save" />
					<input class="uniform title-reset" type="reset" value="Cancel" />
					<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
					<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
					<input type="hidden" name="id" value="<?php echo $unit->get('id'); ?>" />
				</form>
			</div>
			<div class="calendar">
				<form action="/courses/<?php echo $this->course->get('alias'); ?>/manage/<?php echo $this->course->offering()->get('alias'); ?>" class="calendar-form">
					<input type="hidden" name="scope" value="unit" />
					<input type="hidden" name="scope_id" value="<?php echo $unit->get('id'); ?>" />
				</form>
			</div>
			<div class="progress-container">
				<div class="progress-indicator"></div>
			</div>
			<div class="clear"></div>

			<ul class="asset-group-type-list">

			<? foreach($unit->assetgroups() as $agt) : ?>

				<li class="asset-group-type-item">
					<div class="asset-group-title title"><?php echo $agt->get('title'); ?></div>
					<div class="clear"></div>
					<ul class="asset-group sortable">

<?php
				// Loop through our asset groups
				foreach($agt->children() as $ag)
				{
					$view = new JView(
							array(
								'name'      => 'manage',
								'layout'    => 'asset_group_partial')
						);
					$view->base   = $base;
					$view->course = $this->course;
					$view->unit   = $unit;
					$view->ag     = $ag;
					$view->display();
				}

				// Now display assets directly attached to the asset group type
				if ($agt->assets()->total())
				{
					$view = new JView(
							array(
								'name'      => 'manage',
								'layout'    => 'asset_group_partial')
						);
					$view->base   = $base;
					$view->course = $this->course;
					$view->unit   = $unit;
					$view->ag     = $agt;
					$view->display();
				}
?>

						<li class="add-new asset-group-item">
							Add a new <?php echo strtolower(rtrim($agt->get('title'), 's')); ?>
							<form action="/api/courses/assetgroupsave">
								<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
								<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
								<input type="hidden" name="unit_id" value="<?php echo $unit->get('id'); ?>" />
								<input type="hidden" name="parent" value="<?php echo $agt->get('id'); ?>" />
							</form>
						</li>
					</ul>
				</li>

			<? endforeach; // foreach asset groups ?>

			</ul>
<?php
			if ($unit->assets()->total())
			{
?>
				<ul class="assets-list">
<?php
				foreach ($unit->assets() as $a)
				{
					$href = $a->path($this->course->get('id'));
					if ($a->get('type') == 'video')
					{
						$href = JRoute::_($base . '&active=outline&a=' . $unit->get('alias'));
					}
					echo '<li class="asset-group-item"><a class="asset ' . $a.get('type') . '" href="' . $href . '">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
				}
?>
				</ul>
<?php
			}
?>
		</li>

		<? endforeach; // foreach unit ?>

		<li class="add-new unit-item">
			Add a new unit
			<form action="/api/courses/unitsave">
				<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
				<input type="hidden" name="offering_id" value="<?php echo $this->course->offering()->get('id'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
			</form>
		</li>
	</ul>
</div>