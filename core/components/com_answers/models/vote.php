<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\Validate;
use Lang;
use Date;
use User;

/**
 * Response Log for Q&A
 */
class Vote extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'item';

	/**
	 * Default order by for model
	 *
	 * @var string
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
		'item_id'   => 'positive|nonzero',
		'item_type' => 'notempty',
		'vote'      => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'vote'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Runs extra setup code when creating a new model
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('ip', function($data)
		{
			if (isset($data['ip']) && !Validate::ip($data['ip']))
			{
				return Lang::txt('Invalid IP address');
			}
			return false;
		});
	}

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
				return 1;
			break;

			case -1:
			case '-1':
			case 'no':
			case 'negative':
			case 'dislike':
			default:
				return -1;
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
		return Hubzero\User\Profile::getInstance($this->get('created_by'));
	}

	/**
	 * Check if a user has voted
	 *
	 * @param   integer  $item_type  Item type
	 * @param   integer  $item_id    Item ID
	 * @param   integer  $user_id    User ID
	 * @param   string   $ip         IP address
	 * @return  integer
	 */
	public function hasVoted($item_type=null, $item_id=null, $user_id=null, $ip=null)
	{
		if ($item_id == null)
		{
			return 0;
		}

		$logs = self::all()
			->whereEquals('item_type', $item_type)
			->whereEquals('item_id', $item_id);

		if ($user_id)
		{
			$logs->whereEquals('created_by', $user_id);
		}
		elseif ($ip)
		{
			$logs->whereEquals('ip', $ip);
		}

		$log = $logs
			->order('created', 'desc')
			->limit(1)
			->row();

		return $log->get('id');
	}
}

