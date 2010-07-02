<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
require_once 'api/org/nees/html/TabHtml.php';

class FacilityHelper
{


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
		
		foreach ($tabArrayText as $tabEntryText){
			if ($tabEntryText != '') {
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

		$auth = Authorizer::getInstanceForUseOnHub($editorUserName, $facilityID, DomainEntityType::ENTITY_TYPE_FACILITY);
		$can_grant = $auth->canGrant($facility);

		if(!$can_grant) {
			$errorMsg = "You do not have permission to revoke the membership of members on this facility";
			return;
		}

		if(!is_array($roleIds) || sizeof($roleIds) == 0) 
		{
			$defaultRole = RolePeer::getDefaultRoleByEntityTypeId($this->entity_type_id);
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
	
	/*
	 * 
	 * Used by the component to see if a user should be allowed to edit data on the facility. Since
	 * the right is all or nothing, this same function should be usable across the board, from both the
	 * interface to determine if an edit button should be displayed, as well as the backend to make
	 * sure a submission that might be hacked to grant permissions.
	 * 
	 */
	function canEdit($facility)
	{
		$user =& JFactory::getUser();
		$username = $user->get('username');
		
		$auth = Authorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);

		$can_edit = $auth->canEdit($facility);

		if(!$can_edit)
			return false;
		else
			return true;
	}

        function canCreate($facility)
	{
		$user =& JFactory::getUser();
		$username = $user->get('username');

		$auth = Authorizer::getInstanceForUseOnHub($username, $facility->getId(), DomainEntityType::ENTITY_TYPE_FACILITY);

		$can_create = $auth->canCreate($facility);

		if(!$can_create)
			return false;
		else
			return true;
	}


	
} // end class FaciityHelper


?>