<?php // $Id: version.php,v 1.1 2006/03/10 06:53:01 toyomoyo Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of the blog module
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$blog_version  = 2005030400;  // The current version of blog module (Date: YYYYMMDDXX)
$module->cron     = 1800;           // Period for cron to check this module (secs)
?>
