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

namespace Hubzero\Form\Fields\Modal;

use Hubzero\Form\Field;
use Exception;
use Document;
use Session;
use Route;
use Lang;
use Html;
use App;

/**
 * Supports a modal article picker.
 */
class Article extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Modal_Article';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Load the modal behavior script.
		Html::behavior('modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectArticle_' . $this->id . '(id, title, catid, object) {';
		$script[] = '		$("#' . $this->id . '_id").val(id);';
		$script[] = '		$("#' . $this->id . '_name").val(title);';
		$script[] = '		$.fancybox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		Document::addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html = array();
		$link = Route::url('index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=jSelectArticle_' . $this->id . '&' . Session::getFormToken() . '=1');

		$db	= App::get('db');
		$db->setQuery(
			'SELECT title' .
			' FROM #__content' .
			' WHERE id = ' . (int) $this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg())
		{
			throw new Exception($error, 500);
		}

		if (empty($title))
		{
			$title = Lang::txt('COM_CONTENT_SELECT_AN_ARTICLE');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.
		$html[] = '<div class="input-modal">';
		$html[] = '  <span class="input-cell">';
		$html[] = '    <input type="text" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
		$html[] = '  </span>';

		// The user select button.
		$html[] = '  <span class="input-cell">';
		$html[] = '    <a class="modal button" title="' . Lang::txt('COM_CONTENT_CHANGE_ARTICLE') . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">' . Lang::txt('COM_CONTENT_CHANGE_ARTICLE_BUTTON') . '</a>';
		$html[] = '  </span>';
		$html[] = '</div>';

		// The active article id field.
		if (0 == (int)$this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

		return implode("\n", $html);
	}
}
