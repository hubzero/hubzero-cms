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

namespace Components\Citations\Helpers;

require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'format.php');

use Components\Citations\Tables\Association;
use Components\Citations\Tables\Type;
use Components\Citations\Models\Format as CitationFormat;
use Hubzero\Utility\String;
use Hubzero\User\Profile;

/**
 * Citations helper class for formatting results
 */
class Format
{
	/**
	 * Formatting template to use (defaults to APA)
	 *
	 * @var  string
	 */
	protected $_template = 'apa';

	/**
	 * Description for '_template_keys'
	 *
	 * @var  array
	 */
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
		"journaltitle" => "{JOURNAL TITLE}",
		"volume" => "{VOLUME}",
		"number" => "{ISSUE/NUMBER}",
		"pages" => "{PAGES}",
		"isbn" => "{ISBN/ISSN}",
		"issn" => "{ISSN}",
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
		"abstract" => "{ABSTRACT}",
		"year" => "{YEAR}",
		"month" => "{MONTH}",
		"search_string" => "{SECONDARY LINK}",
		"sec_cnt" => "{SECONDARY COUNT}"
	);

	/**
	 * Values used by COINs
	 *
	 * @var  array
	 */
	protected $_coins_keys = array(
		'title'        => 'rft.atitle',
		'journaltitle' => 'rft.jtitle',
		'date_publish' => 'rft.date',
		'volume'       => 'rft.volume',
		'number'       => 'rft.issue',
		'pages'        => 'rft.pages',
		'issn'         => 'rft.issn',
		'isbn'         => 'rft.isbn',
		'type'         => 'rft.genre',
		'author'       => 'rft.au',
		'url'          => 'rft_id',
		'doi'          => 'rft_id=info:doi/',
		'author'       => 'rft.au'
	);

	/**
	 * Default formats
	 *
	 * @var  array
	 */
	protected $_default_format = array(
		'apa'  => "{AUTHORS}, {EDITORS} ({YEAR}), {TITLE/CHAPTER}, <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER}, {SERIES}, {PUBLISHER}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b>: {PAGES}, {ORGANIZATION}, {INSTITUTION}, {SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}, (DOI: {DOI}). Cited by: <a href='{SECONDARY LINK}'>{SECONDARY COUNT}</a>",
		'ieee' => "{AUTHORS}, {EDITORS} ({YEAR}), {TITLE/CHAPTER}, <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER}, {SERIES}, {PUBLISHER}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b>: {PAGES}, {ORGANIZATION}, {INSTITUTION}, {SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}, (DOI: {DOI})"
	);

	/**
	 * Function to set the formatters template to use
	 *
	 * @param  string  Template string that will be used to format the citation
	 */
	public function setTemplate($template)
	{
		if ($template != '')
		{
			$this->_template = trim($template);
		}
		else
		{
			$this->_template = $this->_default_format['apa'];
		}
	}

	/**
	 * Function to get the formatter template being used
	 *
	 * @return  string  Template string that is being used to format citations
	 */
	public function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * Function to set the template keys the formatter will use
	 *
	 * @param  string  Template keys that will be used to format the citation
	 */
	public function setTemplateKeys($template_keys)
	{
		if (!empty($template_keys))
		{
			$this->_template_keys = $template_keys;
		}
	}

	/**
	 * Function to get the formatter template keys being used
	 *
	 * @return  string  Template string that is being used to format citations
	 */
	public function getTemplateKeys()
	{
		return $this->_template_keys;
	}

	/**
	 * Function to format citation based on template
	 *
	 * @param   object   $citation       Citation object
	 * @param   string   $highlight      String that we want to highlight
	 * @param   boolean  $include_coins  Include COINs?
	 * @param   object   $config         Registry
	 * @param   boolean  $coins_only     Only output COINs?
	 * @return  string   Formatted citation
	 */
	public function formatCitation($citation, $highlight = NULL, $include_coins = true, $config, $coins_only = false)
	{
		//get hub specific details
		$hub_name = \Config::get('sitename');
		$hub_url  = rtrim(\Request::base(), '/');

		$c_type = 'journal';

		$db = \App::get('db');
		$ct = new Type($db);
		$types = $ct->getType();

		$type = '';
		foreach ($types as $t)
		{
			if ($t['id'] == $citation->type)
			{
				$type = $t['type'];
			}
		}

		switch (strtolower($type))
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
			default:
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
		if ($config->get('default_citation_format') != '')
		{
			$template = CitationFormat::all()
				->where('style', 'LIKE', $config->get('default_citation_format'))
				->limit(1)
				->row()->format;
		}
		else
		{
			$template = $this->getTemplate();
		}

		//get the template keys
		$template_keys = $this->getTemplateKeys();

		foreach ($template_keys as $k => $v)
		{
			if (!self::keyExistsOrIsNotEmpty($k, $citation))
			{
				$replace_values[$v] = '';
			}
			else
			{
				$replace_values[$v] = $citation->$k;

				//add to coins data if we can but not authors as that will get processed below
				if (in_array($k, array_keys($this->_coins_keys)) && $k != 'author')
				{

					//key specific
					switch ($k)
					{
						case 'title':
							break;
						case 'doi':
							$coins_data[] = $this->_coins_keys[$k] . $citation->$k;
							break;
						case 'url':
							$coins_data[] = $this->_coins_keys[$k] . '=' . htmlentities($citation->$k);
							break;
						case 'journaltitle':
							$jt = html_entity_decode($citation->$k);
							$jt = (!preg_match('!\S!u', $jt)) ? utf8_encode($jt) : $jt;
							$coins_data[] = $this->_coins_keys[$k] . '=' . $jt;
							break;
						default:
							$coins_data[] = $this->_coins_keys[$k] . '=' . $citation->$k;
					}
				}

				if ($k == 'author')
				{
					$a = array();

					$auth = html_entity_decode($citation->$k);
					$auth = (!preg_match('!\S!u', $auth)) ? utf8_encode($auth) : $auth;

					$author_string = $auth;
					$authors = explode(';', $author_string);

					foreach ($authors as $author)
					{
						preg_match('/{{(.*?)}}/s', $author, $matches);
						if (!empty($matches))
						{
							$id = trim($matches[1]);
							if (is_numeric($id))
							{
								$user = \User::getInstance($id);
								if (is_object($user))
								{
									$a[] = '<a rel="external" href="' . \Route::url('index.php?option=com_members&id=' . $matches[1]) . '">' . str_replace($matches[0], '', $author) . '</a>';
								}
								else
								{
									$a[] = $author;
								}
							}
						}
						else
						{
							$a[] = $author;
						}

						//add author coins
						$coins_data[] = 'rft.au=' . trim(preg_replace('/\{\{\d+\}\}/', '', trim($author)));
					}

					$replace_values[$v] = implode(", ", $a);
				}

				if ($k == 'title')
				{
					$url_format = $config->get("citation_url", "url");
					$custom_url = $config->get("citation_custom_url", '');

					$url = $citation->url;
					if ($url_format == 'custom' && $custom_url != '')
					{
						//parse custom url to make sure we are not using any vars
						preg_match_all('/\{(\w+)\}/', $custom_url, $matches, PREG_SET_ORDER);
						if ($matches)
						{
							foreach ($matches as $match)
							{
								$field = strtolower($match[1]);
								$replace = $match[0];
								$replaceWith = '';
								if (property_exists($citation, $field))
								{
									if (strstr($citation->$field, 'http'))
									{
										$custom_url = $citation->$field;
									}
									else
									{
										$replaceWith = urlencode($citation->$field);
										$custom_url = str_replace($replace, $replaceWith, $custom_url);
									}
								}
							}
							//set the citation url to be the new custom url parsed
							$url  = $custom_url;
						}
					}

					//prepare url
					if (strstr($url, "\r\n"))
					{
						$url = array_filter(array_values(explode("\r\n", $url)));
						$url = $url[0];
					}
					elseif (strstr($url, " "))
					{
						$url = array_filter(array_values(explode(" ", $url)));
						$url = $url[0];
					}

					$t = html_entity_decode($citation->$k);
					$t = (!preg_match('!\S!u', $t)) ? utf8_encode($t) : $t;

					$title = ($url != '' && preg_match('/http:|https:/', $url))
							? '<a rel="external" class="citation-title" href="' . $url . '">' . $t . '</a>'
							: '<span class="citation-title">' . $t . '</span>';

					//do we want to display single citation
					$singleCitationView = $config->get('citation_single_view', 0);
					if ($singleCitationView && isset($citation->id))
					{
						$title = '<a href="' . \Route::url('index.php?option=com_citations&task=view&id=' . $citation->id) . '">' . $t . '</a>';
					}

					//send back title to replace title placeholder ({TITLE})
					$replace_values[$v] = '"' . $title . '"';

					//add title to coin data but fixing bad chars first
					$coins_data[] = 'rft.atitle=' . $t;
				}

				if ($k == 'pages')
				{
					$replace_values[$v] = "pg: " . $citation->$k;
				}
			}
		}

		// Add more to coins

		$tmpl = isset($this->_default_format[$template]) ? $this->_default_format[$template] : $template;;
		$cite = strtr($tmpl, $replace_values);

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
			',.' => '.',
			'","'=> '',
			'doi:.'=>'',
			'(DOI:).'=>''
		);

		foreach ($b as $k => $i)
		{
			$cite = str_replace($k, $i, $cite);
		}

		// Strip empty punctuation from the start
		$c = array(
			"' ",
			'" ',
			'(',
			') ',
			', ',
			'. ',
			'? ',
			'! ',
			': ',
			'; '
		);

		foreach ($c as $k)
		{
			if (substr($cite, 0, 2) == $k)
			{
				$cite = substr($cite, 2);
			}
		}

		// remove trailing commas
		$cite = trim($cite);
		if (substr($cite, -1) == ',')
		{
			$cite = substr($cite, 0, strlen($cite)-1);
		}

		// percent encode chars
		$chars      = array('%', ' ', '/', ':', '"', '\'', '&amp;');
		$replace    = array("%20", "%20", "%2F", "%3A", "%22", "%27", "%26");
		$coins_data = str_replace($chars, $replace, implode('&', $coins_data));

		$cite = preg_replace('/, :/', ':', $cite);

		// highlight citation data
		// do before appendnind coins as we dont want that data accidentily highlighted (causes style issues)
		$cite = ($highlight) ? String::highlight($cite, $highlight) : $cite;

		// if we want coins add them
		if ($include_coins || $coins_only)
		{
			$coins = '<span class="Z3988" title="' . $coins_data . '"></span>';
			if ($coins_only == true)
			{
				return $coins;
			}

			$cite .= $coins;
		}

		// output the citation
		return $cite;
	}

	/**
	 * Function to return the default formats (APA, IEEE, etc)
	 *
	 * @param   string  $format  Format Type
	 * @return  string  Formats Template
	 */
	public function getDefaultFormat($format = 'apa')
	{
		return $this->_default_format[$format];
	}

	/**
	 * Citation links and badges
	 *
	 * @param   object  $citation  Citation record
	 * @param   object  $database  Database
	 * @param   object  $config    Registry
	 * @param   array   $openurl   Data to append
	 * @return  string
	 */
	public function citationDetails($citation, $database, $config, $openurl)
	{
		$downloading = $config->get('citation_download', 1);
		$openurls = $config->get('citation_openurl', 1);

		$html  = '';

		// are we allowing downloading
		if ($downloading)
		{
			$html .= '<a rel="nofollow" href="' . \Route::url('index.php?option=com_citations&task=download&id=' . $citation->id . '&citationFormat=bibtex&no_html=1') . '" title="' . \Lang::txt('COM_CITATIONS_BIBTEX') . '">' . \Lang::txt('COM_CITATIONS_BIBTEX') . '</a>';
			$html .= '<span> | </span>';
			$html .= '<a rel="nofollow" href="' . \Route::url('index.php?option=com_citations&task=download&id=' . $citation->id . '&citationFormat=endnote&no_html=1') . '" title="' . \Lang::txt('COM_CITATIONS_ENDNOTE') . '">' . \Lang::txt('COM_CITATIONS_ENDNOTE') . '</a>';
		}

		// if we have an open url link and we want to use open urls
		if ($openurl['link'] && $openurls)
		{
			$html .= '<span> | </span>' . self::citationOpenUrl($openurl, $citation);
		}

		// citation association - to HUB resources
		$html .= $this->citationAssociation($config, $citation);

		return $html;
	}

	/**
	 * Output a citation's OpenURL
	 *
	 * @param   array   $openurl   OpenURL data
	 * @param   object  $citation  Citation
	 * @return  string  HTML
	 */
	public static function citationOpenUrl($openurl, $citation)
	{
		$html = "";

		$database = \App::get('db');

		$text  = $openurl['text'];
		$icon  = $openurl['icon'];
		$link  = $openurl['link'];
		$query = array();

		// citation type
		$citation_type = new Type($database);
		$citation_type->load($citation->type);

		// do we have a title
		if (isset($citation->title) && $citation->title != '')
		{
			if ($citation_type->type == 'journal')
			{
				$query[] = 'atitle=' . str_replace(' ', '+', $citation->title);
				$query[] = 'title=' . str_replace(' ', '+', $citation->journal);
			}
			else
			{
				$query[] = 'title=' . str_replace(' ', '+', $citation->title);
			}
		}

		// do we have a doi to append?
		if (isset($citation->doi) && $citation->doi != '')
		{
			$query[] = 'doi=' . $citation->doi;
		}

		// do we have an issn or isbn to append?
		if (isset($citation->isbn) && trim($citation->isbn) != '')
		{
			// get the issn/isbn in db
			$issn_isbn = trim($citation->isbn);

			// check to see if we need to do any special processing to the issn/isbn before outputting
			if (strstr($issn_isbn, "\r\n"))
			{
				$issn_isbn = array_filter(array_values(explode("\r\n", $issn_isbn)));
				$issn_isbn = isset($issn_isbn[0]) ? preg_replace("/[^0-9\-]/", '', $issn_isbn[0]) : '';
			}
			elseif (strstr($issn_isbn, ' '))
			{
				$issn_isbn = array_filter(array_values(explode(' ', $issn_isbn)));
				$issn_isbn = isset($issn_isbn[0]) ? preg_replace("/[^0-9\-]/", '', $issn_isbn[0]) : '';
			}

			// append to url as issn if journal otherwise as isbn
			if ($citation_type->type == 'journal')
			{
				$query[] = 'issn=' . $issn_isbn;
			}
			else
			{
				$query[] = 'isbn=' . $issn_isbn;
			}
		}

		// do we have a date/year to append?
		if (isset($citation->year) && $citation->year != '')
		{
			$query[] = 'date=' . $citation->year;
		}

		// to we have an issue/number to append?
		if (isset($citation->number) && $citation->number != '')
		{
			$query[] = 'issue=' . $citation->number;
		}

		// do we have a volume to append?
		if (isset($citation->volume) && $citation->volume != '')
		{
			$query[] = 'volume=' . $citation->volume;
		}

		// do we have pages to append?
		if (isset($citation->pages) && $citation->pages != '')
		{
			$query[] = 'pages=' . $citation->pages;
		}

		// do we have a link with some data to send to resolver?
		if (count($query) > 0)
		{

			// checks for already-appended ?
			if (substr($link, -1, 1) != "?")
			{
				$link .= "?";
			}

			//add parts to url
			$link .= implode("&", $query);

			// do we have an icon or just using text as the link
			$link_text = ($icon != '') ? '<img src="index.php?option=com_citations&controller=citations&task=downloadimage&image=' . $icon . '" />' : $text;

			// final link
			$html .= '<a rel="external nofollow" href="' . $link . '" title="' . $text . '">' . $link_text . '</a>';
		}

		return $html;
	}

	/**
	 * Output a citation's associations
	 *
	 * @param   object  $config    Component config
	 * @param   object  $citation  Citation
	 * @return  string  HTML
	 */
	public function citationAssociation($config, $citation)
	{
		$html = '';

		$internally_cited_image = $config->get('citation_cited', 0);
		$internally_cited_image_single = $config->get('citation_cited_single', '');
		$internally_cited_image_multiple = $config->get('citation_cited_multiple', '');

		//database
		$database = \App::get('db');

		// Get the associations
		$assoc = new Association($database);
		$assocs = $assoc->getRecords(array('cid' => $citation->id));

		if (count($assocs) > 0)
		{
			if (count($assocs) > 1)
			{
				$html .= '<span>|</span> <span style="line-height:1.6em;color:#444">' . \Lang::txt('COM_CITATIONS_RESOURCES_CITED') . ':</span> ';
				$k = 0;
				$rrs = array();
				foreach ($assocs as $rid)
				{
					if ($rid->tbl == 'resource')
					{
						$database->setQuery("SELECT published FROM `#__resources` WHERE id=" . $rid->oid);
						$state = $database->loadResult();
						if ($state == 1)
						{
							$k++;
							if ($internally_cited_image)
							{
								$rrs[] = '<a class="internally-cited" href="' . \Route::url('index.php?option=com_resources&id=' . $rid->oid) . '">[<img src="' . $internally_cited_image_multiple . '" alt="' . \Lang::txt('COM_CITATIONS_RESOURCES_CITED') . '" />]</a>';
							}
							else
							{
								$rrs[] = '<a class="internally-cited" href="' . \Route::url('index.php?option=com_resources&id=' . $rid->oid) . '">[' . $k . ']</a>';
							}
						}
					}
				}

				$html .= implode(', ', $rrs);
			}
			else
			{
				if ($assocs[0]->tbl == 'resource')
				{
					$database->setQuery("SELECT published FROM `#__resources` WHERE id=" . $assocs[0]->oid);
					$state = $database->loadResult();
					if ($state == 1)
					{
						if ($internally_cited_image)
						{
							$html .= ' <span>|</span> <a class="internally-cited" href="' . \Route::url('index.php?option=com_resources&id=' . $assocs[0]->oid) . '"><img src="' . $internally_cited_image_single . '" alt="' . \Lang::txt('COM_CITATIONS_RESOURCES_CITED') . '" /></a>';
						}
						else
						{
							$html .= ' <span>|</span> <a class="internally-cited" href="' . \Route::url('index.php?option=com_resources&id=' . $assocs[0]->oid) . '">' . \Lang::txt('COM_CITATIONS_RESOURCES_CITED') . '</a>';
						}
					}
				}
			}
		}

		if ($citation->eprint)
		{
			$html .= '<span>|</span>';
			$html .= '<a href="' . String::ampReplace($citation->eprint) . '">' . \Lang::txt('Electronic Paper') . '</a>';
		}

		return $html;
	}

	/**
	 * Output a tagcloud of badges associated with a citation
	 *
	 * @param   object  $citation  Citation
	 * @param   object  $database  JDatabase
	 * @return  string  HTML
	 */
	public static function citationBadges($citation, $database, $includeHtml = true)
	{
		$html = '';
		$badges = array();

		$sql = "SELECT DISTINCT t.*
				FROM `#__tags_object` to1
				INNER JOIN `#__tags` t ON t.id = to1.tagid
				WHERE to1.tbl='citations'
				AND to1.objectid={$citation->id}
				AND to1.label='badge'";
		$database->setQuery($sql);
		$badges = $database->loadAssocList();

		if ($badges)
		{
			if ($includeHtml)
			{
				$html = '<ul class="tags badges">';
				foreach ($badges as $badge)
				{
					$html .= '<li><a href="#">' . stripslashes($badge['raw_tag']) . '</a></li>';
				}
				$html .= "</ul>";
				return $html;
			}
			else
			{
				return $badges;
			}
		}

		return ($includeHtml) ? '' : $badges;
	}

	/**
	 * Output a tagcloud of tags associated with a citation
	 *
	 * @param   object  $citation  Citation
	 * @param   object  $database  JDatabase
	 * @return  string  HTML
	 */
	public static function citationTags($citation, $database, $includeHtml = true)
	{
		$html = '';
		$tags = array();

		$sql = "SELECT DISTINCT t.*
				FROM `#__tags_object` to1
				INNER JOIN `#__tags` t ON t.id = to1.tagid
				WHERE to1.tbl='citations'
				AND to1.objectid={$citation->id}
				AND (to1.label='' OR to1.label IS NULL)";
		$database->setQuery($sql);
		$tags = $database->loadAssocList();

		if ($tags)
		{
			if ($includeHtml)
			{
				$isAdmin = (\User::authorise('core.manage', 'com_citations') ? true : false);

				$html  = '<ul class="tags">';
				foreach ($tags as $tag)
				{
					$cls = ($tag['admin']) ? 'admin' : '';

					//display tag if not admin tag or if admin tag and user is adminstrator
					if (!$tag['admin'] || ($tag['admin'] && $isAdmin))
					{
						$html .= '<li class="' . $cls . '"><a class="tag' . ($tag['admin'] ? ' admin' : '') . '" href="' . \Route::url('index.php?option=com_tags&tag=' . $tag['tag']) . '">' . stripslashes($tag['raw_tag']) . '</a></li>';
					}
				}
				$html .= '</ul>';
				return $html;
			}
			else
			{
				return $tags;
			}
		}

		return ($includeHtml) ? '' : $tags;
	}

	/**
	 * Encode ampersands
	 *
	 * @param   string  $url  URL to encode
	 * @return  string
	 */
	public static function cleanUrl($url)
	{
		$url = stripslashes($url);
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);

		return $url;
	}

	/**
	 * Check if a property of an object exist and is filled in
	 *
	 * @param   string   $key  Property name
	 * @param   object   $row  Object to look in
	 * @return  boolean  True if exists, false if not
	 */
	public static function keyExistsOrIsNotEmpty($key, $row)
	{
		if (isset($row->$key))
		{
			if ($row->$key != '' && $row->$key != '0' && $row->$key != '0000-00-00 00:00:00')
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Ensure correct punctuation
	 *
	 * @param   string  $html   String to check punctuation on
	 * @param   string  $punct  Punctuation to insert
	 * @return  string
	 */
	public static function grammarCheck($html, $punct=',')
	{
		if (substr($html, -1) == '"')
		{
			$html = substr($html, 0, strlen($html)-1) . $punct . '"';
		}
		else
		{
			$html .= $punct;
		}
		return $html;
	}

	/**
	 * Formatting Resources
	 *
	 * @param   object  &$row       Record to format
	 * @param   string  $link       Parameter description (if any) ...
	 * @param   string  $highlight  String to highlight
	 * @return  string
	 */
	public static function formatReference(&$row, $link='none', $highlight='')
	{
		$html = "\t" . '<p>';
		if (self::keyExistsOrIsNotEmpty('author', $row))
		{
			$xprofile = Profile::getInstance(\User::get('id'));

			$auths = explode(';', $row->author);
			$a = array();
			foreach ($auths as $auth)
			{
				preg_match('/{{(.*?)}}/s',$auth, $matches);
				if (isset($matches[0]) && $matches[0]!='')
				{
					$matches[0] = preg_replace('/{{(.*?)}}/s', '\\1', $matches[0]);
					$aid = 0;
					if (is_numeric($matches[0]))
					{
						$aid = $matches[0];
					}
					else
					{
						$zuser = \User::getInstance(trim($matches[0]));
						if (is_object($zuser))
						{
							$aid = $zuser->get('id');
						}
					}
					$auth = preg_replace('/{{(.*?)}}/s', '', $auth);
					if ($aid)
					{
						$a[] = '<a href="' . \Route::url('index.php?option=com_members&id=' . $aid) . '">' . trim($auth) . '</a>';
					}
					else
					{
						$a[] = trim($auth);
					}
				}
				else
				{
					$a[] = trim($auth);
				}
			}
			$row->author = implode('; ', $a);

			$html .= stripslashes($row->author);
		}
		elseif (self::keyExistsOrIsNotEmpty('editor', $row))
		{
			$html .= stripslashes($row->editor);
		}

		if (self::keyExistsOrIsNotEmpty('year', $row))
		{
			$html .= ' (' . $row->year . ')';
		}

		if (self::keyExistsOrIsNotEmpty('title', $row))
		{
			if (!$row->url)
			{
				$html .= ', "' . stripslashes($row->title);
			}
			else
			{
				$html .= ', "<a href="' . self::cleanUrl($row->url) . '">' . String::highlight(stripslashes($row->title), $highlight) . '</a>';
			}
		}
		if (self::keyExistsOrIsNotEmpty('journal', $row)
		 || self::keyExistsOrIsNotEmpty('edition', $row)
		 || self::keyExistsOrIsNotEmpty('booktitle', $row))
		{
			$html .= ',';
		}
		$html .= '"';
		if (self::keyExistsOrIsNotEmpty('journal', $row))
		{
			$html .= ' <i>' . String::highlight(stripslashes($row->journal), $highlight) . '</i>';
		}
		elseif (self::keyExistsOrIsNotEmpty('booktitle', $row))
		{
			$html .= ' <i>' . stripslashes($row->booktitle) . '</i>';
		}
		if ($row->type)
		{
			switch ($row->type)
			{
				case 'phdthesis': $html .= ' (' . \Lang::txt('PhD Thesis') . ')'; break;
				case 'mastersthesis': $html .= ' (' . \Lang::txt('Masters Thesis') . ')'; break;
				default: break;
			}
		}
		if (self::keyExistsOrIsNotEmpty('edition', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . $row->edition;
		}
		if (self::keyExistsOrIsNotEmpty('chapter', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . stripslashes($row->chapter);
		}
		if (self::keyExistsOrIsNotEmpty('series', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . stripslashes($row->series);
		}
		if (self::keyExistsOrIsNotEmpty('publisher', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . stripslashes($row->publisher);
		}
		if (self::keyExistsOrIsNotEmpty('address', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . stripslashes($row->address);
		}
		if (self::keyExistsOrIsNotEmpty('volume', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' <b>' . $row->volume . '</b>';
		}
		if (self::keyExistsOrIsNotEmpty('number', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' <b>' . $row->number . '</b>';
		}
		if (self::keyExistsOrIsNotEmpty('pages', $row))
		{
			$html .= ': pg. ' . $row->pages;
		}
		if (self::keyExistsOrIsNotEmpty('organization', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . stripslashes($row->organization);
		}
		if (self::keyExistsOrIsNotEmpty('institution', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . stripslashes($row->institution);
		}
		if (self::keyExistsOrIsNotEmpty('school', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . stripslashes($row->school);
		}
		if (self::keyExistsOrIsNotEmpty('location', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . stripslashes($row->location);
		}
		if (self::keyExistsOrIsNotEmpty('month', $row))
		{
			$html  = self::grammarCheck($html, ',');
			$html .= ' ' . $row->month;
		}
		if (self::keyExistsOrIsNotEmpty('isbn', $row))
		{
			$html  = self::grammarCheck($html, '.');
			$html .= ' ' . $row->isbn;
		}
		if (self::keyExistsOrIsNotEmpty('doi', $row))
		{
			$html  = self::grammarCheck($html, '.');
			$html .= ' (' . \Lang::txt('DOI') . ': ' . $row->doi . ')';
		}
		$html  = self::grammarCheck($html, '.');
		$html .= '</p>' . "\n";

		return $html;
	}
}
