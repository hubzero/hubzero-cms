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

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . ($this->course->offering()->section()->get('alias') != '__default' ? ':' . $this->course->offering()->section()->get('alias') : '');

?>

<div class="header">
	<a href="#" class="trash btn">Deleted Assets</a>
	<a href="<?php echo JRoute::_($base . '&active=outline'); ?>" class="done btn">Done</a>
	<h3><?php echo $this->title; ?></h2>
</div>

<div id="dialog-confirm" class="dialog">This is a dialog box</div>

<div class="content-box-overlay"></div>

<div class="content-box">
	<h3 class="content-box-header">
		<span>Create a note</span>
		<div class="content-box-close"></div>
	</h3>
	<div class="content-box-inner">
		<div class="loading-bar"></div>
	</div>
</div>

<div class="error-box">
	<p class="error-close"></p>
	<p class="error-message">There was an error</p>
</div>

<div class="outline-main">
	<div class="delete-tray">
		<h4>Deleted Assets</h4>
		<ul class="assets-deleted">

<?php
			foreach ($this->course->offering()->units() as $unit) :
				foreach($unit->assetgroups() as $agt) :
					foreach($agt->children() as $ag) :
						if ($ag->assets()->total()) :
							foreach ($ag->assets() as $a) :
								if($a->get('state') == COURSES_STATE_DELETED) :
									$view = new Hubzero_Plugin_View(
										array(
											'folder'  => 'courses',
											'element' => 'outline',
											'name'    => 'outline',
											'layout'  => 'asset_partial'
										)
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

		<? foreach ($this->course->offering()->units() as $unit) : ?>
		<li class="unit-item" id="unit_<?= $unit->get('id') ?>">
			<div class="unit-title-arrow"></div>
			<div class="unit-edit-container">
				<div class="title unit-title">
					<div class="unit-title-value"><?php echo $unit->get('title'); ?></div>
					<div class="edit">edit</div>
				</div>
				<div class="clear"></div>
				<div class="unit-edit">
					<form action="/api/courses/unit/save" class="unit-edit-form">
						<label for="title">Title:</label>
						<input class="unit-edit-text" name="title" type="text" value="<?php echo $unit->get('title'); ?>" placeholder="title" />
						<label for="publish_up">Publish start date:</label>
						<input class="unit-edit-publish-up datepicker" name="publish_up" type="text" value="<?= $unit->get('publish_up') ?>" placeholder="Publish start date" />
						<label for="publish_down">Publish end date:</label>
						<input class="unit-edit-publish-down datepicker" name="publish_down" type="text" value="<?= $unit->get('publish_down') ?>" placeholder="Publish end date" />
						<input class="unit-edit-save" type="submit" value="Save" />
						<input class="unit-edit-reset" type="reset" value="Cancel" />
						<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
						<input type="hidden" name="section_id" value="<?= $this->course->offering()->section()->get('id') ?>" />
						<input type="hidden" name="id" value="<?php echo $unit->get('id'); ?>" />
					</form>
				</div>
			</div>
			<div class="progress-container">
				<div class="progress-indicator"></div>
			</div>
			<div class="clear"></div>

			<ul class="asset-group-type-list">

			<? foreach($unit->assetgroups() as $agt) : ?>

				<li class="asset-group-type-item <?= ($agt->get('state') == '1') ? 'published' : 'unpublished' ?>">
					<div class="asset-group-type-item-container">
						<div class="asset-group-title-container">
							<div class="asset-group-title title">
								<div class="asset-group-title-edit edit">edit</div>
								<div class="title"><?php echo $agt->get('title'); ?></div>
							</div>
							<form action="/api/courses/assetgroup/save">
								<div class="label-input-pair">
									<label for="title">Title:</label>
									<input class="" name="title" type="text" value="<?= $agt->get('title') ?>" />
								</div>
								<div class="label-input-pair">
									<label for="state">Published:</label>
									<select name="state">
										<option value="0"<?= ($agt->get('state') == '0') ? ' selected="selected"' : '' ?>>No</option>
										<option value="1"<?= ($agt->get('state') == '1') ? ' selected="selected"' : '' ?>>Yes</option>
									</select>
								</div>
								<div class="label-input-pair">
									<label for="description">Description:</label>
									<textarea name="description" rows="4"><?= $agt->get('description') ?></textarea>
								</div>
								<input class="asset-group-title-save" type="submit" value="Save" />
								<input class="asset-group-title-cancel" type="reset" value="Cancel" />
								<input type="hidden" name="course_id" value="<?= $this->course->get('id') ?>" />
								<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />
								<input type="hidden" name="id" value="<?= $agt->get('id') ?>" />
							</form>
						</div>
						<div class="asset-group-container">
							<ul class="asset-group sortable">

<?php
				// Loop through our asset groups
				foreach($agt->children() as $ag)
				{
					$view = new Hubzero_Plugin_View(
						array(
							'folder'  => 'courses',
							'element' => 'outline',
							'name'    => 'outline',
							'layout'  => 'asset_group_partial'
						)
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
					$view = new Hubzero_Plugin_View(
						array(
							'folder'  => 'courses',
							'element' => 'outline',
							'name'    => 'outline',
							'layout'  => 'asset_group_partial'
						)
					);
					$view->base   = $base;
					$view->course = $this->course;
					$view->unit   = $unit;
					$view->ag     = $agt;
					$view->display();
				}
?>

								<li class="add-new asset-group-item">
									Add a new <?php echo (substr($agt->get('title'), -3) == 'ies') ? strtolower(preg_replace('/ies$/', 'y', $agt->get('title'))) : strtolower(rtrim($agt->get('title'), 's')); ?>
									<form action="/api/courses/assetgroup/save">
										<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
										<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
										<input type="hidden" name="unit_id" value="<?php echo $unit->get('id'); ?>" />
										<input type="hidden" name="parent" value="<?php echo $agt->get('id'); ?>" />
									</form>
								</li>
							</ul>
						</div>
					</div>
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
					$href = JRoute::_($base . '&asset=' . $a->get('id'));
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
			<form action="/api/courses/unit/save">
				<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
				<input type="hidden" name="offering_id" value="<?php echo $this->course->offering()->get('id'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
				<input type="hidden" name="section_id" value="<?php echo $this->course->offering()->section()->get('id'); ?>" />
			</form>
		</li>
	</ul>
</div>