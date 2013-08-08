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

$year  = date("Y", strtotime($this->event->publish_up));
$month = date("m", strtotime($this->event->publish_up));
?>

<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-date btn date" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$year.'&month='.$month); ?>">
			<?php echo JText::_('Back to Calendar'); ?>
		</a>
	</li>
</ul>

<div class="event-title-bar">
	<span class="event-title">
		<?php echo $this->event->title; ?>
	</span>
	<?php if ($this->juser->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
		<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=delete&event_id='.$this->event->id); ?>">
			Delete
		</a> 
		<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=edit&event_id='.$this->event->id); ?>">
			Edit
		</a>
	<?php endif; ?>
</div>

<div class="event-sub-menu">
	<ul>
		<li>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=details&event_id='.$this->event->id); ?>">
				<span><?php echo JText::_('Details'); ?></span>
			</a>
		</li>
		<?php if (isset($this->event->registerby) && $this->event->registerby != '' && $this->event->registerby != '0000-00-00 00:00:00') : ?>
			<li class="active">
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=register&event_id='.$this->event->id); ?>">
					<span><?php echo JText::_('Register'); ?></span>
				</a>
			</li>
			<?php if ($this->juser->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=registrants&event_id='.$this->event->id); ?>">
						<span><?php echo JText::_('Registrants ('.$this->registrants.')'); ?></span>
					</a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=register&event_id='.$this->event->id); ?>" id="hubForm" method="post">
	<fieldset>
		<legend><?php echo JText::_('Name &amp; Title'); ?></legend>
		
		<div class="group">
			<label><?php echo JText::_('First Name:'); ?> <span class="required">Required</span>
				<input type="text" name="register[first_name]" value="<?php echo (isset($this->register['first_name'])) ? $this->register['first_name'] : ''; ?>" />
			</label>

			<label><?php echo JText::_('Last Name:'); ?> <span class="required">Required</span>
				<input type="text" name="register[last_name]" value="<?php echo (isset($this->register['last_name'])) ? $this->register['last_name'] : ''; ?>" />
			</label>
		</div>
		
		<?php if ($this->params->get('show_affiliation') || $this->params->get('show_title')) : ?>
			<div class="group">
				<?php if ($this->params->get('show_affiliation')) : ?>
				<label><?php echo JText::_('Affiliation:'); ?> <span class="required">Required</span>
					<input type="text" name="register[affiliation]" value="<?php echo (isset($this->register['affiliation'])) ? $this->register['affiliation'] : ''; ?>" />
				</label>
				<?php endif; ?>
				
				<?php if ($this->params->get('show_title')) : ?>
					<label><?php echo JText::_('Title:'); ?> <span class="optional">Optional</span>
						<input type="text" name="register[title]" value="<?php echo (isset($this->register['title'])) ? $this->register['title'] : ''; ?>" />
					</label>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</fieldset>
	
	<fieldset>
		<legend><?php echo JText::_('Contact Information'); ?></legend>
		<?php if ($this->params->get('show_address')) : ?>
			<div class="group">
				<label><?php echo JText::_('City:'); ?> <span class="optional">Optional</span>
					<input type="text" name="register[city]" value="<?php echo (isset($this->register['city'])) ? $this->register['city'] : ''; ?>" />
				</label>
				
				<label><?php echo JText::_('State/Province:'); ?> <span class="optional">Optional</span>
					<input type="text" name="register[state]" value="<?php echo (isset($this->register['state'])) ? $this->register['state'] : ''; ?>" />
				</label>
			</div>
			<div class="group">
				<label><?php echo JText::_('Zip/Postal code:'); ?> <span class="optional">Optional</span>
					<input type="text" name="register[zip]" value="<?php echo (isset($this->register['zip'])) ? $this->register['zip'] : ''; ?>" />
				</label>
				<label><?php echo JText::_('Country:'); ?> <span class="optional">Optional</span>
					<input type="text" name="register[country]" value="<?php echo (isset($this->register['country'])) ? $this->register['country'] : ''; ?>" />
				</label>
			</div>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_telephone') || $this->params->get('show_fax')) : ?>
			<div class="group">
				<?php if ($this->params->get('show_telephone')) : ?>
					<label><?php echo JText::_('Telephone:'); ?> <span class="optional">Optional</span>
						<input type="text" name="register[telephone]" value="<?php echo (isset($this->register['telephone'])) ? $this->register['telephone'] : ''; ?>" />
					</label>
				<?php endif; ?>
				<?php if ($this->params->get('show_fax')) : ?>
					<label><?php echo JText::_('Fax:'); ?> <span class="optional">Optional</span>
						<input type="text" name="register[fax]" value="<?php echo (isset($this->register['fax'])) ? $this->register['fax'] : ''; ?>" />
					</label>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_email') || $this->params->get('show_website')) : ?>
			<div class="group">
				<?php if ($this->params->get('show_email')) : ?>
					<label><?php echo JText::_('E-mail:'); ?> <span class="required">required</span>
						<input type="text" name="register[email]" value="<?php echo (isset($this->register['email'])) ? $this->register['email'] : ''; ?>" />
					</label>
				<?php endif; ?>
				<?php if ($this->params->get('show_website')) : ?>
					<label><?php echo JText::_('Website:'); ?> <span class="optional">Optional</span>
						<input type="text" name="register[website]" value="<?php echo (isset($this->register['website'])) ? $this->register['website'] : ''; ?>" />
					</label>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</fieldset>
	
	<?php if ($this->params->get('show_position') || $this->params->get('show_degree') || $this->params->get('show_gender') || $this->params->get('show_race')) : ?>
		<fieldset>
			<legend><?php echo JText::_('Demographics'); ?></legend>
			<?php if ($this->params->get('show_position')) : ?>
				<label for="register[position]">
					<?php echo JText::_('Which best describes your current position?'); ?> <span class="optional">Optional</span>
					<select name="register[position]">
						<option value="" selected="selected"><?php echo JText::_('(select from list or enter below)'); ?></option>
						<option value="university"><?php echo JText::_('University / College Student or Staff'); ?></option>
						<option value="precollege"><?php echo JText::_('K-12 (Pre-College) Student or Staff'); ?></option>
						<option value="nationallab"><?php echo JText::_('National Laboratory'); ?></option>
						<option value="industry"><?php echo JText::_('Industry / Private Company'); ?></option>
						<option value="government"><?php echo JText::_('Government Agency'); ?></option>
						<option value="military"><?php echo JText::_('Military'); ?></option>
						<option value="unemployed"><?php echo JText::_('Retired / Unemployed'); ?></option>
					</select>
					<input name="register[position_other]" type="text" value="<?php echo (isset($this->register['position_other'])) ? $this->register['position_other'] : ''; ?>" />
				</label>
			<?php endif; ?>
			
			<?php if ($this->params->get('show_degree')) : ?>
				<fieldset>
					<legend><?php echo JText::_('Highest academic degree earned:'); ?> <span class="optional">Optional</span></legend>
						<label>
							<input type="radio" class="option" name="register[degree]" value="Bachelors" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'Bachelors') ? 'checked="checked"': ''; ?> />
							<?php echo JText::_('Bachelors degree'); ?>
						</label>
						<label>
							<input type="radio" class="option" name="register[degree]" value="Masters" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'Masters') ? 'checked="checked"': ''; ?> /> 
							<?php echo JText::_('Masters degree'); ?>
						</label>
						<label>
							<input type="radio" class="option" name="register[degree]" value="Doctoral" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'Doctoral') ? 'checked="checked"': ''; ?> /> 
							<?php echo JText::_('Doctoral degree'); ?>
						</label>
						<label>
							<input type="radio" class="option" name="register[degree]" value="Other" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'Other') ? 'checked="checked"': ''; ?> />
							<?php echo JText::_('None of the above'); ?>
						</label>
				</fieldset>
			<?php endif; ?>

			<?php if ($this->params->get('show_gender')) : ?>
				<fieldset>
					<legend><?php echo JText::_('Gender:'); ?> <span class="optional">Optional</span></legend>
					<label>
						<input type="radio" name="register[sex]" value="Male" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'Male') ? 'checked="checked"': ''; ?> /> 
						<?php echo JText::_('Male'); ?>
					</label>
					<label>
						<input type="radio" name="register[sex]" value="Female" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'Female') ? 'checked="checked"': ''; ?> /> 
						<?php echo JText::_('Female'); ?>
					</label>
					<label>
						<input type="radio" name="register[sex]" value="Refused" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'Refused') ? 'checked="checked"': ''; ?> /> 
						<?php echo JText::_('Do not wish to reveal'); ?>
					</label>
				</fieldset>
			<?php endif; ?>

			<?php if ($this->params->get('show_race')) : ?>
				<fieldset>
					<legend><?php echo JText::_('Race:'); ?> <span class="optional">Optional</span></legend>
					<p class="hint">
						<?php echo JText::_('Select one or more that apply.'); ?>
					</p>
					<label>
						<input type="checkbox" class="option" name="race[nativeamerican]" id="racenativeamerican" value="Native American" /> 
						<?php echo JText::_('American Indian or Alaska Native'); ?>
					</label>
					<label class="indent"><?php echo JText::_('Tribal Affiliation(s):'); ?>
						<input name="race[nativetribe]" id="racenativetribe" type="text" value="" />
					</label>
					<label>
						<input type="checkbox" class="option" name="race[asian]" id="raceasian" value="Asian" /> 
						<?php echo JText::_('Asian'); ?>
					</label>
					<label>
						<input type="checkbox" class="option" name="race[black]" id="raceblack" value="African American" />
						<?php echo JText::_('Black or African American'); ?>
					</label>
					<label>
						<input type="checkbox" class="option" name="race[hawaiian]" id="racehawaiian" value="Hawaiian" />
						<?php echo JText::_('Native Hawaiian or Other Pacific Islander'); ?>
					</label>
					<label>
						<input type="checkbox" class="option" name="race[white]" id="racewhite" value="White" />
						<?php echo JText::_('White'); ?>
					</label>
					<label>
						<input type="checkbox" class="option" name="race[hispanic]" id="racehispanic" value="Hispanic" />
						<?php echo JText::_('Hispanic or Latino'); ?>
					</label>
					<label>
						<input type="checkbox" class="option" name="race[refused]" id="racerefused" value="Refused" />
						<?php echo JText::_('Do not wish to reveal'); ?>
					</label>
				</fieldset>
			<?php endif; ?>
		</fieldset>
	<?php endif; ?>
	
	<?php if ($this->params->get('show_arrival') || $this->params->get('show_departure')) : ?>
		<fieldset>
			<legend><?php echo JText::_('Arrival/Departure'); ?></legend>
			
			<?php if ($this->params->get('show_arrival')) : ?>
				<fieldset>
					<legend><?php echo JText::_('Arrival Information:'); ?> <span class="optional">Optional</span></legend>
					<label><?php echo JText::_('Arrival Day'); ?>
						<input type="text" name="arrival[day]" value="<?php echo (isset($this->arrival['day'])) ? $this->arrival['day'] : ''; ?>" />
					</label>
					<label><?php echo JText::_('Arrival Time'); ?>
						<input type="text" name="arrival[time]" value="<?php echo (isset($this->arrival['time'])) ? $this->arrival['time'] : ''; ?>" />
					</label>
				</fieldset>
			<?php endif ?>

			<?php if ($this->params->get('show_departure')) : ?>
			<fieldset>
				<legend><?php echo JText::_('Departure Information:'); ?> <span class="optional">Optional</span></legend>
				<label><?php echo JText::_('Departure Day'); ?>
					<input type="text" name="departure[day]" value="<?php echo (isset($this->departure['day'])) ? $this->departure['day'] : ''; ?>" />
				</label>
				<label><?php echo JText::_('Departure Time'); ?>
					<input type="text" name="departure[time]" value="<?php echo (isset($this->departure['time'])) ? $this->departure['time'] : ''; ?>" />
				</label>
			</fieldset>
			<?php endif; ?>
		</fieldset>
	<?php endif; ?>
	
	<?php if ($this->params->get('show_disability') || $this->params->get('show_dietary')) : ?>
		<fieldset>
			<legend><?php echo JText::_('Disability/Dietary needs'); ?></legend>
			<?php if ($this->params->get('show_disability')) : ?>
				<label>
					<input type="checkbox" class="option" name="disability" value="yes" <?php if(isset($this->disability) && $this->disability == 'yes') { echo 'checked="checked"'; } ?> /> 
					<?php echo JText::_('I have auxiliary aids or services due to a disability. Please contact me.'); ?>
				</label>
			<?php endif; ?>

			<?php if ($this->params->get('show_dietary')) : ?>
				<label>
					<input type="checkbox" class="option" name="dietary[needs]" value="yes" <?php if(isset($this->dietary['needs']) && $this->dietary['needs'] == 'yes') { echo 'checked="checked"'; } ?> /> 
					<?php echo JText::_('I have specific dietary needs.'); ?>
				</label>
				<label class="indent"><?php echo JText::_('Please specify'); ?>
					<input type="text" name="dietary[specific]" value="<?php echo $this->dietary['specific']; ?>" />
				</label>
			<?php endif; ?>
		</fieldset>
	<?php endif; ?>
	
	<?php if ($this->params->get('show_dinner')) : ?>
		<fieldset>
			<legend><?php echo JText::_('Dinner'); ?></legend>
			<label for="filed-dinner">
				<input type="checkbox" class="option" name="dinner" id="filed-dinner" value="yes" <?php if(isset($this->dinner) && $this->dinner == 'yes') { echo 'checked="checked"'; } ?> /> 
				<?php echo JText::_('I plan to attend the dinner.'); ?>
			</label>
		</fieldset>
	<?php endif; ?>
	
	<?php if ($this->params->get('show_abstract')) : ?>
		<fieldset>
			<legend><?php echo JText::_('Abstract'); ?></legend>
			<label>
				<?php 
					if ($this->params->get('abstract_text')) 
					{
						echo stripslashes($this->params->get('abstract_text'));
					}
				?>
				<textarea name="register[abstract]" rows="16" cols="32"><?php echo (isset($this->register['abstract'])) ? $this->register['abstract'] : ''; ?></textarea>
			</label>
		</fieldset>
	<?php endif; ?>

	<?php if ($this->params->get('show_comments')) : ?>
		<fieldset>
			<legend><?php echo JText::_('Comments'); ?></legend>
			<label>
				<?php echo JText::_('Please use the space below to provide any additional comments:'); ?>
				<textarea name="register[comment]" rows="4" cols="32"><?php echo (isset($this->register['comment'])) ? $this->register['comment'] : ''; ?></textarea>
			</label>
		</fieldset>
	<?php endif; ?>
	
	<input type="hidden" name="option" value="com_groups" />
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="calendar" />
	<input type="hidden" name="action" value="doregister" />
	<input type="hidden" name="event_id" value="<?php echo $this->event->id; ?>" />
	
	<p class="submit">
		<input type="submit" name="event_submit" value="Submit" />
	</p>
</form>