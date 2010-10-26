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
 $oAuthorizer = Authorizer::getInstance();
?>

<img width="50" height="50" alt="Photo for <?php echo ucfirst($oPerson->getFirstName()) ." ". ucfirst($oPerson->getLastName()); ?>" src="/components/com_members/images/profile_thumb.gif">***
<?php if($_REQUEST['LINK']){ ?>
  <span class="name"><a href="/members/<?php echo $_REQUEST["ID"]; ?>"><?php echo ucfirst($oPerson->getLastName()) .", ". ucfirst($oPerson->getFirstName()); ?></a></span>***
<?php }else{ ?>
  <span class="name"><?php echo ucfirst($oPerson->getLastName()) .", ". ucfirst($oPerson->getFirstName()); ?></span>***
<?php } ?>
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
***
<?php echo $oPerson->getEMail(); ?>***

  <div id="<?php echo $strPermissionsTarget; ?>">
    <input type="checkbox" name="canView" value="1" checked disabled> view &nbsp;
    <input type="checkbox" name="canCreate" value="1" <?php if($this->bCanCreate)echo "checked"; ?>> create  &nbsp;
    <input type="checkbox" name="canEdit" value="1" <?php if($this->bCanEdit)echo "checked"; ?>> edit &nbsp;
    <input type="checkbox" name="canDelete" value="1" <?php if($this->bCanDelete)echo "checked"; ?>> delete  &nbsp;
    <input type="checkbox" name="canGrant" value="1" <?php if($this->bCanGrant)echo "checked"; ?>> grant
  </div>
***
<!--
<input type="checkbox" checked="" value="1" name="copyToExp"> Access to all<br>
-->
<a href="javascript:void(0);" title="Add member to all experiments" onClick="selectAll('frmMemberAdd', 'experiment[]');">Add To All Experiments</a>
<p style="height: 100px; width:100%; overflow: auto; border: 1px solid #999999; background: none repeat scroll 0% 0%; color: rgb(0, 0, 0); margin-bottom: 1.5em;margin-top:0px;">
<?php
$oExperimentArray = $oProject->getExperiments();
foreach($oExperimentArray as $iIndex => $oExperiment){
  $strCanView = "";
  if($oAuthorizer->personCanDo($oExperiment, "View", $oPerson->getId())){
    $strCanView = "checked";    
  }


  /* @var $oExperiment Experiment */
  ?>
  <label for="experiment-<?php echo $oExperiment->getId(); ?>"><input id="experiment-<?php echo $oExperiment->getId(); ?>" type='checkbox' name='experiment[]' value='<?php echo $oExperiment->getId(); ?>' <?php echo $strCanView; ?>>&nbsp;<a href="javascript:void(0);" title="<?php echo $oExperiment->getTitle(); ?>"><?php echo $oExperiment->getName(); ?></a></label><br>
  <?php
}
?>
</p>
***
<input type="button" value="Save" onClick="saveMember('frmMemberAdd', '/warehouse/projecteditor/savemember');">***



