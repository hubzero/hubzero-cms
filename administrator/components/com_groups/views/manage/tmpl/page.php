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
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'editpage' ? JText::_('Edit Page') : JText::_('New Page'));

JToolBarHelper::title(JText::_('COM_GROUPS').': <small><small>[ ' . $text . ' ]</small></small>', 'groups.png');
JToolBarHelper::save('savepage');
JToolBarHelper::cancel('cancelpage');
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-100">
		<fieldset class="adminform">
			<legend>Group Page</legend>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="task" value="savepage" />
			<input type="hidden" name="page[id]" value="<?php echo $this->page->id; ?>" />
			<input type="hidden" name="page[gid]" value="<?php echo $this->group->get('gidNumber'); ?>" />
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="title"><?php echo JText::_('Title'); ?>:</label></td>
						<td>
							<input type="text" name="page[title]" value="<?php echo $this->page->title; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="url"><?php echo JText::_('URL'); ?>:</label></td>
						<td>
							<input type="text" name="page[url]" value="<?php echo $this->page->url; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="content"><?php echo JText::_('Content'); ?>:</label></td>
						<td>
							<textarea name="page[content]" rows="10" columns="10"><?php echo $this->page->content; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="content"><?php echo JText::_('Active'); ?>:</label></td>
						<td>
							<input type="radio" name="page[active]" value="1" <?php if($this->page->active) { echo 'checked="checked"'; } ?> /> <font color="green">Yes</font>
							<input type="radio" name="page[active]" value="0" <?php if(!$this->page->active) { echo 'checked="checked"'; } ?> /> <font color="red">No</font>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="content"><?php echo JText::_('Privacy'); ?>:</label></td>
						<td>
							<select name="page[privacy]" id="">
								<option value="default" <?php if($this->page->privacy == 'default') { echo 'selected="selected"'; } ?>>Inherit Overview Tabs Privacy</option>
								<option value="members" <?php if($this->page->privacy == 'members') { echo 'selected="selected"'; } ?>>Members Only</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
</form>