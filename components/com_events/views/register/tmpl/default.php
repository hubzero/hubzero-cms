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

$paramsClass = 'JParameter';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$paramsClass = 'JRegistry';
}

$params = new $paramsClass( $this->event->params );
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<?php if ($this->authorized) { ?>
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=add'); ?>"><?php echo JText::_('EVENTS_ADD_EVENT'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<?php } ?>

	<ul class="sub-menu">
		<li<?php if ($this->task == 'year') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?></span></a></li>
		<li<?php if ($this->task == 'month') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?></span></a></li>
		<li<?php if ($this->task == 'week') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day.'&task=week'); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></span></a></li>
		<li<?php if ($this->task == 'day') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_DAY'); ?></span></a></li>
	</ul>

<div class="main section noaside">
	<h3><?php echo stripslashes($this->event->title); ?></h3>
<?php
		$html  = '<div id="sub-sub-menu">'."\n";
		$html .= '<ul>'."\n";
		$html .= "\t".'<li';
		if ($this->page->alias == '') {
			$html .= ' class="active"';
		}
		$html .= '><a class="tab" href="'. JRoute::_('index.php?option='.$this->option.'&task=details&id='.$this->event->id) .'"><span>'.JText::_('EVENTS_OVERVIEW').'</span></a></li>'."\n";
		if ($this->pages) {
			foreach ($this->pages as $p)
			{
				$html .= "\t".'<li';
				if ($this->page->alias == $p->alias) {
					$html .= ' class="active"';
				}
				$html .= '><a class="tab" href="'. JRoute::_('index.php?option='.$this->option.'&task=details&id='.$this->event->id.'&page='.$p->alias) .'"><span>'.trim(stripslashes($p->title)).'</span></a></li>'."\n";
			}
		}
		$html .= "\t".'<li';
		if ($this->page->alias == 'register') {
			$html .= ' class="active"';
		}
		$html .= '><a class="tab" href="'. JRoute::_('index.php?option='.$this->option.'&task=details&id='.$this->event->id.'&page=register') .'"><span>'.JText::_('EVENTS_REGISTER').'</span></a></li>'."\n";
		$html .= '</ul>'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '</div>'."\n";
		echo $html;
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form method="post" action="index.php" id="hubForm">
		<div class="explaination">
			<p><strong>For Information Contact</strong></p>
			<?php
			if (trim($this->event->contact_info)) {
				echo stripslashes($this->event->contact_info);
			} else {
				echo '<p>No contact information provided.</p>'."\n";
			}
			?>
		</div>
		<fieldset>
			<legend>Name &amp; Title</legend>
			<div class="group">
				<label>First Name <span class="required">required</span>
				<input type="text" name="register[firstname]" value="<?php echo (isset($this->register['firstname'])) ? $this->register['firstname'] : ''; ?>" /></label>

				<label>Last Name <span class="required">required</span>
				<input type="text" name="register[lastname]" value="<?php echo (isset($this->register['lastname'])) ? $this->register['lastname'] : ''; ?>" /></label>
			</div>
			<div class="group">
				<?php if ($params->get('show_affiliation')) { ?>
					<label>Affiliation <span class="required">required</span>
					<input type="text" name="register[affiliation]" value="<?php echo (isset($this->register['affiliation'])) ? $this->register['affiliation'] : ''; ?>" /></label>
				<?php } ?>
				<?php if ($params->get('show_title')) { ?>
					<label>Title
					<input type="text" name="register[title]" value="<?php echo (isset($this->register['title'])) ? $this->register['title'] : ''; ?>" /></label>
				<?php } ?>
			</div>

			<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="process" />
		</fieldset>
	<?php if ($params->get('show_address')
			|| $params->get('show_telephone')
			|| $params->get('show_fax')
			|| $params->get('show_email')
			|| $params->get('show_website')) { ?>
		<fieldset>
			<legend>Contact Information</legend>
			<?php if ($params->get('show_address')) { ?>
			<div class="group">
				<label>City
				<input type="text" name="register[city]" value="<?php echo (isset($this->register['city'])) ? $this->register['city'] : ''; ?>" /></label>

				<label>State/Province
				<input type="text" name="register[state]" value="<?php echo (isset($this->register['state'])) ? $this->register['state'] : ''; ?>" /></label>
			</div>
			<div class="group">
				<label>Zip/Postal code
				<input type="text" name="register[postalcode]" value="<?php echo (isset($this->register['postalcode'])) ? $this->register['postalcode'] : ''; ?>" /></label>

				<label>Country
				<input type="text" name="register[country]" value="<?php echo (isset($this->register['country'])) ? $this->register['country'] : ''; ?>" /></label>
			</div>
			<?php } ?>
			<div class="group">
				<?php if ($params->get('show_telephone')) { ?>
				<label>Telephone
				<input type="text" name="register[telephone]" value="<?php echo (isset($this->register['telephone'])) ? $this->register['telephone'] : ''; ?>" /></label>
				<?php } ?>
				<?php if ($params->get('show_fax')) { ?>
				<label>Fax
				<input type="text" name="register[fax]" value="<?php echo (isset($this->register['fax'])) ? $this->register['fax'] : ''; ?>" /></label>
				<?php } ?>
			</div>
			<div class="group">
				<?php if ($params->get('show_email')) { ?>
				<label>E-mail <span class="required">required</span>
				<input type="text" name="register[email]" value="<?php echo (isset($this->register['email'])) ? $this->register['email'] : ''; ?>" /></label>
				<?php } ?>
				<?php if ($params->get('show_website')) { ?>
				<label>Website
				<input type="text" name="register[website]" value="<?php echo (isset($this->register['website'])) ? $this->register['website'] : ''; ?>" /></label>
				<?php } ?>
			</div>
		</fieldset>
	<?php } ?>
	<?php if ($params->get('show_position')
			|| $params->get('show_degree')
			|| $params->get('show_gender')
			|| $params->get('show_race')) { ?>
		<fieldset>
			<legend>Demographics</legend>

			<?php if ($params->get('show_position')) { ?>
			<label>
				Which best describes your current position? 
				<select name="register[position]">
					<option value="" selected="selected">(select from list or enter below)</option>
					<option value="university">University / College Student or Staff</option>
					<option value="precollege">K-12 (Pre-College) Student or Staff</option>
					<option value="nationallab">National Laboratory</option>
					<option value="industry">Industry / Private Company</option>
					<option value="government">Government Agency</option>
					<option value="military">Military</option>
					<option value="unemployed">Retired / Unemployed</option>
				</select>
				<input name="register[position_other]" type="text" value="<?php echo (isset($this->register['position_other'])) ? $this->register['position_other'] : ''; ?>" />
			</label>
			<?php } ?>

			<?php if ($params->get('show_degree')) { ?>
			<fieldset>
				<legend>Highest academic degree earned:</legend>
				<label><input type="radio" class="option" name="register[degree]" value="bachelors" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'bachelors') ? 'checked="checked"': ''; ?> /> Bachelors degree</label>
				<label><input type="radio" class="option" name="register[degree]" value="masters" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'masters') ? 'checked="checked"': ''; ?> /> Masters degree</label>
				<label><input type="radio" class="option" name="register[degree]" value="doctoral" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'doctoral') ? 'checked="checked"': ''; ?> /> Doctoral degree</label>
				<label><input type="radio" class="option" name="register[degree]" value="none of the above" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'none of the above') ? 'checked="checked"': ''; ?> /> None of the above</label>
			</fieldset>
			<?php } ?>

			<?php if ($params->get('show_gender')) { ?>
			<fieldset>
				<legend>Gender:</legend>
				<label><input type="radio" name="register[sex]" value="male" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'male') ? 'checked="checked"': ''; ?> /> Male</label>
				<label><input type="radio" name="register[sex]" value="female" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'female') ? 'checked="checked"': ''; ?> /> Female</label>
				<label><input type="radio" name="register[sex]" value="refused" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'refused') ? 'checked="checked"': ''; ?> /> Do not wish to reveal</label>
			</fieldset>
			<?php } ?>

			<?php if ($params->get('show_race')) { ?>
			<fieldset>
				<legend>Race:</legend>
				<p class="hint">Select one or more that apply.</p>
				<label><input type="checkbox" class="option" name="race[nativeamerican]" id="racenativeamerican" value="nativeamerican" /> American Indian or Alaska Native</label>
				<label class="indent">Tribal Affiliation(s):
				<input name="race[nativetribe]" id="racenativetribe" type="text" value="" /></label>
				<label><input type="checkbox" class="option" name="race[asian]" id="raceasian" /> Asian</label>
				<label><input type="checkbox" class="option" name="race[black]" id="raceblack" /> Black or African American</label>
				<label><input type="checkbox" class="option" name="race[hawaiian]" id="racehawaiian" /> Native Hawaiian or Other Pacific Islander</label>
				<label><input type="checkbox" class="option" name="race[white]" id="racewhite" /> White</label>
				<label><input type="checkbox" class="option" name="race[hispanic]" id="racehispanic" /> Hispanic or Latino</label>
				<label><input type="checkbox" class="option" name="race[refused]" id="racerefused" /> Do not wish to reveal</label>
			</fieldset>
			<?php } ?>
		</fieldset>
	<?php } ?>
	<?php if ($params->get('show_arrival') || $params->get('show_departure')) { ?>
		<fieldset>
			<legend>Arrival/Departure</legend>

			<?php if ($params->get('show_arrival')) { ?>
			<fieldset>
				<legend>Arrival Information</legend>

				<label>Arrival Day
				<input type="text" name="arrival[day]" value="<?php echo (isset($this->arrival['day'])) ? $this->arrival['day'] : ''; ?>" /></label>

				<label>Arrival Time
				<input type="text" name="arrival[time]" value="<?php echo (isset($this->arrival['time'])) ? $this->arrival['time'] : ''; ?>" /></label>
			</fieldset>
			<?php } ?>

			<?php if ($params->get('show_departure')) { ?>
			<fieldset>
				<legend>Departure Information</legend>

				<label>Departure Day
				<input type="text" name="departure[day]" value="<?php echo (isset($this->departure['day'])) ? $this->departure['day'] : ''; ?>" /></label>

				<label>Departure Time
				<input type="text" name="departure[time]" value="<?php echo (isset($this->departure['time'])) ? $this->departure['time'] : ''; ?>" /></label>
			</fieldset>
			<?php } ?>
		</fieldset>
	<?php } ?>
	<?php if ($params->get('show_disability') || $params->get('show_dietary')) { ?>
		<fieldset>
			<legend>Disability/Dietary needs</legend>
			<?php if ($params->get('show_disability')) { ?>
			<label><input type="checkbox" class="option" name="disability" value="yes" /> I have auxiliary aids or services due to a disability. Please contact me.</label>
			<?php } ?>

			<?php if ($params->get('show_dietary')) { ?>
			<label><input type="checkbox" class="option" name="dietary[needs]" value="yes" /> I have specific dietary needs.</label>
			<label class="indent">
				Please specify
				<input type="text" name="dietary[specific]" />
			</label>
			<?php } ?>
		</fieldset>
	<?php } ?>
	<?php if ($params->get('show_dinner')) { ?>
		<fieldset>
			<legend>Dinner</legend>

			<label for="filed-dinner"><input type="checkbox" class="option" name="dinner" id="filed-dinner" value="yes" /> I plan to attend the dinner.</label>
		</fieldset>
	<?php } ?>

		<!-- <fieldset>
			<h3>Break Out Session</h3>
			<p>Please indicate which Break Out Session you would like to attend (please choose 3): <span class="required">required</span></p>
			<label><input type="checkbox" class="option" name="bos[]" value="Computational Research Tools" /> Computational Research Tools</label>
			<label><input type="checkbox" class="option" name="bos[]" value="Computational Learning Tools" /> Computational Learning Tools</label>
			<label><input type="checkbox" class="option" name="bos[]" value="Community Wiki" /> Community Wiki</label>
			<label><input type="checkbox" class="option" name="bos[]" value="Online Lectures and Tutorials" /> Online Lectures and Tutorials</label>
			<label><input type="checkbox" class="option" name="bos[]" value="Experimental Properties and Databases" /> Experimental Properties and Databases</label>
			<label><input type="checkbox" class="option" name="bos[]" value="Industrial Partnerships" /> Industrial Partnerships</label>
			<label><input type="checkbox" class="option" name="bos[]" value="International Partnerships" /> International Partnerships</label>
		</fieldset> -->
		<?php if ($params->get('show_abstract')) { ?>
		<fieldset>
			<legend>Abstract</legend>
			<label>
				<?php 
				if ($params->get('abstract_text')) {
					echo stripslashes($params->get('abstract_text'));
				}
				?>
				<textarea name="register[additional]" rows="16" cols="32"></textarea>
			</label>
		</fieldset>
		<?php } ?>

		<?php if ($params->get('show_comments')) { ?>
		<fieldset>
			<legend>Comments</legend>
			<label>
				Please use the space below to provide any additional comments:
				<textarea name="register[comments]" rows="4" cols="32"></textarea>
			</label>
		</fieldset>
		<?php } ?>
		<div class="clear"></div>
		<p class="submit"><input type="submit" value="<?php echo JText::_('EVENTS_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->
