<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author	Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since	 Class available since release 1.3.2
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Base\Object;

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
	// table name jos_citations

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
		'type'	=> 'notempty',
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

	public function tags()
	{
		return $this->manyToMany('Tag', '#__tags_object', 'objectid', 'tagid');
	}

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

	    //var to hold COinS data
	    $coins_data = array(
	        "ctx_ver=Z39.88-2004",
	        "rft_val_fmt=info:ofi/fmt:kev:mtx:{$c_type}",
	        "rfr_id=info:sid/{$hub_url}:{$hub_name}"
	    );

	    //array to hold replace vals
	    $replace_values = array();

		// get the template
		$format = \Components\Citations\Models\Format::oneOrFail($config['citationFormat']);
		$template = $format->format;

		//get the template keys
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
		/**
		 * Values used by COINs
		*
		* @var  array
		*/
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

	 /**
		* Default formats
		*
		* @var  array
		*/
	$default_format = array(
		 'apa'  => "{AUTHORS}, {EDITORS} ({YEAR}), {TITLE/CHAPTER}, <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER}, {SERIES}, {PUBLISHER}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b>: {PAGES}, {ORGANIZATION}, {INSTITUTION}, {SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}, (DOI: {DOI}). Cited by: <a href='{SECONDARY LINK}'>{SECONDARY COUNT}</a>",
		 'ieee' => "{AUTHORS}, {EDITORS} ({YEAR}), {TITLE/CHAPTER}, <i>{JOURNAL}</i>, <i>{BOOK TITLE}</i>, {EDITION}, {CHAPTER}, {SERIES}, {PUBLISHER}, {ADDRESS}, <b>{VOLUME}</b>, <b>{ISSUE/NUMBER}</b>: {PAGES}, {ORGANIZATION}, {INSTITUTION}, {SCHOOL}, {LOCATION}, {MONTH}, {ISBN/ISSN}, (DOI: {DOI})"
	 );
			// form the formatted citation
	    foreach ($template_keys as $k => $v)
	    {
	        if (!$this->keyExistsOrIsNotEmpty($k, $this))
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
		$html .= '<a rel="nofollow" href="' . \Route::url('index.php?option=com_citations&task=download&id=' . $this->id . '&format=bibtex&no_html=1') . '" title="' . \Lang::txt('COM_CITATIONS_BIBTEX') . '">' . \Lang::txt('COM_CITATIONS_BIBTEX') . '</a>';
		$html .= '<span> | </span>';
		$html .= '<a rel="nofollow" href="' . \Route::url('index.php?option=com_citations&task=download&id=' . $this->id . '&format=endnote&no_html=1') . '" title="' . \Lang::txt('COM_CITATIONS_ENDNOTE') . '">' . \Lang::txt('COM_CITATIONS_ENDNOTE') . '</a>';

		// if we have an open url link and we want to use open urls
		if ($openurl['link'])
		{
			$html .= '<span> | </span>' . self::citationOpenUrl($openurl, $citation);
		}

		// citation association - to HUB resources
	//	$html .= $this->citationAssociation($config, $citation);

		return $html;
	}

	/**
	 * Output a tagcloud of badges associated with a citation
	 *
	 * @param   object  $citation  Citation
	 * @param   object  $database  JDatabase
	 * @return  string  HTML
	 */
	public static function badgeCloud()
	{
		$html = '';

		$html = '<ul class="tags badges">';
		foreach ($this->tags as $badge)
		{
			if ($tga->tagObject->label == "badge")
			{
				$html .= '<li><a href="#">' . stripslashes($badge['raw_tag']) . '</a></li>';
			}
		}

		$html .= "</ul>";

		return $html;
	}


	/**
	 * Output a tagcloud of tags associated with a citation
	 *
	 * @param   object  $citation  Citation
	 * @param   object  $database  JDatabase
	 * @return  string  HTML
	 */
	public function tagCloud()
	{
		if ($this->tags()->count() > 0)
		{
			$isAdmin = (\User::authorise('core.manage', 'com_citations') ? true : false);

			$html  = '<ul class="tags">';
			foreach ($this->tags as $tag)
			{
				if ($tag->tagObject->label == NULL)
				{

					$cls = ($tag->admin) ? 'admin' : '';

					//display tag if not admin tag or if admin tag and user is adminstrator
					if (!$tag->admin || ($tag->admin && $isAdmin))
					{
						$html .= '<li class="' . $cls . '"><a href="' . \Route::url('index.php?option=com_tags&tag=' . $tag->tag) . '">' . stripslashes($tag->raw_tag) . '</a></li>';
					}
				}
			}
			$html .= '</ul>';
			return $html;
		}
	}


} //end Citation Class
