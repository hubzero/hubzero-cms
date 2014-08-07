<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css($this->controller);
?>

<div class="com_time_permissions_container">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="POST">
		<div class="permissions-box">
			<fieldset class="permissions">
				<?php echo $this->permissions->label; ?>
				<?php echo $this->permissions->input; ?>
			</fieldset>
		</div>
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="scope" value="<?php echo $this->scope; ?>" />
		<input type="hidden" name="scope_id" value="<?php echo $this->scope_id; ?>" />
		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_TIME_PERMISSIONS_SAVE'); ?>" />
			<button type="button" class="btn btn-secondary cancel"><?php echo JText::_('COM_TIME_PERMISSIONS_CANCEL'); ?></button>
		</p>
	</form>
</div>