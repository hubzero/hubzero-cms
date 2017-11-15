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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models;

use Hubzero\Database\Relational;
use Request;
use User;
use Lang;
use Date;

/**
 * Wishlist model class for a vote
 */
class Vote extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'vote';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__vote_log';

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
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'referenceid' => 'positive|nonzero',
		'category'    => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'voted',
		'voter',
		'ip'
	);

	/**
	 * Generates automatic voted field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 **/
	public function automaticVoted($data)
	{
		return (isset($data['voted']) && $data['voted'] ? $data['voted'] : Date::toSql());
	}

	/**
	 * Generates automatic userid field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 **/
	public function automaticVoter($data)
	{
		return (isset($data['voter']) && $data['voter'] ? (int)$data['voter'] : (int)User::get('id'));
	}

	/**
	 * Generates automatic userid field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 **/
	public function automaticIp($data)
	{
		return (isset($data['ip']) && $data['ip'] ? $data['ip'] : Request::ip());
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function voter()
	{
		return $this->belongsToOne('Hubzero\User\User', 'voter');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $rtrn  What data to return
	 * @return  string
	 */
	public function voted($rtrn='')
	{
		$rtrn = strtolower($rtrn);

		if ($rtrn == 'date')
		{
			return Date::of($this->get('voted'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($rtrn == 'time')
		{
			return Date::of($this->get('voted'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('voted');
	}

	/**
	 * Load a record by user and wish
	 *
	 * @param   integer  $voter
	 * @param   integer  $referenceid
	 * @return  object
	 */
	public static function oneByUserAndWish($voter, $referenceid)
	{
		return self::all()
			->whereEquals('voter', $voter)
			->whereEquals('referenceid', $referenceid)
			->whereEquals('category', 'wish')
			->row();
	}
}
