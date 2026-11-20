<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Config\Registry;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Document;

/**
 * Hubzero helper class for retrieving the current wiki editor
 */
class Editor extends Obj
{
    /**
     * Editor Plugin object
     *
     * @var  object|null
     */
    private $editor = null;

    /**
     * Editor Plugin name
     *
     * @var  string|null
     */
    private $name = null;

    /**
     * Constructor
     *
     * @param  string $editor  The parser name
     */
    public function __construct($editor = '')
    {
        if (!$editor) {
            $database = App::get('db');
            $database->setQuery(
                "SELECT element FROM `#__extensions` WHERE folder='wiki' AND type='plugin' AND enabled=1 AND element "
                    . "LIKE 'editor%' ORDER BY enabled DESC LIMIT 1",
            );

            $editor = $database->loadResult();
        }
        $this->name = $editor;
    }

    /**
     * Returns a reference to a global Parser object, only creating it
     * if it doesn't already exist.
     *
     * This method must be invoked as:
     *     $parser = WikiHelperParser::getInstance($parsername);
     *
     * @param   string  $editor  The name of the parser to use.
     * @return  self  The Parser object.
     */
    public static function &getInstance($editor = '')
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
        }

        $signature = serialize($editor);

        if (empty($instances[$signature])) {
            $instances[$signature] = new self($editor);
        }

        return $instances[$signature];
    }

    /**
     * Initialize the parser
     *
     * @return  void
     */
    public function initialise()
    {
        $results = [];
        if (is_null($this->editor)) {
            return;
        }

        $return = '';
        $results[] = $this->callEditor('onInitEditor');

        foreach ($results as $result) {
            if (trim($result)) {
                $return = $result;
            }
        }

        if ($return) {
            Document::addCustomTag($return);
        }
    }

    /**
     * Present a text area
     *
     * @param   string   $name    The control name
     * @param   string   $id      The control id
     * @param   string   $html    The contents of the text area
     * @param   string   $cls     The width of the text area (px or %)
     * @param   int|string  $col     The number of columns for the textarea
     * @param   int|string  $row     The number of rows for the textarea
     * @param   array<mixed>    $params  Associative array of editor parameters
     * @return  mixed
     */
    public function display($name, $id, $html, $cls, $col, $row, $params = [])
    {
        $results = [];
        // Return a standard textarea if no editor is found
        if (!$this->name) {
            return '<textarea name="'
                . $name
                . '" id="'
                . $id
                . '" cols="'
                . $col
                . '" rows="'
                . $row
                . '" class="'
                . $cls
                . '">'
                . htmlspecialchars((string) $html, ENT_QUOTES, 'UTF-8')
                . '</textarea>'
                . "\n";
        }

        $this->load($params);

        // Check if editor is already loaded
        if (is_null($this->editor)) {
            return;
        }

        $return = null;

        $results[] = $this->callEditor('onDisplayEditor', [$name, $id, $html, $cls, $col, $row]);

        foreach ($results as $result) {
            if (trim($result)) {
                $return .= $result;
            }
        }
        return $return;
    }

    /**
     * Save the editor content
     *
     * @param   string  $editor  The name of the editor control
     * @return  string|null
     */
    public function save($editor)
    {
        $results = [];
        $this->load();

        if (is_null($this->editor)) {
            return null;
        }

        $return = '';
        $results[] = $this->callEditor('onSaveEditorContent', [$editor]);

        foreach ($results as $result) {
            if (trim($result)) {
                $return .= $result;
            }
        }

        return $return;
    }

    /**
     * Get the editor contents
     *
     * @param   string  $editor  The name of the editor control
     * @return  mixed
     */
    public function getContent($editor)
    {
        $results = [];
        $this->load();

        if (is_null($this->editor)) {
            return;
        }

        $return = '';
        $results[] = $this->callEditor('onGetEditorContent', [$editor]);

        foreach ($results as $result) {
            if (trim($result)) {
                $return .= $result;
            }
        }

        return $return;
    }

    /**
     * Set the editor contents
     *
     * @param   string  $editor  The name of the editor control
     * @param   string  $html    The contents of the text area
     * @return  mixed
     */
    public function setContent($editor, $html)
    {
        $results = [];
        $this->load();

        $args = [
            'name'  => $editor,
            'html'  => $html,
            'event' => 'onSetEditorContent',
        ];

        $return = '';
        $results[] = $this->callEditor('update', [$args]);
        foreach ($results as $result) {
            if (trim($result)) {
                $return .= $result;
            }
        }
        return $return;
    }

    /**
     * Run a hook of the editor plugin, if it has that hook
     *
     * Neither wiki editor has onSaveEditorContent(), onGetEditorContent() or
     * update(), so save(), getContent() and setContent() give nothing.
     *
     * @param   string        $hook       The hook's name
     * @param   array<mixed>  $arguments  What to pass it
     * @return  mixed  What the hook gives, or null when there is no such hook
     */
    private function callEditor($hook, array $arguments = [])
    {
        if ($this->editor === null) {
            return null;
        }

        $callback = [$this->editor, $hook];
        if (!is_callable($callback)) {
            return null;
        }

        return call_user_func_array($callback, $arguments);
    }

    /**
     * Load the editor
     *
     * @param   array<mixed>  $config  Associative array of editor config parameters
     * @return  void
     */
    private function load($config = [])
    {
        // Check if editor is already loaded
        if (!is_null($this->editor)) {
            return;
        }

        // Build the path to the needed editor plugin
        $name = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $this->name);
        $name = ltrim($name, '.');

        $path = PATH_APP . DS . 'plugins' . DS . 'wiki' . DS . $name . DS . $name . '.php';

        if (!is_file($path)) {
            $path = PATH_CORE . DS . 'plugins' . DS . 'wiki' . DS . $name . DS . $name . '.php';

            if (!is_file($path)) {
                throw new \Exception(Lang::txt('Cannot load the editor'), 500);
            }
        }

        // Require plugin file
        require_once $path;

        // Get the plugin
        $plugin = Plugin::byType('wiki', (string) $this->name);

        // Not published (byType() gives an empty array)
        if (!$plugin instanceof \stdClass) {
            throw new \Exception(Lang::txt('Cannot load the editor'), 500);
        }

        $params = new Registry($plugin->params);
        $params->merge($config);

        $plugin->params = $params;

        // Build editor plugin classname
        $name = 'Plugins\\Wiki\\' . ucfirst((string) $this->name) . '\\' . ucfirst((string) $this->name);

        $this->editor = new $name($this, (array) $plugin);

        // Load plugin parameters
        $this->initialise();
    }

    /**
     * Attach an observer object
     *
     * @param   object  $observer  An observer object to attach
     * @return  void
     */
    public function attach($observer) {}

    /**
     * Detach an observer object
     *
     * @param   object   $observer  An observer object to detach.
     * @return  void
     */
    public function detach($observer) {}
}
