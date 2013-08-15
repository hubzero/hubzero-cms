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


function dataviewerParseRoute($segments) {
	$vars = array();

	if (empty($segments)) {
		return $vars;
	}

	$vars['task'] =  isset($segments[0]) ? $segments[0] : 'view';
	$vars['db'] = isset($segments[1]) ? $segments[1] : false;
	$vars['dv'] = isset($segments[2]) ? $segments[2] : false;

	return $vars;
}

