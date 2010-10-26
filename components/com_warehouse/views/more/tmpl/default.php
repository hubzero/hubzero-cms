<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
?>

<?php
  $strUsername = $this->strUsername;
  $oAuthorizer = Authorizer::getInstance();
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>

<form id="frmPhotos" method="get">
<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="treeBrowser" style="float:left;width:20%;"></div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <div id="title" style="padding-bottom:1em;">
        <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
      </div>
  
      <?php echo TabHtml::getSearchFormWithAction( "frmPhotos", "/warehouse/find" ); ?>
      <?php echo $this->strTabs; ?>
      
      <div class="withleft">
        <div class="aside">
          <ul>
            <li class="active"><a href="/warehouse/more/<?php echo $oProject->getId(); ?>" class="sent"><span>Images</span></a></li>
            <li><a href="/warehouse/moreanalysis/<?php echo $oProject->getId(); ?>" class="new-message"><span>Analysis</span></a></li>
            <li><a href="/warehouse/moredocs/<?php echo $oProject->getId(); ?>" class="new-message"><span>Documents</span></a></li>
          </ul>
        </div><!-- / .aside -->
        <div class="subject">
          <?php if($oAuthorizer->canView($oProject)){ ?>
          <div id="images" style="padding-top:1em;">
            <table style="width:100%;border-top:0px;border-bottom:0px;">
              <tr>
              <?php
                $oPhotoDataFileArray = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
                foreach($oPhotoDataFileArray as $iFileIndex=>$oPhotoDataFile){
                  $iPhotoCounter = $iFileIndex + 1;
                ?>
                  <td align="center" style="float:left; height:100px; width:20%; margin-bottom:50px;"><?php echo $oPhotoDataFile["THUMBNAIL"]; ?></td>
              <?php
                  if($iFileIndex>0 && $iPhotoCounter%4===0){
                  ?>
                    </tr>
                  <?php
                    if($iFileIndex < sizeof($oPhotoDataFileArray)){
                      ?>
                        <tr>
                      <?php
                    }
                  }
                    }

                    if(empty($oPhotoDataFileArray)){?>
                            <p class="warning">No images found.</p>
                    <?php }
              ?>
              </tr>
            </table>
          
          <?php echo $this->pagination; ?>
          
        </div>
            <?php
        }else{?>
          <p class="error" style="margin-top:10px;">You don't have permission to view this project.</p>
        <?php
        }//end canView
      ?>
		</div><!-- / .subject -->
	</div>
      
    </div>
    <div class="clear"></div>
  </div>  
</div>
</form>


