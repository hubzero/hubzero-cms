<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for adding JSON-LD metadata to the document
 */
class plgPublicationsJsonld extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		if (!App::isSite()
		 || Request::getWord('format') == 'raw'
		 || Request::getInt('no_html'))
		{
			return;
		}

		$publication->authors();
		$publication->license();

		// Add metadata
		$data = array();
		$data['@context'] = 'http://schema.org';
		$data['@type'] = 'Dataset';
		$data['name'] = $publication->title;
		$data['description'] = strip_tags($publication->abstract);
		
		$data['url'] = rtrim(Request::root(), '/') . '/' . ltrim(Route::url($publication->link()), '/');
		
		$nullDate = '0000-00-00 00:00:00';

		if ($publication->created && $publication->created != $nullDate)
		{
			$data['dateCreated'] = Date::of($publication->created)->toLocal('Y-m-d');
		}
		if ($publication->modified && $publication->modified != $nullDate)
		{
			$data['dateModified'] = Date::of($publication->modified)->toLocal('Y-m-d');
		}
		if ($publication->published_up && $publication->published_up != $nullDate)
		{
			$data['datePublished'] = Date::of($publication->published_up)->toLocal('Y-m-d');
		}

		if ($doi = $publication->version->get('doi'))
		{
			$data['identifier'] = $doi;
			$data['@id'] = $doi;
		}
		else
		{
			$data['identifier'] = Request::root() . Route::url($publication->link());
			$data['@id'] = Request::root() . Route::url($publication->link());
		}

		$license = $publication->license();
		if (is_object($license))
		{
			$data['license'] = $license->title;
			if ($license->url)
			{
				$data['sdLicense'] = $license->url;
			}
		}

		$keywords = array();
		foreach ($publication->getTags() as $tag)
		{
			$keywords[] = $tag->raw_tag;
		}

		if (!empty($keywords))
		{
			$data['keywords'] = $keywords;
		}

		$authors = array();

		foreach ($publication->_authors as $contributor)
		{
			if (strtolower($contributor->role) == 'submitter')
			{
				continue;
			}

			$givenName = $contributor->givenName;
			$familyName = $contributor->surname;

			if (!$givenName)
			{
				if ($contributor->name)
				{
					$name = stripslashes($contributor->name);
				}
				else
				{
					$name = stripslashes($contributor->p_name);
				}

				$nameParts = explode(' ', $name);

				if (!empty($nameParts))
				{
					$givenName  = array_shift($nameParts);
					$familyName = array_pop($nameParts);
				}
			}

			if (!$contributor->organization)
			{
				$contributor->organization = $contributor->p_organization;
			}
			$contributor->organization = stripslashes(trim($contributor->organization));

			$author = array(
				'@type'      => 'Person',
				'givenName'  => $givenName,
				'familyName' => $familyName
			);

			if ($contributor->organization)
			{
				$org = array(
					'@type' => 'Organization',
					'name'  => $contributor->organization
				);

				$author['affiliation'] = $org;
			}

			if ($contributor->user_id && $contributor->open)
			{
				$author['url'] = rtrim(Request::root(), '/') . '/' . ltrim(Route::url('index.php?option=com_members&id=' . $contributor->user_id), '/');
			}

			$authors[] = $author;
		}

		if (count($authors))
		{
			$data['author'] = $authors;
		}
		
		$data['publisher'] = array(
			'@type' => 'Organization',
			'url' => Request::root(),
			'name' => Config::get('sitename')
		);

		if ($desc = Config::get('MetaDesc'))
		{
			$data['publisher']['description'] = $desc;
		}
		
		$data['version'] = $publication->version->get('version_number');
		
		Document::addScriptDeclaration(json_encode($data, JSON_UNESCAPED_SLASHES), 'application/ld+json');

		/*
		Example

		{
			"@context":"http://schema.org/",
			"@type":"Dataset",
			"name":"NCDC Storm Events Database",
			"description":"Storm Data is provided by the National Weather Service (NWS) and contain statistics on...",
			"url":"https://catalog.data.gov/dataset/ncdc-storm-events-database",
			"sameAs":"https://gis.ncdc.noaa.gov/geoportal/catalog/search/resource/details.page?id=gov.noaa.ncdc:C00510",
			"keywords":[
				 "ATMOSPHERE > ATMOSPHERIC PHENOMENA > CYCLONES",
				 "ATMOSPHERE > ATMOSPHERIC PHENOMENA > DROUGHT",
				 "ATMOSPHERE > ATMOSPHERIC PHENOMENA > FOG",
				 "ATMOSPHERE > ATMOSPHERIC PHENOMENA > FREEZE"
			],
			"creator":{
				 "@type":"Organization",
				 "url": "https://www.ncei.noaa.gov/",
				 "name":"OC/NOAA/NESDIS/NCEI > National Centers for Environmental Information, NESDIS, NOAA, U.S. Department of Commerce",
				 "contactPoint":{
						"@type":"ContactPoint",
						"contactType": "customer service",
						"telephone":"+1-828-271-4800",
						"email":"ncei.orders@noaa.gov"
				 }
			},
			"includedInDataCatalog":{
				 "@type":"DataCatalog",
				 "name":"data.gov"
			},
			"distribution":[
				 {
						"@type":"DataDownload",
						"encodingFormat":"CSV",
						"contentUrl":"http://www.ncdc.noaa.gov/stormevents/ftp.jsp"
				 },
				 {
						"@type":"DataDownload",
						"encodingFormat":"XML",
						"contentUrl":"http://gis.ncdc.noaa.gov/all-records/catalog/search/resource/details.page?id=gov.noaa.ncdc:C00510"
				 }
			],
			"temporalCoverage":"1950-01-01/2013-12-18",
			"spatialCoverage":{
				 "@type":"Place",
				 "geo":{
						"@type":"GeoShape",
						"box":"18.0 -65.0 72.0 172.0"
				 }
			}
		}

		*/
	}
}
