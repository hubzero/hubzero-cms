<?php
/**
 * Copyright 2010-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

$config = JFactory::getConfig();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

mb_internal_encoding('UTF-8');

jimport('joomla.application.component.helper');

require_once(JPATH_COMPONENT . DS . 'controller.php');
require_once(JPATH_COMPONENT . DS . 'dv_config.php');
require_once(JPATH_COMPONENT . DS . 'util/db.php');
require_once(JPATH_COMPONENT . DS . 'util/dl.php');

$document = &JFactory::getDocument();

global $html_path, $com_name;

$html_path = str_replace(JPATH_BASE, '', JPATH_COMPONENT) . '/html';
$com_name = str_replace(JPATH_BASE.'/components/', '', JPATH_COMPONENT);
$com_name = str_replace('com_', '' , $com_name);
$_SESSION['dv']['settings']['com_name'] = $com_name;

$is_debug = false;
$min = '.min';

if ($is_debug) {
	$min = '';
}

$document->addScript($html_path . '/util.js');

$document->addScript($html_path . '/excanvas' . $min . '.js');

$document->addScript($html_path . '/jquery' . $min . '.js');
$document->addScript($html_path . '/ui/jquery-ui' . $min . '.js');
$document->addStyleSheet($html_path . '/ui/themes/smoothness/jquery-ui.css');

$document->addScript($html_path . '/dataTables/jquery.dataTables' . $min . '.js');
$document->addStyleSheet($html_path . '/dataTables/jquery.dataTables.css');

$document->addScript($html_path . '/jquery.tipsy.js');
$document->addStyleSheet($html_path . '/jquery.tipsy.css');

$document->addScript($html_path . '/jqplot/jquery.jqplot.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.canvasTextRenderer' . $min . '.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.canvasAxisLabelRenderer' . $min . '.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.enhancedLegendRenderer' . $min . '.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.canvasAxisTickRenderer' . $min . '.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.categoryAxisRenderer' . $min . '.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.barRenderer' . $min . '.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.highlighter' . $min . '.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.cursor' . $min . '.js');
$document->addScript($html_path . '/jqplot/plugins/jqplot.pointLabels' . $min . '.js');
$document->addStyleSheet($html_path . '/jqplot/jquery.jqplot.css');

$document->addScript($html_path . '/jquery.highlight.js');

$document->addScript($html_path . '/spreadsheet.js');
$document->addStyleSheet($html_path . '/spreadsheet.css');

$document->addScript($html_path . '/dv_charts.js');
$document->addScript($html_path . '/dv_maps.js');


// Sanitizing GET and POST
global $C_GET, $C_POST;
$C_GET = $_GET;
$C_POST = $_POST;
$C_REQUEST = $_REQUEST;

db_clean_input($C_GET);
db_clean_input($C_POST);
db_clean_input($C_REQUEST);

// Instantiate controller
$controller = new Controller();
$mainframe = &JFactory::getApplication();
$controller->mainframe = $mainframe;
$controller->execute();
$controller->redirect();
?>
