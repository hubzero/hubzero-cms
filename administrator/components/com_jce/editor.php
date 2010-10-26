<?php
/**
 * @version		$Id: editor.php 119 2009-06-23 12:10:07Z happynoodleboy $
 * @package		JCE Admin Component
 * @copyright	Copyright (C) 2006 - 2009 Ryan Demmmer. All rights reserved.
 * @license		GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

class JContentEditorBridge extends JObject
{
    var $plugins;
    /**
     * Constructor activating the default information of the class
     *
     * @access	protected
     */
    function __construct($config = array ())
    {
		if ($this->plugins) {
			return $this->plugins;
		}
        $db = & JFactory::getDBO();

        $query = 'SELECT name'
        .' FROM #__jce_plugins'
        .' WHERE type = '.$db->Quote('plugin');

        $db->setQuery($query);
        $this->plugins = $db->loadResultArray();
    }
    /**
     * Returns a reference to a editor object
     *
     * This method must be invoked as:
     * 		<pre>  $bridge = &JContentEditorBridge::getInstance();</pre>
     *
     * @access	public
     * @return	The bridge object.
     * @since	1.5.7
     */
    function & getInstance()
    {
        static $instance;

        if (!is_object($instance)) {
            $instance = new JContentEditorBridge();
        }
        return $instance;
    }

    /**
     * Returns $plugins.
     * @see JContentEditorBridge::$plugins
     */
    function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Sets $plugins.
     * @param array $plugins
     * @see JContentEditorBridge::$plugins
     */
    function setPlugins($plugins)
    {
        $this->plugins = $plugins;
    }
	/**
	 * Check a plugin against the static list
	 * @return boolan
	 * @param string $plugin
	 */
	function checkPlugin($plugin)
	{
		return in_array($plugin, $this->getPlugins());
	}
    /**
     * Load Plugin files
     */
    function load()
    {
        $task = JRequest::getCmd('task');
		if ($task) {
			switch($task) {
	        case 'plugin':
	            $plugin = JRequest::getVar('plugin');
	            if ($this->checkPlugin($plugin)) {
	                $file = basename(JRequest::getVar('file'));
	                $path = JCE_PLUGINS.DS.$plugin;
	                if (is_dir($path) && file_exists($path.DS.$file.'.php')) {
	                    include_once $path.DS.$file.'.php';
	                } else {
	                    JError::raiseError(500, JText::_('File '.$file.' not found!'));
	                }
	            } else {
	                JError::raiseError(500, JText::_('Plugin not found!'));
	            }
	            exit ();
	        break;
	        case 'help':
	            $plugin = JRequest::getVar('plugin');
	            if ($this->checkPlugin($plugin)) {
	                jimport('joomla.application.component.view');
	                $help = new JView($config = array (
	                'base_path'=>JCE_LIBRARIES,
	                'layout'=>'help'
	                ));
	                $help->display();
	            } else {
	                JError::raiseError(500, JText::_('Plugin not found!'));
	            }
	            exit ();
	        break;
	    	}
		} else{
			JError::raiseError(500, JText::_('No Task'));
		}
        
	}
}
$bridge = & JContentEditorBridge::getInstance();
$bridge->load();
?>