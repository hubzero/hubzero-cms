<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Document::setTitle(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller));

Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_' . $this->controller), 'install.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
Toolbar::help('install');

// no direct access
defined('_JEXEC') or die;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.getElementById('item-form');

		// do field validation
		if (form.install_package.value == ""){
			alert("<?php echo Lang::txt('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_PACKAGE', true); ?>");
		} else {
			form.installtype.value = 'upload';
			form.submit();
		}
	}

	Joomla.submitbutton3 = function(pressbutton) {
		var form = document.getElementById('item-form');

		// do field validation
		if (form.install_directory.value == ""){
			alert("<?php echo Lang::txt('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_DIRECTORY', true); ?>");
		} else {
			form.installtype.value = 'folder';
			form.submit();
		}
	}

	Joomla.submitbutton4 = function(pressbutton) {
		var form = document.getElementById('item-form');

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://"){
			alert("<?php echo Lang::txt('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL', true); ?>");
		} else {
			form.installtype.value = 'url';
			form.submit();
		}
	}
</script>

<form enctype="multipart/form-data" action="<?php echo Route::url('index.php?option=com_installer&controller=install');?>" method="post" name="adminForm" id="item-form">

	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>
	<div class="width-70 fltlft">
		<fieldset class="adminform uploadform">
			<legend><span><?php echo Lang::txt('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></span></legend>

			<div class="input-wrap">
				<label for="install_package"><?php echo Lang::txt('COM_INSTALLER_PACKAGE_FILE'); ?></label>
				<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
			</div>
			<div class="input-wrap">
				<input class="button" type="button" value="<?php echo Lang::txt('COM_INSTALLER_UPLOAD_AND_INSTALL'); ?>" onclick="Joomla.submitbutton()" />
			</div>
			<div class="clr"></div>
		</fieldset>

		<fieldset class="adminform uploadform">
			<legend><span><?php echo Lang::txt('COM_INSTALLER_INSTALL_FROM_DIRECTORY'); ?></span></legend>

			<div class="input-wrap">
				<label for="install_directory"><?php echo Lang::txt('COM_INSTALLER_INSTALL_DIRECTORY'); ?></label>
				<input type="text" id="install_directory" name="install_directory" class="input_box" size="70" value="<?php echo $this->state->get('install.directory'); ?>" />
			</div>
			<div class="input-wrap">
				<input type="button" class="button" value="<?php echo Lang::txt('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton3()" />
			</div>
			<div class="clr"></div>
		</fieldset>

		<fieldset class="adminform uploadform">
			<legend><span><?php echo Lang::txt('COM_INSTALLER_INSTALL_FROM_URL'); ?></span></legend>

			<div class="input-wrap">
				<label for="install_url"><?php echo Lang::txt('COM_INSTALLER_INSTALL_URL'); ?></label>
				<input type="text" id="install_url" name="install_url" class="input_box" size="70" value="http://" />
			</div>
			<div class="input-wrap">
				<input type="button" class="button" value="<?php echo Lang::txt('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton4()" />
			</div>
		</fieldset>

		<input type="hidden" name="type" value="" />
		<input type="hidden" name="installtype" value="upload" />

		<input type="hidden" name="option" value="com_installer" />
		<input type="hidden" name="controller" value="install" />
		<input type="hidden" name="task" value="install" />
		<?php echo Html::input('token'); ?>
	</div>
</form>
