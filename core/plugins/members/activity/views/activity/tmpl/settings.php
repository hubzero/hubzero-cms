<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<ul id="page_options">
	<li>
		<a class="icon-archive btn" href="<?php echo Route::url($this->member->link() . '&active=activity'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY'); ?>
		</a>
	</li>
</ul>

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
			<?php echo Lang::txt('PLG_MEMBERS_ACTIVITY_CANCEL'); ?>
		</a>
	</p>
</form>
