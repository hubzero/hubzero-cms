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
use Hubzero\Database\Rows;
use Hubzero\Utility\Str;
use Components\Tags\Models\Tag;
use stdClass;
use Request;
use Route;
use User;

require_once __DIR__ . DS . 'association.php';
require_once __DIR__ . DS . 'author.php';
require_once __DIR__ . DS . 'format.php';
require_once __DIR__ . DS . 'link.php';
require_once __DIR__ . DS . 'secondary.php';
require_once __DIR__ . DS . 'sponsor.php';
require_once __DIR__ . DS . 'type.php';
require_once \Component::path('com_tags') . DS . 'models' . DS . 'tag.php';
require_once \Component::path('com_resources') . DS . 'models' . DS . 'entry.php';
require_once \Component::path('com_publications') . DS . 'models' . DS . 'orm' . DS . 'publication.php';

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Citation extends Relational implements \Hubzero\Search\Searchable
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'name';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'type'  => 'notempty',
		'title' => 'notempty'
	);

	/**
	 * Separate badges?
	 *
	 * @var  boolean
	 */
	private $badgesSeparated = false;

	/**
	 * Returns results based on filters applied.
	 *
	 * @param   array    $filters  the string contaning the various applied filters.
	 * @param   boolean  $admin
	 * @return  object   Citation 
	 */
	public static function getFilteredRecords($filters = array(), $admin = false)
	{
		$records = self::all();

		$recordIdField = $records->getQualifiedFieldName('id');

		$records->select($records->getQualifiedFieldName('*'))
				->select('u.username')
				->select('F.format', 'template')
				->select('CS.sec_cits_cnt', 'sec_cnt')
				->including(['relatedAuthors', function($author){
					$author->order('ordering', 'asc');
				}])
				->including('assignedFormat')
				->including('sponsors')
				->including('tags')
				->including(['resources', function($resource){
					$resource->whereEquals('#__citations_assoc.tbl', 'resource');
				}])
				->join('#__users AS u', $records->getQualifiedFieldName('uid'), 'u.id', 'left')
				->join('#__citations_secondary AS CS', $recordIdField, 'CS.cid', 'left')
				->join('#__citations_format AS F', $records->getQualifiedFieldName('format'), 'F.style', 'left');
				//->group($recordIdField);

		// scope & scope Id
		if (!empty($filters['scope']) && $filters['scope'] != 'all')
		{
			if ($filters['scope'] == 'hub')
			{
				$records->whereEquals('scope', 'hub', 1);
				$records->orWhereEquals('scope', '', 1);
				$records->orWhere('scope', 'IS', null, 1);
				$records->resetDepth();
			}
			else
			{
				$records->whereEquals('scope', $filters['scope']);
			}
		}
		elseif ($filters['scope'] != 'all')
		{
			if (empty($filters['scope_id']))
			{
				$records->whereEquals('scope_id', '', 1);
				$records->whereEquals('scope', '', 1);
				$records->resetDepth();
			}
		}

		if (!empty($filters['scope_id']))
		{
			$records->whereEquals('scope_id', $filters['scope_id']);
		}

		if (!isset($filters['published']))
		{
			$filters['published'] = array(1);
		}

		$records->whereIn('published', $filters['published']);

		if (!empty($filters['sort']))
		{
			$sortValues = explode(' ', $filters['sort']);
			$columnSort = !empty($sortValues[0]) ? $sortValues[0] : $sortValues;
			$direction = !empty($sortValues[1]) ? $sortValues[1] : 'ASC';
			$records->order($columnSort, $direction);
		}

		if (!empty($filters['filter']))
		{
			$filterValues = array('nonaff' => 0, 'aff' => 1);
			$records->whereEquals('affiliated', $filterValues[$filters['filter']]);
		}

		if (!empty($filters['type']))
		{
			$records->whereEquals('type', $filters['type']);
		}

		if (!empty($filters['tag']))
		{
			$tags = explode(',', $filters['tag']);
			$records->whereRelatedHas('tags', function($tag) use ($tags){
				$tag->select('tag', 'tagcount', true)->whereIn('tag', $tags)
					->group('objectid')
					->having('tagcount', '=', count($tags));
				return $tag;
			});
		}

		if (!empty($filters['search']))
		{
			$searchQuery = $filters['search'];
			$records->filterBySearch($searchQuery, 1);

			$records->orWhereRelatedHas('relatedAuthors', function($author) use ($searchQuery){
				$authorFields = array('givenName', 'surname', 'author');
				$query = 'MATCH(' . implode(',', $authorFields) . ') AGAINST (? IN BOOLEAN MODE)';
				$author->whereRaw($query, (array) $searchQuery);
				return $author;
			}, 1);
			$records->resetDepth();
		}

		if (!empty($filters['publishedin']))
		{
			$records->whereLike('booktitle', $filters['publishedin'], 1);
			$records->orWhereLike('journal', $filters['publishedin'], 1);
			$records->resetDepth();
		}

		if (!empty($filters['year_start']))
		{
			$records->where('year', '>=', (int)$filters['year_start']);
		}
		if (!empty($filters['year_end']))
		{
			$records->where('year', '<=', (int)$filters['year_end']);
		}

		$authorFields = array_intersect_key($filters, array('author' => null, 'geo' => '', 'aff' => ''));
		$maxOptions = array('geo' => 4, 'aff' => 3);
		$authorFields = array_filter($authorFields);
		foreach ($maxOptions as $filter => $max)
		{
			if (isset($authorFields[$filter]))
			{
				$selectedValues = array_filter($authorFields[$filter], function($item){
					if ($item == 1)
					{
						return $item;
					}
				});
				if (count($selectedValues) == $max)
				{
					unset($authorFields[$filter]);
				}
			}
		}
		if (!empty($authorFields))
		{
			$records->whereRelatedHas('relatedAuthors', function($author) use ($authorFields){
				foreach ($authorFields as $field => $value)
				{
					$func = 'filterBy' . ucfirst($field);
					if (method_exists($author, $func))
					{
						$author->$func($value);
					}
				}
				return $author;
			}, 1);
		}
		$records->filterByReftype($filters);

		return $records;
	}

	/**
	 * Applies filters based on search tearm provided.
	 *
	 * @param   mixed  $term   string containing search term or an associative array containing multiple search terms for multiple columns.
	 * @param   int    $depth  value indicating if the produced query string should be nested in parenthesis with other querys of the same depth.
	 * @param   array  $searchableFields
	 * @return  $this
	 */
	public function filterBySearch($term, $depth = 0, $searchableFields = array('title', 'isbn', 'doi', 'abstract', 'author', 'publisher'))
	{
		if (is_array($term))
		{
			$searchTerms = array_intersect_key($term, array_flip($searchableFields));
			$term = '';
			$term = array_reduce($searchTerms, function($concat, $item){
				$concat .= $item . ' ';
				return $concat;
			});
			$query = 'MATCH(' . implode(',', array_keys($searchTerms)) . ') AGAINST (? IN BOOLEAN MODE)';
		}
		else
		{
			$query = 'MATCH(' . implode(',', $searchableFields) . ') AGAINST (? IN BOOLEAN MODE)';
		}
		$this->whereRaw($query, (array) $term, $depth);
		return $this;
	}

	/**
	 * Applies filters based on reference type included in the applied filters.
	 *
	 * @param   array  $filters  contains all the values included for filtering the results returned
	 * @return  $this
	 */
	public function filterByReftype($filters)
	{
		$refTypes = array(
			'research' => array('R', 'N', 'S'),
			'education' => array('E'),
			'cyberinfrastructure' => array('C', 'A', 'HD', 'I'),
			'eduresearch' => array('research', 'education')
		);

		if (isset($filters['reftype']) && is_array($filters['reftype']))
		{
			$filters['reftype'] = array_filter($filters['reftype'], function($ref){
				if ($ref == 1)
				{
					return $ref;
				}
			});

			if (!empty($filters['reftype']) && count($filters['reftype']) < 4)
			{
				$refKeys = array_keys($filters['reftype']);
				$firstQuery = $refKeys[0];
				$query = '(';
				$queryBindings = array();
				foreach ($refKeys as $reftype)
				{
					$combination = false;
					$excludes = array();
					switch ($reftype)
					{
						case 'research':
							$excludes = $refTypes['education'];
							break;
						case 'cyberinfrastructure':
							$excludes = array_merge($refTypes['research'], $refTypes['education']);
						case 'education':
							$excludes = $refTypes['research'];
							break;
						case 'eduresearch':
							$combination = true;
							break;
					}
					$query .= $firstQuery == $reftype ? '((' : ' OR ((';

					if ($combination === false)
					{
						$valueCount = count($refTypes[$reftype]);
						$queryBindings = array_merge($queryBindings, $refTypes[$reftype]);
						for ($i = 1; $i <= $valueCount - 1; $i++)
						{
							$query .= " `ref_type` LIKE ? OR ";
						}
						$query .= " `ref_type` LIKE ?) ";
					}
					else
					{
						$comboType1 = $refTypes[$reftype][0];
						$comboType2 = $refTypes[$reftype][1];
						$firstValue = true;
						foreach ($refTypes[$comboType1] as $type1)
						{
							foreach ($refTypes[$comboType2] as $type2)
							{
								$queryBindings[] = $type1;
								$queryBindings[] = $type2;
								$query .= $firstValue ? '' : ' OR ';
								$query .= "(`ref_type` LIKE ? AND `ref_type` LIKE ?)";
								$firstValue = false;
							}
						}
						$query .= ")";
					}

					if (!empty($excludes))
					{
						$excludesCount = count($excludes);
						$queryBindings = array_merge($queryBindings, $excludes);
						for ($i = 1; $i <= $excludesCount; $i++)
						{
							$query .= "AND `ref_type` NOT LIKE ? ";
						}
					}
					$query .= ")";
				}
				$query .= ')';
				$queryBindings = array_map(function($value){
					return '%' . $value . '%';
				}, $queryBindings);
				$this->whereRaw($query, $queryBindings);
			}
		}
		return $this;
	}

	/**
	 * Join association data
	 *
	 * @return  object
	 */
	public function isOwner()
	{
		$this->select('id', 'citation_id')->from('#__citations_assoc');
		return $this;
	}

	/**
	 * Sorts resulted citations sorted by year and then separated by affiliated/non-affilated.
	 *
	 * @param array $filters contains all the values included for filtering the results returned
	 * @return Citation object
	 */
	public static function getYearlyStats($filters = array())
	{
		$publishState = empty($filters['published']) ? array(1) : $filters['published'];
		$publishState = !is_array($publishState) ? array($publishState) : $publishState;
		$scope = empty($filters['scope']) ? 'hub' : $filters['scope'];

		$citations = self::all()
			->select('affiliated')
			->select('year')
			->select('id', 'totalcite', true)
			->whereIn('published', $publishState)
			->group('year')
			->group('affiliated')
			->order('year', 'desc');

		if ($scope == 'hub')
		{
			$citations->whereEquals('scope', '', 1)
				->orWhere('scope', 'IS', null, 1)
				->orWhereEquals('scope', $scope, 1)
				->resetDepth();
		}
		elseif ($scope != 'all' && !empty($filters['scope_id']))
		{
			$citations->whereEquals('scope', $scope)
				->whereEquals('scope_id', $filters['scope_id']);
		}

		$earliestYear = self::blank()
			->select('year')
			->where('year', '!=', '')
			->where('year', 'IS NOT', null)
			->where('year', '>', 0)
			->order('year', 'asc')
			->limit(1)
			->row()
			->get('year');

		$groupCitations = array();
		$affiliations = array('non-affiliate' => 0, 'affiliate' => 0);
		$affiliationLabels = array_keys($affiliations);

		for ($i = date('Y'); $i >= $earliestYear; $i--)
		{
			$groupCitations[$i] = $affiliations;
		}
		$emptyLabel = 'No Year';
		$groupCitations[$emptyLabel] = $affiliations;

		foreach ($citations->rows() as $cite)
		{
			$year = $cite->year;
			$year = !empty($year) && ($year != "0") ? $year : $emptyLabel;
			$affNum = (int) $cite->affiliated;
			$affLabel = (isset($affiliationLabels[$affNum]) ? $affiliationLabels[$affNum] : '');

			if (!isset($groupCitations[$year]))
			{
				$groupCitations[$year] = $affiliations;
			}

			// Set count for affiliation
			if ($year == $emptyLabel)
			{
				$groupCitations[$emptyLabel][$affLabel] += $cite->totalcite;
			}
			else
			{
				$groupCitations[$year][$affLabel] = $cite->totalcite;
			}
		}

		return $groupCitations;
	}

	/**
	 * Defines a belongs to one relationship with format
	 *
	 * @return  object
	 */
	public function assignedFormat()
	{
		return $this->belongsToOne('Format', 'format', 'style');
	}

	/**
	 * Defines a many to many relationship with resources
	 *
	 * @return  object
	 */
	public function resources()
	{
		return $this->manyToMany('\Components\Resources\Models\Entry', '#__citations_assoc', 'cid', 'oid');
	}

	/**
	 * Defines a one to many relationship with associations
	 *
	 * @return  object
	 */
	public function associations()
	{
		return $this->oneToMany('Association', 'cid');
	}

	/**
	 * Defines a many to many relationship with publications
	 *
	 * @return  object
	 */
	public function publications()
	{
		return $this->manytoMany('\Components\Publications\Models\Orm\Publication', '#__citations_assoc', 'cid', 'oid')
			->whereEquals('#__citations_assoc.tbl', 'publication');
	}

	/**
	 * Check if a user can edit
	 *
	 * @param   integer  $userId
	 * @return  bool
	 */
	public function canEdit($userId = null)
	{
		$owners = $this->publications()
			->select('owners.userid')
			->join('#__project_owners as owners', '#__publications.project_id', 'owners.projectid');

		$ownerIds = array();
		foreach ($owners->rows() as $owner)
		{
			$ownerIds[] = $owner->userid;
		}
		$ownerIds = array_filter($ownerIds);
		if (!isset($userId))
		{
			$userId = User::get('id');
		}

		if (in_array($userId, $ownerIds))
		{
			return true;
		}
		return false;
	}

	/**
	 * Defines a many to many relationship with sponsors
	 *
	 * @return  object
	 */
	public function sponsors()
	{
		return $this->manyToMany('Sponsor', '#__citations_sponsors_assoc', 'cid', 'sid');
	}

	/**
	 * Defines a one to many relationship with authors
	 *
	 * @return  object
	 */
	public function relatedAuthors()
	{
		return $this->oneToMany('Author', 'cid');
	}

	/**
	 * Defines a one to many relationship with authors
	 *
	 * @return  object
	 */
	public function relatedType()
	{
		return $this->belongsToOne('Type', 'type', 'id');
	}

	/**
	 * Defines a one to many relationship with secondary citations
	 *
	 * @return  object
	 */
	public function secondaries()
	{
		return $this->oneToMany('Secondary', 'cid');
	}

	/**
	 * Get the model name
	 *
	 * @return  string
	 */
	public function getModelName()
	{
		$this->modelName = 'citations';
		return $this->modelName;
	}

	/**
	 * Defines a many to many relationship with tags
	 *
	 * @return  object
	 */
	public function tags()
	{
		return $this->manyShiftsToMany('\Components\Tags\Models\Tag', '#__tags_object', 'objectid', 'tbl', 'tagid');
	}

	/**
	 * Update tags
	 *
	 * @param   string   $tags
	 * @param   string   $label
	 * @param   itneger  $strength
	 * @return  void
	 */
	public function updateTags($tags, $label= '', $strength = 1)
	{
		$currentTags = $this->tags()->whereEquals('jos_tags_object.label', $label)->rows();
		$currentTagKeys = array();
		foreach ($currentTags as $obj)
		{
			$currentTagKeys[] = $obj->get('tag');
		}

		$tags = !is_array($tags) ? explode(',', $tags) : $tags;
		$removeTags = array_diff($currentTagKeys, $tags);
		$addTags = array_diff($tags, $currentTagKeys);
		foreach ($addTags as $tag)
		{
			$newTag = Tag::oneByTag($tag);
			$newTag->set('raw_tag', $tag);
			if ($newTag->isNew())
			{
				$newTag->save();
			}
			$newTag->addTo('citations', $this->get('id'), User::get('id'), $strength, $label);
		}

		foreach ($currentTags as $tag)
		{
			$tagName = $tag->get('tag');
			if (in_array($tagName, $removeTags))
			{
				$tag->removeFrom('citations', $this->get('id'));
			}
		}
	}

	/**
	 * Separate tags and badges
	 *
	 * @return  object
	 */
	public function separateTagsAndBadges()
	{
		if (!$this->badgesSeparated)
		{
			if ($this->tags->count() > 0)
			{
				$newTags = new Rows();
				if (!$badges = $this->get('badges'))
				{
					$badges = new Rows();
				}
				if (!$this->tags()->rows()->first()->hasAttribute('associative_label'))
				{
					$this->tags = $this->tags()->select('#__tags_object.label', 'associative_label')->rows();
				}

				foreach ($this->tags as $index => $obj)
				{
					if ($obj->associative_label == 'badge')
					{
						$badges->push($obj);
					}
					else
					{
						$newTags->push($obj);
					}
				}
				$this->set('filteredTags', $newTags);
				$this->set('badges', $badges);
			}
			$this->badgesSeparated = true;
		}
		return $this;
	}

	/**
	 * Defines a one to many relationship with links
	 *
	 * @return  object
	 */
	public function links()
	{
		return $this->oneToMany('Link', 'citation_id');
	}

	/**
	 * Format a citation
	 *
	 * @param   array   $config
	 * @param   string  $highlight
	 * @return  object
	 */
	public function formatted($config = array('format' => 'apa'), $highlight = null)
	{
		if ($this->isNew())
		{
			return '';
		}
		if (!empty($this->get('formatted')))
		{
			return $this->get('formatted');
		}
		//get hub specific details
		$hub_name = \Config::get('sitename');
		$hub_url  = rtrim(Request::base(), '/');

		//get scope specific details
		$coins_only = isset($config['coins_only']) ? $config['coins_only'] : "no";
		$include_coins = isset($config['include_coins']) ? $config['include_coins'] : "no";
		$c_type = 'journal';

		$type = 'book';

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

		$template = $this->template;

		// get the default template
		if (empty($template))
		{
			$template = $this->assignedFormat->get('format');
			if (empty($template))
			{
				if (isset($config['format']) && $config['format'] instanceof Format)
				{
					$template = $config['format']->format;
				}
				else
				{
					$template = Format::getDefault()->format;
				}
			}
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
					$authors = $this->relatedAuthors()
						->order('ordering', 'asc')
						->order('id', 'asc')
						->rows();
					$authorCount = $authors->count();
					if ($authorCount > 0)
					{
						//$authors = $this->relatedAuthors;
						//$authorCount = $this->relatedAuthors->count();
					}
					elseif ($auth != '' && $authorCount == 0)
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
										$user = User::getInstance($id);
										if (is_object($user))
										{
											$a[] = '<a rel="external" href="' . Route::url('index.php?option=com_members&id=' . $matches[1]) . '">' . str_replace($matches[0], '', $author) . '</a>';
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
									$a[] = '<a rel="external" href="' . Route::url('index.php?option=com_members&id=' . $author->uidNumber) . '">' . $author->author . '</a>';
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
						$replace_values[$v] = implode(", ", array_filter($a));
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
					elseif (strstr($url, ' '))
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
						$title = '<a href="' . Route::url('index.php?option=com_citations&task=view&id=' . $this->id) . '">' . $t . '</a>';
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
			'","' => '',
			'doi:.' => '',
			'(DOI:).' => '',
			'(DOI: )' => ''
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
		// do before appending coins as we dont want that data accidentally highlighted (causes style issues)
		$cite = ($highlight) ? Str::highlight($cite, $highlight) : $cite;

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
		$details  = '';

		// are we allowing downloading
		$details .= '<a class="icon-download bibtex" rel="nofollow" href="' . Route::url('index.php?option=com_citations&task=download&id=' . $this->id . '&citationFormat=bibtex&no_html=1') . '" title="' . \Lang::txt('COM_CITATIONS_BIBTEX') . '">' . \Lang::txt('COM_CITATIONS_BIBTEX') . '</a>';
		$details .= '<span class="separator"> | </span>';
		$details .= '<a class="icon-download endnote" rel="nofollow" href="' . Route::url('index.php?option=com_citations&task=download&id=' . $this->id . '&citationFormat=endnote&no_html=1') . '" title="' . \Lang::txt('COM_CITATIONS_ENDNOTE') . '">' . \Lang::txt('COM_CITATIONS_ENDNOTE') . '</a>';

		// if we have an open url link and we want to use open urls
		if ($openurl['link'])
		{
			if ($open = self::citationOpenUrl($openurl, $this))
			{
				$details .= '<span class="separator"> | </span>' . $open;
			}
		}

		// citation association - to HUB resources
		$details .= $this->formattedResourceLinks();

		if ($this->eprint)
		{
			$details .= '<span>|</span>';
			$details .= '<a href="' . Str::ampReplace($this->eprint) . '">' . \Lang::txt('Electronic Paper') . '</a>';
		}

		return $details;
	}

	/**
	 * Format resource links
	 *
	 * @return  mixed
	 */
	public function formattedResourceLinks()
	{
		if (!$this->resources)
		{
			return null;
		}
		else
		{
			$resourceCount = count($this->resources);
			if ($resourceCount > 0)
			{
				$config = \Component::params('com_citations');
				$internallyCitedImage = $config->get('citation_cited', 0);
				$internallyCitedImageSingle = $config->get('citation_cited_single', '');
				$internallyCitedImageMultiple = $config->get('citation_cited_multiple', '');
				$image = '';
				$label = '';
				$links = '';
				$count = 1;
				$multiple = false;

				if ($resourceCount > 1)
				{
					$links .= '<span>|</span><span style="line-height:1.6em;color:#444">' . \Lang::txt('COM_CITATIONS_RESOURCES_CITED') . ':</span>';
					$multiple = true;
				}
				else
				{
					$links .= '<span>|</span>';
				}
				$imageSrc = $multiple ? (!empty($internallyCitedImageMultiple)) ? $internallyCitedImageMultiple : $internallyCitedImageSingle : $internallyCitedImageSingle;

				$linkText = \Lang::txt('COM_CITATIONS_RESOURCES_CITED');
				$linkImage = '<img src="' . $imageSrc . '" />';

				$displayValue = ($internallyCitedImage) ? 'linkImage' : 'linkText';
				foreach ($this->resources as $resource)
				{
					if ($multiple)
					{
						$linkText = '[' . $count . ']';
					}
					$links .= '<a href="' . $resource->link() . '">' . $$displayValue . '</a>';
					$count++;
				}
				return $links;
			}
		}
	}

	/**
	 * Output a tagcloud of badges associated with a citation
	 *
	 * @return  string  HTML
	 */
	public function badgeCloud()
	{
		$html = '<ul class="tags badges">';
		foreach ($this->tags() as $badge)
		{
			if ($badge->label == "badge" && $badge->tbl = 'citations')
			{
				$html .= '<li><a href="#" class="tag">' . stripslashes($badge->tag->tag) . '</a></li>';
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

		$tags = clone $this->tags();

		if ($this->tags()->count() > 0)
		{
			$isAdmin = (User::authorise('core.manage', 'com_citations') ? true : false);

			$html  = '<ol class="tags">';
			foreach ($tags as $tag)
			{
				if ($tag->tbl == 'citations' && $tag->label == '')
				{
					//display tag if not admin tag or if admin tag and user is administrator
					if (!$tag->tag->admin || ($tag->tag->admin && $isAdmin))
					{
						$html .= '<li' . ($tag->tag->admin ? ' class="admin"' : '') . '><a class="tag ' . ($tag->tag->admin ? ' admin' : '') . '" href="' . Route::url('index.php?option=com_tags&tag=' . $tag->tag->tag) . '">' . stripslashes($tag->tag->raw_tag) . '</a></li>';
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
		$citation_type = $citation->relatedType()->get('type');

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
			$issn_isbn = $citation->isbn;

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

	/**
	 * Save record and propagate data
	 *
	 * @return  bool
	 */
	public function saveAndPropagate()
	{
		if (!$this->save())
		{
			return false;
		}

		// Loop through the relationships and save
		// Both rows and models know how to save, so it doesn't matter
		// which of the two the particular relationship returned
		foreach ($this->getRelationships() as $relationship => $rows)
		{
			$this->$relationship()->associate($rows);
			if (!$rows->save())
			{
				$this->setErrors($rows->getErrors());
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove record
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		$relatedAuthors = $this->relatedAuthors;
		if (!$relatedAuthors->destroyAll())
		{
			$this->setErrors($relatedAuthors->getErrors());
			return false;
		}

		$associations = $this->associations;
		if (!$associations->destroyAll())
		{
			$this->setErrors($associations->getErrors());
			return false;
		}

		$secondaries = $this->secondaries;
		if (!$secondaries->destroyAll())
		{
			$this->setErrors($secondaries->getErrors());
			return false;
		}

		if (!parent::destroy())
		{
			return false;
		}

		$this->sponsors()->sync(array());
		$this->updateTags(array());
		$this->updateTags(array(), 'badge');

		return true;
	}

	/**
	 * Output a list of authors
	 *
	 * @param   boolean  $includeMemberId  if true, include member ID next to author name
	 * @return  string
	 */
	public function getAuthorString($includeMemberId = true)
	{
		if ($this->isNew() && !empty($this->tempId))
		{
			$this->set('id', $this->tempId);
		}

		$authors = $this->relatedAuthors()
			->order('ordering', 'ASC')
			->rows();

		if ($authors->count())
		{
			$convertedAuthors = array();
			foreach ($authors as $author)
			{
				$lastName   = $author->get('surname', '');
				$firstName  = $author->get('givenName', '');
				$middleName = $author->get('middleName', '');
				$memberId   = $author->get('uidNumber', '');

				$authorText  = $lastName;
				$authorText .= ', ' . $firstName;
				$authorText .= !empty($firstName) && !empty($middleName) ? ' ' . $middleName : '';

				if ($includeMemberId)
				{
					$authorText .= !empty($memberId) ? '{{' . $memberId . '}}' : '';
				}

				if (empty(trim($authorText)))
				{
					continue;
				}

				$convertedAuthors[] = $authorText;
			}

			if ($this->isNew() && !empty($this->tempId))
			{
				$this->removeAttribute('id');
			}

			$str = implode(';', $convertedAuthors);
		}
		else
		{
			$str = $this->get('author');
		}

		return $str;
	}

	/**
	 * Namespace used for search
	 *
	 * @return  string
	 */
	public static function searchNamespace()
	{
		$searchNamespace = 'citation';
		return $searchNamespace;
	}

	/**
	 * Generate search Id
	 *
	 * @return  string
	 */
	public function searchId()
	{
		$searchId = self::searchNamespace() . '-' . $this->id;
		return $searchId;
	}

	/**
	 * Generate search document for search
	 *
	 * @return  array
	 */
	public function searchResult()
	{
		$citation = new stdClass;
		$citation->title = $this->title;
		$citation->hubtype = self::searchNamespace();
		$citation->id = $this->searchId();
		$citation->description = $this->abstract;
		$citation->doi = $this->doi;
		$tags = explode(',', $this->keywords);
		foreach ($tags as $key => &$tag)
		{
			$tag = \Hubzero\Utility\Sanitize::stripAll($tag);
			if ($tag == '')
			{
				unset($tags[$key]);
			}
		}
		$citation->tags = $tags;

		$citation->author = explode(';', $this->getAuthorString(false));
		if ($this->scope == 'member')
		{
			$url = '/members/' . $this->uid . '/citations';
		}
		elseif ($this->scope != 'group')
		{
			$url = '/citations/view/' . $this->id;
		}

		if ($this->published == 1 && $this->scope != 'group')
		{
			if ($this->scope == 'member')
			{
				$user = User::getInstance($this->uid);
				if ($user->get('blocked') == 0 && $user->get('approved') > 0)
				{
					if ($user->get('access') == 1)
					{
						$citation->access_level = 'public';
					}
					elseif ($user->get('access') == 2)
					{
						$citation->access_level = 'registered';
					}
					else
					{
						$citation->access_level = 'private';
					}
				}
				else
				{
					$citation->access_level = 'private';
				}
			}
			else
			{
				$citation->access_level = 'public';
			}
		}
		elseif ($this->scope == 'group')
		{
			$group = \Hubzero\User\Group::getInstance($this->scope_id);
			if ($group)
			{
				$groupName = $group->get('cn');
				$url = '/groups/' . $groupName . '/citations';
				$citationAccess = \Hubzero\User\Group\Helper::getPluginAccess($group, 'citations');
				if ($citationAccess == 'anyone')
				{
					$citation->access_level = 'public';
				}
				elseif ($citationAccess == 'registered')
				{
					$citation->access_level = 'registered';
				}
				else
				{
					$citation->access_level = 'private';
					$citation->owner_type = 'group';
					$citation->owner = $this->scope_id;
				}
			}
		}
		else
		{
			$citation->access_level = 'private';
			$citation->owner_type = 'user';
			$citation->owner = $this->uid;
		}
		$citation->url = rtrim(Request::root(), '/') . Route::urlForClient('site', $url);
		return $citation;
	}

	/**
	 * Get total number of records that will be indexed by search.
	 *
	 * @return integer
	 */
	public static function searchTotal()
	{
		$total = self::all()->total();
		return $total;
	}

	/**
	 * Get records to be included in search index
	 *
	 * @param   integer  $limit
	 * @param   integer  $offset
	 * @return  object   Hubzero\Database\Rows
	 */
	public static function searchResults($limit, $offset = 0)
	{
		return self::all()->start($offset)->limit($limit)->rows();
	}
}
