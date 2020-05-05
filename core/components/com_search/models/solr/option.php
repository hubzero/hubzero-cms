<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Solr;

use Hubzero\Database\Relational;

/**
 * Database model for search blacklist
 *
 * @uses  \Hubzero\Database\Relational
 */
class Option extends Relational
{
	/**
	 * Table name
	 * 
	 * @var  string
	 */
	protected $table = '#__solr_search_filter_options';

	/**
	 * Automatic fields to populate every time a row is updated
	 *
	 * @var  array
	 */
	public $always = array(
		'modified',
		'modified_by'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * children 
	 * @return  object
	 */
	public function filter()
	{
		return $this->belongsToOne('Filter');
	}

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		$data['modified'] = Date::of()->toSql();
		return $data['modified'];
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModifiedBy($data)
	{
		$data['modified_by'] = User::getInstance()->get('id');
		return $data['modified_by'];
	}
}
