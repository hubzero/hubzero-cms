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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<h3 class="section-header"><a name="account"></a><?php echo JText::_('PLG_MEMBERS_ACCOUNT'); ?></h3>
<?php if(isset($this->notifications) && count($this->notifications) > 0) {
	foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } // close foreach
} // close if count ?>
<div id="members-account-section">

<?php if(count($this->domains_unused) > 0 || !empty($this->hzalaccounts[0])) { ?>
	<div class="sub-section">
		<h4><?php echo JText::_('PLG_MEMBERS_LINKED_ACCOUNTS'); ?></h4>
		<div class="clear"></div>
		<div class="sub-section-content">
<?php 
		if($this->hzalaccounts)
		{
			echo "<h5>" . JText::_('PLG_MEMBERS_ACCOUNT_ACTIVE_PROVIDERS') . ":</h5>";
			foreach($this->hzalaccounts as $hzala)
			{
				// Get the display name for the current plugin being used
				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}
				$plugin       = JPluginHelper::getPlugin('authentication', $hzala['auth_domain_name']);
				$pparams      = new $paramsClass($plugin->params);
				$display_name = $pparams->get('display_name', ucfirst($hzala['auth_domain_name']));
?>
				<div class="account-group active" id="<?php echo $hzala['auth_domain_name']; ?>">
					<div class="x"><a title="<?php echo JText::_('PLG_MEMBERS_ACCOUNT_REMOVE_ACCOUNT'); ?>" href="<?php 
						echo JRoute::_('index.php?option=' .
							$this->option . '&id=' .
							$this->member->get('uidNumber') .
							'&active=account&action=unlink&hzal_id=' .
							$hzala['id']); ?>">x</a></div>
					<div class="account-type"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_ACCOUNT_TYPE'); ?>: <?php echo $display_name; ?></div>
					<div class="account-id"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_ACCOUNT_ID'); ?>: <?php echo $hzala['username']; ?></div>
				</div>
<?php
			}
		}

		echo '<div class="clear"></div>';

		if($this->domains_unused)
		{
			echo '<h5>' . JText::_('PLG_MEMBERS_ACCOUNT_AVAILABLE_PROVIDERS') . ':</h5>';
			foreach($this->domains_unused as $domain)
			{
				// Get the display name for the current plugin being used
				$paramsClass = 'JParameter';
				$com_user    = 'com_user';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
					$com_user    = 'com_users';
				}
				$plugin       = JPluginHelper::getPlugin('authentication', $domain->name);
				$pparams      = new $paramsClass($plugin->params);
				$display_name = $pparams->get('display_name', ucfirst($domain->name));
?>
				<a href="<?php echo JRoute::_('index.php?option=' . $com_user . '&view=login&authenticator=' . $domain->name); ?>">
					<div class="account-group inactive" id="<?php echo $domain->name; ?>">
						<div class="account-type"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_ACCOUNT_TYPE'); ?>: <?php echo $display_name; ?></div>
						<div class="account-id"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_CLICK_TO_LINK'); ?></div>
					</div>
				</a>
<?php
			}
		}
?>
		</div><!-- / .sub-section-content -->
	</div><!-- / .sub-section -->
<?php } // close linked accounts subsection check ?>
	<a name="password"></a>
	<div class="sub-section">
		<h4><?php 
			if($this->passtype == 'changelocal')
			{
				echo JText::_('PLG_MEMBERS_CHANGE_LOCAL_PASSWORD');
			}
			else if($this->passtype == 'changehub')
			{
				echo JText::_('PLG_MEMBERS_CHANGE_HUB_PASSWORD');
			}
			else if($this->passtype == 'set')
			{
				echo JText::_('PLG_MEMBERS_SET_LOCAL_PASSWORD');
			}
		?></h4>
		<div class="clear"></div>
		<div class="sub-section-content">
<?php if($this->passtype == 'changelocal' || $this->passtype == 'changehub')
{
?>
			<form action="index.php" method="post" data-section-registation="password" data-section-profile="password">
				<?php if(is_array($this->passinfo)) { ?>
					<p class="<?php echo $this->passinfo['message_style']; ?>">
						<?php echo JText::sprintf('PLG_MEMBERS_ACCOUNT_PASSWORD_EXPIRATION_EXPLANATION', $this->passinfo['diff'], $this->passinfo['max']); ?>
					</p>
				<?php } // close if is array passinfo ?>
				<p class="error" id="section-edit-errors"></p>
				<div id="password-group"<?php echo (count($this->password_rules) > 0) ? ' class="split-left"' : ""; ?>>
					<label>
						<?php echo JText::_('PLG_MEMBERS_ACCOUNT_CURRENT_PASSWORD'); ?> <input type="password" name="oldpass" id="oldpass" class="input-text" />
					</label>
					<label class="side-by-side pad-right">
						<?php echo JText::_('PLG_MEMBERS_ACCOUNT_NEW_PASSWORD'); ?> <input type="password" name="newpass" id="newpass1" class="input-text" />
					</label>
					<label class="side-by-side">
						<?php echo JText::_('PLG_MEMBERS_ACCOUNT_CONFIRM_NEW_PASSWORD'); ?> <input type="password" name="newpass2" id="newpass2" class="input-text" />
					</label>

					<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_ACCOUNT_SAVE'); ?>" id="password-change-save" /> 
					<input type="reset" class="cancel" id="pass-cancel" value="<?php echo JText::_('PLG_MEMBERS_ACCOUNT_CANCEL'); ?>" />
				</div>

				<?php
					if (count($this->password_rules) > 0) {
						echo '<div id="passrules-container">';
						echo '<div id="passrules-subcontainer">';
						echo '<h5>Password Rules</h5>';
						echo '<ul id="passrules">';
						foreach ($this->password_rules as $rule)
						{
							if (!empty($rule))
							{
								if (!empty($this->change) && is_array($this->change)) {
									$err = in_array($rule, $this->change);
								} else {
									$err = '';
								}
								$mclass = ($err)  ? ' class="error"' : ' class="empty"';
								echo "<li $mclass>".$rule."</li>";
							}
						}
						if (!empty($this->change) && is_array($this->change)) {
							foreach ($this->change as $msg)
							{
								if (!in_array($msg, $this->password_rules)) {
									echo '<li class="error">'.$msg."</li>";
								}
							}
						}
						echo "</ul>";
						echo "</div>";
						echo "</div>";
					}
				?>

				<input type="hidden" name="change" value="1" />
				<input type="hidden" name="option" value="com_members" />
				<input type="hidden" name="id" value="<?php echo $this->member->get("uidNumber"); ?>" />
				<input type="hidden" name="task" value="changepassword" />
				<input type="hidden" name="no_html" id="pass_no_html" value="0" />
			</form>
<?php } else { ?>
			<p><?php echo JText::_('PLG_MEMBERS_ACCOUNT_LOCAL_PASS_EXPLANATION'); ?></p>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option .
												'&id=' . $this->member->get('uidNumber') .
												'&active=account' .
												'&task=sendtoken'); ?>">
				<div id="token-button"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_REQUEST_TOKEN'); ?></div>
			</a>
<?php } ?>
		</div><!-- / .sub-section-content -->
	</div><!-- / .sub-section -->

<?php if ($this->params->get('ssh_key_upload', 0)) : ?>
	<div class="sub-section">
		<h4><?php echo JText::_('PLG_MEMBERS_LOCAL_SERVICES'); ?></h4>
		<div class="clear"></div>
		<div class="sub-section-content">
			<h5><?php echo JText::_('PLG_MEMBERS_LOCAL_SERVICES_USERNAME'); ?></h5>
			<p>
				<?php echo JText::_('PLG_MEMBERS_LOCAL_SERVICES_USERNAME_DESC'); ?>
				<span class="local-services-username"><?php echo JFactory::getUser()->get('username'); ?></span>
			</p>
			<h5><?php echo JText::_('PLG_MEMBERS_MANAGE_KEYS'); ?></h5>
			<?php if ($this->key !== false) : ?>
				<form action=<?php echo JRoute::_('index.php?option=' . $this->option .
													'&id=' . $this->member->get('uidNumber') .
													'&active=account' .
													'&task=uploadkey', true, true); ?> method="post">
					<p><?php echo JText::_('PLG_MEMGERS_ACCOUNT_KEY_HINT'); ?>:</p>
					<textarea name="keytext" cols="50" rows="6"><?php echo $this->key; ?></textarea>
					<div class="clear"></div>
					<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_SUBMIT'); ?>" />
					<input type="reset" class="cancel" value="<?php echo JText::_('PLG_MEMBERS_CANCEL'); ?>" />
				</form>
			<?php else : ?>
				<p class="error"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_KEY_ERROR_ACCESSING_HOME_DIR'); ?></p>
			<?php endif; ?>
		</div><!-- / .sub-section-content -->
	</div><!-- / .sub-section -->
<?php endif; ?>
</div><!-- / .subject -->
<div class="clear"></div>