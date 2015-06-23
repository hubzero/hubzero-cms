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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Wishlist\Helpers\Permissions::getActions('list');

$text = ($this->task == 'edit' ? Lang::txt('COM_WISHLIST_EDIT') : Lang::txt('COM_WISHLIST_NEW'));

Toolbar::title(Lang::txt('COM_WISHLIST') . ': ' . Lang::txt('COM_WISHLIST_WISH') . ': ' . $text, 'wishlist.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('comment');

Html::behavior('tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if ($('#field-content').val() == ''){
		alert('<?php echo Lang::txt('COM_WISHLIST_ERROR_MISSING_TEXT'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_WISHLIST_DETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-content"><?php echo Lang::txt('COM_WISHLIST_COMMENT'); ?>:</label><br />
				<?php echo $this->editor('fields[content]', $this->escape(preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', stripslashes($this->row->content))), 50, 30, 'field-content', array('class' => 'minimal no-footer')); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_WISHLIST_REFERENCEID'); ?>:</th>
					<td>
						<?php echo $this->row->item_id; ?>
						<input type="hidden" name="fields[item_id]" value="<?php echo $this->escape($this->row->item_id); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_WISHLIST_FIELD_CATEGORY'); ?>:</th>
					<td>
						<?php echo $this->row->item_type; ?>
						<input type="hidden" name="fields[item_type]" value="<?php echo $this->escape($this->row->item_type); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_WISHLIST_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->id; ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->id); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_WISHLIST_FIELD_CREATED'); ?>:</th>
					<td>
						<time datetime="<?php echo $this->escape($this->row->created); ?>"><?php echo $this->escape($this->row->created); ?></time>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->created); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_WISHLIST_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$editor = User::getInstance($this->row->created_by);
						echo ($editor) ? $this->escape(stripslashes($editor->get('name'))) : Lang::txt('unknown');
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->created_by); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_WISHLIST_PARAMETERS'); ?></span></legend>

			<div class="input-wrap">
				<input type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" <?php echo $this->row->anonymous ? 'checked="checked"' : ''; ?> />
				<label for="field-anonymous"><?php echo Lang::txt('COM_WISHLIST_ANONYMOUS'); ?></label>
			</div>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_WISHLIST_STATUS'); ?>:</label>
				<select name="fields[state]" id="field-state">
					<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php /*
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col width-100 fltlft">
				<fieldset class="panelform">
					<legend><span><?php echo Lang::txt('COM_WISHLIST_FIELDSET_RULES'); ?></span></legend>
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	*/ ?>

	<input type="hidden" name="wish" value="<?php echo $this->wish; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
