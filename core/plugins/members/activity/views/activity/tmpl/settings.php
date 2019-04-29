<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="<?php echo Route::url($this->member->link() . '&active=activity&task=savesettings'); ?>" method="post" id="hubForm" class="full">
	<fieldset class="settings">
		<legend><?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_DIGESTS'); ?></legend>

		<label for="field-settings-frequency-none">
			<input type="radio" name="settings[frequency]" id="field-settings-frequency-none" value="0" <?php if (!$this->settings->get('frequency')) { echo ' checked="checked"'; }?> />
			<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_FREQUENCY_NONE'); ?>
		</label>

		<label for="field-settings-frequency-daily">
			<input type="radio" name="settings[frequency]" id="field-settings-frequency-daily" value="2" <?php if ($this->settings->get('frequency') == 2) { echo ' checked="checked"'; }?> />
			<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_FREQUENCY_DAILY'); ?>
		</label>

		<label for="field-settings-frequency-weekly">
			<input type="radio" name="settings[frequency]" id="field-settings-frequency-weekly" value="3" <?php if ($this->settings->get('frequency') == 3) { echo ' checked="checked"'; }?> />
			<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_FREQUENCY_WEEKLY'); ?>
		</label>

		<label for="field-settings-frequency-monthly">
			<input type="radio" name="settings[frequency]" id="field-settings-frequency-monthly" value="4" <?php if ($this->settings->get('frequency') == 4) { echo ' checked="checked"'; }?> />
			<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS_FREQUENCY_MONTHLY'); ?>
		</label>

		<input type="hidden" name="settings[id]" value="<?php echo $this->settings->get('id', 0); ?>" />
		<input type="hidden" name="settings[scope_id]" value="<?php echo $this->member->get('id'); ?>" />
		<input type="hidden" name="settings[scope]" value="user" />
	</fieldset>
	<div class="clear"></div>

	<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
	<input type="hidden" name="option" value="com_members" />
	<input type="hidden" name="active" value="activity" />
	<input type="hidden" name="action" value="savesettings" />

	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_SAVE'); ?>" />

		<a class="btn btn-secondary" href="<?php echo Route::url($this->member->link() . '&active=activity'); ?>">
			<?php echo Lang::txt('JCANCEL'); ?>
		</a>
	</p>
</form>
