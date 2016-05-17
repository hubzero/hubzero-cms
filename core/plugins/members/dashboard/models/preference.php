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

namespace Plugins\Members\Dashboard\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Date;

/**
 * Model class for dashboard preferences
 */
class Preference extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xprofiles_dashboard';

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
		'preferences' => 'notempty',
		'uidNumber'   => 'positive|nonzero'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'modified'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	protected $preferences = null;

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return Date::toSql();
	}

	/**
	 * Retrieves one row loaded by an alias field
	 *
	 * @param   string   $alias     The alias to load by
	 * @param   string   $scope     Scope
	 * @param   integer  $scope_id  Scope ID
	 * @return  mixed
	 */
	public static function oneByUser($user_id)
	{
		return self::blank()
			->whereEquals('uidNumber', $user_id)
			->row();
	}

	/**
	 * Get the parent user associated with this entry
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'uidNumber');
	}

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformPreferences()
	{
		if (!is_object($this->preferences))
		{
			$this->preferences = new Registry($this->get('preferences'));
		}

		return $this->preferences;
	}

	/**
	 * Save data
	 *
	 * @return  bool
	 */
	public function save()
	{
		if (!is_string($this->get('preferences')))
		{
			$this->set('preferences', json_encode($this->get('preferences')));
		}

		return parent::save();
	}
}
