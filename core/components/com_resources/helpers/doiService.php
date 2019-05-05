<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers;

use Hubzero\Base\Obj;
use stdClass;
use Component;
use Request;
use Config;
use Lang;
use User;

/**
 * Resources doi service class
 */
class DoiService extends Obj
{
	/**
	 * DOI Configs
	 *
	 * @var  object
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
	 * @param   array  $pub
	 * @return  void
	 */
	public function __construct($pub = null)
	{
		// Set configs
		$this->configs();

		// Map to DOI fields
		$this->mapData($pub);
	}

	/**
	 * Set DOI service configs
	 *
	 * @return  object
	 */
	public function configs()
	{
		if (empty($this->_configs))
		{
			$params = Component::params('com_tools');

			$configs = new stdClass;
			$configs->shoulder      = $params->get('doi_shoulder');
			$configs->prefix        = $params->get('doi_newprefix');
			$configs->serviceSwitch = $params->get('doi_service_switch');
			$configs->serviceURL    = $params->get('doi_newservice');
			$configs->userPW        = $params->get('doi_userpw');
			$configs->publisher     = $params->get('doi_publisher', Config::get('sitename'));
			$configs->livesite      = trim(Request::root(), '/');
			$configs->xmlSchema     = trim($params->get('doi_xmlschema', 'http://schema.datacite.org/meta/kernel-4/metadata.xsd'), '/');

			$this->_configs = $configs;
		}

		return $this->_configs;
	}

	/**
	 * Check if the service is on
	 *
	 * @return  boolean
	 */
	public function on()
	{
		// Make sure configs are loaded
		$this->configs();

		// Check required
		if ($this->_configs->serviceSwitch
		 && $this->_configs->shoulder
		 && (
			($this->_configs->serviceSwitch == 2 && $this->_configs->serviceURL && $this->_configs->userPW)
		 || ($this->_configs->serviceSwitch == 1 && $this->_configs->serviceURL && $this->_configs->userPW && $this->_configs->prefix)
		))
		{
			return true;
		}

		return false;
	}

	/**
	 * Build service call path
	 *
	 * @param   string  $doi
	 * @return  string
	 */
	public function getServicePath($doi = null)
	{
		if (!$this->on())
		{
			return false;
		}

		if ($doi)
		{
			if ($this->_configs->serviceSwitch == self::SWITCH_OPTION_DATACITE)
			{
				$call  = $this->_configs->serviceURL . '/id/doi:' . $doi;
			}
			elseif ($this->_configs->serviceSwitch == self::SWITCH_OPTION_EZID)
			{
				$call  = $this->_configs->serviceURL . '/id/doi:' . $doi;
			}
		}
		else
		{
			if ($this->_configs->serviceSwitch == self::SWITCH_OPTION_DATACITE)
			{
				$call  = $this->_configs->serviceURL . '/shoulder/doi:';
			}
			elseif ($this->_configs->serviceSwitch == self::SWITCH_OPTION_EZID)
			{
				$call  = $this->_configs->serviceURL . '/shoulder/doi:';
			}

			$call .= $this->_configs->shoulder;
			$call .= $this->_configs->prefix ? '/' . $this->_configs->prefix : '/';
		}

		return $call;
	}

	/**
	 * Map data to DOI fields
	 *
	 * @param   array  $pub
	 * @return  void
	 */
	public function mapData($pub = null)
	{
		if (empty($pub))
		{
			return false;
		}

		// Clear out any previously set values
		$this->reset();

		if (isset($pub['doi']) && $pub['doi'])
		{
			$this->set('doi', $pub['doi']);
		}

		$this->set('title', htmlspecialchars($pub['title']));

		if (isset($pub['version']) && $pub['version'])
		{
			$this->set('version', htmlspecialchars($pub['version']));
		}

		if (isset($pub['abstract']) && $pub['abstract'])
		{
			$this->set('abstract', htmlspecialchars($pub['abstract']));
		}

		if (isset($pub['language']) && $pub['language'])
		{
			$this->set('language', $pub['language']);
		}

		$this->set('url', $pub['targetURL']);

		// Set dates
		$pubYear = isset($pub['pubYear']) ? $pub['pubYear'] : gmdate('Y');
		$pubDate = isset($pub['datePublished']) && $pub['datePublished'] && $pub['datePublished'] != '0000-00-00 00:00:00'
				? gmdate('Y-m-d', strtotime($pub['datePublished']))
				: gmdate('Y-m-d');
		$this->set('pubYear', $pubYear);
		$this->set('datePublished', $pubDate);
		$this->set('dateAccepted', gmdate('Y-m-d'));

		// Map authors & creator
		$this->set('authors', $pub['authors']);

		$this->mapUser(0, $pub['authors'], 'creator');

		// Map resource type
		$this->set('resourceType', 'Software');
		$this->set('resourceTypeTitle', 'Simulation Tool');

		// Map license
		if (isset($pub['license']) && $pub['license'])
		{
			$this->set('license', htmlspecialchars($pub['license']));
		}

		// Map related identifier
		/*$lastPub = $pub->lastPublicRelease();
		if ($lastPub && $lastPub->doi && $pub->version->version_number > 1)
		{
			$this->set('relatedDoi', $lastPub->doi);
		}*/
	}

	/**
	 * Map user
	 *
	 * @param   integer  $uid      User ID
	 * @param   array    $authors  Publication authors list
	 * @param   string   $type     Mapping field
	 * @return  void
	 */
	public function mapUser($uid = null, $authors = array(), $type = 'creator')
	{
		$name  = '';
		$orcid = '';

		if (!empty($authors) && count($authors) > 0)
		{
			$name  = $authors[0]->name;
			$orcid = (isset($authors[0]->orcid) ? $authors[0]->orcid : '');
		}
		elseif ($uid)
		{
			$user = User::getInstance($uid);
			if ($user && $user->get('id'))
			{
				$name  = $user->get('name');
				$orcid = $user->get('orcid');
			}
		}

		// Use acting user info
		if (empty($name))
		{
			$name  = User::get('name');
		}

		// Format name
		$nameParts = explode(' ', $name);
		$name  = end($nameParts);
		$name .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';

		$this->set($type, htmlspecialchars($name));

		if (!empty($orcid))
		{
			$this->set($type . 'Orcid', $orcid);
		}
	}

	/**
	 * Set default general values
	 *
	 * @return  void
	 */
	public function setDefaults()
	{
		$this->set('language', 'en');
		$this->set('pubYear', gmdate('Y'));
		$this->set('publisher', htmlspecialchars($this->_configs->publisher));
		$this->set('resourceType', 'Software');
		$this->set('resourceTypeTitle', 'Simulation Tool');
		$this->set('datePublished', gmdate('Y-m-d'));
		$this->set('dateAccepted', gmdate('Y-m-d'));
	}

	/**
	 * Reset custom values
	 *
	 * @return  void
	 */
	public function reset()
	{
		$this->set('url', '');
		$this->set('title', '');
		$this->set('abstract', '');
		$this->set('license', '');
		$this->set('version', '');
		$this->set('relatedDoi', '');
		$this->set('contributor', '');
		$this->set('creator', '');
		$this->set('creatorOrcid', '');
		$this->set('authors', array());
		$this->setDefaults();
	}

	/**
	 * Check if required fields are set
	 *
	 * @return  boolean
	 */
	public function checkRequired()
	{
		// Check required
		if ($this->get('pubYear')
			&& $this->get('publisher')
			&& $this->get('resourceType')
			&& $this->get('title')
			&& $this->get('creator')
			&& $this->get('url')
		)
		{
			return true;
		}

		return false;
	}

	/**
	 * Build XML - DOI is not required when register metadata using DataCite MDS API
	 *
	 * @param  string $doi
	 * @return xml string
	 */
	public function buildXml($doi = null)
	{
		if (!$this->checkRequired())
		{
			return false;
		}

		// Start XML
		$xmlfile  = '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlfile .= '<resource xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://datacite.org/schema/kernel-4" xsi:schemaLocation="http://datacite.org/schema/kernel-4 http://schema.datacite.org/meta/kernel-4/metadata.xsd">';
		$xmlfile .= '<identifier identifierType="DOI">' . $doi . '</identifier>';
		$xmlfile .= '<creators>';

		// Add authors
		$authors = $this->get('authors');
		if (!empty($authors) && is_array($authors))
		{
			foreach ($authors as $author)
			{
				$nameParts = explode(' ', $author->name);
				$name  = end($nameParts);
				$name .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';

				$xmlfile .= '<creator>';
				$xmlfile .= '	<creatorName>' . $name . '</creatorName>';
				if (isset($author->orcid) && !empty($author->orcid))
				{
					$xmlfile .= '	<nameIdentifier nameIdentifierScheme="ORCID">' . $author->orcid . '</nameIdentifier>';
				}
				$xmlfile .= '</creator>';
			}
		}
		else
		{
			$xmlfile .= '<creator>';
			$xmlfile .= '	<creatorName>' . $this->get('creator') . '</creatorName>';
			if ($this->get('creatorOrcid'))
			{
				$xmlfile .= '	<nameIdentifier nameIdentifierScheme="ORCID">'.'http://orcid.org/' .  $this->get('creatorOrcid') . '</nameIdentifier>';
			}
			$xmlfile .= '</creator>';
		}
		$xmlfile .= '</creators>';
		$xmlfile .= '<titles>';
		$xmlfile .= '	<title>' . $this->get('title') . '</title>';
		$xmlfile .= '</titles>';
		$xmlfile .= '<publisher>' . $this->get('publisher') . '</publisher>';
		$xmlfile .= '<publicationYear>' . $this->get('pubYear') . '</publicationYear>';
		if ($this->get('contributor'))
		{
			$xmlfile .= '<contributors>';
			$xmlfile .= '	<contributor contributorType="ProjectLeader">';
			$xmlfile .= '		<contributorName>' . htmlspecialchars($this->get('contributor')) . '</contributorName>';
			$xmlfile .= '	</contributor>';
			$xmlfile .= '</contributors>';
		}
		$xmlfile .= '<dates>';
		$xmlfile .= '	<date dateType="Valid">' . $this->get('datePublished') . '</date>';
		$xmlfile .= '	<date dateType="Accepted">' . $this->get('dateAccepted') . '</date>';
		$xmlfile .= '</dates>';
		$xmlfile .= '<language>' . $this->get('language') . '</language>';
		$xmlfile .= '<resourceType resourceTypeGeneral="' . $this->get('resourceType') . '">' . $this->get('resourceTypeTitle') . '</resourceType>';
		if ($this->get('relatedDoi'))
		{
			$xmlfile .= '<relatedIdentifiers>';
			$xmlfile .= '	<relatedIdentifier relatedIdentifierType="DOI" relationType="IsNewVersionOf">' . $this->get('relatedDoi') . '</relatedIdentifier>';
			$xmlfile .= '</relatedIdentifiers>';
		}
		if ($this->get('version'))
		{
			$xmlfile .= '<version>' . $this->get('version') . '</version>';
		}
		if ($this->get('license'))
		{
			$xmlfile .= '<rightsList><rights>' . htmlspecialchars($this->get('license')) . '</rights></rightsList>';
		}
		$xmlfile .= '<descriptions>';
		$xmlfile .= '	<description descriptionType="Abstract">';
		$xmlfile .= stripslashes(htmlspecialchars($this->get('abstract')));
		$xmlfile .= '	</description>';
		$xmlfile .= '</descriptions>';
		$xmlfile .= '</resource>';

		return $xmlfile;
	}

	/**
	 * Run cURL to register metadata and create DOI on DataCite
	 *
	 * @param   string   $url
	 * @param   array    $postVals
	 * @param   string   &$doi
	 * @return  boolean
	 */
	public function regMetadata($url, $postvals, &$doi)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->userPW);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvals);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8', 'Content-Length: ' . strlen($postvals)));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

		$response = curl_exec($ch);

		if (!$response)
		{
			return false;
		}

		$pattern = '/\((.*?)\)/';
		$ret = preg_match($pattern, $response, $match);
		if ($ret != 1)
		{
			return false;
		}

		$doi = $match[1];

		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

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
	 * Run cURL to register DOI name and URL that refers to the dataset on DataCite
	 *
	 * @param   string   $url
	 * @param   array    $postVals
	 * @return  boolean
	 */
	public function regURL($url, $postvals)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->userPW);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvals);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8', 'Content-Length: ' . strlen($postvals)));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

		$response = curl_exec($ch);

		if (!$response)
		{
			return false;
		}

		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

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
	 * Register DOI metadata. This is the first step to create DOI name and register metadata on DataCite.
	 *
	 * @return  string  $doi
	 */
	public function registerMetadata()
	{
		$doi = null;

		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		// Submit DOI metadata
		$metadataURL = $this->_configs->serviceURL . '/metadata/' . $this->_configs->shoulder;
		$xml = $this->buildXml();
		$subResult = $this->regMetadata($metadataURL, $xml, $doi);

		if (!$subResult)
		{
			return false;
		}

		return $doi;
	}

	/**
	 * Register the DOI name and associated URL. This is the second step to finish the DOI registration on DataCite.
	 *
	 * @param   string   $doi
	 * @return  boolean
	 */
	public function registerURL($doi)
	{
		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		// Register URL
		$resURL = $this->get('url');
		$url = $this->_configs->serviceURL . '/doi/' . $doi;
		$postvals = "doi=" . $doi . "\n" . "url=" . $resURL;

		$regResult = $this->regURL($url, $postvals);

		if (!$regResult)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Update DOI metadata
	 *
	 * @param   string   $doi
	 * @return  boolean
	 */
	public function dataciteMetadataUpdate($doi)
	{
		$doi = $doi ? $doi : $this->get('doi');

		if (!$doi)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_UPDATE_NO_HANDLE'));
			return false;
		}

		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		$metadataURL = $this->_configs->serviceURL . '/metadata';
		$xml = $this->buildXml($doi);

		$ch = curl_init($metadataURL);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->userPW);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8', 'Content-Length: ' . strlen($xml)));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_POST, true);

		$response = curl_exec($ch);

		if (!$response)
		{
			return false;
		}

		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

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
	 * Delete a DOI
	 *
	 * @param   string   $doi
	 * @return  boolean
	 */
	public function delete($doi = null)
	{
		$doi = $doi ? $doi : $this->get('doi');
		if (!$doi)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_UPDATE_NO_HANDLE'));
			return false;
		}

		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		// TBD
		// Call to delete a DOI
		return true;
	}

	/**
	 * Start input
	 *
	 * @param   string  $status  DOI status [public, reserved]
	 * @return  string  response string
	 */
	public function startInput($status = 'public')
	{
		if (!$this->checkRequired())
		{
			$this->setError(Lang::txt('Missing required DOI metadata'));
			return false;
		}

		$input  = "_target: " . $this->get('url') ."\n";
		$input .= "datacite.creator: " . $this->get('creator') . "\n";
		$input .= "datacite.title: ". $this->get('title') . "\n";
		$input .= "datacite.publisher: " . $this->get('publisher') . "\n";
		$input .= "datacite.publicationyear: " . $this->get('pubYear') . "\n";
		$input .= "datacite.resourcetype: " . $this->get('resourceType') . "\n";
		$input .= "_profile: datacite". "\n";

		$status = strtolower($status);
		if (!in_array($status, array('public', 'reserved')))
		{
			$status = 'public';
		}

		$input .= "_status: " . $status . "\n";

		return $input;
	}

	/**
	 * Run cURL to register DOI on EZID
	 *
	 * @param   string   $url
	 * @param   array    $postvals
	 * @return  boolean
	 */
	public function runCurl($url, $postvals = null)
	{
		$ch = curl_init($url);

		$options = array(
			CURLOPT_URL             => $url,
			CURLOPT_POST            => true,
			CURLOPT_USERPWD         => $this->_configs->userPW,
			CURLOPT_POSTFIELDS      => $postvals,
			CURLOPT_RETURNTRANSFER  => true,
			CURLOPT_HTTPHEADER      => array('Content-Type: text/plain; charset=UTF-8', 'Content-Length: ' . strlen($postvals))
		);
		curl_setopt_array($ch, $options);

		$response = curl_exec($ch);
		$success = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($success === 201 || $success === 200)
		{
			$out = explode('/', $response);
			$handle = trim(end($out));
			if ($handle)
			{
				// Return DOI
				return strtoupper($this->_configs->shoulder . '/' . $handle);
			}
		}
		else
		{
			$this->setError($success . ' ' . $response);
		}

		return false;
	}

	/**
	 * Register a DOI on EZID
	 *
	 * @param   boolean  $sendXml
	 * @param   string   $status  DOI status [public, reserved]
	 * @return  boolean
	 */
	public function registerEZID($sendXml = false, $status = 'public')
	{
		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		$input = $this->startInput($status);
		if (!$input)
		{
			// Cannot process if any required fields are missing
			return false;
		}

		// Get service call
		$url = $this->getServicePath();

		// Make service call to provision doi
		if ($status == 'reserved')
		{
			$doi = $this->runCurl($url, $input);
		}

		// Send XML and set DOI to public
		if (($status == 'public') && ($sendXml == true))
		{
			$xml = $this->buildXml();

			// Load the xml document in the DOMDocument object
			$xdoc = new \DomDocument;
			$xdoc->loadXML($xml);

			// Append XML
			$input .= 'datacite: ' . strtr($xml, array(":" => "%3A", "%" => "%25", "\n" => "%0A", "\r" => "%0D")) . "\n";

			// Make service call to send extended metadata
			$doi = $this->runCurl($url, $input);
		}

		// Return DOI
		return $doi;
	}

	/**
	 * Update a DOI on EZID
	 *
	 * @param   string   $doi
	 * @param   boolean  $sendXml
	 * @param   string   $status  DOI status [public, reserved]
	 * @return  boolean
	 */
	public function ezidMetadataUpdate($doi = null, $sendXml = false, $status = 'public')
	{
		$doi = $doi ? $doi : $this->get('doi');
		if (!$doi)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_UPDATE_NO_HANDLE'));
			return false;
		}

		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		// Check that we are trying to update a DOI issued by the hub
		if (!preg_match("/" . $this->_configs->shoulder . "/", $doi))
		{
			return false;
		}

		$input = $this->startInput($status);
		if (!$input)
		{
			// Cannot process if any required fields are missing
			return false;
		}

		// Are we sending extended data?
		if ($sendXml == true && $doi)
		{
			$xml = $this->buildXml($doi);

			// Load the xml document in the DOMDocument object
			$xdoc = new \DomDocument;
			$xdoc->loadXML($xml);

			// Validate against schema
			if (!$xdoc->schemaValidate($this->_configs->xmlSchema))
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI_XML_INVALID'));
			}
			else
			{
				// Append XML
				$input .= 'datacite: ' . strtr($xml, array(":" => "%3A", "%" => "%25", "\n" => "%0A", "\r" => "%0D")) . "\n";
			}
		}

		// Get service call
		$url = $this->getServicePath($doi);

		// Make service call
		$result = $this->runCurl($url, $input);

		return $result ? $result : false;
	}

	/**
	 * Update DOI metadata - Entry to update DOI metadata.
	 *
	 * @param   string   $doi
	 * @param   boolean  $sendXML   -- This is set to true when using EZID DOI service
	 * @return  void
	 */
	public function update($doi, $sendXML = false)
	{
		if ($this->_configs->serviceSwitch == self::SWITCH_OPTION_DATACITE)
		{
			$result = $this->dataciteMetadataUpdate($doi);

			if (!$result)
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_UPDATE_DATACITE_DOI_METADATA'));
			}
		}
		elseif ($this->_configs->serviceSwitch == self::SWITCH_OPTION_EZID)
		{
			$result = $this->ezidMetadataUpdate($doi, $sendXML);

			if (!$result)
			{
				$this->setError(Lang::txt('COM_RESOURCES_ERROR_UPDATE_EZID_DOI_METADATA'));
			}
		}
	}

	/**
	 * Register - Entry to register DOI metadata, or URL for DataCite DOI; Or register DOI for EZID
	 *
	 * @param   boolean  $regMetadata - Register Metadata for DataCite DOI when it is set to true.
	 * @param   boolean  $regUrl      - Register URL and DOI name for for DataCite DOI when it is set to true.
	 * @param   string   $doi
	 * @param   boolean  $sendXML     - Whether including XML for EZID DOI Update
	 * @param   string   $status      - EZID DOI status
	 * @return  string   $doi or null
	 */
	public function register($regMetadata = false, $regUrl = false, $doi = null, $sendXML = false, $status = 'public')
	{
		if ($this->_configs->serviceSwitch == self::SWITCH_OPTION_DATACITE)
		{
			// Register DOI Metadata through DataCite service
			if ($regMetadata)
			{
				$doi = $this->registerMetadata();

				if (!$doi)
				{
					$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI'));
				}

				return $doi;
			}

			if ($regUrl && !empty($doi))
			{
				$regResult = $this->registerURL($doi);

				if (!$regResult)
				{
					$this->setError(Lang::txt('COM_RESOURCES_ERROR_REGISTER_NAME_URL'));
				}

				return $regResult;
			}
		}
		elseif ($this->_configs->serviceSwitch == self::SWITCH_OPTION_EZID)
		{
			// Register DOI through EZID service
			if (!$regUrl)
			{
				$doi = $this->registerEZID($sendXML, $status);

				if (!$doi)
				{
					$this->setError(Lang::txt('COM_RESOURCES_ERROR_DOI'));
				}

				return $doi;
			}
		}
		elseif ($this->_configs->serviceSwitch == self::SWITCH_OPTION_NONE)
		{
			$this->setError(Lang::txt('COM_RESOURCES_ERROR_NO_DOI_SERVICE_ACTIVATED'));

			return false;
		}
	}
}
