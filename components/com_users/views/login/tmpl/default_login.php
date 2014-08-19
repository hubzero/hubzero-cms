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

$hash  = JUtility::getHash(JFactory::getApplication()->getName().':authenticator');
$crypt = new JSimpleCrypt();

if (($cookie = \Hubzero\Utility\Cookie::eat('authenticator')) && !JRequest::getInt('reset', false))
{
	$primary  = $cookie->authenticator;
	$user     = JFactory::getUser($cookie->user_id);
	$user_img = $cookie->user_img;
	JRequest::setVar('primary', $primary);
}

$app         = JFactory::getApplication();
$usersConfig = JComponentHelper::getParams('com_users');
$primary     = JRequest::getWord('primary', false);
?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
	<header id="content-header">
		<h2><?php echo $this->escape($this->params->get('page_heading')) ?></h2>
	</header>
<?php endif; ?>

	<div class="hz_user">

<?php if ($primary && $primary != 'hubzero') : ?>
	<a class="primary" href="<?php echo JRoute::_('index.php?option=com_users&view=login&authenticator=' . $primary . $this->returnQueryString); ?>">
		<div class="<?php echo $primary; ?> upper"></div>
		<div class="auth">
			<div class="person">
				<?php if (isset($user_img)) : ?>
					<img src="<?php echo $user_img; ?>" alt="<?php echo JText::_('User profile picture'); ?>" />
				<?php endif; ?>
			</div>
			<div class="lower">
				<div class="instructions"><?php echo JText::sprintf('Sign in with %s', ucfirst($primary)); ?></div>
			</div>
		</div>
	</a>
<?php else: ?>
	<div class="auth">
		<div class="person">
			<?php if (isset($user_img)) : ?>
				<img src="<?php echo $user_img; ?>" alt="<?php echo JText::_('User profile picture'); ?>" />
			<?php endif; ?>
		</div>
		<div class="default" style="display:<?php echo ($primary || count($this->authenticators) == 0) ? 'none' : 'block'; ?>;">
			<div class="instructions"><?php echo JText::_('Choose your sign in method:'); ?></div>
			<div class="options">
				<?php foreach ($this->authenticators as $a) : ?>
					<a class="<?php echo $a['name']; ?> account" href="<?php echo JRoute::_('index.php?option=com_users&view=login&authenticator=' . $a['name'] . $this->returnQueryString); ?>">
						<?php 
							$refl = new \ReflectionClass("plgAuthentication{$a['name']}");
							if ($refl->hasMethod('onRenderOption') && ($html = $refl->getMethod('onRenderOption')->invoke(NULL))):
								echo is_array($html) ? implode("\n", $html) : $html;
							else:
						?>
						<div class="signin"><?php echo JText::sprintf('Sign in with %s', $a['display']); ?></div>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
			<div class="or"></div>
			<div class="local">
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=login&primary=hubzero&reset=1' . $this->returnQueryString); ?>">
					<?php echo JText::sprintf('Sign in with your %s account', ((isset($this->site_display)) ? $this->site_display : $app->getCfg('sitename'))); ?>
				</a>
			</div>
		</div>
		<div class="hz" style="display:<?php echo ($primary == 'hubzero' || count($this->authenticators) == 0) ? 'block' : 'none'; ?>;">
			<div class="instructions"><?php echo JText::sprintf('Sign in to %s', $app->getCfg('sitename')); ?></div>
			<form action="<?php echo JRoute::_('index.php', true, true); ?>" method="post" class="login_form">
				<div class="input-wrap">
					<?php if (isset($user) && is_object($user)) : ?>
						<input type="hidden" name="username" value="<?php echo $user->get('username'); ?>" />
						<div class="existing-name"><?php echo $user->get('name'); ?></div>
						<div class="existing-email"><?php echo $user->get('email'); ?></div>
					<?php else : ?>
						<div class="label-input-pair username">
							<label for="username"><?php echo JText::_('Username or email'); ?>:</label>
							<input tabindex="1" type="text" name="username" id="username" class="username" placeholder="<?php echo JText::_('email address or username'); ?>" />
						</div>
					<?php endif; ?>
					<div class="label-input-pair">
						<label for="password"><?php echo JText::_('Password'); ?>:</label>
						<input tabindex="2" type="password" name="passwd" id="password" class="passwd" placeholder="<?php echo JText::_('password'); ?>" />
						<div class="loading"></div>
					</div>
					<div class="input-error">blah blah</div>
				</div>
				<div class="submission">
					<input type="submit" value="<?php echo JText::_('Sign in'); ?>" class="login-submit btn btn-primary" />
					<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
						<div class="remember-wrap">
							<input type="checkbox" class="remember option" name="remember" id="remember" value="yes" title="<?php echo JText::_('Remember Me'); ?>" <?php echo ($this->remember_me_default) ? 'checked="checked"' : ''; ?> />
							<label for="remember" class="remember-me-label"><?php echo JText::_('Keep me logged in?'); ?></label>
						</div>
					<?php endif; ?>
				</div>
				<div class="forgots">
					<?php if (!isset($user)) : ?>
						<a class="forgot-username" href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('Lost username?');?></a>
					<?php endif; ?>
					<a class="forgot-password" href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('Forgot password?'); ?></a>
				</div>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="authenticator" value="hubzero" />
				<input type="hidden" name="task" value="user.login" />
				<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
				<input type="hidden" name="freturn" value="<?php echo $this->freturn; ?>" />
				<?php echo JHTML::_('form.token'); ?>
			</form>
		</div>
	</div>
<?php endif; ?>
<?php if (isset($user) && is_object($user)) : ?>
	<div class="others">
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=login&reset=1' . $this->returnQueryString); ?>">
			<?php echo JText::_('Sign in with a different account'); ?>
		</a>
	</div>
<?php elseif ($usersConfig->get('allowUserRegistration') != '0') : ?>
	<p class="create">
		<a href="<?php echo JURI::base(true); ?>/register" class="register">
			<?php echo JText::_('Create an account'); ?>
		</a>
	</p>
<?php endif; ?>

	</div> <!-- / .hz_user -->
