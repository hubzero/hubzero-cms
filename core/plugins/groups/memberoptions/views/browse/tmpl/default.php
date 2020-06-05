<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$params = $params =  Component::params('com_groups');

$allowEmailResponses = $params->get('email_comment_processing');

// Be sure to update this if you add more options
$atLeastOneOption = false;
if ($allowEmailResponses)
{
	$atLeastOneOption = true;
}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=memberoptions'); ?>" method="post" id="memberoptionform">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="action" value="savememberoptions" />
	<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID;?>" />

	<div class="group-content-header">
		<h3><?php echo Lang::txt('GROUP_MEMBEROPTIONS'); ?></h3>
	</div>

	<p><?php echo Lang::txt('GROUP_MEMBEROPTIONS_DESC'); ?></p>

	<?php if ($allowEmailResponses) { ?>
		<div class="input-wrap">
			<input type="checkbox" id="recvpostemail" value="1" name="recvpostemail" <?php if ($this->recvEmailOptionValue == 1) { echo 'checked="checked"'; } ?> />
			<label for="recvpostemail"><?php echo Lang::txt('GROUP_RECEIVE_EMAILS_DISCUSSION_POSTS'); ?></label>
		</div>
	<?php } ?>

	<?php if ($atLeastOneOption) { ?>
		<div class="submit">
			<input type="submit" class="btn" value="<?php echo Lang::txt('Save'); ?>" />
		</div>
	<?php } else { ?>
		<?php echo Lang::txt('GROUP_MEMBEROPTIONS_NONE'); ?>
	<?php } ?>
</form>
