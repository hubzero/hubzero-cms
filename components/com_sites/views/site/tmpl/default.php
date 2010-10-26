<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<?php if($this->allowEdit)
		echo '<a href="' . JRoute::_('index.php?option=com_sites&view=editsite&id=' . $this->facilityID) . '">[Edit]</a>';
	?>

	<div style="padding-bottom: 20px; padding-top:20px;">
		<img src="<?php echo $this->imgFacilityURL; ?>"></img>
	</div>

	<table style="border-width: 0px; width: 800px">
	
		<tr><td class="facility-table-td-label" style="width: 160px;">Host University:</td><td><?php echo $this->FacilityName; ?></td></tr>
	
		<tr><td class="facility-table-td-label">Facility Name:</td><td><?php echo $this->SiteName; ?></td></tr>
	
		<tr><td class="facility-table-td-label">Laboratory:</td><td><?php echo $this->Department; ?></td></tr>
	
		<tr><td class="facility-table-td-label">NSF Award Abstract:</td><td><?php echo $this->NsfAwardUrl; ?></td></tr>
	
		<tr><td class="facility-table-td-label">Site URL:</td><td><?php echo $this->SiteUrl; ?></td></tr>
	
		<tr><td class="facility-table-td-label">NSF Acknowledgement:</td><td><?php echo $this->NsfAcknowledgement; ?></td></tr>
	
	</table>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->introDataFileArr, "Facility Introduction", $this->redirectURL) ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=Facility&subinfo=Site%20Introduction&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>



	<div style="padding-left:10px; padding-top:25px">
		<?php echo FacilityHelper::getViewSimpleFileBrowser($this->descDataFileArr, "Facility Description from NEES O&M proposal", $this->redirectURL) ?>
	</div>

        <?php if($this->allowCreate)
            echo '<a style="padding-left: 0px; margin-left: 10px; float: left;" href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=Facility&subinfo=Site%20Description&redirectURL=' . $this->redirectURL) . '">[Add Document]</a>';
        ?>


	<div style="padding-left:10px; padding-top:25px">
            <?php echo FacilityHelper::getViewSimpleFileBrowser($this->historyDataFileArr, "Site History", $this->redirectURL) ?>
	</div>

        <?php if($this->allowEdit)
            echo '<a style="padding-left: 0px; margin-left: 10px; float: left;" href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=Facility&subinfo=History&redirectURL=' . $this->redirectURL) . '">[Add Document]</a>';
        ?>


</div>

