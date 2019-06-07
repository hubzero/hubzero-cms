<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		// Build the ORCID Create or Connect hyperlink
		$config = Component::params('com_members');
		$srv = $config->get('orcid_service', 'members');
		$clientID = $config->get('orcid_' . $srv . '_client_id', '');
		$redirectURI = $config->get('orcid_' . $srv . '_redirect_uri', '');
		$html[] = '     <a id="create-orcid" class="btn" href="https://';
		if ($config->get('orcid_service', 'members') == 'sandbox')
		{
			$html[] = 'sandbox.';
		}
	    $html[] = 'orcid.org/oauth/authorize?client_id=' . $clientID . htmlspecialchars('&') . 'response_type=code' . htmlspecialchars('&') . 'scope=/authenticate' . htmlspecialchars('&'). 'redirect_uri=' . urlencode($redirectURI)
		. '" rel="nofollow external">' . '<img src="' . Request::root(true) . 'core/components/com_members/site/assets/img/orcid_16x16.png" class="logo" width="20" height="20" alt="iD"/>'
		. Lang::txt('COM_MEMBERS_PROFILE_ORCID_CREATE_OR_CONNECT') . '</a>';
		$html[] = '	</div>';
		$html[] = '</div>';
		$html[] = '<p><img src="' . Request::root(true)  . 'core/components/com_members/site/assets/img/orcid-logo.png" width="80" alt="ORCID" /> ' . Lang::txt('COM_MEMBERS_PROFILE_ORCID_ABOUT') . '</p>';

		Behavior::framework(true);
		Behavior::modal();

		$path = dirname(dirname(__DIR__)) . '/site/assets/js/orcid.js';

		if (file_exists($path))
		{
			Document::addScript(Request::root(true) . 'core/components/com_members/site/assets/js/orcid.js?t=' . filemtime($path));
		}

		return implode($html);
	}
}
