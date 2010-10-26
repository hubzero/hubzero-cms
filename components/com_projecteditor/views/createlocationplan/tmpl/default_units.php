<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<form action="/warehouse/projecteditor/savelocationplan" method="post">
    <input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>"/>
    <table style="border: 0px;width: 300px;">
        <tr>
            <td colspan="2"><h3>Create New Sensor List</h3></td>
        </tr>
        <tr>
            <td>Sensor List Name</td>
            <td><input type="text" name="lpName" autocomplete="off"/></td>
        </tr>
        <tr>
            <td>Units</td>
            <td>
                <?php 
                  $oUnits = unserialize($_REQUEST["UNITS"]);
                  $oDefaultUnit = unserialize($_REQUEST["DEFAULT_UNIT"]);
                ?>
                <select name="unit">
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
        <tr>
            <td colspan="2"><input type="submit" value="Create"/></td>
        </tr>
    </table>
</form>