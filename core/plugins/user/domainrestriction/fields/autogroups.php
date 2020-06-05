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
 * Form Field class for AutGroups.
 */
class AutoGroups extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'AutoGroups';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		Html::behavior('framework', true);

		Document::addScript(Request::root(true).'/core/plugins/user/domainrestriction/assets/js/bulk.js');
		Document::addScript(Request::root(true).'/core/plugins/user/domainrestriction/assets/js/autogroups.js');
		Document::addScript(Request::root(true).'/core/plugins/user/domainrestriction/assets/js/base64.js');
		Document::addScript(Request::root(true).'/core/plugins/user/domainrestriction/assets/js/Array.sortOn.js');

		$strings = array();
		foreach (array('ADD','EDIT','REMOVE','INVALID','DUPLICATE') as $string)
		{
			$strings[$string] = Lang::txt('PLG_USER_DOMAINRESTRICTION_' . $string);
		}

		// upgrading from a previous version of the plugin - this will convert the data
		$value = base64_decode($this->value);

		if (count(json_decode($value)))
		{
			$value = json_decode($value);

			if (!is_object($value[0]))
			{
				$newvalue = new stdClass;

				foreach ($value as $key => $domain)
				{
					$newvalue->domain = $domain[0];
					$newvalue->groups = $domain[1];

					$value[$key] = $newvalue;
				}
			}

			$value = json_encode($value);
		}

		$script   = array("jQuery(document).ready(function ($) {");
		$script[] = "var ".$this->id.'_autogroupsobject = AutoGroups.initialize({';
		$script[] = implode(',', array("id:\"".$this->id."\"", "autogroups:".$value, "strings:".trim(json_encode($strings))));
		$script[] = "})";
		$script[] = '});';

		Document::addScriptDeclaration(implode('', $script) . "\n");

		$html = array();
		$html[] = '<label for="'.$this->id.'-domain">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_OPTIONS_DOMAIN_LABEL').'</label>';
		$html[] = '<input type="text" id="'.$this->id.'-domain" />';
		$html[] = '<label for="'.$this->id.'-groups">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_OPTIONS_GROUPS_LABEL').'</label>';
		$html[] = Html::access('usergroup', $this->id.'-groups', '', 'multiple="multiple"', array()).'<br />';
		$html[] = '<button id="'.$this->id.'-save">'.Lang::txt('PLG_USER_DOMAINRESTRICTION_ADD').'</button>';
		$html[] = '<input id="'.$this->id.'" name="'.$this->name.'" type="hidden" value="'.base64_encode($value).'"/>';
		$html[] = '<br style="clear:both" />';
		$html[] = '<ul id="'.$this->id.'-list">';
		$html[] = '</ul>';
		$html[] = '<hr style="clear:both" />';

		return implode("\n", $html);
	}
}
