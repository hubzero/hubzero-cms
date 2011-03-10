<?php  // $Id: log.php,v 1.15.2.1 2008/05/02 04:07:30 dongsheng Exp $
       // log.php - old scheduled backups report. Now redirecting
       // to the new admin one

    require_once("../config.php");

    require_login();

    require_capability('moodle/site:backup', get_context_instance(CONTEXT_SYSTEM));

    redirect("$CFG->wwwroot/$CFG->admin/report/backups/index.php", '', 'admin', 1);

?>
