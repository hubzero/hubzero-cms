<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

?>
	<h2><?php echo $this->facility->getName(); ?></h2>
	<?php echo $this->tabs; ?> 
	<h2>Sensor: <?php echo $this->sensor->getName(); ?></h2>


	<h3 style="padding-bottom:10px;">Main</h3>
	<table cellspacing="0" cellpadding="0" style=" border:1px solid #CCCCCC; width:700px">
		<tr><td style="font-weight:bold" nowrap="nowrap">Name:</td><td width="100%"><?echo $this->sensor->getName(); ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Model:</td><td><?= $this->sensor->getSensorModel()->getName() ?></a></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Serial Number:</td><td><?= $this->sensor->getSerialNumber() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Local Id:</td><td><?= $this->sensor->getLocalId() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Supplier:</td><td><?= $this->sensor->getSupplier() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Commissioned Date:</td><td><?= $this->sensor->getCommissionDate() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">Decommissioned Date:</td><td><?= $this->sensor->getDecommissionDate() ?></td></tr>
        <tr><td style="font-weight:bold" nowrap="nowrap">&nbsp;</td></tr>
	</table>	



	<h3 style="padding-bottom:10px;">Calibrations</h3>



	<h3 style="padding-bottom:10px;">Documentation</h3>












