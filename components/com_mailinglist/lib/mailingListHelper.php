<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'lib'.DS.'php_mailman.php' );

class mailingListHelper
{
    static private $groupIDs;

    /**
     * Return mailing list groups
     *
     * @return <type> array, each element is a 2 element array, 1st element is the
     * group name, second element is the admin password for the group
     */
    static function getGroups()
    {
        if(!self::$groupIDs)
        {
            $mainframe = JFactory::getApplication();
            $params =& $mainframe->getParams();
            $temp = $params->get('grouopIDs');

            $tempArray = explode(',', $temp);
            self::$groupIDs = array();
            foreach($tempArray as $groupNamePWEntry)
            {
                $tempGroupInfoArray = explode('|', $groupNamePWEntry);
                $tempGroupName = $tempGroupInfoArray[0];
                $tempGroupPW = $tempGroupInfoArray[1];
                $tempGroupDescription = $tempGroupInfoArray[2];

                self::$groupIDs[] = array($tempGroupName, $tempGroupPW, $tempGroupDescription);
            }
        }

        return self::$groupIDs;
    }

    /**
     * See if an email is subscribed to the specified list
     *
     * @param <string> $email
     * @param <string> $listname
     * @return <boolean>
     */
    static function userMemberOfList($email, $listname, $listadminpw)
    {
        
        $mailman = new php_mailman('mailman.nees.org', $listadminpw, $listname, 'http');
        $output = $mailman->list_member($email);

        // Uncomment out for debug info
        //echo $output;

        // Screen scape the search results for the list search page
        // 'x members total'
        preg_match('/([0-9]+)( members total)/i', $output, $matches);

        // If the search returns only 1 result return true

        if(count($matches) == 0)
        {
            echo 'Error: cannot find info for: <b>' . $listname . '</b><br>';
            return false;
        }

        if($matches[1] == 1)
            return true;
        else
            return false;
    }


    static function addUserToList($email, $listname, $listadminpw)
    {
        //echo $email;
        //echo $listname;
        
        $mailman = new php_mailman('mailman.nees.org', $listadminpw, $listname, 'http');

        // Put echo statement in front of call below to debug
        $mailman->subscribe($email);
    }

    static function removeUserFromList($email, $listname, $listadminpw)
    {
        $mailman = new php_mailman('mailman.nees.org', $listadminpw, $listname, 'http');

        // Put echo statement below to debug
        $mailman->unsubscribe($email);
    }

}
?>
