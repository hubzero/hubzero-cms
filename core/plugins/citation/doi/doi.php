<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once Component::path('com_publications') . DS . 'tables' . DS . 'version.php';
require_once Component::path('com_publications') . DS . 'models' . DS . 'doi.php';

/**
 * Citation Plugin class for doi
 */
class plgCitationDoi extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * The publication component configuration
	 *
	 * @var object
	 */
	public $_configs = null;

	/**
	 * Update DOI metadata record with citation included
	 *
	 * @return  void
	 */
	public function onCitationAfterSave()
	{
		// Associations and citation from citation component admin interface
		$assocParams = Request::getArray('assocs', array(), 'post');
		$citation = array_map('trim', Request::getArray('citation', array(), 'post'));

		if (!empty($assocParams))
		{
			$doiArr = [];
			$relTypeArr = [];

			foreach ($assocParams as $assocParam)
			{
				if (count(array_unique($assocParam)) != 1)
				{
					if ($assocParam['tbl'] == 'publication' && !empty($assocParam['oid']) && !empty($assocParam['type']))
					{
						// Get doi by publication version id
						$db = App::get('db');
						$objVer = new \Components\Publications\Tables\Version($db);
						$pubId = $objVer->getPubId($assocParam['oid']);
						$objVerList = $objVer->getVersions($pubId, $filters = array('public' => 1));

						// Save doi and context
						foreach ($objVerList as $objV)
						{
							if (($objV->id == $assocParam['oid']) && ($objV->state == 1))
							{
								$doiArr[] = $objV->doi;
								$relTypeArr[$objV->doi] = $assocParam['type'];
								break;
							}
						}
					}

					if (empty($assocParam['tbl']) || empty($assocParam['oid']) || empty($assocParam['type']))
					{
						Notify::warning(Lang::txt('PLG_CITATION_PARAMETER_MISSING'));
					}
				}
			}

			if (!empty($doiArr) && !empty($relTypeArr) && !empty($citation))
			{
				$this->_updateDoiRecord($doiArr, $citation, $relTypeArr);
			}
		}
	}

	/**
	 * Update DOI metadata record with citation information
	 *
	 * @param      array    $doiArr
	 * @param      array    $citation
	 * @param      array    $relationTypeArr
	 *
	 * @return     null
	 */
	protected function _updateDoiRecord($doiArr, $citation, $relationTypeArr)
	{
		$citationInfo = [];
		$citationInfo = $this->_getCitationInfo($citation);

		// Get DOI service configuration information
		$this->getPubConfig();

		if (!empty($citationInfo))
		{
			foreach ($doiArr as $doi)
			{
				$xml = $this->_getDoiXML($doi);

				if ($xml)
				{
					if (!empty($relationTypeArr[$doi]))
					{
						if (!$this->_isCitationUpdated($xml, $citationInfo, $relationTypeArr[$doi]))
						{
							$this->updateDoiMetadataXML($xml, $citationInfo, $relationTypeArr[$doi]);
						}
						else
						{
							if (!$this->isCitationIncluded($xml, $citationInfo, $relationTypeArr[$doi]))
							{
								$this->incCitationInDoiMetadataXML($xml, $citationInfo, $relationTypeArr[$doi]);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Get citation metadata from original citation
	 *
	 * @param   array  $citation
	 * @return  array  $citationArr
	 */
	protected function _getCitationInfo($citation)
	{
		$citationArr = [];

		if (!empty($citation['doi']))
		{
			$citationArr['DOI'] = $citation['doi'];

		}
		else
		{
			if (!empty($citation['url']))
			{
				if (preg_match("/purl/", $citation['url']))
				{
					$citationArr['PURL'] = $citation['url'];
				}
				elseif (preg_match("/handle/", $citation['url']))
				{
					$citationArr['HANDLE'] = $citation['url'];
				}
				elseif (preg_match("/ark/", $citation['url']))
				{
					$citationArr['ARK'] = $citation['url'];
				}
				elseif (preg_match("/arxiv/", $citation['url']))
				{
					$citationArr['ARXIV'] = $citation['url'];
				}
				else
				{
					$citationArr['URL'] = $citation['url'];
				}
			}
		}

		return $citationArr;
	}

	/**
	 * Get DOI metadata XML from DataCite
	 *
	 * @param   array   $doi
	 * @return  mixed   XML or false
	 */
	protected function _getDoiXML($doi)
	{
		if ($this->_configs->dataciteEZIDSwitch == \Components\Publications\Models\Doi::SWITCH_OPTION_DATACITE)
		{
			$url = rtrim($this->_configs->dataciteServiceURL, '/') . '/metadata/' . $doi;

			$ch = curl_init($url);
			$options = array(
				CURLOPT_URL             => $url,
				CURLOPT_USERPWD         => $this->_configs->dataciteUserPW,
				CURLOPT_RETURNTRANSFER  => true,
				CURLOPT_HTTPHEADER      => array('Content-Type:text/plain;charset=UTF-8')
			);

			curl_setopt_array($ch, $options);

			$response = curl_exec($ch);

			$success = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($success === 201 || $success === 200)
			{
				return $response;
			}
			else
			{
				return false;
			}
		}

		return false;
	}

	/**
	 * Check if the DOI metadata record already has the citation
	 *
	 * @param   string   $xml
	 * @param   array    $citation
	 * @param   string   $relationType
	 * @return  boolean
	 */
	protected function isCitationIncluded($xml, $citation, $relationType)
	{
		$dom = new \DomDocument();
		$dom->loadXML($xml);
		$relatedIdentifierNodeList = $dom->getElementsByTagName("relatedIdentifier");
		$key = key($citation);
		$val = current($citation);

		if ($relatedIdentifierNodeList->length != 0)
		{
			foreach ($relatedIdentifierNodeList as $relatedIdentifier)
			{
				if (!empty($relatedIdentifier->getAttribute("relationType"))
				 && ($relatedIdentifier->getAttribute("relationType") == $relationType)
				 && ($relatedIdentifier->getAttribute("relatedIdentifierType") == $key)
				 && ($relatedIdentifier->nodeValue == $val))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check if the citation exists and relationType is set to the given relationType.
	 *
	 * @param   string   $xml
	 * @param   array    $citation
	 * @param   string   $relationType
	 * @return  boolean
	 */
	protected function _isCitationUpdated($xml, $citation, $relationType)
	{
		$dom = new \DomDocument();
		$dom->loadXML($xml);

		$relatedIdentifierNodeList = $dom->getElementsByTagName("relatedIdentifier");

		$key = key($citation);
		$val = current($citation);

		if ($relatedIdentifierNodeList->length != 0)
		{
			foreach ($relatedIdentifierNodeList as $relatedIdentifier)
			{
				if (($relatedIdentifier->getAttribute("relatedIdentifierType") == $key)	&& ($relatedIdentifier->nodeValue == $val))
				{
					if ($relatedIdentifier->getAttribute("relationType") != $relationType)
					{
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Update citation record in DOI metadata xml on DataCite
	 *
	 * @param   string   $xml
	 * @param   array    $citationInfo
	 * @param   string   $relationType
	 * @return  boolean
	 */
	public function updateDoiMetadataXML($xml, $citationInfo, $relationType)
	{
		$xmlStr = '';
		$key = key($citationInfo);
		$val = current($citationInfo);

		$dom = new \DomDocument();
		$dom->loadXML($xml);

		$xpath = new DOMXPath($dom);
		$xpath->registerNamespace('ns', "http://datacite.org/schema/kernel-4");

		$query = "//ns:relatedIdentifier[text()=" . "'" . $val . "'" . " and @relatedIdentifierType=" . "'" . $key . "'" . "]";
		$nodeList = $xpath->query($query);
		$nodeList->item(0)->setAttribute("relationType", $relationType);
		$xmlStr = $dom->saveXML();

		$result = $this->_regMetadata($xmlStr);

		return $result;
	}

	/**
	 * Include citation and update DOI metadata record on DataCite
	 *
	 * @param   string   $xml
	 * @param   array    $citationInfo
	 * @param   string   $relationType
	 * @return  boolean
	 */
	public function incCitationInDoiMetadataXML($xml, $citationInfo, $relationType)
	{
		$xmlStr = '';

		$dom = new \DomDocument();
		$dom->loadXML($xml);
		$relatedIdentifiersNodeList = $dom->getElementsByTagName("relatedIdentifiers");

		if ($relatedIdentifiersNodeList->length != 0)
		{
			foreach ($citationInfo as $key => $citation)
			{
				$relatedIdentifierElement = $dom->createElement("relatedIdentifier", $citation);
				$relatedIdentifierNode = $relatedIdentifiersNodeList->item(0)->appendChild($relatedIdentifierElement);
				$relatedIdentifierNode->setAttribute("relatedIdentifierType", $key);
				$relatedIdentifierNode->setAttribute("relationType", $relationType);
				$xmlStr = $dom->saveXML();
			}
		}
		else
		{
			$resourceElementNodeList = $dom->getElementsByTagName("resource");
			$sizeElementNodeList = $dom->getElementsByTagName("sizes");

			if ($sizeElementNodeList->length != 0)
			{
				$relatedIdentifiersElement = $dom->createElement("relatedIdentifiers");
				$relatedIdentifiersNode = $resourceElementNodeList->item(0)->insertBefore($relatedIdentifiersElement, $sizeElementNodeList->item(0));

				foreach ($citationInfo as $key => $citation)
				{
					$relatedIdentifierElement = $dom->createElement("relatedIdentifier", $citation);
					$relatedIdentifierNode = $relatedIdentifiersNode->appendChild($relatedIdentifierElement);
					$relatedIdentifierNode->setAttribute("relatedIdentifierType", $key);
					$relatedIdentifierNode->setAttribute("relationType", $relationType);
					$xmlStr = $dom->saveXML();
				}
			}
			else
			{
				$formatElementNodeList = $dom->getElementsByTagName("formats");

				if ($formatElementNodeList->length != 0)
				{
					$relatedIdentifiersElement = $dom->createElement("relatedIdentifiers");
					$relatedIdentifiersNode = $resourceElementNodeList->item(0)->insertBefore($relatedIdentifiersElement, $formatElementNodeList->item(0));

					foreach ($citationInfo as $key => $citation)
					{
						$relatedIdentifierElement = $dom->createElement("relatedIdentifier", $citation);
						$relatedIdentifierNode = $relatedIdentifiersNode->appendChild($relatedIdentifierElement);
						$relatedIdentifierNode->setAttribute("relatedIdentifierType", $key);
						$relatedIdentifierNode->setAttribute("relationType", $relationType);
						$xmlStr = $dom->saveXML();
					}
				}
				else
				{
					$versionElementNodeList = $dom->getElementsByTagName("version");

					if ($versionElementNodeList->length != 0)
					{
						$relatedIdentifiersElement = $dom->createElement("relatedIdentifiers");
						$relatedIdentifiersNode = $resourceElementNodeList->item(0)->insertBefore($relatedIdentifiersElement, $versionElementNodeList->item(0));

						foreach ($citationInfo as $key => $citation)
						{
							$relatedIdentifierElement = $dom->createElement("relatedIdentifier", $citation);
							$relatedIdentifierNode = $relatedIdentifiersNode->appendChild($relatedIdentifierElement);
							$relatedIdentifierNode->setAttribute("relatedIdentifierType", $key);
							$relatedIdentifierNode->setAttribute("relationType", $relationType);
							$xmlStr = $dom->saveXML();
						}
					}
				}
			}
		}

		$result = $this->_regMetadata($xmlStr);

		return $result;
	}

	/**
	 * Get DOI service information from publication component configuration
	 *
	 * @param   string  $xml  the DOI metadata xml file
	 * @return  bool    curl command response code
	 */
	protected function _regMetadata($xml)
	{
		$url = rtrim($this->_configs->dataciteServiceURL, '/') . '/' . 'metadata';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->dataciteUserPW);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		curl_exec($ch);

		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return $code == 201 || $code == 200;
	}

	/**
	 * Get DOI service information from publication component configuration
	 *
	 * @return  void
	 */
	public function getPubConfig()
	{
		if (empty($this->_configs))
		{
			$params = Component::params('com_publications');

			$configs = new stdClass;
			$configs->dataciteEZIDSwitch = $params->get('datacite_ezid_doi_service_switch');
			$configs->dataciteServiceURL = $params->get('datacite_doi_service');
			$configs->dataciteUserPW = $params->get('datacite_doi_userpw');

			$this->_configs = $configs;
		}
	}
}
