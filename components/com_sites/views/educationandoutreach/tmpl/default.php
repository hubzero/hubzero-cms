<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2>Education and Outreach</h2>

	<div style="padding-left:10px">
		<?php echo $this->fileBrowserObj->getViewSimpleFileBrowser($this->datafiles, "Education and Outreach Documents") ?>	
	</div>


</div>

