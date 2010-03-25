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

class CitationsDownloadEndnote extends CitationsDownloadAbstract
{
	protected $_mime = 'application/x-endnote-refer';
	protected $_extension = 'enw';
	
	public function format($row)
	{
		$doc = '';
		switch ($row->type) 
		{
			case 'article':
				$doc .= "%0 Journal Article\r\n";
				if ($row->journal) $doc .= "%J " . trim(stripslashes($row->journal)) . "\r\n";
				break; // journal
			case 'conference':
				$doc .= "%0 Conference Paper\r\n";
				if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
				break;
			case 'proceedings':
			case 'inproceedings':
				$doc .= "%0 Conference Proceedings\r\n";
				if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
				break; // conference proceedings 
			case 'techreport':
				$doc .= "%0 Tech Report\r\n";
				break; // report
			case 'book':
				$doc .= "%0 Book\r\n";
				break; // book
			case 'inbook':
				$doc .= "%0 Book Excerpt\r\n";
				if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
				break; // book section
			case 'mastersthesis':
				$doc .= "%0 Masters Thesis\r\n";
				if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
				break;
			case 'phdthesis':
				$doc .= "%0 PhD Thesis\r\n";
				if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
				break; // thesis
			case 'patent':
				$doc .= "%0 Patent\r\n";
				if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
				break; // patent
			case 'xarchive':
			case 'magazine':
			case 'patent appl':
			case 'chapter':
			case 'notes':
			case 'letter':
			case 'manuscript':
			case 'booklet':
			case 'manual':
			case 'misc':
			case 'unpublished':
			default:
				$doc .= "%0 Generic\r\n";
				if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
				if ($row->journal) $doc .= "%B " . trim(stripslashes($row->journal)) . "\r\n";
				break; // generic
		} 
		$doc .= "%D " . trim($row->year) . "\r\n";
		$doc .= "%T " . trim(stripslashes($row->title)) . "\r\n";

		$author_array = explode(";", stripslashes($row->author));
		foreach ($author_array as $auth) 
		{
			$auth = preg_replace( '/{{(.*?)}}/s', '', $auth );
			if (!strstr($auth,',')) {
				$bits = explode(' ',$auth);
				$n = array_pop($bits).', ';
				$bits = array_map('trim',$bits);
				$auth = $n.trim(implode(' ',$bits));
			}
			$doc .= "%A " . trim($auth) . "\r\n";
		} 

		if ($row->address) $doc .= "%C " . trim(stripslashes($row->address)) . "\r\n";
		if ($row->editor) {
			$author_array = explode(";", stripslashes($row->editor));
			foreach ($author_array as $auth) 
			{
				$doc .= "%E " . trim($auth) . "\r\n";
			} 
		}
		if ($row->publisher) $doc .= "%I " . trim(stripslashes($row->publisher)) . "\r\n";
		if ($row->number)    $doc .= "%N " . trim($row->number) . "\r\n";
		if ($row->pages)     $doc .= "%P " . trim($row->pages) . "\r\n";
		if ($row->url)       $doc .= "%U " . trim($row->url) . "\r\n";
		if ($row->volume)    $doc .= "%V " . trim($row->volume) . "\r\n";
		if ($row->note)      $doc .= "%Z " . trim($row->note) . "\r\n";
		if ($row->edition)   $doc .= "%7 " . trim($row->edition) . "\r\n";
		if ($row->month)     $doc .= "%8 " . trim($row->month) . "\r\n";
		if ($row->isbn)      $doc .= "%@ " . trim($row->isbn) . "\r\n";
		if ($row->doi)       $doc .= "%1 " . trim($row->doi) . "\r\n";

		$doc .= "\r\n";

		return $doc;
	}
}
