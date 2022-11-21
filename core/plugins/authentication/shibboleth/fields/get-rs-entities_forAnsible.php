<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (PHP_SAPI !== 'cli') {
	exit();
}

$mdSource = 'https://md.incommon.org/InCommon/InCommon-metadata.xml';
$cache = '/www/tmp/incommon-rs-entities.json';

/**
 * Get a list of research and scholarship IDs
 */
$ch = curl_init();
// fetch the latest InCommon metadata, if needed
curl_setopt($ch, \CURLOPT_URL, $mdSource);
curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, \CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, \CURLOPT_TIMEOUT, 2);
curl_setopt($ch, \CURLOPT_HTTPHEADER, []);
curl_setopt($ch, \CURLOPT_TIMEOUT, 60);
$xml = curl_exec($ch);

if (!$xml) {
	echo '[]';
	exit();
}

$xp = new \SimpleXMLElement($xml);
foreach ($xp->getNamespaces(true) as $name => $url)
{
	$xp->registerXPathNamespace($name ? $name : 'base', $url);
}
$rv = [];
// select entities having the saml attribute indicating that they are research & scholarship category members
// being members ourselves, we can get attributes about users released fromt these entities
foreach ($xp->xpath('//base:EntityDescriptor[
	base:Extensions/
		mdattr:EntityAttributes/
			saml:Attribute[attribute::Name="http://macedir.org/entity-category-support"]/
				saml:AttributeValue[text()="http://id.incommon.org/category/research-and-scholarship" or text()="http://refeds.org/category/research-and-scholarship"]
	]') as $entity)
{
	// easier to work with as an array, the SimpleXMLElement class is bizarre
	$entity = (array)$entity;
	$id = $entity['@attributes']['entityID'];
	$title = $xp->xpath('//base:EntityDescriptor[attribute::entityID="'.$id.'"]//mdui:DisplayName');
	if (isset($title[0])) {
		$title = (string)$title[0];
	}
	else {
		continue;
	}
	preg_match('/([^.:]+[.][^.]+?)(?:[\/]|$)/', $id, $ma);
	$host = $ma[1];
	// logos no longer fetched b/c the result was ugly. if you want to experiment, go for it
//			$logo = $xp->xpath('//base:EntityDescriptor[attribute::entityID="'.$id.'"]//mdui:Logo');
//			$logo = isset($logo[0]) ? (string)$logo[0] : 'https://'.$host.'/favicon.ico';
        echo "{\n";
        echo '    "entity_id": "';
        echo $id . '",' . "\n";
	echo '    "label": "';
	echo $title . '",' . "\n";
	echo '    "host": "';
	echo $host . '"' . "\n";
	echo "},\n";
	$rv[] = [
		'entity_id' => $id,
		'label'     => $title,
		'host'      => $host
//				'logo'      => $logo
	];
}
#  $rv = json_encode($rv);
# file_put_contents($cache, $rv);
# echo $rv;
