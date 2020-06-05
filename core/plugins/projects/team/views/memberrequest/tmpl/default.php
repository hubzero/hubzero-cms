<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
$i = 1;

?>
<div id="abox-content">
	<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_DENY_MEMBERREQUEST_TITLE'); ?></h3>
	<?php
	// Display error or success message
	if ($this->getError()) {
		echo '<p class="witherror">' . $this->getError() . '</p>';
	}
	?>
	<?php if (!$this->getError()) { ?>
		<form id="hubForm-ajax" method="get" action="<?php echo Route::url($this->model->link('team')); ?>">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="action" value="denymembership" />
				<input type="hidden" name="owner" value="<?php echo $this->owner->userid;?>" />
				<p class="anote"><?php echo Lang::txt('PLG_PROJECTS_TEAM_DENY_MEMBERREQUEST_NOTE'); ?></p>
				<p><?php echo Lang::txt('PLG_PROJECTS_TEAM_DENY_MEMBERREQUEST_PROMPT'); ?></p>
				<p class="prominent"> 
					<?php echo $this->owner->user->name;?>
				</p>
				<label>Reason:</label>
				<textarea name="message"></textarea>
				<p class="submitarea">
					<input type="hidden" name="confirm" value="1" />
					<?php echo Html::input('token'); ?>
					<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_DENY_MEMBERREQUEST'); ?>" class="btn" />
					<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_CANCEL'); ?>" />
				</p>
			</fieldset>
		</form>
	<?php } ?>
</div>
