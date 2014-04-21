<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit' ? JText::_('Edit Zone') : JText::_('New Zone'));

JToolBarHelper::title(JText::_('Tools').': <small><small>[ ' . $text . ' ]</small></small>', 'tools.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor = JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
        <nav role="navigation" class="sub-navigation">
                <div id="submenu-box">
                        <div class="submenu-box">
                                <div class="submenu-pad">
                                        <ul id="submenu" class="member">
                                                <li><a href="#" onclick="return false;" id="profile" class="active">Profile</a></li>
                                                <li><a href="index.php?option=com_tools&controller=zones&task=locations&id=<?php echo $this->row->id;?>" id="locations">Locations</a></li>
                                        </ul>
                                        <div class="clr"></div>
                                </div>
                        </div>
                        <div class="clr"></div>
                </div>
        </nav><!-- / .sub-navigation -->

	<div id="zone-document">
		<div id="page-profile" class="tab">
		<div class="col width-60 fltlft">

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('ZONE_PROFILE'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />

			<table class="admintable">
			 <tbody>
		  	  <tr>
			   <td class="key"><label for="zone"><?php echo JText::_('Zone'); ?>:</label></td>
			   <td colspan="2"><input type="text" name="fields[zone]" id="zone" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->zone)); ?>" /></td>
			  </tr>
			  <tr>
			    <td class="key"><label for="master"><?php echo JText::_('Master'); ?>:</label></td>
			    <td colspan="2"><input type="text" name="fields[master]" id="master" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->master)); ?>" /></td>
			  </tr>
			  <tr>
			    <td class="key"><?php echo JText::_('State'); ?>:</td>
			    <td><label for="state-up"><input class="option" type="radio" name="fields[state]" id="state-up" size="30" value="up"<?php if ($this->row->state == 'up') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('up'); ?></label></td>
			    <td><label for="state-down"><input class="option" type="radio" name="fields[state]" id="state-down" size="30" value="down"<?php if ($this->row->state == 'down') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('down'); ?></label></td>
			  </tr>
			 </tbody>
			</table>
		</fieldset>
		</div>
		<div class="col width-40 fltrt">
			<table class="meta" summary="<?php echo JText::_('Metadata for this item'); ?>">
			<tbody>
				<tr>
					<th scope="row"><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->escape($this->row->id); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('State'); ?></th>
					<td><?php echo $this->escape($this->row->state); ?></td>
				</tr>
			</tbody>
			</table>
		</div>
		<div class="clr"></div>
		</div>
	<?php echo JHTML::_('form.token'); ?>
</form>
