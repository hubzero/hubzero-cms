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
	 * Short description for 'updateDoi'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string &$doi Parameter description (if any) ...
	 * @param      array $row Parameter description (if any) ...
	 * @param      array $authors Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @param      array $metadata Parameter description (if any) ...
	 * @param      string &$doierr Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function updateDoi( $doi, $row, $authors, $config, $metadata = array(), &$doierr = '')
	{		
		if (!$doi)
		{
			$doierr .= 'Missing DOI handle for update';
			return false;
		}
		
		// Get configs
		$jconfig 	=& JFactory::getConfig();
		$service    = trim($config->get('doi_service', 'https://n2t.net/ezid' ), DS);
		
		// Collect metadata
		$metadata['publisher']  = $config->get('doi_publisher', $jconfig->getValue('config.sitename') );
		$metadata['pubYear'] 	= date( 'Y' );
		
		// Get config
		$livesite = $jconfig->getValue('config.live_site');
		if (!$livesite) 
		{
			$doierr .= 'Missing live site configuration';
			return false;
		}

		$metadata['url'] = $livesite.DS.'publications'.DS.$row->publication_id.DS.'?v='.$row->version_number;
		$metadata['title'] = stripslashes(htmlspecialchars($row->title));
		
		// Get first author / creator name
		if (count($authors) > 0) 
		{
			$creatorName = $authors[0]->name;
		}
		else 
		{
			$creator = JUser::getInstance($row->created_by);
			$creatorName = $creator->get('name');
		}

		// Format name
		$nameParts    = explode(" ", $creatorName);
		$metadata['creator']  = end($nameParts);
		$metadata['creator'] .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';	
		
		// Start XML
		$xdoc 		= new DomDocument;
		$xmlfile 	= PublicationUtilities::getXml($row, $authors, $metadata, $doi, $do = 'doi');	
		$xmlschema 	= 'http://schema.datacite.org/meta/kernel-2.1/metadata.xsd';

		// Load the xml document in the DOMDocument object
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
			$input .= "datacite.resourcetype: " . $metadata['resourceType'] . "\n";
			$input .= "_profile: datacite". "\n";

		    /*colons(:),percent signs(%),line terminators(\n),carriage returns(\r) are percent encoded for given input string  */ 
		    $input  .= 'datacite: ' . strtr($xmlfile, array(":" => "%3A", "%" => "%25", "\n" => "%0A", "\r" => "%0D")) . "\n"; 
		
			// Make service path
			$call  = $service . DS . 'id' . DS . 'doi:' . $doi;	

		    $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $call);

		    /* Purdue Hubzero Username/Password */
		    curl_setopt($ch, CURLOPT_USERPWD, '');
		    curl_setopt($ch, CURLOPT_POST, true);

		    curl_setopt($ch, CURLOPT_HTTPHEADER,
		      array('Content-Type: text/plain; charset=UTF-8',
		            'Content-Length: ' . strlen($input)));
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    $output = curl_exec($ch);
		    curl_close($ch);
		} 
		else 
		{
			$doierr .= "XML is invaild. Unable to upload XML as it is invalid. Please modify the created DOI with a valid XML .\n";
			return false;
		}
		
		return true;
	}
	
	/**
	 * Short description for 'registerDoi'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $row Parameter description (if any) ...
	 * @param      array $authors Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @param      array $metadata Parameter description (if any) ...
	 * @param      string &$doierr Parameter description (if any) ...
	 * @param      string $do Parameter description (if any) ...
	 * @param      string $partial Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function registerDoi( $row, $authors, $config, $metadata = array(), &$doierr = '', $do = 'doi', $reserve = 0 )
	{
		// Get configs
		$jconfig 	=& JFactory::getConfig();
		$shoulder   = $do == 'doi' ? $config->get('doi_shoulder', '10.5072' ) : $config->get('ark_shoulder', '/99999' );
		$service    = trim($config->get('doi_service', 'https://n2t.net/ezid' ), DS);
		$prefix     = $do == 'doi' ? $config->get('doi_prefix', '' ) : $config->get('ark_prefix', '' );
		$handle     = '';
		$doi 		= '';
		
		// Collect metadata
		$metadata['publisher']  = $config->get('doi_publisher', '' );
		$metadata['pubYear'] 	= date( 'Y' );
				
		// Make service path
		$which = $do == 'doi' ? 'doi:' : 'ark:';
		$call  = $service . DS . 'shoulder' . DS . $which . $shoulder;
		$call .= $prefix ? DS . $prefix : DS;		
		
		// Get publisher name
		if (!$metadata['publisher']) 
		{
			$metadata['publisher'] = $jconfig->getValue('config.sitename');
		}
	
		// Get config
		$livesite = $jconfig->getValue('config.live_site');
		if (!$livesite) 
		{
			$doierr .= 'Missing live site configuration';
			return false;
		}
		
		$metadata['url'] = $livesite.DS.'publications'.DS.$row->publication_id.DS.'?v='.$row->version_number;
		
		// Get first author / creator name
		if (count($authors) > 0) 
		{
			$creatorName = $authors[0]->name;
		}
		else 
		{
			$creator = JUser::getInstance($row->created_by);
			$creatorName = $creator->get('name');
		}
		
		// Format name
		$nameParts    = explode(" ", $creatorName);
		$metadata['creator']  = end($nameParts);
		$metadata['creator'] .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';	
		
		$metadata['title'] = stripslashes(htmlspecialchars($row->title));								

		// Start input
		$input  = "_target: " . $metadata['url'] ."\n";
		$input .= "datacite.creator: " . $metadata['creator'] . "\n";
		$input .= "datacite.title: ". $metadata['title'] . "\n";
		$input .= "datacite.publisher: " . $metadata['publisher'] . "\n";
		$input .= "datacite.publicationyear: " . $metadata['pubYear'] . "\n";
		$input .= "datacite.resourcetype: " . $metadata['resourceType'] . "\n";
		$input .= "_profile: datacite";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $call);

		/* Purdue Hubzero Username/Password */
		curl_setopt($ch, CURLOPT_USERPWD, '');
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_HTTPHEADER,
		  array('Content-Type: text/plain; charset=UTF-8',
		        'Content-Length: ' . strlen($input)));
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
		
		$handle = $do == 'doi' ? strtoupper($handle) : $handle;
		$doi = $shoulder . DS . $handle;
		curl_close($ch);
		
		// Prepare XML data
		if ($handle && $reserve == 0) 
		{
			$xdoc 		= new DomDocument;
			$xmlfile 	= PublicationUtilities::getXml($row, $authors, $metadata, $doi, $do);	
			$xmlschema 	= 'http://schema.datacite.org/meta/kernel-2.1/metadata.xsd';
			
			//Load the xml document in the DOMDocument object
			$xdoc->loadXML($xmlfile);

			//Validate the XML file against the schema
			if ($xdoc->schemaValidate($xmlschema) || $do == 'ark') 
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
				if ($do == 'ark')
				{
					$call  = $service . DS . 'id' . DS . 'ark:' . $doi;
				}
				else 
				{
					$call  = $service . DS . 'id' . DS . 'doi:' . $doi;	
				}	

			    $ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $call);

			    /* Purdue Hubzero Username/Password */
			    curl_setopt($ch, CURLOPT_USERPWD, '');
			    curl_setopt($ch, CURLOPT_POST, true);

			    curl_setopt($ch, CURLOPT_HTTPHEADER,
			      array('Content-Type: text/plain; charset=UTF-8',
			            'Content-Length: ' . strlen($input)));
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    $output = curl_exec($ch);
			    curl_close($ch);
			} 
			else 
			{
				$doierr .= "XML is invaild. DOI has been created but unable to upload XML as it is invalid. Please modify the created DOI with a valid XML .\n";
			}		
		}
		
		return $handle ? $doi : NULL;
	}
	
	/**
	 * Short description for 'getXml'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $row Parameter description (if any) ...
	 * @param      array $authors Parameter description (if any) ...
	 * @param      array $metadata Parameter description (if any) ...
	 * @param      unknown $doi Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getXml( $row, $authors, $metadata, $doi = 0, $do = 'doi')
	{
		$datePublished = JHTML::_('date', $row->published_up, '%Y-%m-%d');
		$dateAccepted  = date( 'Y-m-d' );
		
		$xmlfile = '<?xml version="1.0" encoding="UTF-8"?><resource xmlns="http://datacite.org/schema/kernel-2.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://datacite.org/schema/kernel-2.1 http://schema.datacite.org/meta/kernel-2.1/metadata.xsd">';
		if ($do == 'doi')
		{
			$xmlfile.='<identifier identifierType="DOI">'.$doi.'</identifier>';	
		}
		else
		{
			if (substr($doi,0,1) == '/') 
			{
				$doi = substr($doi,1,strlen($doi));
			}
			$xmlfile.='<identifier identifierType="ARK">'.$doi.'</identifier>';
		}		
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
				$xmlfile.='</creator>';
			}
		}
		else 
		{
			$xmlfile.='<creator>';
			$xmlfile.='	<creatorName>'.$metadata['creator'].'</creatorName>';
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
}

