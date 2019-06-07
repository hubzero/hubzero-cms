<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="overlay"></div>
<div id="questions" data-redirect="true">
	<p>
		<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_THANK_YOU'); ?>
		<?php if ($award): ?>
			<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_AWARDS_EARNED', $award); ?>
		<?php endif; ?>
		<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_REDIRECTED_SOON'); ?>
	</p>

	<a href="<?php echo Request::getString('REQUEST_URI', Request::getString('REDIRECT_REQUEST_URI', '', 'server'), 'server'); ?>"><?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_CLICK_IF_NOT_REDIRECTED'); ?></a>
</div>
