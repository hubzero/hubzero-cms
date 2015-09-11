<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ASSET_GROUPS') . ': ' . $text, 'courses.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

Html::behavior('modal');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if ($('#field-title').val() == '') {
		alert('<?php echo Lang::txt('COM_COURSES_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
jQuery(document).ready(function($){
	document.assetform = $.fancybox;
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
			<input type="hidden" name="fields[unit_id]" value="<?php echo $this->row->get('unit_id'); ?>" />
			<input type="hidden" name="unit" value="<?php echo $this->row->get('unit_id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_PARENT_HINT'); ?>">
				<label for="field-parent"><?php echo Lang::txt('COM_COURSES_FIELD_PARENT'); ?>:</label><br />
				<select name="fields[parent]" id="field-parent">
					<option value="0"<?php if (0 == $this->row->get('parent')) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_NONE'); ?></option>
					<?php foreach ($this->assetgroups as $assetgroup) { ?>
						<option value="<?php echo $assetgroup->get('id'); ?>"<?php if ($assetgroup->get('id') == $this->row->get('parent')) { echo ' selected="selected"'; } ?>><?php echo $assetgroup->treename . $this->escape(stripslashes($assetgroup->get('title'))); ?></option>
					<?php } ?>
				</select>
				<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_PARENT_HINT'); ?></span>
			</div>
			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
			</div>
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_SHORT_DESCRIPTION_HINT'); ?>">
				<label for="field-description"><?php echo Lang::txt('COM_COURSES_FIELD_DESCRIPTION'); ?>:</label><br />
				<input type="text" name="fields[description]" id="field-description" value="<?php echo $this->escape(stripslashes($this->row->get('description'))); ?>">
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_ASSETS'); ?></span></legend>
			<?php if ($this->row->get('id')) { ?>
				<iframe width="100%" height="400" name="assets" id="assets" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=assets&tmpl=component&scope=asset_group&scope_id=' . $this->row->get('id') . '&course_id=' . $this->offering->get('course_id')); ?>"></iframe>
			<?php } else { ?>
				<p><?php echo Lang::txt('COM_COURSES_ENTRY_MUST_BE_SAVED_BEFORE_ASSETS'); ?></p>
			<?php } ?>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_COURSES_FIELD_UNIT_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('unit_id')); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_COURSES_FIELD_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
			<?php if ($this->row->get('created')) { ?>
				<tr>
					<th><?php echo Lang::txt('COM_COURSES_FIELD_CREATED'); ?></th>
					<td><?php echo $this->escape($this->row->get('created')); ?></td>
				</tr>
			<?php } ?>
			<?php if ($this->row->get('created_by')) { ?>
				<tr>
					<th><?php echo Lang::txt('COM_COURSES_FIELD_CREATOR'); ?></th>
					<td><?php
					$creator = User::getInstance($this->row->get('created_by'));
					echo $this->escape(stripslashes($creator->get('name'))); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label><br />
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></option>
				</select>
			</div>
		</fieldset>

		<?php
			if ($plugins = Event::trigger('courses.onAssetgroupEdit'))
			{
				$data = $this->row->get('params');

				foreach ($plugins as $plugin)
				{
					$default = Plugin::params('courses', $plugin['name']);

					$param = new \Hubzero\Html\Parameter(
						(is_object($data) ? $data->toString() : $data),
						PATH_CORE . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
					);
					foreach ($default->toArray() as $k => $v)
					{
						if (substr($k, 0, strlen('default_')) == 'default_')
						{
							$param->def(substr($k, strlen('default_')), $default->get($k, $v));
						}
					}
					$out = $param->render('params', 'onAssetgroupEdit');

					if (!$out)
					{
						continue;
					}
					?>
					<fieldset class="adminform eventparams" id="params-<?php echo $plugin['name']; ?>">
						<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_PARAMETERS', $plugin['title']); ?></span></legend>
						<div class="input-wrap">
							<?php echo $out; ?>
						</div>
					</fieldset>
					<?php
				}
			}
		?>
	</div>
	<div class="clr"></div>

	<?php echo Html::input('token'); ?>
</form>
