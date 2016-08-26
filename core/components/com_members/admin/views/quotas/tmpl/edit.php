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

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS_QUOTAS') . ': ' . $text, 'user');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

$base = str_replace('/administrator', '', rtrim(Request::base(true), '/'));
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

	jQuery(document).ready(function($){
		$('#class_id').on('change', function (e) {
			//e.preventDefault();
			$.getJSON('<?php echo Route::url('index.php?option=' . $this->option . '&controller=quotas&task=getClassValues&class_id=', false); ?>' + $(this).val(), {}, function (data) {
				$.each(data, function (key, val) {
					var item = $('#field-'+key);
					item.val(val);

					if (e.target.options[e.target.selectedIndex].text == 'custom') {
						item.prop("readonly", false);
					} else {
						item.prop("readonly", true);
					}
				});
			});
		});
	});
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_QUOTA_LEGEND'); ?></span></legend>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />

				<?php if (!$this->row->get('user_id')) : ?>
					<div class="input-wrap">
						<script type="text/javascript" src="<?php echo $base; ?>/plugins/hubzero/autocompleter/autocompleter.js"></script>
						<script type="text/javascript">var plgAutocompleterCss = "<?php echo $base; ?>/plugins/hubzero/autocompleter/autocompleter.css";</script>

						<label for="field-user_id"><?php echo Lang::txt('COM_MEMBERS_QUOTA_USER'); ?>:</label>
						<input type="text" name="fields[user_id]" id="field-user_id" data-options="members,multi," id="acmembers" class="autocomplete" value="" autocomplete="off" data-css="" data-script="<?php echo $base; ?>/administrator/index.php" />
						<span><?php echo Lang::txt('COM_MEMBERS_QUOTA_USER_HINT'); ?></span>
					</div>
				<?php else : ?>
					<input type="hidden" name="fields[user_id]" id="field-user_id" value="<?php echo $this->row->get('user_id'); ?>" />
				<?php endif; ?>
				<div class="input-wrap">
					<label for="class_id"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CLASS'); ?>:</label>
					<?php echo $this->classes; ?>
				</div>
				<div class="input-wrap">
					<label for="field-soft_blocks"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_BLOCKS'); ?>:</label>
					<input <?php echo ($this->row->get('class_id')) ? 'readonly' : ''; ?> type="text" name="fields[soft_blocks]" id="field-soft_blocks" value="<?php echo $this->escape(stripslashes($this->row->get('soft_blocks'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-hard_blocks"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_BLOCKS'); ?>:</label>
					<input <?php echo ($this->row->get('class_id')) ? 'readonly' : ''; ?> type="text" name="fields[hard_blocks]" id="field-hard_blocks" value="<?php echo $this->escape(stripslashes($this->row->get('hard_blocks'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-soft_files"><?php echo Lang::txt('COM_MEMBERS_QUOTA_SOFT_FILES'); ?>:</label>
					<input <?php echo ($this->row->get('class_id')) ? 'readonly' : ''; ?> type="text" name="fields[soft_files]" id="field-soft_files" value="<?php echo $this->escape(stripslashes($this->row->get('soft_files'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-hard_files"><?php echo Lang::txt('COM_MEMBERS_QUOTA_HARD_FILES'); ?>:</label>
					<input <?php echo ($this->row->get('class_id')) ? 'readonly' : ''; ?> type="text" name="fields[hard_files]" id="field-hard_files" value="<?php echo $this->escape(stripslashes($this->row->get('hard_files'))); ?>" />
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_ID'); ?></th>
						<td><?php echo $this->row->get('user_id'); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_USERNAME'); ?></th>
						<td><?php echo $this->row->get('username'); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_NAME'); ?></th>
						<td><?php echo $this->row->get('name'); ?></td>
					</tr>
				</tbody>
			</table>
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_SPACE'); ?></th>
						<td><?php echo Lang::txt('COM_MEMBERS_QUOTA_SPACE_DISPLAY', (isset($this->du['info']['space']) ? $this->du['info']['space'] / 1024 : 0), $this->du['percent']); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_FILES'); ?></th>
						<td><?php echo (isset($this->du['info']['files']) ? $this->du['info']['files'] : 0); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php echo Html::input('token'); ?>
</form>