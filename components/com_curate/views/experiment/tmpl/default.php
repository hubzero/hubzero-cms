
<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/templates/newpulse/css/main.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_curate/css/curate.css",'text/css');
  $document->addScript($this->baseurl."/components/com_curate/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_curate/js/curate.js", 'text/javascript');
?>

<?php 
  $oProjectArray = $this->projectArray;
  $oExperimentArray = $this->experimentArray;
  $oExperimentDropDownArray = $this->experimentDropDownArray;
?>

<form id="frmProjects" action="#" method="post">
  <div class="contentpaneopen heading">
    <div class="content-header">
	  <h2 class="contentheading">NEES Data Curation</h2>
    </div>
  
    <div style="width: 100%; padding-bottom: 20px;" id="viewWrapper">
      <div style="float: left;" id="optionHeading">
        <a href="http://hpc102.tech.purdue.edu/curate/project/<?php echo $this->projectId; ?>">Return to Project</a>
      </div>
      <div align="right" style="float: left; right: 0pt; position: absolute; padding-right: 25px;" id="changeView">
        View:&nbsp; 
        <select id="cboViewObject" name="expId" onChange="changeView('frmProjects', '/curate?task=showexperiment');">
          <option value="">Select Experiment</option>
          <?php 
            foreach($oExperimentDropDownArray as $oExperiment){
              ?>
                <option value="<?php echo $oExperiment["EXPID"]; ?>"><?php echo $oExperiment["NAME"].": ".$oExperiment["TITLE"]; ?></option>
              <?php 
            }
          ?>
        </select>
      </div>
      <div class="clear"></div>
    </div>
  </div>
  
  <input type="hidden" id="curatedProjectId" name="curatedExperimentId" value="<?php echo $oExperimentArray['CURATED_ID']; ?>"/>
  <input type="hidden" id="projectId" name="projectId" value="<?php echo $this->projectId; ?>"/>
  <input type="hidden" id="expId" name="expId" value="<?php echo $oExperimentArray['EXPID']; ?>"/>
  <input type="hidden" id="experimentName" name="experimentName" value="<?php echo $oExperimentArray['EXPERIMENT_NAME']; ?>"/>
  <input type="hidden" id="projectName" name="projectName" value="<?php echo $oExperimentArray['PROJECT_NAME']; ?>"/>
  
  <div id="introduction" class="section">
    <div id="projectTop">
      <div id="projectTitle" style="font-size:16px;float:left;">
        <?php echo $oExperimentArray['EXPERIMENT_TITLE']; ?>
        <input type="hidden" id="txtProjectTitle" name="txtProjectTitle" value="<?php echo $oExperimentArray['EXPERIMENT_TITLE']; ?>"/>
      </div>
      <div id="projectButton" style="float: left; right: 0pt; position: absolute; padding-right: 25px;">
        <?php if($oExperimentArray['CURATED_ID'] == 0): ?>
          <input type="button" id="btnSubmitObject" value="Submit Experiment" onClick="validateObject('frmProjects', '/curate?task=experiment');"/>
        <?php endif; ?> 
      </div>
      <div class="clear"></div>
    </div>
    <p id="projectShortTitle"><?php echo $this->shortTitleAjax; ?></p>
    <p id="projectDescription"><?php echo $this->descriptionAjax; ?></p>
    <div id="projectInfo" style="100%">
      <div id="projectName" class="projectInfoColumn">
        <?php echo $this->curationNameAjax; ?>
      </div>
      <div id="projectObjectType" class="projectInfoColumn">
        <?php echo $this->curationObjectTypeAjax; ?>
      </div>
      <div id="projectVersion" class="projectInfoColumn">
	    <?php echo $this->versionAjax; ?>
      </div>
      <div class="clear projectInfoBottomSpace"></div>
      <div id="projectStartDate" class="projectInfoColumn">
        <?php echo $this->startDateAjax; ?>
      </div>
      <div id="projectCurated" class="projectInfoColumn">
        <?php echo $this->curatedDateAjax; ?>
      </div>
      <div id="projectCurationState" class="projectInfoColumn">
        <?php echo $this->curationStateAjax; ?>
      </div>
      <div class="clear projectInfoBottomSpace"></div>
      <div id="projectId" class="projectInfoColumn">
        Project ID: <?php echo $oExperimentArray['PROJID']; ?>
      </div>
      <div id="projectContactName" class="projectInfoColumn">
        <?php echo $this->contactNameAjax; ?>
      </div>
      <div id="projectITContact" class="projectInfoColumn">
        <?php echo $this->itContactAjax; ?>
      </div>
      <div class="clear projectInfoBottomSpace"></div>
      <div id="projectVisibility" class="projectInfoColumn">
        <?php echo $this->visibilityAjax; ?>
      </div>
      <div id="projectStatus" class="projectInfoColumn">
        <?php echo $this->statusAjax; ?>
      </div>
      <div id="projectLink" class="projectInfoColumn">
        <?php echo $this->linkAjax; ?>
      </div>
    </div>
  </div>

  <?php 
    $oExperimentDocumentArray = $this->experimentDocumentArray;    
  ?>

  <div class="main section">
    <div id="documentsTitle" style="float:left;">
      <span style="font-size:14px;color:#666666">Documents</span>&nbsp;&nbsp;&nbsp;
      <a href="javascript:void(0);" onClick="curateAll(<?php echo sizeof($oExperimentDocumentArray); ?>);">Select All</a> | 
      <a href="javascript:void(0);" onClick="completeAll(<?php echo sizeof($oExperimentDocumentArray); ?>)">Complete All</a>
    </div>  
    <div id="buttons" align="right">
      <input type="button" id="btnDownload" value="Download" onClick="validateDownload('frmProjects', '/curate?task=download', <?php echo sizeof($oExperimentDocumentArray); ?>);" style="margin-right:15px;"/>
      <input type="button" id="btnSubmit" value="Submit Documents" onClick="validateDocuments('frmProjects', '/curate?task=documents', <?php echo sizeof($oExperimentDocumentArray); ?>);"/>
    </div>
    <div class="clear"></div>
    
    <div style="margin-top:30px;overflow:auto;height:300px;">
      <table summary="A list of project documents" id="grouplist">
        
        <?php
// 		  echo CurateHtml::getHeaders();
        
          $nKey = 0;
          
          #display rows from query.
    	  foreach ($oExperimentDocumentArray as $nKey => $oDocument) {
    	  	  if($nKey%14===0)echo CurateHtml::getHeaders();
    	  	  
    	      $sClass="even";
    	      if($nKey%2===0)$sClass="odd";
    	    ?>
    	      <tr class="<?php echo $sClass ?>">
    	        <td></td>
    	        <td align="center">
    	          <input type="checkbox" 
    	          		 id="cbxCurateSelect<?php echo $nKey;?>" 
    	          		 name="cbxCurateSelect[]" 
    	          		 value="<?php echo $nKey."#".$oDocument["ID"]."#".$oDocument["OBJECT_ID"];?>"
    	          		 onClick="setCurateSelect(<?php echo $nKey;?>);"/>
    	        </td>
    	        <td align="center">
    	          <input type="checkbox" id="cbxCurateDone<?php echo $nKey;?>" 
    	          		 name="cbxCuratDone<?php echo $nKey;?>" 
    	          		 value="<?php if($oDocument["OBJECT_STATUS"]==="Complete"){echo "1";}else{echo "0";} ?>" 
    	          		 <?php if($oDocument["OBJECT_STATUS"]==="Complete") echo "checked"; ?>
    	          		 onClick="setCurationDone(<?php echo $nKey;?>, true);" 
    	          		 disabled/>
    	        </td>
    	        <td nowrap="" class="spreadsheetInput">
    	          <?php echo $oDocument["PATH"] ?>
    	          <input type="hidden" id="txtDocumentPath<?php echo $nKey;?>" name="strDocumentPath<?php echo $nKey;?>" value="<?php echo $oDocument["PATH"] ?>"/>
    	        </td>
    	        <td nowrap="" class="spreadsheetInput">
    	          <?php echo $oDocument["NAME"] ?>
    	          <input type="hidden" id="txtDocumentName<?php echo $nKey;?>" name="strDocumentName<?php echo $nKey;?>" value="<?php echo $oDocument["NAME"] ?>"/>
    	        </td>
    	        <td nowrap="" class="spreadsheetInput"><input type="text" id="txtDocumentTitle<?php echo $nKey;?>" name="strDocumentTitle<?php echo $nKey;?>" value="<?php echo $oDocument["TITLE"] ?>" class="spreadsheetInput" disabled></td>
    	        <td nowrap="" class="spreadsheetInput"><input type="text" id="txtDocumentDescription<?php echo $nKey;?>" name="strDocumentDescription<?php echo $nKey;?>" value="<?php echo $oDocument["DESCRIPTION"] ?>" class="spreadsheetInput" disabled></td>
    	        <td class="spreadsheetInput"><?php echo $oDocument["OBJECT_TYPE"]; ?></td>
    	        <td class="spreadsheetInput"><?php echo $oDocument["EXTENTION"]; ?></td>
    	        <td><input type="text" id="txtDocumentCurateDate<?php echo $nKey;?>" name="strDocumentCurateDate<?php echo $nKey;?>" value="<?php echo $oDocument["CURATE_CREATED_DATE"]; ?>" class="spreadsheetInput" disabled></td>
    	        <td><input type="text" id="txtDocumentCurateVersion<?php echo $nKey;?>" name="strDocumentCurateVersion<?php echo $nKey;?>" value="<?php echo $oDocument["CURATED_VERSION"]; ?>" class="spreadsheetInput" disabled size="5"></td>
    	      </tr>
    	  <?
     	  }
     	  
     	  $oCuratedObjectTypeArray = $this->curatedObjectTypeArray;
     	  
     	  
     	  
  	    ?>
      </table>
    </div>
    
  </div>
</form>