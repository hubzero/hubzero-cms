<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
use Components\Citations\Models\Citation;

/**
 * Citations plugin class for bibtex
 */
class plgCitationEndnote extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return file type
	 *
	 * @return  string HTML
	 */
	public function onImportAcceptedFiles()
	{
		return '.enw <small>(' . Lang::txt('PLG_CITATION_ENDNOTE_FILE') . ')</small>';
	}

	/**
	 * Import data from a file
	 *
	 * @param   array    $file
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  array
	 */
	public function onImport($file, $scope = null, $scope_id = null)
	{
		// Endnote format
		$active = 'enw';

		// Get the file extension
		$extension = $file->getClientOriginalExtension();

		// Only process more in this file if it matches endnote format
		if ($active != $extension)
		{
			return;
		}

		// Get the file contents
		$raw_citations = file($file->getPathname());

		// Process the uploaded citation data
		return $this->onImportProcessEndnote($raw_citations);
	}

	/**
	 * SProcess EndNote on import
	 *
	 * @param   array  $raw_citations_text
	 * @return  array
	 */
	protected function onImportProcessEndnote($raw_citations_text)
	{
		// Make sure we have some citation data to process
		if (empty($raw_citations_text))
		{
			return;
		}

		$raw_citation = array();
		$raw_citations = array();

		foreach ($raw_citations_text as $k => $line)
		{
			// Get this lines content
			$line = trim($line);

			// Check to see if we can get the next lines content
			if (isset($raw_citations_text[$k + 1]))
			{
				$nextline = trim($raw_citations_text[$k + 1]);
			}

			// If we have two line breaks in a row that means next citation
			if ($line == '' && $nextline == '')
			{
				$raw_citations[] = $raw_citation;
				$raw_citation = array();
				continue;
			}
			$raw_citation[] = $line;
		}

		// Append each citation as a single citation
		$raw_citations[] = $raw_citation;

		// Remove empty citations
		$raw_citations = array_values(array_filter($raw_citations));

		foreach ($raw_citations as $k => $rc)
		{
			$raw_citations[$k] = null;
			foreach ($rc as $r => $line)
			{
				$raw_citations[$k] .= $line . "\r\n";
			}
		}

		// Var to hold citation data
		$citations = array();

		// Loop through each citations raw data
		for ($i=0, $n=count($raw_citations); $i<$n; $i++)
		{
			// Split citation data match % sign followed by char
			// $citation_data = preg_split('/%.\s{1}/', $raw_citations[$i], null, PREG_SPLIT_OFFSET_CAPTURE);
			$citation_data = preg_split('/%.{1}/', $raw_citations[$i], null, PREG_SPLIT_OFFSET_CAPTURE);

			// Array to hold each citation
			$citation = array();

			// Build array of citation data
			foreach ($citation_data as $cd)
			{
				if (!empty($cd[0]))
				{
					// $key = substr($raw_citations[$i], ($cd[1]-3), 2);
					$key = substr($raw_citations[$i], ($cd[1]-2), 2);
					if (array_key_exists($key, $citation))
					{
						switch ($key)
						{
							case "%A":
								$citation[$key] .= "; " . htmlspecialchars(trim($cd[0]));
								break;
							case "%E":
								$citation[$key] .= "; " . htmlspecialchars(trim($cd[0]));
								break;
							case "%Z":
								$citation[$key] .= "\n" . htmlspecialchars(trim($cd[0]));
								break;
						}
					}
					else
					{
						if ($key == "%K")
						{
							$keywords = str_replace(",", " ", $cd[0]);
							$keywords = str_replace("\r\n", "\n", $keywords);
							$keywords = preg_replace('/[\r|\n|\r\n]/', ",\n", $keywords);
							$citation[$key] = $keywords;
						}
						else
						{
							$citation[$key] = htmlspecialchars(trim($cd[0]));
						}
					}
				}
			}

			$citations[] = $citation;
		}

		// Get the citation objects vars
		$citation_vars = $this->getCitationVars();

		// Get the endnote tags
		$endnote_tags = $this->getEndnoteTags();

		// Array to hold final citations
		$final_citations = array();

		// Loop through the split up citations
		foreach ($citations as $citation)
		{
			$cite = array();
			foreach ($endnote_tags as $tag => $keys)
			{
				// Loop through each key, we might have more than one key that we 
				// want as a var (ie. %K & %< to be tags)
				foreach ($keys as $key)
				{
					// Make sure to remove unwanted space
					$key = trim($key);

					// make sure the var exists in our citation
					if (array_key_exists($key, $citation))
					{
						// Trim the value
						$value = trim(trim($citation[$key], ':;,'));

						// Append the data if we have already set that variable
						if (isset($cite[$tag]))
						{
							$cite[$tag] .= "\n" . $value;
						}
						else
						{
							$cite[$tag] = $value;
						}
					}
				}
			}
			// Make sure all tags are separated by comma
			if (isset($cite['tags']))
			{
				$cite['tags'] = str_replace("\n", ',', $cite['tags']);
				$cite['tags'] = str_replace(',,', ',', $cite['tags']);
			}

			$final_citations[] = $cite;
		}

		// Check for duplicates
		for ($i = 0; $i < count($final_citations); $i++)
		{
			$duplicate = $this->checkDuplicateCitation($final_citations[$i]);

			if ($duplicate)
			{
				$final_citations[$i]['duplicate'] = $duplicate;
				$final['attention'][] = $final_citations[$i];
			}
			else
			{
				$final_citations[$i]['duplicate'] = 0;
				$final['no_attention'][] = $final_citations[$i];
			}
		}

		return $final;
	}

	/**
	 * Get citation fields as an array
	 *
	 * @return  array
	 */
	protected function getCitationVars()
	{
		// Get all the vars that a citation can have
		$obj = Citation::blank();
		$tableName = $obj->getTableName();
		$keys = $obj->getStructure()->getTableColumns($tableName);

		// Return keys with keys reset
		return array_keys($keys);
	}

	/**
	 * Get EndNote tags
	 *
	 * @return  array
	 */
	protected function getEndnoteTags()
	{
		$tags = array(
			'author'           => array('%A'),
			'booktitle'        => array('%B'),
			'address'          => array('%C'),
			'year'             => array('%D'),
			'editor'           => array('%E'),
			'label'            => array('%F'),
			'language'         => array('%G'),
			'publisher'        => array('%I'),
			'journal'          => array('%J'),
			'keywords'         => array('%K'),
			'call_number'      => array('%L'),
			'accession_number' => array('%M'),
			'number'           => array('%N'),
			'pages'            => array('%P'),
			'doi'              => array('%R'),
			'title'            => array('%T'),
			'url'              => array('%U'),
			'volume'           => array('%V'),
			'abstract'         => array('%X'),
			'notes'            => array('%Z'),
			'type'             => array('%0'),
			'edition'          => array('%7'),
			'month'            => array('%8'),
			'isbn'             => array('%@'),
			'short_title'      => array('%!'),
			'author_address'   => array('%+'),
			'research_notes'   => array('%<')
		);

		// Get any custom tags that we want to use
		$custom_tags = $this->params->get('custom_tags');
		$custom_tags = explode("\n", $custom_tags);

		// Loop through each custom tag in the parameter and add it to the tag list
		foreach ($custom_tags as $ct)
		{
			if ($ct)
			{
				$parts = explode('-', $ct);
				if (in_array($parts[0], array_keys($tags)))
				{
					$tags[$parts[0]][] = $parts[1];
				}
				else
				{
					$tags[$parts[0]] = array($parts[1]);
				}
			}
		}

		// Return endnote tags
		return $tags;
	}

	/**
	 * Check if a citation is a duplicate
	 *
	 * @param   array    $citation
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  integer
	 */
	protected function checkDuplicateCitation($citation, $scope = null, $scope_id = null)
	{
		// Vars
		$title = '';
		$doi = '';
		$isbn = '';
		$match = 0;
		$title_does_match = false;

		// Default percentage to match title
		$default_title_match = 90;

		// Get the % amount that titles should be alike to be considered a duplicate
		$title_match = $this->params->get('title_match_percent', $default_title_match);

		// Force title match percent to be integer and remove any unnecessary % signs
		$title_match = (int) str_replace("%", '', $title_match);

		// Make sure 0 is not the %
		$title_match = ($title_match == 0) ? $default_title_match : $title_match;
		$existingCitations = Citation::all();
		if (!empty($scope))
		{
			$existingCitations->whereEquals('scope', $scope);
		}
		if (!empty($scope_id))
		{
			$existingCitations->whereEquals('scope_id', $scope_id);
		}

		$matchingKeys = array('isbn', 'title', 'doi');
		$searchParams = array_intersect_key($citation, array_flip($matchingKeys));
		$searchParams = array_filter($searchParams);
		if (!empty($searchParams))
		{
			$existingCitations->filterBySearch($searchParams);
		}

		// Loop through all current citations
		foreach ($existingCitations->rows() as $r)
		{
			$id    = $r->id;
			$title = $r->title;
			$doi   = $r->doi;
			$isbn  = $r->isbn;
			$cScope = $r->scope;
			$cScope_id = $r->scope_id;

			if (!isset($scope))
			{
				// Direct matches on doi
				if (isset($citation['doi']) && $doi == $citation['doi'] && $doi != '')
				{
					$match = $id;
					break;
				}

				// Direct matches on isbn
				if (isset($citation['isbn']) && $isbn == $citation['isbn'] && $isbn != '')
				{
					$match = $id;
					break;
				}

				// Match titles based on percect param
				similar_text($title, $citation['title'], $similar);
				if ($similar >= $title_match)
				{
					$match = $id;
					break;
				}
			}
			elseif (isset($scope) && isset($scope_id))
			{
				// Matching within a scope domain
				if ($cScope == $scope && $cScope_id == $scope_id)
				{
					// Direct matches on doi
					if (isset($citation['doi']) && $doi == $citation['doi'] && $doi != '')
					 {
						 $match = $id;
						 break;
					 }

					 // Direct matches on isbn
					 if (isset($citation['isbn']) && $isbn == $citation['isbn'] && $isbn != '')
					 {
						 $match = $id;
						 break;
					 }

					 // Match titles based on percect param
					 similar_text($title, $citation['title'], $similar);
					 if ($similar >= $title_match)
					 {
						 $match = $id;
						 break;
					 }
				}
			}
		} // End foreach result as r
		return $match;
	}

	/**
	 * Encode entities in a string
	 *
	 * @param   string  $string
	 * @return  string
	 */
	protected function _cleanText($string)
	{
		$translations = get_html_translation_table(HTML_ENTITIES);
		$encoded = strtr($string, $translations);
		return $encoded;
	}
}
