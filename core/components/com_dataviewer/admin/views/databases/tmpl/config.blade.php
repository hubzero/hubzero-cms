@php
/**
 * Database config editor view (admin).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$backLink = '/administrator/index.php?option=com_dataviewer&controller=databases';
$configLink = '/administrator/index.php?option=com_dataviewer&controller=databases&task=config_current&db=' . urlencode($dbId);
$formAction = '/administrator/index.php?option=com_dataviewer&controller=databases&task=config_update';
@endphp

<div id="dv-admin-config"
    data-com-name="{{ $comName }}"
    data-db="{{ $dbId }}"
    data-back-link="{{ $backLink }}"
    data-config-link="{{ $configLink }}"
    class="hidden">
</div>

<div style="font-size: 1.5em;">{{ Lang::txt('COM_DATAVIEWER_EDITING_CONFIG') }}
    <strong>{{ $dvConfFile }}</strong></div>
<br />

<div id="db-conf-editor"
    style="height: 400px; width: 900px; display: inline-block;">
    {{ $dvConfText }}
</div>

<div style="height: 400px; width: 400px; display: inline-block;
    vertical-align: top; margin-left: 30px; border: 1px solid #DDD;
    background: #FAFAFA; padding: 5px;">

<pre style="font-size: 0.8em; line-height: 1em; white-space: pre">
<strong>{{ Lang::txt('COM_DATAVIEWER_ACL_HELP_TITLE') }}</strong>

{{ Lang::txt('COM_DATAVIEWER_ACL_HELP_BOTH') }}
{
    "acl": {
        "allowed_users": ["user1", "user2"],
        "allowed_groups": ["group1", "group2"]
    }
}

{{ Lang::txt('COM_DATAVIEWER_ACL_HELP_USERS_ONLY') }}
{
    "acl": {
        "allowed_users": ["user1", "user2"],
        "allowed_groups": false
    }
}

{{ Lang::txt('COM_DATAVIEWER_ACL_HELP_PUBLIC') }}
{
    "acl": {
        "allowed_users": false,
        "allowed_groups": false
    }
}

<strong>{{ Lang::txt('COM_DATAVIEWER_ACL_HELP_MULTIPLE') }}</strong>
{
    "settings": {
        "limit": 50
    },
    "null_desc": "No data available for this field",
    "acl": {
        "allowed_users": false,
        "allowed_groups": ["test"]
    }
}

</pre>

</div>

<br />
<input id="db-config-update" type="button" value="{{ Lang::txt('COM_DATAVIEWER_UPDATE') }}"
    style="color: red; font-weight: bold; font-size: 1.2em;" />
<input id="db-config-view"
    data-link="{{ $configLink }}"
    type="button" value="{{ Lang::txt('COM_DATAVIEWER_VIEW_COMBINED_CONFIG') }}"
    style="color: blue; font-weight: bold; font-size: 1.2em;" />

<form id="db-conf-update-form" method="post"
    action="{{ $formAction }}">
    <input name="{{ $csrfToken }}" type="hidden" value="1" />
    <input name="db" type="hidden" value="{{ $dbId }}" />
    <input name="update" type="hidden" value="true" />
    <input name="conf_text" type="hidden" value="" />
</form>

<div id="dv-view-conf" style="display: none;">
    <div id="db-conf-viewer" style="height: 100%; width: 100%;"></div>
</div>
