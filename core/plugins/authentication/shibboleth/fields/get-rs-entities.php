<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steven Snyder <snyder13@purdue.edu>
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
if (file_exists($cache))
{
	curl_setopt($ch, \CURLOPT_HTTPHEADER, ['If-Modified-Since: '.gmdate('D, d M Y H:i:s \G\M\T', filemtime($cache))]);
	$xml = curl_exec($ch);
	if (curl_getinfo($ch, \CURLINFO_HTTP_CODE) == 304)
	{
		echo file_get_contents($cache);
		exit();
	}
}
else
{
	$xml = curl_exec($ch);
}

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
	$rv[] = [
		'entity_id' => $id,
		'label'     => $title,
		'host'      => $host
//				'logo'      => $logo
	];
}
$rv = json_encode($rv);
file_put_contents($cache, $rv);
echo $rv;
