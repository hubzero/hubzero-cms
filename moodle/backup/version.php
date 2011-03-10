<?php  //$Id: version.php,v 1.114.2.8 2009/11/17 05:41:51 moodler Exp $

/// This file defines the current version of the
/// backup/restore code that is being used.  This can be
/// compared against the values stored in the
/// database (backup_version) to determine whether upgrades should
/// be performed (see db/backup_*.php)

    $backup_version = 2009111300;   // The current version is a date (YYYYMMDDXX)
    $backup_release = '1.9.7';     // User-friendly version number

?>
