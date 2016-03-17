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
		<a class="icon-archive btn" href="<?php echo Route::url($this->member->getLink() . '&active=blog'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_BLOG_ARCHIVE'); ?>
		</a>
	</li>
</ul>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="<?php echo Route::url($this->member->getLink() . '&active=blog&task=savesettings'); ?>" method="post" id="hubForm" class="full">
	<fieldset class="settings">
		<legend><?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_POSTS'); ?></legend>
		<p><?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_POSTS_EXPLANATION'); ?></p>
	</fieldset>
	<fieldset class="settings">
		<legend><?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_FEEDS'); ?></legend>

		<label for="field-param-feeds_enabled">
			<?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_ENTRY_FEED'); ?>
			<select name="params[feeds_enabled]" id="field-param-feeds_enabled">
				<option value="0"<?php if (!$this->config->get('feeds_enabled', 1)) { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_DISABLED'); ?></option>
				<option value="1"<?php if ($this->config->get('feeds_enabled', 1) == 1) { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_ENABLED'); ?></option>
			</select>
		</label>

		<label for="field-params-feed_entries">
			<?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_FEED_ENTRY_LENGTH'); ?>
			<select name="params[feed_entries]" id="field-params-feed_entries">
				<option value="full"<?php if ($this->config->get('feed_entries', 'partial') == 'full') { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_FULL'); ?></option>
				<option value="partial"<?php if ($this->config->get('feed_entries', 'partial') == 'partial') { echo ' selected="selected"'; }?>><?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_PARTIAL'); ?></option>
			</select>
		</label>

		<p class="help">
			<?php echo Lang::txt('PLG_MEMBERS_BLOG_SETTINGS_FEED_HELP'); ?>
		</p>

		<input type="hidden" name="settings[id]" value="<?php echo $this->settings->id; ?>" />
		<input type="hidden" name="settings[object_id]" value="<?php echo $this->member->get('uidNumber'); ?>" />
		<input type="hidden" name="settings[folder]" value="members" />
		<input type="hidden" name="settings[element]" value="blog" />
	</fieldset>
	<div class="clear"></div>

	<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
	<input type="hidden" name="process" value="1" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="active" value="blog" />
	<input type="hidden" name="action" value="savesettings" />

	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_BLOG_SAVE'); ?>" />

		<a class="btn btn-secondary" href="<?php echo Route::url($this->member->getLink() . '&active=blog'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_BLOG_CANCEL'); ?>
		</a>
	</p>
</form>
