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
  $document->addScript($this->baseurl."/components/com_projecteditor/js/general.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php JHTML::_('behavior.modal'); ?>

<?php
  $oUser = $this->oUser;
  $oExperiment = unserialize($_SESSION[ExperimentPeer::TABLE_NAME]);

  $strAction = "/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiment/".$oExperiment->getId()."/drawings";

  $oAuthorizer = Authorizer::getInstance();
?>

<form id="frmProject" name="frmProject" action="<?php echo $strAction; ?>" method="post">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="projid" value="<?php echo $this->iProjectId; ?>" />
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>" />
<input type="hidden" id="return" name="return" value="<?php echo $this->strReturnUrl; ?>" />

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>

  <div id="quickstart">
    <div id="pdfIcon" class="editorInputFloat">
      <img src="/components/com_projecteditor/images/icons/pdf.jpg"/>&nbsp;&nbsp;
    </div>
    <div id="helpdoc" class="editorInputFloat">
      <a href="<?php echo ProjectEditor::QUICK_START_GUIDE?>" target="peQuickStart">Quick Start Guide</a>
    </div>
    <div class="clear"></div>
  </div>

  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;"><?php echo $oExperiment->getProject()->getTitle(); ?></span>
    </div>

    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php echo $this->strTabs; ?>

      <div class="aside">
        <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
          <p style="margin-left:10px; margin-top:10px;"><?php echo $this->iEntityActivityLogViews; ?> Views</p>
          <p style="margin-left:10px;"><?php echo $this->iEntityActivityLogDownloads; ?> Downloads</p>
        </div>

        <div id="editEntity" class="admin-options" style="margin-top:30px">
          <?php
            $strProjectDisplay = "/warehouse/experiment/".$oExperiment->getId()."/project/".$oExperiment->getProjectId();
          ?>
          <p class="edit"><a href="<?php echo $strProjectDisplay; ?>">View Experiment</a></p>
        </div>

        <div id="curation">
          <span class="curationTitle">Curation in progress:</span>
          <?php if(StringHelper::hasText($this->mod_curationprogress)){ ?>
            <p><?php echo $this->mod_curationprogress; ?></p>
          <?php }else{ ?>
            <p>No curation yet.</p>
          <?php } ?>
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

        <p class="experimentTitle"><?php echo $oExperiment->getTitle(); ?></p>

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
            <tr id="drawings">
              <td nowrap>
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Drawings:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Drawings :: Please provide images (png, jpg, or gif) that represent your setup.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
                <!--<p><a title="Formats: PNG, JPG, or GIF" class="modal" href="/warehouse/projecteditor/editdrawing?format=ajax&projectId=<?php //echo $this->iProjectId; ?>&experimentId=<?php echo $this->iExperimentId; ?>">Upload Drawing</a></p>-->
                <p><a title="Formats: PNG, JPG, or GIF" class="modal" href="/warehouse/projecteditor/uploadform?format=ajax&projid=<?php echo $this->iProjectId; ?>&experimentId=<?php echo $this->iExperimentId; ?>&path=<?php echo $this->path; ?>&uploadType=<?php echo $this->requestType; ?>">Upload Drawing</a></p>
              </td>
              <td width="100%">
                <table cellpadding="1" cellspacing="1">
                    <thead>
                      <th width="1">
                        <input id="checkAll" type="checkbox" name="checkAll" onClick="setAllCheckBoxes('frmProject', 'dataFile[]', this.checked, <?php echo $this->iExperimentId; ?>);setFilesToDelete('frmProject', 'dataFile[]', 'cbxDelete', <?php echo $this->iExperimentId; ?>, 'fileDeleteLink', 112);"/>
                        <input type="hidden" id="cbxDelete" name="deleteFiles" value=""/>
                      </th>
                      <th>Title</th>
                      <th>Description</th>
                      <th>Type</th>
                      <th>Manage</th>
                    </thead>
                    <?php
                      $oDrawingArray =  unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
                      /* @var $oDrawing DataFile */
                      foreach($oDrawingArray as $iDrawingIndex=>$oDrawing){
                        //$strBgColor = "";
                        $strBgColor = "odd";
                        if($iDrawingIndex%2 === 0){
                          //$strBgColor = "#EFEFEF";
                          $strBgColor = "even";
                        }

                        $strDrawingFriendlyPath = $oDrawing->getFriendlyPath();

                        $strOriginalPhotoName = $oDrawing->getName();
                        $strDrawingName = "display_".$oDrawing->getId()."_".$strOriginalPhotoName;
                        $oDrawing->setName($strDrawingName);

                        $strOriginalPhotoPath = $oDrawing->getPath();
                        $strDrawingPath = $strOriginalPhotoPath."/".Files::GENERATED_PICS;
                        $oDrawing->setPath($strDrawingPath);
                        $strDrawingUrl = $oDrawing->getUrl();
                      ?>
                        <tr class="<?php echo $strBgColor; ?>">
                          <td width="1"><input id="<?php echo $this->iExperimentId; ?>" type="checkbox" name="dataFile[]" value="<?php echo $oDrawing->getId(); ?>" onClick="setFilesToDelete('frmProject', 'dataFile[]', 'cbxDelete', <?php echo $this->iExperimentId; ?>, 'fileDeleteLink', 112);"/></td>
                          <td><a rel="lightbox[drawings]" title="<?php echo $strDrawingFriendlyPath; ?>" href="<?php echo $strDrawingUrl; ?>" title=""><?php echo $oDrawing->getTitle(); ?></a></td>
                          <td><?php echo $oDrawing->getDescription(); ?></td>
                          <td nowrap>
                            <?php
                              $strDrawingType = "";
                              $strType = $oDrawing->getEntityType()->getDatabaseTableName();
                              if($strType){
                                $strTypeArray = explode("-", $strType);
                                $strDrawingType = (sizeof($strTypeArray)==2) ? $strTypeArray[1] : $strType;
                              }
                              echo $strDrawingType;
                            ?>
                          </td>
                          <td nowrap>
                            [<a class="modal" href="/warehouse/projecteditor/editdrawing?format=ajax&projectId=<?php echo $this->iProjectId; ?>&experimentId=<?php echo $this->iExperimentId; ?>&dataFileId=<?php echo $oDrawing->getId(); ?>&return=<?php echo $this->strReturnUrl; ?>&index=<?php echo $this->iPageIndex; ?>&display=<?php echo $this->iDisplay; ?>">Edit</a>]&nbsp&nbsp;
                            <?php if($oAuthorizer->canDelete($oExperiment)){ ?>
                              [<a class="modal" href="/warehouse/projecteditor/delete?path=<?php echo $strOriginalPhotoPath; ?>&format=ajax&eid=<?php echo $oDrawing->getId(); ?>&etid=112&return=<?php echo $this->strReturnUrl; ?>" title="Remove <?php echo $strOriginalPhotoName; ?>">Delete</a>]
                            <?php } ?>
                          </td>
                        </tr>
                      <?php
                      }
                    ?>
                  </table>

                  <?php #form buttons ?>
                  <table style="border:0px;">
                    <tr>
                      <td>
                        <div class="sectheaderbtn">
                          <?php
                            $bCanDelete = $oAuthorizer->canDelete($oExperiment);
                            if($bCanDelete){?>
                              <a id="fileDeleteLink" title="Delete the selected file(s)"
                                 tabindex="" href="/warehouse/projecteditor/delete?format=ajax" class="button2 modal">Delete</a>
                            <?php
                            }
                          ?>
                        </div>
                      </td>
                    </tr>
                  </table>

                  <?php echo $this->pagination; ?>
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
