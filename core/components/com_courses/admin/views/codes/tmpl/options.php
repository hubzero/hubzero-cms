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

$tmpl = Request::getVar('tmpl', '');

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_COUPON_CODE') . ': ' . Lang::txt('COM_COURSES_GENERATE'), 'course.png');
	if ($canDo->get('core.edit'))
	{
		Toolbar::save();
	}
	Toolbar::cancel();
}

Html::behavior('framework', true);

$offset = Config::get('config.offset');

$year  = strftime("%Y", time()+($offset*60*60));
$month = strftime("%m", time()+($offset*60*60));
$day   = strftime("%d", time()+($offset*60*60));

//$nextMonth = date("m", mktime(0, 0, 0, $month, $day + 7, $year));
$nextYear  = date("Y", mktime(0, 0, 0, $month+1, $day, $year));
$nextMonth = date("m", mktime(0, 0, 0, $month+1, $day, $year));
$nextDay   = date("d", mktime(0, 0, 0, $month+1, $day, $year));
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
	if (form.num.value == '') {
		alert('<?php echo Lang::txt('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
	window.top.setTimeout("window.parent.location='<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&section=' . $this->section->get('id'), false); ?>'", 700);
}

jQuery(document).ready(function($){
	$(window).on('keypress', function(){
		if (window.event.keyCode == 13) {
			submitbutton('generate');
		}
	})
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" onclick="submitbutton('generate');"><?php echo Lang::txt('COM_COURSES_GENERATE');?></button>
				<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt('COM_COURSES_CANCEL');?></button>
			</div>

			<?php echo Lang::txt('COM_COURSES_GENERATE_CODES') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<input type="hidden" name="section" value="<?php echo $this->section->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="no_html" value="<?php echo ($tmpl == 'component') ? '1' : '0'; ?>">
			<input type="hidden" name="task" value="generate" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-num"><?php echo Lang::txt('COM_COURSES_FIELD_NUMBER_OF_CODES'); ?>:</label></td>
						<td colspan="3"><input type="text" name="num" id="field-num" value="" size="5" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-expires-year"><?php echo Lang::txt('COM_COURSES_FIELD_EXPIRES'); ?>:</label></td>
						<td>YYYY<input type="text" name="expires[year]" id="field-expires-year" value="<?php echo $nextYear; ?>" size="4" /></td>
						<td>MM<input type="text" name="expires[month]" id="field-expires-month" value="<?php echo $nextMonth; ?>" size="2" /></td>
						<td>DD<input type="text" name="expires[day]" id="field-expires-day" value="<?php echo $nextDay; ?>" size="2" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<?php echo Html::input('token'); ?>
</form>
