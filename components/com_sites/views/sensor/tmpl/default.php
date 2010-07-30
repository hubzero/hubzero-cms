<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); 
?>


<h2><?php echo $this->facility->getName(); ?></h2>
<?php echo $this->tabs; ?>

<div id="facility-subpage-primarycontent" style="width:900px;">

    <h2>Sensor: <?php echo $this->sensor->getName(); ?></h2>


    <?php
    if($this->canedit)
    {
        echo '<a href="'. JRoute::_('/index.php?option=com_sites&view=editsensor&sensorid=' . $this->sensorid . '&id=' . $this->facilityID) .'">[edit]</a>';
    }
    ?>

    <h3 style="padding-bottom:10px;">Sensor details</h3>
    <hr>
    <table cellspacing="0" cellpadding="0" style="border:0px; width:700px">
        <tr><td style="font-weight:bold" nowrap="nowrap">Name:</td><td width="100%"><?echo $this->sensor->getName(); ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Model:</td><td><?= $this->sensor->getSensorModel()->getName() ?></a></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Serial Number:</td><td><?= $this->sensor->getSerialNumber() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Local Id:</td><td><?= $this->sensor->getLocalId() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Supplier:</td><td><?= $this->sensor->getSupplier() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Commissioned Date:</td><td><?= $this->sensor->getCommissionDate() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Decommissioned Date:</td><td><?= $this->sensor->getDecommissionDate() ?></td></tr>
    </table>



    <h3 style="padding-bottom:10px;">Calibrations</h3>
    <hr>

    <?php
    if ($this->calibrationsHtml != '')
    { ?>

        <table style="width:900px; border:0px">
        <tr style="background-color:#aaa">
            <th> Date </th>
            <th> Description </th>
            <th> Person </th>
            <th> Adjustments </th>
            <th> Range </th>
            <th> Sensitivity </th>
            <th> Reference </th>
            <th> Factor </th>
        </tr>

    <?php 
        echo $this->calibrationsHtml;
        echo "</table>";
    }
    else
    {
        echo 'No calibration data is available for this sensor';
    }
    ?>


    <h3 style="padding-bottom:10px;">Documentation</h3>
    <hr>

</div>










