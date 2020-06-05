<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$hash  = App::hash(App::get('client')->name . ':authenticator');

if (($cookie = \Hubzero\Utility\Cookie::eat('authenticator')) && !Request::getInt('reset', false))
{
	$primary = $cookie->authenticator;
	// Make sure primary is still enabled
	if (array_key_exists($primary, $authenticators)
	|| (isset($this->local) && $this->local && $primary == 'hubzero'))
	{
		if (isset($cookie->user_id))
		{
			$user     = User::getInstance($cookie->user_id);
			$user_img = $cookie->user_img;
			Request::setVar('primary', $primary);
		}
	}
}

$usersConfig = Component::params('com_members');
$primary     = Request::getWord('primary', false);

// use some reflections to inspect plugins for special behavior (added for shibboleth)
$refl = array();
$login_provider_html = "";
foreach ($authenticators as $a)
{
	$refl[$a['name']] = new \ReflectionClass("plgAuthentication{$a['name']}");
	if ($refl[$a['name']]->hasMethod('onRenderOption'))
	{
		$html = $refl[$a['name']]->getMethod('onRenderOption')->invoke(null, $returnQueryString);
		$login_provider_html .= is_array($html) ? implode("\n", $html) : $html;
	}
	else
	{
		$login_provider_html .= '<a class="' . $a['name'] . ' account" href="' . Route::url('index.php?option=com_users&view=login&authenticator=' . $a['name'] . $returnQueryString) . '">';
		$login_provider_html .= '<div class="signin">' . Lang::txt('MOD_LOGIN_SIGN_IN_WITH_METHOD', $a['display']) . '</div>';
		$login_provider_html .= '</a>';
	}
}
// Make sure the currently chosen primary actuall exists
if ($primary != 'hubzero' && !isset($refl[$primary]))
{
	$primary = null;
}

$current  = $uri->toString(); //Hubzero\Utility\Uri::getInstance()->toString();
$current .= (strstr($current, '?') ? '&' : '?');
?>
<div class="hz_user">

<?php if ($primary && $primary != 'hubzero') : ?>
	<a class="primary" href="<?php echo Route::url('index.php?option=com_users&view=login&authenticator=' . $primary . $returnQueryString); ?>">
		<div class="<?php echo $primary; ?> upper"></div>
		<div class="auth">
			<div class="person">
				<?php if (isset($user_img) && file_exists($user_img)) : ?>
					<img src="<?php echo $user_img; ?>" alt="<?php echo Lang::txt('MOD_LOGIN_USER_PICTURE'); ?>" />
				<?php endif; ?>
			</div>
			<div class="lower">
				<div class="instructions"><?php echo isset($refl[$primary]) && $refl[$primary]->hasMethod('onGetSubsequentLoginDescription') ? $refl[$primary]->getMethod('onGetSubsequentLoginDescription')->invoke(null, $returnQueryString) : Lang::txt('MOD_LOGIN_SIGN_IN_WITH_METHOD', ucfirst($primary)); ?></div>
			</div>
		</div>
	</a>
<?php else: ?>
	<div class="auth">
		<div class="person">
			<?php if (isset($user_img) && file_exists($user_img)) : ?>
				<?php $img_properties = getimagesize(PATH_APP . DS . $user_img); ?>
				<?php $class = ($img_properties[0] > $img_properties[1]) ? 'wide' : 'tall'; ?>
				<img class="<?php echo $class; ?>" src="<?php echo $user_img; ?>" alt="<?php echo Lang::txt('MOD_LOGIN_USER_PICTURE'); ?>" />
			<?php endif; ?>
		</div>
		<div class="default <?php echo ($primary || count($authenticators) == 0) ? 'none' : 'block'; ?>">
			<div class="instructions"><?php echo Lang::txt('MOD_LOGIN_CHOOSE_METHOD'); ?></div>
			<div class="options">
				<?php echo $login_provider_html; ?>
			</div>
			<div class="or"></div>
			<div class="local">
				<a href="<?php echo $current . 'primary=hubzero&reset=1';// . $returnQueryString; ?>">
					<?php echo Lang::txt('MOD_LOGIN_SIGN_IN_WITH_ACCOUNT', ((isset($site_display)) ? $site_display : Config::get('sitename'))); ?>
				</a>
			</div>
		</div>
		<div class="hz <?php echo ($primary == 'hubzero' || $login_provider_html == '') ? 'block' : 'none'; ?>">
			<div class="instructions"><?php echo Lang::txt('MOD_LOGIN_TO', Config::get('sitename')); ?></div>
			<form action="<?php echo Route::url('index.php', true, true); ?>" method="post" class="login_form">
				<div class="input-wrap">
					<?php if (isset($user) && is_object($user)) : ?>
						<input type="hidden" name="username" value="<?php echo $user->get('username'); ?>" />
						<div class="existing-name"><?php echo $user->get('name'); ?></div>
						<div class="existing-email"><?php echo $user->get('email'); ?></div>
					<?php else : ?>
						<div class="label-input-pair username">
							<label for="username"><?php echo Lang::txt('MOD_LOGIN_USERNAME'); ?>:</label>
							<input tabindex="1" type="text" name="username" id="username" class="username" placeholder="<?php echo Lang::txt('MOD_LOGIN_USERNAME'); ?>" />
						</div>
					<?php endif; ?>
					<div class="label-input-pair">
						<label for="password"><?php echo Lang::txt('MOD_LOGIN_PASSWORD'); ?>:</label>
						<input tabindex="2" type="password" name="passwd" id="password" class="passwd" placeholder="<?php echo Lang::txt('MOD_LOGIN_PASSWORD'); ?>" autocomplete="off" />
						<div class="spinner">
							<div class="bounce1"></div>
							<div class="bounce2"></div>
							<div class="bounce3"></div>
						</div>
					</div>
					<div class="input-error"></div>
				</div>
				<div class="submission">
					<input type="submit" value="<?php echo Lang::txt('Sign in'); ?>" class="login-submit btn btn-primary" />
					<?php if (Plugin::isEnabled('system', 'remember')) : ?>
						<div class="remember-wrap">
							<input type="checkbox" class="remember option" name="remember" id="remember" value="yes" title="<?php echo Lang::txt('Remember Me'); ?>" <?php echo ($remember_me_default) ? 'checked="checked"' : ''; ?> />
							<label for="remember" class="remember-me-label"><?php echo Lang::txt('MOD_LOGIN_KEEP_LOGGED_IN'); ?></label>
						</div>
					<?php endif; ?>
				</div>
				<div class="forgots">
					<?php if (!isset($user)) : ?>
						<a class="forgot-username" href="<?php echo Route::url('index.php?option=com_members&task=remind'); ?>"><?php echo Lang::txt('MOD_LOGIN_REMIND');?></a>
					<?php endif; ?>
					<a class="forgot-password" href="<?php echo Route::url('index.php?option=com_members&task=reset'); ?>"><?php echo Lang::txt('MOD_LOGIN_RESET'); ?></a>
				</div>
				<input type="hidden" name="option" value="com_login" />
				<input type="hidden" name="authenticator" value="hubzero" />
				<input type="hidden" name="task" value="login" />
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
			<?php echo Lang::txt('MOD_LOGIN_SIGN_IN_WITH_DIFFERENT_ACCOUNT'); ?>
		</a>
	</div>
<?php elseif ($usersConfig->get('allowUserRegistration') != '0') : ?>
	<p class="create">
		<a href="<?php echo Request::base(true); ?>/register<?php echo $return ? '?return=' . $return : ''; ?>" class="register">
			<?php echo Lang::txt('MOD_LOGIN_CREATE_ACCOUNT'); ?>
		</a>
	</p>
<?php endif; ?>

</div> <!-- / .hz_user -->