<?php // $Id: mod.php,v 1.7.2.3 2008/11/29 14:31:00 skodak Exp $

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if (has_capability('coursereport/participation:view', $context)) {
        echo '<p>';
        $participationreport = get_string('participationreport');
        echo "<a href=\"{$CFG->wwwroot}/course/report/participation/index.php?id={$course->id}\">";
        echo "$participationreport</a>\n";
        echo '</p>';
    }
?>
