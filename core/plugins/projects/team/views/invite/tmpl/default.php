<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Build url
$route = $this->model->isProvisioned()
	? 'index.php?option=com_publications&task=submit'
	: 'index.php?option=com_projects&alias=' . $this->model->get('alias');
$p_url = Route::url($route . '&active=team');

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_INVITE_BY_EMAIL_TO_JOIN'); ?></h3>
<form id="hubForm-ajax" method="post" action="<?php echo $p->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" value="sendinvite" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="team" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
		<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="email" value="<?php echo $this->email; ?>" />
		<?php if ($this->model->isProvisioned()) { ?>
		<input type="hidden" name="task" value="submit" />
		<?php } ?>
		<label for="from"><span class="leftshift"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_FROM')); ?>:</span>
		<input type="text" name="from" value="<?php echo $this->name; ?>" maxlength="100" /></label>
		<label for="to"><span class="leftshift"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_TO')); ?>:
			<span class="hint block mini"><?php echo Lang::txt('PLG_PROJECTS_TEAM_COMMA_SEPARATED_LIST'); ?></span></span>
			<textarea name="to" cols="4" rows="4" class="emailstring" ></textarea>
		</label>
		<label for="message"><span class="leftshift"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_MESSAGE')); ?>:
			<span class="hint block mini">(<?php echo Lang::txt('PLG_PROJECTS_TEAM_OPTIONAL'); ?>)</span></span>
			<textarea name="message" cols="4" rows="4" class="msgstring" ></textarea>
		</label>
		<p class="submitarea">
			<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_INVITE'); ?>" />
			<input type="reset" id="cancel-action" value="<?php echo Lang::txt('PLG_PROJECTS_TEAM_CANCEL'); ?>" />
		</p>
	</fieldset>
</form>
</div>