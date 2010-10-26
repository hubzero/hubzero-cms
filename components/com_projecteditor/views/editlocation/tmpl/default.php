<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/templates/fresh/html/com_groups/groups.css",'text/css');
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');

  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php
 /* @var $oLocation Location */
 $oLocation = unserialize($_REQUEST[LocationPeer::TABLE_NAME]);
 $iLocationPlanId = $oLocation->getLocationPlan()->getId();
 $strLocationPlanName = $oLocation->getLocationPlan()->getName();
 $oOrientationArray = LocationPeer::getOrientation($oLocation);
 $strOrientation0 = ($oOrientationArray[0] === "") ? "" : round($oOrientationArray[0],4);
 $strOrientation1 = ($oOrientationArray[1] === "") ? "" : round($oOrientationArray[1],4);
 $strOrientation2 = ($oOrientationArray[2] === "") ? "" : round($oOrientationArray[2],4);

 $strThisType = LocationPeer::getLocationType($oLocation);
 $strThisLabel = LocationPeer::getLabel($oLocation);
 $strThisX = LocationPeer::getCoordinateValue($oLocation, "X");
 $strThisY = LocationPeer::getCoordinateValue($oLocation, "Y");
 $strThisZ = LocationPeer::getCoordinateValue($oLocation, "Z");
 $strComments = $oLocation->getComment();

?>

<form id="frmPopout" action="/warehouse/projecteditor/savesensor" method="post" enctype="multipart/form-data">
  <input type="hidden" name="locId" value="<?php echo $this->locationId; ?>"/>
  <input type="hidden" name="projectId" value="<?php echo $this->projectId; ?>"/>
  <input type="hidden" name="experimentId" value="<?php echo $this->experimentId; ?>"/>
  <input type="hidden" name="locationPlanId" value="<?php echo $iLocationPlanId; ?>"/>

  <div><h2>Edit Sensor</h2></div>
  <div class="information"><b>Sensor List:</b> <?php echo $strLocationPlanName; ?></div>

  <table style="border: 0px;">
    <tr id="label">
      <td nowrap="" width="1">
        <label for="sensorId" class="editorLabel">Sensor ID:</label>
      </td>
      <td>
        <input id="sensorId" type="text" name="Label" value="<?php echo $strThisLabel; ?>"/>
      </td>
    </tr>
    <tr id="type">
      <td nowrap="" width="1">
        <label for="sensorType" class="editorLabel">Type:</label>
      </td>
      <td>
        <input id="sensorType" type="text" name="Type" onkeyup="suggest('/warehouse/projecteditor/sensortypesearch?format=ajax', 'sensorTypeSearch', this.value, this.id)" value="<?php echo $strThisType; ?>" />
        <div id="sensorTypeSearch" class="suggestResults"></div>
      </td>
    </tr>
    <tr id="orientation">
      <td nowrap="" width="1">
        <label for="sensorOrientationI" class="editorLabel">Orientation:</label>
      </td>
      <td>
        <input id="sensorOrientationI" type="text" name="orientI" style="width:40px;" value="<?php echo $strOrientation0; ?>"/>&nbsp;
        <input id="sensorOrientationJ" type="text" name="orientJ" style="width:40px;" value="<?php echo $strOrientation1; ?>"/>&nbsp;
        <input id="sensorOrientationK" type="text" name="orientK" style="width:40px;" value="<?php echo $strOrientation2; ?>"/>
      </td>
    </tr>
    <tr id="xyzCoord">
      <td nowrap="">
        <label for="sensorX" class="editorLabel">XYZ Coordinates:</label>
      </td>
      <td>
        <input id="sensorX" type="text" name="locX" style="width:40px;" value="<?php echo $strThisX; ?>"/>&nbsp;
        <input id="sensorY" type="text" name="locY" style="width:40px;" value="<?php echo $strThisY; ?>"/>&nbsp;
        <input id="sensorZ" type="text" name="locZ" style="width:40px;" value="<?php echo $strThisZ; ?>"/>&nbsp;
      </td>
    </tr>
    <tr id="xyzUnits">
      <td nowrap="" width="1">
        <label for="xyz" class="editorLabel">Units:</label>
      </td>
      <td>
        <?php
          $oUnits = unserialize($_REQUEST["UNITS"]);
          $oDefaultUnit = unserialize($_REQUEST["DEFAULT_UNIT"]);
        ?>
        <select name="xyzUnits">
          <?php
            /* @var $oUnit MeasurementUnit */
            foreach($oUnits as $oUnit){
              ?>
              <option value="<?php echo $oUnit->getId(); ?>" <?php if($oDefaultUnit->getId()==$oUnit->getId())echo "selected"; ?>><?php echo $oUnit->getAbbreviation(); ?></option>
              <?php
            }
          ?>
        </select>
      </td>
    </tr>
    <tr id="coodSpace">
      <td nowrap="">
        <label for="coodinateSpace" class="editorLabel">Coordinate Space:</label>
      </td>
      <td>
        <select id="coodinateSpace" name="coordinateSpace">
          <?php
            $oCoordinateSpaceArray = unserialize($_REQUEST[CoordinateSpacePeer::TABLE_NAME]);

            /* @var $oCoordinateSpace CoordinateSpace */
            foreach($oCoordinateSpaceArray as $oCoordinateSpace){
              ?>
              <option value="<?php echo $oCoordinateSpace->getId(); ?>"><?php echo $oCoordinateSpace->getName(); ?></option>
              <?php
            }
          ?>
        </select>
      </td>
    </tr>
    <tr id="comments">
      <td nowrap="">
        <label for="txtComments" class="editorLabel">Comments:</label>
      </td>
      <td>
        <textarea id="txtComments" name="comments"><?php echo $strComments; ?></textarea>
      </td>
    </tr>
    <tr id="save">
      <td colspan="2">
        <input type="button" value="Save Sensor" onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);"/>
      </td>
    </tr>
  </table>
</form>

