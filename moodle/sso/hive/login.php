<?php  // $Id: login.php,v 1.4 2007/10/09 21:43:30 iarenaza Exp $
       // login.php - action of the login form put up by expired.php.

    require('../../config.php');

    require('lib.php');

    require_login();

    // get the login data 
    $frm = data_submitted('');

    // log back into Hive
    if (sso_user_login($frm->username, $frm->password)) {  

        /// reopen Hive
        redirect($CFG->wwwroot.'/mod/resource/type/repository/hive/openlitebrowse.php');
    } else {
        redirect($CFG->wwwroot.'/sso/hive/expired.php');
    }

?>
