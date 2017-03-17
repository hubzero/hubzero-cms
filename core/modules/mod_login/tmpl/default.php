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

// no direct access
defined('_HZEXEC_') or die();

$hash  = App::hash(App::get('client')->name . ':authenticator');

if (($cookie = \Hubzero\Utility\Cookie::eat('authenticator')) && !Request::getInt('reset', false))
{
	$primary = $cookie->authenticator;
	$user     = User::getInstance($cookie->user_id);
	$user_img = $cookie->user_img;
	Request::setVar('primary', $primary);
}

$usersConfig = Component::params('com_members');
$primary     = Request::getWord('primary', false);

// use some reflections to inspect plugins for special behavior (added for shibboleth)
$refl = array();
foreach ($authenticators as $a):
	$refl[$a['name']] = new \ReflectionClass("plgAuthentication{$a['name']}");
endforeach;

$current  = Hubzero\Utility\Uri::getInstance()->toString();
$current .= (strstr($current, '?') ? '&' : '?');
?>
<div class="hz_user">

<?php if ($primary && $primary != 'hubzero') : ?>
	<a class="primary" href="<?php echo Route::url('index.php?option=com_users&view=login&authenticator=' . $primary . $returnQueryString); ?>">
		<div class="<?php echo $primary; ?> upper"></div>
		<div class="auth">
			<div class="person">
				<?php if (isset($user_img) && file_exists($user_img)) : ?>
					<img src="<?php echo $user_img; ?>" alt="<?php echo Lang::txt('User profile picture'); ?>" />
				<?php endif; ?>
			</div>
			<div class="lower">
				<div class="instructions"><?php echo isset($refl[$primary]) && $refl[$primary]->hasMethod('onGetSubsequentLoginDescription') ? $refl[$primary]->getMethod('onGetSubsequentLoginDescription')->invoke(NULL, $returnQueryString) : Lang::txt('Sign in with %s', ucfirst($primary)); ?></div>
			</div>
		</div>
	</a>
<?php else: ?>
	<div class="auth">
		<div class="person">
			<?php if (isset($user_img) && file_exists($user_img)) : ?>
				<?php $img_properties = getimagesize(PATH_APP . DS . $user_img); ?>
				<?php $class = ($img_properties[0] > $img_properties[1]) ? 'wide' : 'tall'; ?>
				<img class="<?php echo $class; ?>" src="<?php echo $user_img; ?>" alt="<?php echo Lang::txt('User profile picture'); ?>" />
			<?php endif; ?>
		</div>
		<div class="default" style="display:<?php echo ($primary || count($authenticators) == 0) ? 'none' : 'block'; ?>;">
			<div class="instructions"><?php echo Lang::txt('Choose your sign in method:'); ?></div>
			<div class="options">
				<?php foreach ($authenticators as $a) : ?>
						<?php
							if ($refl[$a['name']]->hasMethod('onRenderOption') && ($html = $refl[$a['name']]->getMethod('onRenderOption')->invoke(NULL, $returnQueryString))):
								echo is_array($html) ? implode("\n", $html) : $html;
							else:
						?>
							<a class="<?php echo $a['name']; ?> account" href="<?php echo Route::url('index.php?option=com_users&view=login&authenticator=' . $a['name'] . $returnQueryString); ?>">
								<div class="signin"><?php echo Lang::txt('Sign in with %s', $a['display']); ?></div>
							</a>
						<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="or"></div>
			<div class="local">
				<a href="<?php echo $current . 'primary=hubzero&reset=1';// . $returnQueryString; ?>">
					<?php echo Lang::txt('Sign in with your %s account', ((isset($site_display)) ? $site_display : Config::get('sitename'))); ?>
				</a>
			</div>
		</div>
		<div class="hz" style="display:<?php echo ($primary == 'hubzero' || count($authenticators) == 0) ? 'block' : 'none'; ?>;">
			<div class="instructions"><?php echo Lang::txt('Sign in to %s', Config::get('sitename')); ?></div>
			<form action="<?php echo Route::url('index.php', true, true); ?>" method="post" class="login_form">
				<div class="input-wrap">
					<?php if (isset($user) && is_object($user)) : ?>
						<input type="hidden" name="username" value="<?php echo $user->get('username'); ?>" />
						<div class="existing-name"><?php echo $user->get('name'); ?></div>
						<div class="existing-email"><?php echo $user->get('email'); ?></div>
					<?php else : ?>
						<div class="label-input-pair username">
							<label for="username"><?php echo Lang::txt('Username or email'); ?>:</label>
							<input tabindex="1" type="text" name="username" id="username" class="username" placeholder="<?php echo Lang::txt('email address or username'); ?>" />
						</div>
					<?php endif; ?>
					<div class="label-input-pair">
						<label for="password"><?php echo Lang::txt('Password'); ?>:</label>
						<input tabindex="2" type="password" name="passwd" id="password" class="passwd" placeholder="<?php echo Lang::txt('password'); ?>" />
						<div class="loading"></div>
					</div>
					<div class="input-error"></div>
				</div>
				<div class="submission">
					<input type="submit" value="<?php echo Lang::txt('Sign in'); ?>" class="login-submit btn btn-primary" />
					<?php if (Plugin::isEnabled('system', 'remember')) : ?>
						<div class="remember-wrap">
							<input type="checkbox" class="remember option" name="remember" id="remember" value="yes" title="<?php echo Lang::txt('Remember Me'); ?>" <?php echo ($remember_me_default) ? 'checked="checked"' : ''; ?> />
							<label for="remember" class="remember-me-label"><?php echo Lang::txt('Keep me logged in?'); ?></label>
						</div>
					<?php endif; ?>
				</div>
				<div class="forgots">
					<?php if (!isset($user)) : ?>
						<a class="forgot-username" href="<?php echo Route::url('index.php?option=com_users&view=remind'); ?>"><?php echo Lang::txt('Lost username?');?></a>
					<?php endif; ?>
					<a class="forgot-password" href="<?php echo Route::url('index.php?option=com_users&view=reset'); ?>"><?php echo Lang::txt('Forgot password?'); ?></a>
				</div>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="authenticator" value="hubzero" />
				<input type="hidden" name="task" value="user.login" />
				<input type="hidden" name="return" value="<?php echo $this->escape($return); ?>" />
				<input type="hidden" name="freturn" value="<?php echo $this->escape($freturn); ?>" />
				<?php echo Html::input('token'); ?>
			</form>
		</div>
	</div>
<?php endif; ?>
<?php if (isset($user) && is_object($user)) : ?>
	<div class="others">
		<a href="<?php echo Route::url($current . 'reset=1'); // . $returnQueryString); ?>">
			<?php echo Lang::txt('Sign in with a different account'); ?>
		</a>
	</div>
<?php elseif ($usersConfig->get('allowUserRegistration') != '0') : ?>
	<p class="create">
		<a href="<?php echo Request::base(true); ?>/register" class="register">
			<?php echo Lang::txt('Create an account'); ?>
		</a>
	</p>
<?php endif; ?>

</div> <!-- / .hz_user -->