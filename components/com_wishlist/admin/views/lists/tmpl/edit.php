<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = \Components\Wishlist\Helpers\Permissions::getActions('list');

$text = ($this->task == 'edit' ? Lang::txt('COM_WISHLIST_EDIT') : Lang::txt('COM_WISHLIST_NEW'));

Toolbar::title(Lang::txt('COM_WISHLIST') . ': ' . Lang::txt('COM_WISHLIST_LIST') . ': ' . $text, 'wishlist.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('list');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton =='resethits') {
		if (confirm('<?php echo Lang::txt('COM_WISHLIST_RESET_HITS_WARNING'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert('<?php echo Lang::txt('COM_WISHLIST_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_WISHLIST_DETAILS'); ?></span></legend>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field->category"><?php echo Lang::txt('COM_WISHLIST_CATEGORY'); ?>:</label><br />
					<select name="fields[category]" id="field-category">
						<option value=""<?php echo ($this->row->category == '') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_SELECT_CATEGORY'); ?></option>
						<option value="general"<?php echo ($this->row->category == 'general') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_GENERAL'); ?></option>
						<option value="group"<?php echo ($this->row->category == 'group') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_GROUP'); ?></option>
						<option value="resource"<?php echo ($this->row->category == 'resource') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_CATEGORY_RESOURCE'); ?></option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-referenceid"><?php echo Lang::txt('COM_WISHLIST_REFERENCEID'); ?>:</label><br />
					<input type="text" name="fields[referenceid]" id="field-referenceid" size="11" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->referenceid)); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_WISHLIST_TITLE'); ?>:</label><br />
				<input type="text" name="fields[title]" id="field-title" size="30" maxlength="150" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo Lang::txt('COM_WISHLIST_DESCRIPTION'); ?>:</label><br />
				<input type="text" name="fields[description]" id="field-description" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_WISHLIST_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->id; ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->id; ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_WISHLIST_FIELD_CREATED'); ?>:</th>
					<td>
						<time datetime="<?php echo $this->row->created; ?>"><?php echo $this->row->created; ?></time>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->created ? $this->row->created : Date::toSql(); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_WISHLIST_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$creator = $this->row->created_by ? $this->row->created_by : User::get('id');
						$editor  = User::getInstance($creator);
						echo ($editor) ? $this->escape(stripslashes($editor->get('name'))) : Lang::txt('unknown');
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $creator; ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_WISHLIST_PARAMETERS'); ?></span></legend>

			<div class="input-wrap">
				<input type="checkbox" name="fields[state]" id="field-state" value="1" <?php echo $this->row->state ? 'checked="checked"' : ''; ?> />
				<label for="field-state"><?php echo Lang::txt('COM_WISHLIST_STATE'); ?></label>
			</div>

			<div class="input-wrap">
				<input type="checkbox" name="fields[public]" id="field-public" value="1" <?php echo $this->row->public ? 'checked="checked"' : ''; ?> />
				<label for="field-public"><?php echo Lang::txt('COM_WISHLIST_PUBLIC'); ?></label>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php /*
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<legend><span><?php echo Lang::txt('COM_WISHLIST_FIELDSET_RULES'); ?></span></legend>
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
	*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
