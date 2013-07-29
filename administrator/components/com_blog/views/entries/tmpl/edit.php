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

if ($this->task == 'add') {
	$txt = JText::_('Add');
} else {
	$txt = JText::_('Edit');
}

$text = ($this->task == 'edit' ? JText::_('Edit entry') : JText::_('New entry'));
JToolBarHelper::title(JText::_('Blog Manager') . ': <small><small>[ ' . $text . ' ]</small></small>', 'blog.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
	JToolBarHelper::apply();
}
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (form.greeting.value == ''){
		alert(<?php echo JText::_('Error! You must fill in a title!'); ?>);
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
		<legend><span><?php echo JText::_('Details'); ?></span></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="field-title"><?php echo JText::_('Title'); ?>:</label></td>
					<td><input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="field-alias"><?php echo JText::_('Alias'); ?>:</label></td>
					<td><input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="field-scope"><?php echo JText::_('Scope'); ?>:</label></td>
					<td>
						<select name="fields[scope]" id="field-scope">
							<option value="site"<?php if ($this->row->scope == 'site' || $this->row->scope == '') { echo ' selected="selected"'; } ?>>site</option>
							<option value="member"<?php if ($this->row->scope == 'member') { echo ' selected="selected"'; } ?>>member</option>
							<option value="group"<?php if ($this->row->scope == 'group') { echo ' selected="selected"'; } ?>>group</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="field-group_id"><?php echo JText::_('Group'); ?>:</label></td>
					<td>
						<?php
						ximport('Hubzero_Group');
						$filters = array();
						$filters['authorized'] = 'admin';
						$filters['fields'] = array('cn','description','published','gidNumber','type');
						$filters['type'] = array(1,3);
						$filters['sortby'] = 'description';
						$groups = Hubzero_Group::find($filters);
						
						$html  = '<select name="fields[group_id]" id="field-group_id">'."\n";
						$html .= '<option value="0"';
						if ($this->row->group_id == 0) 
						{
							$html .= ' selected="selected"';
						}
						$html .= '>'.JText::_('None').'</option>'."\n";
						if ($groups) 
						{
							foreach ($groups as $group)
							{
								$html .= ' <option value="'.$group->gidNumber.'"';
								if ($this->row->group_id == $group->gidNumber) 
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
					<td class="key"><label for="field-content"><?php echo JText::_('Content'); ?></label></td>
					<td><textarea name="fields[content]" id="field-content" cols="35" rows="15"><?php echo $this->escape(stripslashes($this->row->content)); ?></textarea></td>
				</tr>
				<tr>
					<td class="key"><label for="field-allow_comments"><?php echo JText::_('Allow comments'); ?></label></td>
					<td><input class="option" type="checkbox" name="fields[allow_comments]" id="field-allow_comments" value="1"<?php if ($this->row->allow_comments) { echo ' checked="checked"'; } ?> /></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('State'); ?>:</td>
					<td>
						<select name="fields[state]">
							<option value="1"<?php if ($this->row->state == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Public (anyone can see)'); ?></option>
							<option value="2"<?php if ($this->row->state == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('Registered members'); ?></option>
							<option value="0"<?php if ($this->row->state == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Private (only I can see)'); ?></option>
							<option value="-1"<?php if ($this->row->state == -1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Trashed'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="field-tags"><?php echo JText::_('Tags'); ?></label></td>
					<td><textarea name="tags" id="field-tags" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->tags)); ?></textarea></td>
				</tr>
			</tbody>
		</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<table class="meta" summary="<?php echo JText::_('Metadata for this forum section'); ?>">
				<tbody>
					<tr>
						<th class="key"><?php echo JText::_('Created By'); ?>:</th>
						<td>
							<?php 
							$editor = JUser::getInstance($this->row->created_by);
							echo $this->escape(stripslashes($editor->get('name'))); 
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->created_by); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Created Date'); ?>:</th>
						<td>
							<?php echo $this->row->created; ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->created); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Hits'); ?>:</th>
						<td>
							<?php echo $this->row->hits; ?>
							<input type="hidden" name="fields[hits]" id="field-hits" value="<?php echo $this->escape($this->row->hits); ?>" />
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
						<th class="key"><?php echo JText::_('Publish up'); ?>:</th>
						<td>
							<input type="text" name="fields[publish_up]" id="field-publish_up" value="<?php echo $this->escape($this->row->publish_up); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Publish down'); ?>:</th>
						<td>
							<input type="text" name="fields[publish_down]" id="field-publish_down" value="<?php echo $this->escape($this->row->publish_down); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

<?php /*if (version_compare(JVERSION, '1.6', 'ge')) { ?>
	<?php if ($canDo->get('core.admin')): ?>
		<div class="col width-100 fltlft">
			<fieldset class="panelform">
				<legend><span><?php echo JText::_('COM_FORUM_FIELDSET_RULES'); ?></span></legend>
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
		<div class="clr"></div>
	<?php endif; ?>
<?php }*/ ?>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>

