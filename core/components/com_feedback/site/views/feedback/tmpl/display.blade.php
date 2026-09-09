{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$sitename = \Hubzero\Facades\Config::get('sitename');
$storyUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&task=success_story'
);
$troubleUrl = \Hubzero\Facades\Route::url(
    'index.php?option=com_support&controller=tickets&task=new'
);
$answersUrl = \Hubzero\Facades\Route::url('index.php?option=com_answers');
$forumUrl = \Hubzero\Facades\Route::url('index.php?option=com_forum');
$groupsUrl = \Hubzero\Facades\Route::url('index.php?option=com_groups');
@endphp

<x-page-container :title="$title">
    {{-- Introduction --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2">
            <h2 class="text-xl font-bold mb-2">
                {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_HAVE_SOMETHING_TO_SAY') }}
            </h2>
            <p class="text-base-content/70">
                {!! \Hubzero\Facades\Lang::txt('COM_FEEDBACK_INTRO', $sitename) !!}
            </p>
        </div>
        <div>
            <h2 class="text-lg font-semibold mb-2">
                {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_PARTICIPATE') }}
            </h2>
            <ul class="menu menu-sm p-0">
                <li>
                    <a href="{{ $answersUrl }}">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_LINK_ANSWERS') }}
                    </a>
                </li>
                <li>
                    <a href="{{ $forumUrl }}">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_LINK_FORUM') }}
                    </a>
                </li>
                <li>
                    <a href="{{ $groupsUrl }}">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_LINK_GROUPS') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Ways to submit feedback --}}
    <h2 class="text-lg font-semibold mb-4">
        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_WAYS_TO_SUBMIT') }}
    </h2>

    <x-card-grid cols="2">
        {{-- Success story --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="card-title text-base">
                    <a href="{{ $storyUrl }}" class="link link-primary link-hover">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_HEADER') }}
                    </a>
                </h3>
                <p class="text-sm text-base-content/70">
                    {!! \Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_OTHER_OPTIONS') !!}
                </p>
                <div class="card-actions mt-2">
                    <a class="btn btn-primary btn-sm" href="{{ $storyUrl }}">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_BUTTON') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Report problems --}}
        <div class="card bg-base-100 border border-base-300">
            <div class="card-body">
                <h3 class="card-title text-base">
                    <a href="{{ $troubleUrl }}" class="link link-primary link-hover">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_TROUBLE_HEADER') }}
                    </a>
                </h3>
                <p class="text-sm text-base-content/70">
                    {!! \Hubzero\Facades\Lang::txt('COM_FEEDBACK_TROUBLE_INTRO') !!}
                </p>
                <div class="card-actions mt-2">
                    <a class="btn btn-primary btn-sm" href="{{ $troubleUrl }}">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_TROUBLE_BUTTON') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Wishlist --}}
        @if ($wishlist)
            @php
            $wishlistUrl = \Hubzero\Facades\Route::url('index.php?option=com_wishlist');
            @endphp
            <div class="card bg-base-100 border border-base-300">
                <div class="card-body">
                    <h3 class="card-title text-base">
                        <a href="{{ $wishlistUrl }}" class="link link-primary link-hover">
                            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_WISHLIST_HEADER') }}
                        </a>
                    </h3>
                    <p class="text-sm text-base-content/70">
                        {!! \Hubzero\Facades\Lang::txt('COM_FEEDBACK_WISHLIST_DESCRIPTION') !!}
                    </p>
                    <div class="card-actions mt-2">
                        <a class="btn btn-primary btn-sm" href="{{ $wishlistUrl }}">
                            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_WISHLIST_BUTTON') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Poll --}}
        @if ($poll)
            @php
            $pollUrl = \Hubzero\Facades\Route::url(
                'index.php?option=' . $option . '&task=poll'
            );
            @endphp
            <div class="card bg-base-100 border border-base-300">
                <div class="card-body">
                    <h3 class="card-title text-base">
                        <a href="{{ $pollUrl }}" class="link link-primary link-hover">
                            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_POLL_HEADER') }}
                        </a>
                    </h3>
                    <p class="text-sm text-base-content/70">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_POLL_DESCRIPTION') }}
                    </p>
                    <div class="card-actions mt-2">
                        <a class="btn btn-primary btn-sm" href="{{ $pollUrl }}">
                            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_POLL_BUTTON') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </x-card-grid>
</x-page-container>
