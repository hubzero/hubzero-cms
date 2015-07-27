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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_ADD_CHILD'), 'addedit.png' );
Toolbar::cancel();

Request::setVar('hidemainmenu', 1);
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<h3><?php echo stripslashes($this->parent->title); ?></h3>

	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_CHOOSE'); ?></span></legend>

		<?php if ($this->getError()) { echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>'; } ?>

		<div class="col width-50 fltlft">
			<div class="input-wrap">
				<input type="radio" name="method" id="child_create" value="create" checked="checked" />
				<label for="child_create"><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_CREATE'); ?></label>
			</div>
		</div>
		<div class="col width-50 fltrt">
			<div class="input-wrap">
				<input type="radio" name="method" id="child_existing" value="existing" />
				<label for="child_existing"><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_EXISTING'); ?></label>
			</div>
			<div class="input-wrap">
				<label for="childid"><?php echo Lang::txt('COM_RESOURCES_FIELD_RESOURCE_ID'); ?>:</label>
				<input type="text" name="childid" id="childid" value="" />
			</div>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

		<?php echo Html::input('token'); ?>
	</fieldset>

	<p class="align-center"><input type="submit" name="Submit" value="<?php echo Lang::txt('COM_RESOURCES_NEXT'); ?>" /></p>
</form>
