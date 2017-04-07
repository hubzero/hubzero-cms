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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//include registration
$includeRegistration = Request::getVar('includeRegistration', 0);

//set button and form title
$formTitle = Lang::txt('Add Group Event');
$submitBtn = Lang::txt('Submit New Event');
if ($this->event->get('id'))
{
	$formTitle = Lang::txt('Edit Group Event');
	$submitBtn = Lang::txt('Update Event');
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
		<?php if (Request::getVar('action') == 'edit') : ?>
			<a class="icon-prev btn back" title="" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=details&event_id='.$this->event->get('id')); ?>">
				<?php echo Lang::txt('Back to Event'); ?>
			</a>
		<?php else : ?>
			<a class="icon-prev btn back" title="" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$this->year.'&month='.$this->month); ?>">
				<?php echo Lang::txt('Back to Events Calendar'); ?>
			</a>
		<?php endif; ?>
	</li>
</ul>

<div class="grid">
	<div class="col <?php echo ($showImport) ? 'span9' : 'span12'; ?>">
		<form name="editevent" action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar'); ?>" method="post" id="hubForm" class="full">
			<fieldset>
				<legend><?php echo $formTitle; ?></legend>

				<label for="event_title">
					<?php echo Lang::txt('Title:'); ?> <span class="required">Required</span>
					<input type="text" name="event[title]" id="event_title" value="<?php echo $this->escape($this->event->get('title')); ?>" />
				</label>

				<?php if (count($this->calendars) > 0 || $this->authorized == 'manager') : ?>
					<label for="event-calendar-picker">
						<?php echo Lang::txt('Calendar:'); ?> <span class="optional">Optional</span>
						<select name="event[calendar_id]" id="event-calendar-picker">
							<option value=""><?php echo Lang::txt('&mdash; Select Calendar for Event &mdash;'); ?></option>
							<?php $colors = array('red','orange','yellow','green','blue','purple','brown'); ?>
							<?php foreach ($this->calendars as $calendar) : ?>
								<?php
								if (!in_array($calendar->get('color'), $colors))
								{
									$calendar->set('color', '');
								}
								$sel = ($calendar->get('id') == $this->event->get('calendar_id')) ? 'selected="selected"' : '';
								?>
								<option <?php echo $sel; ?> data-img="<?php echo Request::base(true); ?>/core/plugins/groups/calendar/assets/img/swatch-<?php echo ($calendar->get('color')) ? $calendar->get('color') : 'gray'; ?>.png" value="<?php echo $calendar->get('id'); ?>"><?php echo $calendar->get('title'); ?></option>
							<?php endforeach; ?>
						</select>

						<?php if ($this->authorized == 'manager') : ?>
							<span class="hint">
								<?php echo Lang::txt('Need a new calendar?'); ?> <a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=addcalendar'); ?>">Click here!</a>
							</span>
						<?php endif; ?>
					</label>
				<?php endif; ?>

				<label for="event_content">
					<?php echo Lang::txt('Details:'); ?> <span class="optional">Optional</span>
					<textarea name="content" id="event_content" rows="10"><?php echo $this->escape($this->event->get('content')); ?></textarea>
					<span class="hint"><?php echo Lang::txt('Limited HTML allowed (a, iframe, strong, em, u)'); ?></span>
				</label>

				<label for="event_location">
					<?php echo Lang::txt('Location:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[adresse_info]" id="event_location" value="<?php echo $this->escape($this->event->get('adresse_info')); ?>" />
				</label>

				<label for="event-contact_info">
					<?php echo Lang::txt('Contact:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[contact_info]" id="event-contact_info" value="<?php echo $this->escape($this->event->get('contact_info')); ?>" />
					<span class="hint"><?php echo Lang::txt('Accepts names and email addresses. (ex. John Doe john_doe@domain.com)'); ?></span>
				</label>

				<label for="event_website">
					<?php echo Lang::txt('Website:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[extra_info]" id="event_website" value="<?php echo $this->escape($this->event->get('extra_info')); ?>" />
				</label>

				<fieldset>
					<legend><?php echo Lang::txt('Date &amp; Time Settings'); ?></legend>

					<label for="event_start">
						<?php echo Lang::txt('Start:'); ?> <span class="required">Required</span>
						<?php
							$start           = Request::getVar('start', '', 'get');
							$publish_up      = ($this->event->get('publish_up')) ? $this->event->get('publish_up') : $start;
							$publish_up_date = '';
							$publish_up_time = '';
							if ($publish_up != '' && $publish_up != '0000-00-00 00:00:00')
							{
								$publish_up_date = Components\Events\Models\EventDate::of($publish_up)->toTimezone($this->timezone,'m/d/Y');
								$publish_up_time = Components\Events\Models\EventDate::of($publish_up)->toTimezone($this->timezone,'g:i a');
							}
						?>
						<div class="input-group">
							<input type="text" name="event[publish_up]" id="event_start_date" value="<?php echo $this->escape($publish_up_date); ?>" placeholder="mm/dd/yyyy" class="no-legacy-placeholder-support" />
							<input type="text" name="event[publish_up_time]" id="event_start_time" value="<?php echo $this->escape($publish_up_time); ?>" placeholder="h:mm am/pm" class="no-legacy-placeholder-support" />
						</div>
					</label>

					<label for="event_end">
						<?php echo Lang::txt('End:'); ?> <span class="optional">Optional</span>
						<?php
							$end               = Request::getVar('end', '', 'get');
							$publish_down      = ($this->event->get('publish_down')) ? $this->event->get('publish_down') : $end;
							$publish_down_date = '';
							$publish_down_time = '';
							if ($publish_down != '' && $publish_down != '0000-00-00 00:00:00')
							{
								$publish_down_date = Components\Events\Models\EventDate::of($publish_down)->toTimezone($this->timezone,'m/d/Y');
								$publish_down_time = Components\Events\Models\EventDate::of($publish_down)->toTimezone($this->timezone,'g:i a');
							}
						?>
						<div class="input-group">
							<input type="text" name="event[publish_down]" id="event_end_date" value="<?php echo $this->escape($publish_down_date); ?>" placeholder="mm/dd/yyyy" class="no-legacy-placeholder-support" />
							<input type="text" name="event[publish_down_time]" id="event_end_time" value="<?php echo $this->escape($publish_down_time); ?>" placeholder="h:mm am/pm" class="no-legacy-placeholder-support" />
						</div>
					</label>

					<label for="event_allday">
						<input type="hidden" name="event[allday]" value="0" />
						<input class="option" type="checkbox" id="event_allday" name="event[allday]" value="1" <?php if ($this->event->get('allday')) { echo 'checked="checked"'; } ?> /> <?php echo Lang::txt('All day event'); ?>
						<span class="hint"><?php echo Lang::txt(' - can span multiple days'); ?></span>
					</label>
				</fieldset>
				<fieldset>
					<legend>
						<?php echo Lang::txt('Timezone Settings'); ?>
					</legend>
					<label>
						<?php echo Lang::txt('Timezone:'); ?> <span class="optional">Optional</span>
						<?php
							echo \Components\Events\Helpers\Html::buildTimeZoneSelect($this->timezone, '');
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
						<?php echo Lang::txt('Repeating Settings'); ?>
					</legend>

					<label>
						<?php echo Lang::txt('Recurrence:'); ?> <span class="optional">Optional</span>
						<select name="reccurance[freq]" class="event_recurrence_freq">
							<?php foreach ($freqs as $k => $v) : ?>
								<?php $sel = ($repeating['freq'] == $k) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
							<?php endforeach; ?>
						</select>
					</label>

					<div class="reccurance-options options-daily">
						<label>
							<?php echo Lang::txt('Repeat Every:'); ?><br />
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
						<label>
							<?php echo Lang::txt('Repeat Every:'); ?><br />
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
						<label>
							<?php echo Lang::txt('Repeat Every:'); ?><br />
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
						<label>
							<?php echo Lang::txt('Repeat Every:'); ?><br />
							<select name="reccurance[interval][yearly]" class="yearly-years event_recurrence_interval">
								<?php for ($i=1, $n=31; $i < $n; $i++) : ?>
								<?php $sel = ($repeating['freq'] == 'yearly' && $repeating['interval'] == $i) ? 'selected="selected' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
							years
						</label>
					</div>

					<label for="ends" class="ends"><?php echo Lang::txt('Ends:'); ?> <span class="optional">Optional</span>
						<label for="never">
							<input id="never" class="option" type="radio" name="reccurance[ends][when]" value="never" <?php if ($repeating['end'] == 'never') { echo 'checked="checked"'; } ?> /> Never
						</label>
						<label for="after">
							<input id="after" class="option" type="radio" name="reccurance[ends][when]" value="count"  <?php if ($repeating['end'] == 'count') { echo 'checked="checked"'; } ?> /> After
							<input type="text" name="reccurance[ends][count]" placeholder="x" class="after-input event_recurrence_end_count" value="<?php echo $repeating['count']; ?>" /> occurrences 
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
					<legend><?php echo Lang::txt('Registration Settings'); ?></legend>

					<label id="include-registration-toggle">
						<?php $ckd = (($this->event->get('registerby') != '0000-00-00 00:00:00' && $this->event->get('registerby') != '') || $includeRegistration) ? 'checked="checked"' : ''; ?>
						<input class="option" type="checkbox" id="include-registration" name="include-registration" value="1" <?php echo $ckd; ?> />
						<?php echo Lang::txt('Include registration for this event.'); ?>
					</label>

					<div id="registration-fields" class="<?php if ($ckd == '') { echo ' hide'; } ?>">
						<label for="event_registerby">
							<?php echo Lang::txt('Deadline:'); ?> <span class="required"><?php echo Lang::txt('Required for Registration Tab to Appear'); ?></span>
							<?php
								$register_by = '';
								if ($this->event->get('registerby') != '' && $this->event->get('registerby') != '0000-00-00 00:00:00')
								{
									$register_by = Date::of($this->event->get('registerby'))->toLocal('m/d/Y @ g:i a');
								}
							?>
							<input type="text" name="event[registerby]" id="event_registerby" value="<?php echo $this->escape($register_by); ?>" placeholder="mm/dd/yyyy @ h:mm am/pm" class="no-legacy-placeholder-support" />
							<span class="hint"><?php echo Lang::txt('Deadlines are on Eastern Standard Time (EST).'); ?></span>
						</label>

						<label>
							<?php echo Lang::txt('Event Admin Email:'); ?> <span class="optional">Optional</span>
							<input type="text" name="event[email]" value="<?php echo $this->escape($this->event->get('email')); ?>" />
							<span class="hint">
								<?php echo Lang::txt('A copy of event registrations will get sent to this event\'s admin email address.'); ?>
							</span>
						</label>

						<label>
							<?php echo Lang::txt('Password:'); ?> <span class="optional">Optional</span>
							<input type="text" name="event[restricted]" value="<?php echo $this->escape($this->event->get('restricted')); ?>" />
							<span class="hint">
								<?php echo Lang::txt('If you want registration to be restricted (invite only), enter the password users must enter to gain access to the registration form.'); ?>
							</span>
						</label>

						<fieldset>
							<legend><?php echo Lang::txt('Registration Fields'); ?></legend>
							<?php echo $this->registrationFields->render(); ?>
						</fieldset>
					</div>
				</fieldset>
			<?php endif; ?>

			<input type="hidden" name="option" value="com_groups" />
			<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
			<input type="hidden" name="active" value="calendar" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="event[id]" value="<?php echo $this->event->get('id'); ?>" />
			<?php echo Html::input('token'); ?>

			<br class="clear" />
			<p class="submit">
				<input type="submit" name="event_submit" value="<?php echo $submitBtn; ?>" />
			</p>
		</form>
	</div><!-- /.span9 -->

	<?php if ($showImport) : ?>
		<div class="col span3 omega">
			<form name="importevent" action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar'); ?>" method="post" id="hubForm" enctype="multipart/form-data">
				<fieldset>
					<legend><?php echo Lang::txt('Import Event'); ?></legend>

					<div class="upload">
						<span class="title"><?php echo Lang::txt('Upload Event'); ?></span>
						<span class="description"><?php echo Lang::txt('Drag &amp; Drop an Event File Here to Upload'); ?></span>
						<span class="button-container">
							<span class="btn"><?php echo Lang::txt('or, Select Event'); ?>
								<input
									type="file"
									name="import"
									id="import"
									data-url="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=import'); ?>" />
							</span>
						</span>
					</div>
				</fieldset>
			</form>
		</div>
	<?php endif; ?>
</div><!-- /.grid -->
