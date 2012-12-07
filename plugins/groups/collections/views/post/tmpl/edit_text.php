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

$item = $this->entry->item();

ximport('Hubzero_Wiki_Editor');
$editor =& Hubzero_Wiki_Editor::getInstance();
?>
		<div id="post-text" class="fieldset">
			<a name="text"></a>
			<label for="field-title"<?php if ($this->task == 'save' && !$item->get('title')) { echo ' class="fieldWithErrors"'; } ?>>
				<?php echo JText::_('Title'); ?> <span class="optional">optional</span>
				<input type="text" name="fields[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($item->get('title'))); ?>" />
			</label>
			<?php if ($this->task == 'save' && !$item->get('title')) { ?>
				<p class="error"><?php echo JText::_('PLG_GROUPS_' . strtoupper($this->name) . '_ERROR_PROVIDE_TITLE'); ?></p>
			<?php } ?>

			<label for="field_description">
				<?php echo JText::_('Post'); ?>
				<span class="syntax hint">limited <a class="tooltips" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>" title="Syntax Reference :: <table class=&quot;wiki-reference&quot;>
					<tbody>
						<tr>
							<td>'''bold'''</td>
							<td><b>bold</b></td>
						</tr>
						<tr>
							<td>''italic''</td>
							<td><i>italic</i></td>
						</tr>
						<tr>
							<td>__underline__</td>
							<td><span style=&quot;text-decoration:underline;&quot;>underline</span></td>
						</tr>
						<tr>
							<td>{{{monospace}}}</td>
							<td><code>monospace</code></td>
						</tr>
						<tr>
							<td>~~strike-through~~</td>
							<td><del>strike-through</del></td>
						</tr>
						<tr>
							<td>^superscript^</td>
							<td><sup>superscript</sup></td>
						</tr>
						<tr>
							<td>,,subscript,,</td>
							<td><sub>subscript</sub></td>
						</tr>
					</tbody>
				</table>">Wiki formatting</a> is allowed.</span>
				<?php echo $editor->display('fields[description]', 'field_description', $this->escape(stripslashes($item->get('description'))), '', '50', '10'); ?>
			</label>
			<?php if ($this->task == 'save' && !$item->get('description')) { ?>
				<p class="error"><?php echo JText::_('PLG_GROUPS_' . strtoupper($this->name) . '_ERROR_PROVIDE_CONTENT'); ?></p>
			<?php } ?>
			<input type="hidden" name="fields[type]" value="text" />
		</div>