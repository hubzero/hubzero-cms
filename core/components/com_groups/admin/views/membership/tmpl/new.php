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

$tmpl = Request::getCmd('tmpl', '');

$text = ($this->task == 'edit' ? Lang::txt('COM_GROUPS_EDIT') : Lang::txt('COM_GROUPS_NEW'));

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_GROUPS').': ' . $text, 'groups');
	if ($canDo->get('core.edit'))
	{
		Toolbar::save();
	}
	Toolbar::cancel();
}

Html::behavior('framework');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if (form.usernames.value == '') {
		alert('<?php echo Lang::txt('COM_GROUPS_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
	window.top.setTimeout("window.parent.location='index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&gid=<?php echo $this->group->get('cn'); ?>'", 700);
}

jQuery(document).ready(function($){
	$(window).on('keypress', function(){
		if (window.event.keyCode == 13) {
			submitbutton('addusers');
		}
	})
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration" >
			<div class="fltrt configuration-options">
				<button type="button" onclick="submitbutton('addusers');"><?php echo Lang::txt( 'COM_GROUPS_MEMBER_SAVE' );?></button>
				<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt( 'COM_GROUPS_MEMBER_CANCEL' );?></button>
			</div>
			<?php echo Lang::txt('COM_GROUPS_MEMBER_ADD') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_GROUPS_DETAILS'); ?></span></legend>

			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="no_html" value="<?php echo ($tmpl == 'component') ? '1' : '0'; ?>">
			<input type="hidden" name="task" value="addusers" />

			<table class="admintable">
				<tbody>
					<tr>
						<th><label for="field-usernames"><?php echo Lang::txt('COM_GROUPS_ADD_USERNAME'); ?>:</label></th>
						<td><input type="text" name="usernames" class="input-username" id="field-usernames" value="" size="50" /></td>
					</tr>
					<tr>
						<th><label for="field-tbl"><?php echo Lang::txt('COM_GROUPS_TO'); ?>:</label></th>
						<td>
							<select name="tbl" id="field-tbl">
								<option value="invitees"><?php echo Lang::txt('COM_GROUPS_INVITEES'); ?></option>
								<option value="applicants"><?php echo Lang::txt('COM_GROUPS_APPLICANTS'); ?></option>
								<option value="members" selected="selected"><?php echo Lang::txt('COM_GROUPS_MEMBERS'); ?></option>
								<option value="managers"><?php echo Lang::txt('COM_GROUPS_MANAGERS'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<?php echo Html::input('token'); ?>
</form>
