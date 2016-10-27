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


jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldInstitutions extends JFormField
{
	/**
	 * Metadata
	 *
	 * @var  string
	 */
	private static $mdSource = 'https://wayf.incommonfederation.org/InCommon/InCommon-metadata.xml';

	/**
	 * Fallback metadata
	 *
	 * @var  string
	 */
	private static $mdDest = '/www/tmp/InCommon-metadata-fallback.xml';

	/**
	 * Get field input
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		Document::addScript('/core/plugins/authentication/shibboleth/assets/js/admin.js');
		// Commented out due to interfering with admin styles
		//Document::addStyleSheet('/core/plugins/authentication/shibboleth/assets/css/jquery-ui.css');
		Document::addStyleSheet('/core/plugins/authentication/shibboleth/assets/css/admin.css');

		$html = array();
		$a = function($str)
		{
			return str_replace('"', '&quot;', $str);
		};
		$val = is_array($this->value) ? $this->value : json_decode($this->value, TRUE);
		$html[] = '<div class="shibboleth" data-iconify="'.$a(preg_replace('#^'.preg_quote(PATH_CORE).'#', '', __FILE__)).'">';
		$html[] = '<p class="xml-source"><label>Shibboleth ID provider configuration file: <input type="text" name="xmlPath" value="'.$a($val['xmlPath']).'" /></label></p>';
		list($val['xmlRead'], $val['idps']) = self::getIdpList($val);
		$html[] = '<p class="info">Save your changes to retry loading ID providers from this file</p>';
		$html[] = '<input type="hidden" class="serialized" name="' . $this->name . '" value="' . $a(json_encode($val)) . '" />';
		$html[] = '</div>';
		// rest of the form is managed on the client side
		return implode("\n", $html);
	}

	/**
	 * Get Ipd list
	 *
	 * @param   string   $val
	 * @param   boolean  $alwaysUpdate
	 * @return  array
	 */
	private static function getIdpList($val, $alwaysUpdate = TRUE)
	{
		// list is up to date
		if (!file_exists($val['xmlPath']))
		{
			return array(null, 'Invalid XML path');
		}
		if (($mtime = $val['xmlPath'] . ':' . filemtime($val['xmlPath'])) == $val['xmlRead'] && !$alwaysUpdate)
		{
			return array($mtime, $val['idps']);
		}
		if (!($xml = simplexml_load_file($val['xmlPath'])))
		{
			return array(null, 'Failed to parse XML from this path');
		}
		$xml->registerXPathNamespace('shib', 'urn:mace:shibboleth:2.0:native:sp:config');

		$curl = curl_init();
		$rv = array();
		foreach ($xml->xpath('//shib:SSO') as $item)
		{
			$entityId = (string)$item->attributes()->entityID;
			curl_setopt($curl, CURLOPT_URL, $entityId);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$item = array(
				'entity_id' => $entityId,
				'label'    => NULL,
				'host'     => NULL,
				'logo'     => NULL
			);
			if (!($idp = curl_exec($curl)))
			{
				$item['error'] = 'Failed to fetch metadata';
			}
			else if (!($idp = simplexml_load_string($idp)))
			{
				$item['error'] = 'Failed to parse metadata';
			}
			else
			{
				$idp->registerXPathNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:metadata');
				// look in a few places for the display name
				foreach (array(
					'//mdui:DisplayName',        // most preferred, ui extension offers prefs for this specific use case
					'//saml:OrganizationDisplayName', // good fallback
					'//saml:OrganizationName'         // ok fallback
				) as $xp)
				{
					if (($name = $idp->xpath($xp)))
					{
						$item['label'] = (string)$name[0];
						break;
					}
				}
				if (($orgUrl = $idp->xpath('//saml:OrganizationURL')))
				{
					$item['host'] = preg_replace('/^.*[.]([^.]+[.][^.]+)$/', '$1', parse_url($orgUrl[0], \PHP_URL_HOST));
				}
//				$item['logo'] = $idp->xpath('//mdui:Logo');
//				$item['logo'] = $item['logo'] ? (string)$item['logo'][0] : NULL;
			}
			$rv[] = $item;
		}
		$rv = array_merge($rv, self::getResearchAndScholarshipIdps($curl));
		curl_close($curl);
		return array($mtime, $rv);
	}

	/**
	 * Get a list of research and scholarship IDs
	 *
	 * @param   string  $ch
	 * @return  array
	 */
	private static function getResearchAndScholarshipIdps($ch)
	{
		// fetch the latest InCommon metadata, if needed
		curl_setopt($ch, CURLOPT_URL, self::$mdSource);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		if (file_exists(self::$mdDest))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['If-Modified-Since: '.gmdate('D, d M Y H:i:s \G\M\T', filemtime(self::$mdDest))]);
			$xml = curl_exec($ch);
			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 304)
			{
				$xml = file_get_contents(self::$mdDest);
			}
			else
			{
				file_put_contents(self::$mdDest, $xml);
			}
		}
		else
		{
			$xml = curl_exec($ch);
			file_put_contents(self::$mdDest, curl_exec($ch));
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, []);

		$xp = new \SimpleXMLElement($xml);
		foreach ($xp->getNamespaces(TRUE) as $name => $url)
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
//			$logo = $xp->xpath('//base:EntityDescriptor[attribute::entityID="'.$id.'"]//mdui:Logo');
//			$logo = isset($logo[0]) ? (string)$logo[0] : 'https://'.$host.'/favicon.ico';
			$rv[] = [
				'entity_id' => $id,
				'label'     => $title,
				'host'      => $host
//				'logo'      => $logo
			];
		}
		return $rv;
	}
}
