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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

/**
 * Citations plugin class for bibtex
 */
class plgCitationEndnote extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return file type
	 *
	 * @return     string HTML
	 */
	public function onImportAcceptedFiles()
	{
		return '.enw <small>(' . JText::_('PLG_CITATION_ENDNOTE_FILE') . ')</small>';
	}

	/**
	 * Short description for 'onImport'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $file Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function onImport($file)
	{
		//endnote format
		$active = 'enw';

		//get the file extension
		$file_info = pathinfo($file['name']);

		//only process more in this file if it matches endnote format
		if ($active != $file_info['extension'])
		{
			return;
		}

		//get the file contents
		$raw_citations = file($file['tmp_name']);

		//process the uploaded citation data
		return $this->onImportProcessEndnote($raw_citations);
	}

	/**
	 * Short description for 'onImportProcessEndnote'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $raw_citations_text Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	protected function onImportProcessEndnote($raw_citations_text)
	{
		//make sure we have some citation data to process
		if (empty($raw_citations_text))
		{
			return;
		}

		$raw_citation = array();
		$raw_citations = array();

		foreach ($raw_citations_text as $k => $line)
		{
			// get this lines content
			$line = trim($line);

			// check to see if we can get the next lines content
			if(isset($raw_citations_text[$k + 1]))
			{
				$nextline = trim($raw_citations_text[$k + 1]);
			}

			// if we have two line breaks in a row that means next citation
			if ($line == '' && $nextline == '')
			{
				$raw_citations[] = $raw_citation;
				$raw_citation = array();
				continue;
			}
			$raw_citation[] = $line;
		}

		// append each citation as a single citation
		$raw_citations[] = $raw_citation;

		// remove empty citations
		$raw_citations = array_values(array_filter($raw_citations));

		foreach ($raw_citations as $k => $rc)
		{
			foreach ($rc as $r => $line)
			{
				$raw_citations[$k] .= $line . "\r\n";
			}
		}

		// var to hold citation data
		$citations = array();

		// loop through each citations raw data
		for ($i=0, $n=count($raw_citations); $i<$n; $i++)
		{
			//split citation data match % sign followed by char
			//$citation_data = preg_split('/%.\s{1}/', $raw_citations[$i], NULL, PREG_SPLIT_OFFSET_CAPTURE);
			$citation_data = preg_split('/%.{1}/', $raw_citations[$i], NULL, PREG_SPLIT_OFFSET_CAPTURE);

			//array to hold each citation
			$citation = array();

			//build array of citation data
			foreach ($citation_data as $cd)
			{
				if (!empty($cd[0]))
				{
					//$key = substr($raw_citations[$i], ($cd[1]-3), 2);
					$key = substr($raw_citations[$i], ($cd[1]-2), 2);
					if (array_key_exists($key, $citation))
					{
						switch($key)
						{
							case "%A": $citation[$key] .= "; " . htmlspecialchars(trim($cd[0])); break;
							case "%E": $citation[$key] .= "; " . htmlspecialchars(trim($cd[0])); break;
							case "%Z": $citation[$key] .= "\n" . htmlspecialchars(trim($cd[0])); break;
						}
					}
					else
					{
						if($key == "%K")
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

		//get the citation objects vars
		$citation_vars = $this->getCitationVars();

		//get the endnote tags
		$endnote_tags = $this->getEndnoteTags();

		//array to hold final citations
		$final_citations = array();

		//loop through the split up citations
		foreach ($citations as $citation)
		{
			$cite = array();
			foreach($endnote_tags as $tag => $keys)
			{
				//loop through each key, we might have more than one key that we want as a var (ie. %K & %< to be tags)
				foreach($keys as $key)
				{
					// make sure to remove unwanted space
					$key = trim($key);

					//make sure the var exists in our citation
					if(array_key_exists($key, $citation))
					{
						// trim the value
						$value = trim(trim($citation[$key],':;,'));

						//append the data if we have already set that variable
						if(isset($cite[$tag]))
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
			// make sure all tags are separated by comma
			if (isset($cite['tags']))
			{
				$cite['tags'] = str_replace("\n", ',', $cite['tags']);
				$cite['tags'] = str_replace(',,', ',', $cite['tags']);
			}

			$final_citations[] = $cite;
		}

		//check for duplicates
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
	 * Short description for 'getCitationVars'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	protected function getCitationVars()
	{
		//get all the vars that a citation can have
		$keys = array_keys(get_class_vars('CitationsCitation'));

		//remove any private vars
		foreach ($keys as $k => $v)
		{
			if (substr($v,0,1) == '_')
			{
				unset($keys[$k]);
			}
		}

		//return keys with keys reset
		return array_values($keys);
	}

	/**
	 * Short description for 'getEndnoteTags'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
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

		//get any custom tags that we want to use
		$custom_tags = $this->params->get('custom_tags');
		$custom_tags = explode("\n", $custom_tags);

		//loop through each custom tag in the parameter and add it to the tag list
		foreach ($custom_tags as $ct)
		{
			if ($ct)
			{
				$parts = explode('-', $ct);
				if(in_array($parts[0], array_keys($tags)))
				{
					$tags[$parts[0]][] = $parts[1];
				}
				else
				{
					$tags[$parts[0]] = array($parts[1]);
				}
			}
		}

		//return endnote tags
		return $tags;
	}

	/**
	 * Short description for 'checkDuplicateCitation'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $citation Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	protected function checkDuplicateCitation($citation)
	{
		//vars
		$title = '';
		$doi = '';
		$isbn = '';
		$match = 0;
		$title_does_match = false;

		//default percentage to match title
		$default_title_match = 90;

		//get the % amount that titles should be alike to be considered a duplicate
		$title_match = $this->params->get('title_match_percent', $default_title_match);

		//force title match percent to be integer and remove any unnecessary % signs
		$title_match = (int) str_replace("%", '', $title_match);

		//make sure 0 is not the %
		$title_match = ($title_match == 0) ? $default_title_match : $title_match;

		//database object
		$db = JFactory::getDBO();

		//query
		$sql = "SELECT id, title, doi, isbn FROM #__citations";

		//set the query
		$db->setQuery($sql);

		//get the result
		$result = $db->loadObjectList();

		//loop through all current citations
		foreach ($result as $r)
		{
			$id    = $r->id;
			$title = $r->title;
			$doi   = $r->doi;
			$isbn  = $r->isbn;

			//match titles based on percect param
			similar_text($title, $citation['title'], $similar);
			if ($similar >= $title_match)
			{
				$title_does_match = true;
			}

			//direct matches on doi
			if (isset($citation['doi']) && ($doi == $citation['doi']) && ($doi != '' && $title_does_match))
			{
				$match = $id;
				break;
			}

			//direct matches on isbn
			if (isset($citation['isbn']) && ($isbn == $citation['isbn']) && ($isbn != '' && $title_does_match))
			{
				$match = $id;
				break;
			}

			//
			if ($title_does_match)
			{
				$match = $id;
				break;
			}
		}

		return $match;
	}

	/**
	 * Short description for '_cleanText'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function _cleanText($string)
	{
		$translations = get_html_translation_table(HTML_ENTITIES);
		$encoded = strtr($string, $translations);
		return $encoded;
	}
}
