<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
require_once 'api/org/nees/html/TabHtml.php';

class FacilityHelper
{


    /*
     * Detect admin and super admin users
     */
    static function isAdmin() {
        $isadmin = false;

        $user = & JFactory::getUser();
        if ($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
            $isadmin = true;

        return $isadmin;
    }

    /******************************************************************************************
     * 7 Tabs total:
     * 1 - Main
     * 2 - Contact info
     * 3 - Staff
     * 4 - Equipment
     * 5 - Sensors
     * 6 - Training and certification
     * 7 - Education and Outreach
     *
     * Active tab index determines which is selected, and it is zero based
     ******************************************************************************************/
    static function getFacilityTabs($activeTabIndex, $facid)
    {
		
        $tabArrayLinks = array("site",
            "contact",
            "staff",
            "majorequipment",
            "sensors",
            "trainingandcertification",
            "educationandoutreach");
	
        $tabArrayText = array("Main",
            "Contact Info",
            "Staff",
            "Equipment",
            "Sensors",
            "Training and Certification",
            "Education and Outreach");

        $strHtml  = '<div id="sub-menu">';
        $strHtml .= '<ul>';
        $i = 0;
		
        foreach ($tabArrayText as $tabEntryText)
        {
            if ($tabEntryText != '')
            {
                $strHtml .= '<li id="sm-'.$i.'"';
                $strHtml .= ($i==$activeTabIndex) ? ' class="active"' : '';
                $strHtml .= '><a class="tab" rel="' . $tabEntryText . '" href="' . JRoute::_('/index.php?option=com_sites&id=' . $facid . "&view=" . strtolower($tabArrayLinks[$i])) . '"><span>' . $tabEntryText . '</span></a></li>';
                $i++;
            }
        }

        $strHtml .= '</ul>';
        $strHtml .= '<div class="clear"></div>';
        $strHtml .= '</div><!-- / #sub-menu -->';

        return $strHtml;
    }		
		

    /*****************************************************************************************
    *
    * Used to determine if a person is the last person with rights for a site, if so,
    * that person should be granted full rights, to avoid the case where nobody has
    * adequate rights to maintain site information
    *
    ******************************************************************************************/
    static function shouldDisableRevocation($facility)
    {
        $lastPersonWithFullPremissions = AuthorizationPeer::findLastPersonWithFullPermissions($facility);

        if (is_null($lastPersonWithFullPremissions))
            return false;
        else
            return ($lastPersonWithFullPremissions == $this->getEditPerson()->getId());
    }
    
    
    /******************************************************************************************
     *
     * Save the roles and permissions for the speficied facility/person
     * Pass back a confirmation or errorMsg
     *
     ******************************************************************************************/
    static function savecontactrolesandpermissions($facilityID,
            $editorUserName,
            $editPersonID,
            $roleIds,
            $canEdit,
            $canDelete,
            $canCreate,
            $canGrant,
            &$msg,
            &$errorMsg)
    {

    	$facility = FacilityPeer::find($facilityID);
        $editPerson = PersonPeer::find($editPersonID);

        $forceGrantall = FacilityHelper::shouldDisableRevocation($facility);

        $auth = HubAuthorizer::getInstanceForUseOnHub($editorUserName, $facilityID, DomainEntityType::ENTITY_TYPE_FACILITY);
        $can_grant = FacilityHelper::canGrant($facility);

        //var_dump($can_grant);

        if(!$can_grant)
        {
            $errorMsg = "You do not have permission to revoke the membership of members on this facility";
            return;
        }

        if(!is_array($roleIds) || sizeof($roleIds) == 0)
        {
            $defaultRole = RolePeer::getDefaultRoleByEntityTypeId(DomainEntityType::ENTITY_TYPE_FACILITY);
            $roleIds = array($defaultRole->getId());
        }

        // Explicitly set permissions, if they're overridden from the Role-based defaults
        // make sure they have at least 'view' access
        $perms = new Permissions(Permissions::PERMISSION_VIEW);

        if ( $canEdit || $forceGrantall)
        {
            $perms->setPermission(Permissions::PERMISSION_EDIT );
      	}
      
      	if ( $canDelete || $forceGrantall) 
      	{
            $perms->setPermission(Permissions::PERMISSION_DELETE );
        }
		
        if ( $canCreate || $forceGrantall)
        {
            $perms->setPermission(Permissions::PERMISSION_CREATE );
        }
      
        if ( $canGrant || $forceGrantall)
        {
            $perms->setPermission(Permissions::PERMISSION_GRANT );
        }


        $editPerson->removeFromEntity($facility);

        foreach ($roleIds as $roleId)
        {
            $role = RolePeer::find($roleId);
            $editPerson->addRoleForEntity($role, $facility);
        }

        $auth = new Authorization($editPersonID, $facilityID, DomainEntityType::ENTITY_TYPE_FACILITY, $perms);
        $auth->save();
		
        $msg = 'Update sucessful';
		
    }
	

    /**
    * A short way to check a person can have edit permission to create / edit a sensor model or not
    * simply by checking if there is at least one edit permission on any Facility
    *
    * @return boolean
    */
    static function canEditSensorModel($facility)
    {
        $can_edit = false;
        $user =& JFactory::getUser();

        if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
        {
            $can_edit = true;
        }
        else
        {
            if($user->id > 1)
            {
                $username = $user->get('username');
                $auth = HubAuthorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
                $can_edit = AuthorizationPeer::CanDoInAnyFacility($auth->getUserId(), "create");
            }
            else
                $can_edit = false;
        }

        return $can_edit;

    }


    /**
    * A short way to check a person can have edit permission to create / edit a sensor model or not
    * simply by checking if there is at least one edit permission on any Facility
    *
    * @return boolean
    */
    static function canCreateSensorModel($facility)
    {
        $can_create = false;
        $user =& JFactory::getUser();

        if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
        {
            $can_create = true;
        }
        else
        {
            if($user->id > 1)
            {
                $username = $user->get('username');
                $auth = HubAuthorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
                $can_create = AuthorizationPeer::CanDoInAnyFacility($auth->getUserId(), "create");
            }
            else
                $can_create = false;
        }
        
        return $can_create;
    }



    static function canGrant($facility)
    {
        $can_grant = false;
        $user =& JFactory::getUser();

        if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
        {
            $can_grant = true;
        }
        else
        {
            if($user->id > 1)
            {
                $username = $user->get('username');
                $auth = HubAuthorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
                $can_grant = $auth->canGrant($facility);
            }
            else
                $can_grant = false;
        }

        return $can_grant;
    }


    static function canEdit($facility)
    {
        $can_edit = false;
        $user =& JFactory::getUser();

        if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
        {
            $can_edit = true;
        }
        else
        {
            if($user->id > 1)
            {
                $username = $user->get('username');
                $auth = HubAuthorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
                $can_edit = $auth->canEdit($facility);
            }
            else
                $can_edit = false;
        }

        return $can_edit;
    }


    static function canCreate($facility)
    {
        $can_create = false;
        $user =& JFactory::getUser();

        if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
        {
            $can_create = true;
        }
        else
        {
            if($user->id > 1)
            {
                $username = $user->get('username');
                $auth = HubAuthorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
                $can_create = $auth->canCreate($facility);
            }
            else
                $can_create = false;

        }
        
        return $can_create;
    }


    static function canDelete($facility)
    {
        $can_delete = false;
        $user =& JFactory::getUser();

        if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
        {
            $can_delete = true;
        }
        else
        {
            if($user->id > 1)
            {
                $username = $user->get('username');
                $auth = HubAuthorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);
                $can_delete = $auth->canCreate($facility);
            }
            else
                $can_delete = false;
        }

        return $can_delete;
    }


   /**
   * Get a single DataFile for a facility by infoType, subInfoType and GroypBy
   *
   * @param String $info
   * @param String $sub
   * @param String $groupby
   * @return DataFile
   */
    static function getFacilityDataFile( $facilityid, $info, $sub, $groupby='' )
    {
        $facDataFiles = FacilityDataFilePeer::findByDetails($facilityid, $info, $sub, $groupby);

        if (count($facDataFiles) > 0 && $ff = $facDataFiles[0]) {
            $df = $ff->getDataFile();

            if ( !$df->getDeleted())
                return $df;
        }

        return null;
    }


    static function getViewSimpleFileBrowser($datafiles, $rootname, $redirectURL)
    {
        $listing = '';
        $count_files = 0;
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canDelete = FacilityHelper::canDelete($facility);

        // loop through each data file
        foreach($datafiles as $df)
        {
            /* @var $df DataFile */
            //name, directory, created, filesize

            if(is_null($df)) continue;

            $df_id          = $df->getId();
            $df_path        = $df->getPath();
            $df_name        = $df->getName();
            $df_created     = $df->getCreated();

            $df_filesize    = $df->getFilesize();
            $df_thumbid     = $df->getThumbId();

            $df_description = str_replace("\r\n", " ", htmlspecialchars($df->getDescription()));
            $df_title       = str_replace("\r\n", " ", htmlspecialchars($df->getTitle()));
            $df_authors     = str_replace("\r\n", " ", htmlspecialchars($df->getAuthors()));
            $df_emails      = str_replace("\r\n", " ", htmlspecialchars($df->getAuthorEmails()));
            $df_cite        = str_replace("\r\n", " ", htmlspecialchars($df->getHowToCite()));

            $df_fullpath = $df_path . "/" . $df_name;

            if(!file_exists($df_fullpath)) continue;

            $df_directory = is_dir($df_fullpath) ? 1 : 0;

            if($df_directory) continue;

            if(empty($df_created))
                $df_created = date("Y m d H:i:s", filemtime($df_fullpath));

            //
            //$df_created = $this->cleantimestamp($df_created);

            $count_files++;

            $name_truncate = FacilityHelper::str_truncate($df_name, 60);
            $name_encode = rawurlencode($df_name);


            if(empty($df_filesize)) {
                $df_filesize = filesize($df_fullpath);
            }

            $cleansize = FacilityHelper::cleanSize($df_filesize);
            $file_url = $df->get_url();

            $canDeleteLink = ($canDelete) ? '<a onclick="return confirm(\'Are you sure you want to delete this document?\');" class="imagelink-no-underline" href="'.JRoute::_('index.php?option=com_sites&task=deletefile&id=' . $facilityID . '&fileid=' . $df_id . '&redirectURL=' . $redirectURL).'"><img title="Delete" alt="Delete" src="/components/com_sites/images/cross.png"></a>' : '';

            $listing .= <<<ENDHTML
                <tr>
                    <td nowrap="nowrap"><a href="$file_url" target="_blank">$name_truncate</a></td>
                    <td nowrap="nowrap">$df_created</a></td>
                    <td nowrap="nowrap">$cleansize</a></td>
                    <td style="text-align: right;" nowrap="nowrap">
                        $canDeleteLink
                    </td>
                </tr>
ENDHTML;

        } // end file foreach loop

        if($count_files == 0)
        {
            $browser = <<<ENDHTML
            <h3>$rootname</h3>
            <i>No files uploaded.</i>
ENDHTML;
        }
        else
        {
            $browser = <<<ENDHTML
            <h3>$rootname</h3>
            <table style="border:0px; margin-top:5px;">
                <tr style="background:#eee;" >
                    <td style="width:300px">Name</td>
                    <td style="width:300px">Timestamp</td>
                    <td style="width:100px">Size</td>
                    <td style="width:25px">&nbsp;</td>
                </tr>
            $listing
            </table>
            <hr>
ENDHTML;
        }


        return $browser;

    }


    static function changeDateFormat($date)
    {
        if($date == "00-00-0000" || empty($date)) return null;

        //$d = explode('-',$date);
        //return $d[2].'-'.$d[0].'-'.$d[1];

        return $date;

    }




    //**********************************************************************************************
    /**
    * @desc
    *   Truncate a string if its length exceed the max length
    *
    * @param $str: The string to be truncated
    * @param $max_char: the max length allowed
    *
    * @return
    *   If length < max length, return its its original string
    *   Else, cut it into three parts, then return the concat string of first and last part
    */
    //**********************************************************************************************
    static function str_truncate($str, $max_char) {

        if(strlen($str) > $max_char)
        {
            $part1 = substr($str, 0, $max_char - 10);
            $part2 = substr($str, -6);
            return $part1."...".$part2;
        }
        return $str;

    }


################################################################################
## size cleaning function
################################################################################
    function cleanSize($size, $precision = null) {
        $default_precision = 0;

        if ($size >= 1099511627776) {
            $size = $size / 1099511627776;
            $unit = " TB";
            $default_precision = 2;
        } elseif ($size >= 1073741824) {
            $size = $size / 1073741824;
            $unit = " GB";
            $default_precision = 1;
        } elseif ($size >= 1048576) {
            $size = $size / 1048576;
            $unit = " MB";
        } elseif ($size >= 1024) {
            $size = $size / 1024;
            $unit = " KB";
        } else {
            $unit = " b";
        }

        if (is_null($precision))
            $precision = $default_precision;
        $size = round($size, $precision);
        return "$size $unit";
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