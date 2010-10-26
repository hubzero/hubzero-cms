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

<?php JHTML::_('behavior.modal'); ?>

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

<form id="frmProject" action="/warehouse/projecteditor/savesetup" method="post" enctype="multipart/form-data">
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
            <tr id="sensor">
              <td nowrap="" width="1">
                <p class="editorParagraph">
                  <label for="txtSpecimenMaterial" class="editorLabel">Sensors:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                  	 class="Tips3" title="Specimen Material :: Please provide the specimen material used during the experiment.">
                  		<img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="sensorPlan" class="editorInputFloat editorInputSize">
                  <table style="border:0">
                    <tr>
                      <td nowrap="" width="1"><label for="planName" class="editorLabel">Sensor List Name:</label></td>
                      <td>
                        <input id="planName" type="text" name="lpName" class="editorInputSize" onkeyup="suggestLocationPlanByExperimentId('/warehouse/projecteditor/locationplansearch?format=ajax', <?php echo $iExperimentId; ?>, 'planSearch', this.value, this.id)" value="" /> &nbsp; View All
                        <div id="planSearch" class="suggestResults"></div>
                      </td>
                    </tr>
                    <tr>
                      <td nowrap="" width="1"><label for="xyzUnits" class="editorLabel">Sensor Units:</label></td>
                      <td>
                        <select id="xyzUnits" type="text" name="xyzUnits">
                          
                        </select>
                      </td>
                    </tr>
                  </table>
                </div>

                <div id="sensorList" class="editorInputFloat editorInputSize topSpace20">
                  <fieldset style="width:100%">
                    <fieldset>
                      <legend>New Sensor</legend>
                      <table style="border:0">
                        <tr id="label">
                          <td nowrap="" width="1">
                            <label for="sensorId" class="editorLabel">Sensor ID:</label>
                          </td>
                          <td>
                            <input id="sensorId" type="text" name="Label"/>
                          </td>
                        </tr>
                        <tr id="type">
                          <td nowrap="" width="1">
                            <label for="sensorType" class="editorLabel">Type:</label>
                          </td>
                          <td>
                            <input id="sensorType" type="text" name="Type" onkeyup="suggest('/warehouse/projecteditor/sensortypesearch?format=ajax', 'sensorTypeSearch', this.value, this.id)" value="" />
                            <div id="sensorTypeSearch" class="suggestResults"></div>
                          </td>
                        </tr>
                        <tr id="orientation">
                          <td nowrap="" width="1">
                            <label for="sensorOrientationI" class="editorLabel">Orientation:</label>
                          </td>
                          <td>
                            <input id="sensorOrientationI" type="text" name="orientI" style="width:40px;color: #999999;" value="n1"/>&nbsp;
                            <input id="sensorOrientationJ" type="text" name="orientJ" style="width:40px;color: #999999;" value="n2"/>&nbsp;
                            <input id="sensorOrientationK" type="text" name="orientK" style="width:40px;color: #999999;" value="n3"/>
                          </td>
                        </tr>
                        <tr id="xyz">
                          <td nowrap="" width="1">
                            <label for="sensorX" class="editorLabel">XYZ Coordinates:</label>
                          </td>
                          <td>
                            <input id="sensorX" type="text" name="locX" style="width:40px;color: #999999;" value="x"/>&nbsp;
                            <input id="sensorY" type="text" name="locY" style="width:40px;color: #999999;" value="y"/>&nbsp;
                            <input id="sensorZ" type="text" name="locZ" style="width:40px;color: #999999;" value="z"/>&nbsp;
                          </td>
                        </tr>
                      </table>
                    </fieldset>
                  </fieldset>
                </div>
                <!--
                <div id="sensorAdd" class="editorInputFloat editorInputButton topSpace20"">
                  <a href="javascript:void(0);" title="Add another sensor"
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
                <input type="submit" value="Save Sensor" style="margin-top:15px"/>
              </td>
            </tr>
            <tr id="currentSensors">
              <td></td>
              <td>
                <div id="materialPicked" style="margin-top:10px;">
                  <span style="color:#999999">Added Sensor Lists</span>
                </div>
              </td>
            </tr>

            <tr id="test">
              <td></td>
              <td>
                <a href="/components/com_warehouse/images/calendar/calendar-blue.png" class="modal">
        <span title="hello">
            Go to Google
        </span>
</a>
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
