<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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