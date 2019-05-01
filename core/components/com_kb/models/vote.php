<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use stdClass;
use Request;
use Route;
use Lang;
use Date;
use User;

/**
 * Vote model for an article
 */
class Vote extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'kb';

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
		'vote'      => 'notempty',
		'object_id' => 'positive|nonzero',
		'type'      => 'notempty',
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'vote'
	);

	/**
	 * Normalize a vote
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 */
	public function automaticVote($data)
	{
		switch (strtolower($data['vote']))
		{
			case 1:
			case '1':
			case 'yes':
			case 'positive':
			case 'like':
				return 'like';
			break;

			case -1:
			case '-1':
			case 'no':
			case 'negative':
			case 'dislike':
			default:
				return 'dislike';
			break;
		}
	}

	/**
	 * Defines a belongs to one relationship between article and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function voter()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Get the vote for a specific object/type combination and user
	 *
	 * @param   integer  $object_id  Object ID
	 * @param   integer  $user_id    User ID
	 * @param   string   $ip         IP Address
	 * @param   string   $type       Object type (article, comment)
	 * @return  string
	 */
	public function find($object_id = null, $user_id = null, $ip = null, $type = null)
	{
		return self::all()
				->whereEquals('object_id', $object_id)
				->whereEquals('user_id', $user_id)
				->whereEquals('ip', $ip)
				->whereEquals('type', $type)
				->limit(1)
				->row();
	}
}
