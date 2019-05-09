<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Model class for publication rating
 */
class Rating extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'publication';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'rating' => 'positive|nonzero',
		'publication_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
	);

	/**
	 * Establish relationship to parent publication
	 *
	 * @return  object
	 */
	public function publiation()
	{
		return $this->belongsToOne('Publication');
	}

	/**
	 * Establish relationship to parent version
	 *
	 * @return  object
	 */
	public function version()
	{
		return $this->belongsToOne('Version', 'publication_version_id');
	}

	/**
	 * Get a record by publication ID and user ID, optional version ID
	 *
	 * @param   integer  $publication_id
	 * @param   integer  $created_by
	 * @param   integer  $publication_version_id
	 * @return  object
	 */
	public function oneByPublicationAndUser($publication_id, $created_by, $publication_version_id = null)
	{
		$entry = self::all()
			->whereEquals('publication_id', $publication_id)
			->whereEquals('created_by', $created_by);

		if ($publication_version_id)
		{
			$entry->whereEquals('publication_version_id', $publication_version_id);
		}

		return $entry
			->row();
	}
}
