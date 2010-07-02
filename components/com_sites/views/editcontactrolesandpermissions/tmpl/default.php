<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2>Roles and Permissions for <?php echo $this->editPerson->getFirstName() . " " . $this->editPerson->getLastName(); ?></h2>
	
	
	<form method="post">
		<div style="width:800px;">

			<?php if(JRequest::getVar('msg'))
				echo '<p class="passed">' . JRequest::getVar('msg') . '</p>';
			?>	

			<?php if(JRequest::getVar('errorMsg'))
				echo '<p class="failed">' . JRequest::getVar('errorMsg') . '</p>';
			?>	

			<h3 style="padding-bottom:10px;"> Roles </h3>
	
			<?php
				$num_col = 3;
				$rolesCount = count($this->roles);
				$itemPerRow = ceil($rolesCount / $num_col);
		
				echo "<table style=\"border:0px; width:700px;\n\">";
				
				for ($i=0; $i<$rolesCount; $i++) 
				{
					$role = $this->roles[$i];
					$roleId = $role->getId();
					
					// At the end of a row?
					if($i % $num_col == 0)
					{
						// Forget the </tr> for the first item in the first row
						if($i != 0) echo '</tr>';
	
						// Forget the <tr> if this item is the last in a row, and the last item overall
						if($i+1 <= $rolesCount) echo '<tr>';
					}	
			?>
					<td>
						<input type="checkbox" <?php echo $this->hasRole($this->editPerson, $role, $this->facility) ? "checked" : "" ?> name="roleIds[]" id="roleid_<?php echo $roleId;?>" value="<?php echo $roleId; ?>">
							<?php echo $role->getName();?>
						</input>
					</td>
			<?php
		        }
				echo "</table>\n";
		    ?>
		
			<h3 style="padding-bottom:10px;">Permissions</h3>
		
			<div style="padding-left:10px; padding-bottom:25px;">
				<input type="checkbox" checked disabled="disabled">View</input>&nbsp;&nbsp;&nbsp;
				<input type="hidden" name="canView" id="canView" value="checked"></input>
				<input type="checkbox" name="canCreate" id="canCreate" <?= $this->auth->personCanDo($this->facility, 'Create', $this->editPersonID) ? 'checked' : ''?> >Create</input>&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="canEdit" id="canEdit" <?= $this->auth->personCanDo($this->facility, 'Edit', $this->editPersonID) ? 'checked' : ''?> >Edit</input>&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="canDelete" id="canDelete" <?= $this->auth->personCanDo($this->facility, 'Delete', $this->editPersonID) ? 'checked' : ''?> >Delete</input>&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="canGrant" id="canGrant" <?= $this->auth->personCanDo($this->facility, 'Grant', $this->editPersonID) ? 'checked' : ''?>>Grant</input>
			</div>
			
			<div class="editpage-buttons-section">
				<input name="submitted" type="submit" value="Save"/>
				<input type="button" value="Cancel" onclick="javaScript:history.go(-1);"/>
			</div>
	
		</div>

		<input type="hidden" name="editPersonID" value="<?php echo $this->editPersonID; ?>"></input>
		<input type="hidden" name="option" value="com_sites" />
		<input type="hidden" name="task" value="savecontactrolesandpermissions" />
		<input type="hidden" name="facilityID" value="<?php echo $this->facilityID;?>" />

	</form>
	
		
</div>