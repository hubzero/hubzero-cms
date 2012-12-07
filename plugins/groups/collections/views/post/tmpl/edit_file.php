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

$item = $this->entry->item();

//tag editor
ximport('Hubzero_Wiki_Editor');
$editor =& Hubzero_Wiki_Editor::getInstance();
?>
		<div id="post-file" class="fieldset">
			<a name="file"></a>
			<div class="field-wrap">
<?php 
	$assets = $item->assets();
	if ($assets->total() > 0) 
	{ 
		foreach ($assets as $asset)
		{
?>
					<p class="file-drop">
						<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->name . '&scope=post/' . $item->get('id') . '/edit&remove=' . $asset->get('id')); ?>">delete</a>
						<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>
						<input type="hidden" name="asset[<?php echo $asset->get('id'); ?>][id]" value="<?php echo $asset->get('id'); ?>" />
						<span><input type="text" name="asset[<?php echo $asset->get('id'); ?>][description]" size="35" value="<?php echo $this->escape(stripslashes($asset->get('description'))); ?>" placeholder="Brief description" /></span>
					</p>
<?php 
		}
	}
?>
				<p class="file-drop">
					<input type="file" name="fls[]" />
					<span><input type="text" name="description[]" value="" size="35" placeholder="Brief description (optional)" /></span>
				</p>
				<p class="file-add">
					Max size: <strong>10 Mb</strong>
					<a href="#" class="add btn">Add another file</a>
				</p>
			</div>
			
			<label for="field_description">
				<?php echo JText::_('Description'); ?> <span class="optional">optional</span>
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
						<tr>
							<td>[http://hubzero.org A link]</td>
							<td><a href=&quot;http://hubzero.org&quot;>A link</a></td>
						</tr>
					</tbody>
				</table>">Wiki formatting</a> is allowed.</span>
				<?php //echo $editor->display('fields[description]', 'field_description', $this->escape(stripslashes($this->entry->description)), '', '50', '5'); ?>
				<textarea name="fields[description]" id="field_description" cols="50" rows="5"><?php echo $this->escape(stripslashes($item->get('description'))); ?></textarea>
			</label>
			<?php if ($this->task == 'save' && !$item->get('description')) { ?>
				<p class="error"><?php echo JText::_('PLG_GROUPS_' . strtoupper($this->name) . '_ERROR_PROVIDE_CONTENT'); ?></p>
			<?php } ?>
			<input type="hidden" name="fields[type]" value="file" />
		</div>
