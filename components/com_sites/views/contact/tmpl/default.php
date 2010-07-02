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
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->driving_datafiles, "Driving to the Facility") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->map_datafiles, "Map of the Facility Location") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->local_datafiles, "Local Area Information (transportation, lodging, etc.)") ?>	
	</div>

	<div style="padding-left:10px; padding-top:25px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->additional_datafiles, "Additional Documents") ?>	
	</div>
	
	
	
	
	
	
		
</div>