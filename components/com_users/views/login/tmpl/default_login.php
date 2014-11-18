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

// use some reflections to inspect plugins for special behavior (added for shibboleth)
$refl = array();
foreach ($this->authenticators as $a):
	$refl[$a['name']] = new \ReflectionClass("plgAuthentication{$a['name']}");
endforeach;
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
					<img src="<?php echo $user_img; ?>" alt="<?php echo JText::_('COM_USERS_LOGIN_USER_PICTURE'); ?>" />
				<?php endif; ?>
			</div>
			<div class="lower">
				<div class="instructions"><?php echo isset($refl[$primary]) && $refl[$primary]->hasMethod('onGetSubsequentLoginDescription') ? $refl[$primary]->getMethod('onGetSubsequentLoginDescription')->invoke(NULL, $this->returnQueryString) : JText::sprintf('COM_USERS_LOGIN_SIGN_IN_WITH_METHOD', $this->authenticators[$primary]['display']); ?></div>
			</div>
		</div>
	</a>
<?php else: ?>
	<div class="auth">
		<div class="person">
			<?php if (isset($user_img)) : ?>
				<?php $image = Hubzero\User\Profile::getInstance($user->get('id'))->getPicture(0, false, false); ?>
				<?php $img_properties = getimagesize(JPATH_ROOT . DS . $image); ?>
				<?php $class = ($img_properties[0] > $img_properties[1]) ? 'wide' : 'tall'; ?>
				<img class="<?php echo $class; ?>" src="<?php echo $user_img; ?>" alt="<?php echo JText::_('COM_USERS_LOGIN_USER_PICTURE'); ?>" />
			<?php endif; ?>
		</div>
		<div class="default" style="display:<?php echo ($primary || count($this->authenticators) == 0) ? 'none' : 'block'; ?>;">
			<div class="instructions"><?php echo JText::_('COM_USERS_LOGIN_CHOOSE_METHOD'); ?></div>
			<div class="options">
				<?php foreach ($this->authenticators as $a) : ?>
						<?php 
							if ($refl[$a['name']]->hasMethod('onRenderOption') && ($html = $refl[$a['name']]->getMethod('onRenderOption')->invoke(NULL, $this->returnQueryString))):
								echo is_array($html) ? implode("\n", $html) : $html;
							else:
						?>
							<a class="<?php echo $a['name']; ?> account" href="<?php echo JRoute::_('index.php?option=com_users&view=login&authenticator=' . $a['name'] . $this->returnQueryString); ?>">
								<div class="signin"><?php echo JText::sprintf('COM_USERS_LOGIN_SIGN_IN_WITH_METHOD', $a['display']); ?></div>
							</a>
						<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="or"></div>
			<div class="local">
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=login&primary=hubzero&reset=1' . $this->returnQueryString); ?>">
					<?php echo JText::sprintf('COM_USERS_LOGIN_SIGN_IN_WITH_ACCOUNT', ((isset($this->site_display)) ? $this->site_display : $app->getCfg('sitename'))); ?>
				</a>
			</div>
		</div>
		<div class="hz" style="display:<?php echo ($primary == 'hubzero' || count($this->authenticators) == 0) ? 'block' : 'none'; ?>;">
			<div class="instructions"><?php echo JText::sprintf('COM_USERS_LOGIN_TO', $app->getCfg('sitename')); ?></div>
			<form action="<?php echo JRoute::_('index.php', true, true); ?>" method="post" class="login_form">
				<div class="input-wrap">
					<?php if (isset($user) && is_object($user)) : ?>
						<input type="hidden" name="username" value="<?php echo $user->get('username'); ?>" />
						<div class="existing-name"><?php echo $user->get('name'); ?></div>
						<div class="existing-email"><?php echo $user->get('email'); ?></div>
					<?php else : ?>
						<div class="label-input-pair username">
							<label for="username"><?php echo JText::_('COM_USERS_LOGIN_USERNAME'); ?>:</label>
							<input tabindex="1" type="text" name="username" id="username" class="username" placeholder="<?php echo strtolower(JText::_('COM_USERS_LOGIN_USERNAME')); ?>" />
						</div>
					<?php endif; ?>
					<div class="label-input-pair">
						<label for="password"><?php echo JText::_('COM_USERS_LOGIN_PASSWORD'); ?>:</label>
						<input tabindex="2" type="password" name="passwd" id="password" class="passwd" placeholder="<?php echo strtolower(JText::_('COM_USERS_LOGIN_PASSWORD')); ?>" />
						<div class="spinner">
							<div class="bounce1"></div>
							<div class="bounce2"></div>
							<div class="bounce3"></div>
						</div>
					</div>
					<div class="input-error"></div>
				</div>
				<div class="submission">
					<input type="submit" value="<?php echo JText::_('COM_USERS_LOGIN'); ?>" class="login-submit btn btn-primary" />
					<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
						<div class="remember-wrap">
							<input type="checkbox" class="remember option" name="remember" id="remember" value="yes" <?php echo ($this->remember_me_default) ? 'checked="checked"' : ''; ?> />
							<label for="remember" class="remember-me-label"><?php echo JText::_('COM_USERS_LOGIN_KEEP_LOGGED_IN'); ?></label>
						</div>
					<?php endif; ?>
				</div>
				<div class="forgots">
					<?php if (!isset($user)) : ?>
						<a class="forgot-username" href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('COM_USERS_LOGIN_REMIND');?></a>
					<?php endif; ?>
					<a class="forgot-password" href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
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
			<?php echo JText::_('COM_USERS_LOGIN_SIGN_IN_WITH_DIFFERENT_ACCOUNT'); ?>
		</a>
	</div>
<?php elseif ($usersConfig->get('allowUserRegistration') != '0') : ?>
	<p class="create">
		<a href="<?php echo JURI::base(true); ?>/register" class="register">
			<?php echo JText::_('COM_USERS_LOGIN_CREATE_ACCOUNT'); ?>
		</a>
	</p>
<?php endif; ?>

	</div> <!-- / .hz_user -->
