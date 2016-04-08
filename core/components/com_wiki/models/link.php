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

namespace Components\Wiki\Models;

use Hubzero\Database\Relational;
use Date;
use Lang;

/**
 * Wiki model for page URIs
 */
class Link extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wiki';

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
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'page_id' => 'positive|nonzero',
		'scope'   => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'timestamp'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  string
	 */
	public function automaticTimestamp($data)
	{
		if (!isset($data['timestamp']))
		{
			$data['timestamp'] = Date::of('now')->toSql();
		}
		return $data['timestamp'];
	}

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  object
	 */
	public function page()
	{
		return $this->belongsToOne('Page', 'page_id');
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as
	 * @param   string  $format
	 * @return  string
	 */
	public function created($as='', $format=null)
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('timestamp'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('timestamp'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($format)
		{
			return Date::of($this->get('timestamp'))->toLocal($format);
		}

		return $this->get('timestamp');
	}

	/**
	 * Delete all entries for a specific page
	 *
	 * @param   array    $links
	 * @return  boolean  True on success
	 */
	public function addLinks($links=array())
	{
		if (count($links) <= 0)
		{
			return true;
		}

		$timestamp = Date::toSql();

		foreach ($links as $link)
		{
			$row = self::blank();
			$row->set('page_id', $link['page_id']);
			$row->set('timestamp', $timestamp);
			$row->set('scope', $link['scope']);
			$row->set('scope_id', $link['scope_id']);
			$row->set('link', $link['link']);
			$row->set('url', $link['url']);

			if (!$row->save())
			{
				$this->addError($row->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Update entries
	 *
	 * @param   integer  $page_id  Page ID
	 * @param   array    $data
	 * @return  boolean  True on success
	 */
	public function updateLinks($page_id, $data=array())
	{
		$links = array();

		foreach ($data as $data)
		{
			// Eliminate duplicates
			$links[$data['link']] = $data;
		}

		$rows = self::all()
			->whereEquals('page_id', $page_id)
			->rows();

		foreach ($rows as $row)
		{
			if (!isset($links[$row->get('link')]))
			{
				// Link wasn't found, delete it
				$row->destroy();
			}
			else
			{
				unset($links[$row->get('link')]);
			}
		}

		if (count($links) > 0)
		{
			return $this->addLinks($links);
		}

		return true;
	}
}
