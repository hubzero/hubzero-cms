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
use Hubzero\Geocode\Geocode;
use Hubzero\Base\Object;

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Author extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'citations';
	// table name jos_citations

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'author';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		//'name'	=> 'notempty',
		//'liaison' => 'notempty'
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
	 * Defines the inverse relationship between a record and a task
	 *
	 * @return \Hubzero\Database\Relationship\belongsToOne
	 * @author
	 **/
	public function citation()
	{
		return $this->belongsToOne('Citation', 'cid', 'id');
	}

	public function filterByAuthor($authorString)
	{
		if (!empty($authorString))
		{
			$this->orWhereLike('author', $authorString, 1)
				   ->orWhereLike('givenName', $authorString, 1)
				   ->orWhereLike('surname', $authorString, 1);
		}
		return $this;
	}

	public function filterByGeo($geoCodes, $totalOptions = 4)
	{
		$geoFields = array_filter($geoCodes, function($geo){
			if ($geo == 1)
			{
				return $geo;
			}
		});

		if (count($geoFields) < $totalOptions)
		{
			$geoKeys = array_keys($geoFields);
			$countryCodes = array();
			foreach ($geoKeys as $continent)
			{
				if ($continent == 'us')
				{
					$countryCodes[] = 'us';
				}
				else
				{
					$countries = array();
					$countries = Geocode::getCountriesByContinent($continent);
					$countryCodes = array_merge($countryCodes, $countries);
				}
			}
			$this->whereIn('countryresident', $countryCodes);
		}
		return $this;
	}

	public function filterByAff($types, $totalOptions = 3)
	{
		$types = array_filter($types, function($org){
			if ($org == 1)
			{
				return $org;
			}
		});

		if (count($types) < $totalOptions)
		{
			$firstQuery = true;
			$orgtypes = array_keys($types);
			foreach ($orgtypes as $type)
			{
				$whereFunc = $firstQuery ? 'whereLike' : 'orWhereLike';
				$this->$whereFunc('orgtype', $type, 1);
				if ($type == 'university')
				{
					$this->orWhereLike('orgtype', 'education', 1);
				}
				$firstQuery = false;
			}
		}
		return $this;
	}
}
