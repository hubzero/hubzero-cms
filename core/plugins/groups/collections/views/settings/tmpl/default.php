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

			<input type="hidden" name="settings[id]" value="<?php echo $this->settings->id; ?>" />
			<input type="hidden" name="settings[object_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
			<input type="hidden" name="settings[folder]" value="groups" />
			<input type="hidden" name="settings[element]" value="collections" />
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
				<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_CANCEL'); ?>
			</a>
		</p>
	</form>
