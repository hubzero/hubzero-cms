<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2012 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2012 Purdue University. All rights reserved.
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
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('OAI-PMH Settings'), 'generic.png');
JToolBarHelper::preferences('com_oaipmh', 500);
JToolBarHelper::spacer();
JToolBarHelper::save('save', 'Save Settings');
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('oaipmh');

//$this->last;

$document =  JFactory::getDocument();
?>

<script type="text/javascript">
jQuery(document).ready(function(){ 
	jQuery("#tabs").tabs();
}); 

function addGroup() {
	sets++;
	jQuery.post("index.php?option=com_oaipmh",{task:"addset",sets:sets},function(){
		location.reload();
	});
}

function removeGroup(id) {
	var r = confirm("Are you sure you want to remove this Group?");
	if (r==true) {
		jQuery.post("index.php?option=com_oaipmh",{task:"removeset",id:id},function(){
			location.reload();
		});
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table class="admintable">
		<thead>
			<tr>
				<th>Dublin Core Table Specifications</th>
				<td id="toolbar-new">
					<span><a class="button" href="#" onclick="javascript:addGroup()">Add Group</a></span>
				</td>
			</tr>
		</thead>
	</table>
	<div id="tabs">
		<ul id="tablist">
		<?php
			$i = 0; 
			foreach ($this->sets as $set) 
			{
				echo "<li><a href='#tabs-$set[0]'>Group $set[0]:</a></li>";
				$i++; 
			}
			echo "<script type=\"text/javascript\">var sets = $i;</script>"; 
		?>
		</ul>
		<?php
			$x = 0; 
			foreach ($this->sets as $set) 
			{
				echo '<div id="tabs-' . $set[0] . '">';
				for ($i=$x;$i<=$x+16;$i++) 
				{
					echo '<div class="input-wrap">';
					echo '<label>' . $this->dcs[$i][1] . '</label><br />';
					echo '<textarea rows="3" name="queries[]">' . $this->dcs[$i][2] . '</textarea>';
					echo '<input type="hidden" name="qid[]" value="' . $this->dcs[$i][0] . '" />';
					echo '</div>';
				}
				if ($set[0] > 1) 
				{
					echo "<br /><br />[ <a href=\"#\" onclick=\"javascript:removeGroup($set[0])\" >Remove Group</a> ]";
				}
				echo '</div>';
				$x=$i;
			}
		?>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>