<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Audience;

use Hubzero\Database\Relational;

/**
 * Resource audience level model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Level extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource_taxonomy_audience';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'label'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticLabel($data)
	{
		$alias = (isset($data['label']) && $data['label'] ? $data['label'] : $data['title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 11)
		{
			$alias = substr($alias . ' ', 0, 11);
			$alias = substr($alias, 0, strrpos($alias, ' '));
		}
		$alias = str_replace(' ', '-', $alias);

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Get field by label
	 *
	 * @param   string  $label
	 * @return  object
	 */
	public static function oneByLabel($label)
	{
		$result = self::all()
			->whereEquals('label', $label)
			->row();

		if (!$result)
		{
			$result = self::blank();
		}

		return $result;
	}
}
