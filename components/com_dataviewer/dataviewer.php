<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2012,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

mb_internal_encoding('UTF-8');

jimport('joomla.application.component.helper');

require_once(JPATH_COMPONENT . DS . 'controller.php');
require_once(JPATH_COMPONENT . DS . 'dv_config.php');

require_once(JPATH_COMPONENT . DS . 'lib' . DS. 'html.php');

require_once(JPATH_COMPONENT . DS . 'lib/db.php');
require_once(JPATH_COMPONENT . DS . 'lib/dl.php');

$document =  JFactory::getDocument();

global $html_path, $com_name, $dv_conf;

$html_path = str_replace(JPATH_BASE, '', JPATH_COMPONENT) . '/html';
$com_name = str_replace(JPATH_BASE.'/components/', '', JPATH_COMPONENT);
$com_name = str_replace('com_', '' , $com_name);
$dv_conf['settings']['com_name'] = $com_name;

controller();
return;
?>
