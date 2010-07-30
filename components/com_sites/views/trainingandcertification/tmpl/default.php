<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2>Training and Certification</h2>
	
	<div style="padding-left:10px;">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->onSiteTrainingDFs, "Training Programs for On-Site Researchers", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=TrainingAndCertification&subinfo=On%20Site%20Training&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>


	<div style="padding-left:10px; padding-top:25px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->remoteTrainingDFs, "Training Programs for Remote Participants", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=TrainingAndCertification&subinfo=Remote%20Training&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>


	<div style="padding-left:10px; padding-top:25px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->trainingDFs, "Training Documents", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=TrainingAndCertification&subinfo=Training&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>


	<div style="padding-left:10px; padding-top:25px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->safetyDFs, "Safety policies and requirements (from NEES O&M proposals)", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=TrainingAndCertification&subinfo=Training&groupby=Safety%20Policy&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>


	<div style="padding-left:10px; padding-top:25px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->onSiteProceduresDFs, "On Site Procedures", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=TrainingAndCertification&subinfo=On%20Site%20Procedures&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>


	<div style="padding-left:10px; padding-top:25px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->proposalPreparationDFs, "Proposal Preparation", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=TrainingAndCertification&subinfo=Proposal%20Preparation&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>


	<div style="padding-left:10px; padding-top:25px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->additionalDocumentsDFs, "Additional Documents", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=TrainingAndCertification&subinfo=Additional%20Documents&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>



</div>

