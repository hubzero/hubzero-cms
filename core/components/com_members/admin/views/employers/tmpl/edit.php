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

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS_REGISTRATION') . ': ' . Lang::txt('COM_MEMBERS_ORGTYPE') . ': ' . $text, 'user.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_FIELD_ALIAS_HINT'); ?>">
				<label for="field-type"><?php echo Lang::txt('COM_MEMBERS_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="type" id="field-type" value="<?php echo $this->escape($this->model->type); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_MEMBERS_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_MEMBERS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="title" id="field-title" value="<?php echo $this->escape($this->model->title); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_MEMBERS_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->model->id; ?>
						<input type="hidden" name="id" value="<?php echo $this->model->id; ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
