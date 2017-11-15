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

namespace Components\Redirect\Models;

use Hubzero\Database\Relational;
use Lang;
use Date;

/**
 * Model class for a redirect entry
 */
class Link extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'redirect';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'created_date';

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
		'old_url' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'old_url',
		'new_url',
		'status_code',
		'created_date',
		'modified_date'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('old_url', function($data)
		{
			$entries = Link::blank()
				->whereEquals('old_url', substr($data['old_url'], 0, 255));

			if (isset($data['id']))
			{
				$entries->where('id', '!=', $data['id']);
			}

			$row = $entries->row();

			return !$row->get('id') ? false : Lang::txt('COM_REDIRECT_ERROR_DUPLICATE_OLD_URL');
		});
	}

	/**
	 * Generates automatic old_url field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOldUrl($data)
	{
		if (substr($data['old_url'], 0, strlen('http')) != 'http')
		{
			$data['old_url'] = '/' . ltrim($data['old_url'], '/');
		}
		return rtrim($data['old_url'], '/');
	}

	/**
	 * Generates automatic new_url field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticNewUrl($data)
	{
		if ($data['new_url'])
		{
			$data['new_url'] = trim($data['new_url']);
			$data['new_url'] = trim($data['new_url'], '/');
			if (substr($data['new_url'], 0, strlen('http')) != 'http')
			{
				$data['new_url'] = '/' . $data['new_url'];
			}
		}
		return $data['new_url'];
	}

	/**
	 * Generates automatic status_code field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticStatusCode($data)
	{
		// Redirects must be either a 301 or 302
		if (isset($data['new_url']) && $data['new_url'] && !in_array($data['status_code'], array(301, 302)))
		{
			$data['status_code'] = 301;
		}
		return $data['status_code'];
	}

	/**
	 * Generates automatic created date field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticCreatedDate($data)
	{
		if (!isset($data['created_date']))
		{
			$data['created_date'] = null;
		}

		$created_date = $data['created_date'];

		if (!$created_date || $created_date == '0000-00-00 00:00:00')
		{
			$created_date = Date::of('now')->toSql();
		}

		return $created_date;
	}

	/**
	 * Generates automatic modified date field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModifiedDate($data)
	{
		if (!isset($data['modified_date']) || !$data['modified_date'])
		{
			$data['modified_date'] = '0000-00-00 00:00:00';
		}
		if (isset($data['id']) && $data['id'])
		{
			$data['modified_date'] = Date::of('now')->toSql();
		}
		return $data['modified_date'];
	}

	/**
	 * Determine if an entry is published
	 *
	 * @return  bool
	 */
	public function isPublished()
	{
		return ($this->get('published') == self::STATE_PUBLISHED);
	}
}
