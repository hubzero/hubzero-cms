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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'version.php';
require_once __DIR__ . DS . 'author.php';
require_once __DIR__ . DS . 'rating.php';
require_once __DIR__ . DS . 'type.php';
require_once __DIR__ . DS . 'category.php';

/**
 * Model class for publication
 */
class Publication extends Relational
{
	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
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
	 * Establish relationship to type
	 *
	 * @return  object
	 */
	public function type()
	{
		return $this->oneToOne('Type', 'id', 'master_type');
	}

	/**
	 * Establish relationship to category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->oneToOne('Category', 'id', 'category');
	}

	/**
	 * Establish relationship to versions
	 *
	 * @return  object
	 */
	public function versions()
	{
		return $this->oneToMany('Version', 'publication_id');
	}

	/**
	 * Establish relationship to ratings
	 *
	 * @return  object
	 */
	public function ratings()
	{
		return $this->oneToMany('Rating', 'publication_id');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove ratings
		foreach ($this->ratings as $rating)
		{
			if (!$rating->destroy())
			{
				$this->addError($rating->getError());
				return false;
			}
		}

		// Remove versions
		foreach ($this->versions as $version)
		{
			if (!$version->destroy())
			{
				$this->addError($version->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}
