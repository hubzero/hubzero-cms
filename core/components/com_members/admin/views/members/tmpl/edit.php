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

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . $text, 'user.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

$name = stripslashes($this->profile->get('name'));
$surname = stripslashes($this->profile->get('surname'));
$givenName = stripslashes($this->profile->get('givenName'));
$middleName = stripslashes($this->profile->get('middleName'));

if (!$surname)
{
	$bits = explode(' ', $name);
	$surname = array_pop($bits);
	if (count($bits) >= 1)
	{
		$givenName = array_shift($bits);
	}
	if (count($bits) >= 1)
	{
		$middleName = implode(' ', $bits);
	}
}

Html::behavior('switcher', 'submenu');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', (array)$this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
<div class="col span7">
	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="member-nav">
						<li><a href="#" onclick="return false;" id="profile" class="active"><?php echo Lang::txt('COM_MEMBERS_PROFILE'); ?></a></li>
						<li><a href="#" onclick="return false;" id="demographics"><?php echo Lang::txt('COM_MEMBERS_DEMOGRAPHICS'); ?></a></li>
						<?php if (is_object($this->password)) : ?>
							<li><a href="#" onclick="return false;" id="password"><?php echo Lang::txt('COM_MEMBERS_FIELD_PASSWORD'); ?></a></li>
						<?php endif; ?>
						<li><a href="#" onclick="return false;" id="groups"><?php echo Lang::txt('COM_MEMBERS_GROUPS'); ?></a></li>
						<li><a href="#" onclick="return false;" id="hosts"><?php echo Lang::txt('COM_MEMBERS_HOSTS'); ?></a></li>
						<li><a href="#" onclick="return false;" id="messaging"><?php echo Lang::txt('COM_MEMBERS_MENU_MESSAGING'); ?></a></li>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->
	<div id="member-document">
		<div id="page-profile" class="tab">

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MEMBERS_PROFILE'); ?></span></legend>

			<input type="hidden" name="id" value="<?php echo $this->profile->get('uidNumber'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />

			<div class="grid">
				<div class="col span6">
					<div class="input-wrap">
						<input type="checkbox" name="profile[public]" id="field-public" value="1"<?php if ($this->profile->get('public') == 1) { echo ' checked="checked"'; } ?> />
						<label for="field-public"><?php echo Lang::txt('COM_MEMBERS_FIELD_PUBLIC_PROFILE'); ?></label>
					</div>
				</div>
				<div class="col span6">
					<div class="input-wrap">
						<input type="checkbox" name="profile[vip]" id="field-vip" value="1"<?php if ($this->profile->get('vip') == 1) { echo ' checked="checked"'; } ?> />
						<label for="field-vip"><?php echo Lang::txt('COM_MEMBERS_FIELD_VIP'); ?></label>
					</div>
				</div>
			</div>

			<div class="input-wrap">
				<label for="field-givenName"><?php echo Lang::txt('COM_MEMBERS_FIELD_FIRST_NAME'); ?>:</label><br />
				<input type="text" name="profile[givenName]" id="field-givenName" value="<?php echo $this->escape($givenName); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-middleName"><?php echo Lang::txt('COM_MEMBERS_FIELD_MIDDLE_NAME'); ?>:</label><br />
				<input type="text" name="profile[middleName]" id="field-middleName" value="<?php echo $this->escape($middleName); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-surname"><?php echo Lang::txt('COM_MEMBERS_FIELD_LAST_NAME'); ?>:</label><br />
				<input type="text" name="profile[surname]" id="field-surname" value="<?php echo $this->escape($surname); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-orgtype"><?php echo Lang::txt('COM_MEMBERS_FIELD_EMPLOYMENT_STATUS'); ?>:</label><br />
				<select name="profile[orgtype]" id="field-orgtype">
					<option value=""<?php if (!$this->profile->get('orgtype')) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_MEMBERS_SELECT'); ?></option>
					<?php
					include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'organizationtype.php');

					$database = App::get('db');
					$rot = new \Components\Members\Tables\OrganizationType($database);
					if ($types = $rot->find('list'))
					{
						foreach ($types as $orgtype)
						{
							echo '<option value="' . $this->escape($orgtype->type) . '"';
							if ($this->profile->get('orgtype') == $orgtype->type)
							{
								echo ' selected="selected"';
							}
							echo '>' . $this->escape(stripslashes($orgtype->title)) . '</option>' . "\n";
						}
					}
					?>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-organization"><?php echo Lang::txt('COM_MEMBERS_FIELD_ORGANIZATION'); ?>:</label><br />
				<input type="text" name="profile[organization]" id="field-organization" value="<?php echo $this->escape(stripslashes($this->profile->get('organization'))); ?>" />
			</div>

			<div class="grid">
				<div class="col span6">
					<div class="input-wrap">
						<label for="field-corigin"><?php echo Lang::txt('COM_MEMBERS_PROFILE_CITIZEN'); ?>:</label>
						<select name="profile[countryorigin]" id="field-corigin">
							<?php if (!$this->profile->get('countryorigin') || $this->profile->get('countryorigin') == 'US') { ?>
								<option value=""><?php echo Lang::txt('COM_MEMBERS_PROFILE_FORM_SELECT_FROM_LIST'); ?></option>
							<?php } ?>
							<?php
							$countries = \Hubzero\Geocode\Geocode::countries();
							if ($countries)
							{
								foreach ($countries as $country)
								{
									?>
									<option value="<?php echo $country->code; ?>"<?php if (strtoupper($this->profile->get('countryorigin')) == strtoupper($country->code)) { echo ' selected="selected"'; } ?>><?php echo $this->escape($country->name); ?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="col span6">
					<div class="input-wrap">
						<label for="field-cresident"><?php echo Lang::txt('COM_MEMBERS_PROFILE_RESIDENT'); ?>:</label>
						<select name="profile[countryresident]" id="field-cresident">
							<?php if (!$this->profile->get('countryresident') || strcasecmp($this->profile->get('countryresident'), 'US') == 0) { ?>
								<option value=""><?php echo Lang::txt('COM_MEMBERS_PROFILE_FORM_SELECT_FROM_LIST'); ?></option>
							<?php } ?>
							<?php
							if ($countries)
							{
								foreach ($countries as $country)
								{
									?>
									<option value="<?php echo $country->code; ?>"<?php if (strtoupper($this->profile->get('countryresident')) == strtoupper($country->code)) { echo ' selected="selected"'; } ?>><?php echo $this->escape($country->name); ?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
				</div>
			</div>

			<div class="input-wrap">
				<label for="field-url"><?php echo Lang::txt('COM_MEMBERS_FIELD_ORCID'); ?>:</label><br />
				<input type="text" name="profile[orcid]" id="field-orcid" value="<?php echo $this->escape(stripslashes($this->profile->get('orcid'))); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-url"><?php echo Lang::txt('COM_MEMBERS_FIELD_WEBSITE'); ?>:</label><br />
				<input type="text" name="profile[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->profile->get('url'))); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-phone"><?php echo Lang::txt('COM_MEMBERS_FIELD_TELEPHONE'); ?>:</label><br />
				<input type="text" name="profile[phone]" id="field-phone" value="<?php echo $this->escape($this->profile->get('phone')); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-tags"><?php echo Lang::txt('COM_MEMBERS_FIELD_INTERESTS'); ?>:</label><br />
				<input type="text" name="tags" id="field-tags" value="<?php echo $this->escape($this->tags); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-bio"><?php echo Lang::txt('COM_MEMBERS_FIELD_BIOGRAPHY'); ?>:</label><br />
				<?php
					echo $this->editor('profile[bio]', $this->escape($this->profile->getBio('raw')), 40, 10, 'field-bio');
				?>
			</div>
			<div class="input-wrap">
				<?php
					$options = array(
						'-1' => Lang::txt('COM_MEMBERS_PROFILE_FORM_SELECT_FROM_LIST'),
						'1'  => Lang::txt('JYES'),
						'0'  => Lang::txt('JNO')
					);
				?>
				<label for="field-mailPreferenceOption"><?php echo Lang::txt('COM_MEMBERS_FIELD_MAIL_PREFERENCE'); ?></label>
				<select name="profile[mailPreferenceOption]" id="field-mailPreferenceOption">
					<?php foreach ($options as $key => $value) : ?>
						<?php $sel = ($key == $this->profile->get('mailPreferenceOption')) ? 'selected="selected"' : ''; ?>
						<option <?php echo $sel; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?></span></legend>

			<?php
			if ($this->profile->get('uidNumber') != '') {
				$pics = stripslashes($this->profile->get('picture'));
				$pics = explode(DS, $pics);
				$file = end($pics);
			?>
			<iframe height="350" name="filer" id="filer" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&tmpl=component&file=' . $file . '&id=' . $this->profile->get('uidNumber')); ?>"></iframe>
			<?php
			} else {
				echo '<p class="warning">' . Lang::txt('COM_MEMBERS_PICTURE_ADDED_LATER') . '</p>';
			}
			?>
		</fieldset>

		</div>
		<div id="page-demographics" class="tab">

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MEMBERS_DEMOGRAPHICS'); ?></span></legend>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_FIELD_GENDER'); ?>:</legend>

				<div class="input-wrap">
					<input type="radio" name="profile[gender]" id="gender_male" value="male" <?php echo ($this->profile->get('gender') == 'male') ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_GENDER_MALE'); ?><br />
					<input type="radio" name="profile[gender]" id="gender_female" value="female" <?php echo ($this->profile->get('gender') == 'female') ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_GENDER_FEMALE'); ?><br />
					<input type="radio" name="profile[gender]" id="gender_refused" value="refused" <?php echo ($this->profile->get('gender') == 'refused') ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_GENDER_REFUSED'); ?>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_FIELD_DISABILITY'); ?>:</legend>

				<?php
				$dises = array('no','yes','refused','vocal','blind','deaf','physical','learning');
				$dis = $this->profile->get('disability');
				$disother = '';
				foreach ($dis as $d)
				{
					if (!in_array($d, $dises)) {
						$disother = $d;
					}
				}
				$dis_noanswer = (is_array($dis) && count($dis) <= 1 && empty($dis[0]));

				?>
				<div class="input-wrap">
					<input type="radio" class="option" name="profile[disability]" id="disabilityyes" value="yes" <?php echo (!$dis_noanswer && !in_array('no',$this->profile->get('disability')) && !in_array('refused',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/>
					<label><?php echo Lang::txt('JYES'); ?></label>

					<div class="input-wrap">
						<label><input type="checkbox" class="option" name="profile[disabilities][blind]" id="disabilityblind" value="blind" <?php echo (in_array('blind',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_DISABILITY_BLIND'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][deaf]" id="disabilitydeaf" value="deaf" <?php echo (in_array('deaf',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_DISABILITY_DEAF'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][physical]" id="disabilityphysical" value="physical" <?php echo (in_array('physical',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_DISABILITY_PHYSICAL'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][learning]" id="disabilitylearning" value="learning" <?php echo (in_array('learning',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_DISABILITY_LEARNING'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[disabilities][vocal]" id="disabilityvocal" value="vocal" <?php echo (in_array('vocal',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_DISABILITY_VOCAL'); ?></label><br />
						<label><?php echo Lang::txt('COM_MEMBERS_FIELD_OTHER'); ?>
						<input name="profile[disabilities][other]" id="disabilityother" type="text" value="<?php echo $this->escape($disother); ?>" /></label>
					</div>

					<input type="radio" class="option" name="profile[disability]" id="disabilityno" value="no" <?php echo (in_array('no',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/>
					<label><?php echo Lang::txt('COM_MEMBERS_FIELD_NO_NONE'); ?></label>
					<br />
					<input type="radio" class="option" name="profile[disability]" id="disabilityrefused" value="refused" <?php echo (in_array('refused',$this->profile->get('disability'))) ? 'checked="checked" ' : ''; ?>/>
					<label><?php echo Lang::txt('COM_MEMBERS_FIELD_REFUSED'); ?></label>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_FIELD_RACE'); ?>:</legend>

				<div class="input-wrap">
					<label><input type="checkbox" class="option" name="profile[race][nativeamerican]" id="racenativeamerican" value="nativeamerican" <?php echo (in_array('nativeamerican',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_RACE_NATIVE_AMERICAN'); ?></label><br />
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_MEMBERS_FIELD_RACE_TRIBE'); ?>: <input name="racenativetribe" id="profile[nativeTribe]" type="text" value="<?php echo $this->escape($this->profile->get('nativeTribe')); ?>" /></label><br />
					</div>
					<label><input type="checkbox" class="option" name="profile[race][asian]" id="raceasian" value="asian" <?php echo (in_array('asian',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_RACE_ASIAN'); ?></label><br />
					<label><input type="checkbox" class="option" name="profile[race][black]" id="raceblack" value="black" <?php echo (in_array('black',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_RACE_BLACK'); ?></label><br />
					<label><input type="checkbox" class="option" name="profile[race][hawaiian]" id="racehawaiian" value="hawaiian" <?php echo (in_array('hawaiian',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_RACE_PACIFIC_ISLANDER'); ?></label><br />
					<label><input type="checkbox" class="option" name="profile[race][white]" id="racewhite" value="white" <?php echo (in_array('white',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_RACE_WHITE'); ?></label><br />
					<label><input type="checkbox" class="option" name="profile[race][refused]" id="racerefused" value="refused" <?php echo (in_array('refused',$this->profile->get('race'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_REFUSED'); ?></label>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MEMBERS_FIELD_HISPANIC'); ?>:</legend>

				<div class="input-wrap">
					<?php
					$hises = array('no','yes','refused','cuban','mexican','deaf','puertorican');
					$his = $this->profile->get('disability');
					$hisother = '';
					foreach ($his as $h)
					{
						if (!in_array($h, $hises)) {
							$hisother = $h;
						}
					}
					$his_noanswer = (is_array($his) && count($his) <= 1 && empty($his[0]));
					$hispanic = false;
					if (!empty($his) && !$his_noanswer && !in_array('no',$this->profile->get('hispanic')) && !in_array('refused',$this->profile->get('hispanic'))) {
						$hispanic = true;
					}
					?>
					<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicyes" value="yes"  <?php echo ($hispanic) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_HISPANIC_YES'); ?></label>
					<div class="input-wrap">
						<label><input type="checkbox" class="option" name="profile[hispanics][cuban]" id="hispaniccuban" value="cuban" <?php echo (in_array('cuban',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_HISPANIC_CUBAN'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[hispanics][mexican]" id="hispanicmexican" value="mexican" <?php echo (in_array('mexican',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_HISPANIC_CHICANO'); ?></label><br />
						<label><input type="checkbox" class="option" name="profile[hispanics][puertorican]" id="hispanicpuertorican" value="puertorican" <?php echo (in_array('puertorican',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_HISPANIC_PUERTORICAN'); ?></label><br />
						<label><?php echo Lang::txt('COM_MEMBERS_FIELD_HISPANIC_OTHER'); ?>: <input name="profile[hispanics][other]" id="hispanicother" type="text" value="<?php echo $this->escape($hisother); ?>" /></label>
					</div>
					<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicno" value="no" <?php echo (in_array('no',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_HISPANIC_NO'); ?></label><br />
					<label><input type="radio" class="option" name="profile[hispanic]" id="hispanicrefused" value="refused" <?php echo (in_array('refused',$this->profile->get('hispanic'))) ? 'checked="checked" ' : ''; ?>/> <?php echo Lang::txt('COM_MEMBERS_FIELD_REFUSED'); ?></label>
				</div>
			</fieldset>
		</fieldset>
		</div>

		<?php if (is_object($this->password)) : ?>
		<div id="page-password" class="tab">

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_FIELD_PASSWORD'); ?></span></legend>

				<div class="input-wrap">
					<?php echo Lang::txt('COM_MEMBERS_PASSWORD_CURRENT'); ?>:
					<input type="text" name="profile[currentpassword]" disabled="disabled" <?php echo ($this->profile->get('userPassword')) ? "value=\"{$this->profile->get('userPassword')}\"" : 'placeholder="no local password set"'; ?> />
				</div>
				<div class="input-wrap">
					<label for="newpass"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_NEW'); ?>:</label>
					<input type="password" name="newpass" id="newpass" value="" />
					<p class="warning"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_NEW_WARNING'); ?></p>
					<?php if (count($this->password_rules) > 0) : ?>
						<?php $this->css('password.css'); ?>
						<script type="text/javascript">
							/*jQuery(document).ready(function ( $ )
							{
								var password = $('#newpass'),
								checkPass    = function() {
									// Create an ajax call to check the potential password
									$.ajax({
										url: "/api/members/checkpass",
										type: "POST",
										data: "password1="+password.val(),
										dataType: "html",
										cache: false,
										success: function ( html ) {
											if (html.length > 0 && password.val() != '')
											{
												$('.passrules').html(html);
											}
											else
											{
												// Probably deleted password, so reset classes
												$('.passrules').find('li').removeClass('error passed').addClass('empty');
											}
										}
									});
								};

								password.on('keyup', checkPass);
							});*/
						</script>
						<div><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES'); ?>:</div>
						<ul class="passrules">
							<?php foreach ($this->password_rules as $rule) : ?>
								<?php if (!empty($rule)) : ?>
									<?php if ($this->validated && is_array($this->validated) && in_array($rule, $this->validated)) : ?>
										<li class="pass-error"><?php echo $rule; ?></li>
									<?php elseif ($this->validated) : ?>
										<li class="pass-passed"><?php echo $rule; ?></li>
									<?php else : ?>
										<li class="pass-empty"><?php echo $rule; ?></li>
									<?php endif; ?>
								<?php endif; ?>
							<?php endforeach ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="input-wrap">
					<label title="shadowLastChange"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_LAST_CHANGE'); ?>:</label>
					<?php
						if (is_object($this->password) && $this->password->get('shadowLastChange'))
						{
							$shadowLastChange = $this->password->get('shadowLastChange')*86400;
							echo date("Y-m-d", $shadowLastChange);
							echo " ({$this->password->get('shadowLastChange')})";
							echo " - " . intval((time()/86400) - ($shadowLastChange/86400)) . " days ago";
						}
						else
						{
							echo Lang::txt('COM_MEMBERS_NEVER');
						}
					?>
				</div>
				<div class="input-wrap">
					<label title="shadowMax" class="key"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_MAX'); ?>:</label>
					<input type="text" name="shadowMax" value="<?php echo $this->escape($this->password->get('shadowMax')); ?>" />
				</div>
				<div class="input-wrap">
					<label title="shadowWarning" class="key"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_WARNING'); ?>:</label>
					<input type="text" name="shadowWarning" value="<?php echo $this->escape($this->password->get('shadowWarning')); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE_HINT'); ?>">
					<label title="shadowExpire"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE'); ?>:</label>
					<input type="text" name="shadowExpire" value="<?php echo $this->escape($this->password->get('shadowExpire')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE_HINT'); ?></span>
				</div>
			</fieldset>

		</div>
		<?php endif; ?>
			<div id="page-groups" class="tab">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_MEMBERS_GROUPS'); ?></span></legend>

					<iframe height="500" name="grouper" id="grouper" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=groups&tmpl=component&id=' . $this->profile->get('uidNumber')); ?>"></iframe>
				</fieldset>
			</div>
			<div id="page-hosts" class="tab">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_MEMBERS_HOSTS'); ?></span></legend>

					<iframe height="500" name="hosts" id="hosts-list" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=hosts&tmpl=component&id=' . $this->profile->get('uidNumber')); ?>"></iframe>
				</fieldset>
			</div>
			<div id="page-messaging" class="tab">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_MEMBERS_MENU_MESSAGING'); ?></span></legend>

					<iframe height="500" name="messaging" id="messaging-settings" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=messages&tmpl=component&task=settings&id=' . $this->profile->get('uidNumber')); ?>"></iframe>
				</fieldset>
			</div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="col span5">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_ID'); ?></th>
					<td><?php echo $this->profile->get('uidNumber'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME'); ?></th>
					<td><?php echo $this->profile->get('username'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_CITIZENSHIP'); ?></th>
					<th><?php echo $this->profile->get('countryorigin'); ?></th>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_RESIDENCE'); ?></th>
					<th><?php echo $this->profile->get('countryresident'); ?></th>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_REGHOST'); ?></th>
					<td><?php
						echo ($this->profile->get('regHost')) ? $this->profile->get('regHost').'<br />' : '';
						echo ($this->profile->get('regIP')) ? $this->profile->get('regIP') : '';
					?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_MODIFIED'); ?></th>
					<th><?php echo $this->profile->get('modifiedDate'); ?></th>
				</tr>
				<?php
				$database = App::get('db');
				$database->setQuery("SELECT du.*, d.domain FROM `#__xdomain_users` AS du, `#__xdomains` AS d WHERE du.domain_id=d.domain_id AND du.uidNumber=" . (int) $this->profile->get('uidNumber'));
				$domains = $database->loadObjectList();
				if ($domains) {
					foreach ($domains as $d)
					{
						?>
						<tr>
							<th><?php echo $d->domain; ?></th>
							<td><?php echo $d->domain_username; ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_DOMAINS'); ?></th>
						<td><?php echo Lang::txt('COM_MEMBERS_NONE'); ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="email"><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL'); ?></label>
				<?php
				if ($this->profile->get('emailConfirmed') == 1)
				{
					$confirmed = '<label for="emailConfirmed"><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" checked="checked" /> ' . Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED') . '</label>';
				}
				elseif ($this->profile->get('emailConfirmed') == 2)
				{
					$confirmed = Lang::txt('COM_MEMBERS_FIELD_EMAIL_GRANDFATHERED') . '<input type="hidden" name="emailConfirmed" id="emailConfirmed" value="2" />';
				}
				elseif ($this->profile->get('emailConfirmed') == 3)
				{
					$confirmed = Lang::txt('COM_MEMBERS_FIELD_EMAIL_DOMAIN_SUPPLIED') . '<input type="hidden" name="emailConfirmed" id="emailConfirmed" value="3" />';
				}
				elseif ($this->profile->get('emailConfirmed') < 0)
				{
					if ($this->profile->get('email'))
					{
						$confirmed  = Lang::txt('COM_MEMBERS_FIELD_EMAIL_AWAITING_CONFIRMATION');
						$confirmed .= '<br />[code: ' . -$this->profile->get('emailConfirmed') . '] <label for="emailConfirmed"><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> ' . Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM') . '</label>';
					}
					else
					{
						$confirmed  = Lang::txt('COM_MEMBERS_FIELD_EMAIL_NONE_ON_FILE');
					}
				}
				else
				{
					$confirmed  = '[' . Lang::txt('COM_MEMBERS_FIELD_EMAIL_UNKNOWN_STATUS') . '] <label for="emailConfirmed"><input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" /> ' . Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM') . '</label>';
				}
				?>
				<?php if ($this->profile->get('email')) { ?>
					<input type="text" name="profile[email]" id="email" value="<?php echo $this->escape(stripslashes($this->profile->get('email'))); ?>" /> (<?php echo $confirmed; ?>)
				<?php } else { ?>
					<span style="color:#c00;"><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL_NONE_ON_FILE'); ?></span><br />
					<input type="text" name="profile[email]" id="email" value="" />

					<input type="checkbox" name="emailConfirmed" id="emailConfirmed" value="1" />
					<label for="emailConfirmed"><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM'); ?></label>
				<?php } ?>
			</div>
			<div class="input-wrap">
				<label for="homeDirectory"><?php echo Lang::txt('COM_MEMBERS_FIELD_HOMEDIRECTORY'); ?></label>
				<input type="text" name="profile[homeDirectory]" id="homeDirectory" value="<?php echo $this->escape($this->profile->get('homeDirectory')); ?>" />
			</div>
			<div class="input-wrap">
				<label for="loginShell"><?php echo Lang::txt('COM_MEMBERS_FIELD_LOGINSHELL'); ?></label>
				<input type="text" name="profile[loginShell]" id="loginShell" value="<?php echo $this->escape($this->profile->get('loginShell')); ?>" />
			</div>
			<div class="input-wrap">
				<label><?php echo Lang::txt('COM_MEMBERS_FIELD_ADMINISTRATOR'); ?></label>
				<?php echo ($this->profile->get('admin') ? implode(', ', $this->profile->get('admin')) : '--'); ?>
			</div>
		</fieldset>
	</div>
	</div>
	<?php echo Html::input('token'); ?>
</form>
