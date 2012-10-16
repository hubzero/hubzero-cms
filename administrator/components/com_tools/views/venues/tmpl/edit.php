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

// No direct access
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit' ? JText::_('Edit Venue') : JText::_('New Venue'));

JToolBarHelper::title(JText::_('Tools').': <small><small>[ ' . $text . ' ]</small></small>', 'tools.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

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
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="venue"><?php echo JText::_('Venue'); ?>:</label></td>
						<td colspan="2">
							<input type="text" name="fields[venue]" id="venue" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->venue)); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="master"><?php echo JText::_('Master'); ?>:</label></td>
						<td colspan="2">
							<input type="text" name="fields[master]" id="master" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->master)); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Status'); ?>:</td>
						<td>
							<label for="status-up"><input class="option" type="radio" name="fields[status]" id="status-up" size="30" value="up"<?php if ($this->row->status == 'up') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('up'); ?></label>
						</td>
						<td>
							<label for="status-down"><input class="option" type="radio" name="fields[status]" id="status-down" size="30" value="down"<?php if ($this->row->status == 'down') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('down'); ?></label>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="admin"><?php echo JText::_('Locations'); ?>:</label></td>
						<td colspan="2">
							<select multiple="multiple" name="locations[]" size="10">
								<option value="one">one</option>
								<option value="two">two</option>
								<option value="three">three</option>
<?php
							$found = array('one', 'two', 'three');
							ximport('Hubzero_Geo');
							if ($countries = Hubzero_Geo::getcountries()) 
							{
								foreach ($countries as $country)
								{
									for ($i=0; $i<count($this->locations); $i++)
									{
										if ($this->locations[$i]->location == $country['code']) 
										{
											$found[] = $this->locations[$i]->location;
										}
?>
								<option value="<?php echo $this->escape($country['code']); ?>"<?php if ($this->locations[$i]->location == $country['code']) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($country['name'])); ?></option>
<?php
									}
								}
							}
?>
							</select>
						</td>
						<tr>
							<td class="key"><label for="admin"><?php echo JText::_('Custom locations'); ?>:</label></td>
							<td colspan="2">
<?php
							for ($i=0; $i<count($this->locations); $i++)
							{
								if (!in_array($this->locations[$i]->location, $found)) 
								{
?>
								<input type="text" name="locations[]" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->locations[$i]->location)); ?>" /><br />
<?php
								}
							}
?>
								<input type="text" name="locations[]" size="30" maxlength="255" value="" />
							</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<table class="meta" summary="<?php echo JText::_('Metadata for this item'); ?>">
			<tbody>
				<tr>
					<th scope="row"><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->escape($this->row->id); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('Status'); ?></th>
					<td><?php echo $this->escape($this->row->status); ?></td>
				</tr>
			</tbody>
		</table>
		
		<?php /* <fieldset class="adminform">
			<legend><span><?php echo JText::_('Locations'); ?></span></legend>

			<iframe width="100%" height="200" name="hosts" id="hosts" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=locations&amp;tmpl=component&amp;venue_id=<?php echo $this->row->id; ?>"></iframe>
		</fieldset> */ ?>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>