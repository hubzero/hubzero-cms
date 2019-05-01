<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$time = $this->time;
?>

<div class="input-wrap">
	<label for="field-title"><?php echo Lang::txt('COM_RESOURCES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
	<input type="text" name="fields[title]" id="field-title" maxlength="250" class="required" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
</div>

<div class="input-wrap">
	<label><?php echo Lang::txt('COM_RESOURCES_FIELD_TYPE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
	<?php echo $this->lists['type']; ?>
</div>

<?php if ($this->row->standalone == 1) { ?>
	<div class="input-wrap">
		<label for="field-alias"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS'); ?>:</label><br />
		<input type="text" name="fields[alias]" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
	</div>

	<div class="input-wrap">
		<label for="field-license"><?php echo Lang::txt('COM_RESOURCES_FIELD_LICENSE'); ?>:</label><br />
		<select name="fields[license]" id="field-license">
			<option value=""<?php echo ($this->row->get('license', $this->row->params->get('license')) == '') ? 'selected="selected"' : '';?>><?php echo Lang::txt('COM_RESOURCES_NONE'); ?></option>
			<?php foreach ($this->licenses as $license) { ?>
				<option value="<?php echo $license->get('name'); ?>"<?php echo ($this->row->get('license', $this->row->params->get('license')) == $license->get('name')) ? 'selected="selected"' : ''; ?>><?php echo $license->get('title'); ?></option>
			<?php } ?>
		</select>
	</div>

	<div class="grid">
		<div class="col span6">
			<div class="input-wrap">
				<label for="attrib-location"><?php echo Lang::txt('COM_RESOURCES_FIELD_LOCATION'); ?>:</label><br />
				<input type="text" name="attrib[location]" id="attrib-location" maxlength="250" value="<?php echo $this->row->attribs->get('location', ''); ?>" />
			</div>
		</div>
		<div class="col span6">
			<div class="input-wrap">
				<label for="attrib-timeof"><?php echo Lang::txt('COM_RESOURCES_FIELD_TIME'); ?>:</label><br />
				<input type="text" name="attrib[timeof]" id="attrib-timeof" maxlength="250" value="<?php echo $time ? Date::of($time)->toLocal('Y-m-d H:i:s') : ''; ?>" placeholder="YYYY-MM-DD hh:mm:ss" />
			</div>
		</div>
	</div>

	<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_FIELD_CANONICAL_HINT'); ?>">
		<label for="attrib-canonical"><?php echo Lang::txt('COM_RESOURCES_FIELD_CANONICAL'); ?>:</label><br />
		<input type="text" name="attrib[canonical]" id="attrib-canonical" maxlength="250" value="<?php echo $this->row->attribs->get('canonical', ''); ?>" />
		<span class="hint"><?php echo Lang::txt('COM_RESOURCES_FIELD_CANONICAL_HINT'); ?></span>
	</div>
<?php } else { ?>
	<div class="input-wrap">
		<label><?php echo Lang::txt('COM_RESOURCES_FIELD_LOGICAL_TYPE'); ?>:</label><br />
		<?php echo $this->lists['logical_type']; ?>
		<input type="hidden" name="fields[alias]" value="" />
	</div>

	<div class="input-wrap">
		<label for="field-path"><?php echo Lang::txt('COM_RESOURCES_FIELD_PATH'); ?>:</label><br />
		<input type="text" name="fields[path]" id="field-path" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('path'))); ?>" />
	</div>

	<div class="input-wrap">
		<label for="attrib[duration]"><?php echo Lang::txt('COM_RESOURCES_FIELD_DURATION'); ?>:</label><br />
		<input type="text" name="attrib[duration]" id="attrib[duration]" maxlength="100" value="<?php echo $this->row->attribs->get('duration', ''); ?>" />
	</div>

	<div class="grid">
		<div class="col span6">
			<div class="input-wrap">
				<label for="attrib[width]"><?php echo Lang::txt('COM_RESOURCES_FIELD_WIDTH'); ?>:</label><br />
				<input type="text" name="attrib[width]" id="attrib[width]" maxlength="250" value="<?php echo $this->row->attribs->get('width', ''); ?>" />
			</div>
		</div>
		<div class="col span6">
			<div class="input-wrap">
				<label for="attrib[height]"><?php echo Lang::txt('COM_RESOURCES_FIELD_HEIGHT'); ?>:</label><br />
				<input type="text" name="attrib[height]" id="attrib[height]" maxlength="250" value="<?php echo $this->row->attribs->get('height', ''); ?>" />
			</div>
		</div>
	</div>

	<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_FIELD_ATTRIBUTES_HINT'); ?>">
		<label for="attrib[attributes]"><?php echo Lang::txt('COM_RESOURCES_FIELD_ATTRIBUTES'); ?>:</label><br />
		<input type="text" name="attrib[attributes]" id="attrib[attributes]" maxlength="100" value="<?php echo $this->row->attribs->get('attributes', ''); ?>" /><br />
		<span class="hint"><?php echo Lang::txt('COM_RESOURCES_FIELD_ATTRIBUTES_HINT'); ?></span>
	</div>
<?php } ?>
<div class="input-wrap">
	<label for="field-introtext"><?php echo Lang::txt('COM_RESOURCES_FIELD_INTRO_TEXT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
	<?php
	echo $this->editor('fields[introtext]', $this->escape(stripslashes($this->row->get('introtext'))), 45, 5, 'field-introtext', array('buttons' => false));
	?>
</div>
<div class="input-wrap">
	<label for="field-fulltxt"><?php echo Lang::txt('COM_RESOURCES_FIELD_MAIN_TEXT'); ?>:</label><br />
	<?php
	echo $this->editor('fields[fulltxt]', $this->escape($this->row->description), 45, 15, 'field-fulltxt', array('buttons' => false));
	?>
</div>

