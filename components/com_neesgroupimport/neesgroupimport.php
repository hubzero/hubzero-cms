<?php

ximport('xregistration');
ximport('xregistrationhelper');
ximport('xprofile');
ximport('xuserhelper');
ximport('xhubhelper');
ximport('xneesprofile');
ximport('xgroup');

$groupcreatecount = 0;
$groupupdatecount = 0;
$groupimportcounterrors = 0;
$usergroupaddcount = 0;
$usergroupadderrors = 0;

// Super quick and dirty user import
main();


function main()
{
    global $groupcreatecount;
    global $groupupdatecount;
    global $groupimportcounterrors;
    global $usergroupaddcount;
    global $usergroupadderrors;

    $groupConfirmationMsg = '';
    $userGroupAddConfirmationMsg = '';
    $confirmationmsg = '';

    if(JRequest::getVar('formsubmit', '') != '')
    {
            //Process the form
            if(Jrequest::getVar('password') == 'justdoit')
            {
                if(JRequest::getVar('import') == 'groups')
                    $groupConfirmationMsg = processGroups();

                if(JRequest::getVar('import') == 'usergroupadd')
                    $userGroupAddConfirmationMsg = processUserGroupAdds();

            }
            else
            {
                $confirmationmsg = '<font color="red">wrong password</font>';
            }
    }

    echo $confirmationmsg;

    displayGroupsForm();
    displayGroupUserAddForm();

    echo '<h3>Overall Statistics</h3>';
    echo 'groupcreatecount = ' .$groupcreatecount . '<br/>';
    echo 'groupupdatecount = ' .$groupupdatecount . '<br/>';
    echo 'groupimportcounterrors = ' . $groupimportcounterrors . '<br/>';
    echo 'usergroupaddcount = ' . $usergroupaddcount . '<br/>';
    echo 'usergroupadderrors = ' . $usergroupadderrors . '<br/>';

    echo '<h3>Group Creation Confirmation Message</h3>';
    echo $groupConfirmationMsg;

    echo '<h3>User Membership Add Confirmation Messages</h3>';
    echo $userGroupAddConfirmationMsg;

}


function processUserGroupAdds()
{

    $rv = '';
    $import = JRequest::getVar('usermembershipadd-csv', '');

    if($import != '')
    {
        $lines = explode("\n", $import);

        foreach($lines as $line)
        {
                $fieldarray = explode("|", $line);
                $rv .= useraddmembership($fieldarray[0], $fieldarray[1], $fieldarray[2]);
        }
    }

    return $rv;
}


function processGroups()
{

    $rv = '';
    $groupsimport = JRequest::getVar('groups-csv', '');
    $ownername = trim(JRequest::getvar('groupwneruserid'));

    $lines = explode("\n", $groupsimport);

    foreach($lines as $line)
    {
        $fieldarray = explode("|", $line);

        //$rv .= $fieldarray[0];


        $rv .= create($fieldarray[0],
            $fieldarray[1],
            $fieldarray[2],
            $fieldarray[3],
            $fieldarray[4],
            $fieldarray[5],
            $fieldarray[6],
            $fieldarray[7],
            $ownername);

    }

    return $rv;

}


function displayGroupsForm()
{

    // Present the form
    echo '<form method=post>';

    echo '<h3>Groups</h3> <br/><b>Format:</b><br/> groupname,<br/>description (title),<br/> public description,<br/> private decription,<br/> Join Policy (0-anyone, 1-restricted, 2-invite only, 3-closed),<br/> restrict message,<br/> privacy (0-public, 1-protected, 4-private), <br/>access-content privacy (0-public, 3-protected, 4-private)';
    echo '<br/><br/>Import Password:<input type="text" name="password">';

    echo '	<br/><br/>';
    echo '	<textarea name="groups-csv" cols="256" rows="16"></textarea>';
    echo '	<br/><br/>';

    echo '  Group Owner Joomla UserID:<input type="text" name="groupwneruserid">';
    echo '	<br/><br/>';

    echo '	<br/><br/>';
    echo '	<input type="hidden" name="formsubmit" value="1">';
    echo '	<input type="hidden" name="import" value="groups">';
    echo '	<br/><br/>';
    echo '	<input type="submit" value="Import Groups">';
    echo '</form>';

}



function displayGroupUserAddForm()
{

    // Present the form
    echo '<form method=post>';
    echo '<h3>User group membership adds</h3> <br/><b>Format:</b><br/>group,user,type (1-mananger, 2-user)';
    echo '<br/><br/>Import Password:<input type="text" name="password">';

    echo '	<br/><br/>';
    echo '	<textarea name="usermembershipadd-csv" cols="256" rows="16"></textarea>';
    echo '	<br/><br/>';

    echo '	<input type="hidden" name="formsubmit" value="1">';
    echo '	<input type="hidden" name="import" value="usergroupadd">';
    echo '	<br/><br/>';
    echo '	<input type="submit" value="Import User Group Membership Additions">';
    echo '</form>';

}



/*************************************************************************
 * groupname
 * description (title)
 * public descrtiption
 * private decription
 * Join Policy (0-anyone, 1 - restricted, 2 invite only, 3 closed)
 * restrict message
 * privacy (0-public, 1-protected, 4-private)
 * access (content privacy) (0-public, 3-protected, 4-private)
 * ownerusername
 *
 *************************************************************************/

function create($g_cn, $g_description, $g_public_desc, $g_private_desc, $g_join_policy,
           $g_restrict_msg, $g_privacy, $g_access, $owneruserid)
{

    global $groupcreatecount;
    global $groupupdatecount;
    global $groupimportcounterrors;

    $isNew = false;
    $rv = '';

    // Get some needed objects
    $xhub =& XFactory::getHub();
    $jconfig =& JFactory::getConfig();

    // Instantiate an XGroup object
    $group = new XGroup();

    // If the group already exists
    if($group->select( $g_cn ))
    {
        $isNew = false;
        $groupupdatecount++;
    }
    else // create it
    {
        $isNew = true;

        $group->set('cn', $g_cn);

        $group->set('type', 1 );
        $group->set('published', 1 );

        // We auto add a global admin user for all imported groups
        if($owneruserid != '')
            $group->add('managers',$owneruserid);

        $groupcreatecount++;

    }

    // Save or update, this is the same
    $group->set('description', $g_description );
    $group->set('access', $g_access );
    $group->set('privacy', $g_privacy );
    $group->set('public_desc', $g_public_desc );
    $group->set('private_desc', $g_private_desc );
    $group->set('restrict_msg',$g_restrict_msg);
    $group->set('join_policy',$g_join_policy);

    $group->save();

    if($isNew)
        $rv .= $g_cn . ' created <br/>';
    else
        $rv .= $g_cn . ' updated <br/>';

    // Process tags (Don't think we need tags here)
    //$database =& JFactory::getDBO();
    //$gt = new GroupsTags( $database );
    //$gt->tag_object($juser->get('id'), $group->get('gidNumber'), $tags, 1, 1);

    return $rv;

}


function useraddmembership($groupname, $user, $type)
{
    global $usergroupaddcount;
    global $usergroupadderrors;
    $rv = '';

    // Instantiate an XGroup object
    $group = new XGroup();


    // If the group exists
    if($group->select( $groupname ))
    {

        $userLookup =& JFactory::getUser($user);

        if($userLookup)
        {
/*
            // Manager or normal user? (1-manager, 2-normal user)
            if($type == 1)
            {
                if(!$group->isManager($user))
                {
                    $group->add('managers',$user);
                    $rv = $user . ' ' . ' added to ' .$groupname . ' as manager<br/>';
                    $usergroupaddcount++;
                }
                else
                {
                    $rv = $user . ' ' . ' is already a manager in ' .$groupname . '<br/>';
                    $usergroupadderrors++;
                }

            }
            if($type == 2)
            {
                if( !$group->isMember($user) )
                {
                    $group->add('members',$user);
                    $rv = $user . ' ' . ' added to ' .$groupname . ' as ordinary member<br/>';
                    $usergroupaddcount++;
                }
                else
                {
                    $rv = $user . ' ' . ' is already a member in ' .$groupname . '<br/>';
                    $usergroupadderrors++;
                }

            }

            $group->save();
*/
        }
        else // cannot find user
        {
            $rv = $user . ' ' . 'not added to ' . $groupname . '. Could not find user. <br/>';
            $usergroupadderrors++;
        }


    }
    else // cannot find group
    {
        $rv = $user . ' ' . 'not added to ' .$groupname . '. Could not find group. <br/>';
        $usergroupadderrors++;
    }

    return $rv;

}




?>