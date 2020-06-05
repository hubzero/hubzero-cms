<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Date;

require_once __DIR__ . DS . 'service.php';

/**
 * Subscription model
 *
 * @uses \Hubzero\Database\Relational
 */
class Subscription extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'users_points';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__users_points_subscriptions';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'uid'       => 'positive|nonzero',
		'serviceid' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'added'
	);

	/**
	 * Generates automatic added field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 **/
	public function automaticAdded($data)
	{
		return (isset($data['added']) && $data['added'] ? $data['added'] : Date::toSql());
	}

	/**
	 * Get the related service
	 *
	 * @return  object
	 */
	public function service()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Service', 'serviceid');
	}

	/**
	 * Generates a list of authors
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->oneToOne('\Hubzero\User\User', 'id', 'uid');
	}

	/**
	 * Generate a code
	 *
	 * @param   integer  $minlength   Minimum length
	 * @param   integer  $maxlength   Maximum length
	 * @param   integer  $usespecial  Use special characters?
	 * @param   integer  $usenumbers  Use numbers?
	 * @param   integer  $useletters  Use letters?
	 * @return  string
	 */
	public function generateCode($minlength = 6, $maxlength = 6, $usespecial = 0, $usenumbers = 1, $useletters = 1)
	{
		$key = '';
		$charset = '';

		if ($useletters)
		{
			$charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		}
		if ($usenumbers)
		{
			$charset .= "0123456789";
		}
		if ($usespecial)
		{
			$charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		}

		if ($minlength > $maxlength)
		{
			$length = mt_rand($maxlength, $minlength);
		}
		else
		{
			$length = mt_rand($minlength, $maxlength);
		}

		for ($i = 0; $i < $length; $i++)
		{
			$key .= $charset[(mt_rand(0, (strlen($charset)-1)))];
		}

		return $key;
	}

	/**
	 * Get remaining
	 *
	 * @param   string   $type          Type
	 * @param   object   $subscription  Subscription object
	 * @param   integer  $maxunits      Maximum units
	 * @param   mixed    $unitsize      Unit size
	 * @return  mixed
	 */
	public function getRemaining($type='unit', $maxunits = 24, $unitsize=1)
	{
		$current_time = time();

		$limits    = array();
		$starttime = $this->get('added');
		$lastunit  = 0;
		$today     = Date::of(time() - (24 * 60 * 60))->toSql();

		for ($i = 0; $i < $maxunits; $i++)
		{
			$starttime = Date::of(strtotime("+" . $unitsize . "month", strtotime($starttime)))->format('Y-m-d');
			$limits[$i] = $starttime;
		}

		for ($j = 0; $j < count($limits); $j++)
		{
			if (strtotime($current_time) < strtotime($limits[$j]))
			{
				$lastunit = $j + 1;

				if ($type == 'unit')
				{
					$remaining = $this->get('units') - $lastunit;
					$refund    = $remaining > 0 ? $remaining : 0;
					return ($remaining);
				}
			}
		}

		return 0;
	}

	/**
	 * Cancel a subscription
	 *
	 * @param   integer  $refund     Refund amount
	 * @param   integer  $unitsleft  Units left
	 * @return  boolean  True on success, False on error
	 */
	public function cancel($refund=0, $unitsleft=0)
	{
		// status quo if now money back is expected
		$unitsleft = $refund ? $unitsleft : 0;

		$this->set('status', 1);
		$this->set('pendingpayment', $refund);
		$this->set('pendingunits', $unitsleft);

		return $this->save();
	}
}
