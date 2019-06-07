<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;

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
		'type_title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'type'
	);

	/**
	 * Generates automatic type field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticType($data)
	{
		$alias = (isset($data['type']) && $data['type'] ? $data['type'] : $data['type_title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 250)
		{
			$alias = substr($alias . ' ', 0, 250);
			$alias = substr($alias, 0, strrpos($alias, ' '));
		}
		$alias = str_replace(' ', '_', $alias);

		return preg_replace("/[^a-zA-Z0-9_]/", '', strtolower($alias));
	}

	/**
	 * Defines a one to Many relationship with citation
	 *
	 * @return $this
	 **/
	public function citations()
	{
		return $this->oneToMany('Citation', 'type');
	}

	/**
	 * Defines a one to Many relationship with citation
	 *
	 * @param   array  $filters
	 * @return  array
	 **/
	public static function getCitationsCountByType($filters = array())
	{
		$types = self::all()
			->including(['citations', function($citation) use ($filters){
				$publishState = empty($filters['published']) ? array(1) : $filters['published'];
				$publishState = !is_array($publishState) ? array($publishState) : $publishState;
				$scope = empty($filters['scope']) ? 'hub' : $filters['scope'];
				$citation->select('*, count(type) as totalcite')
						 ->whereIn('published', $publishState)
						 ->group('type')
						 ->group('id');
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
