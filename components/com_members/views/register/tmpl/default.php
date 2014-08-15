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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

$this->css('register')
     ->js('register');

// get return url
$form_redirect = '';
if ($form_redirect = JRequest::getVar('return', '', 'get'))
{
	// urldecode is due to round trip XSS protection added to this field, see ticket 1411
	$form_redirect = urldecode($form_redirect);
}
?>
<header id="content-header">
	<h2><?php echo JText::_('COM_MEMBERS_REGISTER_'.strtoupper($this->task)); ?></h2>
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

			if (!JRequest::getVar('update', false, 'post'))
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
					<p>Passwords can be changed with <a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&task=changepassword'); ?>" title="Change password form">this form</a>.</p>
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

	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&' . ($this->task == 'create' ? 'return=' . $form_redirect : 'task=' . $this->task)); ?>" method="post" id="hubForm">

		<?php
		if ($this->task == 'create' && empty($this->xregistration->_invalid) && empty($this->xregistration->_missing))
		{
			// Check to see if third party auth plugins are enabled
			$plugins        = JPluginHelper::getPlugin('authentication');
			$authenticators = array();

			foreach ($plugins as $p)
			{
				if ($p->name != 'hubzero')
				{
					$pparams = new JRegistry($p->params);
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
						<?php foreach ($authenticators as $a) { ?>
							<a class="<?php echo $a['name']; ?> account" href="<?php echo JRoute::_('index.php?option=com_users&view=login&authenticator=' . $a['name']); ?>">
								<div class="signin">Sign in with <?php echo $a['display']; ?></div>
							</a>
						<?php } ?>
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
				<p class="submit"><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>" class="btn btn-danger">Email Existing Account Information</a>
				<p>If you are aware you already have another account registered to this email address, and are requesting another account because you need more resources, <?php echo $this->sitename; ?> would be happy to work with you to raise your resource limits instead:</p>
				<p class="submit"><a href="<?php echo JRoute::_('index.php?option=com_support&controller=tickets&task=new'); ?>" class="btn btn-danger">Submit Request to Raise Existing Limits</a></p>
			</div>
		<?php } ?>

		<?php
		if (!empty($this->xregistration->_invalid) || !empty($this->xregistration->_missing))
		{
			$html .= '<div class="error">Please correct the indicated invalid fields in the form below.';

			if ($this->showMissing && !empty($this->xregistration->_missing))
			{
				if ($this->task == 'update') {
					$html .= '<br />We are missing some vital information regarding your account! Please confirm the information below so we can better serve you. Thank you!';
				} else {
					$html .= '<br />Missing required information:';
				}
				$html .= '<ul>'."\n";
				foreach ($this->xregistration->_missing as $miss) {
					$html .= ' <li>'. $miss .'</li>'."\n";
				}
				$html .= '</ul>'."\n";
			}

			$html .= '</div>'."\n";
		}
		?>

		<?php if ($this->registrationUsername != REG_HIDE || $this->registrationPassword != REG_HIDE) { // Login information ?>
			<div class="explaination">
				<p><?php echo JText::_('COM_MEMBERS_REGISTER_CANNOT_CHANGE_USERNAME'); ?></p>

				<?php if ($this->task == 'create' || $this->task == 'proxycreate') { ?>
					<p><?php echo JText::_('COM_MEMBERS_REGISTER_PASSWORD_CHANGE_HINT'); ?></p>
				<?php } ?>
			</div>

			<fieldset>
				<legend><?php echo JText::_('COM_MEMBERS_REGISTER_LOGIN_INFORMATION'); ?></legend>

				<?php if ($this->registrationUsername == REG_READONLY) { ?>
					<label for="login">
						<?php JText::_('COM_MEMBERS_REGISTER_USER_LOGIN'); ?>: <br />
						<?php echo $this->escape($this->registration['login']); ?>
						<input name="login" id="login" type="hidden" value="<?php echo $this->escape($this->registration['login']); ?>" />
					</label>
				<?php } else if ($this->registrationUsername != REG_HIDE) { ?>
					<div class="grid">
						<div class="col span6">
							<label for="userlogin" <?php echo (!empty($this->xregistration->_invalid['login']) ? 'class="fieldWithErrors"' : ''); ?>>
								<?php echo JText::_('COM_MEMBERS_REGISTER_USER_LOGIN'); ?>: <?php echo ($this->registrationUsername == REG_REQUIRED ? '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''); ?>
								<input name="login" id="userlogin" type="text" maxlength="32" value="<?php echo $this->escape($this->registration['login']); ?>" />
								<?php echo (!empty($this->xregistration->_invalid['login']) ? '<span class="error">' . $this->xregistration->_invalid['login'] . '</span>' : ''); ?>
							</label>
						</div>
						<div class="col span6 omega">
							<p class="hint" id="usernameHint"><?php echo JText::_('COM_MEMBERS_REGISTER_USERNAME_HINT'); ?></p>
						</div>
					</div>
				<?php } ?>

				<?php if ($this->registrationPassword != REG_HIDE) { ?>
					<div class="grid">
						<div class="col span<?php echo ($this->registrationConfirmPassword != REG_HIDE ? '6' : '12'); ?>">
							<label<?php echo (!empty($this->xregistration->_invalid['password']) && !is_array($this->xregistration->_invalid['password'])
											? ' class="fieldWithErrors"'
											: ''); ?>>
								<?php echo JText::_('COM_MEMBERS_REGISTER_PASSWORD'); ?>: <?php if ($this->registrationPassword == REG_REQUIRED) { echo '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>'; } ?>
								<input name="password" id="password" type="password" value="<?php echo $this->escape($this->registration['password']); ?>" />
								<?php echo (!empty($this->xregistration->_invalid['password']) && !is_array($this->xregistration->_invalid['password'])
											? '<span class="error">' . $this->xregistration->_invalid['password'] . '</span>'
											: ''); ?>
							</label>
						</div>
						<?php if ($this->registrationConfirmPassword != REG_HIDE) { ?>
							<div class="col span6 omega">
								<label<?php echo (!empty($this->xregistration->_invalid['confirmPassword']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo JText::_('COM_MEMBERS_REGISTER_CONFIRM_PASSWORD'); ?>: <?php if ($this->registrationConfirmPassword == REG_REQUIRED) { echo '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>'; } ?>
									<input name="password2" id="password2" type="password" value="<?php echo $this->escape($this->registration['confirmPassword']); ?>" />
									<?php echo (!empty($this->xregistration->_invalid['confirmPassword']) ? '<span class="error">' . $this->xregistration->_invalid['confirmPassword'] . '</span>' : ''); ?>
								</label>
							</div>
						<?php } ?>
					</div>
					<?php if (count($this->password_rules) > 0) { ?>
						<ul>
							<?php foreach ($this->password_rules as $rule)
							{
								if (!empty($rule))
								{
									$err = '';
									if (!empty($this->xregistration->_invalid['password']) && is_array($this->xregistration->_invalid['password']))
									{
										$err = in_array($rule, $this->xregistration->_invalid['password']);
									}

									echo '<li' . ($err ? ' class="error"' : '') . '>' . $rule . '</li>' . "\n";
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

				<?php if ($this->config->get('passwordMeter')) { ?>
					<input type="hidden" id="passmeter" value="on" />
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
					<p><?php echo JText::_('COM_MEMBERS_REGISTER_ACTIVATION_EMAIL_HINT'); ?></p>
				<?php } ?>
				<p><?php echo JText::_('COM_MEMBERS_REGISTER_PRIVACY_HINT'); ?></p>
			</div>

			<fieldset>
				<legend><?php echo JText::_('COM_MEMBERS_REGISTER_CONTACT_INFORMATION'); ?></legend>

				<?php if ($this->registrationFullname != REG_HIDE) { ?>
					<?php
					$required = ($this->registrationFullname == REG_REQUIRED) ? '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
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
							<label<?php echo $fieldclass; ?>>
								<?php echo JText::_('COM_MEMBERS_REGISTER_FIRST_NAME'); ?>: <?php echo $required; ?>
								<input type="text" name="name[first]" id="first-name" value="<?php echo $this->escape(trim($givenName)); ?>" />
							</label>
						</div>
						<div class="col span4">
							<label>
								<?php echo JText::_('COM_MEMBERS_REGISTER_MIDDLE_NAME'); ?>:
								<input type="text" name="name[middle]" id="middle-name" value="<?php echo $this->escape(trim($middleName)); ?>" />
							</label>
						</div>
						<div class="col span4 omega">
							<label<?php echo $fieldclass; ?>>
								<?php echo JText::_('COM_MEMBERS_REGISTER_LAST_NAME'); ?>:
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
								<label<?php echo (!empty($this->xregistration->_invalid['email']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo JText::_('COM_MEMBERS_REGISTER_VALID_EMAIL'); ?>: <?php echo ($this->registrationEmail == REG_REQUIRED ? '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''); ?>
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
								<label<?php echo (!empty($this->xregistration->_invalid['confirmEmail']) ? ' class="fieldWithErrors"' : ''); ?>>
									<?php echo JText::_('COM_MEMBERS_REGISTER_CONFIRM_EMAIL'); ?>: <?php echo ($this->registrationConfirmEmail == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
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
							$usersConfig    = JComponentHelper::getParams('com_users');
							$useractivation = $usersConfig->get('useractivation', 1);
							if ($useractivation != 0) { ?>
								<p class="warning"><?php echo JText::sprintf('COM_MEMBERS_REGISTER_YOU_MUST_CONFIRM_EMAIL', \Hubzero\Utility\String::obfuscate($this->jconfig->getValue('config.mailfrom'))); ?></p>
							<?php } ?>
						<?php } else { ?>
							<p class="warning">Important! If you change your e-mail address you <strong>must</strong> confirm receipt of the confirmation e-mail from <?php echo \Hubzero\Utility\String::obfuscate($this->jconfig->getValue('config.mailfrom')); ?> in order to re-activate your account.</p>
						<?php } ?>
					<?php } ?>
				<?php } ?>

				<?php if ($this->registrationORCID != REG_HIDE) { ?>
					<div class="grid">
						<div class="col span9">
							<label<?php echo (!empty($this->xregistration->_invalid['orcid']) ? ' class="fieldWithErrors"' : ''); ?>>
								<?php echo JText::_('COM_MEMBERS_ORCID'); ?>: <?php echo ($this->registrationORCID == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
								<input name="orcid" id="orcid" type="text" value="<?php echo $this->escape($this->registration['orcid']); ?>" />
								<?php echo (!empty($this->xregistration->_invalid['orcid'])) ? '<span class="error">' . $this->xregistration->_invalid['orcid'] . '</span>' : ''; ?>
							</label>
						</div>
						<div class="col span3 omega">
							<a class="btn icon-search" id="orcid-fetch" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=orcid'); ?>"><?php echo JText::_('COM_MEMBERS_REGISTER_FIND_ID'); ?></a>
						</div>
					</div>
					<p><img src="<?php echo $this->img('orcid-logo.png'); ?>" width="80" alt="ORCID" /> <?php echo JText::_('COM_MEMBERS_ORCID_EXPLANATION'); ?></p>
				<?php } ?>

				<?php if ($this->registrationURL != REG_HIDE) { ?>
					<label<?php echo (!empty($this->xregistration->_invalid['web']) ? ' class="fieldWithErrors"' : ''); ?>>
						<?php echo JText::_('Website URL'); ?>: <?php echo ($this->registrationURL == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<input name="web" id="web" type="text" value="<?php echo $this->escape($this->registration['web']); ?>" placeholder="http://" />
						<?php echo (!empty($this->xregistration->_invalid['web'])) ? '<span class="error">' . $this->xregistration->_invalid['web'] . '</span>' : ''; ?>
					</label>
				<?php } ?>

				<?php if ($this->registrationPhone != REG_HIDE) { ?>
					<label<?php echo (!empty($this->xregistration->_invalid['phone']) ? ' class="fieldWithErrors"' : ''); ?>>
						<?php echo JText::_('Telephone (###-###-####)'); ?>: <?php echo ($this->registrationPhone == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
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
				<p>By providing this information you are helping us target our efforts to our users. We will <em>not</em> disclose your personal information to others unless required by law
				<?php if ($this->registrationEmployment != REG_HIDE || $this->registrationOrganization != REG_HIDE ) { ?>
					, and we will <em>not</em> contact your employer
				<?php } ?>
				</p>
				<?php if ($this->registrationCitizenship != REG_HIDE
				 || $this->registrationResidency != REG_HIDE
				 || $this->registrationSex != REG_HIDE
				 || $this->registrationDisability != REG_HIDE
				) { ?>
					<p>We operate as a community service and are committed to serving a diverse population of users. This information helps us assess our progress towards that goal.</p>
				<?php } ?>
			</div>

			<fieldset>
				<legend><?php echo JText::_('Personal Information'); ?></legend>

				<?php if ($this->registrationEmployment != REG_HIDE) { ?>
					<?php
					$message = (!empty($this->xregistration->_invalid['orgtype'])) ? '<span class="error">' . $this->xregistration->_invalid['orgtype'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

					include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'organizationtype.php');
					$database = JFactory::getDBO();
					$rot = new MembersTableOrganizationType($database);
					$types = $rot->getTypes();

					if (!$types || count($types) <= 0)
					{
						$types = array(
							'universityundergraduate' => 'University / College Undergraduate',
							'universitygraduate'      => 'University / College Graduate Student',
							'universityfaculty'       => 'University / College Faculty', // university
							'universitystaff'         => 'University / College Staff',
							'precollegestudent'       => 'K-12 (Pre-College) Student',
							'precollegefacultystaff'  => 'K-12 (Pre-College) Faculty/Staff', // precollege
							'nationallab'             => 'National Laboratory',
							'industry'                => 'Industry / Private Company',
							'government'              => 'Government Agency',
							'military'                => 'Military',
							'unemployed'              => 'Retired / Unemployed'
						);
					}
					?>
					<label<?php echo $fieldclass; ?>>
						<?php echo JText::_('Employment Type'); ?>: <?php echo ($this->registrationEmployment == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<select name="orgtype" id="orgtype">
							<?php if (empty($this->registration['orgtype']) || !empty($this->xregistration->_invalid['orgtype'])) { ?>
								<option value="" selected="selected"><?php echo JText::_('COM_MEMBERS_REGISTER_FORM_SELECT_FROM_LIST'); ?></option>
							<?php } ?>
							<?php foreach ($types as $type => $title) { ?>
								<option value="<?php echo $type; ?>"<?php if ($this->registration['orgtype'] == $type) { echo ' selected="selected"'; } ?>><?php echo $title; ?></option>
							<?php } ?>
						</select>
						<?php echo ($message) ? "\t\t\t\t" . $message . "\n" : ''; ?>
					</label>
				<?php } ?>

				<?php if ($this->registrationOrganization != REG_HIDE) { ?>
					<?php
					$orgtext = $this->registration['org'];
					$org_known = 0;

					include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'organization.php');
					$database = JFactory::getDBO();
					$xo = new MembersTableOrganization($database);
					$orgs = $xo->getOrgs();

					if (!$orgs || count($orgs) <= 0)
					{
						$orgs[0] = 'Purdue University';
						$orgs[1] = 'University of Pennsylvania';
						$orgs[2] = 'University of California at Berkeley';
						$orgs[3] = 'Vanderbilt University';
					}

					foreach ($orgs as $org)
					{
						if ($org == $this->registration['org'])
						{
							$org_known = 1;
						}
					}

					$message = (!empty($this->xregistration->_invalid['org'])) ? '<span class="error">' . $this->xregistration->_invalid['org'] . '</span>' : '';
					?>
					<label<?php echo ($message) ? ' class="fieldWithErrors"' : ''; ?>>
						<?php echo JText::_('Organization or School'); ?>: <?php echo ($this->registrationOrganization == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : '';; ?>
						<select name="org" id="org">
							<option value=""<?php if (!$org_known) { echo ' selected="selected"'; } ?>><?php echo ($org_known) ? JText::_('(other / none)') : JText::_('COM_MEMBERS_REGISTER_FORM_SELECT_OR_ENTER'); ?></option>
							<?php foreach ($orgs as $org) { ?>
								<option value="<?php echo $this->escape($org); ?>"<?php if ($org == $this->registration['org']) { $orgtext = ''; echo ' selected="selected"'; } ?>><?php echo $this->escape($org); ?></option>
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

					$reasons = array(
						'Required for class',
						'Developing a new course',
						'Using in an existing course',
						'Using simulation tools for research',
						'Using as background for my research',
						'Learning about subject matter',
						'Keeping current in subject matter'
					);
					$otherreason = '';
					?>
					<label<?php echo $fieldclass; ?>>
						<?php echo JText::_('Reason for Account'); ?>: <?php echo ($this->registrationReason == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<select name="reason" id="reason">
						<?php if (!in_array($this->registration['reason'], $reasons)) { ?>
							<option value="" selected="selected"><?php echo JText::_('COM_MEMBERS_REGISTER_FORM_SELECT_OR_ENTER'); ?></option>
						<?php } ?>
						<?php foreach ($reasons as $reason) { ?>
							<option value="<?php echo $reason; ?>"<?php if ($this->registration['reason'] == $reason) { echo ' selected="selected"'; } ?>><?php echo JText::_($reason); ?></option>
						<?php } ?>
						</select>
					</label>
					<input name="reasontxt" id="reasontxt" type="text" value="<?php echo $this->escape($this->registration['reason']); ?>" />
					<?php echo ($message) ? $message . "\n" : ''; ?>
				<?php } ?>

				<?php if ($this->registrationInterests != REG_HIDE) { ?>
					<label<?php echo (!empty($this->xregistration->_invalid['interests'])) ? ' class="fieldWithErrors"' : ''; ?>>
						<?php echo JText::_('What are you interested in?'); ?>: <?php echo ($this->registrationInterests == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
						<?php
						JPluginHelper::importPlugin('hubzero');
						$dispatcher = JDispatcher::getInstance();
						$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'interests', 'actags','',stripslashes($this->registration['interests']))));

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
				<?php if ($this->registrationHispanic != REG_HIDE) { ?>
					<p>All users are asked to clarify if they are of Hispanic origin or descent.
				<?php } ?>
				<?php if ($this->registrationRace != REG_HIDE) { ?>
					, but only United States citizens and Permanent Resident Visa holders need answer the next section
				<?php } ?>
				<?php if ($this->registrationHispanic != REG_HIDE) { ?>
					</p>
				<?php } ?>
				<p>Please provide this information if you feel comfortable doing so. This information will not affect the level of service you receive.</p>
			</div>
			<fieldset>
				<legend><?php echo JText::_('Demographics'); ?></legend>

				<?php if ($this->registrationCitizenship != REG_HIDE) { ?>
					<?php
					$message = (!empty($this->xregistration->_invalid['countryorigin'])) ? '<span class="error">' . $this->xregistration->_invalid['countryorigin'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

					if (!$this->registration['countryorigin'])
					{
						$userCountry = \Hubzero\Geocode\Geocode::ipcountry(JRequest::ip());
						$this->registration['countryorigin'] = $userCountry;
					}
					?>
					<fieldset<?php echo $fieldclass; ?>>
						<legend>
							Are you a Legal Citizen or Permanent Resident of the <abbr title="United States">US</abbr>?
							<?php echo ($this->registrationCitizenship == REG_REQUIRED) ? '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''; ?>
						</legend>

						<?php echo ($message) ? $message . "\n" : ''; ?>

						<label>
							<input type="radio" class="option" name="corigin_us" id="corigin_usyes" value="yes"<?php if (strcasecmp($this->registration['countryorigin'],'US') == 0) { echo ' checked="checked"'; } ?> />
							<?php echo JText::_('COM_MEMBERS_REGISTER_FORM_YES'); ?>
						</label>

						<label>
							<input type="radio" class="option" name="corigin_us" id="corigin_usno" value="no"<?php if (!empty($this->registration['countryorigin']) && (strcasecmp($this->registration['countryorigin'], 'US') != 0)) { echo ' checked="checked"'; } ?> />
							<?php echo JText::_('COM_MEMBERS_REGISTER_FORM_NO'); ?>
						</label>

						<label>
							<?php echo JText::_('Citizen or Permanent Resident of'); ?>:
							<select name="corigin" id="corigin">
								<?php if (!$this->registration['countryorigin'] || $this->registration['countryorigin'] == 'US') { ?>
									<option value=""><?php echo JText::_('COM_MEMBERS_REGISTER_FORM_SELECT_FROM_LIST'); ?></option>
								<?php } ?>
								<?php
								$countries = \Hubzero\Geocode\Geocode::getcountries();
								if ($countries)
								{
									foreach ($countries as $country)
									{
										?>
										<option value="<?php echo $country['code']; ?>"<?php if ($this->registration['countryorigin'] == $country['code']) { echo ' selected="selected"'; } ?>><?php echo $this->escape($country['name']); ?></option>
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
							$userCountry = \Hubzero\Geocode\Geocode::ipcountry(JRequest::ip());
						}
						$this->registration['countryresident'] = $userCountry;
					}
					?>
					<fieldset<?php echo $fieldclass; ?>>
						<legend>
							<?php echo JText::_('Do you Currently Live in the <abbr title="United States">US</abbr>?'); ?>
							<?php echo ($this->registrationResidency == REG_REQUIRED) ? '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : ''; ?>
						</legend>

						<?php echo ($message) ? $message . "\n" : ''; ?>

						<label>
							<input type="radio" class="option" name="cresident_us" id="cresident_usyes" value="yes"<?php if (strcasecmp($this->registration['countryresident'], 'US') == 0) { echo ' checked="checked"'; } ?> />
							<?php echo JText::_('COM_MEMBERS_REGISTER_FORM_YES'); ?>
						</label>

						<label>
							<input type="radio" class="option" name="cresident_us" id="cresident_usno" value="no"<?php if (!empty($this->registration['countryresident']) && strcasecmp($this->registration['countryresident'], 'US') != 0) { echo ' checked="checked"'; } ?> />
							<?php echo JText::_('COM_MEMBERS_REGISTER_FORM_NO'); ?>
						</label>

						<label>
							<?php echo JText::_('Currently Living in'); ?>:
							<select name="cresident" id="cresident">
								<?php if (!$this->registration['countryresident'] || strcasecmp($this->registration['countryresident'], 'US') == 0) { ?>
									<option value=""><?php echo JText::_('COM_MEMBERS_REGISTER_FORM_SELECT_FROM_LIST'); ?></option>
								<?php } ?>
								<?php
								if (!isset($countries) || !$countries)
								{
									$countries = \Hubzero\Geocode\Geocode::getcountries();
								}
								if ($countries)
								{
									foreach ($countries as $country)
									{
										?>
										<option value="<?php echo $country['code']; ?>"<?php if (strcasecmp($this->registration['countryresident'], $country['code']) == 0) { echo ' selected="selected"'; } ?>><?php echo $this->escape($country['name']); ?></option>
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
						<legend><?php echo JText::_('COM_MEMBERS_REGISTER_FORM_GENDER'); ?>: <?php echo ($this->registrationSex == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?></legend>
						<?php echo (!empty($this->xregistration->_invalid['sex'])) ? '<span class="error">' . $this->xregistration->_invalid['sex'] . '</span>' : ''; ?>
						<input type="hidden" name="sex" value="unspecified" />
						<label><input class="option" type="radio" name="sex" valie="male"<?php echo ($this->registration['sex'] == 'male' ? ' checked="checked"' : ''); ?> /> <?php echo JText::_('COM_MEMBERS_REGISTER_FORM_MALE'); ?></label>
						<label><input class="option" type="radio" name="sex" valie="female"<?php echo ($this->registration['sex'] == 'female' ? ' checked="checked"' : ''); ?> /> <?php echo JText::_('COM_MEMBERS_REGISTER_FORM_FEMALE'); ?></label>
						<label><input class="option" type="radio" name="sex" valie="refused"<?php echo ($this->registration['sex'] == 'refused' ? ' checked="checked"' : ''); ?> /> <?php echo JText::_('COM_MEMBERS_REGISTER_FORM_REFUSED'); ?></label>
					</fieldset>
				<?php } ?>

				<?php if ($this->registrationDisability != REG_HIDE) { ?>
					<?php
					$required = ($this->registrationDisability == REG_REQUIRED) ? '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
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
						<legend><?php echo JText::_('Disability'); ?>: <?php echo $required; ?></legend>
						<?php echo ($message) ? $message : ''; ?>

						<label>
							<input type="radio" class="option" name="disability" id="disabilityyes" value="yes"<?php if ($disabilityyes) { echo ' checked="checked"'; } ?> />
							<?php echo JText::_('Yes'); ?>
						</label>

						<fieldset>
							<label>
								<input type="checkbox" class="option" name="disabilityblind" id="disabilityblind" <?php if (in_array('blind', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo JText::_('Blind / Visually Impaired'); ?>
							</label>

							<label>
								<input type="checkbox" class="option" name="disabilitydeaf" id="disabilitydeaf" <?php if (in_array('deaf', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo JText::_('Deaf / Hard of Hearing'); ?>
							</label>

							<label>
								<input type="checkbox" class="option" name="disabilityphysical" id="disabilityphysical" <?php if (in_array('physical', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo JText::_('Physical / Orthopedic Disability'); ?>
							</label>

							<label>
								<input type="checkbox" class="option" name="disabilitylearning" id="disabilitylearning" <?php if (in_array('learning', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo JText::_('Learning / Cognitive Disability'); ?>
							</label>

							<label>
								<input type="checkbox" class="option" name="disabilityvocal" id="disabilityvocal" <?php if (in_array('vocal', $this->registration['disability'])) { echo 'checked="checked" '; } ?>/>
								<?php echo JText::_('Vocal / Speech Disability'); ?>
							</label>

							<label>
								<?php echo JText::_('Other (please specify)'); ?>:
								<input name="disabilityother" id="disabilityother" type="text" value="<?php echo $this->escape($disabilityother); ?>" />
							</label>
						</fieldset>

						<label>
							<input type="radio" class="option" name="disability" id="disabilityno" value="no"<?php if (in_array('no', $this->registration['disability'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('No (none)'); ?>
						</label>

						<label>
							<input type="radio" class="option" name="disability" id="disabilityrefused" value="refused"<?php if (in_array('refused', $this->registration['disability'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('Do not wish to reveal'); ?>
						</label>
					</fieldset>
				<?php } ?>

				<?php if ($this->registrationHispanic != REG_HIDE) { ?>
					<?php
					$required = ($this->registrationHispanic == REG_REQUIRED) ? '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
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
						<legend>Hispanic or Latino (<a class="popup 700x500" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=raceethnic'); ?>">more information</a>) <?php echo $required; ?></legend>
						<?php echo $message; ?>

						<label>
							<input type="radio" class="option" name="hispanic" id="hispanicyes" value="yes" <?php if ($hispanicyes) { echo 'checked="checked"'; } ?> />
							<?php echo JText::_('Yes (Hispanic Origin or Descent)'); ?>
						</label>

						<fieldset>
							<label>
								<input type="checkbox" class="option" name="hispaniccuban" id="hispaniccuban" <?php if (in_array('cuban', $this->registration['hispanic'])) { echo 'checked="checked"'; } ?> />
								<?php echo JText::_('Cuban'); ?>
							</label>

							<label>
								<input type="checkbox" class="option" name="hispanicmexican" id="hispanicmexican" <?php if (in_array('mexican', $this->registration['hispanic'])) { echo 'checked="checked"'; } ?> />
								<?php echo JText::_('Mexican American or Chicano'); ?>
							</label>

							<label>
								<input type="checkbox" class="option" name="hispanicpuertorican" id="hispanicpuertorican" <?php if (in_array('puertorican', $this->registration['hispanic'])) { echo 'checked="checked"'; } ?> />
								<?php echo JText::_('Puerto Rican'); ?>
							</label>

							<label>
								<?php echo JText::_('Other Hispanic or Latino'); ?>:
								<input name="hispanicother" id="hispanicother" type="text" value="<?php echo $this->escape($hispanicother); ?>" />
							</label>
						</fieldset>

						<label>
							<input type="radio" class="option" name="hispanic" id="hispanicno" value="no"<?php if (in_array('no', $this->registration['hispanic'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('No (not Hispanic or Latino)'); ?>
						</label>

						<label>
							<input type="radio" class="option" name="hispanic" id="hispanicrefused" value="refused"<?php if (in_array('refused', $this->registration['hispanic'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('Do not wish to reveal'); ?>
						</label>
					</fieldset>
				<?php } ?>

				<?php if ($this->registrationRace != REG_HIDE) { ?>
					<?php
					$required = ($this->registrationRace == REG_REQUIRED) ? '<span class="required">' . JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED') . '</span>' : '';
					$message = (!empty($this->xregistration->_invalid['race'])) ? '<span class="error">' . $this->xregistration->_invalid['race'] . '</span>' : '';
					$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

					if (!is_array($this->registration['race']))
					{
						$this->registration['race'] = array(trim($this->registration['race']));
					}
					?>
					<fieldset<?php echo $fieldclass; ?>>
						<legend>U.S. Citizens and Permanent Residents Only (<a class="popup 675x678" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=raceethnic'); ?>">more information</a>) <?php echo $required; ?></legend>

						<p class="hint"><?php echo JText::_('Select one or more that apply.'); ?></p>

						<label>
							<input type="checkbox" class="option" name="racenativeamerican" id="racenativeamerican" value="nativeamerican"<?php if (in_array('nativeamerican', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('American Indian or Alaska Native'); ?>
						</label>

						<label class="indent">
							<?php echo JText::_('Tribal Affiliation(s)'); ?>:
							<input name="racenativetribe" id="racenativetribe" type="text" value="<?php echo $this->escape($this->registration['nativetribe']); ?>" />
						</label>

						<label>
							<input type="checkbox" class="option" name="raceasian" id="raceasian"<?php if (in_array('asian', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('Asian'); ?>
						</label>

						<label>
							<input type="checkbox" class="option" name="raceblack" id="raceblack"<?php if (in_array('black', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('Black or African American'); ?>
						</label>

						<label>
							<input type="checkbox" class="option" name="racehawaiian" id="racehawaiian"<?php if (in_array('hawaiian', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('Native Hawaiian or Other Pacific Islander'); ?>
						</label>

						<label>
							<input type="checkbox" class="option" name="racewhite" id="racewhite"<?php if (in_array('white', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('White'); ?>
						</label>

						<label>
							<input type="checkbox" class="option" name="racerefused" id="racerefused"<?php if (in_array('refused', $this->registration['race'])) { echo ' checked="checked" '; } ?>/>
							<?php echo JText::_('Do not wish to reveal'); ?>
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
				'-1' => '- Select email option &mdash;',
				'1'  => 'Yes, send me emails',
				'0'  => 'No, don\'t send me emails'
			);

			//if we dont have a mail pref option set to unanswered
			if (!isset($this->registration['mailPreferenceOption']) || $this->registration['mailPreferenceOption'] == '')
			{
				$this->registration['mailPreferenceOption'] = '-1';
			}
			?>
			<fieldset>
				<legend><?php echo JText::_('Receive Email Updates'); ?></legend>

				<label for="mailPreferenceOption"<?php echo $fieldclass; ?>>
					Would you like to receive email updates (newsletters, etc.)? <?php echo ($this->registrationOptIn == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
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
			$required = ($this->registrationCAPTCHA == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : '';
			$message = (isset($this->xregistration->_invalid['captcha']) && !empty($this->xregistration->_invalid['captcha'])) ? '<span class="error">' . $this->xregistration->_invalid['captcha'] . '</span>' : '';

			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher = JDispatcher::getInstance();
			$captchas = $dispatcher->trigger( 'onGetCaptcha' );

			if (count($captchas) > 0) { ?>
				<fieldset>
					<legend><?php echo JText::_('Human Check'); ?></legend>
					<?php echo ($message) ? $message : ''; ?>
			<?php } ?>

			<label id="botcheck-label" for="botcheck">
				<?php echo JText::_('Please leave this field blank.'); ?> <?php echo $required; ?>
				<input type="text" name="botcheck" id="botcheck" value="" />
			</label>

			<?php if (count($captchas) > 0) {
					echo implode("\n", $captchas); ?>
				</fieldset>
			<?php } ?>
		<?php } ?>

		<?php if ($this->registrationTOU != REG_HIDE) { ?>
			<fieldset>
				<legend><?php echo JText::_('COM_MEMBERS_REGISTER_TERMS_AND_CONDITIONS'); ?></legend>

				<label<?php echo (!empty($this->xregistration->_invalid['usageAgreement'])) ? ' class="fieldWithErrors"' : ''; ?>>
					<input type="checkbox" class="option" id="usageAgreement" value="1" name="usageAgreement"<?php if ($this->registration['usageAgreement']) { echo ' checked="checked"'; } ?>/>
					<?php echo ($this->registrationTOU == REG_REQUIRED) ? '<span class="required">'.JText::_('COM_MEMBERS_REGISTER_FORM_REQUIRED').'</span>' : ''; ?>
					<?php echo JText::_('Yes, I have read and agree to the <a class="popup 700x500" href="/legal/terms">Terms of Use</a>.'); ?>
				</label>

				<?php echo (!empty($this->xregistration->_invalid['usageAgreement'])) ? '<span class="error">' . $this->xregistration->_invalid['usageAgreement'] . '</span>' : ''; ?>
			</fieldset>
			<div class="clear"></div>
		<?php } else if ($this->registration['usageAgreement']) { ?>
			<input name="usageAgreement" type="hidden" id="usageAgreement" value="checked" />
			<div class="clear"></div>
		<?php } ?>

		<p class="submit">
			<input type="submit" name="<?php echo $this->task; ?>" value="<?php echo JText::_('COM_MEMBERS_REGISTER_BUTTON_'.strtoupper($this->task)); ?>" />
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="act" value="submit" />
		<?php echo JHTML::_( 'form.token' ); ?>

		<input type="hidden" name="base_uri" id="base_uri" value="<?php echo rtrim(JURI::base(true), '/'); ?>" />

		<input type="hidden" name="return" value="<?php echo urlencode($form_redirect); // urlencode is XSS protection added to this field, see ticket 1411 ?>" />
	</form>
</section><!-- / .main section -->
