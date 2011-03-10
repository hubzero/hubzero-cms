<?PHP // 

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of feedback
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2005100305;  // The current module version (Date: YYYYMMDDXX)
$feedback_version_intern = 1;   //this version is used for restore older backups
$module->cron     = 0;           // 1 hour Period for cron to check this module (secs)

?>
