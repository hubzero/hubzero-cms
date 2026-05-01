<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

class Institutions extends Field
{
	/**
	 * Append a debug line to a dedicated log file so we can trace
	 * the institutions field read/write path without grepping cmsdebug.log.
	 *
	 * @param   string  $msg
	 * @return  void
	 */
	private static function shibLog($msg)
	{
		$line = '[' . date('Y-m-d H:i:s') . '] [pid ' . getmypid() . '] ' . $msg . "\n";
		$paths = ['/var/log/hubzero/shib-debug.log', PATH_APP . DS . 'tmp' . DS . 'shib-debug.log'];
		foreach ($paths as $p)
		{
			$dir = dirname($p);
			if (is_dir($dir) && is_writable($dir) && @file_put_contents($p, $line, FILE_APPEND | LOCK_EX) !== false)
			{
				break;
			}
		}
		try { \Log::debug('[shib-field] ' . $msg); } catch (\Exception $e) {}
	}

	/**
	 * Get field input
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		Document::addScript('/core/plugins/authentication/shibboleth/assets/js/admin.js');
		Document::addStyleSheet('/core/plugins/authentication/shibboleth/assets/css/admin.css');

		self::shibLog('getInput called; name=' . $this->name
			. ' value-type=' . gettype($this->value)
			. ' value=' . (is_string($this->value) ? $this->value : json_encode($this->value)));

		// Log what's actually stored in the DB row right now, so we can compare
		// against what just got POSTed (if anything).
		try
		{
			$db = \App::get('db');
			$db->setQuery("SELECT params FROM `#__extensions` WHERE folder='authentication' AND element='shibboleth' LIMIT 1");
			$dbParams = $db->loadResult();
			self::shibLog('DB row params=' . $dbParams);
		}
		catch (\Exception $e)
		{
			self::shibLog('DB lookup failed: ' . $e->getMessage());
		}

		// Log what was actually POSTed for this field, if anything (only present
		// when getInput runs after an "apply" task in the same request).
		$posted = \Request::getVar('fields', null, 'post', 'array', 2);
		if (is_array($posted) && isset($posted['params']['institutions']))
		{
			self::shibLog('POSTed fields[params][institutions]=' . (is_string($posted['params']['institutions']) ? $posted['params']['institutions'] : json_encode($posted['params']['institutions'])));
		}
		else
		{
			self::shibLog('no POSTed fields[params][institutions] in this request');
		}

		$val = is_array($this->value) ? $this->value : json_decode($this->value, true);
		if (!$val)
		{
			self::shibLog('value did not decode, using defaults');
			$val = ['xmlPath' => '/etc/shibboleth/metadata/federation-metadata.xml', 'activeIdps' => []];
		}
		if (!isset($val['activeIdps']))
		{
			$val['activeIdps'] = [];
		}
		self::shibLog('decoded activeIdps count=' . count($val['activeIdps'])
			. ' entity_ids=' . json_encode(array_column($val['activeIdps'], 'entity_id')));

		$activeMap = [];
		foreach ($val['activeIdps'] as $idp)
		{
			if (isset($idp['entity_id']))
			{
				$activeMap[$idp['entity_id']] = true;
			}
		}

		$entities = self::loadFederationEntities($val['xmlPath']);

		// Only store xmlPath and activeIdps — never the full entity list
		$storedVal = ['xmlPath' => $val['xmlPath'], 'activeIdps' => $val['activeIdps']];
		$hiddenJson = htmlspecialchars(json_encode($storedVal), ENT_QUOTES, 'UTF-8');
		$xmlPathEsc = htmlspecialchars($val['xmlPath'], ENT_QUOTES, 'UTF-8');
		$nameEsc    = htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8');

		$html   = [];
		$html[] = '<div class="shibboleth">';
		$html[] = '<p class="xml-source"><label>Federation metadata XML path: <input type="text" id="shib-xmlpath" value="' . $xmlPathEsc . '" /></label></p>';
		$html[] = '<input type="hidden" class="serialized" name="' . $nameEsc . '" value="' . $hiddenJson . '" />';

		if (is_array($entities))
		{
			$html[] = '<p><input type="text" id="shib-idp-search" placeholder="Search institutions…" style="width:100%;box-sizing:border-box;margin-bottom:4px" /></p>';
			$html[] = '<div class="idp-list" style="height:400px;overflow-y:scroll;border:1px solid #ccc;padding:4px">';
			foreach ($entities as $entityId => $displayName)
			{
				$checked   = isset($activeMap[$entityId]) ? ' checked' : '';
				$eidEsc    = htmlspecialchars($entityId,   ENT_QUOTES, 'UTF-8');
				$nameDisp  = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
				$html[]    = '<label style="display:block"><input type="checkbox" class="shib-idp-checkbox" value="' . $eidEsc . '" data-label="' . $nameDisp . '"' . $checked . '> ' . $nameDisp . '</label>';
			}
			$html[] = '</div>';
		}
		else
		{
			$html[] = '<p class="warning">' . htmlspecialchars((string)$entities, ENT_QUOTES, 'UTF-8') . '</p>';
		}

		$html[] = '</div>';
		return implode("\n", $html);
	}

	/**
	 * Stream-parse the federation metadata XML and return [entityId => displayName].
	 * Results are cached to app/tmp to avoid re-parsing on every page load.
	 *
	 * @param   string  $xmlPath  Filesystem path to the federation metadata XML
	 * @return  array|string  Associative array on success, error string on failure
	 */
	private static function loadFederationEntities($xmlPath)
	{
		if (!file_exists($xmlPath))
		{
			return 'Federation metadata XML not found: ' . $xmlPath;
		}

		$tmpDir    = \Config::get('tmp_path', PATH_APP . DS . 'tmp');
		$cacheFile = $tmpDir . DS . 'shib_entities_' . md5($xmlPath) . '.json';

		if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($xmlPath))
		{
			$cached = json_decode(file_get_contents($cacheFile), true);
			if (is_array($cached))
			{
				return $cached;
			}
		}

		$reader = new \XMLReader();
		if (!$reader->open($xmlPath))
		{
			return 'Failed to open federation metadata XML: ' . $xmlPath;
		}

		$mduiNs        = 'urn:oasis:names:tc:SAML:metadata:ui';
		$xmlLangNs     = 'http://www.w3.org/XML/1998/namespace';
		$entities      = [];
		$currentId     = null;
		$inIdpSso      = false;
		$captureNext   = false;

		while (@$reader->read())
		{
			$type = $reader->nodeType;

			if ($type === \XMLReader::ELEMENT)
			{
				$local = $reader->localName;

				if ($local === 'EntityDescriptor')
				{
					$currentId   = $reader->getAttribute('entityID');
					$inIdpSso    = false;
					$captureNext = false;
				}
				elseif ($local === 'IDPSSODescriptor')
				{
					$inIdpSso = true;
				}
				elseif ($local === 'DisplayName'
					&& $reader->namespaceURI === $mduiNs
					&& $inIdpSso
					&& $currentId !== null
					&& !isset($entities[$currentId]))
				{
					$lang = $reader->getAttributeNs('lang', $xmlLangNs);
					if ($lang === 'en')
					{
						$captureNext = true;
					}
				}

				if ($reader->isEmptyElement)
				{
					$captureNext = false;
				}
			}
			elseif ($type === \XMLReader::TEXT && $captureNext)
			{
				$entities[$currentId] = trim($reader->value);
				$captureNext          = false;
			}
			elseif ($type === \XMLReader::END_ELEMENT)
			{
				$local = $reader->localName;
				if ($local === 'DisplayName')
				{
					$captureNext = false;
				}
				elseif ($local === 'IDPSSODescriptor')
				{
					$inIdpSso = false;
				}
				elseif ($local === 'EntityDescriptor')
				{
					$currentId   = null;
					$inIdpSso    = false;
					$captureNext = false;
				}
			}
		}

		$reader->close();

		uasort($entities, 'strcasecmp');

		if (is_writable($tmpDir))
		{
			file_put_contents($cacheFile, json_encode($entities));
		}

		return $entities;
	}
}
