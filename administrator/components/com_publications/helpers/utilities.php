<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Utility methods
 */
class PublicationUtilities
{
	/**
	 * Register DOI with configures DOI service
	 *
	 * @param      array 	$row 		Publication version info
	 * @param      array 	$authors 	Publication version authors
	 * @param      array 	$config 	Publications component config
	 * @param      array 	$metadata 	Array of metadata
	 * @param      string 	&$doierr 	Collector for errors
	 * @param      int 		$reserve 	Reserving DOI? (no extended XML metadata)
	 * @return     true on success or false on error
	 */
	public static function registerDoi( $row, $authors, $config, $metadata = array(), &$doierr = '', $reserve = 0 )
	{
		// Get configs
		$jconfig  = JFactory::getConfig();
		$shoulder = $config->get('doi_shoulder');
		$service  = trim($config->get('doi_service'), DS);
		$prefix   = $config->get('doi_prefix', '' );
		$userpw   = $config->get('doi_userpw');

		if (!$service || !$userpw || !$shoulder)
		{
			$doierr .= JText::_('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE');
			return false;
		}

		$handle = '';
		$doi    = '';

		// Collect metadata
		$metadata['publisher'] = $config->get('doi_publisher', '' );
		$metadata['pubYear']   = date( 'Y' );

		// Make service path
		$call  = $service . DS . 'shoulder' . DS . 'doi:' . $shoulder;
		$call .= $prefix ? DS . $prefix : DS;

		// Get publisher name
		if (!$metadata['publisher'])
		{
			$metadata['publisher'] = $jconfig->getValue('config.sitename');
		}

		$juri = JURI::getInstance();

		// Get config
		$livesite = $jconfig->getValue('config.live_site')
			? $jconfig->getValue('config.live_site')
			: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);
		if (!$livesite)
		{
			$doierr .= JText::_('COM_PUBLICATIONS_ERROR_DOI_MISSING_LIVE_CONFIG');
			return false;
		}

		$metadata['url'] = $livesite . DS . 'publications'. DS . $row->publication_id . DS . $row->version_number;

		// Get first author / creator name
		if (count($authors) > 0)
		{
			$creatorName = $authors[0]->name;
			$creatorOrcid = (isset($authors[0]->orcid) ? $authors[0]->orcid : '');
		}
		else
		{
			$creator = JUser::getInstance($row->created_by);
			$creatorName = $creator->get('name');
			$creatorOrcid = $creator->get('orcid');
		}

		// Format name
		$nameParts    = explode(" ", $creatorName);
		$metadata['creator']  = end($nameParts);
		$metadata['creator'] .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';
		$metadata['creatorOrcid'] = $creatorOrcid;

		$metadata['title'] = stripslashes(htmlspecialchars($row->title));

		// Start input
		$input  = "_target: " . $metadata['url'] ."\n";
		$input .= "datacite.creator: " . $metadata['creator'] . "\n";
		$input .= "datacite.title: ". $metadata['title'] . "\n";
		$input .= "datacite.publisher: " . $metadata['publisher'] . "\n";
		$input .= "datacite.publicationyear: " . $metadata['pubYear'] . "\n";
		$input .= "datacite.resourcetype: " . $metadata['resourceType'] . "\n";
		$input .= "_profile: datacite";

		// cURL Request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $call);
		curl_setopt($ch, CURLOPT_USERPWD, $userpw);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain; charset=UTF-8', 'Content-Length: ' . strlen($input)));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);

		/*returns HTTP Code for success or fail */
		$success = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($success === 201)
		{
			$out = explode('/', $output);
			$handle = trim(end($out));
		}
		else
		{
			$doierr = $success . $output;
			$doierr.= ' '.$call;
			$handle = 0;
		}

		$handle = strtoupper($handle);
		$doi = $shoulder . DS . $handle;
		curl_close($ch);

		// Prepare XML data
		if ($handle && $reserve == 0)
		{
			$xdoc      = new DomDocument;
			$xmlfile   = PublicationUtilities::getXml($row, $authors, $metadata, $doi);
			$xmlschema = trim($config->get('doi_xmlschema', 'http://schema.datacite.org/meta/kernel-2.1/metadata.xsd' ), DS);

			//Load the xml document in the DOMDocument object
			$xdoc->loadXML($xmlfile);

			//Validate the XML file against the schema
			if ($xdoc->schemaValidate($xmlschema))
			{
				/*EZID parses text received based on new lines. */
				$input  = "_target: " . $metadata['url'] ."\n";
				$input .= "datacite.creator: " . $metadata['creator'] . "\n";
				$input .= "datacite.title: ". $metadata['title'] . "\n";
				$input .= "datacite.publisher: " . $metadata['publisher'] . "\n";
				$input .= "datacite.publicationyear: " . $metadata['pubYear'] . "\n";
				$input .= "_profile: datacite". "\n";

				/*colons(:),percent signs(%),line terminators(\n),carriage returns(\r) are percent encoded for given input string  */
				$input  .= 'datacite: ' . strtr($xmlfile, array(":" => "%3A", "%" => "%25", "\n" => "%0A", "\r" => "%0D")) . "\n";

				// Make service path
				$call  = $service . DS . 'id' . DS . 'doi:' . $doi;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $call);

				/* Purdue Hubzero Username/Password */
				curl_setopt($ch, CURLOPT_USERPWD, $userpw);
				curl_setopt($ch, CURLOPT_POST, true);

				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain; charset=UTF-8', 'Content-Length: ' . strlen($input)));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($ch);
				curl_close($ch);
			}
			else
			{
				$doierr .= JText::_('COM_PUBLICATIONS_ERROR_DOI_XML_INVALID');
			}
		}

		return $handle ? $doi : NULL;
	}

	/**
	 * Update DOI information
	 *
	 * Long description (if any) ...
	 *
	 * @param      string 	$doi 		DOI handle, e.g. 10.4231/D3F47GT6N
	 * @param      array 	$row 		Publication version info
	 * @param      array 	$authors 	Publication version authors
	 * @param      array 	$config 	Publications component config
	 * @param      array 	$metadata 	Array of metadata
	 * @param      string 	&$doierr 	Collector for errors
	 * @param      boolean 	$sendXML 	Send XML metadata or not
	 * @return     true on success or false on error
	 */
	public static function updateDoi( $doi, $row, $authors, $config, $metadata = array(), &$doierr = '', $sendXML = true)
	{
		if (!$doi)
		{
			$doierr .= JText::_('COM_PUBLICATIONS_ERROR_DOI_UPDATE_NO_HANDLE');
			return false;
		}

		// Check that this is hub-created DOI
		$shoulder   = $config->get('doi_shoulder');
		$rShoulder  = substr($doi, 0, strlen($shoulder));
		if ($rShoulder != $shoulder)
		{
			// We are not updating DOIs issued by others
			return true;
		}

		// Get configs
		$juri = JURI::getInstance();

		$jconfig = JFactory::getConfig();
		$service = trim($config->get('doi_service'), DS);
		$userpw  = $config->get('doi_userpw');

		// Collect metadata
		$metadata['publisher'] = $config->get('doi_publisher', $jconfig->getValue('config.sitename') );
		$metadata['pubYear']   = $row->published_up && $row->published_up != '0000-00-00 00:00:00'
								? date( 'Y', strtotime($row->published_up)) : date( 'Y' );

		// Get config
		$livesite = $jconfig->getValue('config.live_site')
			? $jconfig->getValue('config.live_site')
			: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);
		if (!$livesite)
		{
			$doierr .= JText::_('COM_PUBLICATIONS_ERROR_DOI_MISSING_LIVE_CONFIG');
			return false;
		}

		if (!$service || !$userpw)
		{
			$doierr .= JText::_('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE');
			return false;
		}

		$metadata['url'] = $livesite . DS . 'publications' . DS . $row->publication_id . DS . $row->version_number;
		$metadata['title'] = stripslashes(htmlspecialchars($row->title));

		// Get first author / creator name
		if (count($authors) > 0)
		{
			$creatorName = $authors[0]->name;
			$creatorOrcid = (isset($authors[0]->orcid) ? $authors[0]->orcid : '');
		}
		else
		{
			$creator = JUser::getInstance($row->created_by);
			$creatorName = $creator->get('name');
			$creatorOrcid = $creator->get('orcid');
		}

		// Format name
		$nameParts            = explode(" ", $creatorName);
		$metadata['creator']  = end($nameParts);
		$metadata['creator'] .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';
		$metadata['creatorOrcid'] = $creatorOrcid;

		// Start XML
		if ($sendXML == true)
		{
			$xdoc      = new DomDocument;
			$xmlfile   = PublicationUtilities::getXml($row, $authors, $metadata, $doi);
			$xmlschema = trim($config->get('doi_xmlschema', 'http://schema.datacite.org/meta/kernel-2.1/metadata.xsd' ), DS);

			// Load the xml document in the DOMDocument object
			$xdoc->loadXML($xmlfile);
		}

		/*EZID parses text received based on new lines. */
		$input  = "_target: " . $metadata['url'] ."\n";
		$input .= "datacite.creator: " . $metadata['creator'] . "\n";
		$input .= "datacite.title: ". $metadata['title'] . "\n";
		$input .= "datacite.publisher: " . $metadata['publisher'] . "\n";
		$input .= "datacite.publicationyear: " . $metadata['pubYear'] . "\n";
		$input .= "datacite.resourcetype: " . $metadata['resourceType'] . "\n";
		$input .= "_profile: datacite". "\n";

		//Validate the XML file against the schema
		if ($sendXML == true && $xdoc->schemaValidate($xmlschema))
		{
			/*colons(:),percent signs(%),line terminators(\n),carriage returns(\r) are percent encoded for given input string  */
			$input  .= 'datacite: ' . strtr($xmlfile, array(":" => "%3A", "%" => "%25", "\n" => "%0A", "\r" => "%0D")) . "\n";
		}
		elseif ($sendXML == true)
		{
			$doierr .= JText::_('COM_PUBLICATIONS_ERROR_DOI_XML_INVALID');
			return false;
		}

		// Make service path
		$call  = $service . DS . 'id' . DS . 'doi:' . $doi;

		// cURL Request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $call);
		curl_setopt($ch, CURLOPT_USERPWD, $userpw);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain; charset=UTF-8', 'Content-Length: ' . strlen($input)));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);

		return true;
	}

	/**
	 * Get XML
	 *
	 * @param      array 	$row 		Publication version info
	 * @param      array 	$authors 	Publication version authors
	 * @param      array 	$metadata 	Array of metadata
	 * @param      string 	$doi 		DOI handle, e.g. 10.4231/D3F47GT6N
	 * @return     xml output
	 */
	public function getXml( $row, $authors, $metadata, $doi = 0)
	{
		$datePublished = JHTML::_('date', $row->published_up, 'Y-m-d');
		$dateAccepted  = date('Y-m-d');

		$xmlfile = '<?xml version="1.0" encoding="UTF-8"?><resource xmlns="http://datacite.org/schema/kernel-2.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://datacite.org/schema/kernel-2.1 http://schema.datacite.org/meta/kernel-2.1/metadata.xsd">';
		$xmlfile.='<identifier identifierType="DOI">'.$doi.'</identifier>';
		$xmlfile.='<creators>';
		if (count($authors) > 0)
		{
			foreach ($authors as $author)
			{
				$nameParts    = explode(" ", $author->name);
				$name  = end($nameParts);
				$name .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';
				$xmlfile.='<creator>';
				$xmlfile.='	<creatorName>'.$name.'</creatorName>';
				if (isset($author->orcid) && !empty($author->orcid))
				{
					$xmlfile.='	<nameIdentifier nameIdentifierScheme="ORCID">'.$author->orcid.'</nameIdentifier>';
				}
				$xmlfile.='</creator>';
			}
		}
		else
		{
			$xmlfile.='<creator>';
			$xmlfile.='	<creatorName>'.$metadata['creator'].'</creatorName>';
			if (array_key_exists('creatorOrcid', $metadata) && !empty($metadata['creatorOrcid']))
			{
				$xmlfile.='	<nameIdentifier nameIdentifierScheme="ORCID">'.'http://orcid.org/'.$metadata['creatorOrcid'].'</nameIdentifier>';
			}
			$xmlfile.='</creator>';
		}
		$xmlfile.='</creators>';
		$xmlfile.='<titles>
			<title>'.$metadata['title'].'</title>
		</titles>
		<publisher>'.$metadata['publisher'].'</publisher>
		<publicationYear>'.$metadata['pubYear'].'</publicationYear>';
		if (isset($metadata['contributor']) && $metadata['contributor'])
		{
			$xmlfile.='<contributors>';
			$xmlfile.='	<contributor contributorType="ProjectLeader">';
			$xmlfile.='		<contributorName>'.htmlspecialchars($metadata['contributor']).'</contributorName>';
			$xmlfile.='	</contributor>';
			$xmlfile.='</contributors>';
		}
		$xmlfile.='<dates>
			<date dateType="Valid">'.$datePublished.'</date>
			<date dateType="Accepted">'.$dateAccepted.'</date>
		</dates>
		<language>'.$metadata['language'].'</language>
		<resourceType resourceTypeGeneral="' . $metadata['resourceType'] . '">'.$metadata['typetitle'].'</resourceType>';
		if (isset($metadata['relatedDoi']) && $metadata['relatedDoi'])
		{
			$xmlfile.='<relatedIdentifiers>
				<relatedIdentifier relatedIdentifierType="DOI" relationType="IsNewVersionOf">' . $metadata['relatedDoi'] . '</relatedIdentifier>
			</relatedIdentifiers>';
		}
		$xmlfile.= '<version>'.$row->version_label.'</version>';
		if (isset($metadata['license']))
		{
			$xmlfile.='<rights>'.htmlspecialchars($metadata['license']).'</rights>';
		}
		$xmlfile .='<descriptions>
			<description descriptionType="Abstract">';
		$xmlfile.= stripslashes(htmlspecialchars($row->abstract));
		$xmlfile.= '</description>
			</descriptions>
		</resource>';
		return $xmlfile;
	}

	/**
	 * Collect DOI metadata
	 *
	 * @param      object $pub      Publication
	 * @return     void
	 */
	public static function collectMetadata($pub)
	{
		if (!$pub || !$pub->id)
		{
			return false;
		}

		$database = JFactory::getDBO();

		// Load version
		$row = new PublicationVersion($database);

		// Collect metadata
		$metadata = array();

		if (!isset($pub->_category))
		{
			// Get type info
			$pub->_category = new PublicationCategory( $database );
			$pub->_category->load($pub->category);
			$pub->_category->_params = new JParameter( $pub->_category->params );
		}

		if (!$pub->_category)
		{
			return false;
		}

		$metadata['typetitle'] 		= $pub->_category->alias;
		$metadata['resourceType'] 	= $pub->_category->dc_type ? $pub->_category->dc_type : 'Dataset';
		$metadata['language'] 		= 'en';
		$metadata['version']		= $pub->version_label;
		$metadata['title'] 			= stripslashes(htmlspecialchars($pub->title));

		if (!isset($pub->_project))
		{
			// Get project
			$pub->_project = new Project($database);
			$pub->_project->load($pub->project_id);
		}

		if (!$pub->_project)
		{
			return false;
		}

		// Get dc:contibutor
		$profile = \Hubzero\User\Profile::getInstance(JFactory::getUser()->get('id'));
		$owner 	 = $pub->_project->owned_by_user ? $pub->_project->owned_by_user : $pub->_project->created_by_user;
		if ($profile->load( $owner ))
		{
			$metadata['contributor'] = $profile->get('name');
		}

		// Get previous version DOI
		$lastPub = $row->getLastPubRelease($objP->id);
		if ($lastPub && $lastPub->doi)
		{
			$metadata['relatedDoi'] = $row->version_number > 1 ? $lastPub->doi : '';
		}

		// Get previous version DOI
		$lastPub = $row->getLastPubRelease($pub->id);
		if ($lastPub && $lastPub->doi)
		{
			$metadata['relatedDoi'] = $pub->version_number > 1 ? $lastPub->doi : '';
		}

		// Get license type
		$objL = new PublicationLicense( $database);
		if ($objL->loadLicense($pub->license_type))
		{
			$metadata['license']    = $objL->title;
		}

		return $metadata;
	}
}
