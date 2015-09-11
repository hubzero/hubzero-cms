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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Tables;

/**
 * Password rules class
 */
class PasswordRules extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__password_rule', 'id', $db);
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  Database query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS pr";

		return $query;
	}

	/**
	 * Get a count of the number of password rules (used mainly for pagination)
	 *
	 * @param   array    $filters
	 * @return  integer  Return count of rows
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(DISTINCT pr.id)";
		$query .= $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the an object list of password rules
	 *
	 * @param   array  $filters  Start and limit, needed for pagination
	 * @return  array  Return password rule records
	 */
	public function getRecords($filters=array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'ordering';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
		if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'ASC';
		}

		$query  = "SELECT pr.*";
		$query .= $this->buildQuery($filters);
		$query .= " ORDER BY `" . $filters['sort'] . "` " . $filters['sort_Dir'];
		$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Insert default content
	 *
	 * @param   integer  $restore_defaults  Whether or not to force restoration of default values (even if other values are present)
	 * @return  void
	 */
	public function defaultContent($restore_defaults=0)
	{
		$default_content = array(
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

		// Get a few config values
		$schema = \App::get('config')->get('db');
		$prefix = \App::get('config')->get('dbprefix');

		// Check auto_increment value of the table (wish there was a database method for this?)
		$query  =  "SELECT AUTO_INCREMENT AS ai";
		$query .= " FROM information_schema.tables";
		$query .= " WHERE table_schema = '{$schema}' AND table_name = '" . str_replace('#__', $prefix, $this->_tbl) . "'";
		$this->_db->setQuery($query);
		$auto_increment = $this->_db->loadResult();

		// Add default content if auto_increment is 1 and there is nothing there already (sort of redundant), or if it's a manual restore
		if ((self::getCount() == 0 && $auto_increment == '1') || $restore_defaults == 1)
		{
			if ($restore_defaults)
			{
				// Delete current password rules for manual restore
				$rows = self::getRecords($filters=array('start'=>0, 'limit'=>1000));
				foreach ($rows as $row)
				{
					self::delete($row->id);
				}
			}
			// Add default rules
			foreach ($default_content as $rule)
			{
				$row = new self($this->_db);
				$row->save($rule);
			}
		}
	}
}
