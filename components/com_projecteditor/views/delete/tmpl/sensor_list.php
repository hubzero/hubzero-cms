<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' ); 

?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
?>

<?php
  $oLocationArray = array();
  if(isset($_REQUEST[LocationPeer::TABLE_NAME])){
    $oLocationArray = unserialize($_REQUEST[LocationPeer::TABLE_NAME]);
    $strDisplayName = "List of Locations";
  }

  $strButtonStyle = "";
?>

<form id="frmPopout" action="/warehouse/projecteditor/removeentity" method="post">
    <input type="hidden" name="format" value="ajax"/>
    <input type="hidden" name="eid" value="<?php echo $this->entityId; ?>"/>
    <input type="hidden" name="etid" value="<?php echo $this->entityTypeId; ?>"/>
    <input type="hidden" name="path" value="<?php echo $this->path; ?>"/>
    <input type="hidden" id="return" name="return" value="<?php echo $this->strReturnUrl; ?>" />

    <div><h2>Confirm Delete</h2></div>
    <div class="information"><b><?php echo $this->className; ?>:</b> List of Sensor Locations</div>

    <table style="border:0px">
      <thead>
        <tr>
          <th>Sensor ID</th>
          <th>Type</th>
          <th>Orientation</th>
          <th>XYZ Coordinates</th>
        </tr>
      </thead>
      <?php
        if(!empty($oLocationArray)){
          /** @var $oLocation Location */
          foreach($oLocationArray as $iLocationIndex=>$oLocation){
            $oOrientationArray = LocationPeer::getOrientation($oLocation);
            $strOrientation0 = ($oOrientationArray[0] === "") ? "-" : round($oOrientationArray[0],4);
            $strOrientation1 = ($oOrientationArray[1] === "") ? "-" : round($oOrientationArray[1],4);
            $strOrientation2 = ($oOrientationArray[2] === "") ? "-" : round($oOrientationArray[2],4);

            $strThisType = LocationPeer::getLocationType($oLocation);
            $strThisLabel = LocationPeer::getLabel($oLocation);
            $strThisX = LocationPeer::formatCoordinate($oLocation, "X");
            $strThisY = LocationPeer::formatCoordinate($oLocation, "Y");
            $strThisZ = LocationPeer::formatCoordinate($oLocation, "Z");

            $strClass = "odd";
            if($iLocationIndex %2 === 0 ){
              $strClass = "even";
            }
            ?>
              <tr valign="top" class="<?php echo $strClass;?>">
                <td><?php echo $strThisLabel; ?></td>
                <td><?php echo $strThisType; ?></td>
                <td><?php echo $strOrientation0.", ".$strOrientation1.", ".$strOrientation2; ?></td>
                <td><?php echo $strThisX.", ".$strThisY.", ".$strThisZ; ?></td>
              </tr>
            <?php
          }
        }
      ?>
    </table>

 
    <div id="deleteBtn" class="sectheaderbtn" style="<?php echo $strButtonStyle; ?>">
      <a tabindex="" href="javascript:void(0);" class="button2" onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);">Delete</a>
    </div>
</form>