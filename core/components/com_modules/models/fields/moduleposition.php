<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
