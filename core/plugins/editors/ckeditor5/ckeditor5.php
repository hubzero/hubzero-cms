<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * CKEditor Plugin
 */
namespace Plugins\Editors\Ckeditor5;

use Hubzero\Plugin\Plugin;

defined('_HZEXEC_') or die;

/**
 * CKEditor Plugin
 */
class Ckeditor5 extends Plugin
{
    /**
     * Base path for editor files
     */
    protected $_basePath = 'core/plugins/editors/ckeditor5/assets/';

    /**
     * Method to handle the onInitEditor event.
     *  - Initialises the Editor
     *
     * @return  void
     */
    public function onInit()
    {
        Html::behavior('core');

        $bundle = __DIR__ . '/assets/js/ckeditor.js';

        // Version the bundle by its mtime, as the CKEditor 4 plugin does, so a
        // rebuilt editor is not shadowed by a browser cache
        $version = file_exists($bundle) ? '?v=' . filemtime($bundle) : '';

        Document::addScript(
            str_replace('/administrator', '', Request::base(true))
            . '/' . $this->_basePath . 'js/ckeditor.js' . $version
        );
    }

    /**
     * Copy editor content to form field.
     *
     * The bundle also syncs on form submit, so this is for callers that need
     * the textarea populated before then.
     *
     * @return  string
     */
    public function onSave()
    {
        return "if (window.HUB && HUB.Editor) { HUB.Editor.updateAllElements(); }\n";
    }

    /**
     * Encode a value for embedding in an inline <script> block
     *
     * The hex flags keep quotes, ampersands and angle brackets out of the
     * output, so the result is safe both inside the script and if the script
     * ends up in an HTML attribute.
     *
     * @param   mixed   $value
     * @return  string
     */
    private function _js($value)
    {
        return json_encode(
            $value,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
    }

    /**
     * Get the editor content.
     *
     * @param   string  $id  The id of the editor field.
     * @return  string
     */
    public function onGetContent($id)
    {
        return 'getEditorContent(' . $this->_js($id) . ");\n";
    }

    /**
     * Set the editor content.
     *
     * @param   string  $id    The id of the editor field.
     * @param   string  $html  The content to set.
     * @return  string
     */
    public function onSetContent($id, $html)
    {
        return 'setEditorContent(' . $this->_js($id) . ', ' . $this->_js($html) . ");\n";
    }

    /**
     * Inserts text
     *
     * jInsertEditorText() is provided by the editor abstraction layer, which is
     * loaded ahead of every editor plugin.
     *
     * @param   string  $id
     * @return  bool
     */
    public function onGetInsertMethod($id)
    {
        return true;
    }

    /**
     * Display the editor area.
     *
     * @param   string   $name     The control name.
     * @param   string   $content  The contents of the text area.
     * @param   string   $width    The width of the text area (px or %).
     * @param   string   $height   The height of the text area (px or %).
     * @param   int      $col      The number of columns for the textarea.
     * @param   int      $row      The number of rows for the textarea.
     * @param   boolean  $buttons  True and the editor buttons will be displayed.
     * @param   string   $id       An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
     * @param   string   $asset
     * @param   object   $author
     * @param   array    $params  Associative array of editor parameters.
     * @return  string
     */
    public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
    {
        // Make sure we have an id too
        if (empty($id)) {
            $id = $name;
        }

        $col = $col ?: 35;
        $row = $row ?: 10;

        // Optional image-upload wiring: a caller may pass an 'uploadUrl' (and a
        // 'token' form-field name for CSRF) in $params to enable the editor's
        // image-upload button. Pulled out here so they don't leak into the
        // <textarea> attributes below.
        $uploadUrl  = isset($params['uploadUrl']) ? $params['uploadUrl'] : '';
        $tokenField = isset($params['token']) ? $params['token'] : '';
        unset($params['uploadUrl'], $params['token']);

        // Optional file-browser wiring: callers pass the same fileBrowser* keys
        // the CKEditor 4 plugin reads. Pulled out here for the same reason.
        $browseUrl = '';
        foreach (array('fileBrowserImageBrowseUrl', 'fileBrowserBrowseUrl') as $key) {
            if (!empty($params[$key])) {
                $browseUrl = $params[$key];
                break;
            }
        }
        $browseWidth  = isset($params['fileBrowserWindowWidth']) ? intval($params['fileBrowserWindowWidth']) : 1200;
        $browseHeight = isset($params['fileBrowserWindowHeight']) ? intval($params['fileBrowserWindowHeight']) : 600;
        unset(
            $params['fileBrowserBrowseUrl'],
            $params['fileBrowserImageBrowseUrl'],
            $params['fileBrowserImageBrowseLinkUrl'],
            $params['fileBrowserUploadUrl'],
            $params['fileBrowserImageUploadUrl'],
            $params['fileBrowserWindowWidth'],
            $params['fileBrowserWindowHeight']
        );

        // The remaining behavioural parameters the CKEditor 4 plugin honours.
        // Like the file browser keys above, they are pulled out here so they
        // stop being rendered as <textarea> attributes.
        $startInSource    = (isset($params['startupMode']) && $params['startupMode'] == 'source');
        $sourceViewButton = !isset($params['sourceViewButton']) || $params['sourceViewButton'];
        $allowScriptTags  = !empty($params['allowScriptTags']);
        $editorHeight     = isset($params['height']) ? $params['height'] : '';
        $mentions  = (isset($params['mentions']) && is_array($params['mentions'])) ? $params['mentions'] : array();
        // Not 'wordcount': the CKEditor 4 plugin reads that key, and a caller
        // asking this editor for a limit must not silently reconfigure that one.
        $limits = (isset($params['limits']) && is_array($params['limits'])) ? $params['limits'] : array();
        unset(
            $params['startupMode'],
            $params['sourceViewButton'],
            $params['allowScriptTags'],
            $params['allowPhpTags'],
            $params['mentions'],
            $params['limits']
        );

        if (!isset($params['class'])) {
            $params['class'] = array();
        }
        if (!is_array($params['class'])) {
            //$params['class'] = array($params['class']);
            $cls = $params['class'];
            $params['class'] = array();
            foreach ($this->_split(' ', $cls) as $piece) {
                $params['class'][] = $piece;
            }
        }
        $params['class'][] = 'ckeditor-content';

        // Set default height to a rough approximation of the height
        // of the textarea (rows * 1.5em of 12px font)
        if (!isset($params['height'])) {
            $params['height'] = intval($row) . 'em';
        }

        // Editor options (image upload is enabled only when an uploadUrl is given)
        $opts = array();
        if ($uploadUrl !== '') {
            $opts['uploadUrl'] = $uploadUrl;
        }
        if ($tokenField !== '') {
            $opts['tokenField'] = $tokenField;
        }
        if ($browseUrl !== '') {
            $opts['fileBrowser'] = array(
                'url'    => $browseUrl,
                'width'  => $browseWidth,
                'height' => $browseHeight
            );
        }
        if ($startInSource) {
            $opts['startInSource'] = true;
        }
        if (!$sourceViewButton) {
            $opts['sourceViewButton'] = false;
        }
        if ($allowScriptTags) {
            $opts['allowScriptTags'] = true;
        }
        if ($editorHeight !== '') {
            $opts['minHeight'] = $editorHeight;
        }
        if ($mentions) {
            $opts['mentions'] = array_values($mentions);
        }
        if ($limits) {
            $opts['wordCount'] = $limits;
        }

        // The 'minimal' and 'images' class markers select a cut-down toolbar,
        // as they do for the CKEditor 4 plugin
        if (in_array('minimal', $params['class'])) {
            $opts['minimal'] = true;

            if (in_array('images', $params['class'])) {
                $opts['images'] = true;
            }
        }
        $optsJson = $this->_js($opts ? $opts : new \stdClass());
        $idJson   = $this->_js($id);

        // Script to actually make ckeditor (deferred until the element exists).
        // The created instance is registered with HUB.Editor so application code
        // can read and write it without knowing which editor is running.
        $script  = '<script type="text/javascript">';
        $script .= '(function(){var f=function(){if(!window.HubEditor){return;}';
        $script .= 'HubEditor.create(document.getElementById(' . $idJson . '), ' . $optsJson . ')';
        $script .= '.then(function(editor){if(!window.HUB||!HUB.Editor){return;}';
        $script .= 'HUB.Editor.register(' . $idJson . ',{';
        $script .= 'getData:function(){return editor.getData();},';
        $script .= 'setData:function(html){editor.setData(html);},';
        $script .= 'updateElement:function(){editor.updateSourceElement();},';
        $script .= 'instance:editor});';
        $script .= 'editor.on("destroy",function(){HUB.Editor.unregister(' . $idJson . ');});';
        $script .= '})';
        $script .= '.catch(function(e){console.error(e);});};';
        $script .= 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",f);}else{f();}})();';
        $script .= '</script>';

        $params['class'] = implode(' ', $params['class']);

        $atts = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = implode(';', $value);
            }
            $atts[] = $key . '="' . $value . '"';
        }

        // Couldn't find a better way to do this for the timebeing - CK5 documentation is lacking at the moment
        // Puts the style in multiple times and overwrites when there are multiple textareas
        $this->css('.ck-content { height: ' . $row . 'em; }');

        // Output html and script
        $editor  = '<textarea name="' . $name . '" id="' . $id . '" ' . implode(' ', $atts) . '>' . $content . '</textarea>' . $script;
        if (App::isAdmin()) {
            $editor .= $this->_displayButtons($id, $buttons, $asset, $author);
        }
        return $editor;
    }

    /**
     * Displays buttons
     *
     * @param   string  $name
     * @param   array   $buttons
     * @param   string  $asset
     * @param   string  $string
     * @return  string
     */
    private function _displayButtons($name, $buttons, $asset, $author)
    {
        // Load modal popup behavior
        Html::behavior('modal', 'a.modal-button');

        $return = '';
        $results[] = $this->onGetInsertMethod($name);

        foreach ($results as $result) {
            if (is_string($result) && trim($result)) {
                $return .= $result;
            }
        }

        if (is_array($buttons) || (is_bool($buttons) && $buttons)) {
            $results = $this->_subject->getButtons($name, $buttons, $asset, $author);

            // This will allow plugins to attach buttons or change the behavior on the fly using AJAX
            $return .= "\n<div id=\"editor-xtd-buttons\">\n";

            foreach ($results as $button) {
                // Results should be an object
                if ($button->get('name')) {
                    $modal   = ($button->get('modal')) ? ' class="modal-button"' : null;
                    $href    = ($button->get('link')) ? ' href="' . Request::base() . $button->get('link') . '"' : null;
                    $onclick = ($button->get('onclick')) ? ' onclick="' . $button->get('onclick') . '"' : 'onclick="return false;"';
                    $title   = ($button->get('title')) ? $button->get('title') : $button->get('text');
                    $return .= '<div class="button2-left"><div class="' . $button->get('name') . '"><a' . $modal . ' title="' . $title . '"' . $href . $onclick . ' rel="' . $button->get('options') . '">' . $button->get('text') . "</a></div></div>\n";
                }
            }

            $return .= "</div>\n";
        }

        return $return;
    }

    /**
     * Build a config object
     *
     * @param   string $delimiter
     * @param   string $input
     * @return  array
     */
    private function _split($delimiter, $input)
    {
        $even = array();

        if (is_array($input)) {
            foreach ($input as $el) {
                $even = array_merge($even, $this->_split($delimiter, $el));
            }
        } else {
            $pieces = explode($delimiter, $input);
            $pieces = array_map('trim', $pieces);

            $even = array_merge($even, $pieces);
        }
        return $even;
    }
}
