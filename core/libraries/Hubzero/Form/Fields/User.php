<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\User\User as UserModel;
use Hubzero\Html\Builder\Behavior;
use Hubzero\Facades\App;
use Hubzero\Facades\Document;

/**
 * Field to select a user id from a modal list.
 */
class User extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'User';

    /**
     * Method to get the user field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        // Initialize variables.
        $html = array();
        $groups = $this->getGroups();
        $excluded = $this->getExcluded();
        $link = 'index.php?option=com_members&amp;layout=modal'
            . '&amp;tmpl=component&amp;field=' . $this->id
            . (isset($groups)
                ? ('&amp;groups=' . base64_encode(json_encode($groups)))
                : '')
            . (isset($excluded)
                ? ('&amp;excluded=' . base64_encode(json_encode($excluded)))
                : '');

        // Initialize some field attributes.
        $attr  = $this->element['class']
            ? ' class="' . (string) $this->element['class'] . '"'
            : '';
        $attr .= $this->element['size']
            ? ' size="' . (int) $this->element['size'] . '"'
            : '';

        // Load the current username if available.
        if ($this->value) {
            $model = UserModel::oneOrNew($this->value);
        } else {
            $model = UserModel::blank();
            $model->set(
                'name',
                App::get('language')->txt('JLIB_FORM_SELECT_USER')
            );
        }

        $userName = htmlspecialchars(
            $model->get('name'),
            ENT_COMPAT,
            'UTF-8'
        );
        $changeTxt = App::get('language')->txt('JLIB_FORM_CHANGE_USER');
        $isDaisyui = Document::getCssFramework() === 'daisyui';

        if ($isDaisyui) {
            // Blade mode: use data-picker-url + data-parent-callback
            // bridge (admin.js openPickerPopup registers the
            // jSelectUser_* callback dynamically).
            Behavior::picker();

            if ($this->element['readonly'] != 'true') {
                $html[] = '<div class="input-modal">';
                $html[] = '<span class="input-cell">';
            }
            $html[] = '<input type="text" id="' . $this->id . '_name"'
                . ' value="' . $userName . '" disabled="disabled"'
                . $attr . ' />';
            if ($this->element['readonly'] != 'true') {
                $html[] = '</span><span class="input-cell">';
                $html[] = '<button type="button" class="button"'
                    . ' data-picker-url="' . $link . '"'
                    . ' data-picker-width="800"'
                    . ' data-picker-height="500"'
                    . ' data-value-field="' . $this->id . '_id"'
                    . ' data-display-field="' . $this->id . '_name"'
                    . ' data-picker-callback="jSelectUser_'
                    . $this->id . '">'
                    . $changeTxt . '</button>';
                $html[] = '</span></div>';
            }
        } else {
            // Legacy mode: FancyBox modal + inline callback script.
            $onchange = (string) $this->element['onchange'];

            Behavior::modal('a.modal_' . $this->id);

            $script = array();
            $script[] = '	function jSelectUser_'
                . $this->id . '(id, title) {';
            $script[] = '		var old_id = document.getElementById("'
                . $this->id . '_id").value;';
            $script[] = '		if (old_id != id) {';
            $script[] = '			document.getElementById("'
                . $this->id . '_id").value = id;';
            $script[] = '			document.getElementById("'
                . $this->id . '_name").value = title;';
            $script[] = '			' . $onchange;
            $script[] = '		}';
            $script[] = '		$.fancybox.close();';
            $script[] = '	}';

            App::get('document')->addScriptDeclaration(
                implode("\n", $script)
            );

            if ($this->element['readonly'] != 'true') {
                $html[] = '<div class="input-modal">';
                $html[] = '<span class="input-cell">';
            }
            $html[] = '<input type="text" id="' . $this->id . '_name"'
                . ' value="' . $userName . '" disabled="disabled"'
                . $attr . ' />';
            if ($this->element['readonly'] != 'true') {
                $html[] = '</span><span class="input-cell">';
                $html[] = '<a class="button modal_' . $this->id
                    . '" title="' . $changeTxt . '"'
                    . ' href="' . $link . '"'
                    . ' rel="{handler: \'iframe\','
                    . ' size: {x: 800, y: 500}}">'
                    . $changeTxt . '</a>';
                $html[] = '</span></div>';
            }
        }

        // Create the real field, hidden, that stores the user id.
        $html[] = '<input type="hidden" id="' . $this->id . '_id"'
            . ' name="' . $this->name . '"'
            . ' value="' . (int) $this->value . '" />';

        return implode("\n", $html);
    }

    /**
     * Method to get the filtering groups (null means no filtering)
     *
     * @return  mixed  array of filtering groups or null.
     */
    protected function getGroups()
    {
        return null;
    }

    /**
     * Method to get the users to exclude from the list of users
     *
     * @return  mixed  Array of users to exclude or null to to not exclude them
     */
    protected function getExcluded()
    {
        return null;
    }
}
