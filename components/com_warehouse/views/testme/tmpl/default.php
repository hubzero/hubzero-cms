<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $oProjectArray = unserialize($_REQUEST["search1"]);
  echo "Project primary keys = ". count($oProjectArray)." in ".$_REQUEST["time1"]." secs<br>";
  echo "Find ".count($oProjectArray)." projects = ".$_REQUEST["time2"];
?>

<p>

<?php
  $oProjectArray = unserialize($_REQUEST["search3"]);
  echo "Project objects = ". count($oProjectArray)." in ".$_REQUEST["time3"]." secs";
?>



