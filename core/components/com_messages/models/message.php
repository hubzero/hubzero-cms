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

namespace Components\Messages\Models;

use Hubzero\Database\Relational;
use User;
use Date;

/**
 * Model class for a message
 */
class Message extends Relational
{
	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'date_time';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'message_id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'subject' => 'notempty',
		'message' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'user_id_from',
		'date_time'
	);

	/**
	 * Generates automatic created by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 * @since   2.0.0
	 **/
	public function automaticUserIdFrom($data)
	{
		return (isset($data['user_id_from']) && $data['user_id_from'] ? (int)$data['user_id_from'] : (int)User::get('id'));
	}

	/**
	 * Generates automatic created field value
	 *
	 * @return  string
	 */
	public function automaticDateTime()
	{
		return (isset($data['date_time']) && $data['date_time'] ? $data['date_time'] : Date::toSql());
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function from()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id_from');
	}

	/**
	 * Get the recipient of this entry
	 *
	 * @return  object
	 */
	public function to()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id_to');
	}

	/**
	 * Return a formatted timestamp for the created time
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);
		$dt = $this->get('date_time');

		if ($as == 'date')
		{
			return Date::of($dt)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($dt)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($dt)->toLocal($as);
		}

		return $dt;
	}
}
