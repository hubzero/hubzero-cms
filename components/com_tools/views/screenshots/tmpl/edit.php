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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$size = getimagesize($this->upath . DS . $this->file);
$w = ($size[0] > 600) ? $size[0]/1.4444444 : $size[0];
$h = ($w != $size[0]) ? $size[1]/1.4444444 : $size[1];

$title = (count($this->shot) > 0 && isset($this->shot[0]->title)) ? $this->shot[0]->title : ''; 
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
	<div class="ss_pop">
		<div>
			<img src="<?php echo $this->wpath . DS . $this->file; ?>" width="<?php echo $w; ?>" height="<?php echo $h; ?>" alt="" />
		</div>
		<form action="index.php" name="hubForm" id="ss-pop-form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pid; ?>" />
			<input type="hidden" name="path" id="path" value="<?php echo $this->upath; ?>" />
            <input type="hidden" name="filename" id="filename" value="<?php echo $this->file; ?>" />
            <input type="hidden" name="vid" id="vid" value="<?php echo $this->vid; ?>" />
			<input type="hidden" name="task" value="save" />
			<fieldset class="uploading">
				<label class="ss_title" for="ss_title">
					<?php echo JText::_('COM_TOOLS_SS_TITLE'); ?>:
					<input type="text" name="title" id="ss_title"  size="127" maxlength="127" value="<?php echo $this->escape($title); ?>" class="input_restricted" />
				</label>
				<input type="submit" id="ss_pop_save" value="<?php echo strtolower(JText::_('COM_TOOLS_SAVE')); ?>" />
			</fieldset>
 		</form>
	</div>