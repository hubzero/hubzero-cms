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

<form id="frmProject" action="/warehouse/projecteditor/savematerial" method="post" enctype="multipart/form-data">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="projid" value="<?php echo $iProjectId; ?>" />
<input type="hidden" name="experimentId" value="<?php echo $iExperimentId; ?>" />

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;">Create a New Experiment</span>
    </div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php echo $this->strTabs; ?>
      
      <div class="aside">
        <!--
        <p style="font-size:11px;color:#999999; border-width: 1px; border-style: solid; border-color: #cccccc;" align="center">
          <img alt="" src="/components/com_projecteditor/images/logos/NEES-logo_grayscale.png"/><br><br>
          <span style="font-size:48px;font-weight:bold;color:#999999;">NEES</span><br><br>
        </p>
        
        <input type="text" id="txtCaption" name="caption" value="Enter photo caption" style="width:210px;color:#999999;" onFocus="this.style.color='#000000'; this.value='';"/> <br><br>
        <input type="file" id="txtPhoto" name="thumbnail"/>
        -->
        
        <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
          <p style="margin-left:10px; margin-top:10px;">1000 Views</p>
          
          <p style="margin-left:10px;">100 Downloads</p>    
        </div>
        
      
        <div id="curation">
          <span class="curationTitle">Curation in progress:</span>
          <?php //echo $this->mod_curationprogress; ?>
        </div>
        
        <div class="whatisthis">
          <h4>What's this?</h4>
          <p>
            Once the curator starts working with your submission, monitor the object's progress by reading
            the curation history.
          </p>
        </div>
      </div>
      <div class="subject">

        <?php echo $this->strSubTabs; ?>

        <div id="about" style="padding-top:1em;">
          <?php 
            if(isset($_SESSION["ERRORS"])){
              $strErrorArray = $_SESSION["ERRORS"];
              if(!empty($strErrorArray)){?> 
                <p class="error">
                  <?  
                    foreach($strErrorArray as $strError){
                  	  echo $strError."<br>";
                    }
                  ?>
                </p> 
              <?php	
              }
            }
          ?>
          
          <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;margin-top:20px;">
            <!--
            <tr id="specimentType">
              <td nowrap="" width="1">
                <p class="editorParagraph">
                  <label for="txtSpecimenType" class="editorLabel">Specimen Type:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                  	 class="Tips3" title="Specimen Type :: Please provide the specimen type used during the experiment.">
                    <img alt="" src="<?php //echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="specimenTypeInput" class="editorInputFloat editorInputSizeFull">
                  <input type="text" id="txtSpecimenType" name="specimenType" class="editorInputSizeFull"
                  		 onkeyup="suggest('/projecteditor/specimentypesearch?format=ajax', 'specimenTypeSearch', this.value, this.id)"
                  		 style="width:100%;" value="<?php //echo $this->strSpecimenType; ?>"
                                 autocomplete="off"/>
                  <div id="specimenTypeSearch" class="suggestResults"></div>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            -->
            <tr id="specimenMaterial">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtSpecimenMaterial" class="editorLabel">Specimen Material:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                  	 class="Tips3" title="Specimen Material :: Please provide the specimen material used during the experiment.">
                  		<img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="specimenMaterialList" class="editorInputFloat editorInputSizeFull">
                  <fieldset style="width:100%">
                    <fieldset>
                      <legend>New Material</legend>
                      <div style="margin-top:20px;">
                        <div style="float:left; margin-right:6%; width:45%;">
                          <input id="txtMaterial" type="text" name="material" value="<?php echo $this->strMaterial; ?>" style="width:100%;<?php if(!$this->iMaterialId){echo "color:#999999";} ?>" onFocus="style.color='#000000'; this.value='';" />
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
                          <textarea id="taMaterialDesc" name="materialDesc" style="width:100%; height:100px;<?php if(!$this->iMaterialId){echo "color:#999999";} ?>" onFocus="style.color='#000000'; this.value='';"><?php echo $this->strMaterialDesc; ?></textarea>
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
                <!--
                <div id="materialAdd" class="editorInputFloat editorInputButton">
                  <a href="javascript:void(0);" title="Add another material"
                     style="border-bottom: 0px" onClick="saveForm('frmProject', '/warehouse/projecteditor/savematerial');">
                       <img alt="" src="/components/com_projecteditor/images/icons/addButton.png" border="0"/>
                  </a>
                </div>
                -->
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="preview">
              <td></td>
              <td>
                <input type="submit" value="Save Materials" style="margin-top:15px"/>
              </td>
            </tr>
            <tr id="currentMaterials">
              <td></td>  
              <td>
                <div id="materialPicked" style="margin-top:10px;">
                  <span style="color:#999999">Current Materials</span><br/>
                  <?php echo $this->materialInfo; ?>
                </div>
              </td>
            </tr>
          </table>
          
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div> 
</div>

</form>
