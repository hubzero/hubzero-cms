<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = "Duplicate Assets For Existing Asset Groups";

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . $text, 'courses.png');

// SAVE and CANCEL
// Toolbar::save();
Toolbar::cancel();

Html::behavior('framework', true);

// Remove datapicker error
$this->css('duplicateAssets')
	 ->js('duplicateAssets');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>&amp;task=dupassets" 
	method="post" name="adminForm" id="item-form" class="editform form-validate" 
	data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	
	<div class="grid">
		
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span>Assets to Duplicate</span></legend>
				
				<!-- Ids that will be passed to form -->
				<input type="hidden" name="assetGroupIdToDuplicate" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="assetGroupParentIdToDuplicate" value="<?php echo $this->row->get('parent'); ?>" />
				<input type="hidden" name="unitIdToDuplicate" value="<?php echo $this->unit->get('id'); ?>" />
				<input type="hidden" name="offeringIdToDuplicate" value="<?php echo $this->offering->get('id'); ?>" />
				<input type="hidden" name="courseIdToDuplicate" value="<?php echo $this->course->get('id'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
				<input type="hidden" name="task" value="dupassets" />
				<input type="hidden" name="action" value="dupassets" />

				<div class="input-wrap" id="selectDropdownForAssetGroups">
					<label for="field-parent">Save to which Asset Group?</label><br />
					<select id="coursesSelect" name="filterCourses">
						<option selected disabled>Select a Course</option>
					</select>
					<br><br>
					<select id="offeringsSelect" name="filterOfferings">
						<option selected disabled>Select a Course Offering</option>
					</select>
					<br><br>
					<select id="unitsSelect" name="filterUnits">
						<option selected disabled>Select a Course Unit</option>
					</select>
					<br><br>
					<select id="assetGroupsSelect" name="filterAssetGroups">
						<option selected disabled>Select a Asset Group</option>
					</select>
				</div>

				<!-- https://stage.stemedhub.org/administrator/index.php?option=com_courses&controller=assets&tmpl=component&scope=asset_group&scope_id=92&course_id=8 -->
				<!-- SQL: select * from jos_courses_assets; -->
				<p><strong><?php echo $this->escape($this->assetsCount); ?></strong> Assets That Will Be Copied from Current Asset Group <strong><?php echo $this->escape($this->row->get('id')); ?></strong> --> Asset Group Selected ABOVE.</p>
				<table class="adminlist">
					<thead style="background: black">
						<tr>
							<th scope="col">Asset Id</th>
							<th scope="col">Asset Title</th>
							<th scope="col">Type</th>
							<th scope="col">State</th>
							<th scope="col">Ordering</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$i = 0; $k = 0; $n = count($this->assetrows);
							foreach ($this->assetrows as $row) {
						?>
							<tr class="<?php echo "row$k"; ?>">
								<td><?php echo $this->escape($row->id); ?></td>
								<td><?php echo $this->escape(stripslashes($row->title)); ?></td>
								<td><?php echo $this->escape(stripslashes($row->type)); ?></td>
								<td>
									<?php if ($row->state == 2) { ?>
										Trashed
									<?php } else if ($row->state == 1) { ?>
										Published
									<?php } else { ?>
										Unpublished 
									<?php } ?>
								</td>
								<td><?php echo $this->escape(stripslashes($row->ordering)); ?></td>
							</tr>
						<?php 
							$i++; $k = 1 - $k;
						} ?>
					</tbody>
				</table>
			</fieldset>
			<input type="submit" class="btn" value="DUPLICATE ASSETS" />
		</div>
		

		<div class="col span5">
			<table class="meta">
				<thead style="background: black">
					<tr>
						<th colspan="2" style="font-weight: bolder">Course Meta Data</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row">Course Id (<a href='/administrator/index.php?option=com_courses&controller=courses'>All</a>)</th>
						<td>
							<a href='/administrator/index.php?option=com_courses&controller=courses&task=edit&id=<?php echo $this->escape($this->course->get('id')); ?>'>
								<?php echo $this->escape($this->course->get('id')); ?>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">Course Title</th>
						<td><?php echo $this->escape($this->course->get('title')); ?></td>
					</tr>
					<tr>
						<th scope="row">Course Alias</th>
						<td><?php echo $this->escape($this->course->get('alias')); ?></td>
					</tr>
				</tbody>
			</table>
			<table class="meta">
				<thead style="background: black">
					<tr>
						<th colspan="2" style="font-weight: bolder">Offering Meta Data</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row">Offering Id</th>
						<td>
							<a href='/administrator/index.php?option=com_courses&controller=offerings&course=<?php echo $this->escape($this->course->get('id')); ?>'>
								<?php echo $this->escape($this->offering->get('id')); ?>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">Offering Title</th>
						<td><?php echo $this->escape($this->offering->get('title')); ?></td>
					</tr>
					<tr>
						<th scope="row">Offering Alias</th>
						<td><?php echo $this->escape($this->offering->get('alias')); ?></td>
					</tr>
				</tbody>
			</table>
			<table class="meta">
				<thead style="background: black">
					<tr>
						<th colspan="2" style="font-weight: bolder">Unit Meta Data</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row">Unit Id</th>
						<td>
							<a href='/administrator/index.php?option=com_courses&controller=units&offering=<?php echo $this->escape($this->offering->get('id')); ?>'>
								<?php echo $this->escape($this->unit->get('id')); ?>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">Unit Title</th>
						<td><?php echo $this->escape($this->unit->get('title')); ?></td>
					</tr>
					<tr>
						<th scope="row">Unit Alias</th>
						<td><?php echo $this->escape($this->unit->get('alias')); ?></td>
					</tr>
				</tbody>
			</table>
			<table class="meta">
				<thead style="background: black">
					<tr>
						<th colspan="2" style="font-weight: bolder">Asset Group Meta Data</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row">Asset Group Id</th>
						<td>
							<a href='/administrator/index.php?option=com_courses&controller=assetgroups&unit=<?php echo $this->escape($this->unit->get('id')); ?>'>
								<?php echo $this->escape($this->row->get('id')); ?>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">Asset Group Title</th>
						<td><?php echo $this->escape($this->row->get('title')); ?></td>
					</tr>
					</tr>
					<tr>
						<th scope="row">Asset Group PARENT</th>
						<td><?php echo $this->escape($this->row->get('parent')); ?></td>
					</tr>
					<tr>
						<th scope="row">Asset Group Alias</th>
						<td><?php echo $this->escape($this->row->get('alias')); ?></td>
					</tr>
					<?php if ($this->row->get('created')) { ?>
						<tr>
							<th scope="row">Created On</th>
							<td><time datetime="<?php echo $this->escape($this->row->get('created')); ?>"><?php echo $this->escape(Date::of($this->row->get('created'))->toLocal()); ?></time></td>
						</tr>
					<?php } ?>
					<?php if ($this->row->get('created_by')) { ?>
						<tr>
							<th scope="row">Created By</th>
							<td><?php
								$creator = User::getInstance($this->row->get('created_by'));
								echo $this->escape(stripslashes($creator->get('name'))); ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>
