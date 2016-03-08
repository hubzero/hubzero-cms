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

$canDo = \Components\Cron\Helpers\Permissions::getActions('component');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_CRON') . ': ' . $text, 'cron.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('job');

Html::behavior('calendar');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert( '<?php echo Lang::txt('CON_CRON_ERROR_MISSING_TITLE');?>' );
	} else {
		submitform( pressbutton );
	}
}

var Fields = {
	initialise: function() {
		$('#field-event').on('change', function(){
			var ev = $(this).val().replace('::', '--');

			$('fieldset.eventparams').each(function(i, el) {
				$(el).css('display', 'none');
			});

			if ($('#params-' + ev)) {
				$('#params-' + ev).css('display', 'block');
			}
		});

		$('#field-recurrence').on('change', function(){
			var min = '*',
				hour = '*',
				day = '*',
				month = '*',
				dow = '*',
				recurrence = $(this).val();

			switch (recurrence)
			{
				case '0 0 1 1 *':
					min = '0';
					hour = '0';
					day = '1';
					month = '1';
				break;
				case '0 0 1 * *':
					min = '0';
					hour = '0';
					day = '1';
				break;
				case '0 0 * * 0':
					min = '0';
					hour = '0';
					dow = '0';
				break;
				case '0 0 * * *':
					min = '0';
					hour = '0';
				break;
				case '0 * * * *':
					min = '0';
				break;
			}

			if (recurrence == 'custom') {
				if ($('#custom').hasClass('hide')) {
					$('#custom').removeClass('hide');
				}
			} else {
				if (!$('#custom').hasClass('hide')) {
					$('#custom').addClass('hide');
				}
			}

			$('#field-minute-c').val(min);
			$('#field-minute-s').val(min);
			$('#field-hour-c').val(hour);
			$('#field-hour-s').val(hour);
			$('#field-day-c').val(day);
			$('#field-day-s').val(day);
			$('#field-month-c').val(month);
			$('#field-month-s').val(month);
			$('#field-dayofweek-c').val(dow);
			$('#field-dayofweek-s').val(dow);
		});

		$('#field-minute-s').on('change', function(){
			$('#field-minute-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-minute-c').on('change', function(){
			$('#field-minute-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});

		$('#field-hour-s').on('change', function(){
			$('#field-hour-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-hour-c').on('change', function(){
			$('#field-hour-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});

		$('#field-day-s').on('change', function(){
			$('#field-day-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-day-c').on('change', function(){
			$('#field-day-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});

		$('#field-month-s').on('change', function(){
			$('#field-month-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-month-c').on('change', function(){
			$('#field-month-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});

		$('#field-dayofweek-s').on('change', function(){
			$('#field-dayofweek-c').val($(this).val());
			$('#field-recurrence').val('custom');
		});
		$('#field-dayofweek-c').on('change', function(){
			$('#field-dayofweek-s').val($(this).val());
			$('#field-recurrence').val('custom');
		});
	}
}

jQuery(document).ready(function($){
	Fields.initialise();
});
</script>

<?php
	foreach ($this->getErrors() as $error)
	{
		echo '<p class="error">' . $error . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_CRON_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-event"><?php echo Lang::txt('COM_CRON_FIELD_EVENT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<select name="fields[event]" id="field-event">
						<option value=""<?php echo (!$this->row->get('plugin')) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CRON_SELECT'); ?></option>
						<?php
						if ($this->plugins)
						{
							foreach ($this->plugins as $plugin)
							{
								?>
								<optgroup label="<?php echo $this->escape(Lang::txt('plg_cron_' . $plugin->plugin)); ?>">
									<?php
									if ($plugin->events)
									{
										foreach ($plugin->events as $event)
										{
											?>
											<option value="<?php echo $plugin->plugin; ?>::<?php echo $event['name']; ?>"<?php if ($this->row->get('event') == $event['name']) { echo ' selected="selected"'; } ?>><?php echo $this->escape($event['label']); ?></option>
											<?php
										}
									}
									?>
								</optgroup>
								<?php
							}
						}
						?>
					</select>
				</div>
			</fieldset>

			<?php
			if ($this->plugins)
			{
				foreach ($this->plugins as $plugin)
				{
					if (!isset($plugin->events) || !$plugin->events)
					{
						continue;
					}

					foreach ($plugin->events as $event)
					{
						$data = '';
						$style = 'none';
						if ($event['name'] == $this->row->get('event'))
						{
							$style = 'block';
							$data = $this->row->get('params');
						}

						$out = null;
						if ($event['params'])
						{
							$param = new \Hubzero\Html\Parameter(
								(is_object($data) ? $data->toString() : $data),
								PATH_CORE . DS . 'plugins' . DS . 'cron' . DS . $plugin->plugin . DS . $plugin->plugin . '.xml'
							);
							$param->addElementPath(PATH_CORE . DS . 'plugins' . DS . 'cron' . DS . $plugin->plugin);
							//$out = $param->render('params', $event['params']);
							$html = array();
							if ($prm = $param->getParams('params', $event['params']))
							{
								foreach ($prm as $p)
								{
									$html[] = '<div class="input-wrap">';
									if ($p[0])
									{
										$html[] = $p[0];
										$html[] = $p[1];
									}
									else
									{
										$html[] = $p[1];
									}
									$html[] = '</div>';
								}
							}

							$out = (!empty($html) ? implode("\n", $html) : $out);
						}

						if (!$out)
						{
							$out = '<div class="input-wrap"><p><i>' . Lang::txt('COM_CRON_NO_PARAMETERS_FOUND') . '</i></p></div>';
						}
						?>
						<fieldset class="adminform paramlist eventparams" style="display: <?php echo $style; ?>;" id="params-<?php echo $plugin->plugin . '--' . $event['name']; ?>">
							<legend><span><?php echo Lang::txt('COM_CRON_FIELDSET_PARAMETERS'); ?></span></legend>
							<?php echo $out; ?>
						</fieldset>
						<?php
					}
				}
			}
			?>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_CRON_FIELDSET_RECURRENCE'); ?></span></legend>

				<div class="input-wrap">
					<?php echo Lang::txt('COM_CRON_FIELD_COMMON'); ?>:<br />
					<select name="fields[recurrence]" id="field-recurrence">
						<option value=""<?php echo ($this->row->get('recurrence') == '') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_SELECT'); ?></option>
						<option value="custom"<?php echo ($this->row->get('recurrence') == 'custom') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_CUSTOM'); ?></option>
						<option value="0 0 1 1 *"<?php echo ($this->row->get('recurrence') == '0 0 1 1 *') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_YEAR'); ?></option>
						<option value="0 0 1 * *"<?php echo ($this->row->get('recurrence') == '0 0 1 * *') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_MONTH'); ?></option>
						<option value="0 0 * * 0"<?php echo ($this->row->get('recurrence') == '0 0 * * 0') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_WEEK'); ?></option>
						<option value="0 0 * * *"<?php echo ($this->row->get('recurrence') == '0 0 * * *') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_A_DAY'); ?></option>
						<option value="0 * * * *"<?php echo ($this->row->get('recurrence') == '0 * * * *') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CRON_FIELD_COMMON_OPT_ONCE_AN_HOUR'); ?></option>
					</select>
				</div>

				<table class="admintable">
					<tbody id="custom"<?php echo ($this->row->get('recurrence') == 'custom') ? '' : ' class="hide"'; ?>>
						<tr>
							<th>
								<label for="field-minute-c"><?php echo Lang::txt('COM_CRON_FIELD_MINUTE'); ?></label>:
							</th>
							<td>
								<input type="text" name="fields[minute][c]" id="field-minute-c" value="<?php echo $this->row->get('minute'); ?>" />
							</td>
							<td>
								<select name="fields[minute][s]" id="field-minute-s">
									<option value=""<?php if ($this->row->get('minute') == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
									<option value="*"<?php if ($this->row->get('minute') == '*') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?></option>
									<option value="*/5"<?php if ($this->row->get('minute') == '*/5') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_FIVE'); ?></option>
									<option value="*/10"<?php if ($this->row->get('minute') == '*/10') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_TEN'); ?></option>
									<option value="*/15"<?php if ($this->row->get('minute') == '*/15') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_FIFTEEN'); ?></option>
									<option value="*/30"<?php if ($this->row->get('minute') == '*/30') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_THIRTY'); ?></option>
									<?php for ($i=0, $n=60; $i < $n; $i++) { ?>
										<option value="<?php echo $i; ?>"<?php if ($this->row->get('minute') == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>
								<label for="field-hour-c"><?php echo Lang::txt('COM_CRON_FIELD_HOUR'); ?></label>:
							</th>
							<td style="width: 10%">
								<input type="text" name="fields[hour][c]" id="field-hour-c" value="<?php echo $this->row->get('hour'); ?>" />
							</td>
							<td>
								<select name="fields[hour][s]" id="field-hour-s">
									<option value=""<?php if ($this->row->get('hour') == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
									<option value="*"<?php if ($this->row->get('hour') == '*') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?></option>
									<option value="*/2"<?php if ($this->row->get('hour') == '*/2') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_OTHER'); ?></option>
									<option value="*/4"<?php if ($this->row->get('hour') == '*/4') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_FOUR'); ?></option>
									<option value="*/6"<?php if ($this->row->get('hour') == '*/6') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_SIX'); ?></option>
									<option value="0"<?php if ($this->row->get('hour') == "0") { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_MIDNIGHT'); ?></option>
									<?php for ($i=1, $n=24; $i < $n; $i++) { ?>
										<option value="<?php echo $i; ?>"<?php if ($this->row->get('hour') == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>
								<label for="field-day-c"><?php echo Lang::txt('COM_CRON_FIELD_DAY_OF_MONTH'); ?></label>:
							</th>
							<td>
								<input type="text" name="fields[day][c]" id="field-day-c" value="<?php echo $this->row->get('day'); ?>" />
							</td>
							<td>
								<select name="fields[day][s]" id="field-day-s">
									<option value=""<?php if ($this->row->get('day') == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
									<option value="*"<?php if ($this->row->get('day') == '*') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?></option>
									<?php for ($i=1, $n=32; $i < $n; $i++) { ?>
										<option value="<?php echo $i; ?>"<?php if ($this->row->get('day') == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<th>
								<label for="field-month-c"><?php echo Lang::txt('COM_CRON_FIELD_MONTH'); ?></label>:
							</th>
							<td>
								<input type="text" name="fields[month][c]" id="field-month-c" value="<?php echo $this->row->get('month'); ?>" />
							</td>
							<td>
								<select name="fields[month][s]" id="field-month-s">
									<option value=""<?php if ($this->row->get('month') == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
									<option value="*"<?php if ($this->row->get('month') == '*') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?></option>
									<option value="*/2"<?php if ($this->row->get('month') == '*/2') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_OTHER'); ?></option>
									<option value="*/3"<?php if ($this->row->get('month') == '*/4') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_THREE'); ?></option>
									<option value="*/6"<?php if ($this->row->get('month') == '*/6') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY_SIX'); ?></option>
									<option value="1"<?php if ($this->row->get('month') == '1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JANUARY_SHORT'); ?></option>
									<option value="2"<?php if ($this->row->get('month') == '2') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('FEBRUARY_SHORT'); ?></option>
									<option value="3"<?php if ($this->row->get('month') == '3') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('MARCH_SHORT'); ?></option>
									<option value="4"<?php if ($this->row->get('month') == '4') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('APRIL_SHORT'); ?></option>
									<option value="5"<?php if ($this->row->get('month') == '5') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('MAY_SHORT'); ?></option>
									<option value="6"<?php if ($this->row->get('month') == '6') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNE_SHORT'); ?></option>
									<option value="7"<?php if ($this->row->get('month') == '7') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JULY_SHORT'); ?></option>
									<option value="8"<?php if ($this->row->get('month') == '8') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('AUGUST_SHORT'); ?></option>
									<option value="9"<?php if ($this->row->get('month') == '9') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('SEPTEMBER_SHORT'); ?></option>
									<option value="10"<?php if ($this->row->get('month') == '10') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('OCTOBER_SHORT'); ?></option>
									<option value="11"<?php if ($this->row->get('month') == '11') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('NOVEMBER_SHORT'); ?></option>
									<option value="12"<?php if ($this->row->get('month') == '12') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('DECEMBER_SHORT'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th>
								<label for="field-dayofweek-c"><?php echo Lang::txt('COM_CRON_FIELD_DAY_OF_WEEK'); ?></label>:
							</th>
							<td>
								<input type="text" name="fields[dayofweek][c]" id="field-dayofweek-c" value="<?php echo $this->row->get('dayofweek'); ?>" />
							</td>
							<td>
								<select name="fields[dayofweek][s]" id="field-dayofweek-s">
									<option value=""<?php if ($this->row->get('dayofweek') == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
									<option value="*"<?php if ($this->row->get('dayofweek') == '*') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_CRON_FIELD_OPT_EVERY'); ?></option>
									<option value="0"<?php if ($this->row->get('dayofweek') == '0') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('SUN'); ?></option>
									<option value="1"<?php if ($this->row->get('dayofweek') == '1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('MON'); ?></option>
									<option value="2"<?php if ($this->row->get('dayofweek') == '2') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('TUE'); ?></option>
									<option value="3"<?php if ($this->row->get('dayofweek') == '3') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('WED'); ?></option>
									<option value="4"<?php if ($this->row->get('dayofweek') == '4') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('THU'); ?></option>
									<option value="5"<?php if ($this->row->get('dayofweek') == '5') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('FRI'); ?></option>
									<option value="6"<?php if ($this->row->get('dayofweek') == '6') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('SAT'); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_CRON_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->get('id')); ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->get('id')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CRON_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							$editor = User::getInstance($this->row->get('created_by'));
							echo $this->escape($editor->get('name'));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CRON_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->get('created')); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape(Date::of($this->row->get('created'))->toLocal('Y-m-d H:i:s')); ?>" />
						</td>
					</tr>
				<?php if ($this->row->get('modified')) { ?>
					<tr>
						<th><?php echo Lang::txt('COM_CRON_FIELD_MODIFIER'); ?>:</th>
						<td>
							<?php
							$modifier = User::getInstance($this->row->get('modified_by'));
							echo $this->escape($modifier->get('name', Lang::txt('COM_CRON_UNKNOWN')));
							?>
							<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->escape($this->row->get('modified_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CRON_FIELD_MODIFIED'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->get('modified')); ?>
							<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->escape(Date::of($this->row->get('created'))->toLocal('Y-m-d H:i:s')); ?>" />
						</td>
					</tr>
				<?php } ?>
				<?php if ($this->row->get('id')) { ?>
					<tr>
						<th><?php echo Lang::txt('COM_CRON_FIELD_LAST_RUN'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->get('last_run')); ?>
							<input type="hidden" name="fields[last_run]" id="field-last_run" value="<?php echo $this->escape($this->row->get('last_run')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CRON_FIELD_NEXT_RUN'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->get('next_run')); ?>
							<input type="hidden" name="fields[next_run]" id="field-next_run" value="<?php echo $this->escape($this->row->get('next_run')); ?>" />
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_CRON_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php echo ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php echo ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php echo ($this->row->get('state') == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-publish_up"><?php echo Lang::txt('COM_CRON_FIELD_START_RUNNING'); ?>:</label><br />
					<?php echo Html::input('calendar', 'fields[publish_up]', $this->escape(($this->row->get('publish_up') == '0000-00-00 00:00:00' ? '' : $this->row->get('publish_up'))), array('id' => 'field-publish_up')); ?>
				</div>

				<div class="input-wrap">
					<label for="field-publish_down"><?php echo Lang::txt('COM_CRON_FIELD_STOP_RUNNING'); ?>:</label><br />
					<?php echo Html::input('calendar', 'fields[publish_down]', $this->escape(($this->row->get('publish_down') == '0000-00-00 00:00:00' ? '' : $this->row->get('publish_down'))), array('id' => 'field-publish_down')); ?>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
