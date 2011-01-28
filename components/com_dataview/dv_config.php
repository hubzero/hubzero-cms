<?php
/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$_SESSION['dv']['settings']['num_rows'] = array('labels'=>array(5, 10, 15, 20, 25, 50, 100, 1000), 'values'=>array(5, 10, 15, 20, 25, 50, 100, 1000));
$_SESSION['dv']['settings']['limit'] = 10;
$_SESSION['dv']['settings']['serverside'] = false;

global $dv_conf;
$dv_conf['db'] = array('host'=> 'nees.org', 'user'=>'nistequser', 'pass' => '_nist3QkE_', 'name' => 'nistearthquakedata');
$dv_conf['help_file_base_path'] = '/www/neeshub/site/collections/help-files/';
?>
