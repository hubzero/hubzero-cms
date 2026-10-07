<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin;

use Hubzero\Document\Assets;
use Hubzero\Config\Registry;
use Hubzero\Base\Obj;

/**
 * Base class for plugins to extend
 */
class Plugin extends Obj
{
	use \Hubzero\Base\Traits\AssetAware;

	/**
	 * Event object to observe.
	 *
	 * @var  object
	 * @deprecated
	 */
	protected $_subject = null;

	/**
	 * A Registry object holding the parameters for the plugin
	 *
	 * @var  Registry
	 */
	public $params = null;

	/**
	 * The name of the plugin
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * The plugin type
	 *
	 * @var  string
	 */
	protected $_type = null;

	/**
	 * Component this plugin might be associated with
	 * @TODO: Remove this
	 *
	 * @var   string
	 */
	public $option = null;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = false;

	/**
	 * The triggered event
	 *
	 * @var object
	 */
	public $event;

	/**
	 * Constructor
	 *
	 * @param   object  $subject  Event dispatcher
	 * @param   array   $config   Optional configurations to be used
	 * @return  void
	 */
	public function __construct($subject, $config)
	{
		// Set the subject to observe
		$this->_subject = &$subject;

		// Get the parameters.
		if (isset($config['params']))
		{
			$this->params = $config['params'];
		}

		if (!($this->params instanceof Registry))
		{
			$this->params = new Registry($this->params);
		}

		// Get the plugin name.
		if (isset($config['name']))
		{
			$this->_name = $config['name'];
		}

		// Get the plugin type.
		if (isset($config['type']))
		{
			$this->_type = $config['type'];
		}

		// @TODO: Remove this
		$this->option = (isset($config['type']) ? 'com_' . $config['type'] : 'com_' . $this->_type);

		// Load the language files if needed.
		if ($this->_autoloadLanguage)
		{
			$this->loadLanguage('', PATH_APP . DS . 'bootstrap' . DS . \App::get('client')->name);
		}
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = PATH_APP)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$lang = \App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Method to get plugin params
	 *
	 * @param   string  $name    Plugin name
	 * @param   string  $folder  Plugin folder
	 * @return  object
	 */
	public static function getParams($name, $folder)
	{
		$database = \App::get('db');

		// load the params from databse
		$sql = "SELECT params FROM `#__extensions` WHERE folder=" . $database->quote($folder) . " AND element=" . $database->quote($name) . " AND enabled=1";
		$database->setQuery($sql);
		$result = $database->loadResult();

		// params object
		$params = new Registry($result);
		return $params;
	}

	/**
	 * Create a plugin view and return it
	 *
	 * @param   string  $layout  View layout
	 * @param   string  $name    View name
	 * @return  object
	 */
	public function view($layout='default', $name='')
	{
		$view = new View(array(
			'folder'  => $this->_type,
			'element' => $this->_name,
			'name'    => ($name   ?: $this->_name),
			'layout'  => ($layout ?: 'default')
		));
		return $view;
	}
}
