<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->reviewer == 'sponsored')
{
	$title = Lang::txt('COM_PROJECTS_REVIEW_PROJECT_SPS');
	$approved = $this->params->get('grant_approval') || $this->params->get('grant_status') == 1 ? 1 : 0;

	$b_action = $approved ?
			Lang::txt('COM_PROJECTS_SAVE_SPS_APPROVED') :
			Lang::txt('COM_PROJECTS_SAVE_SPS');
}
else {
	$title = $this->model->isPending()
			? Lang::txt('COM_PROJECTS_REVIEW_PROJECT_HIPAA')
			: Lang::txt('COM_PROJECTS_REVIEW_PROJECT_HIPAA_SAVE');
	$b_action = $this->model->isPending()
			? Lang::txt('COM_PROJECTS_SAVE_HIPAA')
			: Lang::txt('COM_PROJECTS_SAVE');
}

$notes = \Components\Projects\Helpers\Html::getAdminNotes($this->model->get('admin_notes'), $this->reviewer);

?>
<?php if (!$this->ajax) { ?>
	<div id="content-header">
		<h2><?php echo $title ?></h2>
	</div>
<?php } ?>
<div id="abox-content" class="reviewer">
<?php if ($this->ajax) { ?>
<h3><?php echo $title ?></h3>
<?php } ?>
<?php
// Display error  message
if ($this->getError()) {
	echo '<p class="error">'.$this->getError().'</p>';
} ?>

<?php if ($this->model->exists()) { ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id') . '&task=process') . '?reviewer=' . $this->reviewer; ?>" method="post" id="<?php echo $this->ajax ? 'hubForm-ajax' : 'plg-form'; ?>" >

	<fieldset>
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="task" value="process" />
		<input type="hidden" name="ajax" value="1" />
		<input type="hidden" name="no_html" value="1" />
		<input type="hidden" name="reviewer" value="<?php echo $this->reviewer; ?>" />
		<input type="hidden" name="filterby" value="<?php echo $this->filterby; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	</fieldset>
	<div class="info_blurb">
		<div class="pthumb"><img src="<?php echo Route::url($this->model->link('thumb')); ?>" alt="" /></div>
		<div class="pinfo">
			<p class="info_title">
			<?php echo $this->model->get('title'); ?> (<span class="aliasname"><?php echo $this->model->get('alias'); ?></span>)</p>
			<p class="info_title"><span class="italic"><?php echo Lang::txt('COM_PROJECTS_CREATED_BY') . ': ' . $this->model->creator('name'); ?></span></p>
		</div>
	</div>

	<?php if ($this->reviewer == 'sponsored')
	{
	?>
	<div id="spsinfo" class="faded mini">
		<table>
			<tr>
				<td>
					<label><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:
					 <input name="grant_title" maxlength="250" type="text" value="<?php echo $this->params->get('grant_title'); ?>"  />
					</label>
				</td>
				<td class="tdmini"></td>
				<td>
					<label><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:
					 <input name="grant_PI" maxlength="250" type="text" value="<?php echo $this->params->get('grant_PI'); ?>"  />
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:
					 <input name="grant_agency" maxlength="250" type="text" value="<?php echo $this->params->get('grant_agency'); ?>"  />
					</label>
				</td>
				<td class="tdmini"></td>
				<td>
					<label><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:
					 <input name="grant_budget" maxlength="250" type="text" value="<?php echo $this->params->get('grant_budget'); ?>"  />
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label for "grant_approval" class="<?php if ($approved) { echo ' spsapproved';
} else { echo 'spsapproval'; } ?>"><?php echo $approved
						? ucfirst(Lang::txt('COM_PROJECTS_APPROVAL_CODE_APPROVED'))
						: Lang::txt('COM_PROJECTS_APPROVAL_CODE_PROVIDE'); ?>:
					 <input name="grant_approval" id="grant_approval" maxlength="250" type="text" value="<?php echo $this->params->get('grant_approval'); ?>"  />
					<?php if (!$approved) { ?>
					 <p class="hint mini"><?php echo Lang::txt('COM_PROJECTS_SPS_APPROVAL_HINT'); ?></p>
					<?php } ?>
					</label>
				</td>
				<td class="tdmini"><?php if (!$approved) { echo '<span class="or">' . Lang::txt('COM_PROJECTS_OR') . '</span>';  } ?></td>
				<td>
					<?php if (!$approved) { ?>
					<label for="rejected" class="dark">
						 <input class="option" name="rejected" id="rejected" type="checkbox" value="1" <?php if ($this->params->get('grant_status') == 2) { echo 'checked="checked"'; } ?> />
						<?php echo $this->params->get('grant_status') == 2 ? Lang::txt('COM_PROJECTS_SPS_REJECTED_KEEP') : Lang::txt('COM_PROJECTS_SPS_REJECT'); ?>
					</label>
					<?php } ?>
				</td>
			</tr>
		</table>
	</div>
	<?php } ?>
	<?php if ($this->model->isPending() && $this->reviewer == 'sensitive') { ?>
	 <div>
		<label id="sdata-approve"><input class="option" name="approve" type="checkbox" value="1" /> <?php echo ucfirst(Lang::txt('COM_PROJECTS_APPROVE_PROJECT_CONFIRM')); ?></label>
	 </div>
	<?php } ?>

	<div id="newadmincomment">
		<h4><?php echo ucfirst(Lang::txt('COM_PROJECTS_ADD_ADMIN_COMMENT')); ?> <span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span></h4>
		<label>
			<textarea name="comment" rows="4" cols="40"></textarea>
		</label>
		<?php if ($this->reviewer == 'sponsored' && !$approved) { ?>
		 <label><input class="option" name="notify" type="checkbox" value="1" /> <?php echo ucfirst(Lang::txt('COM_PROJECTS_REVIEWERS_ADD_ACTIVITY')); ?></label>
		<?php } ?>
	</div>
	<p class="submitarea">
		<input type="submit" value="<?php echo $b_action; ?>" class="btn" />
		<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt(JCANCEL); ?>" />
	</p>
	<div id="admincommentbox">
	<h4><?php echo ucfirst(Lang::txt('COM_PROJECTS_REVIEWER_COMMENTS')); ?> <span class="hint"> <?php echo ucfirst(Lang::txt('COM_PROJECTS_REVIEWER_COMMENTS_LATEST_FIRST')); ?></span></h4>
	<?php if ($notes)  { ?>
		<?php echo $notes; ?>
	<?php } else {  ?>
		<p class="noresults">No comments found</p>
	<?php } ?>
	</div>
</form>
<?php } ?>
</div>