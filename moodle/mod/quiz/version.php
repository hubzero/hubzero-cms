<?php // $Id: version.php,v 1.128.2.4 2009/01/14 07:03:14 tjhunt Exp $

////////////////////////////////////////////////////////////////////////////////
//  Code fragment to define the version of quiz
//  This fragment is called by moodle_needs_upgrading() and /admin/index.php
////////////////////////////////////////////////////////////////////////////////

$module->version  = 2007101511;   // The (date) version of this module
$module->requires = 2007101509;   // Requires this Moodle version
$module->cron     = 0;            // How often should cron check this module (seconds)?

?>
