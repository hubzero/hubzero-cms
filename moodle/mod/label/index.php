<?php // $Id: index.php,v 1.4 2005/10/17 07:59:47 gustav_delius Exp $

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);   // course

    redirect("$CFG->wwwroot/course/view.php?id=$id");

?>
