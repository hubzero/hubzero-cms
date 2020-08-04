<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Activity;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Exception;
use Event;

/**
 * Activity log
 */
class Log extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'activity';

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
		'action' => 'notempty',
		'scope'  => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'//,
		//'uuid'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $always = array(
		'scope'
	);

	/**
	 * Container for details
	 *
	 * @var  object
	 */
	protected $entryDetails = null;

	/**
	 * Generate a UUID
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticUuid($data)
	{
		return sprintf(
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	/**
	 * Generates automatic scope field value
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticScope($data)
	{
		if (!isset($data['scope']))
		{
			$data['scope'] = '';
		}
		return strtolower(preg_replace("/[^a-zA-Z0-9\-_\.]/", '', trim($data['scope'])));
	}

	/**
	 * Get recipients
	 *
	 * @return  object
	 */
	public function recipients()
	{
		return $this->oneToMany('Recipient', 'log_id');
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
	 * Defines a belongs to one relationship between entry and another entry
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->belongsToOne('Hubzero\Activity\Log', 'parent');
	}

	/**
	 * Get children
	 *
	 * @return  object
	 */
	public function children()
	{
		return $this->oneToMany('Hubzero\Activity\Log', 'parent');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		foreach ($this->recipients()->rows() as $recipient)
		{
			if (!$recipient->destroy())
			{
				$this->addError($recipient->getError());
				return false;
			}
		}

		foreach ($this->children()->rows() as $child)
		{
			if (!$child->destroy())
			{
				$this->addError($child->getError());
				return false;
			}
		}

		$result = parent::destroy();

		if ($result)
		{
			Event::trigger('activity.onLogDelete', [$this]);
		}

		return $result;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		$data = $this->get('details');

		if ($data instanceof Registry)
		{
			$this->set('details', $data->toString());
		}
		else if (!is_string($data))
		{
			$this->set('details', json_encode($data));
		}

		$isNew = $this->isNew();
		$result = parent::save();

		if ($result)
		{
			Event::trigger('activity.onLogSave', [$this, $isNew]);
		}

		return $result;
	}

	/**
	 * Transform details into object
	 *
	 * @return  object  Hubzero\Config\Registry
	 */
	public function transformDetails()
	{
		if (!isset($this->entryDetails))
		{
			$this->entryDetails = new Registry($this->get('details'));
		}

		return $this->entryDetails;
	}

	/**
	 * Send an activity to recipients
	 *
	 * @param   array  $recipients
	 * @return  bool
	 */
	public function broadcast($recipients = array())
	{
		// Get everyone subscribed
		$subscriptions = Subscription::all()
			->whereEquals('scope', $this->get('scope'))
			->whereEquals('scope_id', $this->get('scope_id'))
			->rows();

		foreach ($subscriptions as $subscription)
		{
			$recipients[] = array(
				'scope'    => 'user',
				'scope_id' => $subscription->get('user_id')
			);
		}

		$sent = array();

		// Do we have any recipients?
		foreach ($recipients as $receiver)
		{
			// Default to type 'user'
			if (!is_array($receiver))
			{
				$receiver = array(
					'scope'    => 'user',
					'scope_id' => $receiver
				);
			}

			// Make sure we have expected data
			if (!isset($receiver['scope'])
			 || !isset($receiver['scope_id']))
			{
				$receiver = array_values($receiver);

				$receiver['scope']    = $receiver[0];
				$receiver['scope_id'] = $receiver[1];
			}

			$key = $receiver['scope'] . '.' . $receiver['scope_id'];

			// No duplicate sendings
			if (in_array($key, $sent))
			{
				continue;
			}

			// Create a recipient object that ties a user to an activity
			$recipient = Recipient::blank()->set([
				'scope'    => $receiver['scope'],
				'scope_id' => $receiver['scope_id'],
				'log_id'   => $this->get('id'),
				'state'    => Recipient::STATE_PUBLISHED
			]);

			if (!$recipient->save())
			{
				return false;
			}

			$sent[] = $key;
		}

		return true;
	}

	/**
	 * Create an activity log entry and broadcast it.
	 *
	 * @param   mixed    $data
	 * @param   array    $recipients
	 * @return  boolean
	 */
	public static function log($data = array(), $recipients = array())
	{
		if (is_object($data))
		{
			$data = (array) $data;
		}

		if (is_string($data))
		{
			$data = array('description' => $data);

			$data['action'] = 'create';

			if (substr(strtolower($data['description']), 0, 6) == 'update')
			{
				$data['action'] = 'update';
			}

			if (substr(strtolower($data['description']), 0, 6) == 'delete')
			{
				$data['action'] = 'delete';
			}
		}

		try
		{
			$activity = self::blank()->set($data);

			if (!$activity->save())
			{
				return false;
			}

			if (!$activity->broadcast($recipients))
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Modify query to only return published entries
	 *
	 * @return  object
	 */
	public function wherePublished()
	{
		$this->whereEquals(Recipient::blank()->getTableName() . '.state', Recipient::STATE_PUBLISHED);
		return $this;
	}

	/**
	 * Get all logs for a recipient
	 *
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  boolean
	 */
	public static function allForRecipient($scope, $scope_id = 0)
	{
		if (!is_array($scope))
		{
			$scope = array($scope);
		}

		$logs = self::all();

		$r = Recipient::blank()->getTableName();
		$l = $logs->getTableName();

		$logs
			->select($l . '.*')
			->join($r, $l . '.id', $r . '.log_id')
			->whereIn($r . '.scope', $scope)
			->whereEquals($r . '.scope_id', $scope_id);

		return $logs;
	}
}
