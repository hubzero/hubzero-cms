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

namespace Components\Kb\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Config\Registry;
use stdClass;
use Request;
use Route;
use Lang;
use Date;
use User;

require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Vote model for an article
 */
class Vote extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'kb';

	/**
	 * Default order by for model
	 *
	 * @var string
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
		return $this->belongsToOne('Components\Members\Models\Member', 'user_by');
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

