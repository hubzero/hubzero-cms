<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models\Orm;

use Hubzero\Database\Relational;
use User;
use Date;

include_once __DIR__ . '/calendar.php';
include_once __DIR__ . '/category.php';
include_once __DIR__ . '/page.php';

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Event extends Relational
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
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $always = array(
		'modified',
		'modified_by'
	);

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['id']) && $data['id'] ? Date::of('now')->toSql() : null);
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		return (isset($data['id']) && $data['id'] ? User::get('id') : 0);
	}

	/**
	 * Defines a belongs to one relationship between event and calendar
	 *
	 * @return  object
	 */
	public function calendar()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\Calendar', 'calendar_id');
	}

	/**
	 * Get a list of pages
	 *
	 * @return  object
	 */
	public function pages()
	{
		return $this->oneToMany(__NAMESPACE__ . '\Page', 'event_id');
	}

	/**
	 * Get a list of pages
	 *
	 * @return  object
	 */
	public function respondents()
	{
		return $this->oneToMany(__NAMESPACE__ . '\Respondent', 'event_id');
	}

	/**
	 * Gets latest event
	 *
	 * @param   integer  $limit
	 * @param   string   $dateField
	 * @param   string   $sort
	 * @return  object
	 */
	public static function getLatest($limit = 10, $dateField = 'created', $sort = 'DESC')
	{
		$rows = self::all()->where('scope', '=', 'event')->where('state', '=', '1')->order($dateField, $sort)->limit($limit);

		return $rows;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if ($this->isNew())
		{
			return true;
		}

		// Remove associated data
		foreach ($this->pages()->rows() as $page)
		{
			if (!$page->destroy())
			{
				$this->addError($page->getError());
				return false;
			}
		}

		foreach ($this->respondents()->rows() as $respondent)
		{
			if (!$respondent->destroy())
			{
				$this->addError($respondent->getError());
				return false;
			}
		}

		return parent::destroy();
	}
}
