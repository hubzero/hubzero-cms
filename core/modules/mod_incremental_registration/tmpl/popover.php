<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>

<div id="overlay"></div>
<div id="questions">
	<?php echo $introText; ?>
	<?php if ($awardPer): ?>
		<p>We'll award you with <strong><?php echo $awardPer; ?></strong> points for each question you answer. You can use these points towards items in the site <a href="/store">store</a>, or to place bounties on <a href="/answers">questions</a> and <a href="/wishlist">wishes</a>.</p>
	<?php endif; ?>
	<form action="<?php echo Request::current(); ?>" method="post">
		<input type="hidden" name="option" value="<?php echo Request::getCmd('option'); ?>" />
		<ol>
				<?php if (isset($row['orgtype'])): ?>
				<li>
					<label for="orgtype">Which item best describes your organizational affiliation? </label>
					<div class="indented">
					<?php if (isset($errors['orgtype'])): ?>
						<p class="warning">Please select your organizational affiliation</p>
					<?php endif; ?>
					<select id="orgtype" name="orgtype">
						<?php $val = Request::getString('orgtype', '', 'post'); ?>
						<option selected="selected" value="">(select from list)</option>
						<?php
						$dbh->setQuery("SELECT o.value, o.label FROM `#__user_profile_options` AS o INNER JOIN `#__user_profile_fields` AS f ON f.id=o.field_id WHERE f.name='orgtype' ORDER BY o.label ASC");
						foreach ($dbh->loadObjectList() as $opt)
						{
							echo '<option value="'.$opt->value.'"'.($val === $opt->value ? ' selected="selected"' : '').'>'.$opt->label.'</option>';
						}
						?>
					</select>
					</div>
				</li>
				<?php endif; ?>
				<?php if (isset($row['organization'])): ?>
				<li>
					<label for="org">Which organization are you affiliated with? </label><br />
					<div class="indented">
					<?php if (isset($errors['organization'])): ?>
						<p class="warning">Please select an organization or enter one in the provided "other" field</p>
					<?php endif; ?>
					<select id="org" name="org">
						<?php $val = Request::getString('organization', '', 'post'); ?>
						<option value="">(select from list or enter other)</option>
						<?php
						$dbh->setQuery("SELECT o.value, o.label FROM `#__user_profile_options` AS o INNER JOIN `#__user_profile_fields` AS f ON f.id=o.field_id WHERE f.name='organization' ORDER BY o.label ASC");
						foreach ($dbh->loadObjectList() as $opt)
						{
							echo '<option value="'.$opt->value.'"'.($val === $opt->value ? ' selected="selected"' : '').'>'.$opt->label.'</option>';
						}
						?>
					</select>
					<br />
					<label for="org-other">Not listed? Enter your organization here: </label><br />
					<input id="org-other" type="text" name="org-other" value="<?php echo isset($_POST['org-other']) ? str_replace('"', '&quot;', $_POST['org-other']) : ''; ?>" />
					</div>
				</li>
				<?php endif; ?>
				<?php if (isset($row['reason'])): ?>
				<li>
					<label for="reason">What is the primary purpose of your account? </label>
					<div class="indented">
					<?php if (isset($errors['reason'])): ?>
						<p class="warning">Please select the reason for your account or enter one in the provided "other" field</p>
					<?php endif; ?>
					<select id="reason" name="reason">
						<?php $val = Request::getString('reason', '', 'post'); ?>
						<option value="">(select from list or enter other)</option>
						<?php
						$dbh->setQuery("SELECT o.value, o.label FROM `#__user_profile_options` AS o INNER JOIN `#__user_profile_fields` AS f ON f.id=o.field_id WHERE f.name='reason' ORDER BY o.label ASC");
						foreach ($dbh->loadObjectList() as $opt)
						{
							echo '<option value="'.$opt->value.'"'.($val === $opt->value ? ' selected="selected"' : '').'>'.$opt->label.'</option>';
						}
						?>
					</select>
					<br />
					<label for="reason-other">Have a different reason? </label><br />
					<input id="reason-other" type="text" name="reason-other" value="<?php echo isset($_POST['reason-other']) ? str_replace('"', '&quot;', $_POST['reason-other']) : ''; ?>" />
					</div>
				</li>
				<?php endif; ?>
				<?php if (isset($row['name'])): ?>
				<li>
					<label for="name">What is your name?</label>
					<?php if (isset($errors['name'])): ?>
						<p class="warning">Please enter your name</p>
					<?php endif; ?>
					<ol id="name-inp">
						<li>
							<label for="inc_name-first">
								<span>First:</span>
								<input type="text" id="inc_name-first" value="<?php if (isset($_POST['name']['first'])) { echo str_replace('"', '&quot;', $_POST['name']['first']);} ?>" name="name[first]">
							</label>
						</li>
						<li>
							<label for="inc_name-middle">
								<span>Middle:</span>
								<input type="text" id="inc_name-middle" value="<?php if (isset($_POST['name']['middle'])) { echo str_replace('"', '&quot;', $_POST['name']['middle']);} ?>" name="name[middle]">
							</label>
						</li>
						<li>
							<label for="inc_name-last">
								<span>Last:</span>
								<input type="text" id="inc_name-last" value="<?php if (isset($_POST['name']['last'])) { echo str_replace('"', '&quot;', $_POST['name']['last']);} ?>" name="name[last]">
							</label>
					</li>
					</ol>
				</li>
				<?php endif; ?>
				<?php if (isset($row['gender'])): ?>
					<li>
						<label for="gender">What is your gender?</label>
						<div class="indented">
							<?php if (isset($errors['gender'])): ?>
								<p class="warning">Please select your gender, or choose not to reveal it</p>
							<?php endif; ?>
							<?php $gender = Request::getString('gender', '', 'post'); ?>
							<label><input <?php if ($gender == 'male') { echo 'checked="checked" ';} ?>type="radio" class="option" value="male" name="gender"> Male</label>
							<label><input <?php if ($gender == 'female') { echo 'checked="checked" ';} ?>type="radio" class="option" value="female" name="gender"> Female</label>
							<label><input <?php if ($gender == 'refused') { echo 'checked="checked" ';} ?>type="radio" class="option" value="refused" name="gender"> Do not wish to reveal</label>
						</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['url'])): ?>
					<li>
						<label for="url">What is your web site address?</label>
						<div class="indented">
							<?php if (isset($errors['url'])): ?>
								<p class="warning">Please enter your web site URL</p>
							<?php endif; ?>
							<input type="text" id="url" name="url" value="<?php echo $this->escape(Request::getString('url', '', 'post')); ?>" />
						</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['phone'])): ?>
					<li>
						<label for="phone">What is your phone number?</label>
						<div class="indented">
							<?php if (isset($errors['phone'])): ?>
								<p class="warning">Please enter your phone number</p>
							<?php endif; ?>
							<input type="text" id="phone" name="phone" value="<?php echo $this->escape(Request::getString('phone', '', 'post')); ?>" />
						</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['countryorigin'])): ?>
					<li>
						<?php $country = isset($_POST['countryorigin']) ? $_POST['countryorigin'] : \Hubzero\Geocode\Geocode::ipcountry(Request::ip()); ?>
							<label>Are you a Legal Citizen or Permanent Resident of the <abbr title="United States">US</abbr>?</label>
							<div class="indented">
								<?php if (isset($errors['countryorigin'])): ?>
									<p class="warning">Please select your country of origin</p>
								<?php endif; ?>
								<label><input type="radio" class="option" name="countryorigin_us" id="corigin_usyes" value="yes" <?php if (strtolower($country) == 'us' || (isset($_POST['countryorigin_us']) && $_POST['countryorigin_us'] == 'yes')) { echo 'checked="checked"';} ?> />Yes</label>
								<label><input type="radio" class="option" name="countryorigin_us" id="corigin_usno" value="no" <?php if (!empty($_POST['countryorigin']) && strtolower($country) != 'us' || (isset($_POST['countryorigin_us']) && $_POST['countryorigin_us'] == 'no')) { echo 'checked="checked"';} ?> />No</label>
							</div>

							<div class="indented">
								<label for="countryorigin">If not, please select your country of origin</label>
								<select name="countryorigin" id="countryorigin">
									<option value="">Select country...</option>
									<?php
									$countries = \Hubzero\Geocode\Geocode::countries();

									foreach ($countries as $c)
									{
										echo '<option value="' . $c->code . '"';
										if (strtoupper($country) == strtoupper($c->code))
										{
											echo ' selected="selected"';
										}
										echo '>' . htmlentities($c->name, ENT_COMPAT, 'UTF-8') . '</option>'."\n";
									}
									?>
								</select>
							</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['countryresident'])): ?>
					<li>
						<?php $country = isset($_POST['countryresident']) ? $_POST['countryresident'] : \Hubzero\Geocode\Geocode::ipcountry(Request::ip()); ?>
							<label>Do you currently live in the <abbr title="United States">US</abbr>?</label>
							<div class="indented">
								<?php if (isset($errors['countryresident'])): ?>
									<p class="warning">Please select your country of residency</p>
								<?php endif; ?>
								<label><input type="radio" class="option" name="countryresident_us" id="cores_usyes" value="yes" <?php if (strtolower($country) == 'us' || (isset($_POST['countryresident_us']) && $_POST['countryresident_us'] == 'yes')) { echo 'checked="checked"';} ?> />Yes</label>
								<label><input type="radio" class="option" name="countryresident_us" id="cores_usno" value="no" <?php if (!empty($_POST['countryresident']) && strtolower($country) != 'us' || (isset($_POST['countryresident_us']) && $_POST['countryresident_us'] == 'no')) { echo 'checked="checked"';} ?> />No</label>
							</div>

							<div class="indented">
								<label for="countryresident">If not, please select the country where you currently reside</label>
								<select name="countryresident" id="countryresident">
									<option value="">Select country...</option>
									<?php
									// Make sure service provider is on
									$countries = \Hubzero\Geocode\Geocode::countries();

									foreach ($countries as $c)
									{
										echo '<option value="' . $c->code . '"';
										if (strtoupper($country) == strtoupper($c->code))
										{
											echo ' selected="selected"';
										}
										echo '>' . htmlentities($c->name, ENT_COMPAT, 'UTF-8') . '</option>'."\n";
									}
									?>
								</select>
							</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['race'])): ?>
					<?php $race = isset($_POST['race']) ? $_POST['race'] : array(); ?>
					<li>
							<label>If you are a U.S. Citizens or Permanent Residents (<a class="popup 675x678" href="<?php echo Route::url('index.php?option=com_members&controller=register&task=raceethnic'); ?>">more information</a>), select your race(s) below</label>
							<?php if (isset($errors['race'])): ?>
								<p class="warning">Please select your race(s)</p>
							<?php endif; ?>
							<div class="indented">
								<label><input type="checkbox" class="option" name="race[]" id="racenativeamerican" value="nativeamerican" <?php if (in_array('nativeamerican', $race)) { echo 'checked="checked" ';} ?>/>American Indian or Alaska Native</label>
								<div class="indented">
									<label class="indent">
										Tribal Affiliation(s)
										<input name="racenativetribe" id="racenativetribe" type="text" value="<?php if (isset($_POST['racenativetribe'])) { echo str_replace('"', '&quot;', $_POST['racenativetribe']);} ?>" />
									</label>
								</div>

								<label><input type="checkbox" class="option" name="race[]" id="raceasian" value="asian" <?php if (in_array('asian', $race)) { echo 'checked="checked" ';} ?> />Asian</label>

								<label><input type="checkbox" class="option" name="race[]" id="raceblack" value="black" <?php if (in_array('black', $race)) { echo 'checked="checked" ';} ?> />Black or African American</label>
								<label><input type="checkbox" class="option" name="race[]" id="racehawaiian" value="hawaiian" <?php if (in_array('hawaiian', $race)) { echo 'checked="checked" ';} ?> />Native Hawaiian or Other Pacific Islander</label>
								<label><input type="checkbox" class="option" name="race[]" id="racewhite" value="white" <?php if (in_array('white', $race)) { echo 'checked="checked" ';} ?> />White</label>
								<label><input type="checkbox" class="option" name="race[]" id="racerefused" value="refused" <?php if (in_array('refused', $race)) { echo 'checked="checked" ';} ?> />Do not wish to reveal</label>
					</li>
				<?php endif; ?>
				<?php if (isset($row['disability'])): ?>
					<li>
							<label>Do you have any disabilities or impairments?</label>
							<?php if (isset($errors['disability'])): ?>
								<p class="warning">Please make a selection, or choose to refuse to answer</p>
							<?php endif; ?>
							<div class="indented">
								<label><input <?php if (isset($_POST['disability']) && $_POST['disability'] == 'yes') { echo 'checked="checked" ';} ?>type="radio" value="yes" id="disabilityyes" name="disability" class="option"> Yes</label>
								<fieldset class="indented">
									<label><input type="checkbox" id="disabilityblind" name="specificDisability[]" value="visually impaired" class="option"> Blind / Visually Impaired</label><br />
									<label><input type="checkbox" id="disabilitydeaf" name="specificDisability[]" value="hard of hearing" class="option"> Deaf / Hard of Hearing</label><br />
									<label><input type="checkbox" id="disabilityphysical" name="specificDisability[]" value="physical disability" class="option"> Physical / Orthopedic Disability</label><br />
									<label><input type="checkbox" id="disabilitylearning" name="specificDisability[]" value="cognitive disability" class="option"> Learning / Cognitive Disability</label><br />
									<label><input type="checkbox" id="disabilityvocal" name="specificDisability[]" value="speech disability" class="option"> Vocal / Speech Disability</label><br />
									<label>Other (please specify): <input type="text" value="" id="disabilityother" name="otherDisability"></label>
								</fieldset>
								<label><input <?php if (isset($_POST['disability']) && $_POST['disability'] == 'no') { echo 'checked="checked" ';} ?>type="radio" value="no" id="disabilityno" name="disability" class="option"> No (none)</label><br />
								<label><input <?php if (isset($_POST['disability']) && $_POST['disability'] == 'refused') { echo 'checked="checked" ';} ?>type="radio" value="refused" id="disabilityrefused" name="disability" class="option"> Do not wish to reveal</label>
							</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['mailPreferenceOption'])): ?>
					<li>
						<?php if (isset($errors['mailPreferenceOption'])): ?>
							<p class="warning">Please make a selection.</p>
						<?php endif; ?>
						<label for="mailPreferenceOption">Would you like to receive email updates (newsletters, etc.)?</label>
						<div class="indented">
							<select size="3" name="mailPreferenceOption">
								<option value="-1" selected="selected">- Select email option &mdash;</option>
								<option value="1">Yes, send me emails</option>
								<option value="0">No, don't send me emails</option>
							</select>
						</div>
					</li>
				<?php endif; ?>
				<?php if (isset($row['location'])): ?>
					<li>
						<?php if (isset($errors['location'])): ?>
							<p class="warning">Please enter a postal code.</p>
						<?php endif; ?>
						<label for="location">What is your postal code?</label>
						<p class="indented"><input id="location" name="location" value="<?php echo isset($_POST['location']) ? str_replace('"', '&quot;', $_POST['location']) : '' ?>" /></p>
					</li>
				<?php endif; ?>
			</ol>
		<p>
			<input type="hidden" name="incremental-registration" value="update" />
			<button type="submit" name="submit" value="submit" type="submit">Submit</button>
			<button type="submit" name="submit" value="opt-out" type="submit">Ask me later</button>
		</p>
	</form>
</div>
