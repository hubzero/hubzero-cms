<?php  // $Id: settings.php,v 1.1.2.2 2008/11/26 20:58:04 skodak Exp $
$ADMIN->add('reports', new admin_externalpage('reportbackups', get_string('backups', 'admin'), "$CFG->wwwroot/$CFG->admin/report/backups/index.php",'moodle/site:backup'));
?>