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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css('component.css');
?>
<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<fieldset>
		<label for="upload">
			<input type="file" class="option" name="upload" id="upload" />
			<input type="submit" class="option" value="<?php echo strtolower(JText::_('COM_TOOLS_UPLOAD')); ?>" />
		</label>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="resource" value="<?php echo $this->resource; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>

	<?php if ($this->getError()) { ?>
		<p class="error">
			<?php echo implode('<br />', $this->getErrors()); ?>
		</p>
	<?php } ?>

	<?php if (count($this->folders) == 0 && count($this->docs) == 0) { ?>
		<p><?php echo JText::_('Here you can upload and manage screenshots or other files you wish to include in your abstract.'); ?></p>
	<?php } else { ?>
		<table>
			<tbody>
			<?php
			$docs = $this->docs;
			for ($i=0; $i<count($docs); $i++)
			{
				$docName = key($docs);

				$subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
			?>
				<tr>
					<td width="100%">
						<?php echo JRoute::_('index.php?option=com_resources&id=' . ($this->row->alias ? $this->row->alias : $this->resource) . '&task=download&file=' . $docs[$docName]); ?>
					</td>
					<td>
						<a class="icon-delete delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=delete&amp;file=<?php echo $docs[$docName]; ?>&amp;resource=<?php echo $this->resource; ?>&amp;tmpl=component&amp;subdir=<?php echo $this->subdir; ?>&amp;<?php echo JUtility::getToken(); ?>=1" target="filer" onclick="return deleteFile('<?php echo $docs[$docName]; ?>');" title="<?php echo JText::_('DELETE'); ?>">
							<span><?php echo JText::_('DELETE'); ?></span>
						</a>
					</td>
				</tr>
			<?php
				next($docs);
			}
			?>
			</tbody>
		</table>
	<?php } ?>

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
