<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

?>



<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs; ?>

<div id="facility-subpage-primarycontent">

	<h2>Sensor Model: <?php echo $this->sensorModel->getName(); ?></h2>
        <hr>

	<table cellspacing="0" cellpadding="0" style="width:800px; border:0px;">
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Type:</td><td style="padding:2px 2px;"><?php echo $this->sensorModel->getSensorType()->getName(); ?></td></tr>
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Manufacturer:</td><td style="padding:2px 2px;"><?php echo $this->sensorModel->getManufacturer(); ?></td></tr>
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Model:</td><td style="padding:2px 2px;"><?php echo $this->sensorModel->getModel(); ?></td></tr>
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Description:</td><td style="padding:2px 2px;"><?php echo $this->sensorModel->getDescription(); ?></td></tr>
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Signal Type:</td><td style="padding:2px 2px;"><?php echo $this->sensorModel->getSignalType(); ?></td></tr>
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Measurement Range:</td><td style="padding:2px 2px;"><?php echo $this->getSensorModelMeasurementRange($this->sensorModel); ?></td></tr>
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Sensitivity:</td><td style="padding:2px 2px;"><?php echo $this->getSensitivity($this->sensorModel); ?></td></tr>
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Operating Temperature Range:</td><td style="padding:2px 2px;"><?echo $this->getSensorModelOperatingTemperature($this->sensorModel); ?></td></tr>
            <tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">Note:</td><td style="padding:2px 2px;"><?php echo $this->sensorModel->getNote(); ?></td></tr>


	<?php
            if(count($this->groupvalues) > 0)
            {
                echo '<tr><td nowrap="nowrap" colspan="2" style="padding:0px 0px;"><h3>Standard Specifications</h3></td></tr>';

                foreach ($this->groupvalues as $k => $v)
                {
                    echo '<tr><td nowrap="nowrap" style="font-weight:bold; width:225px; padding:2px 2px;">' . $k . ':</td><td style="padding:2px 2px;">'. $v. '</td></tr>';
                }
            }
	?>

    </table>


    <?php     
        echo FacilityHelper::getViewSimpleFileBrowser($this->datafiles, "Sensor Model Documentation", $this->redirectURL)
    ?>



</div>










