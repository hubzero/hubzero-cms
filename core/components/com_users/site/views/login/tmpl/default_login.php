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

// No direct access.
defined('_HZEXEC_') or die();

// Get and add the js and extra css to the page
\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'login.css');
\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'providers.css');
\Hubzero\Document\Assets::addComponentScript('com_users', 'login');

\Hubzero\Document\Assets::addSystemStylesheet('uniform.css');
\Hubzero\Document\Assets::addSystemScript('jquery.uniform');
\Hubzero\Document\Assets::addSystemScript('jquery.hoverIntent');
\Hubzero\Document\Assets::addSystemScript('placeholder');

$hash  = App::hash(App::get('client')->name . ':authenticator');

if (($cookie = \Hubzero\Utility\Cookie::eat('authenticator')) && !Request::getInt('reset', false))
{
	$primary = $cookie->authenticator;

	// Make sure primary is still enabled
	if (array_key_exists($primary, $this->authenticators)
	|| (isset($this->local) && $this->local && $primary == 'hubzero'))
	{
		$user     = User::getInstance($cookie->user_id);
		$user_img = $cookie->user_img;
		Request::setVar('primary', $primary);
	}
}

$usersConfig = Component::params('com_users');
$primary     = Request::getWord('primary', false);

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
		<?php if ($errorText = Request::getString('errorText', false)) : ?>
			<p class="error">
				<?php echo $this->escape($errorText); ?>
			</p>
		<?php endif; ?>
<?php if ($this->totalauths) : ?>
	<?php if ($primary && $primary != 'hubzero') : ?>
		<a class="primary" href="<?php echo Route::url('index.php?option=com_users&view=login&authenticator=' . $primary . $this->returnQueryString); ?>">
			<div class="<?php echo $primary; ?> upper"></div>
			<div class="auth">
				<div class="person">
					<?php if (isset($user_img) && file_exists($user_img) && !$user->get('block')) : ?>
						<img src="<?php echo $user_img; ?>" alt="<?php echo Lang::txt('COM_USERS_LOGIN_USER_PICTURE'); ?>" />
					<?php endif; ?>
				</div>
				<div class="lower">
					<div class="instructions"><?php echo isset($refl[$primary]) && $refl[$primary]->hasMethod('onGetSubsequentLoginDescription') ? $refl[$primary]->getMethod('onGetSubsequentLoginDescription')->invoke(NULL, $this->returnQueryString) : Lang::txt('COM_USERS_LOGIN_SIGN_IN_WITH_METHOD', $this->authenticators[$primary]['display']); ?></div>
				</div>
			</div>
		</a>
	<?php else: ?>
		<div class="auth">
			<div class="person">
				<?php if (isset($user_img) && is_object($user) && file_exists($user_img)) : ?>
					<?php $image = $user->picture(0, false, false); ?>
					<?php $img_properties = getimagesize(PATH_CORE . DS . $image); ?>
					<?php $class = ($img_properties[0] > $img_properties[1]) ? 'wide' : 'tall'; ?>
					<img class="<?php echo $class; ?>" src="<?php echo $user_img; ?>" alt="<?php echo Lang::txt('COM_USERS_LOGIN_USER_PICTURE'); ?>" />
				<?php endif; ?>
			</div>
			<div class="default" style="display:<?php echo ($primary || count($this->authenticators) == 0) ? 'none' : 'block'; ?>;">
				<div class="instructions"><?php echo Lang::txt('COM_USERS_LOGIN_CHOOSE_METHOD'); ?></div>
				<div class="options">
					<?php foreach ($this->authenticators as $a) : ?>
							<?php
								if ($refl[$a['name']]->hasMethod('onRenderOption') && ($html = $refl[$a['name']]->getMethod('onRenderOption')->invoke(NULL, $this->returnQueryString))):
									echo is_array($html) ? implode("\n", $html) : $html;
								else:
							?>
								<a class="<?php echo $a['name']; ?> account" href="<?php echo Route::url('index.php?option=com_users&view=login&authenticator=' . $a['name'] . $this->returnQueryString); ?>">
									<div class="signin"><?php echo Lang::txt('COM_USERS_LOGIN_SIGN_IN_WITH_METHOD', $a['display']); ?></div>
								</a>
							<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<?php if (isset($this->local) && $this->local) : ?>
					<div class="or"></div>
					<div class="local">
						<a href="<?php echo Route::url('index.php?option=com_users&view=login&primary=hubzero&reset=1' . $this->returnQueryString); ?>">
							<?php echo Lang::txt('COM_USERS_LOGIN_SIGN_IN_WITH_ACCOUNT', ((isset($this->site_display)) ? $this->site_display : Config::get('sitename'))); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
			<div class="hz" style="display:<?php echo ($primary == 'hubzero' || count($this->authenticators) == 0) ? 'block' : 'none'; ?>;">
				<div class="instructions"><?php echo Lang::txt('COM_USERS_LOGIN_TO', Config::get('sitename')); ?></div>
				<form action="<?php echo Route::url('index.php', true, true); ?>" method="post" class="login_form">
					<div class="input-wrap">
						<?php if (isset($user) && is_object($user) && !$user->get('block')) : ?>
							<input type="hidden" name="username" value="<?php echo $user->get('username'); ?>" />
							<div class="existing-name"><?php echo $user->get('name'); ?></div>
							<div class="existing-email"><?php echo $user->get('email'); ?></div>
						<?php else : ?>
							<div class="label-input-pair username">
								<label for="username"><?php echo Lang::txt('COM_USERS_LOGIN_USERNAME'); ?>:</label>
								<input tabindex="1" type="text" name="username" id="username" class="username" placeholder="<?php echo strtolower(Lang::txt('COM_USERS_LOGIN_USERNAME')); ?>" />
							</div>
						<?php endif; ?>
						<div class="label-input-pair">
							<label for="password"><?php echo Lang::txt('COM_USERS_LOGIN_PASSWORD'); ?>:</label>
							<input tabindex="2" type="password" name="passwd" id="password" class="passwd" placeholder="<?php echo strtolower(Lang::txt('COM_USERS_LOGIN_PASSWORD')); ?>" />
							<div class="spinner">
								<div class="bounce1"></div>
								<div class="bounce2"></div>
								<div class="bounce3"></div>
							</div>
						</div>
						<div class="input-error"></div>
					</div>
					<div class="submission">
						<input type="submit" value="<?php echo Lang::txt('COM_USERS_LOGIN'); ?>" class="login-submit btn btn-primary" />
						<?php if (Plugin::isEnabled('system', 'remember')) : ?>
							<div class="remember-wrap">
								<input type="checkbox" class="remember option" name="remember" id="remember" value="yes" <?php echo ($this->remember_me_default) ? 'checked="checked"' : ''; ?> />
								<label for="remember" class="remember-me-label"><?php echo Lang::txt('COM_USERS_LOGIN_KEEP_LOGGED_IN'); ?></label>
							</div>
						<?php endif; ?>
					</div>
					<div class="forgots">
						<?php if (!isset($user)) : ?>
							<a class="forgot-username" href="<?php echo Route::url('index.php?option=com_members&task=remind'); ?>"><?php echo Lang::txt('COM_USERS_LOGIN_REMIND');?></a>
						<?php endif; ?>
						<a class="forgot-password" href="<?php echo Route::url('index.php?option=com_members&task=reset'); ?>"><?php echo Lang::txt('COM_USERS_LOGIN_RESET'); ?></a>
					</div>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="authenticator" value="hubzero" />
					<input type="hidden" name="task" value="user.login" />
					<input type="hidden" name="return" value="<?php echo $this->escape($this->return); ?>" />
					<input type="hidden" name="freturn" value="<?php echo $this->escape($this->freturn); ?>" />
					<?php echo Html::input('token'); ?>
				</form>
			</div>
		</div>
	<?php endif; ?>
	<?php if (isset($user) && is_object($user)) : ?>
		<div class="others">
			<a href="<?php echo Route::url('index.php?option=com_users&view=login&reset=1' . $this->returnQueryString); ?>">
				<?php echo Lang::txt('COM_USERS_LOGIN_SIGN_IN_WITH_DIFFERENT_ACCOUNT'); ?>
			</a>
		</div>
	<?php elseif ($usersConfig->get('allowUserRegistration') != '0') : ?>
		<p class="create">
			<a href="<?php echo Request::base(true); ?>/register" class="register">
				<?php echo Lang::txt('COM_USERS_LOGIN_CREATE_ACCOUNT'); ?>
			</a>
		</p>
	<?php endif; ?>
<?php else : ?>
	<p class="warning">
		<?php echo Lang::txt('COM_USERS_LOGIN_UNAVAILABLE'); ?>
	</p>
<?php endif; ?>

	</div> <!-- / .hz_user -->
