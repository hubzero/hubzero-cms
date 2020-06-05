<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Document;
use Request;
use Html;
use Lang;

/**
 * Form Field class for Domains.
 */
class Domains extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Domains';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		Html::behavior('framework', true);

		Document::addScript(Request::root(true) . '/core/plugins/user/domainrestriction/assets/js/domains.js');
		Document::addScript(Request::root(true) . '/core/plugins/user/domainrestriction/assets/js/base64.js');

		$strings = array();

		foreach (array('ADD','EDIT','REMOVE','INVALID','DUPLICATE') as $string)
		{
			$strings[$string]=Lang::txt('PLG_USER_DOMAINRESTRICTION_' . $string);
		}

		$value = base64_decode($this->value) ? trim(base64_decode($this->value)) : json_encode(explode("\n", trim(str_replace('*', '', $this->value))));

		$script   = array("jQuery(document).ready(function ($) {");
		$script[] = "var ".$this->id.'_domainsobject = Domains.initialize({';
		$script[] = implode(',', array("id:\"".$this->id."\"", "domains:".$value,"strings:".json_encode($strings)));
		$script[] = "})";
		$script[] = '});';

		Document::addScriptDeclaration(implode('', $script) . "\n");

		$html = array();
		$html[] = '<div class="input-modal">';
		$html[] = '<span class="input-cell">';
		$html[] = '<input type="text" id="'.$this->id.'-domain" />';
		$html[] = '<input id="'.$this->id.'" name="'.$this->name.'" type="hidden" value="'.base64_encode($value).'"/>';
		$html[] = '</span>';
		$html[] = '<span class="input-cell">';
		$html[] = '<button id="'.$this->id.'-save">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_ADD').'</button>';
		$html[] = '</span>';
		$html[] = '</div>';
		$html[] = '<br style="clear:both" />';
		$html[] = '<ul id="'.$this->id.'-list">';
		$html[] = '</ul>';
		$html[] = '<hr style="clear:both" />';

		return implode("\n", $html);
	}
}
