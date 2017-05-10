<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;

/**
 * Resource license model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Doi extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'author';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__doi_mapping';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'rid' => 'positive|nonzero'
	);

	/**
	 * Get profile for author ID
	 *
	 * @return  object
	 */
	public function resource()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Orm\\Resource', 'rid');
	}

	/**
	 * Get a record by its doi
	 *
	 * @param   string  $doi
	 * @return  object
	 */
	public static function oneByDoi($doi)
	{
		$row = self::all()
			->whereEquals('doi', $doi)
			->row();

		return $row;
	}

	/**
	 * Get a record by resource
	 *
	 * @param   string  $doi
	 * @return  object
	 */
	public static function oneByResource($rid, $revision = null, $versionid = 0)
	{
		$model = self::all()
			->whereEquals('rid', $rid);

		if ($revision)
		{
			$model->whereEquals('local_revision', $revision);
		}

		if ($versionid)
		{
			$model->whereEquals('versionid', $versionid);
		}

		return $model->order('doi_label', 'desc')->row();
	}

	/**
	 * Register a DOI
	 *
	 * @param   array   $authors   Authors of a resource
	 * @param   object  $config    Parameter
	 * @param   array   $metadata  Metadata
	 * @return  mixed   False if error, string on success
	 */
	public function register($authors, $config, $metadata = array())
	{
		if (empty($metadata))
		{
			return false;
		}

		// Get configs
		$shoulder  = trim($config->get('doi_shoulder'), '/');
		$service   = trim($config->get('doi_newservice'), '/');
		$prefix    = $config->get('doi_newprefix');
		$userpw    = $config->get('doi_userpw');
		$xmlschema = trim($config->get('doi_xmlschema', 'http://schema.datacite.org/meta/kernel-2.1/metadata.xsd'), '/');
		$handle    = '';
		$doi       = '';

		if (!$shoulder || !$service)
		{
			$this->addError('Missing DOI configuration');
			return false;
		}

		// Collect metadata
		$metadata['publisher'] = htmlspecialchars($config->get('doi_publisher', \Config::get('sitename')));
		$metadata['pubYear']   = isset($metadata['pubYear']) ? $metadata['pubYear'] : date('Y');
		$metadata['language']  = 'en';

		// Make service path
		$call  = $service . '/shoulder/' . 'doi:' . $shoulder;
		$call .= $prefix ? '/' . $prefix : '/';

		// Get config
		$live_site = rtrim(\Request::base(), '/');

		if (!$live_site || !isset($metadata['targetURL']) || !isset($metadata['title']))
		{
			$this->addError('Missing url, title or live site configuration');
			return false;
		}

		// Get first author / creator name
		if ($authors && count($authors) > 0)
		{
			$creatorName = $authors[0]->name;
		}
		else
		{
			$creatorName = User::get('name');
		}

		// Format name
		$nameParts = explode(' ', $creatorName);
		$metadata['creator']  = end($nameParts);
		$metadata['creator'] .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';

		// Start input
		$input  = "_target: " . $metadata['targetURL'] ."\n";
		$input .= "datacite.creator: " . $metadata['creator'] . "\n";
		$input .= "datacite.title: ". $metadata['title'] . "\n";
		$input .= "datacite.publisher: " . $metadata['publisher'] . "\n";
		$input .= "datacite.publicationyear: " . $metadata['pubYear'] . "\n";
		$input .= "datacite.resourcetype: Software" . "\n";
		$input .= "_profile: datacite";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $call);
		curl_setopt($ch, CURLOPT_USERPWD, $userpw);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain; charset=UTF-8', 'Content-Length: ' . strlen($input)));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);

		// returns HTTP Code for success or fail
		$success = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($success === 201)
		{
			$out = explode('/', $output);
			$handle = trim(end($out));
		}
		else
		{
			if (empty($output))
			{
				$this->addError("$success: " . curl_error($ch) . ' ' . $call);
			}
			else
			{
				$this->addError("$success: " . $output . ' ' . $call);
			}
			$handle = 0;
		}

		$handle = strtoupper($handle);
		$doi = $shoulder . '/' . $handle;
		curl_close($ch);

		// Prepare XML data
		if ($handle)
		{
			$xdoc = new \DomDocument;
			$xmlfile = $this->buildXml($authors, $metadata, $doi);

			// Load the xml document in the DOMDocument object
			$xdoc->loadXML($xmlfile);

			// Validate the XML file against the schema
			if ($xdoc->schemaValidate($xmlschema))
			{
				// EZID parses text received based on new lines.
				$input  = "_target: " . $metadata['targetURL'] ."\n";
				$input .= "datacite.creator: " . $metadata['creator'] . "\n";
				$input .= "datacite.title: ". $metadata['title'] . "\n";
				$input .= "datacite.publisher: " . $metadata['publisher'] . "\n";
				$input .= "datacite.publicationyear: " . $metadata['pubYear'] . "\n";
				$input .= "datacite.resourcetype: Software" . "\n";
				$input .= "_profile: datacite" . "\n";

				// colons(:),percent signs(%),line terminators(\n),carriage returns(\r) are percent encoded for given input string
				$input .= 'datacite: ' . strtr($xmlfile, array(":" => "%3A", "%" => "%25", "\n" => "%0A", "\r" => "%0D")) . "\n";

				// Make service path
				$call = $service . '/id/doi:' . $doi;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $call);
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
				$this->addError('XML is invaild. DOI has been created but unable to upload XML as it is invalid. Please modify the created DOI with a valid XML.');
			}
		}

		return $handle ? $handle : null;
	}

	/**
	 * Generate the XML for creating a DOI
	 *
	 * @param   array   $authors   Authors of a resource
	 * @param   array   $metadata  Metadata to build XML from
	 * @param   string  $doi       DOI
	 * @return  string  XML
	 */
	private function buildXml($authors, $metadata, $doi = 0)
	{
		$datePublished = isset($metadata['datePublished'])
					? $metadata['datePublished'] : date('Y-m-d');
		$dateAccepted  = date('Y-m-d');

		$xmlfile  = '<?xml version="1.0" encoding="UTF-8"?><resource xmlns="http://datacite.org/schema/kernel-2.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://datacite.org/schema/kernel-2.1 http://schema.datacite.org/meta/kernel-2.1/metadata.xsd">';
		$xmlfile .= '<identifier identifierType="DOI">' . $doi . '</identifier>';
		$xmlfile .= '<creators>';
		if ($authors && count($authors) > 0)
		{
			foreach ($authors as $author)
			{
				$nameParts = explode(' ', $author->name);
				$name  = end($nameParts);
				$name .= count($nameParts) > 1 ? ', ' . $nameParts[0] : '';
				$xmlfile .= '<creator>';
				$xmlfile .= '	<creatorName>' . $name . '</creatorName>';
				$xmlfile .= '</creator>';
			}
		}
		else
		{
			$xmlfile .= '<creator>';
			$xmlfile .= '	<creatorName>' . $metadata['creator'] . '</creatorName>';
			$xmlfile .= '</creator>';
		}
		$xmlfile .= '</creators>';
		$xmlfile .= '<titles>';
		$xmlfile .= '	<title>' . $metadata['title'] . '</title>';
		$xmlfile .= '</titles>';
		$xmlfile .= '<publisher>' . $metadata['publisher'] . '</publisher>';
		$xmlfile .= '<publicationYear>' . $metadata['pubYear'] . '</publicationYear>';
		$xmlfile .= '<dates>';
		$xmlfile .= '	<date dateType="Valid">' . $datePublished . '</date>';
		$xmlfile .= '	<date dateType="Accepted">' . $dateAccepted . '</date>';
		$xmlfile .= '</dates>';
		$xmlfile .= '<language>' . $metadata['language'] . '</language>';

		$xmlfile .= '<resourceType resourceTypeGeneral="Software">Simulation Tool</resourceType>';
		if (isset($metadata['version']) && $metadata['version'] != '')
		{
			$xmlfile .= '<version>' . $metadata['version'] . '</version>';
		}
		if (isset($metadata['abstract']) && $metadata['abstract'] != '')
		{
			$xmlfile .= '<descriptions>';
			$xmlfile .= '	<description descriptionType="Other">' . $metadata['abstract'] . '</description>';
			$xmlfile .= '</descriptions>';
		}

		$xmlfile .= '</resource>';
		return $xmlfile;
	}
}
