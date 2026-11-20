<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (PHP_SAPI !== 'cli') {
    exit();
}

$mdPath = '/etc/shibboleth/metadata/federation-metadata.xml';
$mdSource = 'https://md.incommon.org/InCommon/InCommon-metadata.xml';
$cache = '/www/tmp/incommon-rs-entities.json';


if (!($xml = simplexml_load_file($mdPath))) {
    print "Failed to parse XML from this path\n";
    exit(1);
}

$xml->registerXPathNamespace('shib', 'urn:oasis:names:tc:SAML:2.0:metadata');

$dname = "";
$curl = curl_init();
$done = [];
$found = null;
$rc = null;
$ma = null;
foreach ($xml->xpath('//shib:EntityDescriptor') ?: [] as $idp) {
    // Is this an institution that was selected?
    $dname = (string) $idp->Organization->OrganizationDisplayName;
    // print $dname;
    if (str_contains($dname, '"')) {
        // print "error, name ".$dname." contains a quote\n";
        continue;
    }
    exec('grep -q -i ' . escapeshellarg($dname) . ' /root/tmp/cando.txt', $found, $rc);
    if ($rc) {
        // print "skipping\n";
        continue;
    } else {
        // print "got one\n";
    }

    // output metadata only once even if there are duplicates, variants or conflicting entries
    // print $done[$dname];
    if (isset($done[$dname])) {
        // print "already done\n";
        continue;
    } else {
        $done[$dname] = 1;
    }


    $entity = (array) $idp;
    $id = $entity['@attributes']['entityID'];

    echo "{\n";
    echo '    "entity_id": "';
    echo $id . '",' . "\n";

    echo '    "label": "';
    echo (string) $idp->Organization->OrganizationDisplayName;
    echo '",' . "\n";

    preg_match('/([^.:]+[.][^.]+?)(?:[\/]|$)/', $id, $ma);
    $host = isset($ma[1]) ? $ma[1] : '';
    echo '    "host": "';
    echo $host;
    echo '"' . "\n";
    echo "},\n";
}
