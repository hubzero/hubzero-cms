<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>

<div id="abox-content">
	<h3><?php echo $this->watch->get('id') ? Lang::txt('PLG_PROJECTS_WATCH_MANAGE') : Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBE'); ?></h3>
	<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->project->link() . '&active=watch&action=save'); ?>">
		<fieldset >
			<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="task" value="view" />
			<input type="hidden" name="active" value="watch" />
			<input type="hidden" name="ajax" value="1" />
			<input type="hidden" name="option" value="com_projects" />
			<h5><?php echo Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBE_CATEGORIES'); ?>:</h5>

			<ul class="cat-list">
			<?php foreach ($this->cats as $name => $checked) { ?>
				<li>
					<input type="checkbox" name="category[<?php echo $name; ?>]" value="1" <?php if ($checked == 1) { echo 'checked="checked"'; } ?> /> <span class="cat-icon"><span class="<?php echo $name; ?>"></span></span> <span><?php echo Lang::txt('PLG_PROJECTS_WATCH_' . strtoupper($name)); ?></span>
				</li>
			<?php  } ?>
			</ul>
			<?php /*
			<div class="delivery">
				<p><?php echo Lang::txt('PLG_PROJECTS_WATCH_UPDATES_DELIVERED_TO_EMAIL', User::get('email')); ?></p>
			<h5><?php echo Lang::txt('PLG_PROJECTS_WATCH_UPDATES_FREQUENCY'); ?>:</h5>
			<ul class="cat-list">
				<li>
					<input type="radio" name="frequency" value="immediate" checked="checked" /> <span><?php echo Lang::txt('PLG_PROJECTS_WATCH_FREQUENCY_IMMEDIATE'); ?></span>
				</li>
			</ul>
			</div>
			*/?>
			<input type="hidden" name="frequency" value="immediate" />

			<p class="submitarea">
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_WATCH_SAVE'); ?>" />
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_WATCH_CANCEL'); ?>" />
			</p>
			<?php echo Html::input('token'); ?>
		</fieldset>
	</form>
</div>