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

// No direct access.
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS_USER_PREFS') . ': ' . $text, 'user.png');
Toolbar::apply();
Toolbar::save();
Toolbar::spacer();
Toolbar::cancel();

$user = User::getInstance($this->row->user_id);

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

			var req = $.getJSON('index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&task=getClassValues&class_id=' + $(this).val(), {}, function (data) {
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
				<legend><span><?php echo Lang::txt('COM_TOOLS_USER_PREFS_LEGEND'); ?></span></legend>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />

				<?php if (!$this->row->id) : ?>
					<div class="input-wrap">
						<script type="text/javascript" src="<?php echo $base; ?>/plugins/hubzero/autocompleter/assets/js/autocompleter.js"></script>
						<script type="text/javascript">var plgAutocompleterCss = "<?php echo $base; ?>/plugins/hubzero/autocompleter/assets/css/autocompleter.css";</script>

						<label for="field-user_id"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_USER'); ?>:</label>
						<input type="text" name="fields[user_id]" id="field-user_id" data-options="members,multi," id="acmembers" class="autocomplete" value="" autocomplete="off" data-css="" data-script="<?php echo $base; ?>/administrator/index.php" />
						<span><?php echo Lang::txt('COM_TOOLS_USER_PREFS_USER_HINT'); ?></span>
					</div>
				<?php else : ?>
					<input type="hidden" name="fields[user_id]" id="field-user_id" value="<?php echo $this->row->user_id; ?>" />
				<?php endif; ?>
				<div class="input-wrap">
					<label for="class_id"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_CLASS'); ?>:</label>
					<?php echo $this->classes; ?>
				</div>
				<div class="input-wrap">
					<label for="field-jobs"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_JOBS'); ?>:</label>
					<input <?php echo ($this->row->class_id) ? 'readonly' : ''; ?> type="text" name="fields[jobs]" id="field-jobs" value="<?php echo $this->escape(stripslashes($this->row->jobs)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-params"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_PREFERENCES'); ?>:</label>
					<textarea name="fields[params]" id="field-params" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->row->params)); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_TOOLS_USER_PREFS_ID'); ?></th>
						<td><?php echo $this->row->user_id; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_TOOLS_USER_PREFS_USERNAME'); ?></th>
						<td><?php echo ($user ? $user->username : ''); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_TOOLS_USER_PREFS_NAME'); ?></th>
						<td><?php echo ($user ? $user->name : ''); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php echo Html::input('token'); ?>
</form>