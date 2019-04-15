<?php
/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * Turn querystring parameters into an SEF route
 *
 * @param  array &$query Querystring bits
 * @return array
 */
function dataviewerBuildRoute(&$query)
{
	$segments = array();

	return $segments;
}

/**
 * Parse a SEF route
 *
 * @param  array $segments Exploded SEF URL
 * @return array
 */
function dataviewerParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	$vars['task'] = isset($segments[0]) ? $segments[0] : 'view';
	$vars['db']   = isset($segments[1]) ? $segments[1] : false;
	$vars['dv']   = isset($segments[2]) ? $segments[2] : false;

	return $vars;
}
