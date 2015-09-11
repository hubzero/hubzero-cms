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

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES').': ' . Lang::txt('COM_COURSES_COUPON_CODES') . ': ' . $text, 'courses.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

Html::behavior('calendar');
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
	if ($('#field-code').val() == '') {
		alert('<?php echo Lang::txt('COM_COURSES_ERROR_MISSING_CODE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
			<input type="hidden" name="section" value="<?php echo $this->row->get('section_id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />

			<div class="input-wrap">
				<label for="field-section_id"><?php echo Lang::txt('COM_COURSES_FIELD_SECTION'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<select name="fields[section_id]" id="field-section_id">
					<option value="-1"><?php echo Lang::txt('COM_COURSES_SELECT'); ?></option>
					<?php
					require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
					$model = \Components\Courses\Models\Courses::getInstance();
					if ($model->courses()->total() > 0)
					{
						foreach ($model->courses() as $course)
						{
						?>
							<optgroup label="<?php echo $this->escape(stripslashes($course->get('title'))); ?>">
							<?php
							$j = 0;
							foreach ($course->offerings() as $i => $offering)
							{
								?>
								<optgroup label="&nbsp; &nbsp; <?php echo $this->escape(stripslashes($offering->get('title'))); ?>">
								<?php
								foreach ($offering->sections() as $section)
								{
								?>
									<option value="<?php echo $this->escape(stripslashes($section->get('id'))); ?>"<?php if ($section->get('id') == $this->row->get('section_id')) { echo ' selected="selected"'; } ?>>&nbsp; &nbsp; <?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
								<?php
								}
								?>
								</optgroup>
								<?php
							}
							?>
							</optgroup>
						<?php
						}
					}
					?>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-code"><?php echo Lang::txt('COM_COURSES_FIELD_CODE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[code]" id="field-code" value="<?php echo $this->escape(stripslashes($this->row->get('code'))); ?>" />
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_AVAILABILITY'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_STARTS_HINT'); ?>">
				<label for="field-created"><?php echo Lang::txt('COM_COURSES_FIELD_STARTS'); ?>:</label><br />
				<?php echo Html::input('calendar', 'fields[created]', $this->row->get('created'), array('id' => 'field-created')); ?>
				<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_STARTS_HINT'); ?></span>
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_EXPIRES_HINT'); ?>">
				<label for="field-expires"><?php echo Lang::txt('COM_COURSES_FIELD_EXPIRES'); ?>:</label><br />
				<?php echo Html::input('calendar', 'fields[expires]', $this->row->get('expires'), array('id' => 'field-expires')); ?>
				<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_EXPIRES_HINT'); ?></span>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_REDEEMED'); ?></span></legend>

			<table class="admintable">
				<tbody>
				<?php if ($this->row->get('redeemed_by')) { ?>
					<tr>
						<th><label for="field-redeemed"><?php echo Lang::txt('COM_COURSES_FIELD_REDEEMED'); ?>:</label></th>
						<td>
							<?php echo $this->escape(stripslashes($this->row->get('redeemed'))); ?>
							<input type="hidden" name="fields[redeemed]" id="field-redeemed" class="datetime-field" value="<?php echo $this->escape(stripslashes($this->row->get('redeemed'))); ?>" />
						</td>
					</tr>
					<tr>
						<th><label for="field-redeemed_by"><?php echo Lang::txt('COM_COURSES_FIELD_REDEEMED_BY'); ?>:</label></th>
						<td>
							<?php echo $this->escape(stripslashes($this->row->redeemer()->get('name'))) . ' (' . $this->escape(stripslashes($this->row->redeemer()->get('username'))) . ')'; ?>
							<input type="hidden" name="fields[redeemed_by]" id="field-redeemed_by" value="<?php echo $this->escape(stripslashes($this->row->get('redeemed_by'))); ?>" />
						</td>
					</tr>
				<?php } else { ?>
					<tr>
						<td>
							<?php echo Lang::txt('COM_COURSES_CODE_NOT_REDEEMED') ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_COURSES_FIELD_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
		<?php if ($this->row->get('created')) { ?>
				<tr>
					<th><?php echo Lang::txt('COM_COURSES_FIELD_CREATED'); ?></th>
					<td>
						<?php echo $this->escape($this->row->get('created')); ?>
					</td>
				</tr>
			<?php if ($this->row->get('created_by')) { ?>
				<tr>
					<th><?php echo Lang::txt('COM_COURSES_FIELD_CREATOR'); ?></th>
					<td><?php
					$creator = User::getInstance($this->row->get('created_by'));
					echo $this->escape(stripslashes($creator->get('name'))); ?></td>
				</tr>
			<?php } ?>
		<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_COURSES_QR_CODE'); ?></span></legend>
			<?php if ($this->row->get('id')) { ?>
				<img src="<?php echo Route::url('index.php?option=com_courses&controller=codes&task=qrcode&no_html=1&code=' . $this->row->get('code')); ?>" alt="QR Code" />
			<?php } else { ?>
				<p class="warning"><?php echo Lang::txt('COM_COURSES_QR_CODE_ENTY_MOST_BE_SAVED'); ?></p>
			<?php } ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo Html::input('token'); ?>
</form>
