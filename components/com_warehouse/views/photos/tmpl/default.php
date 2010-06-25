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

<form id="frmPhotos" method="get"> 
<div class="innerwrap>
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
		    <p style="margin-left:10px; margin-top:10px;">1000 Views</p>
		          
		    <p style="margin-left:10px;">100 Downloads</p>    
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
		      <?php echo $oExperiment->getName() .": ". $oExperiment->getTitle(); ?>
		    </div>
		    
		    <div id="experimentInfo">
		      <p>Experiment Photos</p>
		      <p><a href="/warehouse/experiment/<?php echo $this->experimentId; ?>/project/<?php echo $this->projectId; ?>">Return</a></p>

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
			  ?>
			  </tr>
			  </table>
			  
			  <?php 
		    	echo $this->pagination;
		      ?>  
		    </div>
		  </div>
		</div>  
    </div>
	<!-- close overview_section -->
	
    <div class="clear"></div>
  </div>
  
</div>
</form>

