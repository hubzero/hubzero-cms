<?php

defined('_JEXEC') or die('Restricted access');

require_once 'api/org/nees/html/TabHtml.php';

class SitesActivitiesHelper {


    /*     * ****************************************************************************************
     * 3 Tabs total:
     * 1 - Map
     * 2 - Experiments
     * 3 - Equipment Availability
     *
     * Active tab index determines which is selected, and it is zero based
     * **************************************************************************************** */

    static function getSitesActivitiesTabs($activeTabIndex) {

        $facilityID = JRequest::getVar('id', 0);

        $tabArrayLinks = array("sitesactivities",
            "upcomingexperiments",
            "equipmentavailability",
            "videofeeds");

        $tabArrayText = array("Activities Map",
            "Site Experiments",
            "Site Equipment Schedules",
            "Site Video Feeds");

        $strHtml = '<div id="sub-menu">';
        $strHtml .= '<ul>';
        $i = 0;

        foreach ($tabArrayText as $tabEntryText) {
            if ($tabEntryText != '') {
                $strHtml .= '<li id="sm-' . $i . '"';
                $strHtml .= ( $i == $activeTabIndex) ? ' class="active"' : '';
                $strHtml .= '><a class="tab" rel="' . $tabEntryText . '" href="' . JRoute::_('/index.php?option=com_sitesactivities' . ($facilityID ? ('&id=' . $facilityID) : '') . '&view=' . strtolower($tabArrayLinks[$i])) . '"><span>' . $tabEntryText . '</span></a></li>';
                $i++;
            }
        }

        $strHtml .= '</ul>';
        $strHtml .= '<div class="clear"></div>';
        $strHtml .= '</div><!-- / #sub-menu -->';

        return $strHtml;
    }

    /*     * ***************************************************************************************
     *
     * Used to determine if a person is the last person with rights for a site, if so,
     * that person should be granted full rights, to avoid the case where nobody has
     * adequate rights to maintain site information
     *
     * **************************************************************************************** */

    static function shouldDisableRevocation($facility) {
        $lastPersonWithFullPremissions = AuthorizationPeer::findLastPersonWithFullPermissions($facility);

        if (is_null($lastPersonWithFullPremissions))
            return false;
        else
            return ($lastPersonWithFullPremissions == $this->getEditPerson()->getId());
    }

    /*     * ****************************************************************************************
     *
     * Save the roles and permissions for the speficied facility/person
     * Pass back a confirmation or errorMsg
     *
     * **************************************************************************************** */

    static function savecontactrolesandpermissions($facilityID, $editorUserName, $editPersonID, $roleIds, $canEdit, $canDelete, $canCreate, $canGrant, &$msg, &$errorMsg) {

        $facility = FacilityPeer::find($facilityID);
        $editPerson = PersonPeer::find($editPersonID);

        $forceGrantall = FacilityHelper::shouldDisableRevocation($facility);

        $auth = Authorizer::getInstanceForUseOnHub($editorUserName, $facilityID, DomainEntityType::ENTITY_TYPE_FACILITY);
        $can_grant = $auth->canGrant($facility);

        if (!$can_grant) {
            $errorMsg = "You do not have permission to revoke the membership of members on this facility";
            return;
        }

        if (!is_array($roleIds) || sizeof($roleIds) == 0) {
            $defaultRole = RolePeer::getDefaultRoleByEntityTypeId($this->entity_type_id);
            $roleIds = array($defaultRole->getId());
        }

        // Explicitly set permissions, if they're overridden from the Role-based defaults
        // make sure they have at least 'view' access
        $perms = new Permissions(Permissions::PERMISSION_VIEW);

        if ($canEdit || $forceGrantall) {
            $perms->setPermission(Permissions::PERMISSION_EDIT);
        }

        if ($canDelete || $forceGrantall) {
            $perms->setPermission(Permissions::PERMISSION_DELETE);
        }

        if ($canCreate || $forceGrantall) {
            $perms->setPermission(Permissions::PERMISSION_CREATE);
        }

        if ($canGrant || $forceGrantall) {
            $perms->setPermission(Permissions::PERMISSION_GRANT);
        }


        $editPerson->removeFromEntity($facility);

        foreach ($roleIds as $roleId) {
            $role = RolePeer::find($roleId);
            $editPerson->addRoleForEntity($role, $facility);
        }

        $auth = new Authorization($editPersonID, $facilityID, DomainEntityType::ENTITY_TYPE_FACILITY, $perms);
        $auth->save();

        $msg = 'Update sucessful';
    }

    static function canGrant($facility) {
        $can_grant = false;
        /* $user =& JFactory::getUser();

          if($user->id > 1)
          {
          $username = $user->get('username');
          $auth = Authorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
          $can_grant = $auth->canGrant($facility);
          }
          else
          $can_grant = false;
         */
        return $can_grant;
    }

    static function canEdit($facility) {
        $can_edit = false;
        /* $user =& JFactory::getUser();

          if($user->id > 1)
          {
          $username = $user->get('username');
          $auth = Authorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
          $can_edit = $auth->canEdit($facility);
          }
          else
          $can_edit = false;
         */
        return $can_edit;
    }

    static function canCreate($facility) {
        $can_create = false;
        /* $user =& JFactory::getUser();

          if($user->id > 1)
          {
          $username = $user->get('username');
          $auth = Authorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
          $can_create = $auth->canCreate($facility);
          }
         */
        return $can_create;
    }

    static function canDelete($facility) {
        $can_delete = false;
        /* $user =& JFactory::getUser();

          if($user->id > 1)
          {
          $username = $user->get('username');
          $auth = Authorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
          $can_delete = $auth->canCreate($facility);
          }
         */

        return $can_delete;
    }

    public static function getNawiStatus($facilityid) {

        $sql = "SELECT FACILITYID, NAWI_STATUS FROM ORGANIZATION WHERE FACILITYID = " . $facilityid;

        $conn = Propel::getConnection();
        $stmt = $conn->prepareStatement($sql);
        $rs = $stmt->executeQuery(ResultSet::FETCHMODE_ASSOC);

        $rv = '';

        if ($rs->next()) {
            $rv = $rs->getString('NAWI_STATUS');
        }

        return $rv;
    }


    public static function CreateHideMoreSection($text, $defaultShowLength=250)
    {

        $rv = '';
        $randNum = mt_rand();
        $shortText = trim(substr($text, 0, $defaultShowLength));


        if(strlen($text) > $defaultShowLength)
        {
            $rv = <<<ENDHTML
            <div id="d$randNum-short" >
                <p>$shortText...
                    <a class="morelesslink" href="javascript:void(0);" onclick="document.getElementById('d$randNum-long').style.display='';document.getElementById('d$randNum-short').style.display='none';">[more]</a>
                </p>
            </div>
            <div id="d$randNum-long" style="display:none;">
                <p> 
                    $text <a class="morelesslink" href="javascript:void(0);" onclick="document.getElementById('d$randNum-short').style.display='';document.getElementById('d$randNum-long').style.display='none';">[less]</a>
                </p>
            </div>
ENDHTML;

        }
        else
        {
            $rv = $text;
        }

        return $rv;

    }







}

// end class FaciityHelper
?>