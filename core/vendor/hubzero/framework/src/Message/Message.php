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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;

/**
 * Model class for a message
 */
class Message extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__xmessage';

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
		'message'    => 'notempty',
		'created_by' => 'positive|nonzero'
	);

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
	 * Get a record count based on filters passed
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		return self::all()
			->total();
	}

	/**
	 * Get records based on filters passed
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		return self::all()
			->rows();
	}

	/**
	 * Builds a query string based on filters passed
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function buildQuery($filters=array())
	{
		$entries = self::all();

		$m = $entries->getTableName();
		$u = '#__users';

		if (isset($filters['group_id']) && $filters['group_id'] != 0)
		{
			$entries
				->select($m . '.*,' . $u . '.name')
				->join($u, $u . '.id', $m . '.created_by', 'inner');
		}
		else
		{
			$r = Recipient::blank()->getTableName();

			$entries
				->select($m . '.*,' . $u . '.name')
				->join($r, $r . '.mid', $m . '.id', 'inner')
				->join($u, $u . '.id', $r . '.uid', 'inner');
		}

		if (isset($filters['created_by']) && $filters['created_by'] != 0)
		{
			$entries->whereEquals('created_by', $filters['created_by']);
		}
		if (isset($filters['daily_limit']) && $filters['daily_limit'] != 0)
		{
			$start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . " 00:00:00";
			$end   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . " 23:59:59";

			$entries->where('created', '>=', $start);
			$entries->where('created', '<=', $end);
		}
		if (isset($filters['group_id']) && $filters['group_id'] != 0)
		{
			$entries->whereEquals('group_id', (int)$filters['group_id']);
		}

		return $entries;
	}

	/**
	 * Get sent messages
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getSentMessages($filters=array())
	{
		$entries = $this->buildQuery($filters);
		$entries->order($entries->getTableName() . '.created', 'desc');

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$entries->limit($filters['limit'])
				->start($filters['start']);
		}

		return $entries->rows();
	}

	/**
	 * Get a record count of messages sent
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getSentMessagesCount($filters=array())
	{
		$entries = $this->buildQuery($filters);

		return $entries->total();
	}

	/**
	 * Transform and prepare content
	 *
	 * @return  string
	 */
	public function transformMessage()
	{
		$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";

		$message = str_replace("\n", "\n ", stripslashes($this->get('message')));
		$message = preg_replace_callback("/$UrlPtrn/", array($this,'autolink'), $message);
		$message = nl2br($message);
		$message = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $message);

		return $message;
	}

	/**
	 * Auto-link mailto, ftp, and http strings in text
	 *
	 * @param   array  $matches  Text to autolink
	 * @return  string
	 */
	protected function autolink($matches)
	{
		$href = $matches[0];

		if (substr($href, 0, 1) == '!')
		{
			return substr($href, 1);
		}

		$href = str_replace('"', '', $href);
		$href = str_replace("'", '', $href);
		$href = str_replace('&#8221', '', $href);

		$h = array('h', 'm', 'f', 'g', 'n');
		if (!in_array(substr($href, 0, 1), $h))
		{
			$href = substr($href, 1);
		}
		$name = trim($href);
		if (substr($name, 0, 7) == 'mailto:')
		{
			$name = substr($name, 7, strlen($name));
			$name = String::obfuscate($name);

			$href = 'mailto:' . $name;
		}
		$l = sprintf(
			' <a class="ext-link" href="%s" rel="external">%s</a>', $href, $name
		);
		return $l;
	}
}
