{{-- /**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */ --}}
@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

$base = Route::url(
    'index.php?option=' . $option . '&controller=' . $controller . '&gid=' . $group->cn, false
);

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');
$text  = ($task == 'edit')
    ? Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE')
    : Lang::txt('COM_GROUPS_PAGES_NEW_PAGE');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $text, 'groups');
if ($canDo->get('core.edit')) {
    Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('page');

@endphp

<form action="{{ $base }}" method="post" name="adminForm" id="item-form">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_DETAILS') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-title">
                        {{ Lang::txt('COM_GROUPS_PAGES_TITLE') }}: <span class="required">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                    </label>
                    <input
                        type="text"
                        name="page[title]"
                        id="field-title"
                        class="input input-bordered w-full required"
                        value="{{ $page->get('title') }}" />
                </div>

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-alias">
                        {{ Lang::txt('COM_GROUPS_PAGES_ALIAS') }}: <span class="required">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                    </label>
                    <input
                        type="text"
                        name="page[alias]"
                        id="field-alias"
                        class="input input-bordered w-full required"
                        value="{{ $page->get('alias') }}" />
                </div>
            </x-admin-fieldset>

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_SETTINGS') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-category">{{ Lang::txt('COM_GROUPS_PAGES_CATEGORY') }}:</label>
                    <select name="page[category]" id="field-category" class="select select-bordered w-full">
                        <option value="">{{ Lang::txt('COM_GROUPS_PAGES_CATEGORY_OPTION_NULL') }}</option>
                        @foreach ($categories as $pageCategory)
                            <option
                                value="{{ $pageCategory->get('id') }}"
                                @if ($page->get('category') == $pageCategory->get('id')) selected="selected" @endif>
                                {{ $pageCategory->get('title') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if (!$page->get('home'))
                    <div class="input-wrap">
                        <label class="label text-base-content" for="field-parent">{{ Lang::txt('COM_GROUPS_PAGES_PARENT') }}:</label>
                        <select name="page[parent]" id="field-parent" class="select select-bordered w-full">
                            @if (!count($pages))
                                <option value="0">{{ Lang::txt('COM_GROUPS_PAGES_TEMPLATE_OPTION_NULL') }}</option>
                            @endif
                            @foreach ($pages as $parentPage)
                                @if ($parentPage->get('id') == $page->get('id'))
                                    @php continue; @endphp
                                @endif
                                <option
                                    value="{{ $parentPage->get('id') }}"
                                    @if ($page->get('parent') == $parentPage->get('id')) selected="selected" @endif>
                                    {{ $parentPage->heirarchyIndicator(' &ndash; ') . $parentPage->get('title') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($group->isSuperGroup())
                    <div class="input-wrap">
                        <label class="label text-base-content" for="field-template">{{ Lang::txt('COM_GROUPS_PAGES_TEMPLATE') }}:</label>
                        <select name="page[template]" id="field-template" class="select select-bordered w-full">
                            <option value="">{{ Lang::txt('COM_GROUPS_PAGES_TEMPLATE_OPTION_NULL') }}</option>
                            @foreach ($pageTemplates as $name => $file)
                                @php $tmpl = str_replace('.php', '', $file); @endphp
                                <option
                                    value="{{ $tmpl }}"
                                    @if ($page->get('template') == $tmpl) selected="selected" @endif>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </x-admin-fieldset>

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_ACCESS') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-state">{{ Lang::txt('COM_GROUPS_PAGES_STATE') }}:</label>
                    <select
                        name="page[state]"
                        id="field-state"
                        class="select select-bordered w-full"
                        @if ($page->get('home') == 1) disabled="disabled" @endif>
                        @php
                            $states = [
                                1 => Lang::txt('COM_GROUPS_PAGES_STATE_PUBLISHED'),
                                0 => Lang::txt('COM_GROUPS_PAGES_STATE_UNPUBLISHED'),
                                2 => Lang::txt('COM_GROUPS_PAGES_STATE_DELETED'),
                            ];
                            foreach ($states as $k => $v) {
                                $sel = ($page->get('state') == $k) ? 'selected="selected"' : '';
                                echo '<option ' . $sel . ' value="' . $k . '">' . $v . '</option>';
                            }
                        @endphp
                    </select>
                </div>

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-privacy">{{ Lang::txt('COM_GROUPS_PAGES_PRIVACY') }}:</label>
                    @php
                        $access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'overview');
                        switch ($access) {
                            case 'anyone':
                                $name = 'Any HUB Visitor';
                                break;
                            case 'registered':
                                $name = 'Registered HUB Users';
                                break;
                            case 'members':
                            default:
                                $name = 'Group Members Only';
                                break;
                        }
                    @endphp
                    <select name="page[privacy]" id="field-privacy" class="select select-bordered w-full">
                        <option
                            value="default"
                            @if ($page->get('privacy') == 'default') selected="selected" @endif>
                            {{ Lang::txt('COM_GROUPS_PAGES_PRIVACY_OPTION_INHERIT', $name) }}
                        </option>
                        <option
                            value="members"
                            @if ($page->get('privacy') == 'members') selected="selected" @endif>
                            {{ Lang::txt('COM_GROUPS_PAGES_PRIVACY_OPTION_PRIVATE') }}
                        </option>
                    </select>
                </div>
            </x-admin-fieldset>

            <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_CONTENT') }}">

                <div class="input-wrap">
                    <label class="label text-base-content" for="field-content">{{ Lang::txt('COM_GROUPS_PAGES_CONTENT') }}:</label>
                    {!! $__view->editor(
                        'pageversion[content]',
                        e($version->get('content')),
                        50,
                        30,
                        'field-content',
                        ['buttons' => false]
                    ) !!}
                    <input
                        type="hidden"
                        name="pageversion[version]"
                        value="{{ $version->get('version') }}" />
                </div>
            </x-admin-fieldset>
        </div>
        <div>
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row">{{ Lang::txt('COM_GROUPS_PAGES_OWNER') }}</th>
                        <td>{{ $group->get('description') }}</td>
                    </tr>
                    @if ($page->get('id'))
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_PAGES_ID') }}</th>
                            <td>{{ $page->get('id') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_PAGES_CURRENT_VERSION') }}</th>
                            <td>{{ $version->get('version') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_PAGES_CREATED') }}</th>
                            <td>{{ Date::of($firstversion->get('created'))->toLocal('F j, Y @ g:ia') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_PAGES_CREATED_BY') }}</th>
                            <td>
                                @php
                                    $profile = User::getInstance($firstversion->get('created_by'));
                                    echo (is_object($profile))
                                        ? $profile->get('name') . ' (' . $profile->get('id') . ')'
                                        : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
                                @endphp
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_PAGES_LAST_MODIFIED') }}</th>
                            <td>{{ Date::of($version->get('created'))->toLocal('F j, Y @ g:ia') }}</td>
                        </tr>
                        <tr>
                            <th scope="row">{{ Lang::txt('COM_GROUPS_PAGES_LAST_MODIFIED_BY') }}</th>
                            <td>
                                @php
                                    $profile = User::getInstance($version->get('created_by'));
                                    echo (is_object($profile))
                                        ? $profile->get('name') . ' (' . $profile->get('id') . ')'
                                        : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
                                @endphp
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($page->get('id'))
                <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_VERSIONS') }}">

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th scope="col">{{ Lang::txt('COM_GROUPS_PAGES_VERSION_NUMBER') }}</th>
                                <th scope="col">{{ Lang::txt('COM_GROUPS_PAGES_VERSION_CREATED') }}</th>
                                <th scope="col">{{ Lang::txt('COM_GROUPS_PAGES_VERSION_APPROVED') }}</th>
                                <th scope="col">{{ Lang::txt('COM_GROUPS_PAGES_VERSION_VIEW') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($page->versions() as $ver)
                                <tr>
                                    <td>{{ $ver->get('version') }}</td>
                                    <td>
                                        @php
                                            $vProfile = User::getInstance($ver->get('created_by'));
                                            $vName = is_object($vProfile)
                                                ? $vProfile->get('name')
                                                : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
                                            echo Lang::txt(
                                                'COM_GROUPS_PAGES_VERSION_CREATED_DETAILS',
                                                $vName,
                                                Date::of($ver->get('created'))->toLocal()
                                            );
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            if ($ver->get('approved')) {
                                                $aProfile = User::getInstance($ver->get('approved_by'));
                                                $aName = is_object($aProfile)
                                                    ? $aProfile->get('name')
                                                    : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
                                                echo Lang::txt(
                                                    'COM_GROUPS_PAGES_VERSION_APPROVED_DETAILS',
                                                    $aName,
                                                    Date::of($ver->get('approved_on'))->toLocal()
                                                );
                                            } else {
                                                echo Lang::txt('COM_GROUPS_PAGES_VERSION_NOT_APPROVED');
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            $rawUrl = $base . '&amp;task=raw&amp;pageid='
                                                . $page->get('id') . '&amp;version=' . $ver->get('version');
                                        @endphp
                                        <a class="version" href="{{ $rawUrl }}">
                                            {{ Lang::txt('COM_GROUPS_PAGES_VERSION_VIEW_RAW') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-admin-fieldset>
            @endif
        </div>
    </div>

    <input type="hidden" name="page[id]" value="{{ $page->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="gid" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="task" value="save" />
    {!! Html::input('token') !!}
</form>
