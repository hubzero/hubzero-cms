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

//tag editor

ximport('Hubzero_Wiki_Editor');
$editor =& Hubzero_Wiki_Editor::getInstance();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->name . '&task=repost'); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo JText::_('Repost'); ?></legend>

		<label for="field-board">
			<?php echo JText::_('Board'); ?>
			<select name="board_id" id="field-board">
				<option value="0"><?php echo JText::_('Select a board...'); ?></option>
				<optgroup label="<?php echo JText::_('My boards'); ?>">
<?php 
if ($this->myboards)
{
	foreach ($this->myboards as $board)
	{
?>
					<option value="<?php echo $this->escape($board->id); ?>"><?php echo $this->escape(stripslashes($board->title)); ?></option>
<?php
	}
}
?>
				</optgroup>
<?php 
if ($this->groupboards)
{
	foreach ($this->groupboards as $optgroup => $boards)
	{
?>
				<optgroup label="<?php echo $this->escape(stripslashes($optgroup)); ?>">
<?php
		foreach ($boards as $board)
		{
?>
					<option value="<?php echo $this->escape($board->id); ?>"><?php echo $this->escape(stripslashes($board->title)); ?></option>
<?php
		}
?>
				</optgroup>
<?php
	}
}
?>
			</select>
		</label>

		<label for="field_description">
			<?php echo JText::_('Add a description'); ?>
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
			<?php echo $editor->display('description', 'field_description', '', '', '50', '10'); ?>
		</label>
	</fieldset>

	<input type="hidden" name="bulletin_id" value="<?php echo $this->bulletin_id; ?>" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

	<input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
	<input type="hidden" name="task" value="repost" />

	<p class="submit">
		<input type="submit" value="<?php echo JText::_('PLG_GROUPS_' . strtoupper($this->name) . '_POST'); ?>" />
	</p>
</form>
