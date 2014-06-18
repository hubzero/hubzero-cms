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

JToolBarHelper::title(JText::_('Resource') . ': ' . JText::_('Tags') . ' #' . $this->row->id, 'addedit.png');
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
	if(input.value == '') {
		input.value = tag;
	} else {
		input.value += ', '+tag;
	}
}
</script>

<form action="index.php" method="post" name="adminForm">
	<h2>Edit Tags for this Resource</h2>
	<p>Create new tags and assign them to this resource by entering them below separated by commas (e.g. <em>negf theory, NEMS, ion transport</em>).</p>
		<table class="adminform">
		 <thead>
		  <tr>
		   <th colspan="2"><?php echo $this->row->title; ?></th>
		  </tr>
		 </thead>
		 <tbody>
		  <tr>
		   <th><label for="tags-men">Create Tags:</label></th>
		   <td><input type="text" name="tags" id="tags-men" size="65" value="<?php //echo $this->objtags->tagMen; ?>" />
		   </td>
		  </tr>
		 </tbody>
		</table>

	<h3>Existing Tags</h3>
	<p>Add or remove tags assigned to this resource by checking or unchecking tags below.</p>
	<table class="adminlist" summary="A list of all tags">
	 <thead>
	  <tr>
	   <th style="width: 15px;"> </th>
	   <th>Raw Tag</th>
	   <th>Tag</th>
	   <th>Admin</th>
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
	   <td><input type="checkbox" name="tgs[]" id="cb<?php echo $i;?>" <?php if (in_array($thistag->tag,$this->mytagarray)) { echo 'checked="checked"'; } ?> value="<?php echo stripslashes($thistag->tag); ?>" /></td>
	   <td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo stripslashes($thistag->raw_tag); ?></a></td>
	   <td><a href="#" onclick="addtag('<?php echo stripslashes($thistag->tag); ?>');"><?php echo stripslashes($thistag->tag); ?></a></td>
	   <td><?php echo $check; ?></td>
	  </tr>
<?php
	$k = 1 - $k;
}
?>
	 </tbody>
	</table>
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
