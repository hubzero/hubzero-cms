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
defined('_JEXEC') or die('Restricted access');

?>
<div id="member-picture">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=media'); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
		<fieldset>
			<legend><?php echo JText::_('UPLOAD'); ?> <?php echo JText::_('WILL_REPLACE_EXISTING_IMAGE'); ?></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="media" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />

			<input type="file" name="upload" id="upload" size="17" />
			<input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" />
		</fieldset>

		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

		<table>
			<caption><label for="image"><?php echo JText::_('MEMBER_PICTURE'); ?></label></caption>
			<tbody>
			<?php
			$k = 0;

			if ($this->file && file_exists($this->file_path . DS . $this->file))
			{
				$this_size = filesize($this->file_path . DS . $this->file);
				list($width, $height, $type, $attr) = getimagesize($this->file_path . DS . $this->file);
				?>
				<tr>
					<td rowspan="6"><img src="<?php echo $this->webpath . DS . $this->path . DS . $this->file; ?>" alt="<?php echo JText::_('MEMBER_PICTURE'); ?>" id="conimage" /></td>
					<td><?php echo JText::_('FILE'); ?>:</td>
					<td><?php echo $this->file; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('SIZE'); ?>:</td>
					<td><?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('WIDTH'); ?>:</td>
					<td><?php echo $width; ?> px</td>
				</tr>
				<tr>
					<td><?php echo JText::_('HEIGHT'); ?>:</td>
					<td><?php echo $height; ?> px</td>
				</tr>
				<tr>
					<td><input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" /></td>
					<td><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;task=deleteimg&amp;file=<?php echo $this->file; ?>&amp;id=<?php echo $this->id; ?>&amp;no_html=1">[ <?php echo JText::_('JACTION_DELETE'); ?> ]</a></td>
				</tr>
			<?php } else { ?>
				<tr>
					<td colspan="4">
						<img src="<?php echo $this->default_picture; ?>" alt="<?php echo JText::_('NO_MEMBER_PICTURE'); ?>" />
						<input type="hidden" name="currentfile" value="" />
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</form>
</div>
