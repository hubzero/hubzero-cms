<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models;

use Hubzero\Database\Relational;

/**
 * User profile model
 */
class Collaborator extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $table = '#__publication_collaborators';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'id' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'id'
	);
	
	/**
	 * Generates automatic id field value
	 *
	 * @return  string
	 */
	public function automaticId()
	{
		
	}

	/**
	 * Get collaborator by name
	 *
	 * @param   string   $name
	 *
	 * @return  object
	 */
	public static function oneByName($name)
	{
		return self::all()
			->whereEquals('name', $name)
			->row();
	}
}
