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

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Date;

/**
 * Projects Stamp model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Stamp extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'project_public';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'processed';

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
		'projectid' => 'positive|nonzero',
		'stamp' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Generates automatic priority field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticProcessed($data)
	{
		if (!isset($data['processed']) || !$data['processed'] || $data['processed'] == '0000-00-00 00:00:00')
		{
			$data['processed'] = '0000-00-00 00:00:00';
		}

		return $data['processed'];
	}

	/**
	 * Determine if the record has expired
	 *
	 * @return  bool
	 */
	public function isExpired()
	{
		if ($this->get('expires')
		 && $this->get('expires') != '0000-00-00 00:00:00'
		 && $this->get('expires') <= Date::of('now')->toSql())
		{
			return true;
		}

		return false;
	}

	/**
	 * Get a record by stamp
	 *
	 * @param   string  $stamp
	 * @return  object
	 */
	public static function oneByStamp($stamp)
	{
		return self::all()
			->whereEquals('stamp', $stamp)
			->row();
	}

	/**
	 * Register stamp
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $reference  Reference string to object (JSON)
	 * @param   string   $type
	 * @param   integer  $listed
	 * @param   string   $expires
	 * @return  mixed    False if error, String on success
	 */
	public static function register($projectid = 0, $reference = '', $type = 'files', $listed = 0, $expires = null)
	{
		if (!$projectid || !$reference)
		{
			return false;
		}

		$obj = self::all()
			->whereEquals('projectid', $projectid)
			->whereEquals('reference', $reference)
			->whereEquals('type', $type)
			->row();

		// Load record
		if ($obj->get('id'))
		{
			if ($obj->isExpired())
			{
				// Expired
				$obj->destroy();
			}
			else
			{
				if ($listed)
				{
					$obj->set('listed', $listed);
				}
				if ($expires)
				{
					$obj->set('expires', $expires);
				}
				$obj->save();

				return $obj->get('stamp');
			}
		}

		// Generate stamp
		require_once \Component::path('com_projects') . DS . 'helpers' . DS . 'html.php';
		$stamp = \Components\Projects\Helpers\Html::generateCode(20, 20, 0, 1, 1);

		$obj->set(array(
			'stamp'     => $stamp,
			'projectid' => $projectid,
			'listed'    => $listed,
			'type'      => $type,
			'reference' => $reference,
			'expires'   => $expires
		));

		if (!$obj->save())
		{
			return false;
		}

		return $stamp;
	}
}
