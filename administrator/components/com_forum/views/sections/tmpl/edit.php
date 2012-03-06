<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$canDo = ForumHelper::getActions('section');

$text = ($this->task == 'edit' ? JText::_('Edit Section') : JText::_('New Section'));
JToolBarHelper::title(JText::_('Forums') . ': <small><small>[ ' . $text . ' ]</small></small>', 'forum.png');
JToolBarHelper::spacer();	
if ($canDo->get('core.edit')) {
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

$create_date = NULL;
if (intval($this->row->created) <> 0) 
{
	$create_date = JHTML::_('date', $this->row->created);
}

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert( 'Entry must have a title' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
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
						<td class="key"><?php echo JText::_('State'); ?>:</td>
						<td>
							<select name="fields[state]">
								<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Unpublished'); ?></option>
								<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Published'); ?></option>
								<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Trashed'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Access'); ?>:</td>
						<td>
							<select name="fields[access]">
								<option value="0"<?php echo ($this->row->access == 0) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Public'); ?></option>
								<option value="1"<?php echo ($this->row->access == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Registered'); ?></option>
								<option value="2"<?php echo ($this->row->access == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Special'); ?></option>
								<option value="3"<?php echo ($this->row->access == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Protected'); ?></option>
								<option value="4"<?php echo ($this->row->access == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Private'); ?></option>
							</select>
						</td>
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
							echo $this->escape($editor->get('name')); 
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->row->created_by; ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('Created Date'); ?>:</th>
						<td>
							<?php echo $this->row->created; ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->created; ?>" />
						</td>
					</tr>
					
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

<?php if (version_compare(JVERSION, '1.6', 'ge')) { ?>
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
<?php } ?>
	
	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
