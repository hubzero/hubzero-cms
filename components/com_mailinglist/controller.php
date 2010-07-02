<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');
ximport('xgroup');



class MailingListController extends JObject
{	
	
	private $_task  = NULL;
	private $mailingListGroupsArray = array();

	public function __construct()
	{
		$this->_redirect = NULL;
		
		$mainframe = JFactory::getApplication();
		$params =& $mainframe->getParams();
		$groupIDs = $params->get('grouopIDs');
		$this->mailingListGroupsArray = explode(',', $groupIDs);
	}
	

    public function execute()
	{
		$this->_task = JRequest::getVar( 'task', '' );
		
		switch ($this->_task) 
		{
			case 'join':		$this->join();			break;
			case 'dojoin':		$this->dojoin();		break;
			default:			$this->join();			break;
		}
	}
    
    
	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}

	
	/*
	 * The form submits here, to actually enroll (or unenroll) a user from the specified groups
	 */
	protected function dojoin()
	{
		
		$loopCount = 0;
		$removedFromGroups = '';
		$addedToGroups = '';
		$temp = '';
		
		$group = new XGroup();
		$juser =& JFactory::getUser();		
		
		// Loop through and get each checkbox
		foreach($this->mailingListGroupsArray as $mailingListGroup)
		{
			$temp = JRequest::getVar($mailingListGroup, '');
			
			$rv = $group->select($mailingListGroup);
			
			if($rv) // group select returns a valid group
			{
			
				// If group name was found from query, that means it was checked 
				if('' != $temp)
				{
					
					if (!$group->is_member_of('applicants',$juser->get('id')) && 
						!$group->is_member_of('members',$juser->get('id')) && 
						!$group->is_member_of('managers',$juser->get('id')) && 
						!$group->is_member_of('invitees',$juser->get('id'))) 
					{
						// Group must have open membershi policy, otherwise, silently ignore
						// request
						if ( $group->get('join_policy') == 0 ) // open membership
						{
							$group->add('members',array($juser->get('id')));
							$group->update();
						}
					}
					
				}
				else // if unavailable, remove from group membership
				{
					// Unenroll, don't even bother checking for previous enrollment
					$group->remove('managers',$juser->get('id'));
					$group->remove('members',$juser->get('id'));
					$group->remove('applicants',$juser->get('id'));
					$group->remove('invitees',$juser->get('id'));
	
					$group->update();
				}
			}				
		}		
		
		// Instantiate a view
		$view = new JView( array('name'=>'join') );

		$listhtml = $this->groupListHtml();
    	$view->assignRef('listhtml', $listhtml); 
		
		$confirmation = 'Mailing list membership updated';
    	$view->assignRef('confirmationMessage', $confirmation); 
		
    	$view->display();
		
	}
	
	
	/*
	 * Displays the form that prompts user to select mailing list groups
	 */
	protected function join()
	{
		
		if ($this->getError()) 
		{
			$view->setError( $this->getError() );
		}

    	// Instantiate a view
		$view = new JView( array('name'=>'join') );
		$listhtml = $this->groupListHtml();
    	$view->assignRef('listhtml', $listhtml); 
    	$view->display();
	}

	
	public function groupListHtml()
	{
		$listhtml = null;
		$isMember = false;
		$juser =& JFactory::getUser();
		
		// build the HTML for the group checkboxes here
    	foreach($this->mailingListGroupsArray as $groupid)
    	{
			$isMember = false;
    		
			// See if user is a member of this group
			$group = new XGroup();
			$group->select( $groupid );
    		
			if ($group->is_member_of('applicants',$juser->get('id')) || 
				$group->is_member_of('members',$juser->get('id')) || 
				$group->is_member_of('managers',$juser->get('id')) || 
				$group->is_member_of('invitees',$juser->get('id'))) 
			{
				$isMember = true;
			}
			
    		$listhtml .= '<li>' . 
    					'<input type="checkbox"' . 
    					($isMember == true ? 'checked="checked"' : '') . 
    					' value="' . $groupid . '"' . 
    					' name="' . $groupid . '"' . 
    					'> ' . $groupid . '</input>' . 
    					"</li>\n";
    			
    	}
		
    	return $listhtml;
	}	

	
	
	
	/*
	 * Return true if user is logged in, false otherwise. Calling the redirect()
	 * function on this class after a false return, will redirect to the login
	 * form with an appropiately set redirectURL for ferrying client back to 
	 * the correct place after they login
	 */
	function userloggedin()
	{
		$juser =& JFactory::getUser();
		
		if ($juser->get('guest')) 
		{
			// Get current page path and querystring
			$uri  =& JURI::getInstance();
			$redirectUrl = $uri->toString(array('path', 'query'));
			
			// Code the redirect URL
			$redirectUrl = base64_encode($redirectUrl);  
			$redirectUrl = '?return=' . $redirectUrl;
			$joomlaLoginUrl = '/login';
		    $finalUrl = $joomlaLoginUrl . $redirectUrl;
		    $finalUrl = JRoute::_($finalUrl);
		    $this->_redirect = $finalUrl;
		    
		    return false;
		}
		else
			return true;
	}
	
	
}
?>
