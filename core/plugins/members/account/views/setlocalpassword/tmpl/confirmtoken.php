<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('providers.css', 'com_login')
     ->js()
     ->js('jquery.hoverIntent', 'system');
?>

<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_ENTER_CONFIRMATION_TOKEN'); ?></h3>

<?php if (isset($this->notifications) && count($this->notifications) > 0) {
	foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } // close foreach
} // close if count ?>

<div id="members-account-section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option .
										'&id=' . $this->id .
										'&active=account' .
										'&task=confirmtoken'); ?>" method="post">
		<fieldset>
			<legend><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_ENTER_CONFIRMATION_TOKEN'); ?></legend>
			<div class="fieldset-grouping">
				<label for="token"><?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_TOKEN'); ?>:</label>
				<input id="token" name="token" type="text" class="required" size="36" />
			</div>
		</fieldset>

		<div class="clear"></div>

		<p class="submit">
			<input name="change" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_SUBMIT'); ?>" />
			<input type="reset" class="cancel" value="<?php echo Lang::txt('PLG_MEMBERS_ACCOUNT_CANCEL'); ?>" />
		</p>

		<?php echo Html::input('token'); ?>
	</form>
</div>
<div class="clear"></div>