<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h3>Contact Info</h3>

        <hr/>

        <table cellspacing="0" cellpadding="0" style="border: 0px;">

            <tr>
                <td class="facility-table-td-label" nowrap="nowrap">Host University:</td>
                <td width="100%"><?= $this->facility->getName() ?></td>
            </tr>

            <tr>
                <td class="facility-table-td-label" nowrap="nowrap">Facility Name:</td>
                <td><?= $this->facility->getSiteName() ?></td>
            </tr>

            <tr>
                <td class="facility-table-td-label" nowrap="nowrap">Site URL:</td>
                <td><a class="bluelt" href="<?= $this->facility->getUrl() ?>" target="_blank"><?= $this->facility->getUrl() ?></a></td>
            </tr>
        </table>
	
        <h3>Contact Person</h3>

        <hr/>
        <table style="border:0px;width:300px">
                <tr>
	        <td nowrap="nowrap">
                    <b><?= $this->contactName ?></b>
                        <?php
                            if($this->allowedit)
                            echo '<a href="' . JRoute::_('index.php?option=com_sites&id=' . $this->facilityID . '&view=editsitecontact') . '">[change]</a>';
                        ?>
                </td>
	        </tr>
	        <tr>
	          <td nowrap="nowrap" style="padding-left:25px;">Email:</td>
	          <td><a class="bluelt" href="mailto:<?= $this->contactEmail ?>" ><?= $this->contactEmail ?></a></td>
	        </tr>
	        <tr>
	          <td nowrap="nowrap" style="padding-left:25px;">Phone:</td>
	          <td><?= $this->contactPhone ?></td>
	        </tr>
		
        </table>

        <?php echo FacilityHelper::getViewSimpleFileBrowser($this->driving_datafiles, "Driving to the Facility", $this->redirectURL); ?>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=VisitorInformation&subinfo=Driving%20Instruction&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>

	<div style="padding-top:25px">
            <?php echo FacilityHelper::getViewSimpleFileBrowser($this->map_datafiles, "Map of the Facility Location", $this->redirectURL); ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=VisitorInformation&subinfo=Site%20Location%20Map&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>

	<div style="padding-top:25px">
            <?php echo FacilityHelper::getViewSimpleFileBrowser($this->local_datafiles, "Local Area Information (transportation, lodging, etc.)",$this->redirectURL); ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=VisitorInformation&subinfo=Local%20Area%20Information&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>

	<div style="padding-top:25px">
            <?php echo FacilityHelper::getViewSimpleFileBrowser($this->additional_datafiles, "Additional Documents", $this->redirectURL); ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=VisitorInformation&subinfo=Additional%20Document&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>
	
</div>