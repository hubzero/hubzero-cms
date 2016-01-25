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
 * @author	Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since	 Class available since release 1.3.2
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Base\Object;

require_once(__DIR__ . DS . 'link.php');

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Citation extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = '';

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'name';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'type'  => 'notempty',
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $always = array(
		//'name_normalized',
		//'asset_id'
	);

	/**
	 * Defines a one to many relationship with authors
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function relatedAuthors()
	{
		return $this->oneToMany('Author', 'cid', 'id');
	}

	/**
	 * Defines a one to many relationship with authors
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function relatedType()
	{
		return $this->belongsToOne('Type', 'type', 'id');
	}

	/**
	 * Defines a many to many relationship with tags
	 *
	 * @return  object
	 */
	public function tags()
	{
		return $this->manyToMany('Tag', '#__tags_object', 'objectid', 'tagid');
	}

	/**
	 * Defines a one to many relationship with links
	 *
	 * @return  object
	 */
	public function links()
	{
		return $this->oneToMany('Link');
	}

	/**
	 * Format a citation
	 *
	 * @param   array   $config
	 * @param   string  $highlight
	 * @return  object
	 */
	//public function formatted($citation, $highlight = NULL, $include_coins = true, $config, $coins_only = false)
	public function formatted($config = array('format' => 'apa'), $highlight = NULL)
	{
		//get hub specific details
		$hub_name = \Config::get('sitename');
		$hub_url  = rtrim(\Request::base(), '/');

		//get scope specific details
		$coins_only = isset($config['coins_only']) ? $config['coins_only'] : "no";
		$include_coins = isset($config['include_coins']) ? $config['include_coins'] : "no";
		$c_type = 'journal';

		$type = $this->relatedType->type;

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

		// var to hold COinS data
		$coins_data = array(
			"ctx_ver=Z39.88-2004",
			"rft_val_fmt=info:ofi/fmt:kev:mtx:{$c_type}",
			"rfr_id=info:sid/{$hub_url}:{$hub_name}"
		);

		// array to hold replace vals
		$replace_values = array();

		// get the template
		// default to IEEE
		try
		{
			$format = \Components\Citations\Models\Format::oneOrFail($config['citationFormat']);
		}
		catch (\Exception $e)
		{
			$format = \Components\Citations\Models\Format::all()->where('style', 'LIKE', '%IEEE%')->row()->toObject();
		}

		// get the template keys
		$template_keys =  array(
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

		// Values used by COINs
		$coins_keys = array(
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

			// form the formatted citation
		foreach ($template_keys as $k => $v)
		{
			if (!$this->keyExistsOrIsNotEmpty($k, $this) && $k != 'author')
			{
				$replace_values[$v] = '';
			}
			else
			{
				$replace_values[$v] = $this->$k;

				//add to coins data if we can but not authors as that will get processed below
				if (in_array($k, array_keys($coins_keys)) && $k != 'author')
				{
					//key specific
					switch ($k)
					{
						case 'title':
							break;
						case 'doi':
							$coins_data[] = $this->_coins_keys[$k] . $this->$k;
							break;
						case 'url':
							$coins_data[] = $this->_coins_keys[$k] . '=' . htmlentities($this->$k);
							break;
						case 'journaltitle':
							$jt = html_entity_decode($this->$k);
							$jt = (!preg_match('!\S!u', $jt)) ? utf8_encode($jt) : $jt;
							$coins_data[] = $this->_coins_keys[$k] . '=' . $jt;
							break;
						default:
							$coins_data[] = $this->_coins_keys[$k] . '=' . $this->$k;
					}
				}

				if ($k == 'author')
				{
					$a = array();

					$auth = html_entity_decode($this->$k);
					$auth = (!preg_match('!\S!u', $auth)) ? utf8_encode($auth) : $auth;

					// prefer the use of the relational table 
					if ($this->relatedAuthors->count() > 0)
					{
						$authors = $this->relatedAuthors()->order('ordering', 'asc');
						$authorCount = $this->relatedAuthors->count();
					}
					elseif ($auth != '' && $this->relatedAuthors->count() == 0)
					{
						$author_string = $auth;
						$authors = explode(';', $author_string);
						$authorCount = count($authors);
					}
					else
					{
						$authorCount = 0;
						$replace_values[$v] = '';
					}

					if ($authorCount > 0)
					{
						foreach ($authors as $author)
						{
							// for legacy profile handling
							if (is_string($author))
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

								// add author coins
								$coins_data[] = 'rft.au=' . trim(preg_replace('/\{\{\d+\}\}/', '', trim($author)));
							} // legacy string
							elseif (is_object($author)) // new ORM method
							{
								if ($author->uidNumber > 0)
								{
									$a[] = '<a rel="external" href="' . \Route::url('index.php?option=com_members&id=' . $author->uidNumber) . '">' . $author->author . '</a>';
								}
								else
								{
									$a[] = $author->author;
								}
							} //new ORM method
							else
							{
								$a[] = $author;
							}
						}
						$replace_values[$v] = implode(", ", $a);
					}
				}

				if ($k == 'title')
				{
					$url_format = isset($config['citation_url']) ? $config['citation_url'] : 'url';
					$custom_url = isset($config['citation_custom_url']) ? $config['citation_custom_url'] : '';

					$url = $this->url;
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
								if (property_exists($this, $field))
								{
									if (strstr($this->$field, 'http'))
									{
										$custom_url = $this->$field;
									}
									else
									{
										$replaceWith = urlencode($this->$field);
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

					$t = html_entity_decode($this->$k);
					$t = (!preg_match('!\S!u', $t)) ? utf8_encode($t) : $t;

					$title = ($url != '' && preg_match('/http:|https:/', $url))
							? '<a rel="external" class="citation-title" href="' . $url . '">' . $t . '</a>'
							: '<span class="citation-title">' . $t . '</span>';

					//do we want to display single citation
					//$singleCitationView = $config('citation_single_view', 0);
					$singleCitationView = isset($config['citation_single_view']) ? $config['citation_single_view'] : 0;

					if ($singleCitationView && isset($this->id))
					{
						$title = '<a href="' . \Route::url('index.php?option=com_citations&task=view&id=' . $this->id) . '">' . $t . '</a>';
					}

					//send back title to replace title placeholder ({TITLE})
					$replace_values[$v] = '"' . $title . '"';

					//add title to coin data but fixing bad chars first
					$coins_data[] = 'rft.atitle=' . $t;
				}

				if ($k == 'pages')
				{
					$replace_values[$v] = "pg: " . $this->$k;
				}
			}
		}

		// Add more to coins

		$template = $format->format;

		$tmpl = isset($template) ? $template : $default_template;
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
		if ($include_coins == "yes"|| $coins_only == "yes")
		{
			$coins = '<span class="Z3988" title="' . $coins_data . '"></span>';
			if ($coins_only == "yes")
			{
				return $coins;
			}

			$cite .= $coins;
		}

		// output the citation
		return $cite;
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
		//$value = $row->$key;
		$value = true;
		if (isset($value))
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
	 * Citation links and badges
	 *
	 * @param   array   $openurl   Data to append
	 * @return  string
	 */
	public function citationDetails($openurl = array())
	{
		$html  = '';

		// are we allowing downloading
		$html .= '<a rel="nofollow" href="' . \Route::url('index.php?option=com_citations&task=download&id=' . $this->id . '&citationFormat=bibtex&no_html=1') . '" title="' . \Lang::txt('COM_CITATIONS_BIBTEX') . '">' . \Lang::txt('COM_CITATIONS_BIBTEX') . '</a>';
		$html .= '<span> | </span>';
		$html .= '<a rel="nofollow" href="' . \Route::url('index.php?option=com_citations&task=download&id=' . $this->id . '&citationFormat=endnote&no_html=1') . '" title="' . \Lang::txt('COM_CITATIONS_ENDNOTE') . '">' . \Lang::txt('COM_CITATIONS_ENDNOTE') . '</a>';

		// if we have an open url link and we want to use open urls
		if ($openurl['link'])
		{
			$html .= '<span> | </span>' . self::citationOpenUrl($openurl, $this);
		}

		// citation association - to HUB resources
		//$html .= $this->citationAssociation($config, $citation);

		return $html;
	}

	/**
	 * Output a tagcloud of badges associated with a citation
	 *
	 * @return  string  HTML
	 */
	public function badgeCloud()
	{
		$html = '<ul class="tags badges">';
		foreach ($this->tags as $badge)
		{
			if ($badge->tagObject->label == "badge")
			{
				$html .= '<li><a href="#" class="tag">' . stripslashes($badge['raw_tag']) . '</a></li>';
			}
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Output a tagcloud of tags associated with a citation
	 *
	 * @return  string  HTML
	 */
	public function tagCloud()
	{
		$html = '';

		$tags = clone $this->tags;

		if ($this->tags()->count() > 0)
		{
			$isAdmin = (\User::authorise('core.manage', 'com_citations') ? true : false);

			$html  = '<ol class="tags">';
			foreach ($tags as $tag)
			{
				if ($tag->tagObject->tbl == 'citations' && $tag->tagObject->label == '')
				{
					//display tag if not admin tag or if admin tag and user is adminstrator
					if (!$tag->admin || ($tag->admin && $isAdmin))
					{
						$html .= '<li' . ($tag->admin ? ' class="admin"' : '') . '><a class="tag ' . ($tag->admin ? ' admin' : '') . '" href="' . \Route::url('index.php?option=com_tags&tag=' . $tag->tag) . '">' . stripslashes($tag->raw_tag) . '</a></li>';
					}
				}
			}
			$html .= '</ol>';
		}

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
		$html  = '';
		$text  = $openurl['text'];
		$icon  = $openurl['icon'];
		$link  = $openurl['link'];
		$query = array();

		// citation type
		$citation_type = $citation->relatedType;

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
		if (isset($citation->isbn) && $citation->isbn != '')
		{
			// get the issn/isbn in db
			$issn_isbn = $citation->isbn;;

			// check to see if we need to do any special processing to the issn/isbn before outputting
			if (strstr($issn_isbn, "\r\n"))
			{
				$issn_isbn = array_filter(array_values(explode("\r\n", $issn_isbn)));
				$issn_isbn = preg_replace("/[^0-9\-]/", '', $issn_isbn[0]);
			}
			elseif (strstr($issn_isbn, ' '))
			{
				$issn_isbn = array_filter(array_values(explode(' ', $issn_isbn)));
				$issn_isbn = preg_replace("/[^0-9\-]/", '', $issn_isbn[0]);
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
			$link_text = ($icon != '') ? '<img alt="' . $text . '" src="index.php?option=com_citations&controller=citations&task=downloadimage&image=' . $icon . '" />' : $text;

			// final link
			$html .= '<a rel="external nofollow" href="' . $link . '" title="' . $text . '">' . $link_text . '</a>';
		}

		return $html;
	}
}
