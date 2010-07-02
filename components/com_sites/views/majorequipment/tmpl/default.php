<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

?>


<script type="text/javascript">
<!--
// Preload plus and Minus images for tree.
var imgRight = new Image();
var imgDown = new Image();
imgRight.src  = '/components/com_sites/images/arrow_right.png';
imgDown.src = '/components/com_sites/images/arrow_down.png';

function setDivStatus(divName, status) {
  //divStatusCache[divName] = status;
}

// Display/hide a div.
function swap(divName) {
  if(!document.getElementById) { return; }

  // Find div and +/- image.
  var div = document.getElementById(divName);
  if( !div ) { return; }
  var img = document.getElementById('img_' + divName);

  if(div.style.display == '') {
    img.src = imgRight.src;
    div.style.display = 'none';
  }
  else {
    img.src = imgDown.src;
    div.style.display = '';
  }
}
//-->
</script>

<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2>Equipment</h2>

	<table style="width:800px; border-width:0 0 0 0;">
		<tr class="facility-table-header">
			<th></th>
			<th nowrap="nowrap">Major Equipment</th>
			<th nowrap="nowrap">Class</th>
			<th nowrap="nowrap">Subcomponent Count</th>
		</tr>


		<?php

		$selectedEquip = -1;
		$rowcount = 0; 
		
		foreach($this->majorEquipment as $singleMajorEquipment) {
			$majorId = $singleMajorEquipment->getId();
			$majorName = $singleMajorEquipment->getName();
			$subcomps = EquipmentPeer::findAllByParent($majorId);
			$bgcolor = ($rowcount++%2 == 0) ? "#ffffff" : "#efefef";
        	$subcomps = EquipmentPeer::findAllByParent($majorId);
        	$expandlink = count($subcomps) > 0 ? "<a class=\"imagelink-no-underline\" href=\"javascript:swap('" . $majorId . "');\"><img style=\"border: 0px\" src='/components/com_sites/images/arrow_right.png' width='16' height='16' id='img_" . $majorId . "' title='Expand' alt='' /></a>" : "&nbsp;";
        	$display = ($selectedEquip == $majorId) ? "" : "none";

		?>
	
			<tr bgcolor="<?php echo $bgcolor; ?>">
				<td><?php echo $expandlink;?></td>
		        <td><?php echo '<a href="' . JRoute::_('/index.php?option=com_sites&id=' . $this->facilityID . "&view=equipment&equipid=" . $majorId) . '">' . $majorName . '</a>' ?></td>
		        <td><?php echo $singleMajorEquipment->getEquipmentModel()->getEquipmentClass()->getClassName()?></td>
		        <td><?php echo count($subcomps); ?></td>
	      	</tr>
	
	
			<tr id="<?php echo $majorId; ?>" style="display:none;">
		        <td colspan="3" style="padding-left:64px;">
		        	<table class="subcomponenttable">
		        		<tr class="facility-table-header">
		              		<th>&nbsp</th>
		              		<th>Subcomponent Name</th>
						</tr>
	
						<?php 
						// Loop through all the subcomponents
				        $orderNum = 0;
				        foreach($subcomps as $subcomp) 
				        {
				        	$orderNum++;
				            echo "<tr>";
				            echo "<td>" . $orderNum . "</td>";
				            echo "<td>";
				            echo '	<a href="' . JRoute::_('/index.php?option=com_sites&id=' . $this->facilityID . "&view=equipment&equipid=" . $subcomp->getId()) . '">';
				            echo $subcomp->getName(); 
				            echo "	</a>";
				            echo "</td>";
				            echo "</tr>";
				        }
						?>
	
		            </table>
		        </td>
			</tr>


		<?php 
		} // end foreach
		?>

	</table>

</div>










