<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (PHP_SAPI !== 'cli') {
	exit();
}

$mdPath = '/etc/shibboleth/metadata/federation-metadata.xml';
$mdSource = 'https://md.incommon.org/InCommon/InCommon-metadata.xml';
$cache = '/www/tmp/incommon-rs-entities.json';


if (!($xml = simplexml_load_file($mdPath)))
{
	print "Failed to parse XML from this path\n";
}

// $xml->registerXPathNamespace('shib', 'urn:mace:shibboleth:2.0:native:sp:config');
// print_r($xml->xpath('//shib:SSO'));
$xml->registerXPathNamespace('shib', 'urn:oasis:names:tc:SAML:2.0:metadata');
// print_r($xml->xpath('//shib:EntityDescriptor'));

// exit();

$curl = curl_init();
$rv = array();
foreach ($xml->xpath('//shib:EntityDescriptor') as $idp)
{
	print (string) $idp->Organization->OrganizationDisplayName;
	print "\n";
}
