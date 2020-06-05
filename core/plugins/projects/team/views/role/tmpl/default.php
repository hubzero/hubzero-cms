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
	<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_REASSIGN_ROLE_TEAM_MEMBERS'); ?></h3>
	<?php
	// Display error or success message
	if ($this->getError()) {
		echo '<p class="witherror">'.$this->getError().'</p>';
	}
	$self = in_array($this->aid, $this->checked) ? 1 : 0;
	?>
	<?php if (!$this->getError()) { ?>
		<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->model->link()); ?>">
			<fieldset >
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="action" value="assignrole" />
				<input type="hidden" name="task" value="view" />
				<input type="hidden" name="ajax" value="1" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="active" value="team" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<p class="anote"><?php echo Lang::txt('PLG_PROJECTS_TEAM_REASSIGN_ROLE_TEAM_MEMBERS_NOTE'); ?></p>
				<p><?php echo Lang::txt('PLG_PROJECTS_TEAM_REASSIGN_ROLE_TEAM_MEMBERS_CONFIRM'); ?></p>
				<p class="prominent"><?php foreach ($this->selected as $owner) {
					echo $owner->fullname;
					echo ($i < count($this->selected)) ? ', ': '';
					$i++;
					echo '<input type="hidden" name="owner[]" value="' . $owner->id . '" />';
				} ?></p>
				<div class="combine_options">
					<?php echo Lang::txt('PLG_PROJECTS_TEAM_SET_ROLE'); ?>
					 <label>
						 <input class="option" name="role" type="radio" value="1" checked="checked"  />
						<?php echo Lang::txt('PLG_PROJECTS_TEAM_LABEL_OWNER'); ?>
					 </label>
					 <label>
						<input class="option" name="role" type="radio" value="2" />
						<?php echo Lang::txt('PLG_PROJECTS_TEAM_LABEL_COLLABORATOR'); ?>
					</label>
				</div>
				<p class="submitarea">
					<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_ROLE'); ?>" />
					<input type="reset" id="cancel-action" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_CANCEL'); ?>" />
				</p>
			</fieldset>
		</form>
	<?php } ?>
</div>