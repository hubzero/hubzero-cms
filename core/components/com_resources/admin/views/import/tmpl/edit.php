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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('import');
$this->js('import');

// set title
$title  = ($this->import->get('id')) ? Lang::txt('COM_RESOURCES_IMPORT_TITLE_EDIT') : Lang::txt('COM_RESOURCES_IMPORT_TITLE_ADD');

Toolbar::title(Lang::txt($title), 'import.png');
Toolbar::save();
Toolbar::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<?php foreach ($this->getErrors() as $error) : ?>
	<p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_NAME'); ?></label>
					<input type="text" name="import[name]" id="field-name" value="<?php echo $this->escape($this->import->get('name')); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-notes"><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_NOTES'); ?></label><br />
					<textarea name="import[notes]" id="field-notes" rows="5"><?php echo $this->escape($this->import->get('notes')); ?></textarea>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_DATA'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE_HINT', $this->import->fileSpacePath()); ?>">
					<label for="field-importfile">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE'); ?>
					</label><br />
					<select name="import[file]" id="field-importfile">
						<option value=""><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE_OPTION_NULL'); ?></option>
						<?php if (isset($this->files)): ?>
							<?php foreach ($this->files as $file): ?>
								<?php
								$file = ltrim($file, DS);
								$sel = ($this->import->get('file') == $file) ? 'selected="selected"' : '';
								?>
								<option <?php echo $sel; ?> value="<?php echo $file; ?>"><?php echo $file; ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<span class="hint">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE_HINT', $this->import->fileSpacePath()); ?>
					</span>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_HINT'); ?>">
					<label for="field-importmode">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE'); ?>
					</label><br />
					<select name="import[mode]" id="field-importmode" disabled="disabled">
						<option value="UPDATE">
							<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_UPDATE'); ?>
						</option>
						<option <?php if ($this->import->get('mode') == Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_PATCH')) { echo 'selected="selected"'; } ?> value="PATCH">
							<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_PATCH'); ?>
						</option>
					</select>
					<span class="hint">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_HINT'); ?>
					</span>
				</div>
			</fieldset>
			<?php
				// parse our hooks
				$hooks              = json_decode($this->import->get('hooks'));
				if (!is_object($hooks))
				{
					$hooks = new stdClass;
				}
				$hooks->postparse   = (isset($hooks->postparse)) ? $hooks->postparse : array();
				$hooks->postmap     = (isset($hooks->postmap)) ? $hooks->postmap : array();
				$hooks->postconvert = (isset($hooks->postconvert)) ? $hooks->postconvert : array();
			?>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_HOOKS'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
					<label for="field-name">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_POSTPARSEHOOK'); ?>
					</label><br />
					<select name="hooks[postparse][]" multiple="multiple" size="4">
						<?php if (isset($hooks->postparse)) : ?>
							<?php foreach ($hooks->postparse as $hook) : ?>
								<?php $importHook = $this->hooks->fetch('id', $hook); ?>
								<?php if (!empty($importHook)): ?>
								<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>

						<?php foreach ($this->hooks as $hook): ?>
							<?php if ($hook->get('type') != 'postparse' || in_array($hook->get('id'), $hooks->postparse)) { continue; } ?>
							<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
						<?php endforeach; ?>
					</select>
					<a class="hook-up" href="#">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
					</a> |
					<a class="hook-down" href="#">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
					</a><br />
					<span class="hint">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
					</span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
					<label for="field-hookpostmap">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_POSTMAPHOOK'); ?>
					</label>
					<select name="hooks[postmap][]" id="field-hookpostmap" multiple="multiple" size="4">
						<?php foreach ($hooks->postmap as $hook) : ?>
							<?php $importHook = $this->hooks->fetch('id', $hook); ?>
							<?php if (!empty($importHook)): ?>
							<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
							<?php endif; ?>
						<?php endforeach; ?>

						<?php foreach ($this->hooks as $hook): ?>
							<?php if ($hook->get('type') != 'postmap' || in_array($hook->get('id'), $hooks->postmap)) { continue; } ?>
							<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
						<?php endforeach; ?>
					</select>
					<a class="hook-up" href="#">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
					</a> |
					<a class="hook-down" href="#">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
					</a><br />
					<span class="hint">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
					</span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
					<label for="field-hookpostconvert">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_POSTCONVERTHOOK'); ?>
					</label>
					<select name="hooks[postconvert][]" id="field-hookpostconvert" multiple="multiple" size="4">
						<?php foreach ($hooks->postconvert as $hook) : ?>
							<?php $importHook = $this->hooks->fetch('id', $hook); ?>
							<?php if (!empty($importHook)): ?>
							<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php foreach ($this->hooks as $hook): ?>
							<?php if ($hook->get('type') != 'postconvert' || in_array($hook->get('id'), $hooks->postconvert)) { continue; } ?>
							<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
						<?php endforeach; ?>
					</select>
					<a class="hook-up" href="#">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
					</a> |
					<a class="hook-down" href="#">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
					</a><br />
					<span class="hint">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
					</span>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_PARAMS'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_STATUS_HINT'); ?>">
					<label for="param-status">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_STATUS'); ?>
					</label>
					<select name="params[status]" id="param-status">
						<option value="2"<?php echo ($this->params->get('status', 1) == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL'); ?></option>
						<option value="5"<?php echo ($this->params->get('status', 1) == 5) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_DRAFT_INTERNAL'); ?></option>
						<option value="3"<?php echo ($this->params->get('status', 1) == 3) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_RESOURCES_PENDING'); ?></option>
						<option value="0"<?php echo ($this->params->get('status', 1) == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php echo ($this->params->get('status', 1) == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="4"<?php echo ($this->params->get('status', 1) == 4) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
					<span class="hint"><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_STATUS_HINT'); ?></span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_ACCESS_HINT'); ?>">
					<label>
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_ACCESS'); ?>
					</label>
					<?php
						$rconfig = Component::params('com_resources');
						echo \Components\Resources\Helpers\Html::selectAccess($rconfig->get('accesses'), $this->params->get('access', 0), 'params[access]');
					?>
					<span class="hint"><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_ACCESS_HINT'); ?></span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_GROUP_HINT'); ?>">
					<label for="import-group">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_GROUP'); ?>
					</label>
					<?php echo \Components\Resources\Helpers\Html::selectGroup($this->groups, $this->params->get('group', ''), 'params[group]' ,'import-group'); ?>
					<span class="hint"><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_GROUP_HINT'); ?></span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_MATCHTITLE_HINT'); ?>">
					<label for="param-titlematch">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_MATCHTITLE'); ?>
					</label>
					<select name="params[titlematch]" id="param-titlematch">
						<option value="0"><?php echo Lang::txt('JNo'); ?></option>
						<option value="1" <?php if ($this->params->get('titlematch', 0) == 1) { echo 'selected="selected"'; } ?>><?php echo Lang::txt('JYes'); ?></option>
					</select>
					<span class="hint"><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_MATCHTITLE_HINT'); ?></span>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_HINT'); ?>">
					<label for="param-requiredfields">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED'); ?>
					</label>
					<select name="params[requiredfields]" id="param-requiredfields">
						<option value="0">
							<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_NO'); ?>
						</option>
						<option value="1" <?php if ($this->params->get('requiredfields', 1) == 1) { echo 'selected="selected"'; } ?>>
							<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_YES'); ?>
						</option>
					</select>
					<span class="hint">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_HINT'); ?>
					</span>
				</div>
			</fieldset>
		</div>
		<div class="col span4">
			<?php if ($this->import->get('id')) : ?>
				<table class="meta">
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_ID'); ?></th>
							<td><?php echo $this->import->get('id'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CREATEDBY'); ?></th>
							<td>
								<?php
									if ($created_by = User::getInstance($this->import->get('created_by')))
									{
										echo $created_by->get('name');
									}
								?>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_CREATEDON'); ?></th>
							<td>
								<?php
									echo Date::of($this->import->get('created_at'))->toLocal('m/d/Y @ g:i a');
								?>
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELDSET_UPLOAD'); ?></span></legend>

				<div class="input-wrap" data-hint=".csv, .xml">
					<label for="field-file">
						<?php echo Lang::txt('COM_RESOURCES_IMPORT_EDIT_FIELD_DATAFILEUPLOAD'); ?>
					</label>
					<input type="file" name="file" id="field-file" />
					<span>.csv, .xml</span>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="import[id]" value="<?php echo $this->import->get('id'); ?>" />

	<?php echo Html::input('token'); ?>
</form>
