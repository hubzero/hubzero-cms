<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//include registration
$includeRegistration = JRequest::getVar('includeRegistration', 0);

//set button and form title
$formTitle = JText::_('Add Group Event');
$submitBtn = JText::_('Submit New Event');
if ($this->event->get('id'))
{
	$formTitle = JText::_('Edit Group Event');
	$submitBtn = JText::_('Update Event');
}

$showImport = false;
if ($this->params->get('allow_import', 1) && !$this->event->get('id'))
{
	$showImport = true;
}
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<?php if (JRequest::getVar('action') == 'edit') : ?>
			<a class="icon-prev btn back" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=details&event_id='.$this->event->get('id')); ?>">
				<?php echo JText::_('Back to Event'); ?>
			</a>
		<?php else : ?>
			<a class="icon-prev btn back" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$this->year.'&month='.$this->month); ?>">
				<?php echo JText::_('Back to Events Calendar'); ?>
			</a>
		<?php endif; ?>
	</li>
</ul>


<div class="grid">
	<div class="col <?php echo ($showImport) ? 'span9' : 'span12'; ?>">
		<form name="editevent" action="index.php" method="post" id="hubForm" class="full">
			<fieldset>
				<legend><?php echo $formTitle; ?></legend>
				<label><?php echo JText::_('Title:'); ?> <span class="required">Required</span>
					<input type="text" name="event[title]" id="event_title" value="<?php echo $this->escape($this->event->get('title')); ?>" />
				</label>

				<?php if (count($this->calendars) > 0 || $this->authorized == 'manager') : ?>
					<label><?php echo JText::_('Calendar:'); ?> <span class="optional">Optional</span>
						<select name="event[calendar_id]" id="event-calendar-picker">
							<option value=""><?php echo JText::_('- Select Calendar for Event &mdash;'); ?></option>
							<?php foreach ($this->calendars as $calendar) : ?>
								<?php $sel = ($calendar->get('id') == $this->event->get('calendar_id')) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> data-img="/plugins/groups/calendar/images/swatch-<?php echo ($calendar->get('color')) ? $calendar->get('color') : 'gray'; ?>.png" value="<?php echo $calendar->get('id'); ?>"><?php echo $calendar->get('title'); ?></option>
							<?php endforeach; ?>
						</select>

						<?php if ($this->authorized == 'manager') : ?>
							<span class="hint">
								Need a new calendar? <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=addcalendar'); ?>">Click here!</a>
							</span>
						<?php endif; ?>
					</label>
				<?php endif; ?>

				<label><?php echo JText::_('Details:'); ?> <span class="optional">Optional</span>
					<textarea name="content" id="event_content" rows="10"><?php echo $this->event->get('content'); ?></textarea>
					<span class="hint"><?php echo JText::_('Limited HTML allowed (a, iframe, strong, em, u)'); ?></span>
				</label>

				<label><?php echo JText::_('Location:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[adresse_info]" id="event_location" value="<?php echo $this->event->get('adresse_info'); ?>" />
				</label>

				<label><?php echo JText::_('Contact:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[contact_info]" value="<?php echo $this->event->get('contact_info'); ?>" />
					<span class="hint">Accepts names and email addresses. (ex. John Doe john_doe@domain.com)</span>
				</label>

				<label><?php echo JText::_('Website:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[extra_info]" id="event_website" value="<?php echo $this->event->get('extra_info'); ?>" />
				</label>

				<fieldset>
					<legend>
						<?php echo JText::_('Date &amp; Time Settings'); ?>
					</legend>
					<label><?php echo JText::_('Start:'); ?> <span class="required">Required</span>
						<?php
							$start      = JRequest::getVar('start', '', 'get');
							$publish_up = ($this->event->get('publish_up')) ? $this->event->get('publish_up') : $start;
							if ($publish_up != '' && $publish_up != '0000-00-00 00:00:00')
							{
								$publish_up = JHTML::_('date', $publish_up, 'm/d/Y @ g:i a');
							}
						?>
						<input type="text" name="event[publish_up]" id="event_start_date" value="<?php echo $publish_up; ?>" placeholder="mm/dd/yyyy @ h:mm am/pm" class="no-legacy-placeholder-support" />
					</label>

					<label><?php echo JText::_('End:'); ?> <span class="optional">Optional</span>
						<?php
							$end          = JRequest::getVar('end', '', 'get');
							$publish_down = ($this->event->get('publish_down')) ? $this->event->get('publish_down') : $end;
							if ($publish_down != '' && $publish_down != '0000-00-00 00:00:00')
							{
								$publish_down = JHTML::_('date', $publish_down, 'm/d/Y @ g:i a');
							}
						?>
						<input type="text" name="event[publish_down]" id="event_end_date" value="<?php echo $publish_down; ?>" placeholder="mm/dd/yyyy @ h:mm am/pm" class="no-legacy-placeholder-support" />
					</label>

					<label><?php echo JText::_('Timezone:'); ?> <span class="optional">Optional</span>
						<?php
							$timezone = $this->event->get('time_zone');
							$timezone = (isset($timezone)) ? $timezone: -5;
							echo EventsHtml::buildTimeZoneSelect($timezone, '');
						?>
					</label>
				</fieldset>
				<?php
					$repeating = $this->event->parseRepeatingRule();
					$freqs = array(
						''        => '- None &mdash;',
						'daily'   => 'Daily',
						'weekly'  => 'Weekly',
						'monthly' => 'Monthy',
						'yearly'  => 'Yearly'
					);
				?>
				<fieldset class="reccurance">
					<legend>
						<?php echo JText::_('Repeating Settings'); ?>
					</legend>
					<label><?php echo JText::_('Recurrence:'); ?> <span class="optional">Optional</span>
						<select name="reccurance[freq]" class="event_recurrence_freq">
							<?php foreach ($freqs as $k => $v) : ?>
								<?php $sel = ($repeating['freq'] == $k) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
							<?php endforeach; ?>
						</select>
					</label>

					<div class="reccurance-options options-daily">
						<label for=""><?php echo JText::_('Repeat Every:'); ?><br />
							<select name="reccurance[interval][daily]" class="daily-days event_recurrence_interval">
								<?php for ($i=1, $n=31; $i < $n; $i++) : ?>
									<?php $sel = ($repeating['freq'] == 'daily' && $repeating['interval'] == $i) ? 'selected="selected' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
							days
						</label>
					</div>

					<div class="reccurance-options options-weekly">
						<label for=""><?php echo JText::_('Repeat Every:'); ?><br />
							<select name="reccurance[interval][weekly]" class="weekly-weeks event_recurrence_interval">
								<?php for ($i=1, $n=31; $i < $n; $i++) : ?>
									<?php $sel = ($repeating['freq'] == 'weekly' && $repeating['interval'] == $i) ? 'selected="selected' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
							weeks
						</label>
					</div>

					<div class="reccurance-options options-monthly">
						<label for=""><?php echo JText::_('Repeat Every:'); ?><br />
							<select name="reccurance[interval][monthly]" class="monthly-months event_recurrence_interval">
								<?php for ($i=1, $n=31; $i < $n; $i++) : ?>
									<?php $sel = ($repeating['freq'] == 'monthly' && $repeating['interval'] == $i) ? 'selected="selected' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
							months
						</label>
					</div>

					<div class="reccurance-options options-yearly">
						<label for=""><?php echo JText::_('Repeat Every:'); ?><br />
							<select name="reccurance[interval][yearly]" class="yearly-years event_recurrence_interval">
								<?php for ($i=1, $n=31; $i < $n; $i++) : ?>
								<?php $sel = ($repeating['freq'] == 'yearly' && $repeating['interval'] == $i) ? 'selected="selected' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
							years
						</label>
					</div>

					<label for="ends" class="ends"><?php echo JText::_('Ends:'); ?> <span class="optional">Optional</span>
						<label for="never">
							<input id="never" class="option" type="radio" name="reccurance[ends][when]" value="never" <?php if ($repeating['end'] == 'never') { echo 'checked="checked"'; } ?> /> Never
						</label>
						<label for="after">
							<input id="after" class="option" type="radio" name="reccurance[ends][when]" value="count"  <?php if ($repeating['end'] == 'count') { echo 'checked="checked"'; } ?> /> After
							<input type="text" name="reccurance[ends][count]" placeholder="5" class="after-input event_recurrence_end_count" value="<?php echo $repeating['count']; ?>" /> times
						</label>
						<label for="on">
							<input id="on" class="option" type="radio" name="reccurance[ends][when]" value="until"  <?php if ($repeating['end'] == 'until') { echo 'checked="checked"'; } ?> /> On
							<input type="text" name="reccurance[ends][until]" placeholder="mm/dd/yyyy" class="on-input event_recurrence_end_date no-legacy-placeholder-support" value="<?php echo $repeating['until']; ?>" />
						</label>
					</label>
				</fieldset>
			</fieldset>

			<?php if ($this->params->get('allow_registrations', 1) && $this->authorized == 'manager') : ?>
				<fieldset>
					<legend><?php echo JText::_('Registration Settings'); ?></legend>

					<label id="include-registration-toggle">
						<?php $ckd = (($this->event->get('registerby') != '0000-00-00 00:00:00' && $this->event->get('registerby') != '') || $includeRegistration) ? 'checked="checked"' : ''; ?>
						<input class="option" type="checkbox" id="include-registration" name="include-registration" value="1" <?php echo $ckd; ?> />
						<?php echo JText::_('Include registration for this event.'); ?>
					</label>

					<div id="registration-fields" class="<?php if ($ckd == '') { echo ' hide'; } ?>">
						<label><?php echo JText::_('Deadline:'); ?> <span class="required">Required for Registration Tab to Appear</span>
							<?php
								$register_by = '';
								if ($this->event->get('registerby') != '' && $this->event->get('registerby') != '0000-00-00 00:00:00')
								{
									$register_by = JHTML::_('date', $this->event->get('registerby'), 'm/d/Y @ g:i a');
								}
							?>
							<input type="text" name="event[registerby]" id="event_registerby" value="<?php echo $register_by; ?>" placeholder="mm/dd/yyyy @ h:mm am/pm" class="no-legacy-placeholder-support" />
							<span class="hint"><?php echo JText::_('Deadlines are on Eastern Standard Time (EST).'); ?></span>
						</label>

						<label><?php echo JText::_('Event Admin Email:'); ?> <span class="optional">Optional</span>
							<input type="text" name="event[email]" value="<?php echo $this->event->get('email'); ?>" />
							<span class="hint">
								<?php echo JText::_('A copy of event registrations will get sent to this event\'s admin email address.'); ?>
							</span>
						</label>

						<label><?php echo JText::_('Password:'); ?> <span class="optional">Optional</span>
							<input type="text" name="event[restricted]" value="<?php echo $this->event->get('restricted'); ?>" />
							<span class="hint">
								<?php echo JText::_('If you want registration to be restricted (invite only), enter the password users must enter to gain access to the registration form.') ; ?>
							</span>
						</label>

						<fieldset>
							<legend><?php echo JText::_('Registration Fields'); ?></legend>
							<?php echo $this->registrationFields->render(); ?>
						</fieldset>
					</div>
				</fieldset>
			<?php endif; ?>

			<input type="hidden" name="option" value="com_groups" />
			<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="calendar" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="event[id]" value="<?php echo $this->event->get('id'); ?>" />
			<br class="clear" />
			<p class="submit">
				<input type="submit" name="event_submit" value="<?php echo $submitBtn; ?>" />
			</p>
		</form>
	</div><!-- /.span9 -->

	<?php if ($showImport) : ?>
		<div class="col span3 omega">
			<form name="importevent" action="index.php" method="post" id="hubForm" enctype="multipart/form-data">
				<fieldset>
					<legend><?php echo JText::_('Import Event'); ?></legend>

					<div class="upload">
						<span class="title"><?php echo JText::_('Upload Event'); ?></span>
						<span class="description"><?php echo JText::_('Drag &amp; Drop an Event File Here to Upload'); ?></span>
						<span class="button-container">
							<span class="btn"><?php echo JText::_('or, Select Event'); ?>
								<input
									type="file"
									name="import"
									id="import"
									data-url="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=import'); ?>" />
							</span>
						</span>
					</div>
				</fieldset>
			</form>
		</div>
	<?php endif; ?>
</div><!-- /.grid -->
