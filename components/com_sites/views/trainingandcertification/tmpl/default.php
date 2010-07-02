<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2>Training and Certification</h2>
	
	<div style="padding-left:10px;">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->onSiteTrainingDFs, "Training Programs for On-Site Researchers") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->remoteTrainingDFs, "Training Programs for Remote Participants") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->trainingDFs, "Training Documents") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->safetyDFs, "Safety policies and requirements (from NEES O&M proposals)") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->onSiteProceduresDFs, "On Site Procedures") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->proposalPreparationDFs, "Proposal Preparation") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->additionalDocumentsDFs, "Additional Documents") ?>	
	</div>


</div>

