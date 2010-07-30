<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2>Add Facility Data File</h2>

	<?php if(JRequest::getVar('msg'))
		echo '<p class="passed">' . JRequest::getVar('msg') . '</p>';
	?>	

	<?php if(JRequest::getVar('errorMsg'))
		echo '<p class="failed">' . JRequest::getVar('errorMsg') . '</p>';
	?>	

        <form method="post" enctype="multipart/form-data">

            <table cellspacing="0" cellpadding="0" style="border:1px solid #CCCCCC;">

                <tr>
                  <td nowrap="nowrap">Brief file description</td>
                  <td width="100%"><?= $this->infotype?> : <?= $this->subinfo ?> <?php  if($this->groupby) {echo  ": ". $this->groupby;} ?></td>
                </tr>

                    <tr>
                            <td nowrap="nowrap">Document to Upload</td>
                            <td><input type="file" size="40" name="documentFile"/></td>
                    </tr>

                    <tr>
                            <td nowrap="nowrap">Document Title:</td>
                            <td><input type="text" style="width:95%" name="documentTitle" maxlength="100" /></td>
                    </tr>

                    <tr>
                            <td nowrap="nowrap">Document Description: </td>
                            <td><textarea name="documentDesc" row="5" style="width:95%"></textarea></td>
                    </tr>
                    <tr><td colspan="2" height="20">&nbsp;</td></tr>
                    <tr>
                            <td colspan="2" class="sectheaderbtn">
                                    <input class="btn" type="button" value="Cancel" onclick="history.back();" />
                                    <input class="btn" type="submit" name="submit" value="Save Changes"  />
                                    <input type="hidden" name="id" value="<?php echo $this->facilityID; ?>"></input>
                                    <input type="hidden" name="task" value="savefile"></input>
                                    <input type="hidden" name="infotype" value="<?php echo $this->infotype; ?>"></input>
                                    <input type="hidden" name="subinfo" value="<?php echo $this->subinfo; ?>"></input>
                                    <input type="hidden" name="groupby" value="<?php echo $this->groupby; ?>"></input>
                                    <input type="hidden" name="redirectURL" value="<?php echo $this->redirectURL; ?>"></input>
                            </td>
                    </tr>

            </table>
        </form>
	 

</div>

