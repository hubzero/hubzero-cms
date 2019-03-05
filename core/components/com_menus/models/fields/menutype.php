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

use Components\Menus\Helpers\Menus as MenusHelper;
use Filesystem;
use Component;
use Document;
use Route;
use Html;
use Lang;
use App;

/**
 * Form Field class
 */
class Menutype extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'menutype';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialise variables.
		$html     = array();
		$recordId = (int) $this->form->getValue('id');
		$size     = ($v = $this->element['size']) ? ' size="'.$v.'"' : '';
		$class    = ($v = $this->element['class']) ? ' class="'.$v.'"' : 'class="text_area"';

		// Get a reverse lookup of the base link URL to Title
		$model = new \Components\Menus\Models\Menutype();
		$rlu = $model->getReverseLookup();

		switch ($this->value)
		{
			case 'url':
				$value = Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL');
				break;

			case 'alias':
				$value = Lang::txt('COM_MENUS_TYPE_ALIAS');
				break;

			case 'separator':
				$value = Lang::txt('COM_MENUS_TYPE_SEPARATOR');
				break;

			default:
				$link = $this->form->getValue('link');
				// Clean the link back to the option, view and layout
				$value = Lang::txt(\Hubzero\Utility\Arr::getValue($rlu, MenusHelper::getLinkKey($link)));
				break;
		}
		// Load the javascript and css
		Html::behavior('framework');
		Html::behavior('modal');

		Document::addScriptDeclaration("
			jQuery(document).ready(function($){
				$('input.modal').fancybox({
					arrows: false,
					type: 'iframe',
					autoSize: false,
					fitToView: false,
					width: 600,
					height: 450,
					href: '" . Route::url('index.php?option=com_menus&view=menutypes&tmpl=component&recordId='.$recordId, false) . "'
				});
			});
		");

		$html[] = '<div class="input-modal">';
		$html[] = '<span class="input-cell">';
		$html[] = '<input type="text" id="'.$this->id.'" readonly="readonly" disabled="disabled" value="'.$value.'"'.$size.$class.' />';
		$html[] = '</span><span class="input-cell">';
		$html[] = '<input type="button" class="modal" value="'.Lang::txt('JSELECT').'" />';
		$html[] = '<input type="hidden" name="'.$this->name.'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" />';
		$html[] = '</span>';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
