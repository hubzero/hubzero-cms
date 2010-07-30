<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2>Contact Info</h2>

	<div style="border: thin solid black; width: 800px; padding-top:10px; padding-bottom:10px">
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
	
	        <tr>
	        	<td colspan="2">&nbsp;</td>
	        </tr>
	
	
	       <tr>
	          <td class="facility-table-td-label" nowrap="nowrap">Contact Person:</td>
	          <td><?= $this->facilityContactPerson->getFirstName()." ".$this->facilityContactPerson->getLastName() ?></td>
	        </tr>
	        <tr>
	          <td nowrap="nowrap">&nbsp;&nbsp;&nbsp;Email:</td>
	          <td><a class="bluelt" href="mailto:<?= $this->facilityContactPerson->getEMail() ?>" ><?= $this->facilityContactPerson->getEMail() ?></a></td>
	        </tr>
	        <tr>
	          <td nowrap="nowrap">&nbsp;&nbsp;&nbsp;Phone:</td>
	          <td><?= $this->facilityContactPerson->getPhone() ?></td>
	        </tr>
	        <tr>
	          <td nowrap="nowrap">&nbsp;&nbsp;&nbsp;Fax:</td>
	          <td><?= $this->facilityContactPerson->getFax() ?></td>
	        </tr>
	        <tr>
	          <td nowrap="nowrap">&nbsp;&nbsp;&nbsp;Mailing Address:</td>
	          <td><?= str_replace("\r\n","<br />",$this->facilityContactPerson->getAddress()) ?></td>
	        </tr>
		
		</table>
	</div>

	<div style="padding-left:10px;">
            <?php echo FacilityHelper::getViewSimpleFileBrowser($this->driving_datafiles, "Driving to the Facility", $this->redirectURL); ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=VisitorInformation&subinfo=Driving%20Instruction&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>

	<div style="padding-left:10px; padding-top:25px">
            <?php echo FacilityHelper::getViewSimpleFileBrowser($this->map_datafiles, "Map of the Facility Location", $this->redirectURL); ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=VisitorInformation&subinfo=Site%20Location%20Map&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>

	<div style="padding-left:10px; padding-top:25px">
            <?php echo FacilityHelper::getViewSimpleFileBrowser($this->local_datafiles, "Local Area Information (transportation, lodging, etc.)",$this->redirectURL); ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=VisitorInformation&subinfo=Local%20Area%20Information&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>

	<div style="padding-left:10px; padding-top:25px">
            <?php echo FacilityHelper::getViewSimpleFileBrowser($this->additional_datafiles, "Additional Documents", $this->redirectURL); ?>
	</div>
        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; margin-left: 10px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&infotype=VisitorInformation&subinfo=Additional%20Document&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>
	
	
	
	
	
	
		
</div>