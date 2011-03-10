<?php // $Id: version.php,v 1.17.2.2 2008/07/11 02:54:54 moodler Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of label
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2007101510;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2007101509;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

?>
