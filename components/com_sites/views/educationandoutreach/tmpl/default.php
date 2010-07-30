<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2>Education and Outreach</h2>

	<div style="padding-left:10px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->datafiles, "Education and Outreach Documents", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=EducationOutreach&subinfo=Education%20and%20Outreach&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>


</div>

