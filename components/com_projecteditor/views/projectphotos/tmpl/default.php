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
  $oProject = unserialize($_REQUEST[ProjectPeer::TABLE_NAME]);

  //$strAction = "/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiment/".$oExperiment->getId()."/photos";
?>

<form id="frmProject" name="frmProject" action="<?php //echo $strAction; ?>" method="get">
<input type="hidden" name="projid" value="<?php echo $this->iProjectId; ?>" />

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
      <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
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
            $strProjectDisplay = "/warehouse/project/".$oProject->getId();
          ?>
          <p class="edit"><a href="<?php echo $strProjectDisplay; ?>">View Project</a></p>
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

        <p class="experimentTitle"><?php //echo $oExperiment->getTitle(); ?></p>

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
            <tr id="Photos">
              <td nowrap>
                <p class="editorParagraph">
                  <label class="editorLabel">Photos:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Photos :: Please share images (png, jpg, or gif).  Choose where to display the photos.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
                <p><a title="Formats: PNG, JPG, or GIF" class="modal" href="/warehouse/projecteditor/uploadform?format=ajax&projid=<?php echo $this->iProjectId; ?>&path=<?php echo $this->strPath; ?>&uploadType=<?php echo $this->uploadType; ?>">Upload Photos</a></p>
              </td>
              <td width="100%">
                <table cellpadding="1" cellspacing="1">
                    <thead>
                      <th width="1"><input id="checkAll" type="checkbox" name="checkAll" onClick="setAllCheckBoxes('frmProject', 'dataFile[]', this.checked, <?php echo $this->iProject; ?>);"/></th>
                      <th>Title</th>
                      <th>Description</th>
                      <th>Type</th>
                      <th>Manage</th>
                    </thead>
                    <?php
                      $oDataFileArray =  unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
                      /* @var $oDataFile DataFile */
                      foreach($oDataFileArray as $iPhotoIndex=>$oDataFile){
                        $strBgColor = "odd";
                        if($iPhotoIndex%2 === 0){
                          $strBgColor = "even";
                        }

                        $strOriginalPhotoName = $oDataFile->getName();
                        $strPhotoFriendlyPath = $oDataFile->getFriendlyPath();
                        $strPhotoFriendlyPath = str_replace("/".Files::GENERATED_PICS, "", $strPhotoFriendlyPath);
                        $iDataFileId = $oDataFile->getId();
                        $strPhotoName = "display_".$iDataFileId."_".$strOriginalPhotoName;
                        $oDataFile->setName($strPhotoName);

                        $strPhotoPath = $oDataFile->getPath();
                        if(!StringHelper::endsWith($strPhotoPath, Files::GENERATED_PICS)){
                          $strPhotoPath = $oDataFile->getPath()."/".Files::GENERATED_PICS;
                        }
                        $oDataFile->setPath($strPhotoPath);
                        $strPhotoUrl = $oDataFile->get_url();

                        $strFileTitle = (StringHelper::hasText($oDataFile->getTitle())) ? $oDataFile->getTitle() : $strOriginalPhotoName;

                      ?>
                        <tr class="<?php echo $strBgColor; ?>">
                          <td width="1"><input id="<?php echo $this->iProjectId; ?>" type="checkbox" name="dataFile[]" value="<?php echo $iDataFileId ?>"/></td>
                          <td nowrap><a rel="lightbox[Photos]" title="<?php echo $strPhotoFriendlyPath; ?>" href="<?php echo $strPhotoUrl; ?>"><?php echo $strFileTitle; ?></a></td>
                          <td><?php echo $oDataFile->getDescription(); ?></td>
                          <td nowrap>
                            <?php
                              /* @var $oEntityType EntityType */
                              $oEntityType = $oDataFile->getEntityType();
                              if($oEntityType){
                                $strPhotoType = "";
                                $strType = $oDataFile->getEntityType()->getDatabaseTableName();
                                if($strType){
                                  $strTypeArray = explode("-", $strType);
                                  $strPhotoType = (sizeof($strTypeArray)==2) ? $strTypeArray[1] : $strType;
                                }
                                echo $strPhotoType;
                              }
                            ?>
                          </td>
                          <td nowrap>[<a class="modal" href="/warehouse/projecteditor/editphoto?format=ajax&projectId=<?php echo $this->iProjectId; ?>&dataFileId=<?php echo $oDataFile->getId(); ?>&photoType=<?php echo $this->iPhotoType; ?>">Edit</a>]&nbsp&nbsp;<!--[Delete]--></td>
                        </tr>
                      <?php
                      }
                    ?>
                  </table>

                  <?php #form buttons ?>
                  <table style="border:0px;">
                    <tr>
                        <td>
                          <!--
                          <div id="filmstrip" class="editorInputFloat editorInputMargin">
                            <a title="Select png, jpg, or gif photos for the experiment filmstrip." href="javascript:void(0);" onClick="document.getElementById('frmProject').action='/warehouse/projecteditor/savefilmstrip';document.getElementById('frmProject').submit();" style="border:0px">
                              <img src="/components/com_projecteditor/images/buttons/FilmstripPhoto.png" border="0" alt="Upload png, jpg, gif to experiment filmstip."/>
                            </a>
                          </div>

                          <div id="general" class="editorInputFloat editorInputMargin">
                            <a title="Select general png, jpg, or gif photos for the project More tab." href="javascript:void(0);" onClick="document.getElementById('frmProject').action='/warehouse/projecteditor/savemorephotos';document.getElementById('frmProject').submit();" style="border:0px">
                              <img src="/components/com_projecteditor/images/buttons/MoreTabPhoto.png" border="0" alt="Upload png, jpg, gif to More tab."/>
                            </a>
                          </div>
                          -->
                          <div class="clear"></div>
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
