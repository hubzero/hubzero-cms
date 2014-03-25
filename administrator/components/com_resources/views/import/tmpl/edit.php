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

$this->css('import');
$this->js('import');

// set title
$title  = ($this->import->get('id')) ? JText::_('COM_RESOURCES_IMPORT_TITLE_EDIT') : JText::_('COM_RESOURCES_IMPORT_TITLE_ADD');

JToolBarHelper::title(JText::_($title), 'import.png');
JToolBarHelper::save();
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

<form action="index.php?option=com_resources&amp;controller=import&amp;task=save" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="col width-70 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELDSET_DETAILS'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_NAME'); ?>
							</label>
						</td>
						<td>
							<input type="text" name="import[name]" id="field-name" value="<?php echo $this->escape($this->import->get('name')); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="field-notes">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_NOTES'); ?>
							</label>
						</td>
						<td>
							<textarea name="import[notes]" id="field-notes" rows="5"><?php echo $this->escape($this->import->get('notes')); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELDSET_DATA'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE'); ?>
							</label>
						</td>
						<td>
							<select name="import[file]">
								<option value=""><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE_OPTION_NULL'); ?></option>
								<?php if (isset($this->files)): ?>
									<?php foreach ($this->files as $file): ?>
										<?php $sel = ($this->import->get('file') == $file) ? 'selected="selected"' : ''; ?>
										<option <?php echo $sel; ?> value="<?php echo $file; ?>"><?php echo $file; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<span class="hint">
								<?php echo JText::sprintf('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_FILE_HINT', $this->import->fileSpacePath()); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE'); ?>
							</label>
						</td>
						<td>
							<select name="import[mode]" disabled="disabled">
								<option value="UPDATE">
									<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_UPDATE'); ?>
								</option>
								<option <?php if ($this->import->get('mode') == JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_PATCH')) { echo 'selected="selected"'; } ?> value="PATCH">
									<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_PATCH'); ?>
								</option>
							</select>
							<span class="hint">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_DATA_MODE_HINT'); ?>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
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
			<legend><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELDSET_HOOKS'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_POSTPARSEHOOK'); ?>
							</label>
						</td>
						<td>	
							<select name="hooks[postparse][]" multiple>
								<?php if (isset($hooks->postparse)) : ?>
									<?php foreach ($hooks->postparse as $hook) : ?>
										<?php $importHook = $this->hooks->fetch('id', $hook); ?>
										<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
								
								<?php foreach ($this->hooks as $hook): ?>
									<?php if ($hook->get('type') != 'postparse' || in_array($hook->get('id'), $hooks->postparse)) { continue; } ?>
									<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
								<?php endforeach; ?>
							</select>
							<a class="hook-up" href="#">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
							</a> | 
							<a class="hook-down" href="#">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
							</a><br />
							<span class="hint">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_POSTMAPHOOK'); ?>
							</label>
						</td>
						<td>	
							<select name="hooks[postmap][]" multiple>
								<?php foreach ($hooks->postmap as $hook) : ?>
									<?php $importHook = $this->hooks->fetch('id', $hook); ?>
									<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
								<?php endforeach; ?>
								
								<?php foreach ($this->hooks as $hook): ?>
									<?php if ($hook->get('type') != 'postmap' || in_array($hook->get('id'), $hooks->postmap)) { continue; } ?>
									<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
								<?php endforeach; ?>
							</select>
							<a class="hook-up" href="#">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
							</a> | 
							<a class="hook-down" href="#">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
							</a><br />
							<span class="hint">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_POSTCONVERTHOOK'); ?>
							</label>
						</td>
						<td>
							<select name="hooks[postconvert][]" multiple>

								<?php foreach ($hooks->postconvert as $hook) : ?>
									<?php $importHook = $this->hooks->fetch('id', $hook); ?>
									<option selected="selected" value="<?php echo $importHook->get('id'); ?>"><?php echo $importHook->get('name'); ?></option>
								<?php endforeach; ?>
								
								<?php foreach ($this->hooks as $hook): ?>
									<?php if ($hook->get('type') != 'postconvert' || in_array($hook->get('id'), $hooks->postconvert)) { continue; } ?>
									<option value="<?php echo $hook->get('id'); ?>"><?php echo $hook->get('name'); ?></option>
								<?php endforeach; ?>
							</select>
							<a class="hook-up" href="#">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
							</a> | 
							<a class="hook-down" href="#">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
							</a><br />
							<span class="hint">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELDSET_PARAMS'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_STATUS'); ?>
							</label>
						</td>
						<td>
							<select name="params[status]">
								<option value="2"<?php echo ($this->params->get('status', 1) == 2) ? ' selected="selected"' : ''; ?>>Draft (user created)</option>
								<option value="5"<?php echo ($this->params->get('status', 1) == 5) ? ' selected="selected"' : ''; ?>>Draft (internal)</option>
								<option value="3"<?php echo ($this->params->get('status', 1) == 3) ? ' selected="selected"' : ''; ?>>Pending</option>
								<option value="0"<?php echo ($this->params->get('status', 1) == 0) ? ' selected="selected"' : ''; ?>>Unpublished</option>
								<option value="1"<?php echo ($this->params->get('status', 1) == 1) ? ' selected="selected"' : ''; ?>>Published</option>
								<option value="4"<?php echo ($this->params->get('status', 1) == 4) ? ' selected="selected"' : ''; ?>>Delete</option>
							</select>
							<span class="hint"><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_STATUS_HINT'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_ACCESS'); ?>
							</label>
						</td>
						<td>
							<?php 
								$rconfig = JComponentHelper::getParams('com_resources');
								echo ResourcesHtml::selectAccess($rconfig->get('accesses'), $this->params->get('access', 0), 'params[access]'); 
							?>
							<span class="hint"><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_ACCESS_HINT'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_GROUP'); ?>
							</label>
						</td>
						<td>
							<?php echo ResourcesHtml::selectGroup($this->groups, $this->params->get('group', ''), 'params[group]' ,'import-group'); ?>
							<span class="hint"><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_GROUP_HINT'); ?></span>
						</td>
					</tr>

					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_MATCHTITLE'); ?>
							</label>
						</td>
						<td>
							<select name="params[titlematch]">
								<option value="0">No</option>
								<option value="1" <?php if ($this->params->get('titlematch', 0) == 1) { echo 'selected="selected"'; } ?>>Yes</option>
							</select>
							<span class="hint"><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_MATCHTITLE_HINT'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED'); ?>
							</label>
						</td>
						<td>
							<select name="params[requiredfields]">
								<option value="0">
									<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_NO'); ?>
								</option>
								<option value="1" <?php if ($this->params->get('requiredfields', 1) == 1) { echo 'selected="selected"'; } ?>>
									<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_YES'); ?>
								</option>
							</select>
							<span class="hint">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_CHECKREQUIRED_HINT'); ?>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-30 fltrt">
		<?php if ($this->import->get('id')) : ?>
			<table class="meta" summary="Metadata">
				<tbody>
					<tr>
						<th><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_ID'); ?></th>
						<td><?php echo $this->import->get('id'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_CREATEDBY'); ?></th>
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
						<th><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_CREATEDON'); ?></th>
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
			<legend><?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELDSET_UPLOAD'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key" width="50px">
							<label for="field-name">
								<?php echo JText::_('COM_RESOURCES_IMPORT_EDIT_FIELD_DATAFILEUPLOAD'); ?>
							</label>
						</td>
						<td>
							<input type="file" name="file" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

	</div>
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="import[id]" value="<?php echo $this->import->get('id'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>