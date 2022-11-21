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

$xml->registerXPathNamespace('shib', 'urn:oasis:names:tc:SAML:2.0:metadata');

$dname = "";
$curl = curl_init();
$done = array();
foreach ($xml->xpath('//shib:EntityDescriptor') as $idp)
{
	// Is this an institution that was selected?
	$dname = (string) $idp->Organization->OrganizationDisplayName;
	// print $dname;
	if (strpos($dname, '"')!== false) {
		// print "error, name ".$dname." contains a quote\n";
		continue;
	}
	exec('grep -q -i "'.$dname.'" /root/tmp/cando.txt', $found, $rc);
	if ($rc) {
		// print "skipping\n";
		continue;
	} else {
		// print "got one\n";
	}

	// output metadata only once even if there are duplicates, variants or conflicting entries
	// print $done[$dname];
	if ( $done[$dname] == 1 ) {
		// print "already done\n";
		continue;
	} else {
		$done[$dname] = 1;
	}


	$entity = (array)$idp;
	$id = $entity['@attributes']['entityID'];

	echo "{\n";
	echo '    "entity_id": "';
	echo $id . '",' . "\n";

	echo '    "label": "';
	echo (string) $idp->Organization->OrganizationDisplayName;
	echo '",' . "\n";

	preg_match('/([^.:]+[.][^.]+?)(?:[\/]|$)/', $id, $ma);
	$host = $ma[1];
	echo '    "host": "';
	echo $host;
	echo '"' . "\n";
	echo "},\n";
}
