<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Citations\Models\Citation;

/**
 * Citations plugin class for bibtex
 */
class plgCitationBibtex extends \Hubzero\Plugin\Plugin
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
	 * @return  string  HTML
	 */
	public function onImportAcceptedFiles()
	{
		return '.bib <small>(' . Lang::txt('PLG_CITATION_BIBTEX_FILE') . ')</small>';
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
		// File type
		$active = 'bib';

		// Get the file extension
		$extension = $file->getClientOriginalExtension();

		// Make sure we have a .bib file
		if ($active != $extension)
		{
			return;
		}

		// Include bibtex file
		include_once Component::path('com_citations') . DS . 'helpers' . DS . 'BibTex.php';

		// Create bibtex object
		$bibtex = new Structures_BibTex();

		// Feed bibtex lib the file
		$bibtex->loadFile($file->getPathname());

		// Parse file
		$bibtex->parse();

		// Get parsed citations
		$citations = $bibtex->data;

		// Fix authors
		for ($i=0; $i<count($citations); $i++)
		{
			$authors = array();
			$auths   = isset($citations[$i]['author']) ? $citations[$i]['author'] : '';
			if ($auths != '')
			{
				foreach ($auths as $a)
				{
					if (isset($a['jr']) && $a['jr'] != '')
					{
						$authors[] = $a['last'] . ' ' . $a['jr'] . ', ' . $a['first'];
					}
					else
					{
						$authors[] = $a['last'] . ', ' . $a['first'];
					}
				}
				$citations[$i]['author'] = implode('; ', $authors);
			} // End if 
		}

		// Array to hold final citataions
		$final = array();

		// Check for duplicates
		for ($i = 0; $i < count($citations); $i++)
		{
			$duplicate = $this->checkDuplicateCitation($citations[$i], $scope, $scope_id);

			if ($duplicate)
			{
				$citations[$i]['duplicate'] = $duplicate;
				$final['attention'][] = $citations[$i];
			}
			else
			{
				$citations[$i]['duplicate'] = 0;
				$final['no_attention'][] = $citations[$i];
			}
		}

		return $final;
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
		$doi   = '';
		$isbn  = '';
		$match = 0;

		// Default percentage to match title
		$default_title_match = 90;

		// Get the % amount that titles should be alike to be considered a duplicate
		$title_match = $this->params->get('title_match_percent', $default_title_match);

		// Force title match percent to be integer and remove any unnecessary % signs
		$title_match = (int) str_replace('%', '', $title_match);

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
}
