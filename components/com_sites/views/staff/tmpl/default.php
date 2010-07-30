<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent" style="padding-bottom:25px;">
	
	<h2>Staff</h2>
	
	<table style="width:800px; border-width:0 0 0 0;">
            <tr style="background:#bbb;">
                <th nowrap="nowrap">Name</th>
                <th nowrap="nowrap">Roles</th>
                <th nowrap="nowrap">Permissions</th>
                <th nowrap="nowrap">Email</th>
                <th nowrap="nowrap"></th>
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
                    $permissions = isset($this->permissionArr[$personid]) ? $this->permissionArr[$personid] : "&nbsp;";

                    $bgcolor = ($rowcount++%2 == 0) ? "#ffffff" : "#efefef";

                    $prev_personid = $personid;

                    $href = "";

                    //TODO, link to the hub userinfo page?
                    //$userInfo = "<a class='button mini' style='display: inline;' href='$pageUrl&personId=$personid&viewDetail=1'>View Detail</a>";

                    if($this->allowGrant)
                    {
                        $editlink = JRoute::_('/index.php?option=com_sites&view=editcontactrolesandpermissions&id=' . $this->facilityID . '&editpersonid=' . $personid);
                        $deletelink = JRoute::_('/index.php?option=com_sites&task=deletesitemembership&id=' . $this->facilityID . '&editpersonid=' . $personid);
                    }
                    else
                    {
                        $editlink = '';
                        $deletelink = '';
                    }

        ?>
                <tr bgcolor="<?php echo $bgcolor; ?>" id="memberId_<?php echo $personid; ?>">
                  <td><span title="PersonId: <?php echo $personid; ?>"><?php echo $fullname ;?></span></td>
                  <td><?php echo $rolenames; ?></td>
                  <td><?php echo $permissions; ?> </td>
                  <td><a href="mailto: <?php echo $email; ?>"><?php echo $email;?></a></td>

                  <?php if($editlink != ''){ ?>
                    <td><a href="<?php echo $editlink?>">[edit]</a></td>
                  <?php } else {?>
                    <td> </td>
                  <?php } ?>

                  <?php if($deletelink != ''){ ?>
                    <td><a onclick="return confirm('Are you sure you want to delete this user?');" href="<?php echo $deletelink?>">[delete]</a></td>
                  <?php } else {?>
                    <td> </td>
                  <?php } ?>

                </tr>

        <?php
            } // End while
	?>

        </table>




<?php if($this->allowGrant) {?>
    <div style="margin-top:25px;">
        <form method="post">
            <select class="selectbox" name="editpersonid" id="optional">

            <?php
            while($this->candidates->next())
            {
              echo '<option name="editperson" value="' . $this->candidates->get('ID') . '">' .
                      $this->candidates->get("LAST_NAME") . ', '  . $this->candidates->get("FIRST_NAME") .
                      '('  . $this->candidates->get("USER_NAME") . ')</option>';
            }
            ?>

            </select>
            <input class="btn" type="submit" name="FormAction" value="Grant Membership" />
            <input type="hidden" name="task" value="addsitemembership">
            <input type="hidden" name="id" value="<?php echo $this->facilityID?>">

        </form>
    </div>
<?php } ?>

</div>
