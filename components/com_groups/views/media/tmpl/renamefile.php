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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// get file info
$fileInfo = pathinfo($this->file);
?>

<form action="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=dorenamefile&no_html=1'); ?>" method="post" class="hubForm">
	<fieldset>
		<legend><?php echo JTexT::_('COM_GROUPS_MEDIA_RENAME_FILE'); ?></legend>
		<label>
			<?php echo JText::_('COM_GROUPS_MEDIA_RENAME_CURRENT_NAME'); ?>:<br />
			<input type="hidden" name="file" value="<?php echo $this->file; ?>" />

			<input type="text" name="name" value="<?php echo $fileInfo['basename']; ?>" />
		</label>
		<p class="controls">
			<button type="submit" class="btn icon-edit"><?php echo JTexT::_('COM_GROUPS_MEDIA_RENAME'); ?></button>
		</p>
	</fieldset>
</form>