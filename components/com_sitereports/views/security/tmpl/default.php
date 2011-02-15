<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<?php echo $this->tabs; ?>

<div class="siteReportsMainPageConent">


<?php
if($this->allowPageView)
{
?>
    
    <div style="padding-bottom:25px;">
        <form method="get" name="facilityChooserForm" id="facilityChooserForm">
            <input type="hidden" name="option" value="com_sitereports">

            <select name="facilityid" onchange="this.form.submit();" style="margin-top:20px;">
            <?php
            /* @var $site SiteReportsSite */
                foreach ($this->allSiteReportSites as $site)
                {
                    $id = $site->getOrganization()->getId();
                    $selectedText = ($id == $this->facilityid) ? ' selected ' : '';
                    echo '<option value="'. $id .'"' . $selectedText . '>' . $site->getOrganization()->getName() . '</option>';
                }
            ?>
            </select>
            <input type="hidden" name="view" value="security">
        </form>
    </div>

    <?php
    if($this->grantCurrentSite)
    {
    ?>

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

            while ($this->members->next())
            {
                $personid = $this->members->get("PERSON_ID");
                $rolenamesArr = RolePeer::listRolesForPersonInEntity($this->sitereportsite, $personid);
                $rolenames = implode("<br/>", $rolenamesArr);

                $lastname = $this->members->get("LAST_NAME");
                $firstname = $this->members->get("FIRST_NAME");
                $fullname = $lastname . ", " . $firstname;
                $firstlast = htmlspecialchars($firstname . " " . $lastname);
                $email = $this->members->get("E_MAIL");
                $permissions = isset($this->permissionArr[$personid]) ? $this->permissionArr[$personid] : "&nbsp;";

                $bgcolor = ($rowcount++ % 2 == 0) ? "#ffffff" : "#efefef";

                $prev_personid = $personid;

                $href = "";

                //TODO, link to the hub userinfo page?
                //$userInfo = "<a class='button mini' style='display: inline;' href='$pageUrl&personId=$personid&viewDetail=1'>View Detail</a>";

                if (true) {
                    $editlink = ''; //JRoute::_('/index.php?option=com_sitereports&task=edit&editpersonid=' . $personid & '&facilityid=' . $this->facilityid);
                    $deletelink = JRoute::_('/index.php?option=com_sitereports&task=deletesitemembership&editpersonid=' . $personid . '&facilityid=' . $this->facilityid);
                } else {
                    $editlink = '';
                    $deletelink = '';
                }
            ?>
                <tr bgcolor="<?php echo $bgcolor; ?>" id="memberId_<?php echo $personid; ?>">
                    <td><span title="PersonId: <?php echo $personid; ?>"><?php echo $fullname; ?></span></td>
                    <td><?php echo $rolenames; ?></td>
                    <td><?php echo $permissions; ?> </td>
                    <td><a href="mailto: <?php echo $email; ?>"><?php echo $email; ?></a></td>

                    <?php if ($editlink != '') { ?>
                        <td><a href="<?php echo $editlink ?>">[edit]</a></td>
                    <?php } else { ?>
                        <td> </td>
                    <?php } ?>

                    <?php if ($deletelink != '') { ?>
                        <td><a onclick="return confirm('Are you sure you want to remove this user?');" href="<?php echo $deletelink ?>">[delete]</a></td>
                    <?php } else { ?>
                        <td> </td>
                    <?php } ?>
                        </tr>

            <?php
            } // End while

            if ($rowcount == 0)
            {
                echo '<tr><td colspan=6>No users assigned</td></tr>';
            }

            ?>

        </table>




        <div style="padding-top:25px">
            <form method="post" name="usrAddForm" id="userAddForm">
                <select class="selectbox" name="editpersonid">

                <?php
                    while ($this->candidates->next()) {
                        echo '<option name="editperson" value="' . $this->candidates->get('ID') . '">' .
                        $this->candidates->get("LAST_NAME") . ', ' . $this->candidates->get("FIRST_NAME") .
                        '(' . $this->candidates->get("USER_NAME") . ')</option>';
                    }
                ?>

                </select>
                <input class="btn" type="submit" name="FormAction" value="Grant Membership" />
                <input type="hidden" name="option" value="com_sitereports"
                <input type="hidden" name="task" value="addsitemembership">
                <input type="hidden" name="id" value="<?php echo $this->facilityID ?>">
            </form>
        </div>

    <?php
    }
    else
    {
        echo 'Access denied to selected site';
    }
    ?>


<?php
}
else
{
    // Access denied to entire page, user has no rights to grant rights to ANY site in the system
    echo 'Access denied';
}
?>



</div>
