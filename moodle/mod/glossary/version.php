<?php // $Id: version.php,v 1.60.2.1 2008/03/03 11:48:40 moodler Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of glossary
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2007101509;
$module->requires = 2007101509;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

?>
