<?php
/**
 * @version		$Id: router.php 11002 2008-10-07 01:12:20Z ian $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * @param	array
 * @return	array
 */
function SearchBuildRoute( &$query )
{
	$segments = array();

	if (isset($query['searchword'])) {
		$segments[] = $query['searchword'];
		unset($query['searchword']);
	}

	// Retrieve configuration options - needed to know which SEF URLs are used
	$app =& JFactory::getApplication();
	// Allows for searching on strings that include ".xxx" that appear to Apache as an extension
	if (($app->getCfg('sef')) && ($app->getCfg('sef_rewrite')) && !($app->getCfg('sef_suffix'))) {
		$segments[] .= '/';
	}

	if (isset($query['view'])) {
		unset($query['view']);
	}
	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function SearchParseRoute( $segments )
{
	$vars = array();

	$searchword	= array_shift($segments);
	$vars['searchword'] = $searchword;
	$vars['view'] = 'search';

	return $vars;
}