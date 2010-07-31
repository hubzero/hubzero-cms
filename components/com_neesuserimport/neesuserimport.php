<?php

ximport('xneesprofile');
ximport('Hubzero_Users_Password');


$confirmationmsg = '';
$createdAccountCount = 0;
$SuccessfulCreations = 0;
$ErrorCount = 0;


main();


/***************************************************************************************/
function main()
{

    global $confirmationmsg;
    global $createdAccountCount;
    global $SuccessfulCreations;
    global $ErrorCount;

    if (JRequest::getVar('formsubmit', '') != '') {
        //Process the form
        if (Jrequest::getVar('password') == 'justdoit') {
            $createdAccountCount = process();

            $confirmationmsg .= 'Processed ' . $createdAccountCount . ' accounts' . '<br/>';
            $confirmationmsg .= 'SuccessfulCreations = ' . $SuccessfulCreations . '<br/>';
            $confirmationmsg .= 'ErrorCount = ' . $ErrorCount . '<br/>';
        } 
        else
        {
            $confirmationmsg = 'wrong password';
        }
    }


    displayform($confirmationmsg);
}

/***************************************************************************************/
function process()
{
    $createdAccountCount = 0;

    $usersimport = JRequest::getVar('users-csv', '');

    $lines = explode("\n", $usersimport);

    foreach($lines as $line)
    {
        // username,firstname,lastname,email,neesaffiliation,pw
        $fieldarray = explode(",", $line);

        create($fieldarray[0], $fieldarray[1], $fieldarray[2], $fieldarray[3], $fieldarray[4], 'Alton541.6681136');

        $createdAccountCount++;
    }

    return $createdAccountCount;
}

	
	
/***************************************************************************************/
function displayform($confirmationmsg)
{
    // Display confirmation mesaage if available
    //if($confirmationmsg != '')
            echo '<h2>' . $confirmationmsg . '</h2>';

    // Present the form
    echo 'Comma delimited users. Line format: username,firstname,lastname,email,neesaffiliation';
    echo '	<br/><br/>';
    echo '<form method=post>';
    echo '	<textarea name="users-csv" cols="64" rows="16"></textarea>';
    echo '	<br/><br/>';
    echo '  Import Password:<input type="text" name="password">';
    echo '	<br/><br/>';
    echo '	<input type="hidden" name="formsubmit" value="1">';
    echo '	<br/><br/>';
    echo '	<input type="submit">';
    echo '</form>';
}





/***************************************************************************************/
function create($username, $firstname, $lastname, $email, $neesaffiliation, $pw) {

    global $SuccessfulCreations;
    global $ErrorCount;


    $acl = & JFactory::getACL();

    //Create a new Joomla user
    $target_juser = new JUser();
    $target_juser->set('id', 0);
    $target_juser->set('name', $firstname . ' ' . $lastname);
    $target_juser->set('username', strtolower($username));
    $target_juser->set('password_clear', '');
    $target_juser->set('email', $email);
    $target_juser->set('gid', $acl->get_group_id('', 'Registered'));
    $target_juser->set('usertype', 'Registered');
    $savejuser = $target_juser->save();

    if (!$savejuser)
    {
        echo '<br/> juser->save() error for : ' . $username . ' ';
        echo $target_juser->getError();
        $ErrorCount++;
        return;
    }
    else
    {

        // Attempt to get the new user
        $xprofile = XProfile::getInstance($target_juser->get('id'));
        $result = is_object($xprofile);

        // Did we successfully create an account?
        if ($result) {
            $xprofile->loadRegistration($xregistration);
            $xprofile->set('homeDirectory', '/home/neeshub/' . $xprofile->get('username'));
            $xprofile->set('jobsAllowed', 5);
            $xprofile->set('regIP', $_SERVER['REMOTE_ADDR']);
            $xprofile->set('emailConfirmed', -rand(1, pow(2, 31) - 1));

            if (isset($_SERVER['REMOTE_HOST'])) {
                $xprofile->set('regHost', $_SERVER['REMOTE_HOST']);
            }
            $xprofile->set('registerDate', date('Y-m-d H:i:s'));
            $xprofile->set('proxyUidNumber', $target_juser->get('id'));
            $xprofile->set('emailConfirmed', 1);

            // We'll probably comment these out, the password's will be  synced up via
            // ldap update
            //$xprofile->set('password', $pw);
            //$xprofile->set('proxyPassword', $pw);

            // Not entirely sure how to deal with this, do we need to have imported users
            // re-agree to our usageAgreement?
            $xprofile->set('usageAgreement', 'TRUE');

            // Update the account
            $result = $xprofile->update();

            Hubzero_Users_Password::changePassword($username, $pw);

            // NeesHub p1 - Insert jos_neesprofile row for extended NEES profile info
            $neesprofile = new XNeesProfile();
            $neesprofile->set('uid', $target_juser->get('id'));
            $neesprofile->set('NeesAffiliation', $neesaffiliation);

            if ($neesprofile->create() == false) {
                echo '<br/>neesprofile error for : ' . $username;
                $ErrorCount++;
                return;
            }


        }
        else
        {
            echo '<br/> xprofile->update() error for : ' . $username . ' ';
            echo $xprofile->getError();
            $ErrorCount++;
            return;
        }

    }
 
    $SuccessfulCreations++;



}
?>
