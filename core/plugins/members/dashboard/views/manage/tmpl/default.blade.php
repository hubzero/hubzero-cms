{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$pushUrl = Route::url(
    'index.php?option=com_members&controller=plugins'
    . '&task=manage&plugin=dashboard&action=push'
);
$addUrl = Route::url(
    'index.php?option=com_members&controller=plugins'
    . '&task=manage&plugin=dashboard&action=add'
);
@endphp

<div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold">
        {{ Lang::txt('PLG_MEMBERS_DASHBOARD_MANAGE') }}
    </h3>
    <div class="flex gap-2">
        <a class="btn btn-sm btn-primary" href="{{ $pushUrl }}">
            {{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_TITLE') }}
        </a>
        <a class="btn btn-sm btn-secondary" href="{{ $addUrl }}">
            {{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES') }}
        </a>
    </div>
</div>

<div class="member_dashboard">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 modules customizable">
        @foreach ($modules as $module)
            {!! $__view->view('module', 'display')
                ->set('admin', $admin)
                ->set('module', $module)
                ->loadTemplate() !!}
        @endforeach
    </div>

    @if (empty($modules))
        <div class="text-center py-12">
            <h3 class="text-lg font-semibold">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_TITLE') }}</h3>
            <p class="text-base-content/70 mt-2">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_DESC') }}</p>
        </div>
    @endif
</div>
