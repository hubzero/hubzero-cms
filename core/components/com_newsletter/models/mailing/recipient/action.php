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

namespace Components\Newsletter\Models\Mailing\Recipient;

use Hubzero\Database\Relational;

/**
 * Newsletter model for a mailing recipient action
 */
class Action extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'newsletter_mailing_recipient';

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
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'action'    => 'notempty',
		'mailingid' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between mailing and recipient
	 *
	 * @return  object
	 */
	public function mailing()
	{
		return $this->belongsToOne('Components\\Newsletter\\Models\\Mailing', 'mailingid');
	}

	/**
	 * Load a record by mailing ID, email, and action
	 *
	 * @param   integer  $mailingid
	 * @param   string   $email
	 * @param   string   $action
	 * @return  object
	 */
	public static function oneForMailingAndEmail($mailingid, $email, $action)
	{
		$row = self::all()
			->whereEquals('mailingid', $mailingid)
			->whereEquals('email', $email)
			->whereEquals('action', $action)
			->row();

		if (!$row)
		{
			$row = self::blank();
		}

		return $row;
	}
}
