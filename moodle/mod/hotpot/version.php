<?PHP // $Id: version.php,v 1.48.2.5 2009/03/16 01:46:54 gbateson Exp $
/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of hotpot
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////
$module->version  = 2007101513;   // release date of this version (see note below)
$module->release  = 'v2.4.13';    // human-friendly version name (used in mod/hotpot/lib.php)
$module->requires = 2007101509;  // Requires this Moodle version
$module->cron     = 0;            // period for cron to check this module (secs)
// interpretation of YYYYMMDDXY version numbers
//     YYYY : year
//     MM   : month
//     DD   : day
//     X    : point release version 1,2,3 etc
//     Y    : increment between point releases
?>
