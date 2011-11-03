<?php
/**
 * @package     hubzero-cms
 * @author      Christopher Smoak <csmoak@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
jimport('joomla.filesystem.file');

//-----------

JPlugin::loadLanguage( 'plg_citation_endnote' );

//-----------

class plgCitationEndnote extends JPlugin
{
	public function plgCitationEndnote(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'citation', 'endnote' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----
	
	public function onImportAcceptedFiles()
	{
		return ".enw <small>(EndNote File)</small>";
	}

	//-----
	
	public function onImport( $file )
	{
		//endnote format
		$active = "enw";

		//get the file extension
		$file_info = pathinfo($file['name']);

		//only process more in this file if it matches endnote format
		if($active != $file_info['extension']) {
			return;
		}

		//get the file contents
		//$raw_citations = file_get_contents($file['tmp_name']);
		$raw_citations = file($file['tmp_name']);

		//process the uploaded citation data
		return $this->onImportProcessEndnote( $raw_citations );
	}

	//-----
	
	protected function onImportProcessEndnote( $raw_citations_text )
	{
		//make sure we have some citation data to process
		if(empty($raw_citations_text)) {
			return;
		}

		//split multiple citations and remove empties and reset keys
		//$raw_citations = explode("\r\n\r\n", $raw_citations_text);
		//$raw_citations = array_values(array_filter($raw_citations));
		
		$raw_citation = array();
		$raw_citations = array();

		foreach($raw_citations_text as $line) 
		{
			$line = $this->_cleanText(trim($line));
			if($line == '') 
			{
				$raw_citations[] = $raw_citation;
				$raw_citation = array();
				continue;
			}
			$raw_citation[] = $line;
		}
		$raw_citations[] = $raw_citation;

		//remove empties
		$raw_citations = array_values(array_filter($raw_citations));

		foreach($raw_citations as $k => $rc) {
			foreach($rc as $r => $line) {
				//echo $line .'<br>';
				$raw_citations[$k] .= $line . "\r\n";
			}
		}

		//vars used 
		$citations = array();

		//loop through each citations raw data
		for($i=0; $i<count($raw_citations); $i++) {
			//split citation data match % sign followed by char
			$citation_data = preg_split('/%.\s{1}/', $raw_citations[$i], NULL, PREG_SPLIT_OFFSET_CAPTURE);

			//array to hold each citation
			$citation = array();

			//build array of citation data
			foreach($citation_data as $cd) {
				if(!empty($cd[0])) {
					$key = substr($raw_citations[$i], ($cd[1]-3), 2);
					if(array_key_exists( $key, $citation )) {
						switch( $key )
						{
							case "%A":	$citation[$key] .= "; " . htmlentities(trim($cd[0]));	break;
							case "%E":	$citation[$key] .= "; " . htmlentities(trim($cd[0]));	break;
							case "%Z":	$citation[$key] .= "\n" . htmlentities(trim($cd[0]));	break;
						}
					} else {
						$citation[$key] = htmlentities(trim($cd[0]));
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
		foreach($citations as $citation) {
			$cite = array();
			foreach($endnote_tags as $k => $v) {
				//make sure the var exists in our citation
				if(array_key_exists($v, $citation)) {
					$cite[$k] = $citation[$v];
				}

				//if we have fields in our imported citation data that dont exist in the database report errors
				if(!in_array($k, $citation_vars) && array_key_exists($v, $citation)) { 
					$cite['errors'][] = "Failed to add '{$k}' to this citation.";
				}
			}
			$final_citations[] = $cite;
		}

		//check for duplicates
		for($i = 0; $i < count($final_citations); $i++) {
			$duplicate = $this->checkDuplicateCitation( $final_citations[$i] );

			if($duplicate) {
				$final_citations[$i]['duplicate'] = $duplicate;
				$final['attention'][] = $final_citations[$i];
			} else {
				$final_citations[$i]['duplicate'] = 0;
				$final['no_attention'][] = $final_citations[$i];
			}
		}

		return $final;
	}

	//-----
	
	protected function getCitationVars()
	{
		//get all the vars that a citation can have
		$keys = array_keys(get_class_vars("CitationsCitation"));

		//remove any private vars
		foreach($keys as $k => $v) {
			if(substr($v,0,1) == "_") {
				unset($keys[$k]);
			}
		}

		//return keys with keys reset
		return array_values($keys);
	}

	//------
	
	protected function getEndnoteTags()
	{
		$tags = array(
			'author' => '%A',
			'editor' => '%E',
			'type'	=> '%0',
			'journal' => '%J',
			'booktitle' => '%B',
			'title' => '%T',
			'address' => '%C',
			'publisher' => '%I',
			'number' => '%N',
			'pages' => '%P',
			'url' => '%U',
			'volume' => '%V',
			'notes' => '%Z',
			'edition' => '%7',
			'month' => '%8',
			'year' => '%D',
			'isbn' => '%@',
			'doi' => '%1',

			'abstract' => '%X',
			'keywords' => '%K',
			'label' => '%F',
			'language' => '%G',
			'call_number' => '%L',
			'accession_number' => '%M',
			'short_title' => '%!',
			'research_notes' => '%<',
			'author_address' => '%+'
		);

		//get any custom tags that we want to use
		$custom_tags = $this->_params->get("custom_tags");
		$custom_tags = explode( "\n", $custom_tags);

		//loop through each custom tag in the parameter and add it to the tag list
		foreach($custom_tags as $ct) {
			if($ct) {
				$parts = explode("-",$ct);
				$tags[$parts[0]] = $parts[1]; 
			}
		}

		//return endnote tags
		return $tags;
	}

	//-----
	
	protected function checkDuplicateCitation( $citation )
	{
		//vars
		$title = ""; $doi = ""; $isbn = ""; $match = 0; $title_does_match = false;

		//default percentage to match title
		$default_title_match = 90;

		//get the % amount that titles should be alike to be considered a duplicate
		$title_match = $this->params->get("title_match_percent", $default_title_match);

		//force title match percent to be integer and remove any unnecessary % signs
		$title_match = (int)str_replace("%", "", $title_match);

		//make sure 0 is not the %
		$title_match = ($title_match == 0) ? $default_title_match : $title_match;

		//database object
		$db =& JFactory::getDBO();

		//table we a going to query
		$tbl = "#__citations";

		//query
		$sql = "SELECT id, title, doi, isbn FROM {$tbl}";

		//set the query
		$db->setQuery( $sql );

		//get the result
		$result = $db->loadObjectList();

		//loop through all current citations
		foreach($result as $r) {
			$id 	= $r->id;
			$title 	= $r->title;
			$doi 	= $r->doi;
			$isbn	= $r->isbn;

			//match titles based on percect param
			similar_text( $title, $citation['title'], $similar);
			if($similar >= $title_match) {
				$title_does_match = true;
			}

			//direct matches on doi
			if(isset($citation['doi']) && ($doi == $citation['doi']) && ($doi != "" && $title_does_match)) {
				$match = $id;
				break;
			}

			//direct matches on isbn
			if(isset($citation['isbn']) && ($isbn == $citation['isbn']) && ($isbn != "" && $title_does_match)) {
				$match = $id;
				break;
			}

			//
			if($title_does_match) {
				$match = $id;
				break;
			}
		}

		return $match;
	}

	//-----
	
	protected function _cleanText( $string )
	{
		$translations = get_html_translation_table(HTML_ENTITIES);
		$encoded = strtr( $string, $translations );
		return $encoded;
	} 
	
	//-----
}
