<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent" style="padding-bottom:25px;">
	
	<h2>Staff</h2>
	
	<table style="width:700px; border-width:0 0 0 0;">
		<tr style="background:#bbb;">
			<th nowrap="nowrap">Name</th>
			<th nowrap="nowrap">Roles</th>
			<th nowrap="nowrap">Email</th>
			<th nowrap="nowrap"></th>
		</tr>
		
	<?php 
	    $rowcount = 0; 
	
		while($this->members->next()) {
			$personid    = $this->members->get("PERSON_ID");
			$rolenamesArr = RolePeer::listRolesForPersonInEntity($this->facility, $personid);
			$rolenames = implode("<br/>", $rolenamesArr);
	
			$lastname = $this->members->get("LAST_NAME");
			$firstname = $this->members->get("FIRST_NAME");
			$fullname = $lastname . ", " . $firstname;
			$firstlast = htmlspecialchars($firstname . " " . $lastname);
			$email = $this->members->get("E_MAIL");
			$permissions = isset($permissionArr[$personid]) ? $permissionArr[$personid] : "&nbsp;";
	
			$bgcolor = ($rowcount++%2 == 0) ? "#ffffff" : "#efefef";
	
			$prev_personid = $personid;
	
			$href = "";
			//$userInfo = "<a class='button mini' style='display: inline;' href='$pageUrl&personId=$personid&viewDetail=1'>View Detail</a>";
			
			$editlink = JRoute::_('/index.php?option=com_sites&view=editcontactrolesandpermissions&id=' . $this->facilityID . '&editpersonid=' . $personid);   
			
	?>
		    <tr bgcolor="<?php echo $bgcolor; ?>" id="memberId_<?php echo $personid; ?>">
		      <td><span title="PersonId: <?php echo $personid; ?>"><?php echo $fullname ;?></span></td>
		      <td><?php echo $rolenames; ?></td>
		      <td><a href="mailto: <?php echo $email; ?>"><?php echo $email;?></a></td>
		      <td><a href="<?php echo $editlink?>">[edit]</a></td>
		    </tr>
	
	<?php
		} // End while
	?>

</div>










	
	
</table>