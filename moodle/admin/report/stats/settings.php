<?php  // $Id: settings.php,v 1.2.2.3 2008/11/29 14:30:55 skodak Exp $
// just a link to course report
$ADMIN->add('reports', new admin_externalpage('reportstats', get_string('stats', 'admin'), "$CFG->wwwroot/course/report/stats/index.php", 'coursereport/stats:view'));
?>