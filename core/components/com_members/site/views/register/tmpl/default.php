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

use Components\Members\Models\Profile\Field;

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
			if ($this->isSelf)
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
					<p>Already have an account? <a href="<?php echo Route::url('index.php?option=com_users&view=login'); ?>">Log in here.</a></p>
				</div>
				<fieldset>
					<legend>Connect With</legend>
					<div id="providers" class="auth">
						<?php
						foreach ($authenticators as $a)
						{
							$refl = new ReflectionClass('plgauthentication'.$a['name']);
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
		$emailusers = User::oneByEmail($this->registration['email'])->get('id');

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
						<br />Missing required information:
					<?php endif; ?>
					<ul>
						<?php foreach ($this->xregistration->_missing as $miss) : ?>
							<li><?php echo $miss; ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ($this->registrationUsername != Field::STATE_HIDDEN || $this->registrationPassword != Field::STATE_HIDDEN) { // Login information ?>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_CANNOT_CHANGE_USERNAME'); ?></p>

				<?php if ($this->task == 'create' || $this->task == 'proxycreate') { ?>
					<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_PASSWORD_CHANGE_HINT'); ?></p>
				<?php } ?>
			</div>

			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LOGIN_INFORMATION'); ?></legend>

				<?php if ($this->registrationUsername == Field::STATE_READONLY) { ?>
					<label for="login">
						<?php Lang::txt('COM_MEMBERS_REGISTER_USER_LOGIN'); ?>: <br />
						<?php echo $this->escape($this->registration['login']); ?>
						<input name="login" id="login" type="hidden" value="<?php echo $this->escape($this->registration['login']); ?>" />
					</label>
				<?php } else if ($this->registrationUsername != Field::STATE_HIDDEN) { ?>
					<div class="grid">
						<div class="col span6">
							<label for="userlogin" <?php echo (!empty($this->xregistration->_invalid['login']) ? 'class="fieldWithErrors"' : ''); ?>>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_USER_LOGIN'); ?>: <?php echo ($this->registrationUsername == Field::STATE_REQUIRED ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''); ?>
								<input name="login" id="userlogin" type="text" maxlength="32" value="<?php echo $this->escape($this->registration['login']); ?>" />
								<?php echo (!empty($this->xregistration->_invalid['login']) ? '<span class="error">' . $this->xregistration->_invalid['login'] . '</span>' : ''); ?>
							</label>
						</div>
						<div class="col span6 omega">
							<p class="hint" id="usernameHint"><?php echo Lang::txt('COM_MEMBERS_REGISTER_USERNAME_HINT'); ?></p>
						</div>
					</div>
				<?php } ?>

				<?php if ($this->registrationPassword != Field::STATE_HIDDEN) { ?>
					<div class="grid">
						<div class="col span<?php echo ($this->registrationConfirmPassword != Field::STATE_HIDDEN ? '6' : '12'); ?>">
							<label<?php echo (!empty($this->xregistration->_invalid['password']) && !is_array($this->xregistration->_invalid['password'])
											? ' class="fieldWithErrors"'
											: ''); ?>>
								<?php echo Lang::txt('COM_MEMBERS_REGISTER_PASSWORD'); ?>: <?php if ($this->registrationPassword == Field::STATE_REQUIRED) { echo '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>'; } ?>
								<input name="password" id="password" type="password" value="<?php echo $this->escape($this->registration['password']); ?>" autocomplete="off" />
								<?php echo (!empty($this->xregistration->_invalid['password']) && !is_array($this->xregistration->_invalid['password'])
											? '<span class="error">' . $this->xregistration->_invalid['password'] . '</span>'
											: ''); ?>
							</label>
						</div>
						<?php if ($this->registrationConfirmPassword != Field::STATE_HIDDEN) { ?>
							<div class="col span6 omega">
								<label<?php echo (!empty($this->xregistration->_invalid['confirmPassword']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo Lang::txt('COM_MEMBERS_REGISTER_CONFIRM_PASSWORD'); ?>: <?php if ($this->registrationConfirmPassword == Field::STATE_REQUIRED) { echo '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>'; } ?>
									<input name="password2" id="password2" type="password" value="<?php echo $this->escape($this->registration['confirmPassword']); ?>" autocomplete="off" />
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

		<?php if ($this->registrationFullname != Field::STATE_HIDDEN) { ?>
			<div class="explaination">
				<?php if ($this->task == 'create') { ?>
					<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACTIVATION_EMAIL_HINT'); ?></p>
				<?php } ?>
				<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_PRIVACY_HINT'); ?></p>
			</div>

			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_CONTACT_INFORMATION'); ?></legend>

				<?php if ($this->registrationFullname != Field::STATE_HIDDEN) { ?>
					<?php
					$required = ($this->registrationFullname == Field::STATE_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
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

				<?php if ($this->registrationEmail != Field::STATE_HIDDEN || $this->registrationConfirmEmail != Field::STATE_HIDDEN) { ?>
					<div class="grid">
						<?php if ($this->registrationEmail != Field::STATE_HIDDEN) { ?>
							<div class="col span6">
								<label for="email"<?php echo (!empty($this->xregistration->_invalid['email']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo Lang::txt('COM_MEMBERS_REGISTER_VALID_EMAIL'); ?>: <?php echo ($this->registrationEmail == Field::STATE_REQUIRED ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''); ?>
									<input name="email" id="email" type="text" value="<?php echo $this->escape($this->registration['email']); ?>" />
									<?php echo (!empty($this->xregistration->_invalid['email']) ? '<span class="error">' . $this->xregistration->_invalid['email'] . '</span>' : ''); ?>
								</label>
							</div>
						<?php } ?>
						<?php if ($this->registrationConfirmEmail != Field::STATE_HIDDEN) { ?>
							<div class="col span6 omega">
								<?php
								if (!empty($this->xregistration->_invalid['email']))
								{
									$this->registration['confirmEmail'] = '';
								}
								?>
								<label for="email2"<?php echo (!empty($this->xregistration->_invalid['confirmEmail']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo Lang::txt('COM_MEMBERS_REGISTER_CONFIRM_EMAIL'); ?>: <?php echo ($this->registrationConfirmEmail == Field::STATE_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
									<input name="email2" id="email2" type="text" value="<?php echo $this->escape($this->registration['confirmEmail']); ?>" />
									<?php echo (!empty($this->xregistration->_invalid['confirmEmail']) ? '<span class="error">' . $this->xregistration->_invalid['confirmEmail'] . '</span>' : ''); ?>
								</label>
							</div>
						<?php } ?>
					</div>

					<?php if ($this->registrationEmail != Field::STATE_HIDDEN) { ?>
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
			</fieldset>
			<div class="clear"></div>
		<?php } ?>

		<?php
		// Convert to XML so we can use the Form processor
		$xml = Field::toXml($this->fields, 'create');

		// Gather data to pass to the form processor
		$data = new Hubzero\Config\Registry();

		// Create a new form
		Hubzero\Form\Form::addFieldPath(Component::path('com_members') . DS . 'models' . DS . 'fields');

		$form = new Hubzero\Form\Form('profile', array('control' => 'profile'));
		$form->load($xml);
		$form->bind($data);

		$scripts = array();
		$toggle = array();

		if ($this->fields->count() > 0): ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LEGEND_PERSONAL_INFO'); ?></legend>

				<?php foreach ($this->fields as $field): ?>
					<?php
					$formfield = $form->getField($field->get('name'));

					if ($field->options->count())
					{
						$i = 0;
						$hasEvents = false;
						$opts = array();
						$hide = array();

						foreach ($field->options as $option)
						{
							$opts[] = '#' . $formfield->id . $i;

							$i++;

							if (!$option->get('dependents'))
							{
								continue;
							}

							$events = json_decode($option->get('dependents'));
							$option->set('dependents', $events);

							if (empty($events))
							{
								continue;
							}

							$hasEvents = true;
						}

						if ($hasEvents)
						{
							if ($field->get('type') == 'dropdown')
							{
								$scripts[] = '	$("#'. $formfield->id . '").on("change", function(e){';
							}
							else
							{
								$scripts[] = '	$("'. implode(',', $opts) . '").on("change", function(e){';
							}
						}

						$i = 0;
						foreach ($field->options as $option)
						{
							if (!$option->get('dependents'))
							{
								continue;
							}

							$events = $option->get('dependents');

							if ($field->get('type') == 'dropdown')
							{
								$scripts[] = '		if ($(this).val() == "' . ($option->value ? $option->value : $option->label) . '") {';
								$show = array();
								foreach ($events as $s)
								{
									$show[] = '#input-' . $s;
								}
								$hide = array_merge($hide, $show);
								$scripts[] = '			$("' . implode(', ', $show) . '").show();';
								$scripts[] = '		} else {';
								$scripts[] = '			$("' . implode(', ', $show) . '").hide();';
								$scripts[] = '		}';

								$toggle[] = '	if ($("#profile_' . $field->get('name') . '").val() == "' . ($option->value ? $option->value : $option->label) . '") {';
								$toggle[] = '		$("' . implode(', ', $show) . '").show();';
								$toggle[] = '	} else {';
								$toggle[] = '		$("' . implode(', ', $show) . '").hide();';
								$toggle[] = '	}';
							}
							else
							{
								$scripts[] = '		if ($(this).is(":checked") && $(this).val() == "' . ($option->value ? $option->value : $option->label) . '") {';
								$show = array();
								foreach ($events as $s)
								{
									$show[] = '#input-' . $s;
								}
								$hide = array_merge($hide, $show);
								$scripts[] = '			$("' . implode(', ', $show) . '").show();';
								$scripts[] = '		} else {';
								$scripts[] = '			$("' . implode(', ', $show) . '").hide();';
								$scripts[] = '		}';

								$toggle[] = '	if ($("#profile_' . $field->get('name') . $i . '").is(":checked") && $("#profile_' . $field->get('name') . $i . '").val() == "' . ($option->value ? $option->value : $option->label) . '") {';
								$toggle[] = '		$("' . implode(', ', $show) . '").show();';
								$toggle[] = '	} else {';
								$toggle[] = '		$("' . implode(', ', $show) . '").hide();';
								$toggle[] = '	}';
							}

							$i++;
						}

						if ($hasEvents)
						{
							$scripts[] = '	});';
							//$scripts[] = '	$("' . implode(', ', $hide) . '").hide();';
							$scripts[] = implode("\n", $toggle);
						}
					}

					if (isset($this->registration['_profile'][$field->get('name')]))
					{
						$formfield->setValue($this->registration['_profile'][$field->get('name')]);
					}

					$errors = (!empty($this->xregistration->_invalid[$field->get('name')])) ? '<span class="error">' . $this->xregistration->_invalid[$field->get('name')] . '</span>' : '';
					?>
					<div class="input-wrap<?php echo ($errors ? ' fieldWithErrors' : ''); ?>" id="input-<?php echo $field->get('name'); ?>">
						<?php
						echo $formfield->label;
						echo $formfield->input;
						echo $errors;
						?>
					</div>
				<?php endforeach; ?>
			</fieldset>
		<?php endif;

		if (!empty($scripts))
		{
			$this->js("jQuery(document).ready(function($){\n" . implode("\n", $scripts) . "\n});");
		}
		?>

		<?php if ($this->registrationOptIn != Field::STATE_HIDDEN) { ?>
			<?php
			$message = (!empty($this->xregistration->_invalid['sendEmail'])) ? '<span class="error">' . $this->xregistration->_invalid['sendEmail'] . '</span>' : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

			//define mail preference options
			$options = array(
				'-1' => Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES_SELECT'),
				'1'  => Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES_YES'),
				'0'  => Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES_NO')
			);

			//if we dont have a mail pref option set to unanswered
			if (!isset($this->registration['sendEmail']) || $this->registration['sendEmail'] == '')
			{
				$this->registration['sendEmail'] = '-1';
			}
			?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_LEGEND_EMAIL_UPDATES'); ?></legend>

				<label for="sendEmail"<?php echo $fieldclass; ?>>
					<?php echo Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES'); ?> <?php echo ($this->registrationOptIn == Field::STATE_REQUIRED) ? '<span class="required">' . Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''; ?>
					<select name="sendEmail">
						<?php foreach ($options as $key => $value) { ?>
							<option <?php echo ($key == $this->registration['sendEmail']) ? 'selected="selected"' : ''; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
						<?php } ?>
					</select>
				</label>
				<?php echo $message; ?>
			</fieldset><div class="clear"></div>
		<?php } ?>


		<?php if ($this->registrationCAPTCHA != Field::STATE_HIDDEN) { ?>
			<?php
			$required = ($this->registrationCAPTCHA == Field::STATE_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (isset($this->xregistration->_invalid['captcha']) && !empty($this->xregistration->_invalid['captcha'])) ? '<span class="error">' . $this->xregistration->_invalid['captcha'] . '</span>' : '';

			$captchas = Event::trigger('captcha.onDisplay');

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

		<?php if ($this->registrationTOU != Field::STATE_HIDDEN) { ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_MEMBERS_REGISTER_TERMS_AND_CONDITIONS'); ?></legend>

				<label for="usageAgreement"<?php echo (!empty($this->xregistration->_invalid['usageAgreement'])) ? ' class="fieldWithErrors"' : ''; ?>>
					<input type="checkbox" class="option" id="usageAgreement" value="1" name="usageAgreement"<?php if ($this->registration['usageAgreement']) { echo ' checked="checked"'; } ?>/>
					<?php echo ($this->registrationTOU == Field::STATE_REQUIRED) ? '<span class="required">'.Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
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
