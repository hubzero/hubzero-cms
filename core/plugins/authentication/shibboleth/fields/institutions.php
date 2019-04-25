<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

class Institutions extends Field
{
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
		$val = is_array($this->value) ? $this->value : json_decode($this->value, true);

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
	private static function getIdpList($val, $alwaysUpdate = true)
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
				'label'     => null,
				'host'      => null,
				'logo'      => null
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
				//$item['logo'] = $idp->xpath('//mdui:Logo');
				//$item['logo'] = $item['logo'] ? (string)$item['logo'][0] : null;
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
		$rv = [];
		exec('php ' . __DIR__ . '/get-rs-entities.php', $out);
		return json_decode(join('', $out));
	}
}
