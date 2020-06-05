<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

$this->css('login')
     ->css('providers', 'com_login')
     ->js('login');

Html::behavior('keepalive');
?>

<div class="hz_user">
	<div class="auth">
		<div class="default <?php echo count($authenticators) == 0 ? 'none' : 'block'; ?>">
			<div class="instructions"><?php echo Lang::txt('COM_LOGIN_CHOOSE_METHOD'); ?></div>
			<div class="options">
				<?php
				foreach ($authenticators as $a):
					$refl[$a['name']] = new \ReflectionClass("plgAuthentication{$a['name']}");
					if ($refl[$a['name']]->hasMethod('onRenderOption')):
						$html = $refl[$a['name']]->getMethod('onRenderOption')->invoke(null, $returnQueryString);
						echo is_array($html) ? implode("\n", $html) : $html;
					else:
						?>
						<a class="<?php echo $a['name']; ?> account" href="<?php echo Route::url('index.php?option=com_login&task=display&authenticator=' . $a['name'] . $returnQueryString); ?>">
							<div class="signin"><?php echo Lang::txt('COM_LOGIN_SIGN_IN_WITH_METHOD', $a['display']); ?></div>
						</a>
						<?php
					endif;
				endforeach;
				?>
			</div>
			<?php if (isset($basic) && $basic) : ?>
				<div class="or"></div>
				<div class="local">
					<a href="#">
						<?php echo Lang::txt('COM_LOGIN_SIGN_IN_WITH_ACCOUNT', $site_display); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<div class="hz <?php echo count($authenticators) == 0 ? 'block' : 'none'; ?>">
			<form action="<?php echo Route::url('index.php', true, true); ?>" method="post" class="login_form">
				<label id="mod-login-username-lbl" for="mod-login-username">
					<span><?php echo Lang::txt('JGLOBAL_USERNAME'); ?></span>
					<input name="username" id="mod-login-username" class="input-username" type="text" size="15" placeholder="<?php echo Lang::txt('JGLOBAL_USERNAME'); ?>" />
				</label>

				<label id="mod-login-password-lbl" for="mod-login-password">
					<span><?php echo Lang::txt('JGLOBAL_PASSWORD'); ?></span>
					<input name="passwd" id="mod-login-password" class="input-password" type="password" size="15" placeholder="<?php echo Lang::txt('JGLOBAL_PASSWORD'); ?>" />
				</label>

				<div class="button-holder">
					<input type="submit" class="btn" value="<?php echo Lang::txt('MOD_ADMINLOGIN_LOGIN'); ?>" />
				</div>

				<?php if (count($authenticators) > 0) : ?>
					<div class="or"></div>
					<div class="multi-auth">
						<a href="#">
							<?php echo Lang::txt('COM_LOGIN_SIGN_IN_WITH_OTHER'); ?>
						</a>
					</div>
				<?php endif; ?>

				<input type="hidden" name="option"        value="com_login" />
				<input type="hidden" name="authenticator" value="hubzero" />
				<input type="hidden" name="task"          value="login" />
				<input type="hidden" name="return"        value="<?php echo $return; ?>" />
				<input type="hidden" name="freturn"       value="<?php echo $freturn; ?>" />
				<?php echo Html::input('token'); ?>
			</form>
		</div>
	</div>
</div>