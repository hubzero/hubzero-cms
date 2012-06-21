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
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'download'.DS.'abstract.php');

/**
 * Short description for 'CitationsDownloadEndnote'
 * 
 * Long description (if any) ...
 */
class CitationsDownloadEndnote extends CitationsDownloadAbstract
{

	/**
	 * Description for '_mime'
	 * 
	 * @var string
	 */
	protected $_mime = 'application/x-endnote-refer';

	/**
	 * Description for '_extension'
	 * 
	 * @var string
	 */
	protected $_extension = 'enw';

	/**
	 * Short description for 'format'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $row Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function format($row)
	{
		//get fields to not include for all citations
		$config = JComponentHelper::getParams("com_citations");
		$exclude = $config->get("citation_download_exclude","");
		if(strpos($exclude,",") !== false)
		{
			$exclude = str_replace(',',"\n",$exclude);
		}
		$exclude = array_values(array_filter(array_map("trim", explode("\n", $exclude))));
		
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		
		//get fields to not include for specific citation
		$cparams = new $paramsClass( $row->params );
		$citation_exclude = $cparams->get("exclude","");
		if(strpos($citation_exclude,",") !== false)
		{
			$citation_exclude = str_replace(',',"\n",$citation_exclude);
		}
		$citation_exclude = array_values(array_filter(array_map("trim", explode("\n", $citation_exclude))));
		
		//merge overall exclude and specific exclude
		$exclude = array_values(array_unique(array_merge($exclude, $citation_exclude)));
		
		//var to hold document conetnt
		$doc = '';

		//get all the citation types
		$db =& JFactory::getDBO();
		$ct = new CitationsType( $db );
		$types = $ct->getType();
		
		$type = "";
		foreach($types as $t) 
		{
			if($t['id'] == $row->type) 
			{
				$type = $t['type_title'];
			}
		}
		
		//set the type to generic if we dont have one
		$type = ($type != "") ? $type : "Generic";

		//set the type
		$doc .= "%0 {$type}" . "\r\n";

		if ($row->booktitle && !in_array("booktitle",$exclude)) 
		{
			$doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
		}
		if ($row->journal && !in_array("journal",$exclude)) 
		{
			$doc .= "%J " . trim(stripslashes($row->journal)) . "\r\n";
		}
		if($row->year && !in_array("year", $exclude))
		{
			$doc .= "%D " . trim($row->year) . "\r\n";
		}
		if($row->title && !in_array("title", $exclude))
		{
			$doc .= "%T " . trim(stripslashes($row->title)) . "\r\n";
		}
		if(!in_array("authors", $exclude))
		{
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
		}
        if ($row->address && !in_array("address", $exclude))
		{
			$doc .= "%C " . trim(stripslashes($row->address)) . "\r\n";
		}
		if ($row->editor && !in_array("editor", $exclude)) 
		{
			$author_array = explode(";", stripslashes($row->editor));
			foreach ($author_array as $auth)
			{
				$doc .= "%E " . trim($auth) . "\r\n";
			}
		}
		if ($row->publisher && !in_array("publisher",$exclude)) 
		{
			$doc .= "%I " . trim(stripslashes($row->publisher)) . "\r\n";
		}
		if ($row->number && !in_array("number",$exclude))
		{
			$doc .= "%N " . trim($row->number) . "\r\n";
		}
		if ($row->pages && !in_array("pages",$exclude))
		{
			$doc .= "%P " . trim($row->pages) . "\r\n";
		}
		if ($row->url && !in_array("url",$exclude))
		{
			$doc .= "%U " . trim($row->url) . "\r\n";
		}
		if ($row->volume && !in_array("volume",$exclude))
		{
			$doc .= "%V " . trim($row->volume) . "\r\n";
		}
		if ($row->note && !in_array("note",$exclude))
		{
			$doc .= "%Z " . trim($row->note) . "\r\n";
		}
		if ($row->edition && !in_array("edition",$exclude))
		{
			$doc .= "%7 " . trim($row->edition) . "\r\n";
		}
		if ($row->month && !in_array("month",$exclude))
		{
			$doc .= "%8 " . trim($row->month) . "\r\n";
		}
		if ($row->isbn && !in_array("isbn",$exclude))
		{
			$doc .= "%@ " . trim($row->isbn) . "\r\n";
		}
		if ($row->doi && !in_array("doi",$exclude))
		{
			$doc .= "%1 " . trim($row->doi) . "\r\n";
		}
		if($row->keywords && !in_array("keywords",$exclude))
		{
			$doc .= "%K " . trim($row->keywords) . "\r\n";
		}
		if($row->research_notes && !in_array("research_notes",$exclude))
		{
			$doc .= "%< " . trim($row->research_notes) . "\r\n";
		}
		if($row->abstract && !in_array("abstract",$exclude))
		{
			$doc .= "%X " . trim($row->abstract) . "\r\n";
		}
		if($row->label && !in_array("label",$exclude))
		{
			$doc .= "%F " . trim($row->label) . "\r\n";
		}
		if($row->language && !in_array("language",$exclude))
		{
			$doc .= "%G " . trim($row->language) . "\r\n";
		}
		if($row->author_address && !in_array("author_address",$exclude))
		{
			$doc .= "%+ " . trim($row->author_address) . "\r\n";
		}
		if($row->accession_number && !in_array("accession_number",$exclude))
		{
			$doc .= "%M " . trim($row->accession_number) . "\r\n";
		}
		if($row->call_number && !in_array("callnumber",$exclude))
		{
			$doc .= "%L " . trim($row->call_number) . "\r\n";
		}
		if($row->short_title && !in_array("short_title",$exclude))
		{
			$doc .= "%! " . trim($row->short_title) . "\r\n";
		}
		$doc .= "\r\n";
		return $doc;
	}
}

