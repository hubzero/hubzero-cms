<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
?>

<?php
 /* @var $oPerson Person */
 $oPerson = unserialize($_REQUEST[PersonPeer::TABLE_NAME]);
 
 /* @var $oProject Project */
 $oProject = unserialize($_REQUEST[ProjectPeer::TABLE_NAME]);

 $oRoleArray = unserialize($_REQUEST[RolePeer::TABLE_NAME]);

 $strPermissionsTarget = $_SESSION["PERMISSIONS_TARGET_ID"];
?>

<td class="photo" width="60"><img width="50" height="50" alt="Photo for <?php echo ucfirst($oPerson->getFirstName()) ." ". ucfirst($oPerson->getLastName()); ?>" src="/components/com_members/images/profile_thumb.gif"></td>
<?php if($_REQUEST['LINK']){ ?>
  <td><span class="name"><a href="/members/<?php echo $_REQUEST["ID"]; ?>"><?php echo ucfirst($oPerson->getLastName()) .", ". ucfirst($oPerson->getFirstName()); ?></a></span></td>
<?php }else{ ?>
  <td><span class="name"><?php echo ucfirst($oPerson->getLastName()) .", ". ucfirst($oPerson->getFirstName()); ?></span></td>
<?php } ?>
<td style="padding: 0;" id="selectRole">
  <table style="border:0;">
    <tr>
      <td width="1">
          <?php
            //$iPersonId = $_REQUEST["ID"];
            $iPersonId = $oPerson->getId();
            $iProjectId = $oProject->getId();
            $strOnChange="getMootools('/warehouse/projecteditor/permissions?format=ajax&personId=$iPersonId&projectId=$iProjectId&roleId='+this.value, '$strPermissionsTarget');"
          ?>
          <input type="hidden" name="personId" value="<?php echo $iPersonId; ?>"/>
          <select name="role" id="cboRole" onChange="<?php echo $strOnChange; ?>">
            <option value="0" selected>-Select Role-</option>
          <?php
            /* @var $oRole Role */
            foreach($oRoleArray as $oRole){
              if($oRole->getDisplayName() != "NEEShub User"){
                ?>
              <option value="<?php echo $oRole->getId(); ?>"><?php echo $oRole->getDisplayName(); ?></option>
            <?php
              }
            }
          ?>
        </select>
      </td>
      <td>
        <?php
          $strOnClick = "getMootools('/warehouse/projecteditor/editrole?format=ajax&action=add&personId=$iPersonId&projectId=$iProjectId&roleId='+document.getElementById('cboRole').value, 'selectRole');";
        ?>
        <a href="javascript:void(0);"
           title="Add another role"
           style="border-bottom: 0px"
           onClick="<?php echo $strOnClick; ?>">
            <img alt="" src="/components/com_projecteditor/images/icons/addButton.png" border="0"/>
        </a>
      </td>
    </tr>

    <?php
      //$oCurrentRoleArray = unserialize($_REQUEST["ROLE_ARRAY"]);
      $oCurrentRoleArray = unserialize($_SESSION["USER_ROLES"]);

      /* @var $oCurrentRole Role */
      foreach($oCurrentRoleArray as $iCurrentIndex=>$oCurrentRole){
        $iCurrentRoleId = $oCurrentRole->getId();
        ?>
        <tr id="currentRole-<?php echo $iCurrentIndex; ?>">
          <td><?php echo $oCurrentRole->getDisplayName(); ?></td>
          <td>
            <?php
              $strOnClick = "getMootools('/warehouse/projecteditor/editrole?format=ajax&action=remove&index=$iCurrentIndex&personId=$iPersonId&projectId=$iProjectId&roleId=$iCurrentRoleId', 'selectRole');";
            ?>

            <a href="javascript:void(0);"
               title="Remove role <?php echo $oCurrentRole->getDisplayName(); ?>"
               style="border-bottom: 0px"
               onClick="<?php echo $strOnClick; ?>">
                <img alt="" src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/>
            </a>
          </td>
        </tr>
      <?php
      }
    ?>

  </table>
</td>
<td><?php echo $oPerson->getEMail(); ?></td>
<td>
  <div id="<?php echo $strPermissionsTarget; ?>">
    <input type="checkbox" name="canView" value="1" checked disabled> view &nbsp;
    <input type="checkbox" name="canCreate" value="1" <?php if($this->bCanCreate)echo "checked"; ?>> create  &nbsp;
    <input type="checkbox" name="canEdit" value="1" <?php if($this->bCanEdit)echo "checked"; ?>> edit &nbsp;
    <input type="checkbox" name="canDelete" value="1" <?php if($this->bCanDelete)echo "checked"; ?>> delete  &nbsp;
    <input type="checkbox" name="canGrant" value="1" <?php if($this->bCanGrant)echo "checked"; ?>> grant
  </div>
</td>
<td><input type="checkbox" name="copyToExp" value="1" checked/></td>
<td><input type="button" value="Save" onClick="saveMember('frmMemberAdd', '/warehouse/projecteditor/savemember');"></td>




