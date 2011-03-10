<?php // $Id: add.php,v 1.6.2.2 2008/11/30 19:25:50 skodak Exp $

    require_once('../config.php');

    $courseid      = required_param('course', PARAM_INT);
    $userid        = required_param('user', PARAM_INT);

    redirect("edit.php?courseid=$courseid&amp;userid=$userid");

    //note: this script is not used anymore - removed from HEAD
?>
