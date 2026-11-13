<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin;

use Hubzero\Document\Assets;
use Hubzero\Config\Registry;
use Hubzero\Facades\App;

/**
 * Base class for plugins to extend
 */
class Plugin
{
    use \Hubzero\Base\Traits\ErrorBag;
    use \Hubzero\Base\Traits\AssetAware;

    /**
     * Event object to observe.
     *
     * @var  object
     * @deprecated
     */
    protected $subject = null;

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
    protected $pluginName = null;

    /**
     * The plugin type
     *
     * @var  string
     */
    protected $pluginType = null;

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
    protected $autoloadLanguage = false;

    /**
     * The triggered event
     *
     * @var object|null
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
        $this->subject = &$subject;

        // Get the parameters.
        if (isset($config['params'])) {
            $this->params = $config['params'];
        }

        if (!($this->params instanceof Registry)) {
            $this->params = new Registry($this->params);
        }

        // Get the plugin name.
        if (isset($config['name'])) {
            $this->pluginName = $config['name'];
        }

        // Get the plugin type.
        if (isset($config['type'])) {
            $this->pluginType = $config['type'];
        }

        // @TODO: Remove this
        $this->option = (isset($config['type']) ? 'com_' . $config['type'] : 'com_' . $this->pluginType);

        // Load the language files if needed.
        if ($this->autoloadLanguage) {
            $this->loadLanguage('', PATH_APP);
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
        if (empty($extension)) {
            $extension = 'plg_' . $this->pluginType . '_' . $this->pluginName;
        }

        $lang = App::get('language');
        return $lang->load(strtolower($extension), $basePath, null, false, true)
            || $lang->load(strtolower($extension), PATH_APP
                . DS
                . 'plugins'
                . DS
                . $this->pluginType
                . DS
                . $this->pluginName, null, false, true)
            || $lang->load(strtolower($extension), PATH_CORE
                . DS
                . 'plugins'
                . DS
                . $this->pluginType
                . DS
                . $this->pluginName, null, false, true);
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
        $database = App::get('db');

        // load the params from databse
        $sql = "SELECT params FROM `#__extensions` WHERE folder="
            . $database->quote($folder)
            . " AND element="
            . $database->quote($name)
            . " AND enabled=1";
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
     * @return  View
     */
    public function view($layout = 'default', $name = '')
    {
        $view = new View([
            'folder'  => $this->pluginType,
            'element' => $this->pluginName,
            'name'    => ($name ?: $this->pluginName),
            'layout'  => ($layout ?: 'default'),
        ]);
        return $view;
    }
}
