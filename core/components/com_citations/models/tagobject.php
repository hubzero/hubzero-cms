<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;

/**
 * Tag (for citations) database model
 *
 * @uses \Hubzero\Database\Relational
 */
class TagObject extends Relational
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
	 * Override the table
	 *
	 * @var string
	 **/
	protected $table = '#__tags_object';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		//'name'    => 'notempty',
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
	 * Defines a one to one relationship with citation
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function tag()
	{
		return $this->belongsToOne('Tag', 'tagid', 'id');
	}
}
