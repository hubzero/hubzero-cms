<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') .'&active=collections&action=savesettings'); ?>" method="post" id="hubForm" class="full">

		<fieldset class="settings">
			<legend><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS'); ?></legend>

			<label for="param-posting">
				<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS'); ?>
				<select name="params[create_collection]" id="param-create_collection">
					<option value="0"<?php if (!$this->params->get('create_collection', 1)) { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_ALL'); ?></option>
					<option value="1"<?php if ($this->params->get('create_collection', 1) == 1) { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_MANAGERS'); ?></option>
				</select>
			</label>

			<p class="info">
				<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_INFO'); ?>
			</p>
		</fieldset>
		<fieldset class="settings">
			<legend><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_POSTS'); ?></legend>

			<label for="param-posting">
				<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS'); ?>
				<select name="params[create_post]" id="param-create_post">
					<option value="0"<?php if (!$this->params->get('create_post', 0)) { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_ALL'); ?></option>
					<option value="1"<?php if ($this->params->get('create_post', 0) == 1) { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_MANAGERS'); ?></option>
				</select>
			</label>

			<p class="info">
				<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_INFO'); ?>
			</p>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="collections" />
		<input type="hidden" name="action" value="savesettings" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SAVE'); ?>" />

			<a class="btn btn-secondary" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=collections'); ?>">
				<?php echo Lang::txt('JCANCEL'); ?>
			</a>
		</p>
	</form>
