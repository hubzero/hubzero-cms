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

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=forum';

$this->css()
     ->js();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>
	<form action="<?php echo Route::url($base . '&action=savesettings'); ?>" method="post" id="hubForm" class="full">
		<fieldset class="settings">
			<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADS'); ?></legend>

			<label for="param-threading">
				<?php echo Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADING'); ?>
				<select name="params[threading]" id="param-threading">
					<option value="list"<?php if ($this->config->get('threading', 'list') == 'list') { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_GROUPS_FORUM_SETTINGS_LIST'); ?></option>
					<option value="tree"<?php if ($this->config->get('threading', 'list') == 'tree') { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_GROUPS_FORUM_SETTINGS_TREE'); ?></option>
				</select>
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADING_HINT'); ?>
			</label>

			<label for="param-threading_depth">
				<?php echo Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADING_DEPTH'); ?>
				<input type="text" name="params[threading_depth]" id="param-threading_depth" value="<?php echo $this->config->get('threading_depth', 3); ?>" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADING_DEPTH_HINT'); ?></span>
			</label>

			<input type="hidden" name="settings[id]" value="<?php echo $this->settings->get('id'); ?>" />
			<input type="hidden" name="settings[object_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
			<input type="hidden" name="settings[folder]" value="groups" />
			<input type="hidden" name="settings[element]" value="forum" />
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="forum" />
		<input type="hidden" name="action" value="savesettings" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />

			<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
				<?php echo Lang::txt('PLG_GROUPS_FORUM_CANCEL'); ?>
			</a>
		</p>
	</form>
