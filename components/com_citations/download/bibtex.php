<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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
			$author = preg_replace('/\{\{(.+)\}\}/i','',$author);
			$author_arr = explode(',',$author);
			$author_arr = array_map('trim',$author_arr);
			
			$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? trim($author_arr[1]) : '';
			$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? trim($author_arr[0]) : '';
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

