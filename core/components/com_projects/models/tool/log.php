<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Models\Tool;

use Hubzero\Base\Model;
use Components\Projects\Tables;

/**
 * Project Tool Log model
 */
class Log extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\ToolLog';

	/**
	 * Registry
	 *
	 * @var object
	 */
	public $config = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db = \JFactory::getDBO();

		$this->_tbl = new Tables\ToolLog($this->_db);
	}

	/**
	 * Returns a reference to a log model
	 *
	 * @param      mixed $oid object ID
	 * @return     object Todo
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Update Parent Name
	 *
	 * @param      integer $id
	 * @param      string $name
	 * @return     boolean
	 */
	public function updateParentName($id=null, $name=null)
	{
		if (!$id || !$name)
		{
			return false;
		}
		return $this->_tbl->updateParentName($id, $name);
	}

	/**
	 * getLastUpdate
	 *
	 * @param      string $name
	 * @return     object
	 */
	public function getLastUpdate($statusChange = false)
	{
		return $this->_tbl->getLastUpdate(
			$this->get('instance_id'),
			$this->get('parent_name'),
			$this->get('parent_id'),
			$statusChange
		);
	}

	/**
	 * Get item history
	 *
	 *
	 * @param      array $filters
	 * @return     object list
	 */
	public function getHistory($filters = array())
	{
		return $this->_tbl->getHistory(
			$this->get('parent_name'),
			$this->get('parent_id'),
			$this->get('instance_id'),
			$filters
		);
	}

	/**
	 * Determine changes
	 *
	 * @param      object $old  Tool model
	 * @param      object $new  Tool model
	 * @return     string
	 */
	public function whatChanged($old, $new)
	{
		$log = '';

		// Name change
		if ($old->get('name') != $new->get('name'))
		{
			$log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_NAME'));
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM');
			$log .= ' &ldquo;' . $old->get('name') . '&rdquo; ';
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO');
			$log .= ' &ldquo;' . $new->get('name') . '&rdquo; ';
			$log .= "\n";
		}

		// Title change
		if ($old->get('title') != $new->get('title'))
		{
			$log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_TITLE'));
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM');
			$log .= ' &ldquo;' . $old->get('title') . '&rdquo; ';
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO');
			$log .= ' &ldquo;' . $new->get('title') . '&rdquo; ';
			$log .= "\n";
		}

		// Dev area access change
		if ($old->get('opendev') != $new->get('opendev'))
		{
			$log .= strtolower(Lang::txt('PLG_PROJECTS_TOOLS_DEV_AREA_ACCESS'));
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_CHANGED');
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_FROM') . ' ';
			$log .= $old->get('opendev') ? Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_SHARED') : Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_RESTRICTED');
			$log .= ' ' . Lang::txt('PLG_PROJECTS_TOOLS_TO') . ' ';
			$log .= $new->get('opendev') == 1 ? Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_SHARED') : Lang::txt('PLG_PROJECTS_TOOLS_ACCESS_RESTRICTED');
			$log .= "\n";
		}

		return $log;
	}

	/**
	 * Get the actor
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function actor($property=null)
	{
		if (!isset($this->_actor) || !($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_actor = \Hubzero\User\Profile::getInstance($this->get('actor'));
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_actor->get($property);
		}
		return $this->_actor;
	}
}

