<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'lib'.DS.'mailingListHelper.php' );


class MailingListController extends JController
{	
	public function __construct()
	{
            parent::__construct();
            $this->_redirect = NULL;

            // Register tasks
            $this->registerTask('saveesubscriptions', 'saveesubscriptions');
            $this->registerTask('edit', 'edit');
            $this->registerTask('save', 'save');
            $this->registerTask('__default', 'edit');

            
	}
	

	public function redirect()
	{
            if ($this->_redirect != NULL) {
                $app =& JFactory::getApplication();
                $app->redirect( $this->_redirect, $this->_message, $this->_messageType );
            }
	}


        function edit()
        {
            JRequest::setVar('view','join');
            parent::display();
        }

	/*
	 * The form submits here, to actually enroll (or unenroll) a user from the specified groups
	 */
	function save()
	{
            $juser =& JFactory::getUser();

            // Get groups and loop through them
            $mailingListGroupsArray = mailingListHelper::getGroups();
            $addedGroups = 0;
            $removedGroups = 0;

            foreach($mailingListGroupsArray as $group)
            {
                $groupName = $group[0];
                $groupPW = $group[1];

                // See if group has a checkbox associated with it
                $temp = JRequest::getVar($groupName, '');
			
                // If group name was found from query, that means it was checked
                if('' != $temp)
                {
                    //Add only if user isn't already in list
                    if(!mailingListHelper::userMemberOfList($juser->email, $groupName, $groupPW))
                    {
                        mailingListHelper::addUserToList($juser->email, $groupName, $groupPW);
                        $addedGroups++;
                    }
                }
                else // if checkbox wasn't there
                {
                    // remove only if they were enrolled
                    if(mailingListHelper::userMemberOfList($juser->email, $groupName, $groupPW))
                    {
                        mailingListHelper::removeUserFromList($juser->email, $groupName, $groupPW);
                       $removedGroups++;
                    }
                }
            }

            $this->_redirect = JRoute::_('/index.php?option=com_mailinglist&task=edit');
            $this->_message = 'Save Complete. Added to ' . $addedGroups . ' group' . ($addedGroups != 1 ? 's' : '') . ' and removed from ' . $removedGroups . ' group' . ($removedGroups != 1 ? 's' : '');
            $this->_messageType = 'notice';
            



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
