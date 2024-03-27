<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;
use stdClass;
use Component;
use Request;
use Config;
use Lang;
use User;

include_once __DIR__ . DS . 'publication.php';

/**
 * Publication doi model class
 */
class Doi extends Obj
{
	/**
	 * DOI Configs
	 *
	 * @var  object
	 */
	public $_configs = null;

	/**
	 * Container for properties
	 *
	 * @var  array
	 */
	private $_data = array();

	/**
	 * Database
	 *
	 * @var  object
	 */
	private $_db = null;

	/**
	 * DataCite and EZID switch options
	 *
	 * @const
	 */
	const SWITCH_OPTION_NONE = 0;
	const SWITCH_OPTION_EZID = 1;
	const SWITCH_OPTION_DATACITE = 2;

	/**
	 * Publication state transition
	 *
	 * @const
	 */
	const STATE_FROM_PUBLISHED_TO_DRAFTREADY = 0;
	const STATE_FROM_DRAFTREADY_TO_PUBLISHED = 1;
	
	/**
	 * DataCite
	 *
	 * @const
	 */
	const DATACITE = "datacite::";
	
	/**
	 * Relation type
	 *
	 * @const
	 */
	const REFERENCES = "references";
	const REFERENCEDBY = "referencedby";
	const ISREFERENCEDBY = "IsReferencedBy";
	
	/*
	* Handle
	*/
	const HANDLE = "handle";
	const HANDLE_DOMAIN_NAME = "hdl.handle.net";

	/**
	 * Constructor
	 *
	 * @param   object  $pub
	 * @return  void
	 */
	public function __construct($pub = null)
	{
		$this->_db = \App::get('db');

		// Set configs
		$this->configs();

		// Map to DOI fields
		$this->mapPublication($pub);
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
			$params = Component::params('com_publications');

			$configs = new stdClass;
			$configs->shoulder  = $params->get('doi_shoulder');
			$configs->prefix    = $params->get('doi_prefix');
			$configs->dataciteEZIDSwitch = $params->get('datacite_ezid_doi_service_switch');
			$configs->dataciteServiceURL = $params->get('datacite_doi_service');
			$configs->dataciteUserPW = $params->get('datacite_doi_userpw');
			$configs->ezidServiceURL = $params->get('ezid_doi_service');
			$configs->ezidUserPW = $params->get('ezid_doi_userpw');
			$configs->publisher = $params->get('doi_publisher', Config::get('sitename'));
			$configs->livesite  = trim(Request::root(), DS);
			$configs->xmlSchema = trim($params->get('doi_xmlschema', 'http://schema.datacite.org/meta/kernel-4/metadata.xsd'), DS);

			$this->_configs = $configs;
		}

		return $this->_configs;
	}

	/**
	 * Check if a property is set
	 *
	 * @param   string   $property  Name of property to set
	 * @return  boolean  True if set
	 */
	public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Set a property
	 *
	 * @param   string  $property  Name of property to set
	 * @param   mixed   $value     Value to set property to
	 * @return  void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 *
	 * @param   string  $property  Name of property to retrieve
	 * @return  mixed
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
	 * @return  boolean
	 */
	public function on()
	{
		// Make sure configs are loaded
		$this->configs();

		// Check required
		if ($this->_configs->dataciteEZIDSwitch && $this->_configs->shoulder && (($this->_configs->dataciteServiceURL && $this->_configs->dataciteUserPW) || ($this->_configs->prefix && $this->_configs->ezidServiceURL && $this->_configs->ezidUserPW)))
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
			if ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_DATACITE)
			{
				$call  = $this->_configs->dataciteServiceURL . DS . 'id' . DS . 'doi:' . $doi;
			}
			elseif ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_EZID)
			{
				$call  = $this->_configs->ezidServiceURL . DS . 'id' . DS . 'doi:' . $doi;
			}
		}
		else
		{
			if ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_DATACITE)
			{
				$call  = $this->_configs->dataciteServiceURL . DS . 'shoulder' . DS . 'doi:';
			}
			elseif ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_EZID)
			{
				$call  = $this->_configs->ezidServiceURL . DS . 'shoulder' . DS . 'doi:';
			}

			$call .= $this->_configs->shoulder;
			$call .= $this->_configs->prefix ? DS . $this->_configs->prefix : DS;

		}
		return $call;
	}

	/**
	 * Map publication object to DOI fields
	 *
	 * @param   object  $pub  Instance of Components\Publications\Models\Publication
	 * @return  void
	 */
	public function mapPublication($pub = null)
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
		$this->set('abstract', htmlspecialchars($pub->version->description));
		$this->set('synopsis', htmlspecialchars($pub->version->abstract));
		
		$this->set('url', $this->_configs->livesite . DS . 'publications'. DS . $pub->id . DS . $pub->version->version_number);

		// Set dates
		$pubYear = $pub->version->published_up && $pub->version->published_up != $this->_db->getNullDate()
				? date('Y', strtotime($pub->version->published_up)) : date('Y');
		$pubDate = $pub->version->published_up && $pub->version->published_up != $this->_db->getNullDate()
				? date('Y-m-d', strtotime($pub->version->published_up)) : date('Y-m-d');
		$acceptedDate = $pub->version->accepted && $pub->version->accepted != $this->_db->getNullDate()
				? date('Y-m-d', strtotime($pub->version->accepted)) : "0000-00-00";
		$submittedDate = $pub->version->submitted && $pub->version->submitted != $this->_db->getNullDate()
				? date('Y-m-d', strtotime($pub->version->submitted)) : "0000-00-00";
		$this->set('pubYear', $pubYear);
		$this->set('datePublished', $pubDate);
		$this->set('dateAccepted', $acceptedDate);
		$this->set('dateSubmitted', $submittedDate);
		
		// Map authors & creator
		$this->set('authors', $pub->authors());
		$this->mapUser($pub->version->created_by, $pub->_authors, 'creator');
		
		// Publication contact person as contributor
		$contactPerson = [];
		foreach($this->get('authors') as $author)
		{
			if ($author->repository_contact == 1)
			{
				$contactPerson[] = $author;
			}
		}
		$this->set('contactPerson', $contactPerson);

		// Map resource type
		$category = $pub->category();
		$dcType   = $category->dc_type ? $category->dc_type : 'Dataset';
		$this->set('resourceType', $dcType);
		$categoryName = ($category->alias == "series") ? "Dataset series" : $category->name;
		$this->set('resourceTypeTitle', htmlspecialchars($categoryName));

		// Map license
		$license = $pub->license();
		$licenseTitle = is_object($license) ? $license->title : null;
		$this->set('license', htmlspecialchars($licenseTitle));
		
		$licenseURI = is_object($license) ? $license->url : null;
		if ($licenseURI != null && !empty($licenseURI))
		{
			$this->set('licenseURI', $licenseURI);
		}

		// Map related identifier
		$prevPub = $pub->prevPublication();
		if ($prevPub && $prevPub->doi)
		{
			$this->set('doiOfPrevPub', $prevPub->doi);
		}
		
		$nextPub = $pub->nextPublication();
		if ($nextPub && $nextPub->doi)
		{
			$this->set('doiOfNextPub', $nextPub->doi);
		}
		
		$citations = $pub->getCitationsForMetadataSet();
		if ($citations)
		{
			$this->set('citations', $citations);
		}
		
		// Format
		$type = $pub->masterType();
		$this->set('pubType', $type->id);
		$attachments = $pub->attachments();
		
		if ($type->id == 1)
		{
			// get the quantity of files of file publication
			$quantity = $pub->attachmentsCount();
			
			// get mime-type for all files
			$mimeTypes = \Components\Publications\Helpers\Html::getMimeTypes($type->id, $pub->version->publication_id, $pub->version->id, $pub->version->secret, $attachments);
		}
		else if($type->id == 5)
		{
			// get the quantity of publication in series publication.
			$quantity = count($attachments[1]);
		}
		else if ($type->id == 7)
		{
			// get the quantity of the support and gallery files in database publication
			$quantity = $pub->attachmentsCount();
			$quantity--;
			
			$files = \Components\Publications\Helpers\Html::getdatabaseFiles($pub->version->publication_id, $pub->version->id);
			
			if ($files != false && !empty($files))
			{
				$quantity += count($files);
			}
			
			// get MIME type for support and gallery file
			$mimeTypes = \Components\Publications\Helpers\Html::getMimeTypes($type->id, $pub->version->publication_id, $pub->version->id, $pub->version->secret, $attachments);
			
			// get MIME type of files in data directory
			if ($files != false && !empty($files))
			{
				foreach ($files as $file)
				{
					$mimeType = mime_content_type($file);
					if ($mimeType && !in_array($mimeType, $mimeTypes))
					{
						$mimeTypes[] = $mimeType;
					}
				}
			}
		}
		else
		{
			$quantity = 0;
			$mimeTypes = [];
		}
		$this->set('fileCount', $quantity);
		if ($type->id == 1 || $type->id == 7)
		{
			$this->set('mimeTypes', $mimeTypes);
		}
		
		// Size
		$path = \Components\Publications\Helpers\Html::buildPubPath($pub->version->publication_id, $pub->version->id, '', '', 1);
		$path .= DIRECTORY_SEPARATOR . str_replace(['.', '/'], '_', $pub->version->doi) . ".zip";
		
		if ($type->id == 1 && file_exists($path))
		{
			$size = filesize($path);
			if ($size)
			{
				$this->set('size', \Hubzero\Utility\Number::formatBytes($size));
			}
		}
		
		// Project grant info
		$project = $pub->project();
		$grantInfo = [];
		if ($project->params->get('grant_title'))
		{
			$grantInfo['grant_title'] = $project->params->get('grant_title');
		}
		if ($project->params->get('award_number'))
		{
			$grantInfo['award_number'] = $project->params->get('award_number');
		}
		if ($project->params->get('grant_agency'))
		{
			$grantInfo['grant_agency'] = $project->params->get('grant_agency');
		}
		if ($project->params->get('grant_agency_id'))
		{
			$grantInfo['grant_agency_id'] = $project->params->get('grant_agency_id');
		}
		if (!empty($grantInfo))
		{
			$this->set('grantInfo', $grantInfo);
		}
		
		// Get tags
		$tags = $pub->getTagsOfPublication();
		$regTags = $subjectTags = $fosTags = [];
		
		if ($tags)
		{
			foreach ($tags as $tag)
			{
				$fosTagObjects = $pub->getFOSTag($tag->id);
				
				if (!$fosTagObjects)
				{
					$regTags[] = $tag;
				}
				else
				{
					$subjectTags[] = $tag;
					
					foreach ($fosTagObjects as $fosTag)
					{
						if (!in_array($fosTag, $fosTags))
						{
							$fosTags[] = $fosTag;
						}
					}
				}
			}
			
			if (!empty($regTags))
			{
				$this->set('regTags', $regTags);
			}
			
			if (!empty($subjectTags))
			{
				$this->set('subjectTags', $subjectTags);
			}
			
			if (!empty($fosTags))
			{
				$this->set('fosTags', $fosTags);
			}
		}
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
		if (!empty($authors) && count($authors) > 0)
		{
			$name = $authors[0]->name;
			$orcid = (isset($authors[0]->orcid) ? $authors[0]->orcid : '');
		}
		elseif ($uid)
		{
			$user = User::getInstance($uid);
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
		$this->set('pubYear', date('Y'));
		$this->set('publisher', htmlspecialchars($this->_configs->publisher));
		$this->set('resourceType', 'Dataset');
		$this->set('resourceTypeTitle', 'Dataset');
		$this->set('datePublished', date('Y-m-d'));
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
		$xmlfile  = '<?xml version="1.0" encoding="UTF-8"?><resource xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://datacite.org/schema/kernel-4" xsi:schemaLocation="http://datacite.org/schema/kernel-4 https://schema.datacite.org/meta/kernel-4.5/metadata.xsd">';
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
				
				if ($author->user_id != 0)
				{
					if (!empty($author->givenName))
					{
						$xmlfile .='	<givenName>' . $author->givenName . '</givenName>';
					}
					
					if (!empty($author->surname))
					{
						$xmlfile .='	<familyName>' . $author->surname . '</familyName>';
					}
				}
				else
				{
					if (!empty($author->firstName))
					{
						$xmlfile .='	<givenName>' . $author->firstName . '</givenName>';
					}
					
					if (!empty($author->lastName))
					{
						$xmlfile .='	<familyName>' . $author->lastName . '</familyName>';
					}
				}
				
				if (isset($author->orcid) && !empty($author->orcid))
				{
					$xmlfile.='	<nameIdentifier nameIdentifierScheme="ORCID" schemeURI="https://orcid.org/">' . $author->orcid . '</nameIdentifier>';
				}
				
				if ($author->organization)
				{
					$orgidAtr = (isset($author->orgid) && !empty($author->orgid)) ? ' affiliationIdentifier="' . $author->orgid . '"' . ' affiliationIdentifierScheme="ROR" schemeURI="https://ror.org"' : "";
					$xmlfile .= ' <affiliation' .  $orgidAtr . '>' . $author->organization . '</affiliation>';
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
				$xmlfile.='	<nameIdentifier nameIdentifierScheme="ORCID" schemeURI="https://orcid.org/">' .  $this->get('creatorOrcid') . '</nameIdentifier>';
			}
			$xmlfile.='</creator>';
		}
		$xmlfile.='</creators>';
		$xmlfile.='<titles>
			<title>' . $this->get('title') . '</title>
		</titles>
		<publisher>' . $this->get('publisher') . '</publisher>
		<publicationYear>' . $this->get('pubYear') . '</publicationYear>';
		
		if ($this->get('contactPerson'))
		{
			$xmlfile.='<contributors>';
			
			foreach ($this->get('contactPerson') as $contactPerson)
			{
				$xmlfile.='	<contributor contributorType="ContactPerson">';
				$xmlfile.='		<contributorName nameType="Personal">' . $contactPerson->name . '</contributorName>';
				if ($contactPerson->user_id)
				{
					$xmlfile.='		<givenName>' . $contactPerson->givenName . '</givenName>';
					$xmlfile.='		<familyName>' . $contactPerson->surname . '</familyName>';
				}
				else
				{
					$xmlfile.='		<givenName>' . $contactPerson->firstName . '</givenName>';
					$xmlfile.='		<familyName>' . $contactPerson->lastName . '</familyName>';
				}
				
				if (isset($contactPerson->orcid) && !empty($contactPerson->orcid))
				{
					$xmlfile.='		<nameIdentifier nameIdentifierScheme="ORCID" schemeURI="https://orcid.org/">' .  $contactPerson->orcid . '</nameIdentifier>';
				}
				
				if ($contactPerson->organization)
				{
					$orgidAtr = (isset($contactPerson->orgid) && !empty($contactPerson->orgid)) ? ' affiliationIdentifier="' . $contactPerson->orgid . '"' . ' affiliationIdentifierScheme="ROR" schemeURI="https://ror.org"' : "";
					$xmlfile .= '		<affiliation' .  $orgidAtr . '>' . $contactPerson->organization . '</affiliation>';
				}
				
				$xmlfile.='	</contributor>';
			}
			
			$xmlfile.='</contributors>';
		}
		
		$xmlfile.='<dates>';
		$xmlfile .= '<date dateType="Available">' . $this->get('datePublished') . '</date>';
		$xmlfile .= '<date dateType="Submitted">' . $this->get('dateSubmitted') . '</date>';
		$xmlfile .= '<date dateType="Accepted">' . $this->get('dateAccepted') . '</date>';
		$xmlfile .= '</dates>';
		
		$xmlfile .= '<language>' . $this->get('language') . '</language>
		<resourceType resourceTypeGeneral="' . $this->get('resourceType') . '">' . $this->get('resourceTypeTitle') . '</resourceType>';
		
		$doiOfPrevPub = $this->get('doiOfPrevPub');
		$doiOfNextPub = $this->get('doiOfNextPub');
		$citations = $this->get('citations');
		if ($doiOfPrevPub || $doiOfNextPub || $citations)
		{
			$xmlfile .= '<relatedIdentifiers>';
			
			if ($doiOfPrevPub)
			{
				$xmlfile .= '	<relatedIdentifier relatedIdentifierType="DOI" relationType="IsNewVersionOf">' . $doiOfPrevPub . '</relatedIdentifier>';
			}
			
			if ($doiOfNextPub)
			{
				$xmlfile .= '	<relatedIdentifier relatedIdentifierType="DOI" relationType="IsPreviousVersionOf">' . $doiOfNextPub . '</relatedIdentifier>';
			}
			
			if ($citations)
			{
				foreach ($citations as $citation)
				{
					$pos = strpos(trim($citation->resourceTypeGeneral), self::DATACITE);
					
					if ($pos == 0)
					{
						$citation->resourceTypeGeneral = substr(trim($citation->resourceTypeGeneral), $pos + strlen(self::DATACITE));
					}
					
					if (!empty($citation->relationType))
					{
						if (preg_match("/" . self::REFERENCES . "/i", $citation->relationType))
						{
							$citation->relationType = ucfirst(self::REFERENCES);
						}
						else if (preg_match("/" . self::REFERENCEDBY . "/i", $citation->relationType))
						{
							$citation->relationType = self::ISREFERENCEDBY;
						}
					}
					
					if (!empty($citation->relationType) && !empty($citation->resourceTypeGeneral))
					{
						if (!empty($citation->relatedIdentifier))
						{
							$xmlfile .= '<relatedIdentifier relatedIdentifierType="DOI" relationType="' . $citation->relationType . '" resourceTypeGeneral="' . $citation->resourceTypeGeneral . '">' . trim($citation->relatedIdentifier) . '</relatedIdentifier>';
						}
						else
						{
							if (!empty($citation->url))
							{
								if (preg_match("/purl/i", $citation->url))
								{
									$xmlfile .= '<relatedIdentifier relatedIdentifierType="PURL" relationType="' . $citation->relationType . '" resourceTypeGeneral="' . $citation->resourceTypeGeneral . '">' . trim($citation->url) . '</relatedIdentifier>';
								}
								elseif (preg_match("/handle/i", $citation->url))
								{
									if (preg_match("/hdl\.handle\.net/i", $citation->url))
									{
										$pos = strpos($citation->url, self::HANDLE_DOMAIN_NAME);
										$val = substr($citation->url, $pos + strlen(self::HANDLE_DOMAIN_NAME) + 1);
									}
									else
									{
										$pos = strpos($citation->url, self::HANDLE);
										$val = substr($citation->url, $pos + strlen(self::HANDLE) + 1);
									}
									$xmlfile .= '<relatedIdentifier relatedIdentifierType="Handle" relationType="' . $citation->relationType . '" resourceTypeGeneral="' . $citation->resourceTypeGeneral . '">' . $val . '</relatedIdentifier>';
								}
								elseif (preg_match("/ark:/i", $citation->url) && !empty($citation->eprint))
								{
									$xmlfile .= '<relatedIdentifier relatedIdentifierType="ARK" relationType="' . $citation->relationType . '" resourceTypeGeneral="' . $citation->resourceTypeGeneral . '">' . $citation->eprint . '</relatedIdentifier>';
								}
								elseif (preg_match("/arxiv/i", $citation->url) && !empty($citation->eprint))
								{
									$xmlfile .= '<relatedIdentifier relatedIdentifierType="arXiv" relationType="' . $citation->relationType . '" resourceTypeGeneral="' . $citation->resourceTypeGeneral . '">' . $citation->eprint . '</relatedIdentifier>';
								}
								elseif (preg_match("/urn:/i", $citation->url) && !empty($citation->eprint))
								{
									$xmlfile .= '<relatedIdentifier relatedIdentifierType="URN" relationType="' . $citation->relationType . '" resourceTypeGeneral="' . $citation->resourceTypeGeneral . '">' . $citation->eprint . '</relatedIdentifier>';
								}
								else
								{
									$xmlfile .= '<relatedIdentifier relatedIdentifierType="URL" relationType="' . $citation->relationType . '" resourceTypeGeneral="' . $citation->resourceTypeGeneral . '">' . $citation->url . '</relatedIdentifier>';
								}
							}
						}
					}
				}
			}
			
			$xmlfile .= '</relatedIdentifiers>';
		}
		
		if ($this->get('version'))
		{
			$xmlfile.= '<version>' . $this->get('version') . '</version>';
		}
		if ($this->get('license'))
		{
			$rightsURI = $this->get('licenseURI') ? 'rightsURI="' . $this->get('licenseURI') . '"' : "";
			$xmlfile.='<rightsList><rights ' . $rightsURI . '>' . htmlspecialchars($this->get('license')) . '</rights></rightsList>';
		}
		if ($this->get('grantInfo'))
		{
			$grantInfo = $this->get('grantInfo');
			$xmlfile .= '<fundingReferences>
				<fundingReference>';
			
			if (array_key_exists('grant_agency', $grantInfo))
			{
				$xmlfile .= '	<funderName>' . $grantInfo['grant_agency'] . '</funderName>';
			}
			
			if (array_key_exists('grant_agency_id', $grantInfo))
			{
				$xmlfile .= '	<funderIdentifier funderIdentifierType="Crossref Funder ID">' . $grantInfo['grant_agency_id'] . '</funderIdentifier>';
			}
			
			if (array_key_exists('award_number', $grantInfo))
			{
				$xmlfile .= '	<awardNumber>' . $grantInfo['award_number'] . '</awardNumber>';
			}
			
			if (array_key_exists('grant_title', $grantInfo))
			{
				$xmlfile .= '	<awardTitle>' . $grantInfo['grant_title'] . '</awardTitle>';
			}
			
			$xmlfile .= '	</fundingReference>
			</fundingReferences>';
		}
		// Format
		if ($this->get('pubType') && $this->get('mimeTypes'))
		{
			$xmlfile .= '<formats>';
			$mimeTypes = $this->get('mimeTypes');
			foreach ($mimeTypes as $mimeType)
			{
				$xmlfile .= '	<format>' . $mimeType . '</format>';
			}
			$xmlfile .= '</formats>';
		}
		//Size
		if ($this->get('size'))
		{
			$xmlfile .= '<sizes>';
			$pubType = $this->get('pubType');
			if ($this->get('fileCount'))
			{
				$xmlfile .= '	<size>' . $this->get('fileCount') . ($pubType == 5 ? ($this->get('fileCount') == 1 ? " publication" : " publications") : ($this->get('fileCount') == 1 ? " file" : " files")). '</size>';
			}
			if ($this->get('size'))
			{
				$xmlfile .= '	<size>' . $this->get('size') . '</size>';
			}
			$xmlfile .= '</sizes>';
		}
		// Subject
		$xmlfile .= '<subjects>';
		if ($this->get('regTags'))
		{
			foreach ($this->get('regTags') as $regTag)
			{
				if (!empty($regTag->description) && preg_match("/^lcsh::/i", trim($regTag->description)))
				{
					$url = substr(trim(strip_tags($regTag->description)), 6);
					$xmlfile .= '<subject subjectScheme="Library of Congress Subject Headings (LCSH)" schemeURI="https://id.loc.gov/authorities/" valueURI="' . $url . '">' . $regTag->raw_tag . '</subject>';
				}
				else
				{
					$xmlfile .= '	<subject>' . $regTag->raw_tag . '</subject>';
				}
			}
		}
		if ($this->get('subjectTags'))
		{
			$subjectTags = $this->get('subjectTags');
			
			foreach ($subjectTags as $subjectTag)
			{
				if (!empty($subjectTag->description) && preg_match("/^lcsh::/i", trim($subjectTag->description)))
				{
					$url = substr(trim(strip_tags($subjectTag->description)), 6);
					$xmlfile .= '<subject subjectScheme="Library of Congress Subject Headings (LCSH)" schemeURI="https://id.loc.gov/authorities/" valueURI="' . $url . '">' . $subjectTag->raw_tag . '</subject>';
				}
				else
				{
					$xmlfile .= '	<subject>' . $subjectTag->raw_tag . '</subject>';
				}
			}
			
		}
		if ($this->get('fosTags'))
		{
			$fosTags = $this->get('fosTags');
			
			foreach($fosTags as $fosTag)
			{
				$xmlfile .= '<subject subjectScheme="Fields of Science and Technology (FOS)" schemeURI="http://www.oecd.org/science/inno" valueURI="http://www.oecd.org/science/inno/38235147.pdf">' . $fosTag->raw_tag . '</subject>';
			}
		}
		$xmlfile .= '</subjects>';
		
		$xmlfile .='<descriptions>
			<description descriptionType="Abstract">';
		$xmlfile.= stripslashes(htmlspecialchars($this->get('abstract')));
		$xmlfile.= '</description>';
		
		if ($this->get('synopsis'))
		{
			$xmlfile .= '<description descriptionType="Other">' . stripslashes($this->get('synopsis')) . '</description>';
		}
		$xmlfile .= '
			</descriptions>
		</resource>';
		return $xmlfile;
	}

	/**
	 * Run cURL to register metadata. When input $doi is null, it is going to create DOI on DataCite.
	 *
	 * @param	string	$url
	 * @param	array	$postVals
	 * @param   string   &$doi
	 *
	 * @return	boolean
	 */
	public function regMetadata($url, $postvals, &$doi)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->dataciteUserPW);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvals);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8', 'Content-Length: ' . strlen($postvals)));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		$response = curl_exec($ch);

		if (!$response)
		{
			return false;
		}

		if (!$doi)
		{
			$pattern = '/\((.*?)\)/';
			$ret = preg_match($pattern, $response, $match);
			if ($ret != 1)
			{
				return false;
			}

			$doi = $match[1];
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
	 * Run cURL to register DOI name and URL that refers to the dataset on DataCite
	 *
	 * @param	string	$url
	 * @param	array	$postVals
	 *
	 * @return	boolean
	 */
	public function regURL($url, $postvals)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->dataciteUserPW);
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
	 * @param  string  $doi  - It is null when the function is used for DOI registration. Otherwise, the function is used for updating DOI state on DataCite.
	 *
	 * @return string $doi
	 */
	public function registerMetadata($doi = null)
	{
		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		// Submit DOI metadata
		$metadataURL = $this->_configs->dataciteServiceURL . DS . 'metadata' . DS . $this->_configs->shoulder;
		$xml = $this->buildXml($doi);
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
	 * @return boolean
	 */
	public function registerURL($doi, $resURL = "")
	{
		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		// Register URL
		$url = $this->_configs->dataciteServiceURL . DS . 'doi' . DS . $doi;
		$postVals = "";
		
		if (empty($resURL))
		{
			$postvals = "doi=" . $doi . "\n" . "url=" . $this->get('url');
		}
		else
		{
			$postvals = "doi=" . $doi . "\n" . "url=" . $resURL;
		}
		
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
	 *
	 * @return  boolean
	 */
	public function dataciteMetadataUpdate($doi)
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

		$metadataURL = $this->_configs->dataciteServiceURL . DS . 'metadata';
		$xml = $this->buildXml($doi);

		$ch = curl_init($metadataURL);
		curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->dataciteUserPW);
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
	 * @param  string $doi
	 * @return boolean
	 */
	public function delete($doi = null)
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
	 * Start input
	 *
	 * @param   string  $status  DOI status [draft/reserved, findable/public, registered/unavailable]
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
			CURLOPT_USERPWD         => $this->_configs->ezidUserPW,
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
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE'));
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
	 * Update DOI status on EZID
	 *
	 * @param   string   $doi
	 * @param   string   $stateSwitch   0 - Publication changed from published to draft   1 - Publication changed from draft to published
	 * @return  boolean
	 */
	public function ezidDoiStatusUpdate($doi, $stateSwitch)
	{
		if (!$doi)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NULL'));
			return false;
		}

		if ($stateSwitch == self::STATE_FROM_PUBLISHED_TO_DRAFTREADY)
		{
			$postVals = "_status:unavailable";
		}
		elseif ($stateSwitch == self::STATE_FROM_DRAFTREADY_TO_PUBLISHED)
		{
			$postVals = "_status:public";
		}

		$url = $this->getServicePath($doi);

		$ch = curl_init($url);

		$options = array(
			CURLOPT_URL             => $url,
			CURLOPT_POST            => true,
			CURLOPT_USERPWD         => $this->_configs->ezidUserPW,
			CURLOPT_POSTFIELDS      => $postVals,
			CURLOPT_RETURNTRANSFER  => true,
			CURLOPT_HTTPHEADER      => array('Content-Type: text/plain; charset=UTF-8', 'Content-Length: ' . strlen($postVals))
		);
		curl_setopt_array($ch, $options);

		$response = curl_exec($ch);
		$success = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($success === 201 || $success === 200)
		{
			return true;
		}
		else
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_ERROR_UPDATE_STATUS'), 400);
		}
	}

	/**
	 * Update DOI status on DataCite
	 *
	 * @param   string   $doi
	 * @param   integer  $stateSwitch   0 - Publication changed from published to draftready   1 - Publication changed from draftready to published
	 *
	 * @return  boolean
	 */
	public function dataciteDoiStatusUpdate($doi, $stateSwitch)
	{
		if (!$doi)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NULL'));
			return false;
		}

		if ($stateSwitch == self::STATE_FROM_PUBLISHED_TO_DRAFTREADY)
		{
			$url = $this->_configs->dataciteServiceURL . DS . 'metadata' . DS . $doi;

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_USERPWD, $this->_configs->dataciteUserPW);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8'));
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

			$response = curl_exec($ch);

			$success = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($success === 200 || $success === 201)
			{
				return true;
			}
			else
			{
				$this->setError($success . ' ' . $response);
				return false;
			}
		}
		elseif ($stateSwitch == self::STATE_FROM_DRAFTREADY_TO_PUBLISHED)
		{
			return $this->registerMetadata($doi);
		}
	}

	/**
	 * Update DOI metadata - Entry to update DOI metadata.
	 *
	 * @param   string   $doi
	 * @param   boolean  $sendXML   -- This is set to true when using EZID DOI service
	 *
	 * @return  null
	 */
	public function update($doi, $sendXML = false)
	{
		if ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_DATACITE)
		{
			$result = $this->dataciteMetadataUpdate($doi);

			if (!$result)
			{
				$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_UPDATE_DATACITE_DOI_METADATA'));
			}
		}
		elseif ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_EZID)
		{
			$result = $this->ezidMetadataUpdate($doi, $sendXML);

			if (!$result)
			{
				$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_UPDATE_EZID_DOI_METADATA'));
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
	 *
	 * @return  string $doi or null
	 */
	public function register($regMetadata = false, $regUrl = false, $doi = null, $sendXML = false, $status = 'public')
	{
		if ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_DATACITE)
		{
			// Register DOI Metadata through DataCite service
			if ($regMetadata)
			{
				$doi = $this->registerMetadata();

				if (!$doi)
				{
					$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI'));
				}

				return $doi;
			}

			if ($regUrl && !empty($doi))
			{
				$regResult = $this->registerURL($doi);

				if (!$regResult)
				{
					$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_REGISTER_NAME_URL'));
				}

				return $regResult;
			}
		}
		elseif ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_EZID)
		{
			// Register DOI through EZID service
			if (!$regUrl)
			{
				$doi = $this->registerEZID($sendXML, $status);

				if (!$doi)
				{
					$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI'));
				}

				return $doi;
			}
		}
		elseif ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_NONE)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_NO_DOI_SERVICE_ACTIVATED'));

			return false;
		}
	}

	/**
	 * Revert - DOI state update according to specific revert operation on publication.
	 *          When a publication is reverted from published to draft, the DOI state is supposed to change
	 *          from findable/public to registered/unavailable. When such publication is submitted, the DOI state
	 *          is set to findable/public.
	 *
	 * @param   string    $doi
	 * @param   integer   $stateSwitch   0 - Publication changed from published to draft   1 - Publication changed from draft to published
	 *
	 * @return
	 */
	public function revert($doi, $stateSwitch)
	{
		if (!$this->on())
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NO_SERVICE'));
			return false;
		}

		if (!$doi)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_DOI_NULL'));
			return false;
		}

		if (($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_DATACITE)
			|| ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_EZID))
		{
			if ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_DATACITE)
			{
				$result = $this->dataciteDoiStatusUpdate($doi, $stateSwitch);
			}

			if ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_EZID)
			{
				$result = $this->ezidDoiStatusUpdate($doi, $stateSwitch);
			}

			if ($result)
			{
				return true;
			}
			else
			{
				$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_UPDATE_DOI_STATUS'));
				return false;
			}
		}
		elseif ($this->_configs->dataciteEZIDSwitch == self::SWITCH_OPTION_NONE)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_ERROR_NO_DOI_SERVICE_ACTIVATED'));
			return false;
		}
	}
}
