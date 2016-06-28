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

namespace Components\Courses\Models\Orm;

use Hubzero\Database\Relational;
use Html;

/**
 * Model class for a course page
 */
class Page extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'courses';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'url';

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
		'title'   => 'notempty',
		'content' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'url'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'description'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $params = NULL;

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticOrdering($data)
	{
		$highest = self::all()
			->whereEquals('course_id', $data['course_id'])
			->whereEquals('offering_id', $data['offering_id'])
			->order('ordering', 'desc')
			->row();

		return ((int)$highest->get('ordering', 0) + 1);
	}

	/**
	 * Generates automatic url field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticUrl($data)
	{
		$alias = (isset($data['url']) && $data['url'] ? $data['url'] : $data['title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 100)
		{
			$alias = substr($alias . ' ', 0, 100);
			$alias = substr($alias, 0, strrpos($alias,' '));
		}
		$alias = str_replace(' ', '_', $alias);

		return preg_replace("/[^a-zA-Z0-9_\-\.]/", '', strtolower($alias));
	}

	/**
	 * Retrieves one row loaded by url field
	 *
	 * @param   string  $url
	 * @return  mixed
	 */
	public static function oneByUrl($url)
	{
		return self::blank()
			->whereEquals('url', $url)
			->row();
	}

	/**
	 * Get parent course
	 *
	 * @return  object
	 */
	public function course()
	{
		return $this->belongsToOne('Course');
	}

	/**
	 * Get parent offering
	 *
	 * @return  object
	 */
	public function offering()
	{
		return $this->belongsToOne('Offering');
	}

	/**
	 * Get parent section
	 *
	 * @return  object
	 */
	public function section()
	{
		return $this->belongsToOne('Section');
	}

	/**
	 * Parses content string as directed
	 *
	 * @return  string
	 */
	public function transformContent()
	{
		$field = 'content';

		$property = "_{$field}Parsed";

		if (!isset($this->$property))
		{
			$params = array(
				'option'   => 'com_courses',
				'scope'    => '',
				'pagename' => $this->get('url'),
				'pageid'   => 0,
				'filepath' => '',
				'domain'   => ''
			);

			$this->$property = Html::content('prepare', $this->get($field, ''), $params);
		}

		return $this->$property;
	}
}
