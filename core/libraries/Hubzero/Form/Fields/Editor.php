<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Editor as Wysiwyg;
use Hubzero\Facades\App;

/**
 * An editarea field for content creation
 */
class Editor extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'Editor';

    /**
     * The Editor object.
     *
     * @var  object|null
     */
    protected $editor = null;

    /**
     * Method to get the field input markup for the editor area
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        // Initialize some field attributes.
        $rows = (int) $element['rows'];
        $cols = (int) $element['cols'];
        $height = ((string) $element['height']) ? (string) $element['height'] : '250';
        $width = ((string) $element['width']) ? (string) $element['width'] : '100%';
        $assetField = $element['asset_field'] ? (string) $element['asset_field'] : 'asset_id';
        $authorField = $element['created_by_field'] ? (string) $element['created_by_field'] : 'created_by';
        $asset = $this->formValue($assetField)
            ? $this->formValue($assetField)
            : (string) $element['asset_id'];
        // Build the buttons array.
        $buttons = (string) $element['buttons'];

        if ($buttons == 'true' || $buttons == 'yes' || $buttons == '1') {
            $buttons = true;
        } elseif ($buttons == 'false' || $buttons == 'no' || $buttons == '0') {
            $buttons = false;
        } else {
            $buttons = explode(',', $buttons);
        }

        $hide = ((string) $element['hide']) ? explode(',', (string) $element['hide']) : [];

        // Get an editor object.
        $editor = $this->getEditor();

        return (string) $editor->display(
            $this->name,
            htmlspecialchars($this->value ?: '', ENT_COMPAT, 'UTF-8'),
            $width,
            $height,
            $cols,
            $rows,
            $buttons ? (is_array($buttons) ? array_merge($buttons, $hide) : $hide) : false,
            $this->id,
            $asset,
            $this->formValue($authorField),
        );
    }

    /**
     * Method to get a Editor object based on the form field.
     *
     * @return  \Hubzero\Html\Editor  The Editor object.
     */
    protected function getEditor()
    {
        $fieldElement = $this->fieldElement();

        // Only create the editor if it is not already created.
        if (empty($this->editor)) {
            // Initialize variables.
            $editor = null;

            // Get the editor type attribute. Can be in the form of: editor="desired|alternative".
            $type = trim((string) $fieldElement['editor']);

            if ($type) {
                // Get the list of editor types.
                $types = explode('|', $type);

                // Get the database object.
                $db = App::get('db');

                // Iterate over teh types looking for an existing editor.
                foreach ($types as $element) {
                    // Build the query.
                    $query = $db
                        ->getQuery()
                        ->select('element')
                        ->from('#__extensions')
                        ->whereEquals('element', $element)
                        ->whereEquals('folder', 'editors')
                        ->whereEquals('enabled', '1')
                        ->limit(1)
                        ->start(0);

                    // Check of the editor exists.
                    $db->setQuery($query->toString());
                    $editor = $db->loadResult();

                    // If an editor was found stop looking.
                    if ($editor) {
                        break;
                    }
                }
            }

            // Create the Editor instance based on the given editor.
            $this->editor = $editor ? Wysiwyg::getInstance($editor) : App::get('editor');
        }

        return $this->editor;
    }

    /**
     * Method to get the Editor output for an onSave event.
     *
     * @return  string  The Editor object output.
     */
    public function save()
    {
        return (string) $this->getEditor()->save($this->id);
    }
}
