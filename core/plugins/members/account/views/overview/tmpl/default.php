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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('providers.css', 'com_users')
     ->js()
     ->js('jquery.hoverIntent', 'system');
?>

<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT'); ?></h3>

<?php if (isset($this->notifications) && count($this->notifications) > 0) {
	foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } // close foreach
} // close if count ?>

<div id="members-account-section">

<?php if (count($this->domains_unused) > 0 || !empty($this->hzalaccounts[0])) { ?>
	<div class="sub-section">
		<h4><?php echo Lang::txt('PLG_MEMBERS_LINKED_ACCOUNTS'); ?></h4>
		<div class="clear"></div>
		<div class="sub-section-content auth">
			<?php
			if ($this->hzalaccounts)
			{
				echo "<h5>" . Lang::txt('PLG_MEMBERS_ACCOUNT_ACTIVE_PROVIDERS') . ":</h5>";
				foreach ($this->hzalaccounts as $hzala)
				{
					// Get the display name for the current plugin being used
					$plugin       = Plugin::byType('authentication', $hzala['auth_domain_name']);
					$pparams      = new \Hubzero\Config\Registry($plugin->params);
					$display_name = $pparams->get('display_name', ucfirst($hzala['auth_domain_name']));
					?>
					<div class="account active <?php echo $hzala['auth_domain_name']; ?>">
						<div class="x">
							<a title="<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_REMOVE_ACCOUNT'); ?>" href="<?php echo Route::url($this->member->link() . '&active=account&action=unlink&hzal_id=' . $hzala['id']); ?>">x</a>
						</div>
						<div class="account-info">
							<div class="account-type"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_ACCOUNT_TYPE'); ?>: <?php echo $display_name; ?></div>
						</div>
					</div>
					<?php
				}
			}

			echo '<div class="clear"></div>';

			if ($this->domains_unused)
			{
				echo '<h5>' . Lang::txt('PLG_MEMBERS_ACCOUNT_AVAILABLE_PROVIDERS') . ':</h5>';
				foreach ($this->domains_unused as $domain)
				{
					// Get the display name for the current plugin being used
					$plugin       = Plugin::byType('authentication', $domain->name);
					$pparams      = new \Hubzero\Config\Registry($plugin->params);
					$display_name = $pparams->get('display_name', ucfirst($domain->name));
					$refl         = new \ReflectionClass('plgauthentication' . $domain->name);
					?>

					<?php if ($refl->hasMethod('onRenderOption') && ($html = $refl->getMethod('onRenderOption')->invoke(NULL))) : ?>
						<?php echo is_array($html) ? implode("\n", $html) : $html; ?>
					<?php else : ?>
						<a href="<?php echo Route::url('index.php?option=com_users&view=login&authenticator=' . $domain->name); ?>">
							<div class="account inactive <?php echo $domain->name; ?>">
								<div class="account-info">
									<div class="account-type"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_ACCOUNT_TYPE'); ?>: <?php echo $display_name; ?></div>
								</div>
							</div>
						</a>
					<?php endif;
				}
			}
			?>
		</div><!-- / .sub-section-content -->
	</div><!-- / .sub-section -->
<?php } // close linked accounts subsection check ?>

	<div class="sub-section">
		<h4><?php
			if ($this->passtype == 'changelocal')
			{
				echo Lang::txt('PLG_MEMBERS_CHANGE_LOCAL_PASSWORD');
			}
			else if ($this->passtype == 'changehub')
			{
				echo Lang::txt('PLG_MEMBERS_CHANGE_HUB_PASSWORD');
			}
			else if ($this->passtype == 'set')
			{
				echo Lang::txt('PLG_MEMBERS_SET_LOCAL_PASSWORD');
			}
		?></h4>
		<div class="clear"></div>
		<div class="sub-section-content">
		<?php if ($this->passtype == 'changelocal' || $this->passtype == 'changehub') { ?>
			<form action="index.php" method="post" data-section-registation="password" data-section-profile="password">
				<?php if (is_array($this->passinfo)) { ?>
					<p class="<?php echo $this->passinfo['message_style']; ?>">
						<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_PASSWORD_EXPIRATION_EXPLANATION', $this->passinfo['diff'], $this->passinfo['max']); ?>
					</p>
				<?php } // close if is array passinfo ?>
				<p class="error" id="section-edit-errors"></p>
				<div id="password-group"<?php echo (count($this->password_rules) > 0) ? ' class="split-left"' : ""; ?>>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_CURRENT_PASSWORD'); ?> <input type="password" name="oldpass" id="oldpass" class="input-text" />
					</label>
					<label class="side-by-side pad-right">
						<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_NEW_PASSWORD'); ?> <input type="password" name="newpass" id="newpass1" class="input-text" />
					</label>
					<label class="side-by-side">
						<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_CONFIRM_NEW_PASSWORD'); ?> <input type="password" name="newpass2" id="newpass2" class="input-text" />
					</label>

					<input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_SAVE'); ?>" id="password-change-save" />
					<input type="reset" class="cancel" id="pass-cancel" value="<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_CANCEL'); ?>" />
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
								if (!empty($this->change) && is_array($this->change))
								{
									$err = in_array($rule, $this->change);
								}
								else
								{
									$err = '';
								}
								$mclass = ($err)  ? ' class="error"' : ' class="empty"';
								echo "<li $mclass>".$rule."</li>";
							}
						}
						if (!empty($this->change) && is_array($this->change))
						{
							foreach ($this->change as $msg)
							{
								if (!in_array($msg, $this->password_rules))
								{
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
				<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
				<input type="hidden" name="task" value="changepassword" />
				<input type="hidden" name="no_html" id="pass_no_html" value="0" />
				<?php echo Html::input('token'); ?>
			</form>
		<?php } else { ?>
			<p><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_LOCAL_PASS_EXPLANATION'); ?></p>
			<a href="<?php echo Route::url($this->member->link() . '&active=account&task=sendtoken'); ?>">
				<div id="token-button"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_REQUEST_TOKEN'); ?></div>
			</a>
		<?php } ?>
		</div><!-- / .sub-section-content -->
	</div><!-- / .sub-section -->

<?php if ($this->params->get('ssh_key_upload', 0)) : ?>
	<div class="sub-section">
		<h4><?php echo Lang::txt('PLG_MEMBERS_LOCAL_SERVICES'); ?></h4>
		<div class="clear"></div>
		<div class="sub-section-content">
			<h5><?php echo Lang::txt('PLG_MEMBERS_LOCAL_SERVICES_USERNAME'); ?></h5>
			<p>
				<?php echo Lang::txt('PLG_MEMBERS_LOCAL_SERVICES_USERNAME_DESC'); ?>
				<span class="local-services-username"><?php echo User::get('username'); ?></span>
			</p>
			<h5><?php echo Lang::txt('PLG_MEMBERS_MANAGE_KEYS'); ?></h5>
			<?php if ($this->key !== false) : ?>
				<form action="<?php echo Route::url($this->member->link() . '&active=account&task=uploadkey', true, true); ?>" method="post">
					<p><?php echo Lang::txt('PLG_MEMGERS_ACCOUNT_KEY_HINT'); ?>:</p>
					<textarea name="keytext" cols="50" rows="6"><?php echo $this->key; ?></textarea>
					<div class="clear"></div>
					<input type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_SUBMIT'); ?>" />
					<input type="reset" class="cancel" value="<?php echo Lang::txt('PLG_MEMBERS_CANCEL'); ?>" />
				</form>
			<?php else : ?>
				<p class="error"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_KEY_ERROR_ACCESSING_HOME_DIR'); ?></p>
			<?php endif; ?>
		</div><!-- / .sub-section-content -->
	</div><!-- / .sub-section -->
<?php endif; ?>
</div><!-- / .subject -->
<div class="clear"></div>