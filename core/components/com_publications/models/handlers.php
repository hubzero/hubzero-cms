<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;
use Filesystem;
use Hubzero\Facades\Component;

/**
 * Publications handlers class
 *
 */
class Handlers extends Obj
{
    /**
     * Database
     *
     * @var \Hubzero\Database\Driver
     */
    public $db = null;

    /**
     * Loaded elements
     *
     * @var  array<string, Handler>
     */
    protected $types = [];

    /**
     * Directories, where handlers can be stored
     *
     * @var  array<mixed>
     */
    protected $path = [];

    /**
     * Configs
     *
     * @var  mixed
     */
    protected $configs = null;

    /**
     * Editor
     *
     * @var  mixed
     */
    public $editor = null;

    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        $this->db     = $db;
        $this->path[] = dirname(__FILE__) . DS . 'handlers';
    }

    /**
     * Show handler selection
     *
     * @param   \Components\Publications\Models\Publication  $pub
     * @param   integer  $elementid
     * @param   array<mixed>    $handlers
     * @param   Handler|string|null   $handler  A handler, or the name of one
     * @param   array<mixed>    $attachments
     * @param   string|null   $props  Block element key (not used)
     * @return  string
     */
    public function showHandlers($pub, $elementid, $handlers, $handler, $attachments, $props = null)
    {
        $html = '';

        // TBD - Get handler configs from pub/type manifest
        $this->configs = isset($pub->_curationModel->handlers) ? $pub->_curationModel->handlers : null;

        // We have a forced handler
        if ($handler) {
            if (!($handler instanceof Handler)) {
                $handler = $this->ini($handler);
            }
            if ($handler) {
                $html  = '<div class="handler-controls">';
                $html .= $this->drawSelectedHandler($handler);
                $html .= '</div>';
            }
        } elseif ($handlers) {
            // TEMP
            return '';
        }

        return $html;
    }

    /**
     * Update handler status / perform handler action
     *
     * @param   Handler   $handler
     * @param   \Components\Publications\Models\Publication  $pub
     * @param   integer  $elementId
     * @param   string   $action
     * @return  bool|null
     */
    public function update($handler, $pub, $elementId = 0, $action = '')
    {
        if (!$action) {
            return false;
        }
        // TBD
        return null;
    }

    /**
     * Load content editor for handler
     *
     * @param   Handler   $handler
     * @param   \Components\Publications\Models\Publication  $pub
     * @param   integer  $elementId
     * @return  \Components\Publications\Models\Editor
     */
    public function loadEditor($handler, $pub, $elementId = 0)
    {
        // Get handler configs
        $configs = $handler->get('_configs');
        if (!$configs) {
            $configs = $handler->getConfig();
        }

        // Start editor
        $editor = new \Components\Publications\Models\Editor($handler, $configs);

        // Make sure we have attachments
        if (!isset($pub->_attachments)) {
            // Get attachments
            $pContent = new \Components\Publications\Tables\Attachment($this->_parent->db);
            $pub->_attachments = $pContent->sortAttachments($pub->version_id);
        }

        // Sort out attachments for this element
        $attachments = $pub->_attachments;
        $attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;

        // Set editor properties
        $editor->set('pub', $pub);
        $editor->set('attachments', $attachments);
        $editor->set('elementId', $elementId);

        // Check if assigned and get association record
        $objAssoc = new \Components\Publications\Tables\HandlerAssoc($this->db);
        $association = $objAssoc->getAssociation($pub->version_id, $elementId, $handler->get('_name'));
        $editor->set('assoc', $association);

        // Check status
        $editor->set('configured', $association && $association->params ? true : false);
        $editor->set('assigned', $association ? true : false);
        $editor->set('relevant', self::isRelevant($handler, $attachments));

        // Check for changes
        // TBD

        return $editor;
    }

    /**
     * Check if handler applies to selection
     *
     * @param   Handler   $handler
     * @param   array<mixed>    $attachments
     * @return  boolean
     */
    public function isRelevant($handler, $attachments)
    {
        // Get handler configs
        $configs = $handler->get('_configs');
        if (!$configs) {
            $configs = $handler->getConfig();
        }

        // Attachments are needed
        if (!$attachments) {
            return false;
        }
        // Check allowed formats
        if (!self::checkAllowed($attachments, $configs->params)) {
            return false;
        }
        // Check required formats
        if (!self::checkRequired($attachments, $configs->params)) {
            return false;
        }
        // Any additional custom checks
        if (!$handler->checkRequired($attachments)) {
            return false;
        }

        return true;
    }

    /**
     * Check for allowed formats
     *
     * @param   array<mixed>    $attachments
     * @param   \stdClass   $params
     * @return  boolean
     */
    public function checkAllowed($attachments, $params)
    {
        if (empty($attachments)) {
            return true;
        }
        $formats  = $params->allowed_ext;
        $min      = $params->min_allowed;
        $max      = $params->max_allowed;
        $min      = $min ? $min : 1;
        $enforced = isset($params->enforced) ? $params->enforced : 0;

        if (empty($formats)) {
            return true;
        }

        $i = 0;
        $b = 0;
        foreach ($attachments as $attach) {
            // Skip non-file attachments
            if ($attach->type != 'file') {
                continue;
            }
            $file = isset($attach->path) ? $attach->path : $attach;
            $ext = \Components\Projects\Helpers\Html::getFileExtension($file);

            if ($ext && in_array(strtolower($ext), $formats)) {
                $i++;
            } else {
                $b++;
            }
        }
        if ($enforced == 1 && $b > 0) {
            return false;
        }

        return ($i >= $min && ($max && $i <= $max)) ? true : false;
    }

    /**
     * Check for required formats
     *
     * @param   array<mixed>    $attachments
     * @param   \stdClass   $params
     * @return  boolean
     */
    public function checkRequired($attachments, $params)
    {
        if (empty($attachments)) {
            return true;
        }
        $formats = $params->required_ext;

        if (empty($formats)) {
            return true;
        }

        $i = 0;
        foreach ($attachments as $attach) {
            $file = isset($attach->path) ? $attach->path : $attach;
            $ext = \Components\Projects\Helpers\Html::getFileExtension($file);

            if ($ext && in_array(strtolower($ext), $formats)) {
                $i++;
            }
        }

        if ($i < count($formats)) {
            return false;
        }

        return true;
    }

    /**
     * Side controls for handler
     *
     * @param   Handler   $handler
     * @param   boolean|null  $assigned
     * @return  string   HTML
     */
    public function drawSelectedHandler($handler, $assigned = null)
    {
        $configs = $handler->get('_configs');
        if (!$configs) {
            $configs = $handler->getConfig();
        }

        $view = new \Hubzero\Component\View([
            'base_path' => Component::path('com_publications') . DS . 'site',
            'name'   => 'handlers',
            'layout' => '_selected',
        ]);
        $view->set('handler', $handler);
        $view->set('configs', $configs);
        $view->set('assigned', $assigned);
        return $view->loadTemplate();
    }

    /**
     * Initialize
     *
     * @param   string  $name
     * @return  Handler|false
     */
    public function ini($name)
    {
        // Load
        $handler = $this->loadHandler($name);

        if ($handler === false) {
            return false;
        }

        return $handler;
    }

    /**
     * Loads a handler
     *
     * @param   string   $name
     * @param   boolean  $new
     * @return  Handler|false
     */
    public function loadHandler($name, $new = false)
    {
        $signature = md5($name);

        if (isset($this->types[$signature]) && $new === false) {
            return $this->types[$signature];
        }

        $elementClass = '\Components\Publications\Models\Handlers\\' . ucfirst($name);
        if (!is_a($elementClass, Handler::class, true)) {
            return false;
        }

        $this->types[$signature] = new $elementClass($this);
        return $this->types[$signature];
    }

    /**
     * Get params for the handler
     *
     * @param   string  $name
     * @param   array<mixed>   $configs
     * @param   array<mixed>   $savedConfig
     * @return  array<mixed>
     */
    public function parseConfig($name, $configs = [], $savedConfig = [])
    {
        // Get custom config from db
        if (!$savedConfig) {
            $obj = new \Components\Publications\Tables\Handler($this->db);
            $savedConfig = $obj->getConfig($name);
        }

        // Overwrite default config with custom
        if ($savedConfig) {
            foreach ($configs as $configName => $configValue) {
                if ($configName == 'params') {
                    foreach ($configValue as $paramName => $paramValue) {
                        $savedParam = $savedConfig['params'][$paramName] ?? null;
                        $configs['params'][$paramName] = $savedParam ?: $paramValue;
                    }
                } else {
                    $savedValue = $savedConfig[$configName] ?? null;
                    $configs[$configName] = $savedValue ?: $configValue;
                }
            }
        }

        return $configs;
    }
}
