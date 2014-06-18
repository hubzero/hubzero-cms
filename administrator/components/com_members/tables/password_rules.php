<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Password rules class
 */
class MembersPasswordRules extends JTable
{
	/**
	 * ID - primary key
	 *
	 * @var int(11)
	 */
	var $id = null;

	/**
	 * Class
	 *
	 * @var char(255)
	 */
	var $class = null;

	/**
	 * Description
	 *
	 * @var char(255)
	 */
	var $description = null;

	/**
	 * Enabled
	 *
	 * @var tinyint(1)
	 */
	var $enabled = null;

	/**
	 * Failure message
	 *
	 * @var char(255)
	 */
	var $failuremsg = null;

	/**
	 * Group
	 *
	 * @var char(32)
	 */
	var $grp = null;

	/**
	 * Ordering
	 *
	 * @var int(11)
	 */
	var $ordering = null;

	/**
	 * Rule
	 *
	 * @var char(255)
	 */
	var $rule = null;

	/**
	 * Value
	 *
	 * @var char(255)
	 */
	var $value = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__password_rule', 'id', $db );
	}

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS pr";

		return $query;
	}

	/**
	 * Get a count of the number of password rules (used mainly for pagination)
	 *
	 * @param  array $filters
	 * @return object Return count of rows
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(DISTINCT pr.id)";
		$query .= $this->buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the an object list of password rules
	 *
	 * @param  array $filters start and limit, needed for pagination
	 * @return object Return password rule records
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT pr.*";
		$query .= $this->buildquery($filters);
		$query .= " ORDER BY pr.ordering ASC";
		$query .= " LIMIT ".$filters['start'].",".$filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Insert default content
	 *
	 * @param      int $restore_defaults - whether or not to force restoration of default values (even if other values are present)
	 * @return     void
	 */
	public function defaultContent($restore_defaults=0)
	{
		$default_content = array();
		$default_content[] = array(
							'class'       => 'alpha',
							'description' => 'Must contain at least 1 letter',
							'enabled'     => '0',
							'failuremsg'  => 'Must contain at least 1 letter',
							'grp'         => 'hub',
							'ordering'    => '1',
							'rule'        => 'minClassCharacters',
							'value'       => '1');
		$default_content[] = array(
							'class'       => 'nonalpha',
							'description' => 'Must contain at least 1 number or punctuation mark',
							'enabled'     => '0',
							'failuremsg'  => 'Must contain at least 1 number or punctuation mark',
							'grp'         => 'hub',
							'ordering'    => '2',
							'rule'        => 'minClassCharacters',
							'value'       => '1');
		$default_content[] = array(
							'class'       => '',
							'description' => 'Must be at least 8 characters long',
							'enabled'     => '0',
							'failuremsg'  => 'Must be at least 8 characters long',
							'grp'         => 'hub',
							'ordering'    => '3',
							'rule'        => 'minPasswordLength',
							'value'       => '8');
		$default_content[] = array(
							'class'       => '',
							'description' => 'Must be no longer than 16 characters',
							'enabled'     => '0',
							'failuremsg'  => 'Must be no longer than 16 characters',
							'grp'         => 'hub',
							'ordering'    => '4',
							'rule'        => 'maxPasswordLength',
							'value'       => '16');
		$default_content[] = array(
							'class'       => '',
							'description' => 'Must contain more than 4 unique characters',
							'enabled'     => '0',
							'failuremsg'  => 'Must contain more than 4 unique characters',
							'grp'         => 'hub',
							'ordering'    => '5',
							'rule'        => 'minUniqueCharacters',
							'value'       => '5');
		$default_content[] = array(
							'class'       => '',
							'description' => 'Must not contain easily guessed words',
							'enabled'     => '0',
							'failuremsg'  => 'Must not contain easily guessed words',
							'grp'         => 'hub',
							'ordering'    => '6',
							'rule'        => 'notBlacklisted',
							'value'       => '');
		$default_content[] = array(
							'class'       => '',
							'description' => 'Must not contain your name or parts of your name',
							'enabled'     => '0',
							'failuremsg'  => 'Must not contain your name or parts of your name',
							'grp'         => 'hub',
							'ordering'    => '7',
							'rule'        => 'notNameBased',
							'value'       => '');
		$default_content[] = array(
							'class'       => '',
							'description' => 'Must not contain your username',
							'enabled'     => '0',
							'failuremsg'  => 'Must not contain your username',
							'grp'         => 'hub',
							'ordering'    => '8',
							'rule'        => 'notUsernameBased',
							'value'       => '');
		$default_content[] = array(
							'class'       => '',
							'description' => 'Must be different than the previous password (re-use of the same password will not be allowed for one (1) year)',
							'enabled'     => '0',
							'failuremsg'  => 'Must be different than the previous password (re-use of the same password will not be allowed for one (1) year)',
							'grp'         => 'hub',
							'ordering'    => '9',
							'rule'        => 'notReused',
							'value'       => '365');
		$default_content[] = array(
							'class'       => '',
							'description' => 'Must be changed at least every 120 days',
							'enabled'     => '0',
							'failuremsg'  => 'Must be changed at least every 120 days',
							'grp'         => 'hub',
							'ordering'    => '10',
							'rule'        => 'notStale',
							'value'       => '120');

		// Get a few config values from joomla
		$app    = JFactory::getApplication();
		$schema = $app->getCfg('db');
		$prefix = $app->getCfg('dbprefix');

		// Check auto_increment value of the table (wish there was a jdatabase method for this?)
		$query =  "SELECT AUTO_INCREMENT AS ai";
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
