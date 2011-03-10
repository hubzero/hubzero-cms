<?php // $Id: version.php,v 1.39.2.4 2009/03/06 18:44:54 mark-nielsen Exp $
/**
 * Code fragment to define the version of lesson
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @version $Id: version.php,v 1.39.2.4 2009/03/06 18:44:54 mark-nielsen Exp $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

$module->version  = 2008112601;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2007101509;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

?>
