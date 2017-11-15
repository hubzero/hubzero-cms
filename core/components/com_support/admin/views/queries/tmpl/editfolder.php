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

$tmpl = Request::getWord('tmpl', '');
$no_html = Request::getInt('no_html', 0);

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

if (!$no_html && !$tmpl)
{
Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_QUERY_FOLDER') . ': ' . $text, 'support');
Toolbar::apply();
Toolbar::save();
Toolbar::spacer();
Toolbar::cancel();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	if ($('#field-title').val() == '') {
		alert('<?php echo Lang::txt('COM_SUPPORT_CATEGORY_ERROR_NO_TEXT'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php } ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="<?php echo ($tmpl == 'component' ? 'component' : 'item'); ?>-form">
	<?php if ($tmpl == 'component') { ?>
		<script type="text/javascript">
			Joomla.submitbutton = function(task)
			{
				if (document.formvalidator.isValid($('#component-form'))) {
					Joomla.submitform(task, document.getElementById('component-form'));
				}
			}
		</script>
		<fieldset>
			<div class="configuration">
				<div class="configuration-options">
					<button type="button" onclick="Joomla.submitform('applyfolder', this.form);"><?php echo Lang::txt('JAPPLY');?></button>
					<button type="button" onclick="Joomla.submitform('savefolder', this.form);"><?php echo Lang::txt('JSAVE');?></button>
					<button type="button" onclick="<?php echo Request::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';?>  window.parent.$.fancybox.close();"><?php echo Lang::txt('JCANCEL');?></button>
				</div>

				<?php echo Lang::txt('COM_SUPPORT_QUERY_FOLDER') . ': ' . $text; ?>
			</div>
		</fieldset>
	<?php } ?>

	<?php if (!$tmpl) { ?>
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_SUPPORT_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape($this->row->title); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape($this->row->alias); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_SUPPORT_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->id; ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->id); ?>" />
						</td>
					</tr>
				<?php if ($this->row->created_by) { ?>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_SUPPORT_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo Date::of($this->row->created)->toLocal('Y-m-d H:i:s'); ?>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_SUPPORT_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php 
							$user = User::getInstance($this->row->created_by);
							echo $this->escape($user->get('name'));
							?>
						</td>
					</tr>
					<?php if ($this->row->modified_by && $this->row->modified_by != '0000-00-00 00:00:00') { ?>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SUPPORT_FIELD_MODIFIED'); ?>:</th>
							<td>
								<?php echo Date::of($this->row->modified)->toLocal('Y-m-d H:i:s'); ?>
							</td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SUPPORT_FIELD_MODIFIER'); ?>:</th>
							<td>
								<?php 
								$user = User::getInstance($this->row->modified_by);
								echo $this->escape($user->get('name'));
								?>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php } else { ?>
		<fieldset class="adminform">
			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_SUPPORT_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape($this->row->title); ?>" />
			</div>

			<input type="hidden" name="fields[alias]" id="field-alias" value="<?php echo $this->escape($this->row->alias); ?>" />
		</fieldset>
	<?php } ?>

	<input type="hidden" name="no_html" value="<?php echo $no_html; ?>" />
	<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="savefolder" />

	<?php echo Html::input('token'); ?>
</form>