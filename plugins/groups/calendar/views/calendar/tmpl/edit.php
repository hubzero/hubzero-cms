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
if($this->event->id)
{
	$formTitle = JText::_('Edit Group Event');
	$submitBtn = JText::_('Update Event');
}

$showImport = false;
if ($this->params->get('allow_import', 1) && (!isset($this->event->id) || $this->event->id == 0))
{
	$showImport = true;
}
?>

<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<?php if (JRequest::getVar('action') == 'edit') : ?>
			<a class="icon-prev btn back" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=details&event_id='.$this->event->id); ?>">
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
		<form name="editevent" action="index.php" method="post" id="hubForm">
			<fieldset>
				<legend><?php echo $formTitle; ?></legend>

				<label><?php echo JText::_('Title:'); ?> <span class="required">Required</span>
					<input type="text" name="event[title]" id="event_title" value="<?php echo $this->event->title; ?>" />
				</label>

				<?php if (count($this->calendars) > 0 || $this->authorized == 'manager') : ?>
					<label><?php echo JText::_('Calendar:'); ?> <span class="optional">Optional</span>
						<select name="event[calendar_id]" id="event-calendar-picker">
							<option value=""><?php echo JText::_('- Select Calendar for Event &mdash;'); ?></option>
							<?php foreach ($this->calendars as $calendar) : ?>
								<?php $sel = ($calendar->id == $this->event->calendar_id) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> data-img="/plugins/groups/calendar/images/swatch-<?php echo ($calendar->color) ? $calendar->color : 'gray'; ?>.png" value="<?php echo $calendar->id; ?>"><?php echo $calendar->title; ?></option>
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
					<textarea name="content" id="event_content" rows="10"><?php echo $this->event->content; ?></textarea>
					<span class="hint"><?php echo JText::_('Limited HTML allowed (a, iframe, strong, em, u)'); ?></span>
				</label>

				<label><?php echo JText::_('Location:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[adresse_info]" id="event_location" value="<?php echo $this->event->adresse_info; ?>" />
				</label>

				<label><?php echo JText::_('Contact:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[contact_info]" value="<?php echo $this->event->contact_info; ?>" />
					<span class="hint">Accepts names and email addresses. (ex. John Doe john_doe@domain.com)</span>
				</label>

				<label><?php echo JText::_('Website:'); ?> <span class="optional">Optional</span>
					<input type="text" name="event[extra_info]" id="event_website" value="<?php echo $this->event->extra_info; ?>" />
				</label>

				<fieldset>
					<legend>
						<?php echo JText::_('Date &amp; Time Settings'); ?>
					</legend>
					<label><?php echo JText::_('Start:'); ?> <span class="required">Required</span>
						<?php
							$start = JRequest::getVar('start', date('Y-m-d 08:00:00'), 'get');
							$publish_up = ($this->event->publish_up) ? $this->event->publish_up : $start; 
							$publish_up = date("m/d/Y @ g:i a", strtotime($publish_up));
						?>
						<input type="text" name="event[publish_up]" id="event_start_date" value="<?php echo $publish_up; ?>" placeholder="mm/dd/yyyy @ h:mm am/pm" class="no-legacy-placeholder-support" />
					</label>

					<label><?php echo JText::_('End:'); ?> <span class="optional">Optional</span>
						<?php
							$publish_down = '';
							if (isset($this->event->publish_down) && $this->event->publish_down != '' && $this->event->publish_down != '0000-00-00 00:00:00')
							{
								$publish_down = date("m/d/Y @ g:i a", strtotime($this->event->publish_down));
							}
						?>
						<input type="text" name="event[publish_down]" id="event_end_date" value="<?php echo $publish_down; ?>" placeholder="mm/dd/yyyy @ h:mm am/pm" class="no-legacy-placeholder-support" />
					</label> 

					<label><?php echo JText::_('Timezone:'); ?> <span class="optional">Optional</span>
						<?php
							$timezone = ($this->event->time_zone) ? $this->event->time_zone : -5;
							echo EventsHtml::buildTimeZoneSelect($timezone, ''); 
						?>
					</label>
				</fieldset>
			</fieldset>

			<?php if ($this->params->get('allow_registrations', 1) && $this->authorized == 'manager') : ?>
				<fieldset>
					<legend><?php echo JText::_('Registration Settings'); ?></legend>

					<label id="include-registration-toggle">
						<?php $ckd = (($this->event->registerby != '0000-00-00 00:00:00' && $this->event->registerby != '') || $includeRegistration) ? 'checked="checked"' : ''; ?>
						<input class="option" type="checkbox" id="include-registration" name="include-registration" value="1" <?php echo $ckd; ?> /> 
						<?php echo JText::_('Include registration for this event.'); ?>
					</label>

					<div id="registration-fields" class="<?php if($ckd == '') { echo ' hide'; } ?>">
						<label><?php echo JText::_('Deadline:'); ?> <span class="required">Required for Registration Tab to Appear</span>
							<?php
								$register_by = '';
								if (isset($this->event->registerby) && $this->event->registerby != '' && $this->event->registerby != '0000-00-00 00:00:00')
								{
									$register_by = date("m/d/Y @ g:i a", strtotime($this->event->registerby));
								}
							?>
							<input type="text" name="event[registerby]" id="event_registerby" value="<?php echo $register_by; ?>" placeholder="mm/dd/yyyy @ h:mm am/pm" class="no-legacy-placeholder-support" />
							<span class="hint"><?php echo JText::_('Deadlines are on Eastern Standard Time (EST).'); ?></span>
						</label>

						<label><?php echo JText::_('Event Admin Email:'); ?> <span class="optional">Optional</span>
							<input type="text" name="event[email]" value="<?php echo $this->event->email; ?>" />
							<span class="hint">
								<?php echo JText::_('A copy of event registrations will get sent to this event\'s admin email address.'); ?>
							</span>
						</label>

						<label><?php echo JText::_('Password:'); ?> <span class="optional">Optional</span>
							<input type="text" name="event[restricted]" value="<?php echo $this->event->restricted; ?>" />
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
			<input type="hidden" name="event[id]" value="<?php echo $this->event->id; ?>" />
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
