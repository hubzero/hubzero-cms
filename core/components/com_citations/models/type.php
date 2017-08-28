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

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Type extends Relational
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
	public $orderBy = 'name';

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
	 * Defines a one to Many relationship with citation
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function citations()
	{
		return $this->oneToMany('Citation', 'type');
	}

	public static function getCitationsCountByType($filters = array())
	{
		$types = self::all()
			->including(['citations', function($citation) use ($filters){
				$publishState = empty($filters['published']) ? array(1) : $filters['published'];
				$publishState = !is_array($publishState) ? array($publishState) : $publishState;
				$scope = empty($filters['scope']) ? 'hub' : $filters['scope'];
				$citation->select('*, count(type) as totalcite')
						 ->whereIn('published', $publishState)
						 ->group('type');
				if (strtolower($scope) == 'hub')
				{
					$citation->whereEquals('scope', '', 1);
					$citation->orWhere('scope', 'IS', null, 1);
					$citation->orWhereEquals('scope', 'hub', 1);
					$citation->resetDepth();
				}
				elseif ($scope != 'all' && !empty($filters['scope_id']))
				{
					$citation->whereEquals('scope', $scope);
					$citation->whereEquals('scope_id', $filters['scope_id']);
				}
			}])->rows();
		$typeStats = array();
		foreach ($types as $type)
		{
			$typeTitle = $type->type_title;
			foreach ($type->citations as $citation)
			{
				$typeStats[$typeTitle] = $citation->totalcite;
			}

			if (!isset($typeStats[$typeTitle]))
			{
				$typeStats[$typeTitle] = 0;
			}
		}
		return $typeStats;
	}
}
