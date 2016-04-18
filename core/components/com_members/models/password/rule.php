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

namespace Components\Members\Models\Password;

use Hubzero\Database\Relational;

/**
 * Password rule model
 */
class Rule extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'password';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

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
		'class'       => 'notempty',
		'description' => 'notempty',
		'rule'        => 'notempty'
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
	 * Insert default content
	 *
	 * @param   integer  $restore  Whether or not to force restoration of default values (even if other values are present)
	 * @return  void
	 */
	public static function defaultContent($restore=0)
	{
		$defaults = array(
			array(
				'class'       => 'alpha',
				'description' => 'Must contain at least 1 letter',
				'enabled'     => '0',
				'failuremsg'  => 'Must contain at least 1 letter',
				'grp'         => 'hub',
				'ordering'    => '1',
				'rule'        => 'minClassCharacters',
				'value'       => '1'
			),
			array(
				'class'       => 'nonalpha',
				'description' => 'Must contain at least 1 number or punctuation mark',
				'enabled'     => '0',
				'failuremsg'  => 'Must contain at least 1 number or punctuation mark',
				'grp'         => 'hub',
				'ordering'    => '2',
				'rule'        => 'minClassCharacters',
				'value'       => '1'
			),
			array(
				'class'       => '',
				'description' => 'Must be at least 8 characters long',
				'enabled'     => '0',
				'failuremsg'  => 'Must be at least 8 characters long',
				'grp'         => 'hub',
				'ordering'    => '3',
				'rule'        => 'minPasswordLength',
				'value'       => '8'
			),
			array(
				'class'       => '',
				'description' => 'Must be no longer than 16 characters',
				'enabled'     => '0',
				'failuremsg'  => 'Must be no longer than 16 characters',
				'grp'         => 'hub',
				'ordering'    => '4',
				'rule'        => 'maxPasswordLength',
				'value'       => '16'
			),
			array(
				'class'       => '',
				'description' => 'Must contain more than 4 unique characters',
				'enabled'     => '0',
				'failuremsg'  => 'Must contain more than 4 unique characters',
				'grp'         => 'hub',
				'ordering'    => '5',
				'rule'        => 'minUniqueCharacters',
				'value'       => '5'
			),
			array(
				'class'       => '',
				'description' => 'Must not contain easily guessed words',
				'enabled'     => '0',
				'failuremsg'  => 'Must not contain easily guessed words',
				'grp'         => 'hub',
				'ordering'    => '6',
				'rule'        => 'notBlacklisted',
				'value'       => ''
			),
			array(
				'class'       => '',
				'description' => 'Must not contain your name or parts of your name',
				'enabled'     => '0',
				'failuremsg'  => 'Must not contain your name or parts of your name',
				'grp'         => 'hub',
				'ordering'    => '7',
				'rule'        => 'notNameBased',
				'value'       => ''
			),
			array(
				'class'       => '',
				'description' => 'Must not contain your username',
				'enabled'     => '0',
				'failuremsg'  => 'Must not contain your username',
				'grp'         => 'hub',
				'ordering'    => '8',
				'rule'        => 'notUsernameBased',
				'value'       => ''
			),
			array(
				'class'       => '',
				'description' => 'Must be different than the previous password (re-use of the same password will not be allowed for one (1) year)',
				'enabled'     => '0',
				'failuremsg'  => 'Must be different than the previous password (re-use of the same password will not be allowed for one (1) year)',
				'grp'         => 'hub',
				'ordering'    => '9',
				'rule'        => 'notReused',
				'value'       => '365'
			),
			array(
				'class'       => '',
				'description' => 'Must be changed at least every 120 days',
				'enabled'     => '0',
				'failuremsg'  => 'Must be changed at least every 120 days',
				'grp'         => 'hub',
				'ordering'    => '10',
				'rule'        => 'notStale',
				'value'       => '120'
			)
		);


		if ($restore)
		{
			// Delete current password rules for manual restore
			$rows = self::all()->limit(1000)->rows();

			foreach ($rows as $row)
			{
				if (!$row->destroy())
				{
					return false;
				}
			}
		}

		// Add default rules
		foreach ($defaults as $rule)
		{
			$row = self::blank()->set($rule);

			if (!$row->save())
			{
				return false;
			}
		}

		return true;
	}
}
