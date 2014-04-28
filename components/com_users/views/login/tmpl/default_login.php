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
$app = JFactory::getApplication();

$usersConfig =  JComponentHelper::getParams('com_users');
?>

<?php if ($this->params->get('show_page_title',1)) : ?>
<div id="content-header">
	<h2><?php echo $this->escape($this->params->get('page_heading')) ?></h2>
</div>
<?php endif; ?>

<?php
// Check for error messages (regular message queue)
if (!empty($error_message))
{
	echo '<p class="error">'. $error_message . '</p>';
}

// If an account is being linked, and the authenticator is hubzero, give a message
if (!$this->multiAuth && JRequest::getWord('authenticator') == 'hubzero')
{
	echo '<p class="warning">To link your two accounts, you need to login with your ' . $app->getCfg('sitename') . ' account.  You will only need to do this once.</p>';
}

?>

<div id="authentication" class="<?php echo ($this->multiAuth) ? 'multiAuth' : 'singleAuth'; ?>">
	<div class="error"></div>
	<div class="grid">
		<div id="inner" class="<?php echo ($this->multiAuth) ? 'multiAuth' : 'singleAuth'; ?>">
			<?php if($this->multiAuth) { // only display if we have third part auth plugins enabled ?>
				<div id="providers" class="col span-half">
					<h3>Sign in with your:</h2>
					<?php foreach($this->authenticators as $a) : ?>
						<div class="account-group-wrap">
							<a class="account-group" id="<?php echo $a['name']; ?>" href="<?php echo JRoute::_('index.php?option=com_users&view=login&authenticator=' . $a['name'] . $this->returnQueryString); ?>">
								<p><?php echo $a['display']; ?> account</p>
							</a>
							<a class="sign-out" href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&authenticator=' . $a['name'] . $this->returnQueryString); ?>">
								Not <span class="current-user"><?php echo (isset($this->status[$a['name']]['username'])) ? $this->status[$a['name']]['username'] : ''; ?></span>? Sign out.
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			<?php } // close if - check if any authentication plugins are enabled ?>
			<div id="credentials-hub" class="<?php echo ($this->multiAuth) ? 'col span-half omega' : 'singleAuth'; ?>">
				<div id="credentials-hub-inner">
					<h3><?php echo ($this->multiAuth) ? 'Your local hub account:' : 'Sign In:'; ?></h2>
					<form action="<?php echo JRoute::_('index.php', true, true); ?>" method="post" id="login_form">
						<div class="labelInputPair">
							<label for="username"><?php echo JText::_('Username or email'); ?>:</label>
							<a class="forgots forgot-username" href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('Lost username?');?></a>
							<input tabindex="1" type="text" name="username" id="username" placeholder="email or username" />
						</div>
						<div class="labelInputPair">
							<label for="password"><?php echo JText::_('Password'); ?>:</label>
							<a class="forgots forgot-password" href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('Forgot password?'); ?></a>
							<input tabindex="2" type="password" name="passwd" id="password" placeholder="password" />
						</div>
						<div class="submission">
						<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
							<input type="checkbox" class="option" name="remember" id="remember" value="yes" alt="Remember Me" <?php echo ($this->remember_me_default) ? 'checked="checked"' : ''; ?> />
							<label for="remember" id="remember-me-label"><?php echo JText::_('Keep me logged in?'); ?></label>
						<?php endif; ?>
						<input type="submit" value="Login" id="login-submit"/>
						</div>
						<div class="clear"></div>
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="authenticator" value="hubzero" />
						<input type="hidden" name="task" value="user.login" />
						<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
						<input type="hidden" name="freturn" value="<?php echo $this->freturn; ?>" />
						<?php echo JHTML::_('form.token'); ?>
					</form>
				</div>
			</div>
			<?php if (!$this->multiAuth && $usersConfig->get('allowUserRegistration') != '0') { ?>
				<p class="callToAction">Don't have an account? <a href="/register<?php if ($this->return) { echo '?return=' . $this->return; } ?>">Create one.</a></p>
			<?php } ?>
		</div>
	</div>
	<?php if ($this->multiAuth && $usersConfig->get('allowUserRegistration') != '0') { ?>
		<p class="callToAction">Or, you can <a href="/register<?php if ($this->return) { echo '?return=' . $this->return; } ?>">create a local account.</a></p>
	<?php } ?>
</div>
