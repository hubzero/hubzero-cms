<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$params = new \Hubzero\Config\Registry($this->event->params);

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->authorized) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last"><a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=add'); ?>"><?php echo Lang::txt('EVENTS_ADD_EVENT'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header><!-- / #content-header -->

<nav>
	<ul class="sub-menu">
		<li<?php if ($this->task == 'year') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_YEAR'); ?></span></a></li>
		<li<?php if ($this->task == 'month') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_MONTH'); ?></span></a></li>
		<li<?php if ($this->task == 'week') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day.'&task=week'); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_WEEK'); ?></span></a></li>
		<li<?php if ($this->task == 'day') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_DAY'); ?></span></a></li>
	</ul>
</nav>

<section class="main section">
	<h3><?php echo stripslashes($this->event->title); ?></h3>
	<?php
		$html  = '<div id="sub-sub-menu">'."\n";
		$html .= '<ul>'."\n";
		$html .= "\t".'<li';
		if ($this->page->alias == '') {
			$html .= ' class="active"';
		}
		$html .= '><a class="tab" href="'. Route::url('index.php?option='.$this->option.'&task=details&id='.$this->event->id) .'"><span>'.Lang::txt('EVENTS_OVERVIEW').'</span></a></li>'."\n";
		if ($this->pages) {
			foreach ($this->pages as $p)
			{
				$html .= "\t".'<li';
				if ($this->page->alias == $p->alias) {
					$html .= ' class="active"';
				}
				$html .= '><a class="tab" href="'. Route::url('index.php?option='.$this->option.'&task=details&id='.$this->event->id.'&page='.$p->alias) .'"><span>'.trim(stripslashes($p->title)).'</span></a></li>'."\n";
			}
		}
		$html .= "\t".'<li';
		if ($this->page->alias == 'register') {
			$html .= ' class="active"';
		}
		$html .= '><a class="tab" href="'. Route::url('index.php?option='.$this->option.'&task=details&id='.$this->event->id.'&page=register') .'"><span>'.Lang::txt('EVENTS_REGISTER').'</span></a></li>'."\n";
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
			<p><strong><?php echo Lang::txt('COM_EVENTS_REGISTER_EXPLAINATION'); ?></strong></p>
			<?php
			if (trim($this->event->contact_info)) {
				echo stripslashes($this->event->contact_info);
			} else {
				echo '<p>' . Lang::txt('COM_EVENTS_REGISTER_EXPLAINATION_NO_EXPLAINATION') . '</p>'."\n";
			}
			?>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_NAME'); ?></legend>
			<div class="grid">
				<div class="col span6">
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_FIRST_NAME'); ?> <span class="required"><?php echo Lang::txt('COM_EVENTS_REQUIRED'); ?></span>
					<input type="text" name="register[firstname]" value="<?php echo (isset($this->register['firstname'])) ? $this->register['firstname'] : ''; ?>" /></label>
				</div>
				<div class="col span6 omega">
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_LAST_NAME'); ?> <span class="required"><?php echo Lang::txt('COM_EVENTS_REQUIRED'); ?></span>
					<input type="text" name="register[lastname]" value="<?php echo (isset($this->register['lastname'])) ? $this->register['lastname'] : ''; ?>" /></label>
				</div>
			</div>
			<div class="grid">
				<div class="col span6">
				<?php if ($params->get('show_affiliation')) { ?>
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_AFFILIATION'); ?> <span class="required"><?php echo Lang::txt('COM_EVENTS_REQUIRED'); ?></span>
					<input type="text" name="register[affiliation]" value="<?php echo (isset($this->register['affiliation'])) ? $this->register['affiliation'] : ''; ?>" /></label>
				<?php } ?>
				</div>
				<div class="col span6 omega">
				<?php if ($params->get('show_title')) { ?>
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_TITLE'); ?>
					<input type="text" name="register[title]" value="<?php echo (isset($this->register['title'])) ? $this->register['title'] : ''; ?>" /></label>
				<?php } ?>
				</div>
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
			<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_INFO'); ?></legend>
			<?php if ($params->get('show_address')) { ?>
			<div class="grid">
				<div class="col span6">
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_CITY'); ?>
					<input type="text" name="register[city]" value="<?php echo (isset($this->register['city'])) ? $this->register['city'] : ''; ?>" /></label>
				</div>
				<div class="col span6 omega">
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_STATE'); ?>
					<input type="text" name="register[state]" value="<?php echo (isset($this->register['state'])) ? $this->register['state'] : ''; ?>" /></label>
				</div>
			</div>
			<div class="grid">
				<div class="col span6">
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_ZIP'); ?>
					<input type="text" name="register[postalcode]" value="<?php echo (isset($this->register['postalcode'])) ? $this->register['postalcode'] : ''; ?>" /></label>
				</div>
				<div class="col span6 omega">
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_COUNTRY'); ?>
					<input type="text" name="register[country]" value="<?php echo (isset($this->register['country'])) ? $this->register['country'] : ''; ?>" /></label>
				</div>
			</div>
			<?php } ?>
			<div class="grid">
				<div class="col span6">
				<?php if ($params->get('show_telephone')) { ?>
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_PHONE'); ?>
					<input type="text" name="register[telephone]" value="<?php echo (isset($this->register['telephone'])) ? $this->register['telephone'] : ''; ?>" /></label>
				<?php } ?>
				</div>
				<div class="col span6 omega">
				<?php if ($params->get('show_fax')) { ?>
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_FAX'); ?>
					<input type="text" name="register[fax]" value="<?php echo (isset($this->register['fax'])) ? $this->register['fax'] : ''; ?>" /></label>
				<?php } ?>
				</div>
			</div>
			<div class="grid">
				<div class="col span6">
				<?php if ($params->get('show_email')) { ?>
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_EMAIL'); ?> <span class="required"><?php echo Lang::txt('COM_EVENTS_REQUIRED'); ?></span>
					<input type="text" name="register[email]" value="<?php echo (isset($this->register['email'])) ? $this->register['email'] : ''; ?>" /></label>
				<?php } ?>
				</div>
				<div class="col span6 omega">
				<?php if ($params->get('show_website')) { ?>
					<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_WEBSITE'); ?>
					<input type="text" name="register[website]" value="<?php echo (isset($this->register['website'])) ? $this->register['website'] : ''; ?>" /></label>
				<?php } ?>
				</div>
			</div>
		</fieldset>
	<?php } ?>
	<?php if ($params->get('show_position')
			|| $params->get('show_degree')
			|| $params->get('show_gender')
			|| $params->get('show_race')) { ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DEMOGRAPHICS'); ?></legend>

			<?php if ($params->get('show_position')) { ?>
			<label>
				<?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION'); ?>
				<select name="register[position]">
					<option value="" selected="selected"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_NULL'); ?></option>
					<option value="university"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_UNIVERSITY'); ?></option>
					<option value="precollege"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_PRECOLLEGE'); ?></option>
					<option value="nationallab"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_NATIONALLAB'); ?></option>
					<option value="industry"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_INDUSTRY'); ?></option>
					<option value="government"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_GOVERNMENT'); ?></option>
					<option value="military"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_MILITARY'); ?></option>
					<option value="unemployed"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_UNEMPLOYED'); ?></option>
				</select>
				<input name="register[position_other]" type="text" value="<?php echo (isset($this->register['position_other'])) ? $this->register['position_other'] : ''; ?>" />
			</label>
			<?php } ?>

			<?php if ($params->get('show_degree')) { ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEGREE'); ?>:</legend>
				<label><input type="radio" class="option" name="register[degree]" value="bachelors" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'bachelors') ? 'checked="checked"': ''; ?> /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_BACHELORS'); ?></label>
				<label><input type="radio" class="option" name="register[degree]" value="masters" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'masters') ? 'checked="checked"': ''; ?> /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_MASTERS'); ?></label>
				<label><input type="radio" class="option" name="register[degree]" value="doctoral" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'doctoral') ? 'checked="checked"': ''; ?> /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_DOCTORAL'); ?></label>
				<label><input type="radio" class="option" name="register[degree]" value="none of the above" <?php echo (isset($this->register['degree']) && $this->register['degree'] == 'none of the above') ? 'checked="checked"': ''; ?> /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_NULL'); ?></label>
			</fieldset>
			<?php } ?>

			<?php if ($params->get('show_gender')) { ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_GENDER'); ?>:</legend>
				<label><input type="radio" name="register[sex]" value="male" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'male') ? 'checked="checked"': ''; ?> /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_MALE'); ?></label>
				<label><input type="radio" name="register[sex]" value="female" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'female') ? 'checked="checked"': ''; ?> /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_FEMALE'); ?></label>
				<label><input type="radio" name="register[sex]" value="refused" class="option" <?php echo (isset($this->register['sex']) && $this->register['sex'] == 'refused') ? 'checked="checked"': ''; ?> /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_NULL'); ?></label>
			</fieldset>
			<?php } ?>

			<?php if ($params->get('show_race')) { ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE'); ?>:</legend>
				<p class="hint"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_HINT'); ?></p>
				<label><input type="checkbox" class="option" name="race[nativeamerican]" id="racenativeamerican" value="nativeamerican" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_AMERICAN'); ?></label>
				<label class="indent"><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_AFFILIATIONS'); ?>:
				<input name="race[nativetribe]" id="racenativetribe" type="text" value="" /></label>
				<label><input type="checkbox" class="option" name="race[asian]" id="raceasian" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_ASIAN'); ?></label>
				<label><input type="checkbox" class="option" name="race[black]" id="raceblack" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_BLACK'); ?></label>
				<label><input type="checkbox" class="option" name="race[hawaiian]" id="racehawaiian" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_HAWAIIAN'); ?></label>
				<label><input type="checkbox" class="option" name="race[white]" id="racewhite" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_WHITE'); ?></label>
				<label><input type="checkbox" class="option" name="race[hispanic]" id="racehispanic" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_HISPANIC'); ?></label>
				<label><input type="checkbox" class="option" name="race[refused]" id="racerefused" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_NULL'); ?></label>
			</fieldset>
			<?php } ?>
		</fieldset>
	<?php } ?>
	<?php if ($params->get('show_arrival') || $params->get('show_departure')) { ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ARRIVAL_OR_DEPARTURE'); ?></legend>

			<?php if ($params->get('show_arrival')) { ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ARRIVAL'); ?></legend>

				<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_ARRIVAL_DAY'); ?>
				<input type="text" name="arrival[day]" value="<?php echo (isset($this->arrival['day'])) ? $this->arrival['day'] : ''; ?>" /></label>

				<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_ARRIVAL_TIME'); ?>
				<input type="text" name="arrival[time]" value="<?php echo (isset($this->arrival['time'])) ? $this->arrival['time'] : ''; ?>" /></label>
			</fieldset>
			<?php } ?>

			<?php if ($params->get('show_departure')) { ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DEPARTURE'); ?></legend>

				<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEPARTURE_DAY'); ?>
				<input type="text" name="departure[day]" value="<?php echo (isset($this->departure['day'])) ? $this->departure['day'] : ''; ?>" /></label>

				<label><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DEPARTURE_TIME'); ?>
				<input type="text" name="departure[time]" value="<?php echo (isset($this->departure['time'])) ? $this->departure['time'] : ''; ?>" /></label>
			</fieldset>
			<?php } ?>
		</fieldset>
	<?php } ?>
	<?php if ($params->get('show_disability') || $params->get('show_dietary')) { ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DISABILITY'); ?></legend>
			<?php if ($params->get('show_disability')) { ?>
			<label><input type="checkbox" class="option" name="disability" value="yes" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DISABILTIY'); ?></label>
			<?php } ?>

			<?php if ($params->get('show_dietary')) { ?>
			<label><input type="checkbox" class="option" name="dietary[needs]" value="yes" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DIETARY'); ?></label>
			<label class="indent">
				<?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DIETARY_DETAILS'); ?>
				<input type="text" name="dietary[specific]" />
			</label>
			<?php } ?>
		</fieldset>
	<?php } ?>
	<?php if ($params->get('show_dinner')) { ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DINNER'); ?></legend>

			<label for="filed-dinner"><input type="checkbox" class="option" name="dinner" id="filed-dinner" value="yes" /> <?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_DINNER'); ?></label>
		</fieldset>
	<?php } ?>

		<?php if ($params->get('show_abstract')) { ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ABSTRACT'); ?></legend>
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
			<legend><?php echo Lang::txt('COM_EVENTS_REGISTER_FIELDSET_COMMENTS'); ?></legend>
			<label>
				<?php echo Lang::txt('COM_EVENTS_REGISTER_FIELD_COMMENTS'); ?>:
				<textarea name="register[comments]" rows="4" cols="32"></textarea>
			</label>
		</fieldset>
		<?php } ?>
		<div class="clear"></div>
		<p class="submit"><input type="submit" value="<?php echo Lang::txt('EVENTS_SUBMIT'); ?>" /></p>
	</form>
</section><!-- / .main section -->
