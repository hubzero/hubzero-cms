<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

namespace Components\Publications\Models;

use Hubzero\Base\Object;
use stdClass;

include_once(__DIR__ . DS . 'publication.php');

/**
 * Publication doi model class
 */
class Doi extends Object
{
	/**
	 * DOI Configs
	 */
	var $_configs = NULL;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct( $pub = NULL )
	{
		$this->_db = \App::get('db');

		// Set configs
		$this->configs();

		// Map to DOI fields
		$this->mapPublication( $pub );
	}

	/**
	 * Set DOI service configs
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function configs()
	{
		if (empty($this->_configs))
		{
			$params   = Component::params( 'com_publications' );

			$configs            = new stdClass;
			$configs->shoulder  = $params->get('doi_shoulder');
			$configs->service   = trim($params->get('doi_service'), DS);
			$configs->prefix    = $params->get('doi_prefix');
			$configs->userpw    = $params->get('doi_userpw');
			$configs->publisher = $params->get('doi_publisher', Config::get('sitename'));
			$configs->livesite  = trim(Request::root(), DS);
			$configs->xmlSchema = trim($params->get('doi_xmlschema', 'http://schema.datacite.org/meta/kernel-2.1/metadata.xsd' ), DS);

			$this->_configs = $configs;
		}

		return $this->_configs;
	}

	/**
	 * Check if a property is set
	 *
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Set a property
	 *
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 *
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Check if the service is on
	 *
	 * @return     boolean
	 */
	public function on()
	{
		// Make sure configs are loaded
		$this->configs();

		// Check required
		if ($this->_configs->shoulder && $this->_configs->service && $this->_configs->userpw)
		{
			return true;
		}

		return false;
	}

	/**
	 * Build service call path
	 *
	 * @return   string
	 */
	public function getServicePath( $doi = NULL )
	{
		if (!$this->on())
		{
			return false;
		}

		if ($doi)
		{
			$call  = $this->_configs->service . DS . 'id' . DS . 'doi:' . $doi;
		}
		else
		{
			$call  = $this->_configs->service . DS . 'shoulder' . DS . 'doi:';
			$call .= $this->_configs->shoulder;
			$call .= $this->_configs->prefix ? DS . $this->_configs->prefix : DS;
		}
		return $call;
	}

	/**
	 * Map publication object to DOI fields
	 *
	 * Instance of Components\Publications\Models\Publication
	 *
	 * @return  void
	 */
	public function mapPublication($pub = NULL)
	{
		if (empty($pub) || !$pub instanceof Publication)
		{
			return false;
		}
		// Clear out any previously set values
		$this->reset();

		$this->set('doi', $pub->version->doi);
		$this->set('title', htmlspecialchars($pub->version->title));
		$this->set('version', htmlspecialchars($pub->version->version_label));
		$this->set('abstract', htmlspecialchars($pub->version->abstract));
		$this->set('url', $this->_configs->livesite . DS . 'publications'. DS . $pub->id . DS . $pub->version->version_number);

		// Set dates
		$pubYear = $pub->version->published_up
				&& $pub->version->published_up != $this->_db->getNullDate()
				? date( 'Y', strtotime($pub->version->published_up)) : date( 'Y' );
		$pubDate = $pub->version->published_up
			&& $pub->version->published_up != $this->_db->getNullDate()
				? date( 'Y-m-d', strtotime($pub->version->published_up)) : date( 'Y-m-d' );
		$this->set('pubYear', $pubYear);
		$this->set('datePublished', $pubDate);

		// Map authors & creator
		$this->set('authors', $pub->authors());
		$this->mapUser($pub->version->created_by, $pub->_authors, 'creator');

		// Project creator as contributor
		$project = $pub->project();
		$this->mapUser($project->get('owned_by_user'), array(), 'contributor');

		// Map resource type
		$category = $pub->category();
		$dcType   = $category->dc_type ? $category->dc_type : 'Dataset';
		$this->set('resourceType', $dcType);
		$this->set('resourceTypeTitle', htmlspecialchars($category->name));

		// Map license
		$license = $pub->license();
		$this->set('license', htmlspecialchars($license->title));

		// Map related identifier
		$lastPub = $pub->lastPublicRelease();
		if ($lastPub && $lastPub->doi && $pub->version->version_number > 1)
		{
			$this->set('relatedDoi', $lastPub->doi);
		}
	}

	/**
	 * Map user
	 *
	 * User ID
	 * Publication authors list
	 * Mapping field
	 *
	 * @return  void
	 */
	public function mapUser( $uid = NULL, $authors = array(), $type = 'creator')
	{
		if (!empty($authors) && count($authors) > 0)
		{
			$name = $authors[0]->name;
			$orcid = (isset($authors[0]->orcid) ? $authors[0]->orcid : '');
		}
		elseif ($uid)
		{
			$user = \Hubzero\User\Profile::getInstance($uid);
			if ($user)
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
		$nameParts            = explode(" ", $name);
		$name  = end($nameParts);
		$name .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';
		$this->set($type , htmlspecialchars($name));

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
		$this->set('pubYear', date( 'Y' ));
		$this->set('publisher', htmlspecialchars($this->_configs->publisher));
		$this->set('resourceType', 'Dataset');
		$this->set('resourceTypeTitle', 'Dataset');
		$this->set('datePublished', date('Y-m-d'));
		$this->set('dateAccepted', date('Y-m-d'));
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
	 * @return     boolean
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
	 * Run cURL
	 *
	 * @param	   string	$url
	 * @param	   array	$postvals
	 *
	 * @return	   response string
	 */
	public function runCurl($url, $postvals = null)
	{
		$ch = curl_init($url);

		$options = array(
			CURLOPT_URL             => $url,
			CURLOPT_POST            => true,
			CURLOPT_USERPWD         => $this->_configs->userpw,
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
				return strtoupper($this->_configs->shoulder . DS . $handle);
			}
		}
		else
		{
			$this->setError($success . ' ' . $response);
		}

		return false;
	}

	/**
	 * Register a DOI
	 *
	 * @param   boolean  $sendXml
	 * @param   string   $status  DOI status [public, reserved]
	 * @return  boolean
	 */
	public function register($sendXml = false, $status = 'public')
	{
		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		$input = $this->startInput($status);
		if (!$input)
		{
			// Cannot procees if any required fields are missing
			return false;
		}

		// Get service call
		$url = $this->getServicePath();

		// Make service call to provision doi
		$doi = $this->runCurl($url, $input);

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
				$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_XML_INVALID'));
			}
			else
			{
				// Append XML
				$input .= 'datacite: ' . strtr($xml, array(":" => "%3A", "%" => "%25", "\n" => "%0A", "\r" => "%0D")) . "\n";

				// Make service call to send extended metadata
				$doi = $this->runCurl($url, $input);
			}
		}

		// Return DOI
		return $doi;
	}

	/**
	 * Update a DOI
	 *
	 * @param   string   $doi
	 * @param   boolean  $sendXml
	 * @param   string   $status  DOI status [public, reserved]
	 * @return  boolean
	 */
	public function update($doi = NULL, $sendXml = false, $status = 'public')
	{
		$doi = $doi ? $doi : $this->get('doi');
		if (!$doi)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_UPDATE_NO_HANDLE'));
			return false;
		}

		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE'));
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
			// Cannot procees if any required fields are missing
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
				$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_XML_INVALID'));
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
	 * Delete a DOI
	 *
	 * @return boolean
	 */
	public function delete($doi = NULL)
	{
		$doi = $doi ? $doi : $this->get('doi');
		if (!$doi)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_UPDATE_NO_HANDLE'));
			return false;
		}

		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		// TBD
		// Call to delete a DOI
		return true;
	}

	/**
	 * Build XML
	 *
	 * @return     xml output
	 */
	public function buildXml( $doi = NULL)
	{
		$doi = $doi ? $doi : $this->get('doi');
		if (!$doi)
		{
			return false;
		}

		if (!$this->checkRequired())
		{
			return false;
		}

		// Start XML
		$xmlfile  = '<?xml version="1.0" encoding="UTF-8"?><resource xmlns="http://datacite.org/schema/kernel-2.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://datacite.org/schema/kernel-2.1 http://schema.datacite.org/meta/kernel-2.1/metadata.xsd">';
		$xmlfile .='<identifier identifierType="DOI">' . $doi . '</identifier>';
		$xmlfile .='<creators>';

		// Add authors
		$authors = $this->get('authors');
		if (!empty($authors) && is_array($authors))
		{
			foreach ($authors as $author)
			{
				$nameParts    = explode(" ", $author->name);
				$name         = end($nameParts);
				$name        .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';
				$xmlfile .='<creator>';
				$xmlfile .='	<creatorName>' . $name . '</creatorName>';
				if (isset($author->orcid) && !empty($author->orcid))
				{
					$xmlfile.='	<nameIdentifier nameIdentifierScheme="ORCID">' . $author->orcid . '</nameIdentifier>';
				}
				$xmlfile.='</creator>';
			}
		}
		else
		{
			$xmlfile .='<creator>';
			$xmlfile .='	<creatorName>' . $this->get('creator') . '</creatorName>';
			if ($this->get('creatorOrcid'))
			{
				$xmlfile.='	<nameIdentifier nameIdentifierScheme="ORCID">'.'http://orcid.org/' .  $this->get('creatorOrcid') . '</nameIdentifier>';
			}
			$xmlfile.='</creator>';
		}
		$xmlfile.='</creators>';
		$xmlfile.='<titles>
			<title>' . $this->get('title') . '</title>
		</titles>
		<publisher>' . $this->get('publisher') . '</publisher>
		<publicationYear>' . $this->get('pubYear') . '</publicationYear>';
		if ($this->get('contributor'))
		{
			$xmlfile.='<contributors>';
			$xmlfile.='	<contributor contributorType="ProjectLeader">';
			$xmlfile.='		<contributorName>' . htmlspecialchars($this->get('contributor')) . '</contributorName>';
			$xmlfile.='	</contributor>';
			$xmlfile.='</contributors>';
		}
		$xmlfile.='<dates>
			<date dateType="Valid">' . $this->get('datePublished') . '</date>
			<date dateType="Accepted">' . $this->get('dateAccepted') . '</date>
		</dates>
		<language>' . $this->get('language') . '</language>
		<resourceType resourceTypeGeneral="' . $this->get('resourceType') . '">' . $this->get('resourceTypeTitle') . '</resourceType>';
		if ($this->get('relatedDoi'))
		{
			$xmlfile.='<relatedIdentifiers>
				<relatedIdentifier relatedIdentifierType="DOI" relationType="IsNewVersionOf">' . $this->get('relatedDoi') . '</relatedIdentifier>
			</relatedIdentifiers>';
		}
		if ($this->get('version'))
		{
			$xmlfile.= '<version>' . $this->get('version') . '</version>';
		}
		if ($this->get('license'))
		{
			$xmlfile.='<rights>' . htmlspecialchars($this->get('license')) . '</rights>';
		}
		$xmlfile .='<descriptions>
			<description descriptionType="Abstract">';
		$xmlfile.= stripslashes(htmlspecialchars($this->get('abstract')));
		$xmlfile.= '</description>
			</descriptions>
		</resource>';

		return $xmlfile;
	}
}