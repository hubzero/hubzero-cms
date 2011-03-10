<?php   // $Id: version.php,v 1.3 2007/08/31 04:05:13 moodler Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of the note module
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$note_version  = 2007070700;  // The current version of note module (Date: YYYYMMDDXX)
$module->cron     = 1800;           // Period for cron to check this module (secs)
