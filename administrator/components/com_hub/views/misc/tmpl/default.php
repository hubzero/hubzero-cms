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
JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Misc. Settings') );
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">

	<table class="adminlist" summary="A list of variables and their values.">
	 <thead>
	  <tr>
	   <th class="aRight"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
	   <th>Variable</th>
	   <th width="100%">Value</th>
	  </tr>
	 </thead>
	<tfoot>
		<tr>
			<td colspan="3"><?php echo $this->pageNav->getListFooter(); ?></td>
		</tr>
	</tfoot>
	 <tbody>
<?php
$k = 0;
$keys =  array_keys($this->rows);


$i = $this->pageNav->limitstart;
$n = $this->pageNav->limit;
$count = count($keys);
$end = $i + $n;
if ($end > $count)
    $end = $count;

for (; $i < $end; $i++) 
{
	$value = $this->rows[$keys[$i]];
	$name = $keys[$i];
?>
	  <tr class="<?php echo "row$k"; ?>">
	   <td class="aRight"><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo "$name" ?>" onclick="isChecked(this.checked);" /></td>
	   <td><a href="index.php?option=com_hub&amp;task=edit&amp;name=<?php echo $name;?>" title="Edit this variable"><?php echo stripslashes($name); ?></a></td>
	   <td><?php echo stripslashes($value); ?></td>
	  </tr>
<?php
	$k = 1 - $k;
}
?>
	 </tbody>
	</table>
    <p style="text-align:center;">Note: These variable settings can be overridden with the file <span style="text-decoration:underline;">hubconfiguration-local.php</span></p>
	
	<input type="hidden" name="option" value="com_hub" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
