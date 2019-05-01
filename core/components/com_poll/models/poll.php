<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Models;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'option.php';
require_once __DIR__ . DS . 'date.php';

/**
 * Poll model
 */
class Poll extends Relational
{
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
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty',
		'lag'   => 'positive|nonzero'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = str_replace(' ', '-', $alias);
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$alias = \Date::of('now')->format("%Y-%m-%d-%H-%M-%S");
		}
		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Get a list of options
	 *
	 * @return  object
	 */
	public function options()
	{
		return $this->oneToMany('Components\Poll\Models\Option', 'poll_id');
	}

	/**
	 * Get a list of vote dates
	 *
	 * @return  object
	 */
	public function dates()
	{
		return $this->oneToMany('Components\Poll\Models\Date', 'poll_id');
	}

	/**
	 * Defines a belongs to one relationship between poll and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'checked_out');
	}

	/**
	 * Get the latest poll
	 *
	 * @return  object
	 */
	public static function current()
	{
		return self::all()
			->whereEquals('state', 1)
			->whereEquals('open', 1)
			->order('id', 'desc')
			->ordered()
			->row();
	}

	/**
	 * Add vote
	 *
	 * @param   integer  $option_id  The id of the option selected
	 * @return  boolean
	 */
	public function vote($option_id)
	{
		$option_id = (int) $option_id;

		$option = Option::oneOrFail($option_id);
		$option->set('hits', (int)$option->get('hits', 0) + 1);

		if (!$option->save())
		{
			$this->setError($option->getError());
			return false;
		}

		$this->set('voters', (int)$this->get('voters', 0) + 1);

		if (!$this->save())
		{
			return false;
		}

		$dt = new Date;
		$dt->set([
			'date'    => \Date::toSql(),
			'vote_id' => (int) $option_id,
			'poll_id' => (int) $this->get('id')
		]);

		if (!$dt->save())
		{
			$this->setError($dt->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove data
		foreach ($this->options()->rows() as $item)
		{
			if (!$item->destroy())
			{
				$this->addError($item->getError());
				return false;
			}
		}

		// Remove vote logs
		foreach ($this->dates()->rows() as $dt)
		{
			if (!$dt->destroy())
			{
				$this->addError($dt->getError());
				return false;
			}
		}

		// Remove menu entries
		/*foreach ($this->menus()->rows() as $menu)
		{
			if (!$menu->destroy())
			{
				$this->addError($menu->getError());
				return false;
			}
		}*/

		// Attempt to delete the record
		return parent::destroy();
	}
}
