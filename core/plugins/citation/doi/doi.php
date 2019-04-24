<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2019 HUBzero Foundation, LLC.
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
 * @author    Hubzero
 * @copyright Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Citation Plugin class for doi
 */
class plgCitationDoi extends \Hubzero\Plugin\Plugin
{
	/**
	 * The publication component configuration
	 *
	 * @var object
	 */
	public $_configs = null;

	/**
	 * DataCite and EZID switch options
	 *
	 * @const
	 */
	const SWITCH_OPTION_NONE = 0;
	const SWITCH_OPTION_EZID = 1;
	const SWITCH_OPTION_DATACITE = 2;

	/**
	 * Update DOI metadata record with citation included
	 *
	 * @param      	null
	 *
	 * @return		null
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
				if (!empty($assocParam))
				{
					if ($assocParam['tbl'] == 'publication' && !empty($assocParam['doi']) && !empty($assocParam['type']))
					{
						$doiArr[] = $assocParam['doi'];
						$relTypeArr[$assocParam['doi']] = $assocParam['type'];
					}

					if (!empty($assocParam['tbl']) && !empty($assocParam['oid']) && empty($assocParam['doi']) && empty($assocParam['type']))
					{
						Notify::warning("Both DOI and Context of publication " . $assocParam['oid'] . " are not set, so the citation won't be pushed to DataCite");
					}
					elseif (!empty($assocParam['tbl']) && !empty($assocParam['oid']) && empty($assocParam['doi']) && !empty($assocParam['type']))
					{
						Notify::warning("DOI of publication " . $assocParam['oid'] . " is not set, so the citation won't be pushed to DataCite");
					}
					elseif (!empty($assocParam['tbl']) && !empty($assocParam['oid']) && !empty($assocParam['doi']) && empty($assocParam['type']))
					{
						Notify::warning("Context of publication " . $assocParam['doi'] . " is not set, so the citation won't be pushed to DataCite");
					}
				}
			}

			if (!empty($doiArr) && !empty($relTypeArr) && !empty($citation))
			{
				$this->updateDoiRecord($doiArr, $citation, $relTypeArr);
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
	protected function updateDoiRecord($doiArr, $citation, $relationTypeArr)
	{
		$citationInfo = [];
		$citationInfo = $this->getCitationInfo($citation);

		// Get DOI service configuration information
		$this->getPubConfig();

		if (!empty($citationInfo))
		{
			foreach ($doiArr as $doi)
			{
				$xml = $this->getDoiXML($doi);

				if ($xml)
				{
					if (!empty($relationTypeArr[$doi]))
					{
						if (!$this->isCitationUpdated($xml, $citationInfo, $relationTypeArr[$doi]))
						{
							file_put_contents("/tmp/1", "The xml of $doi is going to be updated", FILE_APPEND);
							$this->updateDoiMetadataXML($xml, $citationInfo, $relationTypeArr[$doi]);
						}
						else
						{
							if (!$this->isCitationIncluded($xml, $citationInfo, $relationTypeArr[$doi]))
							{
								file_put_contents("/tmp/1", "The xml of $doi is going to be included", FILE_APPEND);
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
	 * @param      array    $citation
	 *
	 * @return     array    $citationArr
	 */
	protected function getCitationInfo($citation)
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
	 * @param      array	$doi
	 * @return     string  	XML or false
	 */
	protected function getDoiXML($doi)
	{
		if ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_DATACITE)
		{
			$url = rtrim($this->_configs->dataciteServiceURL, '/') . '/metadata/' . $doi;

			$ch = curl_init($url);
			$options = array(
				CURLOPT_URL  			=> $url,
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
		else
		{
			return false;
		}
	}

	/**
	 * Check if the DOI metadata record already has the citation
	 *
	 * @param      string		$xml
	 * @param      array		$citation
	 * @param      string       $relationType
	 *
	 * @return     boolean
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
	 * @param      string		$xml
	 * @param      array		$citation
	 * @param      string       $relationType
	 *
	 * @return     boolean
	 */
	protected function isCitationUpdated($xml, $citation, $relationType)
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
	 * @param      string		$xml
	 * @param      array		$citationInfo
	 * @param      string       $relationType
	 *
	 * @return     boolean
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

		$result = $this->regMetadata($xmlStr);

		return $result;
	}

	/**
	 * Include citation and update DOI metadata record on DataCite
	 *
	 * @param      string		$xml
	 * @param      array		$citationInfo
	 * @param      string       $relationType
	 *
	 * @return     boolean
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

		$result = $this->regMetadata($xmlStr);

		return $result;
	}

	/**
	 * Get DOI service information from publication component configuration
	 *
	 * @param      $xml    the DOI metadata xml file
	 * @return     stdClass object
	 */
	protected function regMetadata($xml)
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

		if ($code == 201 || $code == 200)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get DOI service information from publication component configuration
	 *
	 * @param      null
	 * @return     stdClass object
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
