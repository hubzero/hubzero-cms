<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<?php if(JRequest::getVar('msg'))
		echo '<p class="passed">' . JRequest::getVar('msg') . '</p>';
	?>	

	<?php if(JRequest::getVar('errorMsg'))
		echo '<p class="failed">' . JRequest::getVar('errorMsg') . '</p>';
	?>	

	<form method="post">    
		<table style="width:550px; border:0px" >
		      <tr><td height="20">&nbsp;</td></tr>
		      <tr>
		        <td class="form" nowrap="nowrap">Host University
		
		        </td>
		        <td>
		          <?php echo $this->facility->getName() ?> (<?php echo $this->facility->getShortName() ?>)
		        </td>
		      </tr>
		
		      <tr>
		        <td nowrap="nowrap">Facility Name <span class="requiredfieldmarker">*</span>
		          
		         </td>
		        <td>
		          <input type="text" class="textentry" style="width:95%;" name="siteName" id="siteName" value="<?php echo $this->facility->getSiteName(); ?>"  />
		        </td>
		      </tr>
		
		      <tr>
		        <td nowrap="nowrap">Department 
		        
		        </td>
		        <td>
		          <input type="text" class="textentry" style="width:95%;" name="department" id="department" value="<?php echo $this->facility->getDepartment(); ?>" />
		        </td>
		      </tr>
		
		      <tr>
		        <td nowrap="nowrap">Laboratory 
		        
		        </td>
		        <td>
		          <input type="text" class="textentry" style="width:95%;" name="laboratory" id="laboratory" value="<?php echo $this->facility->getLaboratory(); ?>" />
		        </td>
		      </tr>
		
		      <tr>
		        <td nowrap="nowrap">Web Site URL 
		               
		        </td>
		        <td>
		          <input type="text" class="textentry" style="width:95%;" name="website_URL" id="website_URL" value="<?php echo $this->facility->getUrl(); ?>" />
		        </td>
		      </tr>
		
		      <tr>
		        <td nowrap="nowrap">Description 
		        
		        </td>
		        <td>
		          <textarea input type="text" class="textentry" style="width:95%;" name="description" id="description" rows="6"><?php echo $this->facility->getDescription(); ?></textarea>
		        </td>
		      </tr>
		
		      <tr>
		        <td nowrap="nowrap">NSF Award URL 
		        
		        </td>
		        <td>
		          <input type="text" class="textentry" style="width:95%;" name="nsfAward_URL" id="nsfAward_URL" value="<?php echo $this->facility->getNsfAwardUrl(); ?>"  />
		        </td>
		      </tr>
		
		      <tr>
		        <td nowrap="nowrap">NSF Acknowledgement 
		        
		        </td>
		        <td>
		          <textarea input type="text" style="width:95%;" name="nsfAcknowledgement" id="nsfAcknowledgement" rows="6"><?php echo $this->facility->getNsfAcknowledgement() ?></textarea>
		        </td>
		      </tr>
		     
		      <tr><td><span class="requiredfieldmarker">*</span> Indicates Required Field</td></tr>

		      <tr><td height="20">&nbsp;</td></tr>
		
		      <tr>
		        <td class="sectheaderbtn" colspan="2">
		          <input class="btn" type="submit" name="submitted" value="Save" />
		          <input class="btn" type="button" value="Cancel" onclick="history.back();" />
		          <input type="hidden" name="task" value="savesite"></input>
		          <input type="hidden" name="id" value="<?php echo $this->facility->getId(); ?>"></input>
		        </td>
		      </tr>
		</table>
	</form>
	 

</div>

