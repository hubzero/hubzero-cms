<?php
/**
 * Minimal bootstrap for testing plgAuthenticationOrcid's pure helpers.
 *
 * The plugin class extends \Hubzero\Plugin\OauthClient, which is autoloaded
 * from core/vendor. The helpers under test do not touch Session, User, App,
 * or the ORCID SDK — only pure string manipulation of the ORCID iD.
 */

if (!defined('_HZEXEC_'))
{
	define('_HZEXEC_', true);
}

require __DIR__ . '/../../../../vendor/autoload.php';
require __DIR__ . '/../orcid.php';
