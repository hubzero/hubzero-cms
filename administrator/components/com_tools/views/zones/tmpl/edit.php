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

JToolBarHelper::title(JText::_('Tools').': ' . $text, 'tools.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

JHtml::_('behavior.modal');
JHtml::_('behavior.switcher');
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
document.switcher = null;
window.addEvent('domready', function(){
	toggler = document.id('submenu');
	element = document.id('zone-document');
	if (element) {
		document.switcher = new JSwitcher(toggler, element, {cookieName: toggler.getProperty('class')});
	}

	SqueezeBox.initialize({});
	document.assetform = SqueezeBox;
});
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="member">
						<li><a href="#" onclick="return false;" id="profile" class="active">Profile</a></li>
						<li><a href="#" onclick="return false;" id="locations">Locations</a></li>
						<!-- <li><a href="index.php?option=com_tools&amp;controller=zones&amp;task=locations&amp;id=<?php echo $this->row->get('id'); ?>" id="locations">Locations</a></li> -->
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

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field-zone"><?php echo JText::_('Zone'); ?>:</label></th>
						<td colspan="2"><input type="text" name="fields[zone]" id="field-zone" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('zone'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-master"><?php echo JText::_('Master'); ?>:</label></th>
						<td colspan="2"><input type="text" name="fields[master]" id="field-master" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('master'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-type"><?php echo JText::_('Type'); ?>:</label></th>
						<td colspan="2">
							<select name="fields[type]" id="field-type">
								<option value="local"<?php if ($this->row->get('type') == 'local') { echo ' selected="selected"'; } ?>><?php echo JText::_('Local'); ?></option>
								<option value="remote"<?php if ($this->row->get('type') == 'remote') { echo ' selected="selected"'; } ?>><?php echo JText::_('Remote'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('State'); ?>:</th>
						<td><label for="field-state-up"><input class="option" type="radio" name="fields[state]" id="field-state-up" size="30" value="up"<?php if ($this->row->get('state') == 'up') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('up'); ?></label></td>
						<td><label for="field-state-down"><input class="option" type="radio" name="fields[state]" id="field-state-down" size="30" value="down"<?php if ($this->row->get('state') == 'down') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('down'); ?></label></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		</div>
		<div class="col width-40 fltrt">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo JText::_('ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('id')); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('State'); ?></th>
						<td><?php echo $this->escape($this->row->get('state')); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="clr"></div>
		</div>

		<div id="page-locations" class="tab">
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('Locations'); ?></span></legend>
			<?php if ($this->row->get('id')) { ?>
				<iframe width="100%" height="400" name="locations" id="locations" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=locations&amp;tmpl=component&amp;zone=<?php echo $this->row->get('id'); ?>"></iframe>
			<?php } else { ?>
				<p><?php echo JText::_('Course must be saved before managers can be added.'); ?></p>
			<?php } ?>
		</fieldset>
		</div>
	<?php echo JHTML::_('form.token'); ?>
</form>
