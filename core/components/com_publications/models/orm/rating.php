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

/**
 * Model class for publication rating
 */
class Rating extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'publication';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'rating' => 'positive|nonzero',
		'publication_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
	);

	/**
	 * Establish relationship to parent publication
	 *
	 * @return  object
	 */
	public function publiation()
	{
		return $this->belongsToOne('Publication');
	}

	/**
	 * Establish relationship to parent version
	 *
	 * @return  object
	 */
	public function version()
	{
		return $this->belongsToOne('Version', 'publication_version_id');
	}

	/**
	 * Get a record by publication ID and user ID, optional version ID
	 *
	 * @param   integer  $publication_id
	 * @param   integer  $created_by
	 * @param   integer  $publication_version_id
	 * @return  object
	 */
	public function oneByPublicationAndUser($publication_id, $created_by, $publication_version_id = null)
	{
		$entry = self::all()
			->whereEquals('publication_id', $publication_id)
			->whereEquals('created_by', $created_by);

		if ($publication_version_id)
		{
			$entry->whereEquals('publication_version_id', $publication_version_id);
		}

		return $entry
			->row();
	}
}
