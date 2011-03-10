<?php  // $Id: settings.php,v 1.1.2.3 2008/11/29 14:30:58 skodak Exp $
$ADMIN->add('reports', new admin_externalpage('reportcourseoverview', get_string('courseoverview', 'admin'), "$CFG->wwwroot/$CFG->admin/report/courseoverview/index.php",'report/courseoverview:view'));
?>