<?php  //$Id: settings.php,v 1.1.2.2 2009/01/15 20:16:13 skodak Exp $

$ADMIN->add('reports', new admin_externalpage('reportsecurity', get_string('reportsecurity', 'report_security'), "$CFG->wwwroot/$CFG->admin/report/security/index.php",'report/security:view'));
