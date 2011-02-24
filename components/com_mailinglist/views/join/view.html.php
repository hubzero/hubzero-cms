<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright           Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * 
 * 
 */
 
class mailinglistViewjoin extends JView
{

    function display($tpl = null)
    {
        $listhtml = $this->groupListHtml();
        $this->assignRef('listhtml', $listhtml);

        parent::display($tpl);
    }

    

    public function groupListHtml()
    {
        $rv = null;
        $isMember = false;
        $juser =& JFactory::getUser();
        $email = $juser->email;

        // build the HTML for the group checkboxes here
        $mailingListGroupsArray = mailingListHelper::getGroups();

        foreach($mailingListGroupsArray as $group)
        {
            $groupName = $group[0];
            $groupPW = $group[1];
            $groupDesc = $group[2];

            $isMember = false;

            // Check membership
            $isMember = mailingListHelper::userMemberOfList($email, $groupName, $groupPW);

            $rv .= '<li>' .
                '<input style="vertical-align: middle;" type="checkbox"' .
                ($isMember == true ? 'checked="checked"' : '') .
                ' value="' . $groupName . '"' .
                ' name="' . $groupName . '"' .
                '> ' . $groupName . ' - ' . $groupDesc .  '</input>' .
                "</li>\n";

        }

        return $rv;
    }


}// end class
