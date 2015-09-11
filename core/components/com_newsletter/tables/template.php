<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Newsletter\Tables;

/**
 * Table class for templates
 */
class Template extends \JTable
{
	/**
	 * Newsletter Template object constructor
	 *
	 * @param   object  $db  Database Object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__newsletter_templates', 'id', $db);
	}

	/**
	 * Save Check
	 *
	 * @return  void
	 */
	public function check()
	{
		//regex for validating hex color codes
		$hexcodeRegex = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';

		//make sure we have a name
		if (!$this->name || $this->name == '')
		{
			$this->setError('Template name is required.');
			return false;
		}

		//make sure we have not used a reserved name
		if ($this->name == 'Default HTML Email Template' || $this->name == 'Default Plain Text Email Template')
		{
			$this->setError('The template name you entered is a reserved template name.');
			return false;
		}

		//check to make sure hex codes are formated
		if ($this->primary_title_color != '' && !preg_match($hexcodeRegex, $this->primary_title_color))
		{
			$this->setError('Your primary title color code is not formatted correctly.');
			return false;
		}

		//check to make sure hex codes are formated
		if ($this->primary_text_color != '' && !preg_match($hexcodeRegex, $this->primary_text_color))
		{
			$this->setError('Your primary text color code is not formatted correctly.');
			return false;
		}

		//check to make sure hex codes are formated
		if ($this->secondary_title_color != '' && !preg_match($hexcodeRegex, $this->secondary_title_color))
		{
			$this->setError('Your secondary title color code is not formatted correctly.');
			return false;
		}

		//check to make sure hex codes are formated
		if ($this->secondary_text_color != '' && !preg_match($hexcodeRegex, $this->secondary_text_color))
		{
			$this->setError('Your secondary text color code is not formatted correctly.');
			return false;
		}

		return true;
	}

	/**
	 * Get Templates
	 *
	 * @param   integer  $id  Id of template to load
	 * @return  object
	 */
	public function getTemplates($id = null)
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE deleted=0";

		if ($id)
		{
			$sql .= " AND id=".$id;
			$this->_db->setQuery($sql);
			return $this->_db->loadObject();
		}
		else
		{
			$sql .= " ORDER BY id ASC";
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		}
	}
}