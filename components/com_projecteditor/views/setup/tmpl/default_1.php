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
  
  if(!isset($_REQUEST["ERRORS"])){
    $_SESSION["facility"] = null;
    $_SESSION["organization"] = null;
    $_SESSION["sponsor"] = null;
    $_SESSION["website"] = null;
    $_SESSION["materials"] = null;
    $_SESSION["SUGGESTED_FACILITY_EQUIPMENT"] = null;
  }else{
    $strErrorArray = $_REQUEST["ERRORS"];
    if(empty($strErrorArray)){
      $_SESSION["facility"] = null;
      $_SESSION["organization"] = null;
      $_SESSION["sponsor"] = null;
      $_SESSION["website"] = null;
      $_SESSION["materials"] = null;
      $_SESSION["SUGGESTED_FACILITY_EQUIPMENT"] = null;	
    }
  }
?>

<form id="frmProject" action="/warehouse/projecteditor/previewexp" method="post" enctype="multipart/form-data">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="projid" value="<?php echo $this->iProjectId; ?>" />

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
        <p style="font-size:11px;color:#999999; border-width: 1px; border-style: solid; border-color: #cccccc;" align="center">
          <img alt="" src="/components/com_projecteditor/images/logos/NEES-logo_grayscale.png"/><br><br>
          <span style="font-size:48px;font-weight:bold;color:#999999;">NEES</span><br><br>
        </p>
        
        <input type="text" id="txtCaption" name="caption" value="Enter photo caption" style="width:210px;color:#999999;" onFocus="this.style.color='#000000'; this.value='';"/> <br><br>
        <input type="file" id="txtPhoto" name="thumbnail"/>
      
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
        <div id="about" style="padding-top:1em;">
          <?php 
            if(isset($_REQUEST["ERRORS"])){
              $strErrorArray = $_REQUEST["ERRORS"];
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
          
          <?php echo $this->strFilmstrip; ?>
          
          <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;margin-top:20px;">
            <tr id="title">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtTitle" class="editorLabel editorRequired">Title:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Title :: Please provide the official project title.  For NSF projects, use the title on the grant."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <input type="text" id="txtTitle" name="title" class="editorInputSize" value="<?php echo $this->strTitle; ?>" />
              </td>
            </tr>
            <tr id="description">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtDescription" class="editorLabel">Description:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Description :: Please provide rich text that describes the nature of work being done.">
                       <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <textarea id="txtDescription" name="description" class="editorTextArea"><?php echo $this->strDescription; ?></textarea>
              </td>
            </tr>
            <tr id="start_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strStartDate" class="editorLabel editorRequired">Start Date:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Start Date :: Please provide the date you started the project."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="startDateInput" class="editorInputFloat editorInputSize">
                  <input type="text" id="strStartDate" name="startdate" style="width:100%;" value="<?php echo $this->strStartDate; ?>" />
                </div>
                <div id="startDateCalendar" class="editorInputFloat editorInputButton">
                  <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strStartDate', '%m/%d/%Y');" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="end_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strEndDate" class="editorLabel">End Date:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="End Date :: Please provide the date you completed the project."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="endDateInput" class="editorInputFloat editorInputSize">
                  <input type="text" id="strEndDate" name="enddate" style="width:100%;" value="<?php echo $this->strEndDate; ?>" />
                </div>
                <div id="endDateCalendar" class="editorInputFloat editorInputButton">
                  <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strEndDate', '%m/%d/%Y');" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="facility">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtFacility" class="editorLabel">Facility:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Facility :: Please provide the name of the NEES site(s).">
                    <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="facilityInput" class="editorInputFloat editorInputSize">
                  <input type="text" id="txtFacility" name="facility" class="editorInputSize" 
                  		 onkeyup="suggestFacility('/projecteditor/facilitysearch?format=ajax', 'facilitySearch', this.value, this.id, '/projecteditor/equipment?format=ajax', 'equipmentList')" 
                  		 style="width:100%;" value="<?php echo $this->strFacility; ?>" />
                  <div id="facilitySearch" class="suggestResults"></div>
                </div>
                <div id="facilityAdd" class="editorInputFloat editorInputButton">
                  <a href="javascript:void(0);" title="Add another facility" 
                     style="border-bottom: 0px" onClick="addInputViaMootools('/projecteditor/add?format=ajax', 'facility', 'txtFacility', 'facilityPicked');">
                       <img alt="" src="/components/com_projecteditor/images/icons/addButton.png" border="0"/>
                  </a>
                </div>
                <div class="clear"></div>
                <div id="facilityPicked">
                  <?php 
                    echo $this->strFacilityPicked;
		  ?>
                </div>
              </td>
            </tr>
            <tr id="equipment">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="cboEquipment" class="editorLabel">Equipment:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                      class="Tips3" title="Equipment :: Please select the equipment used from the facility (NEES site) above.">
                      <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="equipmentList" class="editorInputSize" style="color:#999999;">Enter one or more facilities (NEES Sites) above.</div>  
              </td>
            </tr>
            <tr id="specimentType">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtSpecimenType" class="editorLabel">Specimen Type:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                  	 class="Tips3" title="Specimen Type :: Please provide the specimen type used during the experiment.">
                    <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="specimenTypeInput" class="editorInputFloat editorInputSize">
                  <input type="text" id="txtSpecimenType" name="specimenType" class="editorInputSize" 
                  		 onkeyup="suggest('/projecteditor/specimentypesearch?format=ajax', 'specimenTypeSearch', this.value, this.id)" 
                  		 style="width:100%;" value="<?php echo $this->strSpecimenType; ?>" />
                  <div id="specimenTypeSearch" class="suggestResults"></div>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
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
                <div id="specimenMaterialList" class="editorInputFloat editorInputSize">
                  <fieldset style="width:100%">
                    <fieldset>
                      <legend>New Material</legend>
                      <div style="margin-top:20px;">
                        <div style="float:left; margin-right:6%; width:45%;">
                          <input id="txtMaterial" type="text" name="material" value="<?php echo $this->strMaterial; ?>" style="width:100%;color:#999999;" onFocus="style.color='#000000'; this.value='';" />
                        </div>
                        <div style="float:left; width:45%;">
                          <input id="txtMaterialType" type="text" name="materialType" value="<?php echo $this->strMaterialType; ?>"
                                     style="width:100%;color:#999999;" onFocus="style.color='#000000'; this.value='';"
                                     onkeyup="suggest('/projecteditor/materialtypesearch?format=ajax', 'materialTypeSearch', this.value, this.id)"/>
                          <div id="materialTypeSearch" class="suggestResults"></div>
                        </div>
                        <div class="clear"></div>
                        <div style="width:97%; margin-top:10px;">
                          <textarea id="taMaterialDesc" name="materialDesc" style="width:100%; height:100px;color:#999999;" onFocus="style.color='#000000'; this.value='';"><?php echo $this->strMaterialDesc; ?></textarea>
                        </div>
                        <div style="margin-top:10px;">
                          Material File: <input type="file" name="materialFile" id="materialFile"/>
                        </div>
                      </div>
                    </fieldset>
                  </fieldset>
                </div>
                <div id="materialAdd" class="editorInputFloat editorInputButton">
                  <a href="javascript:void(0);" title="Add another material" 
                     style="border-bottom: 0px" onClick="addNewMaterial('/warehouse/projecteditor/addmaterial', 'materialPicked');">
                       <img alt="" src="/components/com_projecteditor/images/icons/addButton.png" border="0"/>
                  </a>
                </div>
                <div class="clear"></div>
                <div id="materialPicked" style="margin-top:10px;color: #999999;">
                  Added Materials
                </div>
              </td>
            </tr>
            <tr id="sensors">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Sensors:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Sensors :: Please provide images that represent sensor setup.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="sensorList" class="editorInputFloat editorInputSize">
                  <fieldset style="width:100%">
                    <fieldset>
                      <legend>New Sensor</legend>
                      <div style="margin-top:20px;">
                        <table style="border:0px;">
                          <tr>
                            <td width="1" nowrap="">Name of List:</td>
                            <td>
                              <input type="text" name="planName" />
                              <div id="planNameSearch"></div>
                            </td>
                          </tr>
                          <tr>
                            <td width="1" nowrap="">Coordinate Space</td>
                            <td>
                              <select name="coordinateSpace">
                                <option value="">-Select-</option>
                              </select>
                            </td>
                          </tr>
                          <tr>
                            <td width="1" nowrap="">Location Type</td>
                            <td>
                              <select name="locationType">
                                <option value="">-Select-</option>
                              </select>
                            </td>
                          </tr>
                          <tr>
                            <td width="1" nowrap="">Sensor Type</td>
                            <td>
                              <input type="text" name="sensorType" />
                              <div id="sensorTypeSearch"></div>
                            </td>
                          </tr>
                          <tr>
                            <td width="1" nowrap="">Sensor Name</td>
                            <td><input type="text" name="sensorName" /></td>
                          </tr>
                          <tr>
                            <td width="1" nowrap="">Sensor Values</td>
                            <td>
                              <table style="border:0px;">
                               <tr>
                                 <td width="1" nowrap="" style="padding-left:0px;">
                                   <input type="text" name="x" value="X Value"/>
                                 </td>
                                 <td>
                                   <select name="x_unit">
                                     <option value="">-Units-</option>
                                   </select>
                                 </td>
                               </tr>
                               <tr>
                                 <td width="1" nowrap="" style="padding-left:0px;">
                                   <input type="text" name="y" value="Y Value"/>
                                 </td>
                                 <td>
                                   <select name="x_unit">
                                     <option value="">-Units-</option>
                                   </select>
                                 </td>
                               </tr>
                               <tr>
                                 <td width="1" nowrap="" style="padding-left:0px;">
                                   <input type="text" name="z" value="Z Value"/>
                                 </td>
                                 <td>
                                   <select name="x_unit">
                                     <option value="">-Units-</option>
                                   </select>
                                 </td>
                               </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </div>
                    </fieldset>
                  </fieldset>
                </div>
                <div id="materialAdd" class="editorInputFloat editorInputButton">
                  <a href="javascript:void(0);" title="Add another material"
                     style="border-bottom: 0px" onClick="addNewMaterial('/warehouse/projecteditor/addmaterial', 'materialPicked');">
                       <img alt="" src="/components/com_projecteditor/images/icons/addButton.png" border="0"/>
                  </a>
                </div>
                <div class="clear"></div>
                <div id="materialPicked" style="margin-top:10px;color: #999999;">
                  Added Sensors
                </div>
              </td>
            </tr>
            <tr id="drawings">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Drawings:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Drawings :: Please provide images that represent sensor setup.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
                <!--
                <a href="">Browse</a><br>
                <a href="">Search</a>
                -->
              </td>
              <td>
                <div id="drawings"class="editorInputFloat editorInputSize">
                  <?php echo $this->mod_warehouseupload_drawings; ?>
                </div>
                <div id="drawingAdd" class="editorInputFloat editorInputButton">
                  <a href="javascript:void(0);" title="Add another drawing"
                     style="border-bottom: 0px" onClick="addNewDrawing('/warehouse/projecteditor/addmaterial', 'materialPicked');">
                       <img alt="" src="/components/com_projecteditor/images/icons/addButton.png" border="0"/>
                  </a>
                </div>
                <div class="clear"></div>
                <div id="drawingInput" class="editorInputSize"> </div>
              </td>
            </tr>
            <tr id="data">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Data:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Drawings :: Please provide images that represent sensor setup.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="dataLinks" class="editorInputSize">
                  <a href="">Upload New File</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                  <a href="">Browse Existing Files</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                  <a href="">Search Existing Files</a>
                </div>
                <div id="dataInput" class="editorInputSize"> </div>
              </td>
            </tr>
            <tr id="images">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Images:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Drawings :: Please provide images that represent sensor setup.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="imagesLinks" class="editorInputSize">
                  <a href="">Upload New File</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                  <a href="">Browse Existing Files</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                  <a href="">Search Existing Files</a>
                </div>
                <div id="imagesInput" class="editorInputSize"> </div>
              </td>
            </tr>
            <tr id="tags">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Tags (related projects):</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Tags :: Please provide keywords that highlight the project.  When users search, they will find your project using the tags.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="tagInput" class="editorInputSize">
                  <input type="text" autocomplete="off" value="" id="actags" name="tags" style="display:none;">
                </div>
              </td>
            </tr>
            
            <tr>
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtAccess" class="editorLabel editorRequired">Access Settings:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Access Settings :: Control resources the are available to users.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <fieldset>
                <fieldset>
                  <legend>Content Privacy</legend>
                  <p>By default, resources added to this group will be:</p>
                  <label>
                        <input type="radio" <?php if($this->iAccess==0)echo "checked='checked'"; ?> value="0" name="access" class="option">
                        <strong>public</strong> <span class="indent">Resources are available to any user.</span>
                  </label>
                  <label>
                        <input type="radio" <?php if($this->iAccess==3)echo "checked='checked'"; ?> value="3" name="access" class="option">
                        <strong>protected</strong> <span class="indent">Resource titles and descriptions are available to any user but the resource itself is available only to group members.</span>
                  </label>
                  <label>
                        <input type="radio" <?php if($this->iAccess==4)echo "checked='checked'"; ?> value="4" name="access" class="option">
                        <strong>private</strong> <span class="indent">Resources are completely hidden from users outside the group.</span>
                  </label>
                </fieldset>
                </fieldset>
              </td>
            </tr>
            <tr id="preview">
              <td></td>
              <td>
                <input type="submit" value="Create Experiment" style="margin-top:15px"/>
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
