<?php
 
defined('_JEXEC') or die('Restricted access'); ?>



<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

<?php if(JRequest::getVar('msg'))
        echo '<p class="passed">' . JRequest::getVar('msg', '', 'default', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<?php if(JRequest::getVar('errorMsg'))
        echo '<p class="failed">' . JRequest::getVar('errorMsg', '', 'default', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<h2><?php if($this->sensormodelid == -1) echo 'Add'; else echo 'Edit';?> Sensor Model</h2>
<hr>

<?php JHTML::_('behavior.calendar'); ?>

    <form method="post">

        <input type="hidden" name="id" value="<?php echo $this->facility->getId();?>"/>
        <input type="hidden" name="task" value="savesensormodel">
        <input type="hidden" name="sensormodelid" value="<?php echo $this->sensormodelid;?>"/>

        <table style="border:0px">

            <tr>
            <td class="form" nowrap="nowrap">Name: <span class="requiredfieldmarker" >*</span></td>
            <td class="form" width="100%">
              <input type="text" style="width:95%" maxlength="50" name="Name" value="<?php echo $this->fieldValues["Name"]; ?>"/>
              <?php echo ''; //$command->printEditError($errors["Name"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Type: </td>
            <td class="form">
              <select name="SensorTypeId" >
              <?php echo $this->sensortypes; ?>
              </select>
              <?php echo ''; //$command->printEditError($errors["SensorTypeId"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Manufacturer: </td>
            <td class="form">
              <select name="Manufacturer" onchange="changeManufacturer(this);" >
                <?php echo $this->mfgs; ?>
              </select>
              <?php echo ''; //$command->printEditError($errors["Manufacturer"]) ?>
              <div id="NewManufacturer" style="display: none;">
                New Manufacturer: <span class="requiredfieldmarker" >*</span>
                <input type="text" class="textentry wide" maxlength="50" name="newManName" value="" />
              </div>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Model: </td>
            <td class="form">
              <input type="text" class="textentry wide" maxlength="50" name="Model" value="<?php echo $this->fieldValues["Model"]; ?>"/>
              <?php echo ''; //$command->printEditError($errors["Model"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Description: </td>
            <td class="form">
              <textarea class="textentry" style="width:95%;" name="Description" rows="4"><?php echo $this->fieldValues["Description"]; ?></textarea>
              <?php echo ''; //$command->printEditError($errors["Description"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Signal Type: </td>
            <td class="form">
              <input type="text" class="textentry" maxlength="50" name="SignalType" value="<?php echo $this->fieldValues["SignalType"]; ?>"/>
              <?php echo ''; //$command->printEditError($errors["SignalType"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Min Measured Value: </td>
            <td class="form">
              <input type="text" class="textentry" maxlength="20" name="MinMeasuredValue" value="<?php echo $this->fieldValues["MinMeasuredValue"]; ?>"/>
              <?php echo ''; //$command->printEditError($errors["MinMeasuredValue"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Max Measured Value:  </td>
            <td class="form">
              <input type="text" class="textentry" maxlength="20" name="MaxMeasuredValue" value="<?php echo $this->fieldValues["MaxMeasuredValue"]; ?>"/>
              <?php echo ''; //$command->printEditError($errors["MaxMeasuredValue"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Measured Value Units: </td>
            <td class="form">
              <select name="MeasuredValueUnitsId">
                <?php echo $this->measuredvalueunits; ?>
              </select>
              <?php echo ''; //$command->printEditError($errors["MeasuredValueUnits"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Sensitivity:  </td>
            <td class="form">
              <input type="text" class="textentry" maxlength="20" name="Sensitivity" value="<?php echo $this->fieldValues["Sensitivity"]; ?>"/>
              <?php echo ''; //$command->printEditError($errors["Sensitivity"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Sensitivity Units:  </td>
            <td class="form">
              <select name="SensitivityUnitsId">
                <?php echo $this->sensitivityunits; ?>
              </select>
              <?php echo ''; //$command->printEditError($errors["SensitivityUnits"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Min Operating Temperature: </td>
            <td class="form">
              <input type="text" class="textentry" maxlength="20" name="MinOpTemp" value="<?php echo $this->fieldValues["MinOpTemp"]; ?>"/>
              <?php echo ''; //$command->printEditError($errors["MinOpTemp"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Max Operating Temperature: </td>
            <td class="form">
              <input type="text" class="textentry" maxlength="20" name="MaxOpTemp" value="<?php echo $this->fieldValues["MaxOpTemp"]; ?>"/>
              <?php echo ''; //$command->printEditError($errors["MaxOpTemp"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap">Operating Temperature Units:  </td>
            <td class="form">
              <select name="TempUnitsId">
                <?php echo $this->temperatureunits; ?>
              </select>
              <?php echo ''; //$command->printEditError($errors["TempUnits"]) ?>
            </td>
          </tr>

          <tr>
            <td class="form" nowrap="nowrap"> Note: </td>
            <td class="form">
              <textarea class="textentry" style="width:95%;" name="Note" rows="6"><?php echo $this->fieldValues["Note"]; ?></textarea>
            </td>
          </tr>


        </table>



        <input type="submit" name="submitbutton" value="Save" />
        <input type="submit" name="submitbutton" value="Cancel" />

    </form>

<div style="margin-top:50px"> <span class="requiredfieldmarker" >*</span> Denotes required field </div>


</div>

