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

	<h2>Sensors</h2>

	<table style="width:800px; border-width:0 0 0 0;">

		<tr class="facility-table-header">
			<th nowrap="nowrap"></th>
			<th nowrap="nowrap">Sensor Model</th>
			<th nowrap="nowrap">Sensor Type</th>
			<th nowrap="nowrap">Range</th>
			<th nowrap="nowrap">Quantity</th>
		</tr>
	
		<?php
		$rowcount =0;
		
		while($this->ssm->next()) {

			$minMeasuredValue = $this->ssm->get('MIN_MEASURED_VALUE');
			$maxMeasuredValue = $this->ssm->get('MAX_MEASURED_VALUE');
			$unit = $this->ssm->get('ABBREVIATION');
			$sensormodelid = $this->ssm->get('SENSOR_MODEL_ID');
			$sensormodelname = $this->ssm->get('SMOD_NAME');
			$sensorTypeName = $this->ssm->get("ST_NAME");
			$quantity = $this->ssm->get("QUANTITY");
			$bgcolor = ($rowcount++%2 == 0) ? "#ffffff" : "#efefef";

			if(empty($sensormodelname)) $sensormodelname = "No Name, (ID = " . $sensormodelid . ")";

			$measRange = null;
			if ($minMeasuredValue !== null) $measRange = "from $minMeasuredValue";
			if ($maxMeasuredValue !== null) $measRange .= " to $maxMeasuredValue";
			if ($measRange !== null) $measRange .= " " . $unit;
			
			$orderNum = 0;
        	$sensors = SensorPeer::findByFacilityAndSensorModel($this->facilityID, $sensormodelid);
		?>
	
	
		<tr bgcolor="<?php echo $bgcolor;?>">
	        <td><a class="imagelink-no-underline" href="javascript:swap('<?=$sensormodelid?>');"><img style="border: 0px" src="/components/com_sites/images/arrow_right.png" width="16" height="16" id="img_<?=$sensormodelid?>" title="Expand" alt="" /></a></td>


			<td><?php echo '<a href="' . JRoute::_('/index.php?option=com_sites&view=sensormodel&id=' . $this->facilityID . '&sensormodelid=' . $sensormodelid) . '">' . $sensormodelname;?></a></td>
			<td><? echo $sensorTypeName; ?></td>
			<td><? echo $measRange; ?></td>
			<td><? echo $quantity; ?></td>
		</tr>
		
		<tr id="<?=$sensormodelid?>" style="display:none;">
			<td colspan="3" style="padding-left:64px;">
				<table class="subcomponenttable">
					<tr class="facility-table-header">
						<th>&nbsp</th>
						<th>Sensor Name</th>
						<th>Serial Number</th>
					</tr>
		
		<?php 
		// Loop through all the senrors
		        $orderNum = 0;
		        foreach($sensors as $sensor) 
		        {
		            echo "<tr>";
		            echo "<td>" . $orderNum++ . "</td>";
		        	echo '<td><a href="' . JRoute::_('/index.php?option=com_sites&view=sensor&id=' . $this->facilityID. '&sensorid=' . $sensor->getId()) . '">' . $sensor->getName() . '</a></td>';
		            echo "<td>" . $sensor->getSerialNumber() . "</td>";
		            echo "</tr>";
		        }
		?>
		        </table>
			</td>
		</tr>
	
		<?php 
		} // End while
		?>

	</table>


	<div class="sectheaderbtn" style="width:900px;">
		<?= $this->uploadSensors ?>
		<?= $this->uploadCalibrations ?>


		<?= $this->exportCalibrations ?>
		<?= $this->exportSensors ?>
		<?= $this->exportSensorModels ?>
	</div>


</div>










