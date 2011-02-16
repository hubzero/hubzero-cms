<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<p>Sensor List</p>

<?php 
  $oLocationCollectionArray = $this->locationArray;

?>

<table style="border: 0px">
  <tr>
    <td><a href="/warehouse/experiment/<?php echo $this->experimentId; ?>/project/<?php echo $this->projectId; ?>">Return</a></td>
    <td align="right">
       <?php
         if(isset($_REQUEST[LocationPlanPeer::TABLE_NAME])){
           /* @var $oLocationPlan LocationPlan */
           $oLocationPlan = unserialize($_REQUEST[LocationPlanPeer::TABLE_NAME]);
           /* @var $oDataFile DataFile */
           $oDataFile = $oLocationPlan->getDataFile();
           if(isset($oDataFile)){
             ?><img src="/templates/fresh/images/icons/fc-download.gif"/>&nbsp;<a style="float:right" href="<?php echo $oDataFile->get_url(); ?>" target="sensorDownload">Download</a><?php
           }
         }
       ?>
    </td>
  </tr>
</table>


  <table style="margin-top:15px;">
    <thead>
      <th>Sensor ID</th>
      <th>Type</th>
      <th>Orientation</th>
      <th>XYZ Coordinates</th>
    </thead>

    <?php
      foreach($oLocationCollectionArray as $iIndex=>$strLocationArray){
      	$strBackgroundColor = "odd";
      	if($iIndex %2 ==0){
      	  $strBackgroundColor = "even";
      	}
      	?>
      	  <tr valign="top" class="<?php echo $strBackgroundColor; ?>">
		    <td><?php echo $strLocationArray["LABEL"]; ?></td>
		    <td><?php echo $strLocationArray["TYPE"]; ?></td>
		    <td><?php echo $strLocationArray["ORIENTATION0"].", ".$strLocationArray["ORIENTATION1"].", ".$strLocationArray["ORIENTATION2"]; ?></td>
		    <td><?php echo $strLocationArray["X"].", ".$strLocationArray["Y"].", ".$strLocationArray["Z"]; ?></td>
		  </tr>
      	<?php
      }
    ?>

  </table>