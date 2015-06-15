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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = \Components\Tags\Helpers\Permissions::getActions();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_TAGGED') . ': ' . $text, 'tags.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('edittagged');

Html::behavior('framework');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if ($('#field-tagid').val() == '') {
		alert('<?php echo Lang::txt('COM_TAGS_ERROR_EMPTY_TAG'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<?php
if ($this->getError())
{
	echo '<p class="error">' . implode('<br />', $this->getError()) . '</p>';
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_TAGID_HINT'); ?>">
				<label for="field-tagid"><?php echo Lang::txt('COM_TAGS_FIELD_TAGID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[tagid]" id="field-tagid" maxlength="11" value="<?php echo $this->escape($this->row->get('tagid')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_TAGID_HINT'); ?></span>
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_OBJECTID_HINT'); ?>">
					<label for="field-objectid"><?php echo Lang::txt('COM_TAGS_FIELD_OBJECTID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[objectid]" id="field-objectid" maxlength="11" value="<?php echo $this->escape($this->row->get('objectid')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_OBJECTID_HINT'); ?></span>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_TAGS_FIELD_TBL_HINT'); ?>">
					<label for="field-tbl"><?php echo Lang::txt('COM_TAGS_FIELD_TBL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[tbl]" id="field-tbl" maxlength="250" value="<?php echo $this->escape($this->row->get('tbl')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_TAGS_FIELD_TBL_HINT'); ?></span>
				</div>
			</div>
			<div class="clr"></div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_TAGS_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->get('id'); ?>
						<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_TAGS_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						echo $this->escape($this->row->creator('name', Lang::txt('COM_TAGS_UNKNOWN')));
						?>
						<input type="hidden" name="fields[taggerid]" id="field-taggerid" value="<?php echo $this->escape($this->row->get('taggerid')); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_TAGS_FIELD_CREATED'); ?>:</th>
					<td>
						<?php echo ($this->row->created() != '0000-00-00 00:00:00' ? $this->row->created() : Lang::txt('COM_TAGS_UNKNOWN')); ?>
						<input type="hidden" name="fields[taggedon]" id="field-taggedon" value="<?php echo $this->escape($this->row->get('taggedon')); ?>" />
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