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
 $oRoleArray = unserialize($_REQUEST[RolePeer::TABLE_NAME]);
 $oCurrentRoleArray = unserialize($_SESSION["USER_ROLES"]);
 $strPermissionsTarget = $_SESSION["PERMISSIONS_TARGET_ID"];

 $iPersonId = $this->personId;
 $iProjectId = $this->projectId;
 $iRoleId = $this->roleId;
 $strOnChange="getMootools('/warehouse/projecteditor/permissions?format=ajax&personId=$iPersonId&projectId=$iProjectId&roleId='+this.value, '$strPermissionsTarget');"
?>

<table style="border:0;">
    <tr>
      <td width="1">
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


