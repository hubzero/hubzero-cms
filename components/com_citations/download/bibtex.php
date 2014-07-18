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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'download' . DS . 'abstract.php');

/**
 * Citations download class for BibText format
 */
class CitationsDownloadBibtex extends CitationsDownloadAbstract
{
	/**
	 * Mime type
	 *
	 * @var string
	 */
	protected $_mime = 'application/x-bibtex';

	/**
	 * File extension
	 *
	 * @var string
	 */
	protected $_extension = 'bib';

	/**
	 * Format the file
	 *
	 * @param      object $row Record to format
	 * @return     string
	 */
	public function format($row)
	{
		// get fields to not include for all citations
		$config = JComponentHelper::getParams('com_citations');
		$exclude = $config->get('citation_download_exclude', '');
		if (strpos($exclude, ',') !== false)
		{
			$exclude = str_replace(',', "\n", $exclude);
		}
		$exclude = array_values(array_filter(array_map('trim', explode("\n", $exclude))));

		//get fields to not include for specific citation
		$cparams = new JRegistry($row->params);
		$citation_exclude = $cparams->get('exclude', '');
		if (strpos($citation_exclude, ',') !== false)
		{
			$citation_exclude = str_replace(',', "\n", $citation_exclude);
		}
		$citation_exclude = array_values(array_filter(array_map('trim', explode("\n", $citation_exclude))));

		//merge overall exclude and specific exclude
		$exclude = array_values(array_unique(array_merge($exclude, $citation_exclude)));

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'BibTex.php');
		$bibtex = new Structures_BibTex();

		$addarray = array();

		//get all the citation types
		$db = JFactory::getDBO();
		$ct = new CitationsType($db);
		$types = $ct->getType();

		//find the right title
		$type = '';
		foreach ($types as $t)
		{
			if ($t['id'] == $row->type)
			{
				$type = $t['type'];
			}
		}
		$type = ($type != '') ? $type : 'Generic';

		$addarray['type']    = $type;
		$addarray['cite']    = $row->cite;
		$addarray['title']   = $row->title;
		$addarray['address'] = $row->address;
		$auths = explode(';', $row->author);
		for ($i=0, $n=count($auths); $i < $n; $i++)
		{
			$author = trim($auths[$i]);
			$author_arr = explode(',', $author);
			$author_arr = array_map('trim', $author_arr);

			$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? $author_arr[1] : '';
			$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? $author_arr[0] : '';

			$addarray['author'][$i]['first'] = preg_replace('/\{\{\d+\}\}/', '', $addarray['author'][$i]['first']);
			$addarray['author'][$i]['last']  = preg_replace('/\{\{\d+\}\}/', '', $addarray['author'][$i]['last']);
		}

		$addarray['booktitle']    = $row->booktitle;
		$addarray['chapter']      = $row->chapter;
		$addarray['edition']      = $row->edition;
		$addarray['editor']       = $row->editor;
		$addarray['eprint']       = $row->eprint;
		$addarray['howpublished'] = $row->howpublished;
		$addarray['institution']  = $row->institution;
		$addarray['journal']      = $row->journal;
		$addarray['key']          = $row->key;
		$addarray['location']     = $row->location;
		$addarray['month']        = ($row->month != 0 || $row->month != '0') ? $row->month : '';
		$addarray['note']         = $row->note;
		$addarray['number']       = $row->number;
		$addarray['organization'] = $row->organization;
		$addarray['pages']        = ($row->pages != 0 || $row->pages != '0') ? $row->pages : '';
		$addarray['publisher']    = $row->publisher;
		$addarray['series']       = $row->series;
		$addarray['school']       = $row->school;
		$addarray['url']          = $row->url;
		$addarray['volume']       = $row->volume;
		$addarray['year']         = $row->year;
		if ($row->journal != '')
		{
			$addarray['issn']     = $row->isbn;
		}
		else
		{
			$addarray['isbn']     = $row->isbn;
		}
		$addarray['doi']          = $row->doi;

		$addarray['language']         = $row->language;
		$addarray['accession_number'] = $row->accession_number;
		$addarray['short_title']      = html_entity_decode($row->short_title);
		$addarray['author_address']   = $row->author_address;
		$addarray['keywords']         = str_replace("\r\n", ', ', $row->keywords);
		$addarray['abstract']         = $row->abstract;
		$addarray['call_number']      = $row->call_number;
		$addarray['label']            = $row->label;
		$addarray['research_notes']   = $row->research_notes;

		foreach ($addarray as $k => $v)
		{
			if (in_array($k, $exclude))
			{
				unset($addarray[$k]);
			}
		}

		$bibtex->addEntry($addarray);

		//$file = 'download_'.$id.'.bib';
		//$mime = 'application/x-bibtex';
		$doc = $bibtex->bibTex();

		return $doc;
	}
}

