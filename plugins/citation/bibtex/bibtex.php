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
class plgCitationBibtex extends \Hubzero\Plugin\Plugin
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
		return '.bib <small>(' . JText::_('PLG_CITATION_BIBTEX_FILE') . ')</small>';
	}

	/**
	 * Short description for 'onImport'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $file Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onImport($file)
	{
		//file type
		$active = 'bib';

		//get the file extension
		$file_info = pathinfo($file['name']);

		//make sure we have a .bib file
		if ($active != $file_info['extension'])
		{
			return;
		}

		//include bibtex file
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'BibTex.php');

		//create bibtex object
		$bibtex = new Structures_BibTex();

		//feed bibtex lib the file
		$bibtex->loadFile($file['tmp_name']);

		//parse file
		$bibtex->parse();

		//get parsed citations
		$citations = $bibtex->data;

		//fix authors
		for ($i=0;$i<count($citations); $i++)
		{
			$auths = $citations[$i]['author'];
			foreach ($auths as $a)
			{
				$authors[] = $a['last'] . ' ' . $a['jr'] . ', ' . $a['first'];
			}

			$citations[$i]['author'] = implode('; ', $authors);
		}

		//array to hold final citataions
		$final = array();

		//check for duplicates
		for ($i = 0; $i < count($citations); $i++)
		{
			$duplicate = $this->checkDuplicateCitation($citations[$i]);

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
		$doi   = '';
		$isbn  = '';
		$match = 0;

		//default percentage to match title
		$default_title_match = 90;

		//get the % amount that titles should be alike to be considered a duplicate
		$title_match = $this->params->get('title_match_percent', $default_title_match);

		//force title match percent to be integer and remove any unnecessary % signs
		$title_match = (int) str_replace('%', '', $title_match);

		//make sure 0 is not the %
		$title_match = ($title_match == 0) ? $default_title_match : $title_match;

		//database object
		$db = JFactory::getDBO();

		//query
		$sql = "SELECT id, title, doi, isbn FROM `#__citations`";

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

			//direct matches on doi
			if ($doi == $citation['doi'] && $doi != '')
			{
				$match = $id;
				break;
			}

			//direct matches on isbn
			if ($isbn == $citation['isbn'] && $isbn != '')
			{
				$match = $id;
				break;
			}

			//match titles based on percect param
			similar_text($title, $citation['title'], $similar);
			if ($similar >= $title_match)
			{
				$match = $id;
				break;
			}
		}

		return $match;
	}
}