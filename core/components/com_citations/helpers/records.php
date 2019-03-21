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
 * @author    Jerry Kuang <kuang5@purdue.edu>
 * @copyright Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Helpers;

use stdClass;
use App;
use Lang;

/**
 * Citations Records class for including citation in the DOI metadata record
 */
class Records
{
	/**
	 * Database
	 *
	 * @var unknown
	 */
	public $_db  = null;

	/**
	 * The table
	 *
	 * @var string
	 */
	public $_publication_ver_tbl;

	/**
	 * The table
	 *
	 * @var string
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
	 * Constructor
	 *
	 * @param      object $db     Database
	 * @param      array  $config Optional configurations
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db  = \App::get('db');
		$this->_publication_ver_tbl= '#__publication_versions';
	}

	// Get doi based on publication id
	// Get DOI metadata xml
	// Insert citations to DOI metadata record and update DOI metadata record on DataCite

	/**
	 * Get DOI based on the publication ID
	 *
	 * @param      pubID    publication ID
	 * @param
	 *
	 * @return     array
	 */
	public function getDoiList($pubIDArr, &$pubWithMultiVer)
	{
		$doiArr = [];

		foreach ($pubIDArr as $pubID)
		{
			$sql = "SELECT doi FROM $this->_publication_ver_tbl WHERE publication_id = " . $pubID;
			$this->_db->setQuery($sql);
			$result = $this->_db->loadObjectList();

			if (count($result) > 1)
			{
				$pubWithMultiVer[] = $pubID;
			}
			else
			{
				$doiArr[] = $result[0]->doi;
			}
		}

		return $doiArr;
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
			$configs->livesite  = trim(Request::root(), DS);
			$configs->xmlSchema = trim($params->get('doi_xmlschema', 'http://schema.datacite.org/meta/kernel-4/metadata.xsd'), DS);

			$this->_configs = $configs;
		}
	}

	/**
	 * Get citation informaiton from original whole citation
	 *
	 * @param      array    $doi
	 * @param      array    $origCitation
	 *
	 * @return     array    $citationArr
	 */
	public function getCitationInfo($citation)
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
	 * @param      $doi
	 * @return     string  XML or false
	 */
	public function getDoiXML($doi)
	{
		// Get DOI service configuration information
		$this->getPubConfig();

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
	 * Include citation and update DOI metadata record on DataCite
	 *
	 * @param      $xml
	 * @param      $citationInfo
	 *
	 * @return     string  XML or false
	 */
	public function updateDoiXML($xml, $citationInfo)
	{
		$xmlStr = '';
		$url = rtrim($this->_configs->dataciteServiceURL, '/') . '/' . 'metadata';

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
				$relatedIdentifierNode->setAttribute("relationType", "IsReferencedBy");
				$xmlStr = $dom->saveXML();
			}
		}
		else
		{
			$resourceElementNodeList = $dom->getElementsByTagName("resource");
			$sizeElementNodeList = $dom->getElementsByTagName("sizes");

			if ($sizeElementNodeList->length != 0 )
			{
				$relatedIdentifiersElement = $dom->createElement("relatedIdentifiers");
				$relatedIdentifiersNode = $resourceElementNodeList->item(0)->insertBefore($relatedIdentifiersElement, $sizeElementNodeList->item(0));

				foreach ($citationInfo as $key => $citation)
				{
					$relatedIdentifierElement = $dom->createElement("relatedIdentifier", $citation);
					$relatedIdentifierNode = $relatedIdentifiersNode->appendChild($relatedIdentifierElement);
					$relatedIdentifierNode->setAttribute("relatedIdentifierType", $key);
					$relatedIdentifierNode->setAttribute("relationType", "IsReferencedBy");
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
						$relatedIdentifierNode->setAttribute("relationType", "IsReferencedBy");
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
							$relatedIdentifierNode->setAttribute("relationType", "IsReferencedBy");
							$xmlStr = $dom->saveXML();
						}
					}
				}
			}
		}

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->dataciteUserPW);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
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
	 * Check if the citation is included
	 *
	 * @param      $xml
	 * @param      $citation
	 *
	 * @return     boolean
	 */
	public function isCitationIncluded($xml, $citation)
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
				if ( ($relatedIdentifier->getAttribute("relationType") == "IsReferencedBy")
					&& ($relatedIdentifier->getAttribute("relatedIdentifierType") == $key)
					&& ($relatedIdentifier->nodeValue == $val))
				{
					return true;
				}
				else
				{
					continue;
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update DOI metadata record with citation information
	 *
	 * @param      array    $doi
	 * @param      array    $citation
	 *
	 * @return     null
	 */
	public function updateDoiRecord($doiArr, $citation)
	{
		$citationInfo = [];
		$citationInfo = $this->getCitationInfo($citation);

		if (!empty($citationInfo))
		{
			foreach ($doiArr as $doi)
			{
				$xml = $this->getDoiXML($doi);

				if ($xml && !$this->isCitationIncluded($xml, $citationInfo))
				{
					$this->updateDoiXml($xml, $citationInfo);
				}
			}
		}
	}
}
