<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getString('tmpl', '');

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

$this->js();

$offset = Config::get('config.offset');

$year  = strftime("%Y", time()+($offset*60*60));
$month = strftime("%m", time()+($offset*60*60));
$day   = strftime("%d", time()+($offset*60*60));

//$nextMonth = date("m", mktime(0, 0, 0, $month, $day + 7, $year));
$nextYear  = date("Y", mktime(0, 0, 0, $month+1, $day, $year));
$nextMonth = date("m", mktime(0, 0, 0, $month+1, $day, $year));
$nextDay   = date("d", mktime(0, 0, 0, $month+1, $day, $year));
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="<?php echo ($tmpl == 'component') ? 'component-form' : 'item-form'; ?>">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" id="btn-generate" data-redirect="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&section=' . $this->section->get('id'), false); ?>"><?php echo Lang::txt('COM_COURSES_GENERATE');?></button>
				<button type="button" id="btn-cancel"><?php echo Lang::txt('JCANCEL');?></button>
			</div>

			<?php echo Lang::txt('COM_COURSES_GENERATE_CODES') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col span12">
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
						<td colspan="3"><input type="text" name="num" id="field-num" value="5" size="5" /></td>
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
