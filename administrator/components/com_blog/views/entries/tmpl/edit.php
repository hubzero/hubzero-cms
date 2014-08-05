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

$canDo = BlogHelper::getActions('entry');

$text = ($this->task == 'edit' ? JText::_('Edit entry') : JText::_('New entry'));
JToolBarHelper::title(JText::_('Blog Manager') . ': ' . $text, 'blog.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('entry.html', true);
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm,
		title = document.getElementById('field-title');

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (title.value == ''){
		alert("<?php echo JText::_('Error! You must fill in a title!'); ?>");
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
		<legend><span><?php echo JText::_('Details'); ?></span></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<label for="field-scope"><?php echo JText::_('Scope'); ?>:</label><br />
						<select name="fields[scope]" id="field-scope">
							<option value="site"<?php if ($this->row->get('scope') == 'site' || $this->row->get('scope') == '') { echo ' selected="selected"'; } ?>>site</option>
							<option value="member"<?php if ($this->row->get('scope') == 'member') { echo ' selected="selected"'; } ?>>member</option>
							<option value="group"<?php if ($this->row->get('scope') == 'group') { echo ' selected="selected"'; } ?>>group</option>
						</select>
					</td>
					<td class="key">
						<label for="field-group_id"><?php echo JText::_('Group'); ?>:</label><br />
						<?php
						$filters = array();
						$filters['authorized'] = 'admin';
						$filters['fields'] = array('cn','description','published','gidNumber','type');
						$filters['type'] = array(1,3);
						$filters['sortby'] = 'description';
						$groups = \Hubzero\User\Group::find($filters);
						
						$html  = '<select name="fields[group_id]" id="field-group_id">'."\n";
						$html .= '<option value="0"';
						if ($this->row->get('group_id') == 0) 
						{
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('None').'</option>'."\n";
						if ($groups) 
						{
							foreach ($groups as $group)
							{
								$html .= ' <option value="'.$group->gidNumber.'"';
								if ($this->row->get('group_id') == $group->gidNumber) 
								{
									$html .= ' selected="selected"';
								}
								$html .= '>' . $this->escape(stripslashes($group->description)) . '</option>'."\n";
							}
						}
						$html .= '</select>'."\n";
						echo $html;
						?>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="field-title"><?php echo JText::_('Title'); ?>: <span class="required">required</span></label><br />
						<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="field-alias"><?php echo JText::_('Alias'); ?>:</label><br />
						<input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="field-content"><?php echo JText::_('Content'); ?>: <span class="required">required</span></label><br />
						<textarea name="fields[content]" id="field-content" cols="35" rows="30"><?php echo $this->escape($this->row->content('raw')); ?></textarea>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="field-tags"><?php echo JText::_('Tags'); ?>:</label><br />
						<textarea name="tags" id="field-tags" cols="35" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo JText::_('Created By'); ?>:</th>
						<td>
							<?php 
							$editor = JUser::getInstance($this->row->get('created_by'));
							echo $this->escape(stripslashes($editor->get('name'))); 
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Created Date'); ?>:</th>
						<td>
							<?php echo $this->row->get('created'); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Hits'); ?>:</th>
						<td>
							<?php echo $this->row->get('hits'); ?>
							<input type="hidden" name="fields[hits]" id="field-hits" value="<?php echo $this->escape($this->row->get('hits')); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Publishing'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><?php echo JText::_('State'); ?>:</th>
						<td>
							<select name="fields[state]">
								<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Public (anyone can see)'); ?></option>
								<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Registered members'); ?></option>
								<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Private (only I can see)'); ?></option>
								<option value="-1"<?php if ($this->row->get('state') == -1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Trashed'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Publish up'); ?>:</th>
						<td>
							<input type="text" name="fields[publish_up]" id="field-publish_up" value="<?php echo $this->escape($this->row->get('publish_up')); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Publish down'); ?>:</th>
						<td>
							<input type="text" name="fields[publish_down]" id="field-publish_down" value="<?php echo $this->escape($this->row->get('publish_down')); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-allow_comments"><?php echo JText::_('Allow comments'); ?></label></th>
						<td><input class="option" type="checkbox" name="fields[allow_comments]" id="field-allow_comments" value="1"<?php if ($this->row->get('allow_comments')) { echo ' checked="checked"'; } ?> /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>