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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_HELP'), 'help.png');

$this->css();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<div class="col-row">
		<div class="col width-25 fltlft">
			<h3><?php echo Lang::txt('COM_HELP_USERS'); ?></h3>
			<ul>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_users'); ?>"><?php echo Lang::txt('COM_HELP_USER_ACCOUNTS'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_members'); ?>"><?php echo Lang::txt('COM_HELP_USER_PROFILES'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_groups'); ?>"><?php echo Lang::txt('COM_HELP_USER_GROUPS'); ?></a>
				</li>
			</ul>
			<h3><?php echo Lang::txt('COM_HELP_MENUS'); ?></h3>
			<ul>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_menus'); ?>"><?php echo Lang::txt('COM_HELP_MENU_MANAGER'); ?></a>
				</li>
			</ul>
			<h3><?php echo Lang::txt('COM_HELP_CONTENT'); ?></h3>
			<ul>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_content'); ?>"><?php echo Lang::txt('COM_HELP_ARTICLE_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_categories'); ?>"><?php echo Lang::txt('COM_HELP_CATEGORY_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_media'); ?>"><?php echo Lang::txt('COM_HELP_MEDIA_MANAGER'); ?></a>
				</li>
			</ul>
			<h3><?php echo Lang::txt('COM_HELP_EXTENSIONS'); ?></h3>
			<ul>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_modules'); ?>"><?php echo Lang::txt('COM_HELP_MODULE_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_plugins'); ?>"><?php echo Lang::txt('COM_HELP_PLUGIN_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_templates'); ?>"><?php echo Lang::txt('COM_HELP_TEMPLATE_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_languages'); ?>"><?php echo Lang::txt('COM_HELP_LANGUAGE_MANAGER'); ?></a>
				</li>
			</ul>
			<h3><?php echo Lang::txt('Components'); ?></h3>
			<ul>
				<?php foreach ($this->components as $component) { ?>
					<li>
						<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=' . $component->element); ?>"><?php echo Lang::txt($component->text); ?></a>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="col width-75 fltrt">
			<iframe id="help-page" src="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_help&page=index'); ?>"></iframe>
		</div>
	</div>
	<div class="clr"></div>
</form>