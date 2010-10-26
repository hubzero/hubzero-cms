<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); 
?>


<h2><?php echo $this->facility->getName(); ?></h2>
<?php echo $this->tabs; ?>

<div id="facility-subpage-primarycontent" style="width:900px;">

    
    <h2>Sensor Calibration for <?php echo $this->sensor->getName() ?> </h2>

    <?php
        if($this->canedit)
            echo '<span style="padding-right:20px;"><a href="' .JRoute::_('/index.php?option=com_sites&id=' . $this->facilityID . '&view=editcalibration&sensorid=' . $this->sensorid . '&calibrationid=' . $this->calibrationid) . '">[edit]</a></span>';
    ?>

    <hr/>

    <table cellspacing="0" cellpadding="0" style="border:0px">
        <tr>
            <td nowrap="nowrap">Date:</td>
            <td width="100%"><?php echo $this->calibration->getCalibDate(); ?></td>
        </tr>
        <tr>
            <td nowrap="nowrap">Person:</td>
            <td><?php echo $this->calibration->getCalibrator(); ?></td>
        </tr>
        <tr>
            <td nowrap="nowrap">Description:</td>
            <td><?php echo $this->calibration->getDescription() ?></td>
        </tr>
        <tr>
            <td nowrap="nowrap">Adjustments:</td>
            <td><?php echo $this->calibration->getAdjustments() ?></td>
        </tr>
        <tr>
            <td nowrap="nowrap">Measured Range:</td>
            <td><?php echo $this->calibration->getSensitivityWithUnit(); ?></td>
        </tr>
        <tr>
            <td nowrap="nowrap">Sensitivity:</td>
            <td><?php echo $this->calibration->getReferenceWithUnit(); ?></td>
        </tr>
        <tr>
            <td nowrap="nowrap">Reference:</td>
            <td><?php echo $this->calibration->getReferenceWithUnit(); ?></td>
        </tr>
        <tr>
            <td nowrap="nowrap">Calibration Factor:</td>
            <td><?php echo $this->calibration->getCalibFactorWithUnit(); ?></td>
        </tr>
        <tr><td height="20" colspan="2">&nbsp;</td></tr>

    </table>



    <div>
        <?php
        echo FacilityHelper::getViewSimpleFileBrowser($this->datafiles, "Calibration Documentation", $this->redirectURL)
        ?>
    </div>

    <?php
        if ($this->allowCreate)
            echo '<a  style="padding-left: 0px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&calibrationid=' . $this->calibrationid . '&redirectURL=' . $this->redirectURL) . '">[Add Document]</a>';
    ?>


    

</div>










