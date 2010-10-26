<?php defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

<?php if(JRequest::getVar('msg'))
        echo '<p class="passed">' . JRequest::getVar('msg', '', 'post', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<?php if(JRequest::getVar('errorMsg'))
        echo '<p class="failed">' . JRequest::getVar('errorMsg', '', 'post', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<h2><?php if($this->calibrationid == -1) echo 'Add'; else echo 'Edit';?> Calibration</h2>
<hr>

<?php JHTML::_('behavior.calendar'); ?>

    <form method="post">

      
        <input type="hidden" name="id" value="<?php echo $this->facility->getId();?>"/>
        <input type="hidden" name="task" value="savecalibration">
        <input type="hidden" name="calibrationid" value="<?php echo $this->calibrationid;?>"/>
        <input type="hidden" name="sensorid" value="<?php echo $this->sensorid;?>"/>


        <table style="border:0px">
            <tr>
                <td class="form" nowrap="nowrap" >Date: <span class="requiredfieldmarker">*</span></td>
                <td class="form" width="100%">
                    <?php //echo $calibDateField ?>

                    <input size="10" maxlength="10" class="textentry" type="text" id="CalibDate" name="CalibDate" value="<?php echo $this->formfields['CalibDate']; ?>"/>
                    <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('CalibDate', '%m-%d-%Y');" />
                    (MM-DD-YYYY)

                </td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Person: </td>
                <td class="form"><input type="text" class="textentry" maxlength="50" name="calibrator" value="<?php echo $this->formfields['calibrator']; ?>"/></td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Description:</td>
                <td class="form"><textarea class="textentry" name="description" style="width:95%;" rows="3"><?php echo $this->formfields['description']; ?></textarea></td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Adjustments Value: </td>
                <td class="form"><input type="text" class="textentry" maxlength="20" name="adjustments" value="<?php echo $this->formfields['adjustments']; ?>"/> (Must be a number)</td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Min Measured Value: </td>
                <td class="form"><input type="text" class="textentry" maxlength="20" name="minMeasuredValue" value="<?php echo $this->formfields['minMeasuredValue']; ?>"/> (Must be a number)</td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Max Measured Value: </td>
                <td class="form"><input type="text" class="textentry" maxlength="20" name="maxMeasuredValue" value="<?php echo $this->formfields['maxMeasuredValue']; ?>"/>  (Must be a number)</td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Measured Value Units:  </td>
                <td class="form">
                    <select name="measuredValueUnits">
                        <?php echo $this->MeasuredValueUnitsOptions; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Sensitivity Value: </td>
                <td class="form"><input type="text" class="textentry" maxlength="20" name="sensitivity" value="<?php echo $this->formfields['sensitivity']; ?>"/> (Must be a number)</td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Sensitivity Units: </td>
                <td class="form">
                    <select name="sensitivityUnits">
                        <?php echo $this->SensitivityUnitsOptions; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Reference Value: </td>
                <td class="form"><input type="text" class="textentry" maxlength="20" name="reference" value="<?php echo $this->formfields['reference']; ?>"/> (Must be a number)</td>
            </tr>
            <tr>
                <td class="form" nowrap="nowrap" >Reference Units: </td>
                <td class="form">
                    <select name="referenceUnits">
                        <?php echo $this->ReferenceUnitsOptions; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="form" nowrap="nowrap" >Calibration Factor: </td>
                <td class="form"><input type="text" class="textentry" maxlength="100" name="calibFactor" value="<?php echo $this->formfields['calibFactor']; ?>"/></td>
            </tr>

            <tr>
                <td class="form" nowrap="nowrap" >Calibration Factor Units: </td>
                <td class="form">
                    <select name="calibFactorUnits">
                        <?php echo $this->CalibFactorUnitsOptions; ?>
                    </select>
                </td>
            </tr>

        </table>







        <div style="padding-top:20px">
            <input type="submit" name="submitbutton" value="Save" />
            <input type="submit" name="submitbutton" value="Cancel" />
        </div>
        
    </form>

<div style="margin-top:50px"> <span class="requiredfieldmarker" >*</span> Denotes required field </div>


</div>


