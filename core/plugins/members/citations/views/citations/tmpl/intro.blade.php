{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css('citations.css')->js();
$base = 'index.php?option=com_members&id=' . $member->get('id') . '&active=citations';
@endphp

@if (isset($messages))
    @foreach ($messages as $message)
        <div class="alert alert-{{ $message['type'] }}">{{ $message['message'] }}</div>
    @endforeach
@endif

@if ($isAdmin)
    <div id="content-header-extra" class="flex flex-wrap gap-2 mb-6">
        <a class="btn btn-primary btn-sm" href="{{ Route::url($base . '&action=add') }}">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION') }}
        </a>
        <a class="btn btn-secondary btn-sm" href="{{ Route::url($base . '&action=import') }}">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION') }}
        </a>
        <a class="btn btn-ghost btn-sm" href="{{ Route::url($base . '&action=settings') }}">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT') }}
        </a>
    </div>
@endif

<div id="intro-container">
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">{{ Lang::txt('PLG_MEMBERS_CITATIONS') }}</h2>

            <p class="text-base-content/70 my-4">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_NO_CITATIONS_FOUND') }}
            </p>

            @if ($isAdmin)
                <p class="mb-4">{{ Lang::txt('PLG_MEMBERS_CITATIONS_MEMBER_MAY') }}</p>

                <ul class="list-none space-y-4 p-0">
                    <li class="flex items-start gap-3">
                        <a class="btn btn-primary btn-sm" href="{{ Route::url($base . '&action=add') }}">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION') }}
                        </a>
                        <span class="text-base-content/70">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_MANUALLY_ENTER') }}
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <a class="btn btn-secondary btn-sm" href="{{ Route::url($base . '&action=import') }}">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION') }}
                        </a>
                        <span class="text-base-content/70">
                            Import a list of citations.
                        </span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-base-content/50 italic">or</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <a class="btn btn-ghost btn-sm" href="{{ Route::url($base . '&action=settings') }}">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT') }}
                        </a>
                        <span class="text-base-content/70">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_DESCRIPTION') }}
                        </span>
                    </li>
                </ul>
            @endif
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm mt-6">
        <div class="card-body">
            <p><strong>{{ Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS') }}</strong></p>
            <p class="text-base-content/70">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_INTRO_WHAT_IS_THIS_EXPLANATION') }}
            </p>
        </div>
    </div>
</div>
