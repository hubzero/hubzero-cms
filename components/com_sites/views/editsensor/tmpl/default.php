<?php
 
defined('_JEXEC') or die('Restricted access'); ?>



<script type="text/javascript">


var sensorModels = [];


    function populateSMTable(){

        smTableE = document.getElementById("sm_table");
        typeE = document.getElementById("sm_types");
        manE = document.getElementById("sm_mans");
        smType = typeE.value;
        allTypes = (smType == "all");
        smMan = manE.value;
        allMans = (smMan == "all");

        //  Remove all rows
        while(smTableE.rows.length > 1)
        {
            smTableE.deleteRow(1);
        }

        //  Make an array of valid sensor models
        smA = new Array();
        for(si in sensorModels){
            sm = sensorModels[si];
            if ( (sm.type == smType || allTypes) && (sm.man == smMan || allMans) ) {
                smA.push(sm);
            }
        }

        //  Now make them again!
        ci = 0;
        for(si=0;si<smA.length;si++){
            s = smA[si];
            newRow = smTableE.insertRow(smTableE.rows.length);

            ci = (ci == 0)? 1:0;
            newRow.className = "row" + ci;
            newRow.name = "smrow";

            newRow.onclick = function(){ onclickSMRow(this); }
            typeName = sensorTypes[s.type];
            manName = manufacturers[s.man];

            inputCell = newRow.insertCell(0);
            inputE = document.createElement(document.uniqueID ? '<input name="sensorModelId" />' : 'input');
            inputE.type="radio";
            inputE.name = "sensorModelId";
            //inputE.setAttribute("name","sensorModelId");
            inputE.value = s.id;
            inputCell.appendChild(inputE);

            inputE.checked = s.selected;
            //inputE.onclick = onclickSMInput;

            //  I need these for IE for some reason ???
            inputE.style.border = "0px";
            inputE.style.background = "none";



            typeCell = newRow.insertCell(1);
            typeText  = document.createTextNode(typeName)
            typeCell.appendChild(typeText);

            manCell = newRow.insertCell(2);
            manText  = document.createTextNode(manName)
            manCell.appendChild(manText);

            modCell = newRow.insertCell(3);
            modCell.innerHTML = s.name;

        }

        //  Check to see that we added anything or advise that we didn't
        if ( smTableE.rows.length == 1 ){
            newRow = smTableE.insertRow(1);
            newCell = newRow.insertCell(0);
            newCell.colSpan = 4;
            newCell.style.textAlign="center";
            newText  = document.createTextNode(" -- None selected -- ")
            newCell.appendChild(newText);
        }else{
            //alert(smTableE.rows.length);
        }
    }

    function sm(type, man, name, id)
    {
        this.type = type;
        this.man = man;
        //this.mod = mod;
        this.name = name;
        this.id = id;
        this.selected = false; //(sel == "true") ? true : false;
        return this;
    }

    function addsm(type, man, mod, name, id)
    {
        s = new sm(type, man, mod, name, id);
        sensorModels.push(s);
    }


    function selsm(id)
    {
        for(si in sensorModels)
        {
            sm = sensorModels[si];
            sm.selected = (sm.id == id);
        }
        sel_sm = id;
    }

<?php echo $this->sensorModelArray($this->sensorModelId); ?>

</script>


 

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

<?php if(JRequest::getVar('msg'))
        echo '<p class="passed">' . JRequest::getVar('msg', '', 'get', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<?php if(JRequest::getVar('errorMsg'))
        echo '<p class="failed">' . JRequest::getVar('errorMsg', '', 'get', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<h2><?php if($this->sensorid == -1) echo 'Add'; else echo 'Edit';?> Sensor</h2>
<hr>

<?php JHTML::_('behavior.calendar'); ?>

    <form method="post">

        <input type="hidden" name="id" value="<?php echo $this->facility->getId();?>"/>
        <input type="hidden" name="task" value="savesensor">
        <input type="hidden" name="sensorid" value="<?php echo $this->sensorid;?>"/>
        <input type="hidden" name="originalName" value="<?php echo $this->originalName;?>" ?>

        <table style="border:1px solid #fff;">

            <tr>
                <td nowrap="nowrap">Sensor Model: <span class="requiredfieldmarker">*</span></td>
                <td>

                    <table style="border:0px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                Type filter:
                            </td>
                            <td>
                                <select class="textentry" id="sm_types" onchange="populateSMTable()" onselect="populateSMTable()" onkeyup="populateSMTable()" >

                                    <option value="all">All Types</option>

                                    <?php
                                        foreach($this->sensorTypes as $smType)
                                        {
                                            $selectedText = ($this->selectedSensorTypeId == $smType->getId()) ? 'selected=true ' : '';
                                            echo '<option ' . $selectedText . 'value="' . $smType->getId() . '" >' . $smType->getName()  . '</option>\n';
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Manufacturer filter:
                            </td>
                            <td>
                                <select class="textentry" id="sm_mans" onchange="populateSMTable()" onselect="populateSMTable()" onkeyup="populateSMTable()">
                                    <option value="all">All Manufacturers</option>

                                    <?php

                                        foreach($this->sensorManufacturers as $manIndx => $manName)
                                        {
                                            $selectedText = ($this->manufacturer == $manName) ? 'selected=true ' : '';
                                            echo '<option ' . $selectedText . 'value="' . $manIndx . '">' . $manName . '</option>';
                                        }
                                    ?>
                                </select>
                            </td>

                        </tr>
                    </table>

                    <table id="sm_table" style="border:0px; margin-top:10px">
                        <tr class="head" style="background-color:#ddd;">
                            <th class="head"> </th>
                            <th class="head">Type</th>
                            <th class="head">Manufacturer</th>
                            <th class="head">Model</th>
                        </tr>
                    </table>

                </td>
            </tr>


            <tr>
                <td nowrap="nowrap">Name: <span class="requiredfieldmarker">*</span></td>
                <td width="100%"><input type="text" class="textentry" maxlength="50" value="<?php echo $this->name; ?>" name="name"/></td>
            </tr>
            <tr>
                <td nowrap="nowrap">Serial Number: </td>
                <td><input type="text" class="textentry" maxlength="50" value="<?php echo $this->serialNumber; ?>" name="serialNumber"/></td>
            </tr>
            <tr>
                <td nowrap="nowrap">Local Id: </td>
                <td><input type="text" class="textentry" maxlength="50" value="<?php echo $this->localId ?>" name="localId"></td>
            </tr>
            <tr>
                <td nowrap="nowrap">Supplier: </td>
                <td><input type="text" class="textentry wide" maxlength="50" value="<?php echo $this->supplier;?>" name="supplier"/></td>
            </tr>
            <tr>
                <td nowrap="nowrap">Commission Date: </td>
                <td>
                    <input id="commissionDate" type="text" name="commissionDate" value="<?php echo $this->commissionDate; ?>" />
                    <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('commissionDate', '%Y-%m-%d');" />

                </td>
            </tr>
            <tr>
                <td nowrap="nowrap">Decommission Date: </td>
                <td>
                    <input id="decommissionDate" type="text" name="decommissionDate" value="<?php echo $this->decommissionDate; ?>"/>
                    <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('decommissionDate', '%Y-%m-%d');" />
                </td>
            </tr>

            <tr>
                <td class="sectheaderbtn" colspan="2">
                    <input type="submit" name="submitted" value="Save" />
                    <input type="button" value="Cancel" onclick="history.back();" />
                </td>
            </tr>

        </table>

    </form>

<div style="margin-top:50px"> <span class="requiredfieldmarker" >*</span> Denotes required field </div>

</div>

<script type="text/javascript">
    <!--
    populateSMTable()
    //-->
</script>
