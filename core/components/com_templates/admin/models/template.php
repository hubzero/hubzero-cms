<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesModelTemplate extends JModelLegacy
{
	protected $template = null;

	/**
	 * Internal method to get file properties.
	 *
	 * @param	string The base path.
	 * @param	string The file name.
	 * @return	object
	 * @since	1.6
	 */
	protected function getFile($path, $name)
	{
		$temp = new stdClass;

		if ($template = $this->getTemplate())
		{
			$temp->name   = $name;
			$temp->exists = file_exists($path . $name);
			$temp->id     = urlencode(base64_encode($template->extension_id . ':' . $name));
			return $temp;
		}
	}

	/**
	 * Method to get a list of all the files to edit in a template.
	 *
	 * @return	array	A nested array of relevant files.
	 * @since	1.6
	 */
	public function getFiles()
	{
		// Initialise variables.
		$result	= array();

		if ($template = $this->getTemplate())
		{
			$client = \Hubzero\Base\ClientManager::client($template->client_id); //JApplicationHelper::getClientInfo($template->client_id);
			$base   = ($template->protected ? PATH_CORE : PATH_APP).'/templates/'.$template->element;
			$path   = Filesystem::cleanPath($base.'/');
			$lang   = Lang::getRoot();

			// Load the core and/or local language file(s).
				$lang->load('tpl_' . $template->element, $path, null, false, true)
			||	$lang->load('tpl_' . $template->element, PATH_APP . '/bootstrap/' . (isset($client->alias) ? $client->alias : $client->name), null, false, true);

			// Check if the template path exists.
			if (is_dir($path))
			{
				$result['main'] = array();
				$result['css']  = array();
				$result['clo']  = array();
				$result['mlo']  = array();
				$result['html'] = array();

				// Handle the main PHP files.
				$result['main']['index']   = $this->getFile($path, 'index.php');
				$result['main']['error']   = $this->getFile($path, 'error.php');
				$result['main']['print']   = $this->getFile($path, 'component.php');
				$result['main']['offline'] = $this->getFile($path, 'offline.php');

				// Handle the CSS files.
				$files = Filesystem::files($path.'/css', '\.css$', true, true);

				foreach ($files as $file)
				{
					$file = str_replace($base.'/', '', $file);
					$result['css'][] = $this->getFile($path.'/css/', $file);
				}
			}
			else
			{
				$this->setError(Lang::txt('COM_TEMPLATES_ERROR_TEMPLATE_FOLDER_NOT_FOUND'));
				return false;
			}
		}

		return $result;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Load the User state.
		$pk = (int) Request::getInt('id');
		$this->setState('extension.id', $pk);

		// Load the parameters.
		$params = Component::params('com_templates');
		$this->setState('params', $params);
	}

	/**
	 * Method to get the template information.
	 *
	 * @return	mixed	Object if successful, false if not and internal error is set.
	 * @since	1.6
	 */
	public function &getTemplate()
	{
		if (empty($this->template))
		{
			// Initialise variables.
			$pk = $this->getState('extension.id');
			$db = $this->getDbo();
			$result = false;

			// Get the template information.
			$db->setQuery(
				'SELECT extension_id, client_id, element, protected' .
				' FROM #__extensions' .
				' WHERE extension_id = '.(int) $pk.
				'  AND type = '.$db->quote('template')
			);

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
				$this->template = false;
			}
			else
			{
				$this->template = $result;
			}
		}

		return $this->template;
	}

	/**
	 * Method to check if new template name already exists
	 *
	 * @return	boolean   true if name is not used, false otherwise
	 * @since	2.5
	 */
	public function checkNewName()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__extensions');
		$query->where('name = ' . $db->quote($this->getState('new_name')));
		$db->setQuery($query);
		return ($db->loadResult() == 0);
	}

	/**
	 * Method to check if new template name already exists
	 *
	 * @return	string     name of current template
	 * @since	2.5
	 */
	public function getFromName()
	{
		return $this->getTemplate()->element;
	}

	/**
	 * Method to check if new template name already exists
	 *
	 * @return	boolean   true if name is not used, false otherwise
	 * @since	2.5
	 */
	public function copy()
	{
		if ($template = $this->getTemplate())
		{
			$client = JApplicationHelper::getClientInfo($template->client_id);
			$fromPath = Filesystem::cleanPath($client->path.'/templates/'.$template->element.'/');

			// Delete new folder if it exists
			$toPath = $this->getState('to_path');
			if (Filesystem::exists($toPath))
			{
				if (!Filesystem::deleteDirectory($toPath))
				{
					Notify::warning(Lang::txt('COM_TEMPLATES_ERROR_COULD_NOT_WRITE'));
					return false;
				}
			}

			// Copy all files from $fromName template to $newName folder
			if (!Filesystem::copyDirectory($fromPath, $toPath) || !$this->fixTemplateName())
			{
				return false;
			}

			return true;
		}
		else
		{
			Notify::warning(Lang::txt('COM_TEMPLATES_ERROR_INVALID_FROM_NAME'));
			return false;
		}
	}

	/**
	 * Method to delete tmp folder
	 *
	 * @return	boolean   true if delete successful, false otherwise
	 * @since	2.5
	 */
	public function cleanup()
	{
		// Clear installation messages
		User::setState('com_installer.message', '');
		User::setState('com_installer.extension_message', '');

		// Delete temporary directory
		return Filesystem::deleteDirectory($this->getState('to_path'));

	}

	/**
	 * Method to rename the template in the XML files and rename the language files
	 *
	 * @return	boolean   true if successful, false otherwise
	 * @since	2.5
	 */
	protected function fixTemplateName()
	{
		// Rename Language files
		// Get list of language files
		$result = true;
		$files = Filesystem::files($this->getState('to_path'), '.ini', true, true);
		$newName = strtolower($this->getState('new_name'));
		$oldName = $this->getTemplate()->element;

		foreach ($files as $file)
		{
			$newFile = str_replace($oldName, $newName, $file);
			$result = Filesystem::move($file, $newFile) && $result;
		}

		// Edit XML file
		$xmlFile = $this->getState('to_path') . '/templateDetails.xml';
		if (Filesystem::exists($xmlFile))
		{
			$contents = Filesystem::read($xmlFile);
			$pattern[] = '#<name>\s*' . $oldName . '\s*</name>#i';
			$replace[] = '<name>'. $newName . '</name>';
			$pattern[] = '#<language(.*)' . $oldName . '(.*)</language>#';
			$replace[] = '<language${1}' . $newName . '${2}</language>';
			$contents = preg_replace($pattern, $replace, $contents);
			$result = Filesystem::write($xmlFile, $contents) && $result;
		}

		return $result;
	}

}
