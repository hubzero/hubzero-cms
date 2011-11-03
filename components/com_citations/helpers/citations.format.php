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

class CitationFormat
{

	protected $_template = "apa";
	protected $_template_keys = array(
									"type" => "{TYPE}",
									"cite" => "{CITE KEY}",
									"ref_type" => "{REF TYPE}",
									"date_submit" => "{DATE SUBMITTED}",
									"date_accept" => "{DATE ACCEPTED}",
									"date_publish" => "{DATE PUBLISHED}",
									"author" => "{AUTHORS}",
									"editor" => "{EDITORS}",
									"title" => "{TITLE/CHAPTER}",
									"booktitle" => "{BOOK TITLE}",
									"chapter" => "{CHAPTER}",
									"journal" => "{JOURNAL}",
									"volume" => "{VOLUME}",
									"number" => "{ISSUE/NUMBER}",
									"pages" => "{PAGES}",
									"isbn" => "{ISBN/ISSN}",
									"doi" => "{DOI}",
									"series" => "{SERIES}",
									"edition" => "{EDITION}",
									"school" => "{SCHOOL}",
									"publisher" => "{PUBLISHER}",
									"institution" => "{INSTITUTION}",
									"address" => "{ADDRESS}",
									"location" => "{LOCATION}",
									"howpublished" => "{HOW PUBLISHED}",
									"url" => "{URL}",
									"eprint" => "{E-PRINT}",
									"note" => "{TEXT SNIPPET/NOTES}",
									"organization" => "{ORGANIZATION}",
									"year" => "{YEAR}",
									"month" => "{MONTH}",
									"search_string" => "{SECONDARY LINK}",
									"sec_cnt" => "{SECONDARY COUNT}"
									);

	protected $_coins_keys = array(
								'title' => 'rft.atitle',
								//'journal' => 'rft.jtitle',
								'date_publish' => 'rft.date',
								'volume' => 'rft.volume',
								'number' => 'rft.issue',
								'pages' => 'rft.pages',
								'isbn' => 'rft.issn',
								//'type' => 'rft.genre',
								'author' => 'rft.au',
								'url' => 'rft_id',
								'doi' => 'rft_id=info:doi/',
								'author' => 'rft.au'
 								);

	protected $_default_format = array(
		"apa" => "{AUTHORS}, {EDITORS} ({YEAR}), {TITLE/CHAPTER}, <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER}, {SERIES}, {PUBLISHER}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b>: pg. {PAGES}, {ORGANIZATION}, {INSTITUTION}, {SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}, (DOI: {DOI}). Cited by: <a href='{SECONDARY LINK}'>{SECONDARY COUNT}</a>",

		"ieee" => "{AUTHORS}, {EDITORS} ({YEAR}), {TITLE/CHAPTER}, <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER}, {SERIES}, {PUBLISHER}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b>: pg. {PAGES}, {ORGANIZATION}, {INSTITUTION}, {SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}, (DOI: {DOI})"
	);

	/**
	 * Function to set the formatters template to use
	 * 
	 * @param 	String		Template string that will be used to format the citation
	 */
	public function setTemplate( $template )
	{
		if($template != "") {
			$this->_template = trim($template);
		}
	}

	/**
	 * Function to get the formatter template being used
	 * 
	 * @return 		String		Template string that is being used to format citations
	 */
	public function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * Function to set the template keys the formatter will use
	 * 
	 * @param 	String		Template keys that will be used to format the citation
	 */
	public function setTemplateKeys( $template_keys )
	{
		if(!empty($template_keys)) {
			$this->_template_keys = $template_keys;
		}
	}

	/**
	 * Function to get the formatter template keys being used
	 * 
	 * @return 		String		Template string that is being used to format citations
	 */
	public function getTemplateKeys()
	{
		return $this->_template_keys;
	}

	/**
	 * Function to format citation based on template
	 * 
	 * @param 		Object		Citation object
	 * @param 		String		String that we want to highlight
	 * @return 		String		Formatted citation
	 */
	public function formatCitation( $citation , $highlight = NULL, $include_coins = true, $config )
	{
		//get hub specific details
		ximport('Hubzero_Hub');
		$hub = new Hubzero_Hub();
		$hub_name = $hub->getCfg("hubShortName");
		$hub_url = $hub->getCfg("hubLongURL");

		$c_type = "journal";

		$db =& JFactory::getDBO();
		$ct = new CitationsType( $db );
		$types = $ct->getType();

		foreach($types as $t) {
			if($t['id'] == $citation->type) {
				$type = $t['type'];
			}
		}

		switch( strtolower($type) )
		{
			case 'book':
			case 'inbook':
			case 'conference':
			case 'proceedings':
			case 'inproceedings':
				$c_type = "book";
				break;
			case 'journal':
			case 'article':
			case 'journal article';
				$c_type = "journal";
				break;
		}

		//var to hold COinS data
		$coins_data = array(
			"ctx_ver=Z39.88-2004",
			"rft_val_fmt=info:ofi/fmt:kev:mtx:{$c_type}",
			"rfr_id=info:sid/{$hub_url}:{$hub_name}"
			);

		//array to hold replace vals
		$replace_values = array();

		//get the template
		$template = $this->getTemplate();

		//get the template keys
		$template_keys = $this->getTemplateKeys();

		foreach( $template_keys as $k => $v ) {

			if(!$this->keyExistsOrIsNotEmpty( $k, $citation)) {
				$replace_values[$v] = "";
			} else {
				$replace_values[$v] = $citation->$k;

				//add to coins data if we can but not authors as that will get processed below
				if( in_array($k,array_keys($this->_coins_keys)) && $k != 'author' ) {

					//key specific
					switch($k)
					{
						case 'isbn':
							$coins_data[] = ($c_type == 'book') ? "rft.isbn={$citation->$k}" : "rft.issn={$citation->$k}";
							break;
						//case 'title':
						//	$coins_data[] = ($c_type == 'book') ? "rft.btitle={$citation->$k}" : "rft.atitle={$citation->$k}";
						//	break;
						case 'doi':
							$coins_data[] = $this->_coins_keys[$k] . $citation->$k;
							break;
						case 'url':
							$coins_data[] = $this->_coins_keys[$k] . "=" . htmlentities($citation->$k);
							break;
						default:
							$coins_data[] = $this->_coins_keys[$k] . "=" . $citation->$k;
					}
				}

				if($k == "author") {
					$a = array();
					$author_string = $citation->$k;
					$authors = explode(";", $author_string);

					foreach($authors as $author) {
						preg_match('/{{(.*?)}}/s',$author, $matches);
						if(!empty($matches)) {
							$id = trim($matches[1]);
							if(is_numeric($id)) {
								$user =& JUser::getInstance( $id );
								if(is_object($user)) {
									$a[] = "<a rel=\"external\" href=\"/members/{$matches[1]}\">" . str_replace($matches[0],"",$author) . "</a>";
								} else {
									$a[] = $author;
								}
							}
						} else {
							$a[] = $author;
						}

						//add author coins
						$coins_data[] = "rft.au=" . trim(preg_replace('/\{\{\d+\}\}/',"",trim($author)));
					}

					$replace_values[$v] = implode(", ", $a);
				}

				if($k == "title") {
					$url_format = $config->get("citation_url", "url");
					$custom_url = $config->get("citation_custom_url", "");

					if($url_format == "custom" && $custom_url != "") {
 						//parse custom url to make sure we are not using any vars
						preg_match( '/\{(\w+)\}/', $custom_url, $matches);
						if($matches) {
							if($citation->$matches[1]) {
								$url = str_replace($matches[0], $citation->$matches[1], $custom_url);
							} else {
								$url = $citation->url;
							}
						} else {
							$url = $citation->url;
						}
					} else {
						$url = $citation->url;
					}

					$title = ($url != "" && preg_match('/http:|https:/', $url)) ? "<a rel=\"external\" class=\"citation-title\" href=\"{$url}\">".html_entity_decode($citation->$k)."</a>" : "<span class=\"citation-title\">" . html_entity_decode($citation->$k) . "</span>";

					$replace_values[$v] = "\"" . $title . "\"";
 				}

				if($k == "pages") {
					$replace_values[$v] = "pg: " . $citation->$k;
				}
 			}
		}

		$cite = strtr($template, $replace_values);

		// Strip empty tags
		$pattern = "/<[^\/>]*>([\s]?)*<\/[^>]*>/";
		$cite = preg_replace($pattern, '', $cite);

		//reformat dates
		$pattern = "/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/";
		$cite = preg_replace($pattern, "$2-$3-$1", $cite);

		// Reduce multiple spaces to one
		$pattern = "/\s/s";
		$cite = preg_replace($pattern, ' ', $cite);

		// Strip empty punctuation inside
		$b = array(
			"''" => '',
			'""' => '',
			'()' => '',
			'{}' => '',
			'[]' => '',
			'??' => '',
			'!!' => '',
			'..' => '.',
			',,' => ',',
			' ,' => '',
			' .' => '',
			'","'=> ''
			//","  => ''
		);

		foreach ($b as $k => $i) {
			$cite = str_replace($k, $i, $cite);
		}

		// Strip empty punctuation from the start
		$c = array(
			"' ",
			'" ',
			'( ',
			') ',
			', ',
			'. ',
			'? ',
			'! ',
			': ',
			'; '
		);

		foreach ($c as $k) {
			if (substr($cite,0,2) == $k) {
				$cite = substr($cite,2);
			}
		}

		//remove trailing commas
		$cite = trim($cite);
		if(substr($cite, -1) == ',') {
			$cite = substr($cite, 0, strlen($cite)-1);
		}

		//percent encode chars
		$chars = array(" ","/",":",'"',"&amp;");
		$replace = array("%20","%2F","%3A","%22", "%26");
		$coins_data = str_replace( $chars, $replace, implode("&",$coins_data));

		//if we want coins add them
		if($include_coins) {
			$cite .= "<span class=\"Z3988\" title=\"{$coins_data}\"></span>";
		}

		//output the citation
		return ($highlight) ? Hubzero_View_Helper_Html::str_highlight( $cite, array($highlight) ) : $cite;
	}

	/**
	 * Function to return the default formats (APA, IEEE, etc)
	 *
	 * @param 	string		Format Type
	 * @return  String		Formats Template
	 */
	public function getDefaultFormat( $format )
	{
		return $this->_default_format[$format];
	}

	//------------------------------------------------------------------
	//	citation links and badges
	//------------------------------------------------------------------

	public function citationDetails( $citation, $database, $config, $openurl )
	{
		$downloading = $config->get("citation_download", 1);
		$openurls = $config->get("citation_openurl", 1);

		$html  = "";

		//are we allowing downloading
		if($downloading) {
			$html .= "<a href=".JRoute::_('index.php?option=com_citations&task=download&id='.$citation->id.'&format=bibtex&no_html=1')." title=".JText::_('DOWNLOAD_BIBTEX').">BibTex</a>";
			$html .= "<span> | </span>";
			$html .= "<a href=".JRoute::_('index.php?option=com_citations&task=download&id='.$citation->id.'&format=endnote&no_html=1')." title=".JText::_('DOWNLOAD_ENDNOTE').">EndNote</a>";
		}

		if( $openurl['link'] && $openurls ) {
			$text = $openurl['text'];
			$icon = $openurl['icon'];
			$link = $openurl['link'];

			$link .= "?doi=" . $citation->doi;
			$link .= "&isbn=" . $citation->isbn;
			$link .= "&issn=" . $citation->isbn;

			$link_text = ($icon != "") ? "<img src=\"{$icon}\" />" : $text;
			$html .= "<span> | </span>";
			$html .= "<a rel=\"external\" href=\"{$link}\" title=\"{$text}\">{$link_text}</a>";
		}

		// Get the associations
		$assoc = new CitationsAssociation( $database );
		$assocs = $assoc->getRecords( array('cid'=>$citation->id) );

		if (count($assocs) > 0) {
			if (count($assocs) > 1) {
				$html .= '<span>|</span> '.JText::_('RESOURCES_CITED').': ';
				$k = 0;
				$rrs = array();
				foreach ($assocs as $rid)
				{
					if ($rid->table == 'resource') {
						$database->setQuery( "SELECT published FROM #__resources WHERE id=".$rid->oid );
						$state = $database->loadResult();
						if ($state == 1) {
							$k++;
							$rrs[] = '<a href="'.JRoute::_('index.php?option=com_resources&id='.$rid->oid).'">['.$k.']</a>';
						}
					}
				}

				$html .= implode(', ',$rrs);
			} else {
				if ($assocs[0]->table == 'resource') {
					$database->setQuery( "SELECT published FROM #__resources WHERE id=".$assocs[0]->oid );
					$state = $database->loadResult();
					if ($state == 1) {
						$html .= ' <span>|</span> <a href="'.JRoute::_('index.php?option=com_resources&id='.$assocs[0]->oid).'">'.JText::_('RESOURCE_CITED').'</a>';
					}
				}
			}
		}

		if($citation->eprint) {
			$html .= "<span>|</span>";
			$html .= "<a href=".Hubzero_View_Helper_Html::ampReplace($citation->eprint).">".JText::_('ELECTRONIC_PAPER')."</a>";
		}

		return $html;
	}

	public function citationBadges( $citation, $database )
	{
		$html = "";
		$badges = array();

		$sql = "SELECT t.*
				FROM #__tags_object to1 
				INNER JOIN #__tags t ON t.id = to1.tagid 
				WHERE to1.tbl='citations' 
				AND to1.objectid={$citation->id}
				AND to1.label='badge'";
		$database->setQuery( $sql );
		$badges = $database->loadAssocList();

		if($badges) {
			$html = "<ul class=\"badges\">";
			foreach($badges as $badge) {
				$html .= "<li>" . $badge['raw_tag'] . "</li>";
			}
			$html .= "</ul>";
		}

		return $html;
	}

	public function citationTags( $citation, $database )
	{
		$html = "";
		$tags = array();

		$sql = "SELECT t.*
				FROM #__tags_object to1 
				INNER JOIN #__tags t ON t.id = to1.tagid 
				WHERE to1.tbl='citations' 
				AND to1.objectid={$citation->id}
				AND to1.label=''";
		$database->setQuery( $sql );
		$tags = $database->loadAssocList();

		if($tags) {
			$html = "<ul class=\"tags\">";
			$html .= "<li>Tags: </li>";
			foreach($tags as $tag) {
				$html .= "<li><a href=\"/tags/{$tag['raw_tag']}\">" . $tag['raw_tag'] . "</a></li>";
			}
			$html .= "</ul>";
		}

		return $html;
	}

	//------------------------------------------------------------------
	//	Utility Functions
	//------------------------------------------------------------------

	public function cleanUrl($url)
	{
		$url = stripslashes($url);
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);

		return $url;
	}

	public function keyExistsOrIsNotEmpty($key,$row)
	{
		if (isset($row->$key)) {
			if ($row->$key != '' && $row->$key != '0' && $row->$key != "0000-00-00 00:00:00") {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function grammarCheck($html, $punct=',')
	{
		if (substr($html,-1) == '"') {
			$html = substr($html,0,strlen($html)-1).$punct.'"';
		} else {
			$html .= $punct;
		}
		return $html;
	}

	//------------------------------------------------------------------
	//	Formatting Resources
	//------------------------------------------------------------------

	public function formatReference(&$row, $link='none', $highlight='')
	{
		ximport('Hubzero_View_Helper_Html');

		$html = "\t".'<p>';
		if (CitationFormat::keyExistsOrIsNotEmpty('author',$row)) {
			$xprofile =& Hubzero_Factory::getProfile();
			$app   =& JFactory::getApplication();
			$auths = explode(';',$row->author);
			$a = array();
			foreach ($auths as $auth)
			{
				preg_match('/{{(.*?)}}/s',$auth, $matches);
				if (isset($matches[0]) && $matches[0]!='') {
					$matches[0] = preg_replace( '/{{(.*?)}}/s', '\\1', $matches[0] );
					$aid = 0;
					if (is_numeric($matches[0])) {
						$aid = $matches[0];
					} else {
						$zuser =& JUser::getInstance( trim($matches[0]) );
						if (is_object($zuser)) {
							$aid = $zuser->get('id');
						}
					}
					$auth = preg_replace( '/{{(.*?)}}/s', '', $auth );
					if ($aid) {
						$app =& JFactory::getApplication();
						if (is_object($xprofile) && in_array(strtolower($app->getCfg('sitename')), $xprofile->get('admin'))) {
							$a[] = '<a href="index.php?option=com_whois&query=uidNumber%3D'.$aid.'">'.trim($auth).'</a>';
						} else {
							$a[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$aid).'">'.trim($auth).'</a>';
						}
					} else {
						$a[] = trim($auth);
					}
				} else {
					$a[] = trim($auth);
				}
			}
			$row->author = implode('; ', $a);

			$html .= stripslashes($row->author);
		} elseif (CitationFormat::keyExistsOrIsNotEmpty('editor',$row)) {
			$html .= stripslashes($row->editor);
		}

		if (CitationFormat::keyExistsOrIsNotEmpty('year',$row)) {
			$html .= ' ('.$row->year.')';
		}

		if (CitationFormat::keyExistsOrIsNotEmpty('title',$row)) {
			if (!$row->url) {
				$html .= ', "'.stripslashes($row->title);
			} else {
				$html .= ', "<a href="'.CitationFormat::cleanUrl($row->url).'">'.Hubzero_View_Helper_Html::str_highlight(stripslashes($row->title),array($highlight)).'</a>';
			}
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('journal',$row)
		|| CitationFormat::keyExistsOrIsNotEmpty('edition',$row)
		|| CitationFormat::keyExistsOrIsNotEmpty('booktitle',$row)) {
			$html .= ',';
		}
		$html .= '"';
		if (CitationFormat::keyExistsOrIsNotEmpty('journal',$row)) {
			$html .= ' <i>'.Hubzero_View_Helper_Html::str_highlight(stripslashes($row->journal),array($highlight)).'</i>';
		} elseif (CitationFormat::keyExistsOrIsNotEmpty('booktitle',$row)) {
			$html .= ' <i>'.stripslashes($row->booktitle).'</i>';
		}
		if ($row->type) {
			switch ($row->type)
			{
				case 'phdthesis': $html .= ' ('.JText::_('PhD Thesis').')'; break;
				case 'mastersthesis': $html .= ' ('.JText::_('Masters Thesis').')'; break;
				default: break;
			}
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('edition',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.$row->edition;
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('chapter',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->chapter);
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('series',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->series);
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('publisher',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->publisher);
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('address',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->address);
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('volume',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' <b>'.$row->volume.'</b>';
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('number',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' <b>'.$row->number.'</b>';
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('pages',$row)) {
			$html .= ': pg. '.$row->pages;
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('organization',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->organization);
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('institution',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->institution);
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('school',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->school);
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('location',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.stripslashes($row->location);
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('month',$row)) {
			$html  = CitationFormat::grammarCheck( $html, ',' );
			$html .= ' '.$row->month;
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('isbn',$row)) {
			$html  = CitationFormat::grammarCheck( $html, '.' );
			$html .= ' '.$row->isbn;
		}
		if (CitationFormat::keyExistsOrIsNotEmpty('doi',$row)) {
			$html  = CitationFormat::grammarCheck( $html, '.' );
			$html .= ' ('.JText::_('DOI').': '.$row->doi.')';
		}
		$html  = CitationFormat::grammarCheck( $html, '.' );
		$html .= '</p>'."\n";

		return $html;
	}

}

?>