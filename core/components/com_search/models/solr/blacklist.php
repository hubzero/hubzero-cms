<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr;

use Hubzero\Database\Relational;

/**
 * Database model for search blacklist
 *
 * @uses  \Hubzero\Database\Relational
 */
class Blacklist extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'search';

	/**
	 * The table name 
	 *
	 * @var  string
	 **/
	protected $table = '#__search_blacklist';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 **/
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'doc_id' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $initiate = array(
		'created_by',
		'created'
	);

	/**
	 * Get all doc_ids that are prefixed by $scope
	 * @param 	string 	$scope 	name of prefix to filter doc_ids on
	 * @return	array	collection of doc_ids
	 **/
	public static function getDocIdsByScope($scope)
	{
		$blackListIds = self::all()->select('doc_id')
			->where('doc_id', 'LIKE', $scope . '%')
			->rows()
			->fieldsByKey('doc_id');
		return $blackListIds;
	}
}
