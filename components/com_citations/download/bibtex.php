<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'download'.DS.'abstract.php');

class CitationsDownloadBibtex extends CitationsDownloadAbstract
{
	protected $_mime = 'application/x-bibtex';
	protected $_extension = 'bib';
	
	public function format($row)
	{
		include_once(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'BibTex.php');

		$bibtex = new Structures_BibTex();
		
		$addarray = array();
		$addarray['type']    = $row->type;
		$addarray['cite']    = $row->cite;
		$addarray['title']   = $row->title;
		$addarray['address'] = $row->address;
		$auths = explode(';',$row->author);
		for ($i=0, $n=count( $auths ); $i < $n; $i++)
		{
			$author = trim($auths[$i]);
			$author_arr = explode(',',$author);
			$author_arr = array_map('trim',$author_arr);
			
			$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? $author_arr[1] : '';
			$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? $author_arr[0] : '';
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
		if ($row->journal != '') {
			$addarray['issn']     = $row->isbn;
		} else {
			$addarray['isbn']     = $row->isbn;
		}
		$addarray['doi']          = $row->doi;
		
		$bibtex->addEntry($addarray);

		//$file = 'download_'.$id.'.bib';
		//$mime = 'application/x-bibtex';
		$doc = $bibtex->bibTex();
		
		return $doc;
	}
}
