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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('register')
     ->js('register');

// get return url
$form_redirect = '';
if ($form_redirect = Request::getVar('return', '', 'get'))
{
	// urldecode is due to round trip XSS protection added to this field, see ticket 1411
	$form_redirect = urldecode($form_redirect);
}
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_MEMBERS_REGISTER_'.strtoupper($this->task)); ?></h2>
</header><!-- / #content-header -->

<section class="main section">

	<?php
	switch ($this->task)
	{
		case 'update':
			if (!empty($this->xregistration->_missing))
			{
				?>
				<div class="help">
					<?php echo $this->sitename; ?> requires additional registration information before your account can be used.<br />
					All fields marked <span class="required">required</span> must be filled in.
				</div>
				<?php
			}

			if (!Request::getVar('update', false, 'post'))
			{
				$this->showMissing = false;
			}
		break;

		case 'edit':
			if ($this->self)
			{
				?>
				<div class="help">
					<h4>How do I change my password?</h4>
					<p>Passwords can be changed with <a href="<?php echo Route::url('index.php?option=com_members&id='.User::get('id').'&task=changepassword'); ?>" title="Change password form">this form</a>.</p>
				</div>
				<?php
			}
		break;

		case 'proxycreate':
			?>
			<div class="help">
				<h4>Proxy Account Creation Instructions</h4>
				<p>
					Simply fill out the form below and an account will be created for that person.
					You will then be shown the basic text of an email which you <strong>MUST</strong> then copy
					and paste and send to that person. This email will provide them with the initial password
					set for them below as well as their email confirmation link. You may add any other information
					that you deem appropriate, including contributed resources or the reason for their account.
				</p>
			</div>
			<?php
		break;

		default:
		break;
	}
	?>

	<form action="<?php echo Route::url('index.php?option='.$this->option.'&' . ($this->task == 'create' ? 'return=' . $form_redirect : 'task=' . $this->task)); ?>" method="post" id="hubForm">

		<?php
		if ($this->task == 'create' && empty($this->xregistration->_invalid) && empty($this->xregistration->_missing))
		{
			// Check to see if third party auth plugins are enabled
			Plugin::import('authentication');
			$plugins        = Plugin::byType('authentication');
			$authenticators = array();

			foreach ($plugins as $p)
			{
				if ($p->name != 'hubzero')
				{
					$pparams = new \Hubzero\Config\Registry($p->params);
					$display = $pparams->get('display_name', ucfirst($p->name));
					$authenticators[] = array(
						'name'    => $p->name,
						'display' => $display
					);
				}
			}

			// There are third party plugins, so show them on the registration form
			if (!empty($authenticators))
			{
				$this->css('providers.css', 'com_users');
				?>
				<div class="explaination">
					<p class="info">You can choose to log in via one of these services, and we'll help you fill in the info below!</p>
				</div>
				<fieldset>
					<legend>Connect With</legend>
					<div id="providers" class="auth">
					<?php
						foreach ($authenticators as $a)
						{
							$refl = new \ReflectionClass('plgauthentication'.$a['name']);
							if ($refl->hasMethod('onRenderOption') && ($html = $refl->getMethod('onRenderOption')->invoke(NULL)))
							{
								echo is_array($html) ? implode("\n", $html) : $html;
							}
							else
							{
					?>
								<a class="<?php echo $a['name']; ?> account" href="<?php echo Route::url('index.php?option=com_users&view=login&authenticator=' . $a['name']); ?>">
									<div class="signin">Sign in with <?php echo $a['display']; ?></div>
								</a>
					<?php
							}
						}
					?>
					</div>
				</fieldset>
				<div class="clear"></div>
				<?php
			}
		}
		?>

		<?php
		$emailusers = \Hubzero\User\Profile\Helper::find_by_email($this->registration['email']);

		if (($this->task == 'create' || $this->task == 'proxycreate') && $emailusers) { ?>
			<div class="error">
				<p>The email address "<?php echo $this->escape($this->registration['email']); ?>" is already registered. If you have lost or forgotten this <?php echo $this->sitename; ?> login information, we can help you recover it:</p>
				<p class="submit"><a href="<?php echo Route::url('index.php?option=com_users&view=remind'); ?>" class="btn btn-danger">Email Existing Account Information</a>
				<p>If you are aware you already have another account registered to this email address, and are requesting another account because you need more resources, <?php echo $this->sitename; ?> would be happy to work with you to raise your resource limits instead:</p>
				<p class="submit"><a href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=new'); ?>" class="btn btn-danger">Submit Request to Raise Existing Limits</a></p>
			</div>
		<?php } ?>

		<?php if (!empty($this->xregistration->_invalid) || !empty($this->xregistration->_missing)) : ?>
			<div class="error">
				Please correct the indicated invalid fields in the form below.

				<?php if ($this->showMissing && !empty($this->xregistration->_missing)) : ?>
					<?php if ($this->task == 'update') : ?>
						<br />We are missing some vital information regarding your account! Please confirm the information below so we can better serve you. Thank you!
					<?php else : ?>
						<br />Missing required information:';
					<?php endif; ?>
					<ul>
						<?php foreach ($this->xregistration->_missing as $miss) : ?>
							<li><?php echo $miss; ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ($this->registrationUsername != REG_HIDE || $this->registrationPassword != REG_HIDE) { // Login information ?>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_CANNOT_CHANGE_USERNAME'); ?></p>

				<?php if ($this->task == 'create' || $this->task == 'proxycreate') { ?>
					<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_PASSWORD_CHANGE_HINT'); ?></p>
				<?php } ?>
			</div>

			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LOGIN_INFORMATION'); ?></legend>

				<?php if ($this->registrationUsername == REG_READONLY) { ?>
					<label for="login">
						<?php Lang::txt('COM_MEMBERS_REGISTER_USER_LOGIN'); ?>: <br />
						<?php echo $this->escape($this->registration['login']); ?>
						<input name="login" id="login" type="hidden" value="<?php echo $this->escape($this->registration['login']); ?>" />
					</label>
				<?php } else if ($this->registrationUsername != REG_HIDE) { ?>
					<div class="grid">
						<div class="col span6">
							<label for="userlogin" <?php echo (!empty($this->xregistration->_invalid['login']) ? 'class="fieldWithErrors"' : ''); ?>>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_USER_LOGIN'); ?>: <?php echo ($this->registrationUsername == REG_REQUIRED ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''); ?>
								<input name="login" id="userlogin" type="text" maxlength="32" value="<?php echo $this->escape($this->registration['login']); ?>" />
								<?php echo (!empty($this->xregistration->_invalid['login']) ? '<span class="error">' . $this->xregistration->_invalid['login'] . '</span>' : ''); ?>
							</label>
						</div>
						<div class="col span6 omega">
							<p class="hint" id="usernameHint"><?php echo Lang::txt('COM_MEMBERS_REGISTER_USERNAME_HINT'); ?></p>
						</div>
					</div>
				<?php } ?>

				<?php if ($this->registrationPassword != REG_HIDE) { ?>
					<div class="grid">
						<div class="col span<?php echo ($this->registrationConfirmPassword != REG_HIDE ? '6' : '12'); ?>">
							<label<?php echo (!empty($this->xregistration->_invalid['password']) && !is_array($this->xregistration->_invalid['password'])
											? ' class="fieldWithErrors"'
											: ''); ?>>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_PASSWORD'); ?>: <?php if ($this->registrationPassword == REG_REQUIRED) { echo '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>'; } ?>
								<input name="password" id="password" type="password" value="<?php echo $this->escape($this->registration['password']); ?>" />
								<?php echo (!empty($this->xregistration->_invalid['password']) && !is_array($this->xregistration->_invalid['password'])
											? '<span class="error">' . $this->xregistration->_invalid['password'] . '</span>'
											: ''); ?>
							</label>
						</div>
						<?php if ($this->registrationConfirmPassword != REG_HIDE) { ?>
							<div class="col span6 omega">
								<label<?php echo (!empty($this->xregistration->_invalid['confirmPassword']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo Lang::txt('COM_MEMBERS_REGISTER_CONFIRM_PASSWORD'); ?>: <?php if ($this->registrationConfirmPassword == REG_REQUIRED) { echo '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>'; } ?>
									<input name="password2" id="password2" type="password" value="<?php echo $this->escape($this->registration['confirmPassword']); ?>" />
									<?php echo (!empty($this->xregistration->_invalid['confirmPassword']) ? '<span class="error">' . $this->xregistration->_invalid['confirmPassword'] . '</span>' : ''); ?>
								</label>
							</div>
						<?php } ?>
					</div>
					<?php if (count($this->password_rules) > 0) { ?>
						<ul id="passrules">
							<?php foreach ($this->password_rules as $rule)
							{
								if (!empty($rule))
								{
									$err = '';
									if (!empty($this->xregistration->_invalid['password']) && is_array($this->xregistration->_invalid['password']))
									{
										$err = in_array($rule, $this->xregistration->_invalid['password']);
									}

									echo '<li' . ($err ? ' class="error"' : ' class="empty"') . '>' . $rule . '</li>' . "\n";
								}
							}
							if (!empty($this->xregistration->_invalid['password']) && is_array($this->xregistration->_invalid['password']))
							{
								foreach ($this->xregistration->_invalid['password'] as $msg)
								{
									if (!in_array($msg, $this->password_rules))
									{
										echo '<li class="error">' . $msg . '</li>'."\n";
									}
								}
							}
							?>
						</ul>
					<?php } ?>
				<?php } ?>
			</fieldset>
			<div class="clear"></div>
		<?php } ?>

		<?php
		if ($this->registrationFullname != REG_HIDE
		 || $this->registrationEmail != REG_HIDE
		 || $this->registrationURL != REG_HIDE
		 || $this->registrationPhone != REG_HIDE
		) { ?>
			<div class="explaination">
				<?php if ($this->task == 'create') { ?>
					<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACTIVATION_EMAIL_HINT'); ?></p>
				<?php } ?>
				<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_PRIVACY_HINT'); ?></p>
			</div>

			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_CONTACT_INFORMATION'); ?></legend>

				<?php if ($this->registrationFullname != REG_HIDE) { ?>
					<?php
					$required = ($this->registrationFullname == REG_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
					$message = (!empty($this->xregistration->_invalid['name'])) ? '<p class="error">' . $this->xregistration->_invalid['name'] . '</p>' : '';
					$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

					$givenName  = '';
					$middleName = '';
					$surname    = '';

					$bits = explode(' ', $this->registration['name']);
					$surname = array_pop($bits);
					if (count($bits) >= 1)
					{
						$givenName = array_shift($bits);
					}
					if (count($bits) >= 1)
					{
						$middleName = implode(' ', $bits);
					}
					?>
					<div class="grid">
						<div class="col span4">
							<label for="first-name"<?php echo $fieldclass; ?>>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_FIRST_NAME'); ?>: <?php echo $required; ?>
								<input type="text" name="name[first]" id="first-name" value="<?php echo $this->escape(trim($givenName)); ?>" />
							</label>
						</div>
						<div class="col span4">
							<label for="middle-name">
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_MIDDLE_NAME'); ?>:
								<input type="text" name="name[middle]" id="middle-name" value="<?php echo $this->escape(trim($middleName)); ?>" />
							</label>
						</div>
						<div class="col span4 omega">
							<label for="last-name"<?php echo $fieldclass; ?>>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_LAST_NAME'); ?>: <?php echo $required; ?>
								<input type="text" name="name[last]" id="last-name" value="<?php echo $this->escape(trim($surname)); ?>" />
							</label>
						</div>
					</div>
					<?php echo ($message) ? $message . "\n" : ''; ?>
				<?php } ?>

				<?php if ($this->registrationEmail != REG_HIDE || $this->registrationConfirmEmail != REG_HIDE) { ?>
					<div class="grid">
						<?php if ($this->registrationEmail != REG_HIDE) { ?>
							<div class="col span6">
								<label for="email"<?php echo (!empty($this->xregistration->_invalid['email']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo Lang::txt('COM_MEMBERS_REGISTER_VALID_EMAIL'); ?>: <?php echo ($this->registrationEmail == REG_REQUIRED ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''); ?>
									<input name="email" id="email" type="text" value="<?php echo $this->escape($this->registration['email']); ?>" />
									<?php echo (!empty($this->xregistration->_invalid['email']) ? '<span class="error">' . $this->xregistration->_invalid['email'] . '</span>' : ''); ?>
								</label>
							</div>
						<?php } ?>
						<?php if ($this->registrationConfirmEmail != REG_HIDE) { ?>
							<div class="col span6 omega">
								<?php
								if (!empty($this->xregistration->_invalid['email']))
								{
									$this->registration['confirmEmail'] = '';
								}
								?>
								<label for="email2"<?php echo (!empty($this->xregistration->_invalid['confirmEmail']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo Lang::txt('COM_MEMBERS_REGISTER_CONFIRM_EMAIL'); ?>: <?php echo ($this->registrationConfirmEmail == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
									<input name="email2" id="email2" type="text" value="<?php echo $this->escape($this->registration['confirmEmail']); ?>" />
									<?php echo (!empty($this->xregistration->_invalid['confirmEmail']) ? '<span class="error">' . $this->xregistration->_invalid['confirmEmail'] . '</span>' : ''); ?>
								</label>
							</div>
						<?php } ?>
					</div>

					<?php if ($this->registrationEmail != REG_HIDE) { ?>
						<?php if ($this->task == 'proxycreate') { ?>
							<p class="warning">Important! The user <strong>MUST</strong> click on the email confirmation link that you will send them in order for them to start using the account you have created for them.</p>
						<?php } else if ($this->task == 'create') { ?>
							<?php
							$usersConfig    = Component::params('com_users');
							$useractivation = $usersConfig->get('useractivation', 1);
							if ($useractivation != 0) { ?>
								<p class="warning"><?php echo Lang::txt('COM_MEMBERS_REGISTER_YOU_MUST_CONFIRM_EMAIL', \Hubzero\Utility\String::obfuscate(Config::get('mailfrom'))); ?></p>
							<?php } ?>
						<?php } else { ?>
							<p class="warning">Important! If you change your e-mail address you <strong>must</strong> confirm receipt of the confirmation e-mail from <?php echo \Hubzero\Utility\String::obfuscate(Config::get('mailfrom')); ?> in order to re-activate your account.</p>
						<?php } ?>
					<?php } ?>
				<?php } ?>

				<?php if ($this->registrationORCID != REG_HIDE) { ?>
					<div class="grid">
						<div class="col span9">
							<label for="orcid"<?php echo (!empty($this->xregistration->_invalid['orcid']) ? ' class="fieldWithErrors"' : ''); ?>>
								<?php echo Lang::txt('COM_MEMBERS_ORCID'); ?>: <?php echo ($this->registrationORCID == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
								<input name="orcid" id="orcid" type="text" value="<?php echo $this->escape($this->registration['orcid']); ?>" />
								<?php echo (!empty($this->xregistration->_invalid['orcid'])) ? '<span class="error">' . $this->xregistration->_invalid['orcid'] . '</span>' : ''; ?>
							</label>
						</div>
						<div class="col span3 omega">
							<a class="btn icon-search" id="orcid-fetch" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=orcid'); ?>"><?php echo Lang::txt('COM_MEMBERS_REGISTER_FIND_ID'); ?></a>
						</div>
					</div>
					<p><img src="<?php echo $this->img('orcid-logo.png'); ?>" width="80" alt="ORCID" /> <?php echo Lang::txt('COM_MEMBERS_ORCID_EXPLANATION'); ?></p>
				<?php } ?>

				<?php if ($this->registrationURL != REG_HIDE) { ?>
					<label for="web"<?php echo (!empty($this->xregistration->_invalid['web']) ? ' class="fieldWithErrors"' : ''); ?>>
						<?php echo Lang::txt('COM_MEMBERS_REGISTER_URL'); ?>: <?php echo ($this->registrationURL == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<input name="web" id="web" type="text" value="<?php echo $this->escape($this->registration['web']); ?>" placeholder="http://" />
						<?php echo (!empty($this->xregistration->_invalid['web'])) ? '<span class="error">' . $this->xregistration->_invalid['web'] . '</span>' : ''; ?>
					</label>
				<?php } ?>

				<?php if ($this->registrationPhone != REG_HIDE) { ?>
					<label for="phone"<?php echo (!empty($this->xregistration->_invalid['phone']) ? ' class="fieldWithErrors"' : ''); ?>>
						<?php echo Lang::txt('COM_MEMBERS_REGISTER_PHONE'); ?>: <?php echo ($this->registrationPhone == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<input name="phone" id="phone" type="text" value="<?php echo $this->escape($this->registration['phone']); ?>" placeholder="###-###-####" />
						<?php echo (!empty($this->xregistration->_invalid['phone'])) ? '<span class="error">' . $this->xregistration->_invalid['phone'] . '</span>' : ''; ?>
					</label>
				<?php } ?>
			</fieldset>
			<div class="clear"></div>
		<?php } ?>

		<?php if ($this->registrationEmployment != REG_HIDE
		 || $this->registrationOrganization != REG_HIDE
		 || $this->registrationInterests != REG_HIDE
		 || $this->registrationReason != REG_HIDE
		) { ?>
			<div class="explaination">
				<p>
					<?php
					if ($this->registrationEmployment != REG_HIDE || $this->registrationOrganization != REG_HIDE )
					{
						echo Lang::txt('COM_MEMBERS_REGISTER_PERSONAL_INFO_DISCLAIMER_ALT');
					}
					else
					{
						echo Lang::txt('COM_MEMBERS_REGISTER_PERSONAL_INFO_DISCLAIMER');
					}
					?>
				</p>
				<?php if ($this->registrationCitizenship != REG_HIDE
				 || $this->registrationResidency != REG_HIDE
				 || $this->registrationSex != REG_HIDE
				 || $this->registrationDisability != REG_HIDE
				) { ?>
					<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_PERSONAL_INFO_WHY_WE_COLLECT'); ?></p>
				<?php } ?>
			</div>

			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LEGEND_PERSONAL_INFO'); ?></legend>

				<?php if ($this->registrationEmployment != REG_HIDE) { ?>
					<?php
					$message = (!empty($this->xregistration->_invalid['orgtype'])) ? '<span class="error">' . $this->xregistration->_invalid['orgtype'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

					include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'organizationtype.php');
					$database = App::get('db');
					$rot = new \Components\Members\Tables\OrganizationType($database);
					$types = $rot->find('list');
					?>
					<label for="orgtype"<?php echo $fieldclass; ?>>
						<?php echo Lang::txt('COM_MEMBERS_REGISTER_EMPLOYMENT_TYPE'); ?>: <?php echo ($this->registrationEmployment == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<select name="orgtype" id="orgtype">
							<?php if (empty($this->registration['orgtype']) || !empty($this->xregistration->_invalid['orgtype'])) { ?>
								<option value="" selected="selected"><?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_SELECT_FROM_LIST'); ?></option>
							<?php } ?>
							<?php foreach ($types as $orgtype) { ?>
								<option value="<?php echo $this->escape($orgtype->type); ?>"<?php if ($this->registration['orgtype'] == $orgtype->type) { echo ' selected="selected"'; } ?>><?php echo $this->escape($orgtype->title); ?></option>
							<?php } ?>
						</select>
						<?php echo ($message) ? "\t\t\t\t" . $message . "\n" : ''; ?>
					</label>
				<?php } ?>

				<?php if ($this->registrationOrganization != REG_HIDE) { ?>
					<?php
					$orgtext = $this->registration['org'];
					$org_known = 0;

					include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'organization.php');
					$database = App::get('db');
					$xo = new \Components\Members\Tables\Organization($database);
					$orgs = $xo->find('list');

					foreach ($orgs as $org)
					{
						if ($org->organization == $this->registration['org'])
						{
							$org_known = 1;
						}
					}

					$message = (!empty($this->xregistration->_invalid['org'])) ? '<span class="error">' . $this->xregistration->_invalid['org'] . '</span>' : '';
					?>
					<label for="org"<?php echo ($message) ? ' class="fieldWithErrors"' : ''; ?>>
						<?php echo Lang::txt('COM_MEMBERS_REGISTER_ORGANIZATION'); ?>: <?php echo ($this->registrationOrganization == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<select name="org" id="org">
							<option value=""<?php if (!$org_known) { echo ' selected="selected"'; } ?>><?php echo ($org_known) ? Lang::txt('COM_MEMBERS_REGISTER_OTHER_NONE') : Lang::txt('COM_MEMBERS_REGISTER_FORM_SELECT_OR_ENTER'); ?></option>
							<?php foreach ($orgs as $org) { ?>
								<option value="<?php echo $this->escape($org->organization); ?>"<?php if ($org->organization == $this->registration['org']) { $orgtext = ''; echo ' selected="selected"'; } ?>><?php echo $this->escape($org->organization); ?></option>
							<?php } ?>
						</select>
						<?php echo ($message) ? $message . "\n" : ''; ?>
					</label>
					<input name="orgtext" id="orgtext" type="text" value="<?php echo $this->escape($this->registration['orgtext']); ?>" />
				<?php } ?>

				<?php if ($this->registrationReason != REG_HIDE) { ?>
					<?php
					$message = (!empty($this->xregistration->_invalid['reason'])) ? '<span class="error">' . $this->xregistration->_invalid['reason'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

					include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'reason.php');
					$database = App::get('db');
					$xr = new \Components\Members\Tables\Reason($database);
					$reasons = $xr->find('list');

					$otherreason = '';
					?>
					<label for="reason"<?php echo $fieldclass; ?>>
						<?php echo Lang::txt('COM_MEMBERS_REGISTER_REASON'); ?>: <?php echo ($this->registrationReason == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<select name="reason" id="reason">
							<option value=""><?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_SELECT_OR_ENTER'); ?></option>
							<?php foreach ($reasons as $r) { ?>
								<option value="<?php echo $this->escape($r->reason); ?>"<?php if ($this->registration['reason'] == $r->reason) { echo ' selected="selected"'; } ?>><?php echo $this->escape($r->reason); ?></option>
							<?php } ?>
						</select>
					</label>
					<input name="reasontxt" id="reasontxt" type="text" value="<?php echo $this->escape($this->registration['reason']); ?>" />
					<?php echo ($message) ? $message . "\n" : ''; ?>
				<?php } ?>

				<?php if ($this->registrationInterests != REG_HIDE) { ?>
					<label<?php echo (!empty($this->xregistration->_invalid['interests'])) ? ' class="fieldWithErrors"' : ''; ?>>
						<?php echo Lang::txt('COM_MEMBERS_REGISTER_INTERESTS'); ?>: <?php echo ($this->registrationInterests == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<?php
						$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'interests', 'actags','',stripslashes($this->registration['interests']))));

						echo (count($tf) > 0)
							? implode("\n", $tf)
							: '<input type="text" name="interests" value="'. $this->escape($this->registration['interests']) .'" />'."\n";
						?>
						<?php echo (!empty($this->xregistration->_invalid['interests'])) ? '<span class="error">' . $this->xregistration->_invalid['interests'] . '</span>' : ''; ?>
					</label>
				<?php } ?>
			</fieldset>
			<div class="clear"></div>
		<?php } ?>

		<?php if ($this->registrationCitizenship != REG_HIDE
		 || $this->registrationResidency != REG_HIDE
		 || $this->registrationSex != REG_HIDE
		 || $this->registrationDisability != REG_HIDE
		 || $this->registrationHispanic != REG_HIDE
		 || $this->registrationRace != REG_HIDE
		) { ?>
			<div class="explaination">
				<?php
				if ($this->registrationHispanic != REG_HIDE)
				{
					echo '<p>';
					if ($this->registrationRace != REG_HIDE)
					{
						echo Lang::txt('COM_MEMBERS_REGISTER_DEMOGRAPHICS_DISCLAIMER_ALT');
					}
					else
					{
						echo Lang::txt('COM_MEMBERS_REGISTER_DEMOGRAPHICS_DISCLAIMER');
					}
					echo '</p>';
				}
				?>
				<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_DEMOGRAPHICS_PLEASE_PROVIDE'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LEGEND_DEMOGRAPHICS'); ?></legend>

				<?php if ($this->registrationCitizenship != REG_HIDE) { ?>
					<?php
					$message = (!empty($this->xregistration->_invalid['countryorigin'])) ? '<span class="error">' . $this->xregistration->_invalid['countryorigin'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

					if (!$this->registration['countryorigin'])
					{
						$userCountry = \Hubzero\Geocode\Geocode::ipcountry(Request::ip());
						$this->registration['countryorigin'] = $userCountry;
					}
					?>
					<fieldset<?php echo $fieldclass; ?>>
						<legend>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_CITIZEN_OF_USA'); ?>
							<?php echo ($this->registrationCitizenship == REG_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''; ?>
						</legend>

						<?php echo ($message) ? $message . "\n" : ''; ?>

						<label for="corigin_usyes">
							<input type="radio" class="option" name="corigin_us" id="corigin_usyes" value="yes"<?php if (strcasecmp($this->registration['countryorigin'],'US') == 0) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_YES'); ?>
						</label>

						<label for="corigin_usno">
							<input type="radio" class="option" name="corigin_us" id="corigin_usno" value="no"<?php if (!empty($this->registration['countryorigin']) && (strcasecmp($this->registration['countryorigin'], 'US') != 0)) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_NO'); ?>
						</label>

						<label for="corigin">
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_CITIZEN'); ?>:
							<select name="corigin" id="corigin">
								<?php if (!$this->registration['countryorigin'] || $this->registration['countryorigin'] == 'US') { ?>
									<option value=""><?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_SELECT_FROM_LIST'); ?></option>
								<?php } ?>
								<?php
								$countries = \Hubzero\Geocode\Geocode::countries();
								if ($countries)
								{
									foreach ($countries as $country)
									{
										?>
										<option value="<?php echo $country->code; ?>"<?php if (strtoupper($this->registration['countryorigin']) == strtoupper($country->code)) { echo ' selected="selected"'; } ?>><?php echo $this->escape($country->name); ?></option>
										<?php
									}
								}
								?>
							</select>
						</label>
					</fieldset>
				<?php } ?>

				<?php if ($this->registrationResidency != REG_HIDE) { ?>
					<?php
					$message = (!empty($this->xregistration->_invalid['countryresident'])) ? '<span class="error">' . $this->xregistration->_invalid['countryresident'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

					if (!$this->registration['countryresident'])
					{
						if (!isset($userCountry) || !$userCountry)
						{
							$userCountry = \Hubzero\Geocode\Geocode::ipcountry(Request::ip());
						}
						$this->registration['countryresident'] = $userCountry;
					}
					?>
					<fieldset<?php echo $fieldclass; ?>>
						<legend>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_RESIDENT_OF_USA'); ?>
							<?php echo ($this->registrationResidency == REG_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''; ?>
						</legend>

						<?php echo ($message) ? $message . "\n" : ''; ?>

						<label for="cresident_usyes">
							<input type="radio" class="option" name="cresident_us" id="cresident_usyes" value="yes"<?php if (strcasecmp($this->registration['countryresident'], 'US') == 0) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_YES'); ?>
						</label>

						<label for="cresident_usno">
							<input type="radio" class="option" name="cresident_us" id="cresident_usno" value="no"<?php if (!empty($this->registration['countryresident']) && strcasecmp($this->registration['countryresident'], 'US') != 0) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_NO'); ?>
						</label>

						<label for="cresident">
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_RESIDENT'); ?>:
							<select name="cresident" id="cresident">
								<?php if (!$this->registration['countryresident'] || strcasecmp($this->registration['countryresident'], 'US') == 0) { ?>
									<option value=""><?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_SELECT_FROM_LIST'); ?></option>
								<?php } ?>
								<?php
								if (!isset($countries) || !$countries)
								{
									$countries = \Hubzero\Geocode\Geocode::countries();
								}
								if ($countries)
								{
									foreach ($countries as $country)
									{
										?>
										<option value="<?php echo $country->code; ?>"<?php if (strtoupper($this->registration['countryresident']) == strtoupper($country->code)) { echo ' selected="selected"'; } ?>><?php echo $this->escape($country->name); ?></option>
										<?php
									}
								}
								?>
							</select>
						</label>
					</fieldset>
				<?php } ?>

				<?php if ($this->registrationSex != REG_HIDE) { ?>
					<fieldset<?php echo (!empty($this->xregistration->_invalid['sex'])) ? ' class="fieldsWithErrors"' : ''; ?>>
						<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_GENDER'); ?>: <?php echo ($this->registrationSex == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?></legend>
						<?php echo (!empty($this->xregistration->_invalid['sex'])) ? '<span class="error">' . $this->xregistration->_invalid['sex'] . '</span>' : ''; ?>
						<input type="hidden" name="sex" value="unspecified" />
						<label for="sex_male"><input class="option" type="radio" name="sex" id="sex_male" value="male"<?php echo ($this->registration['sex'] == 'male' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_MALE'); ?></label>
						<label for="sex_female"><input class="option" type="radio" name="sex" id="sex_female" value="female"<?php echo ($this->registration['sex'] == 'female' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_FEMALE'); ?></label>
						<label for="sex_refused"><input class="option" type="radio" name="sex" id="sex_refused" value="refused"<?php echo ($this->registration['sex'] == 'refused' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MEMBERS_REGISTER_FORM_REFUSED'); ?></label>
					</fieldset>
				<?php } ?>

				<?php if ($this->registrationDisability != REG_HIDE) { ?>
					<?php
					$required = ($this->registrationDisability == REG_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
					$message = (!empty($this->xregistration->_invalid['disability'])) ? '<span class="error">' . $this->xregistration->_invalid['disability'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

					$disabilityyes = false;
					$disabilityother = '';

					if (!is_array($this->registration['disability']))
					{
						$this->registration['disability'] = array();
					}

					foreach ($this->registration['disability'] as $disabilityitem)
					{
						if ($disabilityitem != 'no' && $disabilityitem != 'refused')
						{
							if (!$disabilityyes)
							{
								$disabilityyes = true;
							}
							if ($disabilityitem != 'blind'
							 && $disabilityitem != 'deaf'
							 && $disabilityitem != 'physical'
							 && $disabilityitem != 'learning'
							 && $disabilityitem != 'vocal'
							 && $disabilityitem != 'yes'
							)
							{
								$disabilityother = $disabilityitem;
							}
						}
					}
					?>
					<fieldset<?php echo $fieldclass; ?>>
						<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_DISABILITY'); ?>: <?php echo $required; ?></legend>
						<?php echo ($message) ? $message : ''; ?>

						<label>
							<input type="radio" class="option" name="disability" id="disabilityyes" value="yes"<?php if ($disabilityyes) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('JYES'); ?>
						</label>

						<fieldset>
							<label for="disabilityblind">
								<input type="checkbox" class="option" name="disabilityblind" id="disabilityblind" <?php if (in_array('blind', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_DISABILITY_VISION'); ?>
							</label>

							<label for="disabilitydeaf">
								<input type="checkbox" class="option" name="disabilitydeaf" id="disabilitydeaf" <?php if (in_array('deaf', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_DISABILITY_HEARING'); ?>
							</label>

							<label for="disabilityphysical">
								<input type="checkbox" class="option" name="disabilityphysical" id="disabilityphysical" <?php if (in_array('physical', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_DISABILITY_PHYSICAL'); ?>
							</label>

							<label for="disabilitylearning">
								<input type="checkbox" class="option" name="disabilitylearning" id="disabilitylearning" <?php if (in_array('learning', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_DISABILITY_LEARNING'); ?>
							</label>

							<label for="disabilityvocal">
								<input type="checkbox" class="option" name="disabilityvocal" id="disabilityvocal" <?php if (in_array('vocal', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_DISABILITY_VOCAL'); ?>
							</label>

							<label for="disabilityother">
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_DISABILITY_OTHER'); ?>:
								<input name="disabilityother" id="disabilityother" type="text" value="<?php echo $this->escape($disabilityother); ?>" />
							</label>
						</fieldset>

						<label for="disabilityno">
							<input type="radio" class="option" name="disability" id="disabilityno" value="no"<?php if (in_array('no', $this->registration['disability'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_DISABILITY_NONE'); ?>
						</label>

						<label for="disabilityrefused">
							<input type="radio" class="option" name="disability" id="disabilityrefused" value="refused"<?php if (in_array('refused', $this->registration['disability'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_DO_NOT_REVEAL'); ?>
						</label>
					</fieldset>
				<?php } ?>

				<?php if ($this->registrationHispanic != REG_HIDE) { ?>
					<?php
					$required = ($this->registrationHispanic == REG_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
					$message = (!empty($this->xregistration->_invalid['hispanic'])) ? '<span class="error">' . $this->xregistration->_invalid['hispanic'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

					$hispanicyes = false;
					$hispanicother = '';

					if (!is_array($this->registration['hispanic']))
					{
						$this->registration['hispanic'] = array();
					}

					foreach ($this->registration['hispanic'] as $hispanicitem)
					{
						if ($hispanicitem != 'no' && $hispanicitem != 'refused')
						{
							if (!$hispanicyes)
							{
								$hispanicyes = true;
							}
							if ($hispanicitem != 'cuban'
							 && $hispanicitem != 'mexican'
							 && $hispanicitem != 'puertorican'
							)
							{
								$hispanicother = $hispanicitem;
							}
						}
					}
					?>
					<fieldset<?php echo $fieldclass; ?>>
						<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LEGEND_HISPANIC'); ?> (<a class="popup 700x500" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=raceethnic'); ?>">more information</a>) <?php echo $required; ?></legend>
						<?php echo $message; ?>

						<label for="hispanicyes">
							<input type="radio" class="option" name="hispanic" id="hispanicyes" value="yes" <?php if ($hispanicyes) { echo 'checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_HISPANIC_YES'); ?>
						</label>

						<fieldset>
							<label for="hispaniccuban">
								<input type="checkbox" class="option" name="hispaniccuban" id="hispaniccuban" <?php if (in_array('cuban', $this->registration['hispanic'])) { echo 'checked="checked"'; } ?> />
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_HISPANIC_CUBAN'); ?>
							</label>

							<label for="hispanicmexican">
								<input type="checkbox" class="option" name="hispanicmexican" id="hispanicmexican" <?php if (in_array('mexican', $this->registration['hispanic'])) { echo 'checked="checked"'; } ?> />
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_HISPANIC_MEXICAN'); ?>
							</label>

							<label for="hispanicpuertorican">
								<input type="checkbox" class="option" name="hispanicpuertorican" id="hispanicpuertorican" <?php if (in_array('puertorican', $this->registration['hispanic'])) { echo 'checked="checked"'; } ?> />
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_HISPANIC_PUERTO_RICAN'); ?>
							</label>

							<label for="hispanicother">
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_HISPANIC_OTHER'); ?>:
								<input name="hispanicother" id="hispanicother" type="text" value="<?php echo $this->escape($hispanicother); ?>" />
							</label>
						</fieldset>

						<label for="hispanicno">
							<input type="radio" class="option" name="hispanic" id="hispanicno" value="no"<?php if (in_array('no', $this->registration['hispanic'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_HISPANIC_NO'); ?>
						</label>

						<label for="hispanicrefused">
							<input type="radio" class="option" name="hispanic" id="hispanicrefused" value="refused"<?php if (in_array('refused', $this->registration['hispanic'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_DO_NOT_REVEAL'); ?>
						</label>
					</fieldset>
				<?php } ?>

				<?php if ($this->registrationRace != REG_HIDE) { ?>
					<?php
					$required = ($this->registrationRace == REG_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
					$message = (!empty($this->xregistration->_invalid['race'])) ? '<span class="error">' . $this->xregistration->_invalid['race'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

					if (!is_array($this->registration['race']))
					{
						$this->registration['race'] = array(trim($this->registration['race']));
					}
					?>
					<fieldset<?php echo $fieldclass; ?>>
						<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LEGEND_RACE'); ?> (<a class="popup 675x678" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=raceethnic'); ?>">more information</a>) <?php echo $required; ?></legend>

						<p class="hint"><?php echo Lang::txt('COM_MEMBERS_REGISTER_SELECT_ONE_OR_MORE'); ?></p>

						<label for="racenativeamerican">
							<input type="checkbox" class="option" name="racenativeamerican" id="racenativeamerican" value="nativeamerican"<?php if (in_array('nativeamerican', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_RACE_AMERICAN_INDIAN'); ?>
						</label>

						<label for="racenativetribe" class="indent">
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_RACE_TRIBE'); ?>:
							<input name="racenativetribe" id="racenativetribe" type="text" value="<?php echo $this->escape($this->registration['nativetribe']); ?>" />
						</label>

						<label for="raceasian">
							<input type="checkbox" class="option" name="raceasian" id="raceasian"<?php if (in_array('asian', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_RACE_ASIAN'); ?>
						</label>

						<label for="raceblack">
							<input type="checkbox" class="option" name="raceblack" id="raceblack"<?php if (in_array('black', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_RACE_BLACK'); ?>
						</label>

						<label for="racehawaiian">
							<input type="checkbox" class="option" name="racehawaiian" id="racehawaiian"<?php if (in_array('hawaiian', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_RACE_PACIFIC_ISLANDER'); ?>
						</label>

						<label for="racewhite">
							<input type="checkbox" class="option" name="racewhite" id="racewhite"<?php if (in_array('white', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_RACE_WHITE'); ?>
						</label>

						<label for="racerefused">
							<input type="checkbox" class="option" name="racerefused" id="racerefused"<?php if (in_array('refused', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo Lang::txt('COM_MEMBERS_REGISTER_DO_NOT_REVEAL'); ?>
						</label>

						<?php echo ($message) ? $message . "\n" : ''; ?>
					</fieldset>
				<?php } ?>
			</fieldset>
			<div class="clear"></div>
		<?php } ?>

		<?php if ($this->registrationOptIn != REG_HIDE) { ?>
			<?php
			$message = (!empty($this->xregistration->_invalid['mailPreferenceOption'])) ? '<span class="error">' . $this->xregistration->_invalid['mailPreferenceOption'] . '</span>' : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			//define mail preference options
			$options = array(
				'-1' => Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES_SELECT'),
				'1'  => Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES_YES'),
				'0'  => Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES_NO')
			);

			//if we dont have a mail pref option set to unanswered
			if (!isset($this->registration['mailPreferenceOption']) || $this->registration['mailPreferenceOption'] == '')
			{
				$this->registration['mailPreferenceOption'] = '-1';
			}
			?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LEGEND_EMAIL_UPDATES'); ?></legend>

				<label for="mailPreferenceOption"<?php echo $fieldclass; ?>>
					<?php echo Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES'); ?> <?php echo ($this->registrationOptIn == REG_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''; ?>
					<select name="mailPreferenceOption">
						<?php foreach ($options as $key => $value) { ?>
							<option <?php echo ($key == $this->registration['mailPreferenceOption']) ? 'selected="selected"' : ''; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
						<?php } ?>
					</select>
				</label>
				<?php echo $message; ?>
			</fieldset><div class="clear"></div>
		<?php } ?>


		<?php if ($this->registrationCAPTCHA != REG_HIDE) { ?>
			<?php
			$required = ($this->registrationCAPTCHA == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (isset($this->xregistration->_invalid['captcha']) && !empty($this->xregistration->_invalid['captcha'])) ? '<span class="error">' . $this->xregistration->_invalid['captcha'] . '</span>' : '';

			$captchas = Event::trigger('hubzero.onGetCaptcha');

			if (count($captchas) > 0) { ?>
				<fieldset>
					<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_HUMAN_CHECK'); ?></legend>
					<?php echo ($message) ? $message : ''; ?>
			<?php } ?>

			<label id="botcheck-label" for="botcheck">
				<?php echo Lang::txt('COM_MEMBERS_REGISTER_BOT_CHECK_LABEL'); ?> <?php echo $required; ?>
				<input type="text" name="botcheck" id="botcheck" value="" />
			</label>

			<?php if (count($captchas) > 0) {
					echo implode("\n", $captchas); ?>
				</fieldset>
			<?php } ?>
		<?php } ?>

		<?php if ($this->registrationTOU != REG_HIDE) { ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_TERMS_AND_CONDITIONS'); ?></legend>

				<label for="usageAgreement"<?php echo (!empty($this->xregistration->_invalid['usageAgreement'])) ? ' class="fieldWithErrors"' : ''; ?>>
					<input type="checkbox" class="option" id="usageAgreement" value="1" name="usageAgreement"<?php if ($this->registration['usageAgreement']) { echo ' checked="checked"'; } ?>/>
					<?php echo ($this->registrationTOU == REG_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
					<?php echo Lang::txt('COM_MEMBERS_REGISTER_TOS', Request::base(true)); ?>
				</label>

				<?php echo (!empty($this->xregistration->_invalid['usageAgreement'])) ? '<span class="error">' . $this->xregistration->_invalid['usageAgreement'] . '</span>' : ''; ?>
			</fieldset>
			<div class="clear"></div>
		<?php } else if ($this->registration['usageAgreement']) { ?>
			<input name="usageAgreement" type="hidden" id="usageAgreement" value="checked" />
			<div class="clear"></div>
		<?php } ?>

		<p class="submit">
			<input type="submit" name="<?php echo $this->task; ?>" value="<?php echo Lang::txt('COM_MEMBERS_REGISTER_BUTTON_' . strtoupper($this->task)); ?>" />
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="act" value="submit" />
		<?php echo Html::input('token'); ?>

		<input type="hidden" name="base_uri" id="base_uri" value="<?php echo rtrim(Request::base(true), '/'); ?>" />

		<input type="hidden" name="return" value="<?php echo urlencode($form_redirect); // urlencode is XSS protection added to this field, see ticket 1411 ?>" />
	</form>
</section><!-- / .main section -->
