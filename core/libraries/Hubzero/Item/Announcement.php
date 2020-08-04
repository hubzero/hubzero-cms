<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Item;

use Hubzero\Database\Relational;
use Lang;
use Date;

/**
 * Item Announcement
 */
class Announcement extends Relational
{
	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

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
		'content' => 'notempty'
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
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'publish_up',
		'publish_down'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'content'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('publish_down', function($data)
		{
			if (!$data['publish_down'] || $data['publish_down'] == '0000-00-00 00:00:00')
			{
				return false;
			}
			return $data['publish_down'] >= $data['publish_up'] ? false : Lang::txt('The entry cannot end before it begins');
		});
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishUp($data)
	{
		if (!isset($data['publish_up']))
		{
			$data['publish_up'] = null;
		}

		$publish_up = $data['publish_up'];

		if (!$publish_up || $publish_up == '0000-00-00 00:00:00')
		{
			$publish_up = ($data['id'] ? $this->created : Date::of('now')->toSql());
		}

		return $publish_up;
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishDown($data)
	{
		if (!isset($data['publish_down']) || !$data['publish_down'])
		{
			$data['publish_down'] = null;
		}
		return $data['publish_down'];
	}

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Check if the entry is available
	 *
	 * @return  boolean
	 */
	public function inPublishWindow()
	{
		if ($this->started() && !$this->ended())
		{
			return true;
		}

		return false;
	}

	/**
	 * Has the publish window started?
	 *
	 * @return  boolean
	 */
	public function started()
	{
		// If it doesn't exist or isn't published
		if ($this->isNew())
		{
			return false;
		}

		if ($this->get('publish_up')
		 && $this->get('publish_up') != '0000-00-00 00:00:00'
		 && $this->get('publish_up') > Date::toSql())
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the publish window ended?
	 *
	 * @return  boolean
	 */
	public function ended()
	{
		// If it doesn't exist or isn't published
		if ($this->isNew())
		{
			return true;
		}

		if ($this->get('publish_down')
		 && $this->get('publish_down') != '0000-00-00 00:00:00'
		 && $this->get('publish_down') <= Date::toSql())
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to check if announcement belongs to entity
	 *
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  boolean
	 */
	public function belongsToObject($scope, $scope_id)
	{
		// Make sure we have an id
		if ($this->isNew())
		{
			return true;
		}

		// Make sure scope and id match
		if ($this->get('scope') == (string)$scope
		 && $this->get('scope_id') == (int)$scope_id)
		{
			return true;
		}

		return false;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function published($as='')
	{
		if (!$this->get('publish_up') || $this->get('publish_up') == '0000-00-00 00:00:00')
		{
			$this->set('publish_up', $this->get('created'));
		}

		$as = strtolower($as);

		if ($as)
		{
			if ($as == 'date')
			{
				return Date::of($this->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			}

			if ($as == 'time')
			{
				return Date::of($this->get('publish_up'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			}

			return Date::of($this->get('publish_up'))->toLocal($as);
		}

		return $this->get('publish_up');
	}
}
