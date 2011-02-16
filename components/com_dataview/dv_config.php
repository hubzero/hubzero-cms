<?php
/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$_SESSION['dv']['settings']['num_rows'] = array('labels'=>array(5, 10, 15, 20, 25, 50, 100), 'values'=>array(5, 10, 15, 20, 25, 50, 100));
$_SESSION['dv']['settings']['limit'] = 10;
$_SESSION['dv']['settings']['serverside'] = false;

global $dv_conf;

require_once(JPATH_BASE . '/' . "hubconfiguration.php");
$hcfg = new HubConfig;
$dv_conf['db'] = array('host'=>$hcfg->databasesHost, 'user'=>$hcfg->databasesUser, 'pass'=>$hcfg->databasesPass, 'name'=>$hcfg->databasesName);

$dv_conf['help_file_base_path'] = '/www/neeshub/site/collections/help-files/';
?>
