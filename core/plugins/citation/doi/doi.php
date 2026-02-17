<?php

namespace Plugins\Citation\Doi;

use Hubzero\Plugin\Plugin;

// phpcs:disable PSR1.Files.SideEffects


// phpcs:disable Generic.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:ignore Generic.Files.SideEffects
// phpcs:ignore Generic.Files.SideEffects

/**
 * Citation Plugin class for doi
 */
class Doi extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;

    /**
     * The publication component configuration
     *
     * @var object
     */
// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    public $_configs = null;

    /**
     * Relation type
     *
     * @const
     */
    public const REFERENCES = "references";
    public const REFERENCEDBY = "referencedby";
    public const ISREFERENCEDBY = "IsReferencedBy";

    /**
     * Datacite
     *
     * @const
     */
    public const DATACITE = "datacite::";

    /**
     * Citation
     *
     * @const
     */
    public const KEY_DOI = "DOI";
    public const KEY_PURL = "PURL";
    public const KEY_HANDLE = "Handle";
    public const KEY_ARK = "ARK";
    public const KEY_ARXIV = "arXiv";
    public const KEY_URL = "URL";
    public const KEY_URN = "URN";
    public const KEY_RESOURCETYPEGENERAL = "resourceTypeGeneral";

    /*
    * Handle
    */
    public const HANDLE = "handle";
    public const HANDLE_DOMAIN_NAME = "hdl.handle.net";

    /**
     * Update DOI metadata record with citation included
     *
     * @return  void
     */
    public function onCitationAfterSave()
    {
        // Associations and citation from citation component admin interface
        $assocParams = Request::getArray('assocs', array(), 'post');
        $citation = array_map(
            'trim',
            Request::getArray('citation', array(), 'post')
        );

        if (!empty($assocParams)) {
            $relTypeArr = [];

            foreach ($assocParams as $assocParam) {
                if (count(array_unique($assocParam)) != 1) {
                    if (
                        $assocParam['tbl'] == 'publication'
                        && !empty($assocParam['oid'])
                        && !empty($assocParam['type'])
                    ) {
                        // Get doi by publication version id
                        $db = App::get('db');
                        $verTbl = new \Components\Publications\Tables\Version($db);
                        $pubVer = $verTbl->getPubVersion($assocParam['oid']);
                        if ($pubVer) {
                            if (preg_match("/" . self::REFERENCES . "/i", $assocParam['type'])) {
                                $relTypeArr[$pubVer->doi] = ucfirst(self::REFERENCES);
                            } elseif (preg_match("/" . self::REFERENCEDBY . "/i", $assocParam['type'])) {
                                $relTypeArr[$pubVer->doi] = self::ISREFERENCEDBY;
                            } else {
                                $relTypeArr[$pubVer->doi] = $assocParam['type'];
                            }
                        }
                    }

                    if (empty($assocParam['tbl']) || empty($assocParam['oid']) || empty($assocParam['type'])) {
                        Notify::warning(Lang::txt('PLG_CITATION_PARAMETER_MISSING'));
                    }
                }
            }

            if (!empty($relTypeArr) && !empty($citation)) {
                $this->updateDoiRecord($citation, $relTypeArr);
            }
        }
    }

    /**
     * Update DOI metadata record with citation information
     *
     * @param      array    $citation
     * @param      array    $relationTypeArr
     * @return     null
     */
    protected function updateDoiRecord($citation, $relationTypeArr)
    {
        $citationInfo = [];
        $citationInfo = $this->getCitationInfo($citation);

        // Get DOI service configuration information
        $this->getPubConfig();

        if (!empty($citationInfo)) {
            foreach ($relationTypeArr as $pubDOI => $relationType) {
                $xml = $this->_getDoiXml($pubDOI);

                if ($xml) {
                    if ($this->isCitationUpdated($xml, $citationInfo, $relationType)) {
                        $this->updateDoiMetadataXML($xml, $citationInfo, $relationType);
                    } elseif (!$this->isCitationIncluded($xml, $citationInfo, $relationType)) {
                        $this->incCitationInDoiMetadataXML($xml, $citationInfo, $relationType);
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
    protected function getCitationInfo($citation)
    {
        $citationArr = [];

        if (!empty($citation['type'])) {
            include_once Component::path('com_citations') . DS . 'models' . DS . 'type.php';
            $types = \Components\Citations\Models\Type::all();
            $typeRecord = $types->select($types->getQualifiedFieldName('type_desc'))
                ->whereEquals($types->getQualifiedFieldName('id'), $citation['type'])
                ->row();

            $pos = strpos(trim($typeRecord->type_desc), self::DATACITE);
            if ($pos == 0) {
                $typeRecord->type_desc = substr(trim($typeRecord->type_desc), $pos + strlen(self::DATACITE));
            }
            $citationArr['resourceTypeGeneral'] = $typeRecord->type_desc;
        }

        if (!empty($citation['doi'])) {
            $citationArr['DOI'] = $citation['doi'];
        } else {
            if (!empty($citation['url'])) {
                if (preg_match("/purl/i", $citation['url'])) {
                    $citationArr[self::KEY_PURL] = $citation['url'];
                } elseif (preg_match("/handle/i", $citation['url'])) {
                    if (preg_match("/hdl.handle.net/", $citation['url'])) {
                        $pos = strpos($citation['url'], self::HANDLE_DOMAIN_NAME);
                        $citationArr[self::KEY_HANDLE] = substr(
                            $citation['url'],
                            $pos + strlen(self::HANDLE_DOMAIN_NAME) + 1
                        );
                    } else {
                        $pos = strpos($citation['url'], self::HANDLE);
                        $citationArr[self::KEY_HANDLE] = substr($citation['url'], $pos + strlen(self::HANDLE) + 1);
                    }
                } elseif (preg_match("/ark:/i", $citation['url']) && !empty($citation['eprint'])) {
                    $citationArr[self::KEY_ARK] = $citation['eprint'];
                } elseif (preg_match("/arxiv/i", $citation['url']) && !empty($citation['eprint'])) {
                    $citationArr[self::KEY_ARXIV] = $citation['eprint'];
                } elseif (preg_match("/urn:/i", $citation['url']) && !empty($citation['eprint'])) {
                    $citationArr[self::KEY_URN] = $citation['eprint'];
                } else {
                    $citationArr[self::KEY_URL] = $citation['url'];
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
// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getDoiXml($doi)
    {
        if (
            $this->_configs->dataciteEZIDSwitch
            == \Components\Publications\Models\Doi::SWITCH_OPTION_DATACITE
        ) {
            $url = rtrim($this->_configs->dataciteServiceURL, '/') . '/metadata/' . $doi;

            $ch = curl_init($url);
            $options = array(
                CURLOPT_URL             => $url,
                CURLOPT_USERPWD         => $this->_configs->dataciteUserPW,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_HTTPHEADER      => [
                    'Content-Type:text/plain;charset=UTF-8'
                ],
            );

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);

            $success = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($success === 201 || $success === 200) {
                return $response;
            } else {
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

        if ($relatedIdentifierNodeList->length != 0) {
            foreach ($relatedIdentifierNodeList as $relatedIdentifier) {
                $identifier = $this->getIdentifier($citation);

                if ($identifier) {
                    if (
                        !empty($relatedIdentifier->getAttribute("relationType"))
                        && ($relatedIdentifier->getAttribute("relationType") == $relationType)
                        && ($relatedIdentifier->getAttribute("relatedIdentifierType") == key($identifier))
                        && ($relatedIdentifier->getAttribute("resourceTypeGeneral")
                            == $citation[self::KEY_RESOURCETYPEGENERAL])
                        && ($relatedIdentifier->nodeValue == current($identifier))
                    ) {
                        return true;
                    }
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
    protected function isCitationUpdated($xml, $citation, $relationType)
    {
        $dom = new \DomDocument();
        $dom->loadXML($xml);

        $relatedIdentifierNodeList = $dom->getElementsByTagName("relatedIdentifier");

        if ($relatedIdentifierNodeList->length != 0) {
            foreach ($relatedIdentifierNodeList as $relatedIdentifier) {
                $identifier = $this->getIdentifier($citation);
                if ($identifier) {
                    if (
                        ($relatedIdentifier->getAttribute("relatedIdentifierType") == key($identifier))
                        && ($relatedIdentifier->nodeValue == current($identifier))
                    ) {
                        if ($relatedIdentifier->getAttribute("relationType") != $relationType) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Update citation record in DOI metadata xml on DataCite
     *
     * @param   string   $xml
     * @param   array    $citation
     * @param   string   $relationType
     * @return  boolean
     */
    public function updateDoiMetadataXML($xml, $citation, $relationType)
    {
        $xmlStr = '';
        $identifier = $this->getIdentifier($citation);
        if ($identifier) {
            $relatedIdentifierType = key($identifier);
            $relatedIdentifierVal = current($identifier);

            $resourceTypeGeneral = $citation[self::KEY_RESOURCETYPEGENERAL];

            $dom = new \DomDocument();
            $dom->loadXML($xml);

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('ns', "http://datacite.org/schema/kernel-4");

            $query = "//ns:relatedIdentifier[text()=" . "'" . $relatedIdentifierVal
                . "'" . " and @relatedIdentifierType=" . "'" . $relatedIdentifierType . "'" . "]";
            $nodeList = $xpath->query($query);
            $nodeList->item(0)->setAttribute("relationType", $relationType);
            $nodeList->item(0)->setAttribute("resourceTypeGeneral", $resourceTypeGeneral);
            $xmlStr = $dom->saveXML();

            $result = $this->_regMetadata($xmlStr);

            return $result;
        }
    }

    /**
     * Include citation and update DOI metadata record on DataCite
     *
     * @param   string   $xml
     * @param   array    $citation
     * @param   string   $relationType
     * @return  boolean
     */
    public function incCitationInDoiMetadataXML($xml, $citation, $relationType)
    {
        $xmlStr = '';

        $dom = new \DomDocument();
        $dom->loadXML($xml);
        $relatedIdentifiersNodeList = $dom->getElementsByTagName(
            "relatedIdentifiers"
        );

        if ($relatedIdentifiersNodeList->length != 0) {
            $this->createRelatedIdentifier($xmlStr, $dom, $relatedIdentifiersNodeList, $citation, $relationType);
        } else {
            $resourceElementNodeList = $dom->getElementsByTagName("resource");
            $sizeElementNodeList = $dom->getElementsByTagName("sizes");

            if ($sizeElementNodeList->length != 0) {
                $this->insertRelatedIdentifiersNode(
                    $xmlStr,
                    $dom,
                    $resourceElementNodeList,
                    $sizeElementNodeList,
                    $citation,
                    $relationType
                );
            } else {
                $formatElementNodeList = $dom->getElementsByTagName("formats");

                if ($formatElementNodeList->length != 0) {
                    $this->insertRelatedIdentifiersNode(
                        $xmlStr,
                        $dom,
                        $resourceElementNodeList,
                        $formatElementNodeList,
                        $citation,
                        $relationType
                    );
                } else {
                    $versionElementNodeList = $dom->getElementsByTagName("version");

                    if ($versionElementNodeList->length != 0) {
                        $this->insertRelatedIdentifiersNode(
                            $xmlStr,
                            $dom,
                            $resourceElementNodeList,
                            $versionElementNodeList,
                            $citation,
                            $relationType
                        );
                    }
                }
            }
        }

        $result = $this->_regMetadata($xmlStr);

        return $result;
    }

    /**
     * create relatedIdentifier node
     *
     * @param   reference   &$xml
     * @param   object      $dom
     * @param   object      $relatedIdentifiersNodeList
     * @param   array       $citation
     * @param   string      $relationType
     * @return  void
     */
    public function createRelatedIdentifier(&$xml, $dom, $relatedIdentifiersNodeList, $citation, $relationType)
    {
        $identifier = $this->getIdentifier($citation);

        if ($identifier) {
            $relatedIdentifierElement = $dom->createElement("relatedIdentifier", current($identifier));
            $relatedIdentifierNode = $relatedIdentifiersNodeList->item(0)->appendChild($relatedIdentifierElement);
            $relatedIdentifierNode->setAttribute("relatedIdentifierType", key($identifier));
            $relatedIdentifierNode->setAttribute("relationType", $relationType);
            $relatedIdentifierNode->setAttribute("resourceTypeGeneral", $citation[self::KEY_RESOURCETYPEGENERAL]);
            $xml = $dom->saveXML();
        }
    }

    /**
     * Insert relatedIdentifiers node before specific node and create child node relatedIdentifier
     *
     * @param   reference   &$xml
     * @param   object      $dom
     * @param   object      $resourceNode
     * @param   object      $specNode
     * @param   array       $citation
     * @param   string      $relationType
     * @return  void
     */
    public function insertRelatedIdentifiersNode(&$xml, $dom, $resourceNode, $specNode, $citation, $relationType)
    {
        $identifier = $this->getIdentifier($citation);
        if ($identifier) {
            $relatedIdentifiersElement = $dom->createElement("relatedIdentifiers");
            $relatedIdentifiersNode = $resourceNode->item(0)->insertBefore(
                $relatedIdentifiersElement,
                $specNode->item(0)
            );
            $relatedIdentifierElement = $dom->createElement("relatedIdentifier", current($identifier));
            $relatedIdentifierNode = $relatedIdentifiersNode->appendChild($relatedIdentifierElement);
            $relatedIdentifierNode->setAttribute("relatedIdentifierType", key($identifier));
            $relatedIdentifierNode->setAttribute("relationType", $relationType);
            $relatedIdentifierNode->setAttribute("resourceTypeGeneral", $citation[self::KEY_RESOURCETYPEGENERAL]);
            $xml = $dom->saveXML();
        }
    }

    /**
     * Get DOI service information from publication component configuration
     *
     * @param   string  $xml  the DOI metadata xml file
     * @return  bool    curl command response code
     */
// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _regMetadata($xml)
    {
        $url = rtrim($this->_configs->dataciteServiceURL, '/') . '/' . 'metadata';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->dataciteUserPW);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array('Content-Type:text/plain;charset=UTF-8')
        );
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
        if (empty($this->_configs)) {
            $params = Component::params('com_publications');

            $configs = new stdClass();
            $configs->dataciteEZIDSwitch = $params->get('datacite_ezid_doi_service_switch');
            $configs->dataciteServiceURL = $params->get('datacite_doi_service');
            $configs->dataciteUserPW = $params->get('datacite_doi_userpw');

            $this->_configs = $configs;
        }
    }

    /**
     * Get Identifier type and value from the Citation
     *
     * @param   object  $citation
     * @return  array or false
     */
    public function getIdentifier($citation)
    {
        if (!empty($citation)) {
            $keys = array_keys($citation);
            $checkKeys = [
                self::KEY_DOI,
                self::KEY_PURL,
                self::KEY_HANDLE,
                self::KEY_ARK,
                self::KEY_ARXIV,
                self::KEY_URN,
                self::KEY_URL
            ];

            foreach ($checkKeys as $key) {
                if (in_array($key, $keys) && !empty($citation[$key])) {
                    return [$key => $citation[$key]];
                }
            }
        }

        return false;
    }
}
