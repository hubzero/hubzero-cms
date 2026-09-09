<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Behavior;
use Hubzero\Facades\Document;
use Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\Request;
use Hubzero\Facades\Component;

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
        $isDaisyUi = \Hubzero\Facades\Document::getCssFramework() === 'daisyui';

        $attributes = array(
            'type'         => 'text',
            'value'        => htmlspecialchars($this->value == null ? '' : $this->value, ENT_COMPAT, 'UTF-8'),
            'name'         => $this->name,
            'id'           => $this->id,
            'size'         => ($this->element['size']      ? (int) $this->element['size']      : ''),
            'maxlength'    => ($this->element['maxlength'] ? (int) $this->element['maxlength'] : ''),
            'autocomplete' => ((string) $this->element['autocomplete'] == 'off' ? 'off'      : ''),
            'readonly'     => ((string) $this->element['readonly'] == 'true'    ? 'readonly' : ''),
            'disabled'     => ((string) $this->element['disabled'] == 'true'    ? 'disabled' : ''),
            'onchange'     => ($this->element['onchange']  ? (string) $this->element['onchange'] : '')
        );

        if ($isDaisyUi) {
            $attributes['class']       = 'input input-bordered input-sm w-full';
            $attributes['placeholder'] = '####-####-####-####';
        } else {
            $attributes['class'] = 'orcid' . ($this->element['class'] ? (string) $this->element['class'] : '');
        }

        $attr = array();
        foreach ($attributes as $key => $value) {
            if ($key != 'value' && !$value) {
                continue;
            }
            $attr[] = $key . '="' . $value . '"';
        }
        $attr = implode(' ', $attr);

        $config = Component::params('com_members');
        $srv = $config->get('orcid_service', 'members');
        $clientID = $config->get('orcid_' . $srv . '_client_id', '');
        $redirectURI = $config->get('orcid_' . $srv . '_redirect_uri', '');
        $permissionURI = $config->get('orcid_' . $srv . '_permission_uri', '');
        $userID = User::get('id');
        if ($userID != 0) {
            $profile = \Components\Members\Models\Member::oneOrFail($userID);
        }

        $amp = htmlspecialchars('&');
        $orcidBase = ($config->get('orcid_service', 'members') == 'sandbox')
            ? 'https://sandbox.orcid.org/oauth/authorize'
            : 'https://orcid.org/oauth/authorize';
        $imgPath   = Request::root(true) . 'core/components/com_members/site/assets/img/';
        $orcidIcon = '<img src="' . $imgPath . 'orcid_16x16.png" class="shrink-0" width="16" height="16" alt="iD" />';

        $oauthUrl = $orcidBase . '?client_id=' . $clientID . $amp . 'response_type=code'
            . $amp . 'scope=/authenticate' . $amp . 'redirect_uri=' . urlencode($redirectURI);
        $permScope = '/read-limited%20/activities/update%20/person/update';
        $permUrl   = $orcidBase . '?client_id=' . $clientID . $amp . 'response_type=code'
            . $amp . 'scope=' . $permScope . $amp . 'redirect_uri=' . urlencode($permissionURI);

        $baseUri = rtrim(Request::base(true), '/');

        $html = array();

        if ($isDaisyUi) {
            // ── Blade / daisyUI output ────────────────────────────────
            $html[] = '<input ' . $attr . ' />';
            $html[] = '<input type="hidden" name="base_uri" id="base_uri" value="' . $baseUri . '" />';
            $html[] = '<div class="flex gap-2 mt-2 pb-2 flex-wrap">';

            if ($userID != 0 && !empty($profile->get('orcid'))) {
                $html[] = '<span class="badge badge-success badge-sm">'
                    . Lang::txt('COM_MEMBERS_PROFILE_ORCID_ID_AUTHORIZED') . '</span>';
            } else {
                $html[] = '<a href="' . $oauthUrl . '" rel="nofollow external"'
                    . ' class="btn btn-sm btn-ghost border border-base-300 font-normal">'
                    . '<span class="flex items-center gap-1.5">'
                    . $orcidIcon . Lang::txt('COM_MEMBERS_PROFILE_ORCID_CREATE_OR_CONNECT')
                    . '</span></a>';
            }

            if ($userID != 0 && !empty($profile->get('orcid')) && !empty($profile->get('access_token'))) {
                $html[] = '<span class="badge badge-success badge-sm">'
                    . Lang::txt('COM_MEMBERS_PROFILE_ORCID_PERMISSION_AUTHORIZED') . '</span>';
            } else {
                $html[] = '<a href="' . $permUrl . '" rel="nofollow external"'
                    . ' class="btn btn-sm btn-ghost border border-base-300 font-normal">'
                    . '<span class="flex items-center gap-1.5">'
                    . $orcidIcon . Lang::txt('COM_MEMBERS_PROFILE_ORCID_GRANT_PERMISSION')
                    . '</span></a>';
            }

            $html[] = '</div>';
            $orcidLogo = '<img src="' . $imgPath . 'orcid-logo.png" height="18" alt="ORCID"'
                . ' class="shrink-0" />';
            $html[] = '<p class="flex items-center gap-2 mt-2 text-sm text-subtle-foreground">'
                . $orcidLogo . ' ' . Lang::txt('COM_MEMBERS_PROFILE_ORCID_ABOUT') . '</p>';
        } else {
            // ── Legacy output ─────────────────────────────────────────
            $html[] = '<div class="grid">';
            $html[] = '        <div class="col span9">';
            $html[] = '                <input ' . $attr . ' placeholder="####-####-####-####" />';
            $html[] = '                <input type="hidden" name="base_uri" id="base_uri" value="' . $baseUri . '" />';
            $html[] = '        </div>';

            $legacyIcon = '<img src="' . $imgPath . 'orcid_16x16.png" class="logo" width="20" height="20" alt="iD"/>';
            $html[] = '        <div class="col span3 omega">';
            if ($userID != 0 && !empty($profile->get('orcid'))) {
                $html[] = '<p>' . Lang::txt('COM_MEMBERS_PROFILE_ORCID_ID_AUTHORIZED') . '</p>';
            } else {
                $html[] = '     <a id="authorize-orcid" class="btn" href="' . $oauthUrl
                    . '" rel="nofollow external">' . $legacyIcon
                    . Lang::txt('COM_MEMBERS_PROFILE_ORCID_CREATE_OR_CONNECT') . '</a>';
            }
            $html[] = '        </div>';

            $html[] = '        <div class="col span3 omega">';
            if ($userID != 0 && !empty($profile->get('orcid')) && !empty($profile->get('access_token'))) {
                $html[] = '<p>' . Lang::txt('COM_MEMBERS_PROFILE_ORCID_PERMISSION_AUTHORIZED') . '</p>';
            } else {
                $html[] = '     <a id="grant-orcid-management-permission" class="btn" href="' . $permUrl
                    . '" rel="nofollow external">' . $legacyIcon
                    . Lang::txt('COM_MEMBERS_PROFILE_ORCID_GRANT_PERMISSION') . '</a>';
            }
            $html[] = '        </div>';
            $html[] = '</div>';
            $orcidLogo = '<img src="' . $imgPath . 'orcid-logo.png" width="80" alt="ORCID" />';
            $html[] = '<p>' . $orcidLogo . ' ' . Lang::txt('COM_MEMBERS_PROFILE_ORCID_ABOUT') . '</p>';
        }

        if (\Hubzero\Facades\Document::getCssFramework() !== 'daisyui') {
            Behavior::framework(true);
            Behavior::modal();
        }
        $path = dirname(dirname(__DIR__)) . '/site/assets/js/orcid.js';
        if (file_exists($path)) {
            $jsUrl = Request::root(true) . 'core/components/com_members/site/assets/js/orcid.js?t='
                . filemtime($path);
            Document::addScript($jsUrl);
        }

        return implode($html);
    }
}
