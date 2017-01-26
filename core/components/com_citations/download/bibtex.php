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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Download;

use Components\Citations\Tables\Type;

include_once (__DIR__ . DS . 'downloadable.php');

/**
 * Citations download class for BibText format
 */
class Bibtex extends Downloadable
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
		$config = \Component::params('com_citations');
		$exclude = $config->get('citation_download_exclude', '');
		if (strpos($exclude, ',') !== false)
		{
			$exclude = str_replace(',', "\n", $exclude);
		}
		$exclude = array_values(array_filter(array_map('trim', explode("\n", $exclude))));

		//get fields to not include for specific citation
		$cparams = new \Hubzero\Config\Registry($row->params);
		$citation_exclude = $cparams->get('exclude', '');
		if (strpos($citation_exclude, ',') !== false)
		{
			$citation_exclude = str_replace(',', "\n", $citation_exclude);
		}
		$citation_exclude = array_values(array_filter(array_map('trim', explode("\n", $citation_exclude))));

		//merge overall exclude and specific exclude
		$exclude = array_values(array_unique(array_merge($exclude, $citation_exclude)));

		include_once (dirname(__DIR__) . DS . 'helpers' . DS . 'BibTex.php');
		$bibtex = new \Structures_BibTex();

		$addarray = array();

		//get all the citation types
		$db = \App::get('db');
		$ct = new Type($db);
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

		if (!$row->cite)
		{
			$au = new \Components\Citations\Tables\Author($db);
			$authors = $au->getRecords(array('cid' => $row->id, 'start' => 0, 'limit' => 1));

			foreach ($authors as $author)
			{
				$row->cite .= strtolower($author->surname);
			}

			$row->cite .= $row->year;
			$t = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($row->title));
			$row->cite .= (strlen($t) > 10 ? substr($t, 0, 10) : $t);
		}

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
