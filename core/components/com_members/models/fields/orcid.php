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

use Hubzero\Html\Builder\Behavior;
use Document;
use Route;
use Lang;

/**
 * Supports a URL text field
 */
class Orcid extends Text
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Orcid';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$attributes = array(
			'type'         => 'text',
			'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
			'name'         => $this->name,
			'id'           => $this->id,
			'size'         => ($this->element['size']      ? (int) $this->element['size']      : ''),
			'maxlength'    => ($this->element['maxlength'] ? (int) $this->element['maxlength'] : ''),
			'class'        => 'orcid' . ($this->element['class']     ? (string) $this->element['class']  : ''),
			'autocomplete' => ((string) $this->element['autocomplete'] == 'off' ? 'off'      : ''),
			'readonly'     => ((string) $this->element['readonly'] == 'true'    ? 'readonly' : ''),
			'disabled'     => ((string) $this->element['disabled'] == 'true'    ? 'disabled' : ''),
			'onchange'     => ($this->element['onchange']  ? (string) $this->element['onchange'] : '')
		);

		$attr = array();
		foreach ($attributes as $key => $value)
		{
			if ($key != 'value' && !$value)
			{
				continue;
			}

			$attr[] = $key . '="' . $value . '"';
		}
		$attr = implode(' ', $attr);

		$html = array();

		$html[] = '<div class="grid">';
		$html[] = '	<div class="col span9">';
		$html[] = '		<input ' . $attr . ' placeholder="####-####-####-####" />';
		$html[] = '		<input type="hidden" name="base_uri" id="base_uri" value="' . rtrim(Request::base(true), '/') . '" />';
		$html[] = '	</div>';
		$html[] = '	<div class="col span3 omega">';
		$html[] = '		<a class="btn button icon-search orcid-fetch" id="orcid-fetch" data-id="' . $this->id . '" href="' . Request::base() . '/' . Route::url('index.php?option=com_members&controller=orcid') . '">' . Lang::txt('COM_MEMBERS_PROFILE_ORCID_FIND') . '</a>';
		$html[] = '	</div>';
		$html[] = '</div>';
		$html[] = '<p><img src="' . Request::root()  . '/core/components/com_members/site/assets/img/orcid-logo.png" width="80" alt="ORCID" /> ' . Lang::txt('COM_MEMBERS_PROFILE_ORCID_ABOUT') . '</p>';

		Behavior::framework(true);
		Behavior::modal();

		/*App::get('document')->addScriptDeclaration("
			jQuery(document).ready(function($){
				if ($('.orcid-fetch').length > 0) {
					$('.orcid-fetch').on('click', function(e) {
						e.preventDefault();

						$.fancybox({
							type: 'iframe',
							width: 700,
							height: 'auto',
							autoSize: false,
							fitToView: false,
							titleShow: false,
							closeClick: false,
							helpers: { 
								overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
							},
							tpl: {
								wrap:'<div class=\"fancybox-wrap\"><div class=\"fancybox-skin\"><div class=\"fancybox-outer\"><div id=\"sbox-content\" class=\"fancybox-inner\"></div></div></div></div>'
							},
							beforeLoad: function() {
								var href = $(this).attr('href');
								if (href.indexOf('?') == -1) {
									href += '?tmpl=component';
								} else {
									href += '&tmpl=component';
								}
								href += '&return=1&fname=' + $(this).attr('data-fname') + '&lname=' + $(this).attr('data-lname')  + '&email=' + $(this).attr('data-email');
								$(this).attr('href', href);
							},
							afterClose: function() {
								HUB.Members.Profile.editReloadSections();
							}
						});
					});
				}
		");*/

		return implode($html);
	}
}
