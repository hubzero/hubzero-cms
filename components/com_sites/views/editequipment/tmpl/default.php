<?php defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">
<!--

    function populateModel() {
        //alert('drb 560130');
        var classbox = document.getElementById('equipmentClassId');
        var modelbox = document.getElementById('equipmentModelId');
        var classid = classbox.options[classbox.selectedIndex].value;
        
        modelbox.options.length = 0;
        for( i in models[classid] ) {
            modelbox.options[modelbox.options.length] = new Option(models[classid][i], i);
        }
        displayClassForm();
    }

    function displayClassForm() {
        var classbox = document.getElementById('equipmentClassId');
        var classid = classbox.options[classbox.selectedIndex].value;

        for( i=0; i<classbox.options.length; i++) {
            var divname = 'ClassForm' + classbox.options[i].value;
            var thediv = document.getElementById(divname);
            if( !thediv ) {
                continue;
            }
            if( classbox.options[i].value == classid ) {
                thediv.style.display = 'block';
            }
            else {
                thediv.style.display = 'none';
            }
        }

    }



<?php $this->printEquipmentModelJS(); ?>




//-->
</script>



<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

<?php if(JRequest::getVar('msg'))
        echo '<p class="passed">' . JRequest::getVar('msg', '', 'post', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<?php if(JRequest::getVar('errorMsg'))
        echo '<p class="failed">' . JRequest::getVar('errorMsg', '', 'post', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<h2><?php if($this->equipmentid == -1) echo 'Add'; else echo 'Edit';?> Equipment</h2>
<hr>

<?php JHTML::_('behavior.calendar'); ?>

    <form method="post">

      
        <input type="hidden" name="id" value="<?php echo $this->facility->getId();?>"/>
        <input type="hidden" name="task" value="saveequipment">
        <input type="hidden" name="equipmentid" value="<?php echo $this->equipmentid;?>"/>
        <input type="hidden" name="parentequipmentid" value="<?php echo $this->parentequipmentid;?>"/>
        <input type="hidden" name="previousequipmentclassid" value="<?php echo $this->equipmentValues['previousequipmentclassid']; ?>"/>

        <table style="border-width: 0px">

            <tr><td style="width:150px"></td><td></td></tr>

        <tr>
          <td nowrap="nowrap">Equipment Name<span class="requiredfieldmarker">*</span> </td>
          <td  width="100%">
            <input type="text" maxlenght="128" style="width:50%" name="name" value="<?php echo $this->equipmentValues['name']; ?>"/>
          </td>
        </tr>

        <tr>
          <td nowrap="nowrap">Equipment Class </td>
          <td>
            <select name="equipmentClassId" onchange="populateModel();" onkeypress="populateModel();" id="equipmentClassId">
            <?php echo $this->equipmentClassDropDownList; ?>
            </select>
          </td>
        </tr>
        <tr>
          <td nowrap="nowrap">Equipment Model </td>
          <td>
            <select name="equipmentModelId" id="equipmentModelId">
                <?php echo $this->equippmentModelDropdownOptions; ?>
            </select>
            <!--&nbsp;&nbsp;<a href="">Add New Equipment Model</a>-->
          </td>
        </tr>

             <tr>
          <td nowrap="nowrap">NEES Operated</td>
          <td>
            <?php echo $this->operatedEquipmentOptions; ?>
            <br/>
          </td>
        </tr>

        <!--
        <tr>
          printClassForms(); ?>
            javascript call displayClassForm();
        -->

        <tr>
          <td nowrap="nowrap">Owner</td>
          <td>
            <input type="text" maxlength="100" name="owner" value="<?php echo $this->equipmentValues['owner']; ?>"/>
          </td>
        </tr>

        <tr>
          <td nowrap="nowrap">Serial Number</td>
          <td>
            <input type="text"  maxlength="100" name="sn" value="<?php echo $this->equipmentValues['serialnumber']; ?>"/>
          </td>
        </tr>

        <tr>
          <td nowrap="nowrap">Commission Date</td>
          <td>
            

            <input size="10" maxlength="10" class="textentry" type="text" id="commissionDate" name="commissionDate" value="<?php echo $this->equipmentValues['commissiondate']; ?>"/>


            <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('commissionDate', '%Y-%m-%d');" />

            <?php echo''; //  isset($errors["commissionDate"]) ? $command->printEditError($errors["commissionDate"]) : "" ?>
          </td>
        </tr>


        <tr>
          <td nowrap="nowrap">Separated Scheduling </td>
          <td>
            <?php echo $this->separatedSchedulingOptions; ?>
          </td>
        </tr>

        <tr>
          <td nowrap="nowrap">Calibration Information</td>
          <td>
            <input type="text" maxlength="100" name="calibrationInfo" style="width:75%" value="<?php echo $this->equipmentValues["calibrationinformation"]; ?>"/>
          </td>
        </tr>

        <tr>
          <td nowrap="nowrap">Note</td>
          <td>
            <input type="text" maxlength="100" name="note" style="width:75%" value="<?php echo $this->equipmentValues["note"]; ?>"/>
          </td>
        </tr>

        <tr>
          <td nowrap="nowrap">Lab Assigned ID <?php echo''; //  $command->getHelpTag("Lab Assigned Id"); ?></td>
          <td>
            <input type="text" maxlength="100" name="labAssignedId" value="<?php  echo $this->equipmentValues['labassignedid']; ?>"/>
          </td>
        </tr>

        </table>


        <?php
            if(!empty($this->classSpecificFields))
            {
                echo '<h3>Equipment Class Specific Fields</h3>';
                echo '<table style="border:0px">';
                echo '<div style="padding-bottom:10px"><hr/></div>';
                echo $this->classSpecificFields;
                echo '</table>';
            }
        ?>


        <div style="padding-top:20px">
            <input type="submit" name="submitbutton" value="Save" />
            <input type="submit" name="submitbutton" value="Cancel" />
        </div>
        
    </form>

<div style="margin-top:50px"> <span class="requiredfieldmarker" >*</span> Denotes required field </div>


</div>


