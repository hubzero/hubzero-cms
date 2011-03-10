<?php  //$Id: settings.php,v 1.3.2.2 2008/11/26 20:58:03 skodak Exp $
$ADMIN->add('reports', new admin_externalpage('reportunittest', get_string('simpletest', 'admin'), "$CFG->wwwroot/$CFG->admin/report/unittest/index.php", 'report/unittest:view'));
?>