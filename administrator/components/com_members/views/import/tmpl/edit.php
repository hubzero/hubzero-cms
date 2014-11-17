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

$this->css('import')
     ->js('import');

$canDo = MembersHelper::getActions('component');

// set title
$title  = ($this->import->get('id')) ? JText::_('COM_MEMBERS_IMPORT_TITLE_EDIT') : JText::_('COM_MEMBERS_IMPORT_TITLE_ADD');

JToolBarHelper::title(JText::_('COM_MEMBERS') . ': ' . $title, 'import.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
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

<form action="<?php echo JRoute::_('index.php?option=com_members&controller=import&task=save'); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELDSET_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-name"><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_NAME'); ?></label>
				<input type="text" name="import[name]" id="field-name" value="<?php echo $this->escape($this->import->get('name')); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-notes"><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_NOTES'); ?></label><br />
				<textarea name="import[notes]" id="field-notes" rows="5"><?php echo $this->escape($this->import->get('notes')); ?></textarea>
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
			<legend><span><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELDSET_HOOKS'); ?></span></legend>

			<?php if ($this->hooks->total()) { ?>
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
					<label for="field-name">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTPARSEHOOK'); ?>
					</label><br />
					<select name="hooks[postparse][]" multiple="multiple">
						<?php if (isset($hooks->postparse)) : ?>
							<?php foreach ($hooks->postparse as $hook) : ?>
								<?php $importHook = $this->hooks->fetch('id', $hook); ?>
								<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>

						<?php foreach ($this->hooks as $hook): ?>
							<?php if ($hook->get('event') != 'postparse' || in_array($hook->get('id'), $hooks->postparse)) { continue; } ?>
							<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
						<?php endforeach; ?>
					</select>
					<a class="hook-up" href="#">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
					</a> |
					<a class="hook-down" href="#">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
					</a><br />
					<span class="hint">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
					</span>
				</div>
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
					<label for="field-hookpostmap">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTMAPHOOK'); ?>
					</label>
					<select name="hooks[postmap][]" id="field-hookpostmap" multiple="multiple">
						<?php foreach ($hooks->postmap as $hook) : ?>
							<?php $importHook = $this->hooks->fetch('id', $hook); ?>
							<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
						<?php endforeach; ?>

						<?php foreach ($this->hooks as $hook): ?>
							<?php if ($hook->get('event') != 'postmap' || in_array($hook->get('id'), $hooks->postmap)) { continue; } ?>
							<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
						<?php endforeach; ?>
					</select>
					<a class="hook-up" href="#">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
					</a> |
					<a class="hook-down" href="#">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
					</a><br />
					<span class="hint">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
					</span>
				</div>
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
					<label for="field-hookpostconvert">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTCONVERTHOOK'); ?>
					</label>
					<select name="hooks[postconvert][]" id="field-hookpostconvert" multiple="multiple">
						<?php foreach ($hooks->postconvert as $hook) : ?>
							<?php $importHook = $this->hooks->fetch('id', $hook); ?>
							<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
						<?php endforeach; ?>
						<?php foreach ($this->hooks as $hook): ?>
							<?php if ($hook->get('event') != 'postconvert' || in_array($hook->get('id'), $hooks->postconvert)) { continue; } ?>
							<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
						<?php endforeach; ?>
					</select>
					<a class="hook-up" href="#">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
					</a> |
					<a class="hook-down" href="#">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
					</a><br />
					<span class="hint">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
					</span>
				</div>
			<?php } else { ?>
				<div class="input-wrap">
					<em><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_NO_HOOKS_FOUND'); ?></em>
					<input type="hidden" name="hooks[postparse][]" value="" />
					<input type="hidden" name="hooks[postmap][]" value="" />
					<input type="hidden" name="hooks[postconvert][]" value="" />
				</div>
			<?php } ?>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELDSET_PARAMS'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_APPROVED_HINT'); ?>">
				<label for="param-approved">
					<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_APPROVED'); ?>
				</label>
				<select name="params[approved]" id="param-approved">
					<option value="0"<?php echo ($this->params->get('approved', 1) == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JNO'); ?></option>
					<option value="1"<?php echo ($this->params->get('approved', 1) == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JYES'); ?></option>
				</select>
				<span class="hint"><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_APPROVED_HINT'); ?></span>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_EMAILNEW_HINT'); ?>">
				<label for="param-emailnew">
					<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_EMAILNEW'); ?>
				</label>
				<select name="params[emailnew]" id="param-emailnew">
					<option value="0"<?php echo ($this->params->get('emailnew', 1) == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JNO'); ?></option>
					<option value="1"<?php echo ($this->params->get('emailnew', 1) == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JYES'); ?></option>
				</select>
				<span class="hint"><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_EMAILNEW_HINT'); ?></span>
			</div>
		</fieldset>

		<?php
		$this->view('_fieldmap')
			->set('import', $this->import)
			->display();
		?>
	</div>
	<div class="col width-40 fltrt">
		<?php if ($this->import->get('id')) : ?>
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_ID'); ?></th>
						<td><?php echo $this->import->get('id'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDBY'); ?></th>
						<td>
							<?php
								if ($created_by = Hubzero\User\Profile::getInstance($this->import->get('created_by')))
								{
									echo $created_by->get('name');
								}
							?>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDON'); ?></th>
						<td>
							<?php
								echo JHTML::_('date', $this->import->get('created_at'), 'm/d/Y @ g:i a');
							?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELDSET_UPLOAD'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-file">
					<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATAFILEUPLOAD'); ?>
				</label>
				<input type="file" name="file" id="field-file" />
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELDSET_DATA'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo JText::sprintf('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE_HINT', $this->import->fileSpacePath()); ?>">
				<label for="field-importfile">
					<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE'); ?>
				</label><br />
				<select name="import[file]" id="field-importfile">
					<option value=""><?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE_OPTION_NULL'); ?></option>
					<?php if (isset($this->files)): ?>
						<?php foreach ($this->files as $file): ?>
							<?php $sel = ($this->import->get('file') == $file) ? 'selected="selected"' : ''; ?>
							<option <?php echo $sel; ?> value="<?php echo $file; ?>"><?php echo $file; ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				<span class="hint">
					<?php echo JText::sprintf('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE_HINT', $this->import->fileSpacePath()); ?>
				</span>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_HINT'); ?>">
				<label for="field-importmode">
					<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE'); ?>
				</label><br />
				<select name="import[mode]" id="field-importmode" disabled="disabled">
					<option value="UPDATE">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_UPDATE'); ?>
					</option>
					<option <?php if ($this->import->get('mode') == JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_PATCH')) { echo 'selected="selected"'; } ?> value="PATCH">
						<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_PATCH'); ?>
					</option>
				</select>
				<span class="hint">
					<?php echo JText::_('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_HINT'); ?>
				</span>
			</div>
		</fieldset>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="import[id]" value="<?php echo $this->import->get('id'); ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>