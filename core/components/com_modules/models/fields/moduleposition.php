<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Form;
use Hubzero\Base\ClientManager;
use Hubzero\Facades\Document;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Html;

/**
 * Supports a modal article picker.
 */
class ModulePosition extends Text
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'ModulePosition';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        // Get the client id.
        $clientId = $element['client_id'];
        if (!isset($clientId)) {
            $clientName = $element['client'];
            if (isset($clientName)) {
                $client = ClientManager::client($clientName, true);
                $clientId = $client->id;
            }
        }
        if (!isset($clientId) && $this->form instanceof Form) {
            $clientId = $this->formValue('client_id');
        }
        $clientId = (int) $clientId;

        // Load the modal behavior script.
        Html::behavior('modal', 'a.modal');

        // Build the script.
        $script = [];
        $script[] = '	function jSelectPosition_' . $this->id . '(name) {';
        $script[] = '		$("#' . $this->id . '").val(name);';
        $script[] = '		$.fancybox.close();';
        $script[] = '	}';

        // Add the script to the document head.
        Document::addScriptDeclaration(implode("\n", $script));

        // Setup variables for display.
        $html = [];
        $link = 'index.php?option=com_modules&amp;task=positions&amp;tmpl=component'
            . '&amp;function=jSelectPosition_' . $this->id . '&amp;client_id=' . $clientId;

        // The current user display field.
        //$html[] = '<div class="fltlft">';
        $html[] = '<div class="input-modal">';
        $html[] = '<span class="input-cell">';
        $html[] = parent::getInput();
        $html[] = '</span>';

        // The user select button.
        $html[] = '<span class="input-cell">';
        $title = Lang::txt('COM_MODULES_CHANGE_POSITION_TITLE');
        $btnText = Lang::txt('COM_MODULES_CHANGE_POSITION_BUTTON');
        $html[] = '<a class="button modal" title="' . $title . '"  href="' . $link
            . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">' . $btnText . '</a>';
        $html[] = '</span>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
