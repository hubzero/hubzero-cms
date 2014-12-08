<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$canDo = CronHelperPermissions::getActions('component');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_CRON') . ': ' . $text, 'cron.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('job');

JHTML::_('behavior.calendar');

$create_date = NULL;
if (intval($this->row->get('created')) <> 0)
{
	$create_date = JHTML::_('date', $this->row->get('created'));
}
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
		alert( '<?php echo JText::_('CON_CRON_ERROR_MISSING_TITLE');?>' );
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
	foreach ($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_CRON_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-event"><?php echo JText::_('COM_CRON_FIELD_EVENT'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<select name="fields[event]" id="field-event">
					<option value=""<?php echo (!$this->row->get('plugin')) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_CRON_SELECT'); ?></option>
					<?php
						if ($this->plugins)
						{
							foreach ($this->plugins as $plugin)
							{
								?>
								<optgroup label="<?php echo $this->escape($plugin->name); ?>">
								<?php
								if ($plugin->events)
								{
									foreach ($plugin->events as $event)
									{
									?>
									<option value="<?php echo $plugin->element; ?>::<?php echo $event['name']; ?>"<?php if ($this->row->get('event') == $event['name']) { echo ' selected="selected"'; } ?>><?php echo $this->escape($event['label']); ?></option>
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
					if ($plugin->events)
					{
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
								$param = new JParameter(
									(is_object($data) ? $data->toString() : $data),
									JPATH_ROOT . DS . 'plugins' . DS . 'cron' . DS . $plugin->element . DS . $plugin->element . '.xml'
								);
								$param->addElementPath(JPATH_ROOT . DS . 'plugins' . DS . 'cron' . DS . $plugin->element);
								//$out = $param->render('params', $event['params']);
								$html = array();
								foreach ($param->getParams('params', $event['params']) as $p)
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

								$out = (!empty($html) ? implode("\n", $html) : $out);
							}

							if (!$out)
							{
								$out = '<div class="input-wrap"><p><i>' . JText::_('COM_CRON_NO_PARAMETERS_FOUND') . '</i></p></div>';
							}
							?>
							<fieldset class="adminform paramlist eventparams" style="display: <?php echo $style; ?>;" id="params-<?php echo $plugin->element . '--' . $event['name']; ?>">
								<legend><span><?php echo JText::_('COM_CRON_FIELDSET_PARAMETERS'); ?></span></legend>
								<?php echo $out; ?>
							</fieldset>
							<?php
						}
					}
				}
			}
		?>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_CRON_FIELDSET_RECURRENCE'); ?></span></legend>

			<div class="input-wrap">
				<?php echo JText::_('COM_CRON_FIELD_COMMON'); ?>:<br />
				<select name="fields[recurrence]" id="field-recurrence">
					<option value=""<?php echo ($this->row->get('recurrence') == '') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_CRON_FIELD_COMMON_OPT_SELECT'); ?></option>
					<option value="custom"<?php echo ($this->row->get('recurrence') == 'custom') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_CRON_FIELD_COMMON_OPT_CUSTOM'); ?></option>
					<option value="0 0 1 1 *"<?php echo ($this->row->get('recurrence') == '0 0 1 1 *') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_CRON_FIELD_COMMON_OPT_ONCE_A_YEAR'); ?></option>
					<option value="0 0 1 * *"<?php echo ($this->row->get('recurrence') == '0 0 1 * *') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_CRON_FIELD_COMMON_OPT_ONCE_A_MONTH'); ?></option>
					<option value="0 0 * * 0"<?php echo ($this->row->get('recurrence') == '0 0 * * 0') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_CRON_FIELD_COMMON_OPT_ONCE_A_WEEK'); ?></option>
					<option value="0 0 * * *"<?php echo ($this->row->get('recurrence') == '0 0 * * *') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_CRON_FIELD_COMMON_OPT_ONCE_A_DAY'); ?></option>
					<option value="0 * * * *"<?php echo ($this->row->get('recurrence') == '0 * * * *') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_CRON_FIELD_COMMON_OPT_ONCE_AN_HOUR'); ?></option>
				</select>
			</div>

			<table class="admintable">
				<tbody id="custom"<?php echo ($this->row->get('recurrence') == 'custom') ? '' : ' class="hide"'; ?>>
					<tr>
						<td class="key"><label for="field-minute-c"><?php echo JText::_('COM_CRON_FIELD_MINUTE'); ?></label>:</td>
						<td>
							<input type="text" name="fields[minute][c]" id="field-minute-c" value="<?php echo $this->row->get('minute'); ?>" />
						</td>
						<td>
							<select name="fields[minute][s]" id="field-minute-s">
								<option value=""<?php if ($this->row->get('minute') == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
								<option value="*"<?php if ($this->row->get('minute') == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY'); ?></option>
								<option value="*/5"<?php if ($this->row->get('minute') == '*/5') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_FIVE'); ?></option>
								<option value="*/10"<?php if ($this->row->get('minute') == '*/10') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_TEN'); ?></option>
								<option value="*/15"<?php if ($this->row->get('minute') == '*/15') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_FIFTEEN'); ?></option>
								<option value="*/30"<?php if ($this->row->get('minute') == '*/30') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_THIRTY'); ?></option>
								<?php
								for ($i=0, $n=60; $i < $n; $i++)
								{
								?>
								<option value="<?php echo $i; ?>"<?php if ($this->row->get('minute') == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-hour-c"><?php echo JText::_('COM_CRON_FIELD_HOUR'); ?></label>:</td>
						<td style="width: 10%">
							<input type="text" name="fields[hour][c]" id="field-hour-c" value="<?php echo $this->row->get('hour'); ?>" />
						</td>
						<td>
							<select name="fields[hour][s]" id="field-hour-s">
								<option value=""<?php if ($this->row->get('hour') == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
								<option value="*"<?php if ($this->row->get('hour') == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY'); ?></option>
								<option value="*/2"<?php if ($this->row->get('hour') == '*/2') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_OTHER'); ?></option>
								<option value="*/4"<?php if ($this->row->get('hour') == '*/4') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_FOUR'); ?></option>
								<option value="*/6"<?php if ($this->row->get('hour') == '*/6') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_SIX'); ?></option>
								<option value="0"<?php if ($this->row->get('hour') == "0") { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_MIDNIGHT'); ?></option>
								<?php
								for ($i=1, $n=24; $i < $n; $i++)
								{
								?>
								<option value="<?php echo $i; ?>"<?php if ($this->row->get('hour') == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-day-c"><?php echo JText::_('COM_CRON_FIELD_DAY_OF_MONTH'); ?></label>:</td>
						<td>
							<input type="text" name="fields[day][c]" id="field-day-c" value="<?php echo $this->row->get('day'); ?>" />
						</td>
						<td>
							<select name="fields[day][s]" id="field-day-s">
								<option value=""<?php if ($this->row->get('day') == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
								<option value="*"<?php if ($this->row->get('day') == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY'); ?></option>
								<?php
								for ($i=1, $n=32; $i < $n; $i++)
								{
								?>
								<option value="<?php echo $i; ?>"<?php if ($this->row->get('day') == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-month-c"><?php echo JText::_('COM_CRON_FIELD_MONTH'); ?></label>:</td>
						<td>
							<input type="text" name="fields[month][c]" id="field-month-c" value="<?php echo $this->row->get('month'); ?>" />
						</td>
						<td>
							<select name="fields[month][s]" id="field-month-s">
								<option value=""<?php if ($this->row->get('month') == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
								<option value="*"<?php if ($this->row->get('month') == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY'); ?></option>
								<option value="*/2"<?php if ($this->row->get('month') == '*/2') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_OTHER'); ?></option>
								<option value="*/3"<?php if ($this->row->get('month') == '*/4') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_THREE'); ?></option>
								<option value="*/6"<?php if ($this->row->get('month') == '*/6') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY_SIX'); ?></option>
								<option value="1"<?php if ($this->row->get('month') == '1') { echo ' selected="selected"'; } ?>><?php echo JText::_('JANUARY_SHORT'); ?></option>
								<option value="2"<?php if ($this->row->get('month') == '2') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEBRUARY_SHORT'); ?></option>
								<option value="3"<?php if ($this->row->get('month') == '3') { echo ' selected="selected"'; } ?>><?php echo JText::_('MARCH_SHORT'); ?></option>
								<option value="4"<?php if ($this->row->get('month') == '4') { echo ' selected="selected"'; } ?>><?php echo JText::_('APRIL_SHORT'); ?></option>
								<option value="5"<?php if ($this->row->get('month') == '5') { echo ' selected="selected"'; } ?>><?php echo JText::_('MAY_SHORT'); ?></option>
								<option value="6"<?php if ($this->row->get('month') == '6') { echo ' selected="selected"'; } ?>><?php echo JText::_('JUNE_SHORT'); ?></option>
								<option value="7"<?php if ($this->row->get('month') == '7') { echo ' selected="selected"'; } ?>><?php echo JText::_('JULY_SHORT'); ?></option>
								<option value="8"<?php if ($this->row->get('month') == '8') { echo ' selected="selected"'; } ?>><?php echo JText::_('AUGUST_SHORT'); ?></option>
								<option value="9"<?php if ($this->row->get('month') == '9') { echo ' selected="selected"'; } ?>><?php echo JText::_('SEPTEMBER_SHORT'); ?></option>
								<option value="10"<?php if ($this->row->get('month') == '10') { echo ' selected="selected"'; } ?>><?php echo JText::_('OCTOBER_SHORT'); ?></option>
								<option value="11"<?php if ($this->row->get('month') == '11') { echo ' selected="selected"'; } ?>><?php echo JText::_('NOVEMBER_SHORT'); ?></option>
								<option value="12"<?php if ($this->row->get('month') == '12') { echo ' selected="selected"'; } ?>><?php echo JText::_('DECEMBER_SHORT'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-dayofweek-c"><?php echo JText::_('COM_CRON_FIELD_DAY_OF_WEEK'); ?></label>:</td>
						<td>
							<input type="text" name="fields[dayofweek][c]" id="field-dayofweek-c" value="<?php echo $this->row->get('dayofweek'); ?>" />
						</td>
						<td>
							<select name="fields[dayofweek][s]" id="field-dayofweek-s">
								<option value=""<?php if ($this->row->get('dayofweek') == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_CUSTOM'); ?></option>
								<option value="*"<?php if ($this->row->get('dayofweek') == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_CRON_FIELD_OPT_EVERY'); ?></option>
								<option value="0"<?php if ($this->row->get('dayofweek') == '0') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUN'); ?></option>
								<option value="1"<?php if ($this->row->get('dayofweek') == '1') { echo ' selected="selected"'; } ?>><?php echo JText::_('MON'); ?></option>
								<option value="2"<?php if ($this->row->get('dayofweek') == '2') { echo ' selected="selected"'; } ?>><?php echo JText::_('TUE'); ?></option>
								<option value="3"<?php if ($this->row->get('dayofweek') == '3') { echo ' selected="selected"'; } ?>><?php echo JText::_('WED'); ?></option>
								<option value="4"<?php if ($this->row->get('dayofweek') == '4') { echo ' selected="selected"'; } ?>><?php echo JText::_('THU'); ?></option>
								<option value="5"<?php if ($this->row->get('dayofweek') == '5') { echo ' selected="selected"'; } ?>><?php echo JText::_('FRI'); ?></option>
								<option value="6"<?php if ($this->row->get('dayofweek') == '6') { echo ' selected="selected"'; } ?>><?php echo JText::_('SAT'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('COM_CRON_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->escape($this->row->get('id')); ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->get('id')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_CRON_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$editor = JUser::getInstance($this->row->get('created_by'));
						echo $this->escape($editor->get('name'));
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_CRON_FIELD_CREATED'); ?>:</th>
					<td>
						<?php echo $this->escape($this->row->get('created')); ?>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape(JHTML::_('date', $this->row->get('created'), 'Y-m-d H:i:s')); ?>" />
					</td>
				</tr>
			<?php if ($this->row->get('modified')) { ?>
				<tr>
					<th class="key"><?php echo JText::_('COM_CRON_FIELD_MODIFIER'); ?>:</th>
					<td>
						<?php
						$modifier = JUser::getInstance($this->row->get('modified_by'));
						echo $this->escape($modifier->get('name'));
						?>
						<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->escape($this->row->get('modified_by')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_CRON_FIELD_MODIFIED'); ?>:</th>
					<td>
						<?php echo $this->escape($this->row->get('modified')); ?>
						<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->escape(JHTML::_('date', $this->row->get('modified'), 'Y-m-d H:i:s')); ?>" />
					</td>
				</tr>
			<?php } ?>
			<?php if ($this->row->get('id')) { ?>
				<tr>
					<th class="key"><?php echo JText::_('COM_CRON_FIELD_LAST_RUN'); ?>:</th>
					<td>
						<?php echo $this->escape($this->row->get('last_run')); ?>
						<input type="hidden" name="fields[last_run]" id="field-last_run" value="<?php echo $this->escape($this->row->get('last_run')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_CRON_FIELD_NEXT_RUN'); ?>:</th>
					<td>
						<?php echo $this->escape($this->row->get('next_run')); ?>
						<input type="hidden" name="fields[next_run]" id="field-next_run" value="<?php echo $this->escape($this->row->get('next_run')); ?>" />
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('COM_CRON_FIELD_STATE'); ?>:</label><br />
				<select name="fields[state]" id="field-state">
					<option value="0"<?php echo ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JUNPUBLISHED'); ?></option>
					<option value="1"<?php echo ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JPUBLISHED'); ?></option>
					<option value="2"<?php echo ($this->row->get('state') == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JTRASHED'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="field-publish_up"><?php echo JText::_('COM_CRON_FIELD_START_RUNNING'); ?>:</label><br />
				<?php echo JHTML::_('calendar', $this->escape(($this->row->get('publish_up') == '0000-00-00 00:00:00' ? '' : $this->row->get('publish_up'))), 'fields[publish_up]', 'field-publish_up'); ?>
			</div>

			<div class="input-wrap">
				<label for="field-publish_down"><?php echo JText::_('COM_CRON_FIELD_STOP_RUNNING'); ?>:</label><br />
				<?php echo JHTML::_('calendar', $this->escape(($this->row->get('publish_down') == '0000-00-00 00:00:00' ? '' : $this->row->get('publish_down'))), 'fields[publish_down]', 'field-publish_down'); ?>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
