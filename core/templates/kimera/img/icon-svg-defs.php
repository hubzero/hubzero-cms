<?php
/*
 * This file can contain template specific  icon definitions.
 * Just include the icons from the core in this template
 *
 */

$coreSVGdefs = $_SERVER['DOCUMENT_ROOT'] . '/core/assets/images/icons/svg-icon-definitions.php';
if (file_exists($coreSVGdefs))
{
	include($_SERVER['DOCUMENT_ROOT'] . '/core/assets/images/icons/svg-icon-definitions.php');
}
