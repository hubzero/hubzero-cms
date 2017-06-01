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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
	public function onImport($file, $scope = NULL, $scope_id = NULL)
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
		include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'BibTex.php');

		// Create bibtex object
		$bibtex = new Structures_BibTex();

		// Feed bibtex lib the file
		$bibtex->loadFile($file->getPathname());

		// Parse file
		$bibtex->parse();

		// Get parsed citations
		$citations = $bibtex->data;

		// Fix authors
		for ($i=0;$i<count($citations); $i++)
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
	protected function checkDuplicateCitation($citation, $scope = NULL, $scope_id = NULL)
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

		// Database object
		$db = \App::get('db');

		// Set query and get the result
		$sql = "SELECT id, title, doi, isbn, scope, scope_id FROM `#__citations`";
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		// Loop through all current citations
		foreach ($result as $r)
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
