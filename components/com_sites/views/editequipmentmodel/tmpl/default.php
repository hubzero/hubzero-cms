<?php defined('_JEXEC') or die('Restricted access'); ?>


<script type="text/javascript">
<!--
function preSubmit(){
  nameTextbox = document.getElementsByName("name")[0];

  if(nameTextbox.value == '') {
    alert("Please enter a valid Equipment Model name.");
    return false;
  }
  return true;
}


function deleteEquipModel() {
  return window.confirm('If this equipment model has any equipment that refers to it, those pieces of equipment will be deleted too.  Are you SURE you want to continue?');
}

function changeUploadable(name) {
  if( !document.getElementById ) {
    return;
  }
  var div = document.getElementById(name+'UploadDiv');
  var div1 = document.getElementById(name+'Div');
  var field = document.getElementById(name);
  var deldiv = document.getElementById(name+'DeleteDiv');
  var delfield = document.getElementById(name+'Delete');
  if( div.style.display == 'block' ) {
    div.style.display = 'none';
    div1.style.display = 'block';
    delfield.value = 0;
    deldiv.style.display = 'none';
    field.value = '';
  }
  else {
    div.style.display = 'block';
    div1.style.display = 'none';
  }
}

function deleteUploadable(name) {
  if( !document.getElementById ) {
    return;
  }
  var deldiv = document.getElementById(name+'DeleteDiv');
  var delfield = document.getElementById(name+'Delete');
  delfield.value = 1;
  deldiv.style.display = 'block';
  changeUploadable(name);
}
//-->
</script>






<div id="facility-subpage-primarycontent">

<?php if(JRequest::getVar('msg'))
        echo '<p class="passed">' . JRequest::getVar('msg', '', 'get', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<?php if(JRequest::getVar('errorMsg'))
        echo '<p class="failed">' . JRequest::getVar('errorMsg', '', 'post', 'string', JREQUEST_ALLOWRAW) . '</p>';
?>

<h2><?php if($this->equipmentmodelid == -1) echo 'Add'; else echo 'Edit';?> Equipment Model</h2>
<hr>


    <form method="post">

        <input type="hidden" name="id" value="<?php echo $this->facility->getId();?>"/>
        <input type="hidden" name="task" value="saveequipmentmodel">
        <input type="hidden" name="equipmentmodeid" value="<?php echo $this->equipmentmodelid;?>"/>



        <table style="border:0px">
            <tr>
                <td colspan="2">
                    <p class="failed"> This equipment model is shared by ALL facilities.  Please do not edit existing equipment models unless you know for a FACT that only your facility uses it or you're just making a small typo correction.</p>
                    
                    <?php if($this->equipmentmodelid == -1) { ?>
                        <p class="failed"> After the initial save, you will be able to attach documentation files</p>
                    <?php } ?>
                </td>
            </tr>

            <tr><td>&nbsp;</tr>
            <tr>
                <td nowrap="nowrap">
                    Name<span class="requiredfieldmarker">*</span>
                </td>
                <td>
                    <input type="text" class="textentry" style="width:95%;" maxlength="100" name="name" value="<?php echo $this->equipmentModelFields['name']; ?>"/>
                </td>
            </tr>

            <tr>
                <td nowrap="nowrap">
                    Equipment Class
                </td>
                <td>
                    <select class="textentry" name="equipmentClassId">
                        <?php echo $this->equipmentclassddl ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td nowrap="nowrap">
                    Manufacturer
                </td>
                <td>
                    <input type="text" class="textentry" style="width:95%;" maxlength="100" name="manufacturer" value="<?php  echo $this->equipmentModelFields['manufacturer']; ?>"/>
                </td>
            </tr>

            <tr>
                <td nowrap="nowrap">
                    Supplier
                </td>
                <td>
                    <input type="text" class="textentry" style="width:95%;" maxlength="100" name="supplier" value="<?php  echo $this->equipmentModelFields['supplier']; ?>"/>
                </td>
            </tr>

            <tr>
                <td nowrap="nowrap">
                    Model Number
                </td>
                <td>
                    <input type="text" class="textentry" style="width:95%;" maxlength="100" name="modelnumber" value="<?php echo $this->equipmentModelFields['modelnumber'] ?>"/>
                </td>
            </tr>



        <?php if($this->equipmentmodelid <> -1){ ?>

            <tr>
                  <td>Additional Specification File</td>
                  <td>
                        <?php echo $this->equipmentModelFields['AdditionalSpecFile']; ?>
                  </td>
            </tr>

            <tr>
                  <td>Manufacturer's Document:</td>
                  <td>
                        <?php echo $this->equipmentModelFields['ManufacturerDocFile']; ?>
                  </td>
            </tr>

            <tr>
                 <td>Design Consideration Document</td>
                 <td>
                        <?php echo $this->equipmentModelFields['DesignConsiderationFile']; ?>
                 </td>
            </tr>

            <tr>
                  <td>Subcomponents Document</td>
                  <td>
                        <?php echo $this->equipmentModelFields['SubcomponentsDocFile']; ?>
                  </td>
            </tr>

            <tr>
                  <td>Interface Document:</td>
                  <td>
                        <?php echo $this->equipmentModelFields['InterfaceDocFile']; ?>
                  </td>
            </tr>
        <?php } ?>


        </table>


        <div style="padding-top:20px">
            <input type="submit" name="submitbutton" value="Save" onclick="if ( ! preSubmit() ){ return false; }" />
            <input type="submit" name="submitbutton" value="Cancel" />
        </div>
        
    </form>

<div style="margin-top:50px"> <span class="requiredfieldmarker" >*</span> Denotes required field </div>


</div>


