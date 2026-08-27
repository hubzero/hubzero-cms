<?php
/**
 * Minimal bootstrap for testing plgUserHubzero's pure helpers.
 *
 * The plugin class extends \Hubzero\Plugin\Plugin, which is autoloaded
 * from core/vendor. The helpers under test do not touch App, Session,
 * User, or mail — only inspect the user array shape.
 */

if (!defined('_HZEXEC_'))
{
	define('_HZEXEC_', true);
}

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../hubzero.php';
