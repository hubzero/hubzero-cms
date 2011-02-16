<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: 0"); // Date in the past
?>

<div style="margin-left: 15px;">

  <h2>Available Sensor Types</h2>
  <?php
  $oSensorTypeArray= unserialize($_REQUEST[SensorTypePeer::TABLE_NAME]);
  foreach ($oSensorTypeArray as $oSensorType){
    /* $oSensorType SensorType */
    echo $oSensorType->getName()."<br>";
  }
  ?>

  <p>Note: Submit a <a href="/support/tickets">support ticket</a> if you would like to add additional sensor types.</p>
</div>