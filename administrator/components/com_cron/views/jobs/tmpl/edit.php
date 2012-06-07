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

$canDo = CronHelper::getActions('component');

$text = ($this->task == 'edit' ? JText::_('Edit Job') : JText::_('New Job'));
JToolBarHelper::title(JText::_('Cron') . ': <small><small>[ ' . $text . ' ]</small></small>', 'event.png');
JToolBarHelper::spacer();	
if ($canDo->get('core.edit')) {
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

$create_date = NULL;
if (intval($this->row->created) <> 0) 
{
	$create_date = JHTML::_('date', $this->row->created);
}

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
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
		alert( 'Entry must have a title' );
	} else {
		submitform( pressbutton );
	}
}

var Fields = {
	initialise: function() {
		$('field-recurrence').addEvent('change', function(){
			var min = '*',
				hour = '*',
				day = '*',
				month = '*',
				dow = '*',
				recurrence = $(this).value;
			
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
				if ($('custom').hasClass('hide')) {
					$('custom').removeClass('hide');
				}
			} else {
				if (!$('custom').hasClass('hide')) {
					$('custom').addClass('hide');
				}
			}
			
			$('field-minute-c').value = min;
			$('field-minute-s').value = min;
			$('field-hour-c').value = hour;
			$('field-hour-s').value = hour;
			$('field-day-c').value = day;
			$('field-day-s').value = day;
			$('field-month-c').value = month;
			$('field-month-s').value = month;
			$('field-dayofweek-c').value = dow;
			$('field-dayofweek-s').value = dow;
		});
		
		$('field-minute-s').addEvent('change', function(){
			$('field-minute-c').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		$('field-minute-c').addEvent('change', function(){
			$('field-minute-s').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		
		$('field-hour-s').addEvent('change', function(){
			$('field-hour-c').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		$('field-hour-c').addEvent('change', function(){
			$('field-hour-s').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		
		$('field-day-s').addEvent('change', function(){
			$('field-day-c').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		$('field-day-c').addEvent('change', function(){
			$('field-day-s').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		
		$('field-month-s').addEvent('change', function(){
			$('field-month-c').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		$('field-month-c').addEvent('change', function(){
			$('field-month-s').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		
		$('field-dayofweek-s').addEvent('change', function(){
			$('field-dayofweek-c').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
		$('field-dayofweek-c').addEvent('change', function(){
			$('field-dayofweek-s').value = $(this).value;
			$('field-recurrence').value = 'custom';
		});
	}
}

window.addEvent('domready', Fields.initialise);
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Details'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?>:</label></td>
						<td><input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-event"><?php echo JText::_('Event'); ?>:</label></td>
						<td>
							<select name="fields[event]" id="field-event">
								<option value=""<?php echo (!$this->row->plugin) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Select...'); ?></option>
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
								foreach ($plugin->events as $event => $label)
								{
?>
									<option value="<?php echo $plugin->folder; ?>::<?php echo $event; ?>"<?php if ($this->row->event == $event) { echo ' selected="selected"'; } ?>><?php echo $this->escape($label); ?></option>
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
						</td>
					</tr>
					<!-- <tr>
						<td class="key"><label for="field-event"><?php echo JText::_('Event'); ?>:</label></td>
						<td>
							<input type="text" name="fields[event]" id="field-event" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->event)); ?>" />
						</td>
					</tr> -->
					<tr>
						<td class="key"><label for="field-state"><?php echo JText::_('State'); ?>:</label></td>
						<td>
							<select name="fields[state]" id="field-state">
								<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Unpublished'); ?></option>
								<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Published'); ?></option>
								<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Trashed'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Recurrence'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><?php echo JText::_('Common'); ?>:</td>
						<td colspan="2">
							<select name="fields[recurrence]" id="field-recurrence">
								<option value=""<?php echo ($this->row->recurrence == '') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Select...'); ?></option>
								<option value="custom"<?php echo ($this->row->recurrence == 'custom') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Custom'); ?></option>
								<option value="0 0 1 1 *"<?php echo ($this->row->recurrence == '0 0 1 1 *') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Run once a year, midnight, Jan. 1st'); ?></option>
								<option value="0 0 1 * *"<?php echo ($this->row->recurrence == '0 0 1 * *') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Run once a month, midnight, first of month'); ?></option>
								<option value="0 0 * * 0"<?php echo ($this->row->recurrence == '0 0 * * 0') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Run once a week, midnight on Sunday'); ?></option>
								<option value="0 0 * * *"<?php echo ($this->row->recurrence == '0 0 * * *') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Run once a day, midnight'); ?></option>
								<option value="0 * * * *"<?php echo ($this->row->recurrence == '0 * * * *') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Run once an hour, beginning of hour'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
				<tbody id="custom"<?php echo ($this->row->recurrence == 'custom') ? '' : ' class="hide"'; ?>>
					<tr>
						<td class="key"><label for="field-minute-c"><?php echo JText::_('Minute'); ?></label>:</td>
						<td>
							<input type="text" name="fields[minute][c]" id="field-minute-c" value="<?php echo $this->row->minute; ?>" />
						</td>
						<td>
							<select name="fields[minute][s]" id="field-minute-s">
								<option value=""<?php if ($this->row->minute == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('Custom'); ?></option>
								<option value="*"<?php if ($this->row->minute == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every'); ?></option>
								<option value="*/5"<?php if ($this->row->minute == '*/5') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every 5'); ?></option>
								<option value="*/10"<?php if ($this->row->minute == '*/10') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every 10'); ?></option>
								<option value="*/15"<?php if ($this->row->minute == '*/15') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every 15'); ?></option>
								<option value="*/30"<?php if ($this->row->minute == '*/30') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every 30'); ?></option>
								<?php
								for ($i=0, $n=60; $i < $n; $i++) 
								{
								?>
								<option value="<?php echo $i; ?>"<?php if ($this->row->minute == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
								<?php 
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-hour-c"><?php echo JText::_('Hour'); ?></label>:</td>
						<td style="width: 10%">
							<input type="text" name="fields[hour][c]" id="field-hour-c" value="<?php echo $this->row->hour; ?>" />
						</td>
						<td>
							<select name="fields[hour][s]" id="field-hour-s">
								<option value=""<?php if ($this->row->hour == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('Custom'); ?></option>
								<option value="*"<?php if ($this->row->hour == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every'); ?></option>
								<option value="*/2"<?php if ($this->row->hour == '*/2') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every Other'); ?></option>
								<option value="*/4"<?php if ($this->row->hour == '*/4') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every Four'); ?></option>
								<option value="*/6"<?php if ($this->row->hour == '*/6') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every Six'); ?></option>
								<option value="0"<?php if ($this->row->hour == "0") { echo ' selected="selected"'; } ?>><?php echo JText::_('0 = 12AM/Midnight'); ?></option>
								<?php
								for ($i=1, $n=24; $i < $n; $i++) 
								{
								?>
								<option value="<?php echo $i; ?>"<?php if ($this->row->hour == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
								<?php 
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-day-c"><?php echo JText::_('Day of month'); ?></label>:</td>
						<td>
							<input type="text" name="fields[day][c]" id="field-day-c" value="<?php echo $this->row->day; ?>" />
						</td>
						<td>
							<select name="fields[day][s]" id="field-day-s">
								<option value=""<?php if ($this->row->day == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('Custom'); ?></option>
								<option value="*"<?php if ($this->row->day == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every'); ?></option>
								<?php
								for ($i=1, $n=32; $i < $n; $i++) 
								{
								?>
								<option value="<?php echo $i; ?>"<?php if ($this->row->day == (string) $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
								<?php 
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-month-c"><?php echo JText::_('Month'); ?></label>:</td>
						<td>
							<input type="text" name="fields[month][c]" id="field-month-c" value="<?php echo $this->row->month; ?>" />
						</td>
						<td>
							<select name="fields[month][s]" id="field-month-s">
								<option value=""<?php if ($this->row->month == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('Custom'); ?></option>
								<option value="*"<?php if ($this->row->month == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every'); ?></option>
								<option value="*/2"<?php if ($this->row->month == '*/2') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every Other'); ?></option>
								<option value="*/3"<?php if ($this->row->month == '*/4') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every Three (quarterly)'); ?></option>
								<option value="*/6"<?php if ($this->row->month == '*/6') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every Six'); ?></option>
								<option value="1"<?php if ($this->row->month == '1') { echo ' selected="selected"'; } ?>><?php echo JText::_('Jan'); ?></option>
								<option value="2"<?php if ($this->row->month == '2') { echo ' selected="selected"'; } ?>><?php echo JText::_('Feb'); ?></option>
								<option value="3"<?php if ($this->row->month == '3') { echo ' selected="selected"'; } ?>><?php echo JText::_('Mar'); ?></option>
								<option value="4"<?php if ($this->row->month == '4') { echo ' selected="selected"'; } ?>><?php echo JText::_('Apr'); ?></option>
								<option value="5"<?php if ($this->row->month == '5') { echo ' selected="selected"'; } ?>><?php echo JText::_('May'); ?></option>
								<option value="6"<?php if ($this->row->month == '6') { echo ' selected="selected"'; } ?>><?php echo JText::_('Jun'); ?></option>
								<option value="7"<?php if ($this->row->month == '7') { echo ' selected="selected"'; } ?>><?php echo JText::_('Jul'); ?></option>
								<option value="8"<?php if ($this->row->month == '8') { echo ' selected="selected"'; } ?>><?php echo JText::_('Aug'); ?></option>
								<option value="9"<?php if ($this->row->month == '9') { echo ' selected="selected"'; } ?>><?php echo JText::_('Sep'); ?></option>
								<option value="10"<?php if ($this->row->month == '10') { echo ' selected="selected"'; } ?>><?php echo JText::_('Oct'); ?></option>
								<option value="11"<?php if ($this->row->month == '11') { echo ' selected="selected"'; } ?>><?php echo JText::_('Nov'); ?></option>
								<option value="12"<?php if ($this->row->month == '12') { echo ' selected="selected"'; } ?>><?php echo JText::_('Dec'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="field-dayofweek-c"><?php echo JText::_('Day of week'); ?></label>:</td>
						<td>
							<input type="text" name="fields[dayofweek][c]" id="field-dayofweek-c" value="<?php echo $this->row->dayofweek; ?>" />
						</td>
						<td>
							<select name="fields[dayofweek][s]" id="field-dayofweek-s">
								<option value=""<?php if ($this->row->dayofweek == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('Custom'); ?></option>
								<option value="*"<?php if ($this->row->dayofweek == '*') { echo ' selected="selected"'; } ?>><?php echo JText::_('Every'); ?></option>
								<option value="0"<?php if ($this->row->dayofweek == '0') { echo ' selected="selected"'; } ?>><?php echo JText::_('Sun'); ?></option>
								<option value="1"<?php if ($this->row->dayofweek == '1') { echo ' selected="selected"'; } ?>><?php echo JText::_('Mon'); ?></option>
								<option value="2"<?php if ($this->row->dayofweek == '2') { echo ' selected="selected"'; } ?>><?php echo JText::_('Tue'); ?></option>
								<option value="3"<?php if ($this->row->dayofweek == '3') { echo ' selected="selected"'; } ?>><?php echo JText::_('Wed'); ?></option>
								<option value="4"<?php if ($this->row->dayofweek == '4') { echo ' selected="selected"'; } ?>><?php echo JText::_('Thu'); ?></option>
								<option value="5"<?php if ($this->row->dayofweek == '5') { echo ' selected="selected"'; } ?>><?php echo JText::_('Fri'); ?></option>
								<option value="6"<?php if ($this->row->dayofweek == '6') { echo ' selected="selected"'; } ?>><?php echo JText::_('Sat'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<table class="meta" summary="<?php echo JText::_('Metadata for this cron job'); ?>">
				<tbody>
					<tr>
						<th class="key"><?php echo JText::_('ID'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->id); ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->id); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Created By'); ?>:</th>
						<td>
							<?php 
							$editor = JUser::getInstance($this->row->created_by);
							echo $this->escape($editor->get('name')); 
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->created_by); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Created Date'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->created); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->created); ?>" />
						</td>
					</tr>
<?php if ($this->row->modified) { ?>
					<tr>
						<th class="key"><?php echo JText::_('Modified By'); ?>:</th>
						<td>
							<?php 
							$modifier = JUser::getInstance($this->row->modified_by);
							echo $this->escape($modifier->get('name')); 
							?>
							<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->escape($this->row->modified_by); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Modified Date'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->modified); ?>
							<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->escape($this->row->modified); ?>" />
						</td>
					</tr>
<?php } ?>
<?php if ($this->row->id) { ?>
					<tr>
						<th class="key"><?php echo JText::_('Last Run'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->last_run); ?>
							<input type="hidden" name="fields[last_run]" id="field-last_run" value="<?php echo $this->escape($this->row->last_run); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Next Run'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->next_run); ?>
							<input type="hidden" name="fields[next_run]" id="field-next_run" value="<?php echo $this->escape($this->row->next_run); ?>" />
						</td>
					</tr>
<?php } ?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

<?php if (version_compare(JVERSION, '1.6', 'ge')) { ?>
	<?php if ($canDo->get('core.admin')): ?>
		<div class="col width-100 fltlft">
			<fieldset class="panelform">
				<legend><span><?php echo JText::_('COM_FORUM_FIELDSET_RULES'); ?></span></legend>
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
		<div class="clr"></div>
	<?php endif; ?>
<?php } ?>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
