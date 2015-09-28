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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin;

use Hubzero\Config\Registry;

/**
 * Table class for custom plugin parameters
 */
class Params extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__plugin_params', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->object_id = intval($this->object_id);
		if (!$this->object_id)
		{
			$this->setError(\Lang::txt('Entry must have an object ID'));
			return false;
		}

		$this->folder = trim($this->folder);
		if (!$this->folder)
		{
			$this->setError(\Lang::txt('Entry must have a folder'));
			return false;
		}

		$this->element = trim($this->element);
		if (!$this->element)
		{
			$this->setError(\Lang::txt('Entry must have an element'));
			return false;
		}
		return true;
	}

	/**
	 * Load a record and binf to $this
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  boolean  True on success
	 */
	public function loadPlugin($oid=null, $folder=null, $element=NULL)
	{
		$oid     = $oid     ?: $this->object_id;
		$folder  = $folder  ?: $this->folder;
		$element = $element ?: $this->element;

		if (!$oid || !$element || !$folder)
		{
			return false;
		}

		return parent::load(array(
			'object_id' => (int) $oid,
			'folder'    => (string) $folder,
			'element'   => (string) $element
		));
	}

	/**
	 * Get the custom parameters for a plugin
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  object
	 */
	public function getCustomParams($oid=null, $folder=null, $element=null)
	{
		$oid     = $oid     ?: $this->object_id;
		$folder  = $folder  ?: $this->folder;
		$element = $element ?: $this->element;

		if (!$oid || !$folder || !$element)
		{
			return null;
		}

		$this->_db->setQuery("SELECT params FROM $this->_tbl WHERE object_id=" . $this->_db->quote($oid) . " AND folder=" . $this->_db->quote($folder) . " AND element=" . $this->_db->quote($element) . " LIMIT 1");
		$result = $this->_db->loadResult();

		return new Registry($result);
	}

	/**
	 * Get the default parameters for a plugin
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin name
	 * @return  object
	 */
	public function getDefaultParams($folder=null, $element=null)
	{
		$folder  = $folder  ?: $this->folder;
		$element = $element ?: $this->element;

		if (!$folder || !$element)
		{
			return null;
		}

		$plugin = \Plugin::byType($folder, $element);

		return new Registry($plugin->params);
	}

	/**
	 * Get the parameters for a plugin
	 * Merges default params and custom params (take precedence)
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  object
	 */
	public function getParams($oid=null, $folder=null, $element=null)
	{
		$rparams = $this->getCustomParams($oid, $folder, $element);

		$params = $this->getDefaultParams($folder, $element);
		$params->merge($rparams);

		return $params;
	}
}

