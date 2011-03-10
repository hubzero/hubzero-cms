<?php  // $Id: mod.php,v 1.4.4.2 2008/11/29 14:30:55 skodak Exp $

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if (has_capability('coursereport/outline:view', $context)) {
        echo '<p>';
        $activityreport = get_string( 'activityreport' );
        echo "<a href=\"{$CFG->wwwroot}/course/report/outline/index.php?id={$course->id}\">";
        echo "$activityreport</a>\n";
        echo '</p>';
    }
?>