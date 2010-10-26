<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: 0"); // Date in the past
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
  $oUser = $this->oUser;

  $iExperimentId = 0;
  $iProjectId = 0;

  /* @var $oExperiment Experiment */
  $oExperiment = unserialize($_SESSION[ExperimentPeer::TABLE_NAME]);
  if($oExperiment){
    $iExperimentId = $oExperiment->getId();
    $iProjectId = $oExperiment->getProject()->getId();
  }
?>

  <?php if($this->iMaterialId): ?>
    <input type="hidden" name="materialId" value="<?php echo $this->iMaterialId; ?>" />
  <?php endif; ?>

  <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;">
    <tr id="specimenMaterial">
      <td>
        <div id="specimenMaterialList" class="editorInputFloat editorInputSizeFull">
          <fieldset style="width:100%">
            <fieldset>
              <legend>Edit Material</legend>
              <div style="margin-top:20px;">
                <div style="margin-right:6%; width:45%;" class="editorInputFloat">
                  Material Name<span class="requiredfieldmarker">*</span>
                </div>
                <div style="float:left; width:45%;">
                  Material Type<span class="requiredfieldmarker">*</span>
                </div>
                <div class="clear"></div>
                <div style="float:left; margin-right:6%; width:45%;">
                  <input id="txtMaterial" type="text" name="material" value="<?php echo $this->strMaterial; ?>" style="width:100%;<?php if(!$this->iMaterialId){echo "color:#999999";} ?>" onFocus="style.color='#000000'; clearValue(this.id, 'Material name');;" />
                </div>
                <div style="float:left; width:45%;">
                  <select id="cboMaterialType" name="materialType" onChange="getMootools('/warehouse/projecteditor/materialtypes?format=ajax&materialTypeId='+this.value , 'mproperties');">
                    <option value="0">-Select Material Type-</option>
                  <?php
                   $oMaterialTypeArray = unserialize($_REQUEST[MaterialTypePeer::TABLE_NAME]);
                   foreach($oMaterialTypeArray as $oMaterialType){
                     /* @var $oMaterialType MaterialType */
                     $strSelected="";
                     if($oMaterialType->getName()==$this->strMaterialType){
                       $strSelected = "selected";
                     }
                  ?>
                      <option value="<?php echo $oMaterialType->getId(); ?>" <?php echo $strSelected; ?>><?php echo $oMaterialType->getName(); ?></option>
                  <?php
                  }
                  ?>
                  </select>
                </div>
                <div class="clear"></div>
                <div style="width:97%; margin-top:10px;">
                  Description<span class="requiredfieldmarker">*</span>
                </div>
                <div style="width:97%;">
                  <textarea id="taMaterialDesc" name="materialDesc" style="width:100%; height:100px;<?php if(!$this->iMaterialId){echo "color:#999999";} ?>" onFocus="style.color='#000000';clearValue(this.id, 'Material description');"><?php echo $this->strMaterialDesc; ?></textarea>
                </div>
                <div id="mpropertiesHeader" class="topSpace10"><b>Properties</b></div>
                <div id="mproperties"><?php echo $this->strMaterialProperties; ?></div>
                <div id="mfilesHeader" class="topSpace20"><b>Files</b></div>
                <div id="mfiles"><?php echo $this->strMaterialFiles; ?></div>

                <div class="topSpace10" style="margin-left:30px;">
                  Upload Material File: <input type="file" name="materialFile" id="materialFile"/>
                </div>
              </div>
            </fieldset>
          </fieldset>
        </div>

        <div class="clear"></div>
      </td>
    </tr>
    <tr id="preview">
      <td>
        <input type="submit" value="Save Material" style="margin-top:15px"/>
      </td>
    </tr>
  </table>
       
