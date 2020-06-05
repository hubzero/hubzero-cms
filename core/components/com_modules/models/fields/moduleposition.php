<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form;
use Hubzero\Form\Fields\Text;
use Hubzero\Base\ClientManager;
use Document;
use Lang;
use Html;

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
		// Get the client id.
		$clientId = $this->element['client_id'];
		if (!isset($clientId))
		{
			$clientName = $this->element['client'];
			if (isset($clientName))
			{
				$client = ClientManager::client($clientName, true);
				$clientId = $client->id;
			}
		}
		if (!isset($clientId) && $this->form instanceof Form)
		{
			$clientId = $this->form->getValue('client_id');
		}
		$clientId = (int) $clientId;

		// Load the modal behavior script.
		Html::behavior('modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectPosition_'.$this->id.'(name) {';
		$script[] = '		$("#'.$this->id.'").val(name);';
		$script[] = '		$.fancybox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		Document::addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html = array();
		$link = 'index.php?option=com_modules&amp;task=positions&amp;tmpl=component&amp;function=jSelectPosition_'.$this->id.'&amp;client_id='.$clientId;

		// The current user display field.
		//$html[] = '<div class="fltlft">';
		$html[] = '<div class="input-modal">';
		$html[] = '<span class="input-cell">';
		$html[] = parent::getInput();
		$html[] = '</span>';

		// The user select button.
		$html[] = '<span class="input-cell">';
		$html[] = '<a class="button modal" title="'.Lang::txt('COM_MODULES_CHANGE_POSITION_TITLE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.Lang::txt('COM_MODULES_CHANGE_POSITION_BUTTON').'</a>';
		$html[] = '</span>';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
