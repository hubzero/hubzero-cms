<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('contributor');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_AUTHORS') . ': ' . $text, 'resources');
Toolbar::spacer();
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<table class="admintable">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_RESOURCE'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_NAME'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_ORGANIZATION'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_ROLE'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$i = 0;
					foreach ($this->rows as $row)
					{
						?>
						<tr>
							<td>
								<input type="text" name="fields[<?php echo $i; ?>][subid]" maxlength="250" size="4" value="<?php echo $this->escape(stripslashes($row->subid)); ?>" />
								<input type="hidden" name="fields[<?php echo $i; ?>][ordering]" value="<?php echo $this->escape(stripslashes($row->ordering)); ?>" />
								<input type="hidden" name="fields[<?php echo $i; ?>][subtable]" value="<?php echo $this->escape(stripslashes($row->subtable)); ?>" />
								<input type="hidden" name="fields[<?php echo $i; ?>][authorid]" value="<?php echo $this->escape($this->authorid); ?>" />
								<input type="hidden" name="fields[<?php echo $i; ?>][id]" value="<?php echo $this->escape(stripslashes($row->id)); ?>" />
							</td>
							<td>
								<input type="text" name="fields[<?php echo $i; ?>][name]" maxlength="250" value="<?php echo $this->escape(stripslashes($row->name)); ?>" />
							</td>
							<td>
								<input type="text" name="fields[<?php echo $i; ?>][organization]" maxlength="250" value="<?php echo $this->escape(stripslashes($row->organization)); ?>" />
							</td>
							<td>
								<select name="fields[<?php echo $i; ?>][role]">
									<option value=""<?php if ($row->role == '') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_RESOURCES_ROLE_AUTHOR'); ?></option>
									<?php
									if ($this->roles)
									{
										foreach ($this->roles as $role)
										{
											?>
											<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($row->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
											<?php
										}
									}
									?>
								</select>
							</td>
						</tr>
						<?php
						$i++;
					}
					?>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span4">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_FIELDSET_AUTHOR'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-authorid"><?php echo Lang::txt('COM_RESOURCES_FIELD_ID'); ?>:</label><br />
					<input type="text" name="authorid" id="field-authorid" value="<?php echo $this->escape($this->authorid); ?>" />
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->authorid; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
