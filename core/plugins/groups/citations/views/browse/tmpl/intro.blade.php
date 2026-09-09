{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js();

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=citations';
@endphp

@if (isset($messages))
    @foreach ($messages as $message)
        <div class="alert {{ $message['type'] === 'error' ? 'alert-error' : 'alert-info' }}">
            <p>{!! $message['message'] !!}</p>
        </div>
    @endforeach
@endif

@if ($isManager)
    <div id="content-header-extra" class="flex flex-wrap gap-2 mb-4">
        <a class="btn btn-primary gap-2"
            href="{{ Route::url($base . '&action=add') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            {{ Lang::txt('PLG_GROUPS_CITATIONS_SUBMIT_CITATION') }}
        </a>
        <a class="btn btn-secondary gap-2"
            href="{{ Route::url($base . '&action=import') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3" /></svg>
            {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION') }}
        </a>
        <a class="btn btn-ghost gap-2"
            href="{{ Route::url($base . '&action=settings') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            {{ Lang::txt('PLG_GROUPS_CITATIONS_SET_FORMAT') }}
        </a>
    </div>
@endif

<div class="card bg-base-200">
    <div class="card-body">
        <h2 class="card-title text-2xl">Group Citations</h2>

        <div class="alert alert-info my-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ Lang::txt('PLG_GROUPS_CITATIONS_NO_CITATIONS_FOUND') }}</span>
        </div>

        @if ($isManager)
            <p class="font-semibold mb-3">A group manager may:</p>
            <ul class="space-y-3">
                <li class="flex items-center gap-3">
                    <a class="btn btn-primary btn-sm gap-2"
                        href="{{ Route::url($base . '&action=add') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        {{ Lang::txt('PLG_GROUPS_CITATIONS_SUBMIT_CITATION') }}
                    </a>
                    <span class="text-sm opacity-70">Manually enter a citation.</span>
                </li>
                <li class="flex items-center gap-3">
                    <a class="btn btn-secondary btn-sm gap-2"
                        href="{{ Route::url($base . '&action=import') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3" /></svg>
                        {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION') }}
                    </a>
                    <span class="text-sm opacity-70">Import a list of citations.</span>
                </li>
                <li class="text-sm opacity-60 italic">or</li>
                <li class="flex items-center gap-3">
                    <a class="btn btn-ghost btn-sm gap-2"
                        href="{{ Route::url($base . '&action=settings') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        {{ Lang::txt('PLG_GROUPS_CITATIONS_SET_FORMAT') }}
                    </a>
                    <span class="text-sm opacity-70">Set group-level options for citations.</span>
                </li>
            </ul>
        @endif

        <div class="divider"></div>

        <div>
            <p class="font-semibold mb-1">What is a group citation?</p>
            <p class="text-sm opacity-70">
                Within a group, a citation is a listing of a product resulting in work done by a group or a group member.
                As a group manager, you can choose to display citations curated by a group manager-only or citations that were
                produced by members of your group.
            </p>
        </div>
    </div>
</div>
