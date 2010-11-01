<?php
/**
* @version		$Id: helper.php 16503 2010-04-26 17:17:33Z dextercowley $
* @package		Joomla.Framework
* @subpackage	Plugin
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
* Plugin helper class
*
* @static
* @package		Joomla.Framework
* @subpackage	Plugin
* @since		1.5
*/
class JPluginHelper
{
	/**
	 * Get the plugin data of a specific type if no specific plugin is specified
	 * otherwise only the specific plugin data is returned
	 *
	 * @access public
	 * @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
	 * @param string 	$plugin	The plugin name
	 * @return mixed 	An array of plugin data objects, or a plugin data object
	 */
	function &getPlugin($type, $plugin = null)
	{
		$result = array();

		$plugins = JPluginHelper::_load();

		$total = count($plugins);
		for($i = 0; $i < $total; $i++)
		{
			if(is_null($plugin))
			{
				if($plugins[$i]->type == $type) {
					$result[] = $plugins[$i];
				}
			}
			else
			{
				if($plugins[$i]->type == $type && $plugins[$i]->name == $plugin) {
					$result = $plugins[$i];
					break;
				}
			}

		}

		return $result;
	}

	/**
	 * Checks if a plugin is enabled
	 *
	 * @access	public
	 * @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
	 * @param string 	$plugin	The plugin name
	 * @return	boolean
	 */
	function isEnabled( $type, $plugin = null )
	{
		$result = &JPluginHelper::getPlugin( $type, $plugin);
		return (!empty($result));
	}

	/**
	* Loads all the plugin files for a particular type if no specific plugin is specified
	* otherwise only the specific pugin is loaded.
	*
	* @access public
	* @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
	* @param string 	$plugin	The plugin name
	* @return boolean True if success
	*/
	function importPlugin($type, $plugin = null, $autocreate = true, $dispatcher = null)
	{
		$result = false;

		$plugins = JPluginHelper::_load();

		$total = count($plugins);
		for($i = 0; $i < $total; $i++) {
			if($plugins[$i]->type == $type && ($plugins[$i]->name == $plugin ||  $plugin === null)) {
				JPluginHelper::_import( $plugins[$i], $autocreate, $dispatcher );
				$result = true;
			}
		}

		return $result;
	}

	/**
	 * Loads the plugin file
	 *
	 * @access private
	 * @return boolean True if success
	 */
	function _import( &$plugin, $autocreate = true, $dispatcher = null )
	{
		static $paths;
		static $shutdown_handler_installed;
		$mainframe =& JFactory::getApplication();

		if (!$paths) {
			$paths = array();
		}
	
		// Install shutdown handler if not installed yet
		if(!$shutdown_handler_installed)
		{
			// only register the shutdown function if we are capable of checking the errors (reqs PHP 5.2+)
			if (version_compare("5.2", phpversion(), "<="))
			{
				// you can only register a static method if it is declared static
				// we can't declare static b/c it breaks on PHP4
				// therefore we instantiate the helper for this one purpose
				$pluginHelper = new JPluginHelper;
				register_shutdown_function(array($pluginHelper, 'shutdown'));
			}
			// we may not have installed the handler, but setting this to true
			// will prevent us from continually running the version compare
			$shutdown_handler_installed = true;
		}

		$result	= false;
		$plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
		$plugin->name  = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

		$path	= JPATH_PLUGINS.DS.$plugin->type.DS.$plugin->name.'.php';

		if (!isset( $paths[$path] ))
		{
			if (file_exists( $path ))
			{
				//needed for backwards compatibility
				global $_MAMBOTS, $mainframe;

				jimport('joomla.plugin.plugin');
				$mainframe->set('currentPlugin', $plugin);
				require_once( $path );
				$paths[$path] = true;

				if($autocreate)
				{
					// Makes sure we have an event dispatcher
					if(!is_object($dispatcher)) {
						$dispatcher = & JDispatcher::getInstance();
					}

					$className = 'plg'.$plugin->type.$plugin->name;
					if(class_exists($className))
					{
						// load plugin parameters
						$plugin =& JPluginHelper::getPlugin($plugin->type, $plugin->name);

						// create the plugin
						$instance = new $className($dispatcher, (array)($plugin));
						
					}
				}
				$mainframe->set('currentPlugin', NULL);
			}
		}
	}

	/**
	 * Loads the published plugins
	 *
	 * @access private
	 */
	function _load()
	{
		static $plugins;

		if (isset($plugins)) {
			return $plugins;
		}

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		if (isset($user))
		{
			$aid = $user->get('aid', 0);

			$query = 'SELECT folder AS type, element AS name, params'
				. ' FROM #__plugins'
				. ' WHERE published >= 1'
				. ' AND access <= ' . (int) $aid
				. ' ORDER BY ordering';
		}
		else
		{
			$query = 'SELECT folder AS type, element AS name, params'
				. ' FROM #__plugins'
				. ' WHERE published >= 1'
				. ' ORDER BY ordering';
		}

		$db->setQuery( $query );

		if (!($plugins = $db->loadObjectList())) {
			JError::raiseWarning( 'SOME_ERROR_CODE', "Error loading Plugins: " . $db->getErrorMsg());
			return false;
		}

		return $plugins;
	}

	/**
	 * Shutdown handler called by PHP when executing plugin produces a fatal error
	 * Only runs in PHP 5.2+, don't call it without checking version first
	 *
	 * @access public
	 */
	function shutdown()
	{
		global $mainframe;
		$currentPlugin = $mainframe->get('currentPlugin', NULL);

		if($currentPlugin)
		{
			$error = error_get_last();
			if($error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_COMPILE_ERROR)
			{

				$disabled = false;
				$cfg =& JFactory::getConfig();

				/* If not in debug mode, attempt to disable the plugin */
				if(!$cfg->getValue('config.debug'))
				{
					$db =& JFactory::getDBO();
					$q = 'UPDATE #__plugins SET `published`=0 WHERE `folder`=' . $db->quote($currentPlugin->type) . 
						 'AND `element`=' . $db->quote($currentPlugin->name) .
						 'LIMIT 1';
					$db->setQuery($q);
					$disabled = $db->query();

					/* Following code is based on com_weblinks */

					// admin users gid
					$gid = 25;

					// list of admins
					$query = 'SELECT email, name' .
							' FROM #__users' .
							' WHERE gid = ' . $gid .
							' AND sendEmail = 1';
					$db->setQuery($query);
					if ($db->query()) 
					{
						$adminRows = $db->loadObjectList();
						$mail =& JFactory::getMailer();

						// send email notification to admins
						foreach ($adminRows as $adminRow) 
						{
							$mail->addRecipient($adminRow->email, $adminRow->name);
						}

						$uri = JURI::getInstance();
						$mail->setSubject(JText::sprintf('MAIL_MSG_ADMIN_ERROR_SUBJECT'), $uri->getHost());

						$body = JText::sprintf(
							'MAIL_MSG_ADMIN_ERROR', 
							JURI::current(), $currentPlugin->type, $currentPlugin->name);
						$body .= "\n";
						$body .= "\n" . $error['message'];
						$body .= "\n" . $error['file'] . ' : ' . $error['line'];
						$mail->setBody($body);

						$mail->send();
					}
				}

				if($disabled)
				{
					$app = JFactory::getApplication();
					$app->redirect(JURI::current());
				}
				else
				{
					JError::raise(
							$error['type'], 
							500, 
							JText::sprintf('Error loading %s plugin "%s"', $currentPlugin->type, $currentPlugin->name),
							JText::sprintf('%s : %d', $error['file'], $error['line']), 
							$currentPlugin
							);
				}
			}
		}
	}
}
