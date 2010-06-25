<?php 
/**
 * $Id: uninstall.xmap.php 32 2009-05-18 20:00:14Z guilleva $
 * $LastChangedDate: 2009-05-18 14:00:14 -0600 (lun, 18 may 2009) $
 * $LastChangedBy: guilleva $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' ); 

/**
 * Uninstall routine for Xmap.
 * Drops the settings table from the Joomla! database
 * @author Daniel Grothe
 * @see XmapConfig.php
 * @package Xmap_Admin
 * @version $Id: uninstall.xmap.php 32 2009-05-18 20:00:14Z guilleva $
 */
function com_uninstall() {
	require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xmap'.DS.'classes'.DS.'XmapConfig.php' );
	XmapConfig::remove();
}
