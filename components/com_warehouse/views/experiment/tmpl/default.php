<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>
 
<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
    </div>
      
    <div id="treeBrowser" style="float:left;width:20%;"></div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php $oExperiment = unserialize($_REQUEST[Experiments::SELECTED]); ?>
      
        <?php echo TabHtml::getSearchForm( "/warehouse/find" ); ?>
        <?php echo $this->strTabs; ?>
        <div class="aside">
          <p><?php //echo $oExperiment->getExperimentThumbnailHTML(); ?></p>

          <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
            <p style="margin-left:10px; margin-top:10px;"><?php echo $this->iEntityActivityLogViews; ?> Views</p>

            <p style="margin-left:10px;"><?php echo $this->iEntityActivityLogDownloads; ?> Downloads</p>
          </div>


          <div id="curation">
            <span class="curationTitle">Curation progress:</span>
            <?php echo $this->mod_curationprogress; ?>
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
            <div id="experimentTitle" style="padding-bottom:1em;font-size:14px;font-weight:bold;">
              Experiment: <?php /*echo $oExperiment->getName() .": ".*/ echo $oExperiment->getTitle(); ?>
            </div>

            <div id="experimentInfo">

              <?php echo $this->mod_warehousefilmstrip; ?>

              <table cellpadding="1" cellspacing="1" style="margin-top:20px;border-bottom:0px;border-top:0px;">
                <tr id="description">
                  <td class="entityDetail">Description:</td>
                  <td><?php echo $oExperiment->getDescription(); ?></td>
                </tr>
                <tr id="dates">
                  <td class="entityDetail">Dates:</td>
                  <td><?php echo $this->strDates; ?></td>
                </tr>
                <tr id="facility">
                  <td class="entityDetail">Facility:</td>
                  <td>
                    <?php
                      $oOrganizationArray = unserialize($_REQUEST[OrganizationPeer::TABLE_NAME]);
                      foreach($oOrganizationArray as $iKey => $oOrganization){
                      ?>
                        <span class="nobr"><a href="/sites/?view=site&id=<?php echo $oOrganization->getId(); ?>"><?php echo $oOrganization->getName(); ?></a></span>
                        <?php
                          if($iKey < sizeof($oOrganizationArray)-1){
                            echo ", ";
                          }
                      }
                    ?>
                  </td>
                </tr>
                <tr id="specimenType">
                  <td class="entityDetail">Specimen Type:</td>
                  <td>
                    <?php
                      $oSpecimen =  unserialize($_REQUEST[SpecimenPeer::TABLE_NAME]);
                      if($oSpecimen != null){
                        echo $oSpecimen->getName();
                      }
                                ?>
                  </td>
                </tr>
                <tr id="specimenMaterial">
                  <td class="entityDetail">Specimen Material:</td>
                  <td>
                    <?php
                      $oMaterialArray = unserialize($_REQUEST[MaterialPeer::TABLE_NAME]);
                      if(!empty($oMaterialArray)){
                        $iMaterialIndex = 0;
                        while($iMaterialIndex < 3 && $iMaterialIndex < sizeof($oMaterialArray)){
                          $oMaterial = $oMaterialArray[$iMaterialIndex];
                          echo $oMaterial->getName();
                          ?> (<a href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/<?php echo $oProject->getId(); ?>/experiment/<?php echo $oExperiment->getId(); ?>/detail/<?php echo $oMaterial->getId(); ?>?format=ajax', 'experimentInfo');">view</a>)<?php

                          if( $iMaterialIndex < (sizeof($oMaterialArray)-1) ){
                                          ?><br><?php
                          }
                          ++$iMaterialIndex;
                        }

                        if(sizeof($oMaterialArray) > 3){
                          ?><a href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/<?php echo $oProject->getId(); ?>/experiment/<?php echo $oExperiment->getId(); ?>?format=ajax', 'experimentInfo');">more...</a><br><br><?php
                        }
                      }
                    ?>
                  </td>
                </tr>
                <tr id="sensors">
                  <td class="entityDetail">Sensors:</td>
                  <td>
                   <?php
                     $oLocationPlanArray = unserialize($_REQUEST[LocationPlanPeer::TABLE_NAME]);
                     foreach($oLocationPlanArray as $oLocationPlan){
                       ?>
                       <a href="javascript:void(0);" onClick="getMootools('/warehouse/sensors/<?php echo $oLocationPlan->getId(); ?>/project/<?php echo $oProject->getId(); ?>/experiment/<?php echo $oExperiment->getId(); ?>?format=ajax', 'experimentInfo');"><?php echo $oLocationPlan->getName() ?></a><br>
                       <?php
                     }
                   ?>
                  </td>
                </tr>
                <tr id="drawings">
                  <td class="entityDetail">Drawings:</td>
                  <td>
                    <?php
                      $oDrawingArray =  unserialize($_REQUEST["Drawings"]);
                      foreach($oDrawingArray as $iDrawingIndex=>$oDrawing){
                        $strDrawingUrl = $oDrawing->getPath()."/display_".$oDrawing->getId()."_".$oDrawing->getName();
                        $strDrawingUrl = str_replace("/nees/home/",  "",  $strDrawingUrl);
                        $strDrawingUrl = str_replace(".groups",  "",  $strDrawingUrl);

                        $strLightbox = "";
                        $strExtension = $oDrawing->getDocumentFormat()->getDefaultExtension();
                        if($strExtension==="png" || $strExtension==="jpg" || $strExtension==="gif"){
                          $strLightbox = "lightbox[drawings]";
                        }
                      ?>
                        <a rel="<?php echo $strLightbox; ?>"  title="<?php echo $oDrawing->getTitle(); ?>" href="/data/get/<?php echo $strDrawingUrl; ?>" title=""><?php echo $oDrawing->getTitle(); ?></a><br>
                      <?php
                        if($iDrawingIndex==2 && ($iDrawingIndex < sizeof($oDrawingArray)-1)){?>
                          <a href="/warehouse/drawings/project/<?php echo $oProject->getId(); ?>/experiment/<?php echo $oExperiment->getId(); ?>">more...</a><br>
                        <?
                          break;
                        }
                      }
                    ?>
                  </td>
                </tr>

                <?php
                 $oDataFileArray = unserialize($_REQUEST["ExperimentDataFiles"]);
                 if(!empty($oDataFileArray)){
                   /*
                    * Only 1 Trial and 1 Repetition is found.  Show the data.
                    */
                   $oToolFileArray = unserialize($_REQUEST["TOOL_DATA_FILES"]);
                   if(!empty($oToolFileArray)){
                   ?>
                     <tr id="interactive">
                       <td class="entityDetail">Data:</td>
                           <td>
                             <?php
                                   foreach($oToolFileArray as $iToolIndex=>$oToolDataFile){
                                         $strToolLink = $oToolDataFile->getPath()."/".$oToolDataFile->getName();
                                         $strToolTitle = $oToolDataFile->getTitle();
                                         $strToolDesc = $oToolDataFile->getDescription();
                                         if(strlen($strToolDesc)==0){
                                           $strToolDesc = "Click to launch tool ".$oToolDataFile->getOpeningTool().".";
                                         }
                                 ?>
                                        <a href="<?php echo NeesConfig::LAUNCH_INDEED; ?>=<?php echo $strToolLink; ?>" title="<?php echo $strToolDesc; ?>"><?php echo $strToolTitle; ?></a>
                                 <?php
                                         if($iToolIndex < sizeof($oToolFileArray)-1){
                                   echo "<br>";
                                 }
                                   }
                                         ?>
                                         <div id="dataList"><a href="javascript:void(0);" onClick="getMootools('/warehouse/filebrowser?path=<?php echo $this->strCurrentPath; ?>&format=ajax','dataList');">more...</a></div>
                          </td>
                        </tr>
                        <tr id="photos">
                          <td class="entityDetail">Images:</td>
                          <td>
                            <?php
                              if($this->photoCount > 0):
                            ?>
                          <div id="imageList">Additional photos (<a href="/warehouse/photos/project/<?php echo $oProject->getId(); ?>/experiment/<?php echo $oExperiment->getId(); ?>">view</a>)</div>
                        <?php
                              else:
                            ?>
                              <div id="imageList">Images may be found on the tab labeled 'More'.</div>
                            <?php
                              endif;
                            ?>
                          </td>
                        </tr>
                   <?php
                   }else{
                      /*
                       * Okay, there's not any tools,
                       * but the user supplied some data files.
                       */
                      ?>
                      <tr id="interactive">
                       <td class="entityDetail">Data:</td>
                           <td>
                             <div id="dataList"><a href="javascript:void(0);" onClick="getMootools('/warehouse/filebrowser?path=<?php echo $this->strCurrentPath; ?>&format=ajax','dataList');">more...</a></div>
                          </td>
                        </tr>
                        <tr id="photos">
                          <td class="entityDetail">Images:</td>
                          <td>
                            <?php
                              if($this->photoCount > 0):
                            ?>
                          <div id="imageList">Additional photos (<a href="/warehouse/photos/project/<?php echo $oProject->getId(); ?>/experiment/<?php echo $oExperiment->getId(); ?>">view</a>)</div>
                        <?php
                              else:
                            ?>
                              <div id="imageList">Images may be found on the tab labeled 'More'.</div>
                            <?php
                              endif;
                            ?>
                          </td>
                        </tr>
                    <?php
                   }
                 }else{
                   /*
                    * More than 1 trial and/or repetition exists.  The user has to select it.
                    */
                   ?>
                   <tr id="trials">
                     <td class="entityDetail">Trials:</td>
                     <td>
                       <?php
                         $oTrialArray = unserialize($_REQUEST[TrialPeer::TABLE_NAME]);
                       ?>
                            <select id="cboTrials" name="trial" onChange="getTrialInfo(this.id, 'trial', 'trialDesc');getTrialInfo(this.id, 'repetitions', 'repetitionList');">
                              <option value="">Select Trial</option>
                              <?php
                                foreach($oTrialArray as $oTrial){
                                  $strTrialTitle = $oTrial->getTitle();
                                  if(strlen($strTrialTitle) > 125){
                                    $strTrialTitle = StringHelper::neat_trim($strTrialTitle, 125);
                                  }
                                  ?>
                                  <option value="<?php echo $oTrial->getId(); ?>"><?php echo $oTrial->getName() .": ". $strTrialTitle; ?></option>
                                <?php }
                              ?>
                            </select>
                            <div id="trialDesc"></div>
                         </td>
                   </tr>
                   <?php if($this->repetitionDataFileSize > 0): ?>
                   <tr id="repetitions">
                     <td class="entityDetail">Repetitions:</td>
                     <td>
                       <div id="repetitionList"><span style="color:#999999">Repetitions appear after selecting a trial.</span></div>
                       <div id="repetitionDesc"></div>
                     </td>
                   </tr>
                   <?php else: ?>
                   <tr id="repetitions">
                     <td class="entityDetail">Repetitions:</td>
                     <td>
                       <div id="repetitionList"><span style="color:#999999">0 Repetitions found.</span></div>
                       <div id="repetitionDesc"></div>
                     </td>
                   </tr>
                   <?php endif; ?>
                   <tr id="interactive">
                     <td class="entityDetail">Data:</td>
                         <td>
                           <div id="interactiveList"><span style="color:#999999">Data appears after selecting a trial and/or repetition.</span></div>
                           <div id="dataList"></div>
                         </td>
                        </tr>
                        <tr id="photos">
                          <td class="entityDetail">Images:</td>
                          <td>
                            <?php
                              if($this->photoCount > 0):
                            ?>
                          <div id="imageList">Additional photos (<a href="/warehouse/photos/project/<?php echo $oProject->getId(); ?>/experiment/<?php echo $oExperiment->getId(); ?>">view</a>)</div>
                        <?php
                              else:
                            ?>
                              <div id="imageList">Images may be found on the tab labeled 'More'.</div>
                            <?php
                              endif;
                            ?>
                          </td>
                   </tr>
                   <?php
                 }
                ?>

                <tr id="tags">
                  <td class="entityDetail" nowrap="">Tags (related experiments):</td>
                  <td><?php echo $this->mod_warehousetags; ?></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
    </div>
	<!-- close overview_section -->
	
    <div class="clear"></div>
  </div>
  
</div>


