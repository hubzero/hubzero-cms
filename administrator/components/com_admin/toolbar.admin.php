<?php
/**
 * @version		$Id: toolbar.admin.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	Admin
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ($task)
{
	case 'sysinfo':
		TOOLBAR_admin::_SYSINFO();
		break;

	case 'help':
		TOOLBAR_admin::_HELP();
		break;

	case 'preview':
	case 'preview2':
		TOOLBAR_admin::_PREVIEW();
		break;

	default:
		if ($task) {
			TOOLBAR_admin::_DEFAULT();
		} else {
			TOOLBAR_admin::_CPANEL();
		}
		break;
}