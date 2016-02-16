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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$this->primary_title_color   = strip_tags($this->primary_title_color);
		$this->primary_text_color    = strip_tags($this->primary_text_color);
		$this->secondary_title_color = strip_tags($this->secondary_title_color);
		$this->secondary_text_color  = strip_tags($this->secondary_text_color);
		/* Field changed to allow more styles
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
		}*/

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