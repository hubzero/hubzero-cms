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

$canDo = Components\Members\Helpers\Admin::getActions('component');

$text = ($this->task == 'editClass' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS_QUOTA_CLASSES') . ': ' . $text, 'user');
if ($canDo->get('core.edit'))
{
	Toolbar::apply('applyClass');
	Toolbar::save('saveClass');
	Toolbar::spacer();
}
Toolbar::cancel('cancelClass');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS_LEGEND'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_MEMBERS_QUOTA_ALIAS'); ?>:</label>
					<input <?php echo ($this->row->get('alias') == 'default') ? 'readonly' : ''; ?> type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-soft_blocks"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_BLOCKS'); ?>:</label>
					<input type="text" name="fields[soft_blocks]" id="field-soft_blocks" value="<?php echo $this->escape(stripslashes($this->row->get('soft_blocks'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-hard_blocks"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_BLOCKS'); ?>:</label>
					<input type="text" name="fields[hard_blocks]" id="field-hard_blocks" value="<?php echo $this->escape(stripslashes($this->row->get('hard_blocks'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-soft_files"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_FILES'); ?>:</label>
					<input type="text" name="fields[soft_files]" id="field-soft_files" value="<?php echo $this->escape(stripslashes($this->row->get('soft_files'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-hard_files"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_FILES'); ?>:</label>
					<input type="text" name="fields[hard_files]" id="field-hard_files" value="<?php echo $this->escape(stripslashes($this->row->get('hard_files'))); ?>" />
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS_USERGROUPS_LEGEND'); ?></span></legend>
				<p><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS_USERGROUPS_DESC'); ?></p>
				<?php
				// Include the component HTML helpers.
				Html::addIncludePath(PATH_CORE . '/components/com_users/admin/helpers/html');

				$groups = array();
				foreach ($this->row->groups() as $g)
				{
					$groups[] = $g->get('group_id');
				}
				?>
				<div class="input-wrap">
					<?php echo Html::access('usergroups', 'fields[groups]', $groups, true); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_ID'); ?></th>
						<td><?php echo $this->row->get('id'); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS_USER_COUNT'); ?></th>
						<td><?php echo ($this->row->get('id')) ? $this->user_count : 0; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="saveClass" />

	<?php echo Html::input('token'); ?>
</form>