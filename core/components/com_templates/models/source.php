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

namespace Components\Templates\Models;

use Hubzero\Base\Object;

/**
 * Source model
 */
class Source extends Object
{
	/**
	 * Cache for the template information.
	 *
	 * @var  object
	 */
	private $_template = null;

	/**
	 * Method to get a single record.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getSource()
	{
		$item = new stdClass;

		if (!$this->_template)
		{
			$this->getTemplate();
		}

		if ($this->_template)
		{
			$fileName = $this->get('filename');
			$filePath = \Hubzero\Filesystem\Util::normalizePath($this->_template->path . '/' . $fileName);

			if (file_exists($filePath))
			{
				$item->extension_id = $this->get('extension_id');
				$item->filename     = $fileName;
				$item->source       = Filesystem::read($filePath);
			}
			else
			{
				$this->setError(Lang::txt('COM_TEMPLATES_ERROR_SOURCE_FILE_NOT_FOUND'));
			}
		}

		return $item;
	}

	/**
	 * Method to get the template information.
	 *
	 * @return  mixed  Object if successful, false if not and internal error is set.
	 */
	public function getTemplate()
	{
		// Initialise variables.
		if (is_null($this->_template))
		{
			$pk     = $this->getState('extension.id');
			$db     = App::get('db');
			$result = false;

			// Get the template information.
			$query = $db->getQuery()
				->select('extension_id')
				->select('client_id')
				->select('element')
				->select('protected')
				->from('#__extensions')
				->whereEquals('extension_id', $pk)
				->whereEquals('type', 'template')
				->toString();

			$db->setQuery($query);

			$result = $db->loadObject();

			if (empty($result))
			{
				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);
				}
				else
				{
					$this->setError(Lang::txt('COM_TEMPLATES_ERROR_EXTENSION_RECORD_NOT_FOUND'));
				}
				$this->_template = false;
			}
			else
			{
				$this->_template = $result;
				$this->_template->path = \Hubzero\Filesystem\Util::normalizePath(($this->_template->protected ? PATH_CORE : PATH_APP) . '/templates/' . $this->_template->element);
			}
		}

		return $this->_template;
	}

	/**
	 * Method to store the source file contents.
	 *
	 * @param	array	The souce data to save.
	 * @return	boolean	True on success, false otherwise and internal error set.
	 */
	public function save($data)
	{
		// Get the template.
		$template = $this->getTemplate();

		if (empty($template))
		{
			return false;
		}

		$fileName = $this->get('filename');
		$filePath = \Hubzero\Filesystem\Util::normalizePath($this->_template->path . '/' . $fileName);

		// Try to make the template file writeable.
		if (!Filesystem::setPermissions($filePath, '0644'))
		{
			$this->setError(Lang::txt('COM_TEMPLATES_ERROR_SOURCE_FILE_NOT_WRITABLE'));
			return false;
		}

		// Trigger the onExtensionBeforeSave event.
		$result = Event::trigger('extension.onExtensionBeforeSave', array('com_templates.source', &$data, false));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// [!] HUBZERO - Force line endings to be consistent with the server environment
		$data['source'] = preg_replace('~\R~u', PHP_EOL, $data['source']);

		$return = Filesystem::write($filePath, $data['source']);

		// Try to make the template file unwriteable.
		if (!Filesystem::setPermissions($filePath, '0444'))
		{
			$this->setError(Lang::txt('COM_TEMPLATES_ERROR_SOURCE_FILE_NOT_UNWRITABLE'));
			return false;
		}
		elseif (!$return)
		{
			$this->setError(Lang::txt('COM_TEMPLATES_ERROR_FAILED_TO_SAVE_FILENAME', $fileName));
			return false;
		}

		// Trigger the onExtensionAfterSave event.
		Event::trigger('extension.onExtensionAfterSave', array('com_templates.source', &$table, false));

		return true;
	}
}
