{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Component;
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

$text = $group->get('gidNumber')
    ? Lang::txt('COM_GROUPS_EDIT')
    : Lang::txt('COM_GROUPS_NEW');

$canDo = \Components\Groups\Helpers\Permissions::getActions(
    'group',
    $group->get('gidNumber')
);

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $text, 'groups');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('group');


$params = Component::params('com_groups');
$emailForumComments = $params->get('email_forum_comments', 0);

$autoEmailResponses = $group->get('discussion_email_autosubscribe');
if (is_null($autoEmailResponses)) {
    $autoEmailResponses = $params->get('email_member_groupsidcussionemail_autosignup', 0);
}
$emailSub = $group->get('discussion_email_autosubscribe', null);
if ($emailSub == 1 || ($emailSub == null && $autoEmailResponses)) {
    $autoEmailResponses = 1;
}

$gparams = new \Hubzero\Config\Registry($group->params);
$membership_control   = $gparams->get('membership_control', 1);
$display_system_users = $gparams->get('display_system_users', 'global');
$comments  = $gparams->get('page_comments', $params->get('page_comments', 0));
$author    = $gparams->get('page_author', $params->get('page_author', 0));
$trusted   = $gparams->get('page_trusted', $params->get('page_trusted', 0));

$__view->js();

$formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
$invalidMsg = Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED');
@endphp

@if ($__view->getErrors())
    <p class="error">
        {!! implode('<br />', $__view->getErrors()) !!}
    </p>
@endif

<form
    action="{{ $formAction }}"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="{{ $invalidMsg }}"
>
    {{-- Tab navigation --}}
    <div role="tablist" class="tabs tabs-border mb-6">
        <a role="tab" class="tab" data-tab-target="#page-details">
            {{ Lang::txt('JDETAILS') }}
        </a>
        <a role="tab" class="tab" data-tab-target="#page-files">
            {{ Lang::txt('COM_GROUPS_MEDIA') }}
        </a>
    </div>

    {{-- Details tab panel --}}
    <div id="page-details" class="tab-panel">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_24rem] gap-6">

            {{-- Left: main details --}}
            <div class="min-w-0 space-y-6">
                <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_DETAILS') }}">

                    @php
                    $typeTxt = Lang::txt('COM_GROUPS_TYPE');
                    $reqTxt  = Lang::txt('JOPTION_REQUIRED');
                    $grpType = $group->type;
                    @endphp
                    <div class="admin-field">
                        <label for="field-type" class="label text-base-content">
                            {{ $typeTxt }}
                            <span class="text-error">*</span>
                        </label>
                        <select name="group[type]" id="field-type"
                                class="select select-bordered w-full required">
                            <option value="1" @selected($grpType == '1')>
                                {{ Lang::txt('COM_GROUPS_TYPE_HUB') }}
                            </option>
                            <option value="3" @selected($grpType == '3')>
                                {{ Lang::txt('COM_GROUPS_TYPE_SUPER') }}
                            </option>
                            @if ($canDo->get('core.admin'))
                                <option value="0" @selected($grpType == '0')>
                                    {{ Lang::txt('COM_GROUPS_TYPE_SYSTEM') }}
                                </option>
                            @endif
                            <option value="2" @selected($grpType == '2')>
                                {{ Lang::txt('COM_GROUPS_TYPE_PROJECT') }}
                            </option>
                            <option value="4" @selected($grpType == '4')>
                                {{ Lang::txt('COM_GROUPS_TYPE_COURSE') }}
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="admin-field">
                            <label for="field-published" class="label text-base-content">
                                {{ Lang::txt('COM_GROUPS_PUBLISHED') }}
                            </label>
                            <select name="group[published]" id="field-published"
                                    class="select select-bordered w-full">
                                <option value="0" @selected($group->published == 0)>
                                    {{ Lang::txt('COM_GROUPS_UNPUBLISHED') }}
                                </option>
                                <option value="1" @selected($group->published == 1)>
                                    {{ Lang::txt('COM_GROUPS_PUBLISHED') }}
                                </option>
                                <option value="2" @selected($group->published == 2)>
                                    {{ Lang::txt('COM_GROUPS_ARCHIVED') }}
                                </option>
                            </select>
                        </div>
                        <div class="admin-field">
                            <label for="field-approved" class="label text-base-content">
                                {{ Lang::txt('COM_GROUPS_APPROVE') }}
                            </label>
                            <select name="group[approved]" id="field-approved"
                                    class="select select-bordered w-full">
                                <option value="0" @selected($group->approved == 0)>
                                    {{ Lang::txt('COM_GROUPS_UNAPPROVED') }}
                                </option>
                                <option value="1" @selected($group->approved == 1)>
                                    {{ Lang::txt('COM_GROUPS_APPROVED') }}
                                </option>
                            </select>
                        </div>
                    </div>

                    @php
                    $cnHint   = Lang::txt('COM_GROUPS_CN_HINT');
                    $cnVal    = e($group->cn);
                    $readOnly = $group->cn ? ' readonly="readonly"' : '';
                    @endphp
                    <div class="admin-field">
                        <label for="field-cn" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_CN') }}
                            <span class="text-error">*</span>
                        </label>
                        <input
                            type="text"
                            name="group[cn]"
                            id="field-cn"
                            class="input input-bordered w-full required"
                            {!! $readOnly !!}
                            value="{{ $cnVal }}"
                        />
                        <p class="label-text-alt text-muted-foreground mt-1">{{ $cnHint }}</p>
                    </div>

                    @php $descVal = $group->description ?? ''; @endphp
                    <div class="admin-field">
                        <label for="field-description" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_TITLE') }}
                        </label>
                        <input
                            type="text"
                            name="group[description]"
                            id="field-description"
                            class="input input-bordered w-full"
                            value="{{ $descVal }}"
                        />
                    </div>

                    <div class="admin-field">
                        <label for="field-logo" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_LOGO') }}
                        </label>
                        <input
                            type="text"
                            name="group[logo]"
                            id="field-logo"
                            class="input input-bordered w-full"
                            value="{{ $group->logo }}"
                        />
                    </div>

                    @php
                    $__view->js('customfields');

                    $xml = \Components\Groups\Models\Orm\Field::toXml($customFields);
                    $formInfo = ['control' => 'customfields'];
                    $customForm = new Hubzero\Form\Form('application', $formInfo);
                    $customForm->load($xml);
                    $customForm->bind($customAnswers);
                    @endphp

                    @foreach ($customFields as $field)
                        @php
                        $formfield = $customForm->getField($field->get('name'));
                        $hint = '';
                        if (
                            $formfield->description
                            && strtolower($formfield->type) != 'paragraph'
                        ) {
                            $hint = trim($formfield->description);
                        }
                        @endphp
                        <div class="admin-field">
                            @if (strtolower($formfield->type) != 'paragraph')
                                {!! $formfield->label !!}
                            @endif
                            @if ($field->type == 'textarea')
                                @php
                                $fieldName  = $field->get('name');
                                $fieldValue = isset($customAnswers[$fieldName])
                                    ? $customAnswers[$fieldName]
                                    : $field->get('default_value', '');
                                $fieldNameAttr = $formInfo['control'] . '[' . $fieldName . ']';
                                $fieldIdAttr   = $formInfo['control'] . '_' . $fieldName;
                                @endphp
                                {!! $__view->editor(
                                    $fieldNameAttr,
                                    e($fieldValue),
                                    35,
                                    8,
                                    $fieldIdAttr,
                                    ['class' => 'minimal no-footer images macros']
                                ) !!}
                            @else
                                {!! $formfield->input !!}
                            @endif
                            @if ($hint)
                                <p class="label-text-alt text-muted-foreground mt-1">{{ $hint }}</p>
                            @endif
                        </div>
                    @endforeach
                </x-admin-fieldset>

                <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_PAGE_SETTINGS') }}">

                    <div class="admin-field">
                        <label for="param-page_trusted" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_PAGES_SETTING_TRUSTEDCONTENT') }}
                        </label>
                        <select name="group[params][page_trusted]" id="param-page_trusted"
                                class="select select-bordered w-full">
                            <option value="0" @selected($trusted == 0)>
                                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_TRUSTEDCONTENT_NO') }}
                            </option>
                            <option value="1" @selected($trusted == 1)>
                                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_TRUSTEDCONTENT_YES') }}
                            </option>
                        </select>
                    </div>

                    <div class="admin-field">
                        <label for="param-page_comments" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS') }}
                        </label>
                        <select name="group[params][page_comments]" id="param-page_comments"
                                class="select select-bordered w-full">
                            <option value="0" @selected($comments == 0)>
                                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO') }}
                            </option>
                            <option value="1" @selected($comments == 1)>
                                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES') }}
                            </option>
                            <option value="2" @selected($comments == 2)>
                                {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK') }}
                            </option>
                        </select>
                    </div>

                    <div class="admin-field">
                        <label for="param-page_author" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR') }}
                        </label>
                        <select name="group[params][page_author]" id="param-page_author"
                                class="select select-bordered w-full">
                            <option value="0" @selected($author == 0)>
                                {{ Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_NO') }}
                            </option>
                            <option value="1" @selected($author == 1)>
                                {{ Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_YES') }}
                            </option>
                        </select>
                    </div>

                    @php $tplVal = $gparams->get('page_template', ''); @endphp
                    <div class="admin-field">
                        <label for="param-page_template" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_PAGES_TEMPLATE') }}
                        </label>
                        <input
                            type="text"
                            name="group[params][page_template]"
                            id="param-page_template"
                            class="input input-bordered w-full"
                            value="{{ $tplVal }}"
                        />
                    </div>
                </x-admin-fieldset>
            </div>{{-- /left --}}

            {{-- Right: sidebar --}}
            <div class="space-y-6">
                @if ($group->gidNumber)
                    <table class="meta">
                        <tbody>
                            <tr>
                                <th scope="row">{{ Lang::txt('COM_GROUPS_ID') }}</th>
                                <td>{{ $group->gidNumber }}</td>
                            </tr>
                            <tr>
                                <th scope="row">{{ Lang::txt('COM_GROUPS_PUBLISHED') }}</th>
                                <td>{{ $group->published ? Lang::txt('JYES') : Lang::txt('JNO') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">{{ Lang::txt('COM_GROUPS_APPROVED') }}</th>
                                <td>{{ $group->approved ? Lang::txt('JYES') : Lang::txt('JNO') }}</td>
                            </tr>
                            @if ($group->created)
                                <tr>
                                    <th scope="row">{{ Lang::txt('COM_GROUPS_CREATED') }}</th>
                                    <td>{{ date('l F d, Y @ g:ia', strtotime($group->created)) }}</td>
                                </tr>
                            @endif
                            @if ($group->created_by)
                                @php $creator = User::getInstance($group->created_by); @endphp
                                <tr>
                                    <th scope="row">{{ Lang::txt('COM_GROUPS_CREATED_BY') }}</th>
                                    <td>{{ $creator->get('name') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endif

                <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_MEMBERSHIP') }}">

                    <div class="admin-field flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="group[params][membership_control]"
                            id="field-membership_control"
                            class="checkbox checkbox-sm"
                            value="1"
                            @checked($membership_control == 1)
                        />
                        <label for="field-membership_control" class="label cursor-pointer">
                            {{ Lang::txt('COM_GROUPS_MEMBERSHIP_CONTROL') }}
                        </label>
                    </div>

                    <div class="admin-field">
                        <p class="label text-base-content">{{ Lang::txt('COM_GROUPS_JOIN_POLICY') }}:</p>
                        @php
                        $jp = $group->join_policy;
                        $policies = [
                            0 => Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC')
                                . ' &mdash; ' . Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC_DESC'),
                            1 => Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED')
                                . ' &mdash; ' . Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED_DESC'),
                            2 => Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE')
                                . ' &mdash; ' . Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE_DESC'),
                            3 => Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED')
                                . ' &mdash; ' . Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED_DESC'),
                        ];
                        @endphp
                        <div class="space-y-1">
                            @foreach ($policies as $val => $label)
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="radio" name="group[join_policy]"
                                           class="radio radio-sm mt-0.5"
                                           value="{{ $val }}" @checked($jp == $val) />
                                    <span class="label-text">{!! $label !!}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="admin-field">
                        <label for="restrict_msg" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_EDIT_CREDENTIALS') }}
                        </label>
                        @php
                        $restrictMsg = $group->restrict_msg ? $group->restrict_msg : '';
                        echo $__view->editor(
                            'group[restrict_msg]',
                            e($restrictMsg),
                            40,
                            10,
                            'restrict_msg',
                            ['class' => 'minimal']
                        );
                        @endphp
                    </div>
                </x-admin-fieldset>

                <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_ACCESS') }}">

                    <div class="admin-field">
                        <p class="label text-base-content">{{ Lang::txt('COM_GROUPS_DISCOVERABILITY') }}:</p>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="group[discoverability]"
                                       class="radio radio-sm"
                                       id="field-discoverability0" value="0"
                                       @checked($group->discoverability == 0) />
                                <span class="label-text">
                                    {{ Lang::txt('COM_GROUPS_DISCOVERABILITY_VISIBLE') }}
                                </span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="group[discoverability]"
                                       class="radio radio-sm"
                                       id="field-discoverability1" value="1"
                                       @checked($group->discoverability == 1) />
                                <span class="label-text">
                                    {{ Lang::txt('COM_GROUPS_DISCOVERABILITY_HIDDEN') }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="admin-field">
                        <label for="field-plugins" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_PLUGIN_ACCESS') }}
                        </label>
                        <textarea
                            name="group[plugins]"
                            id="field-plugins"
                            rows="6"
                            class="textarea textarea-bordered w-full font-mono text-xs"
                        >{{ $group->plugins }}</textarea>
                    </div>

                    <div class="admin-field">
                        <label for="display_system_users" class="label text-base-content">
                            {{ Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS') }}
                        </label>
                        <select name="group[params][display_system_users]"
                                id="display_system_users"
                                class="select select-bordered w-full">
                            <option value="global" @selected($display_system_users == 'global')>
                                {{ Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_GLOBAL') }}
                            </option>
                            <option value="no" @selected($display_system_users == 'no')>
                                {{ Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_NO') }}
                            </option>
                            <option value="yes" @selected($display_system_users == 'yes')>
                                {{ Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_YES') }}
                            </option>
                        </select>
                    </div>
                </x-admin-fieldset>

                @if ($emailForumComments)
                    <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_EMAIL_SETTINGS') }}">
                        <div class="admin-field flex items-center gap-2">
                            <input type="hidden"
                                name="group[discussion_email_autosubscribe]"
                                value="0" />
                            <input
                                type="checkbox"
                                name="group[discussion_email_autosubscribe]"
                                id="field-discussion_email_autosubscribe"
                                class="checkbox checkbox-sm"
                                value="1"
                                @checked($autoEmailResponses == 1)
                            />
                            <label for="field-discussion_email_autosubscribe"
                                   class="label cursor-pointer">
                                {{ Lang::txt('COM_GROUPS_DISCUSSION_EMAIL_AUTOSUBSCRIBE') }}
                            </label>
                        </div>
                    </x-admin-fieldset>
                @endif
            </div>{{-- /sidebar --}}

        </div>{{-- /grid --}}
    </div>{{-- /page-details --}}

    {{-- Files tab panel --}}
    <div id="page-files" class="tab-panel hidden">
        <x-admin-fieldset>
            @if ($group->gidNumber)
                @php
                $uploadpath = Component::params('com_groups')->get('uploadpath', '/site/groups');
                $mediaPath  = substr(PATH_APP, strlen(PATH_ROOT))
                    . DS . trim($uploadpath, DS)
                    . DS . $group->get('gidNumber');
                $mediaUrl   = Route::url(
                    'index.php?option=' . $option
                    . '&controller=media&tmpl=component&gidNumber='
                    . $group->gidNumber
                    . '&t=' . Date::toUnix(), false
                );
                @endphp
                <p class="text-sm text-muted-foreground mb-3">
                    {!! Lang::txt('COM_GROUPS_MEDIA_PATH', $mediaPath) !!}
                </p>
                <iframe
                    width="100%"
                    height="500"
                    name="media"
                    id="media"
                    title="{{ Lang::txt('COM_GROUPS_MEDIA') }}"
                    style="border:0"
                    src="{!! $mediaUrl !!}"
                ></iframe>
            @else
                <p class="alert alert-warning">
                    {{ Lang::txt('COM_GROUPS_MEDIA_FILES_WARNING') }}
                </p>
            @endif
        </x-admin-fieldset>
    </div>{{-- /page-files --}}

    <input type="hidden" name="group[gidNumber]" value="{{ $group->gidNumber }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="save" />
    {!! Html::input('token') !!}
</form>
