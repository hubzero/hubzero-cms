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

JToolBarHelper::title(JText::_('COM_RESOURCES') . ': ' . JText::_('COM_RESOURCES_TAGS') . ' #' . $this->row->id, 'addedit.png');
JToolBarHelper::save();
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

	submitform(pressbutton);
}
function addtag(tag)
{
	var input = document.getElementById('tags-men');
	if (input.value == '') {
		input.value = tag;
	} else {
		input.value += ', '+tag;
	}
}
</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_RESOURCES_TAGS_CREATE'); ?></span></legend>

		<p><?php echo JText::_('COM_RESOURCES_TAGS_CREATE_HELP'); ?></p>

		<div class="input-wrap">
			<label for="tags-men"><?php echo JText::_('COM_RESOURCES_TAGS_FIELD_NEW_TAGS'); ?>:</label>
			<input type="text" name="tags" id="tags-men" size="65" value="" />
		</div>
	</fieldset>

	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_RESOURCES_TAGS_EXISTING'); ?></span></legend>
		<p><?php echo JText::_('COM_RESOURCES_TAGS_EXISTING_HELP'); ?></p>

		<table class="adminlist">
			<thead>
				<tr>
					<th></th>
					<th><?php echo JText::_('COM_RESOURCES_TAGS_RAW_TAG'); ?></th>
					<th><?php echo JText::_('COM_RESOURCES_TAGS_TAG'); ?></th>
					<th><?php echo JText::_('COM_RESOURCES_TAGS_ADMIN'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->tags); $i < $n; $i++)
{
	$thistag = &$this->tags[$i];
	$check = '';
	if ($thistag->admin == 1)
	{
		$check = '<span class="check">admin</span>';
	}
?>
				<tr class="<?php echo "row$k"; ?>">
					<td><input type="checkbox" name="tgs[]" id="cb<?php echo $i;?>" <?php if (in_array($thistag->tag, $this->mytagarray)) { echo 'checked="checked"'; } ?> value="<?php echo $this->escape($thistag->tag); ?>" /></td>
					<td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo $this->escape($thistag->raw_tag); ?></a></td>
					<td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo $this->escape($thistag->tag); ?></a></td>
					<td><?php echo $check; ?></td>
				</tr>
<?php
	$k = 1 - $k;
}
?>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
