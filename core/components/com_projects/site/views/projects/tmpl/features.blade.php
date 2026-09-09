{{--
 * Features showcase page
 *
 * Variables:
 *   $option     - Component option string
 *   $title      - Page title
 *   $config     - Component config Registry
 *   $publishing - Whether publishing is enabled (bool)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Component;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css();

    $wishlist   = Component::isEnabled('com_wishlist');
    $suggestTxt = Lang::txt('COM_PROJECTS_FEATURES_SUGGEST_FEATURE');
    $seeTxt     = Lang::txt('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS');

    $startUrl  = Route::url('index.php?option=' . $option . '&task=start');
    $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');

    $features = [
        'blog' => [
            'title' => 'COM_PROJECTS_FEATURES_BLOG',
            'about' => 'COM_PROJECTS_FEATURES_BLOG_ABOUT',
            'tag'   => 'microblog',
            'learn' => true,
        ],
        'todo' => [
            'title' => 'COM_PROJECTS_FEATURES_TODO',
            'about' => 'COM_PROJECTS_FEATURES_TODO_ABOUT',
            'tag'   => 'todo',
        ],
        'notes' => [
            'title' => 'COM_PROJECTS_FEATURES_NOTES',
            'about' => 'COM_PROJECTS_FEATURES_NOTES_ABOUT',
            'tag'   => 'notes',
        ],
        'team' => [
            'title' => 'COM_PROJECTS_FEATURES_TEAM',
            'about' => 'COM_PROJECTS_FEATURES_TEAM_ABOUT',
            'tag'   => 'team',
        ],
        'files' => [
            'title' => 'COM_PROJECTS_FEATURES_FILES',
            'about' => null,
            'tag'   => 'files',
        ],
        'publications' => [
            'title' => 'COM_PROJECTS_FEATURES_PUBLICATIONS',
            'about' => null,
            'tag'   => 'publications',
        ],
    ];
@endphp

<x-page-container :title="$title">
    <x-slot:actions>
        <a class="btn btn-sm btn-outline"
           href="{{ $startUrl }}">
            {{ Lang::txt('COM_PROJECTS_START_NEW') }}
        </a>
        <a class="btn btn-sm btn-outline"
           href="{{ $browseUrl }}">
            {{ Lang::txt('COM_PROJECTS_BROWSE_PUBLIC_PROJECTS') }}
        </a>
    </x-slot:actions>

    <div class="flex flex-col gap-6">
        @foreach ($features as $key => $feature)
            @php
                $suggestUrl = Route::url(
                    'index.php?option=com_wishlist&task=add&category=general&id=1'
                ) . '/?tag=projects,projects:' . $feature['tag'] . ',com_projects';

                $seeUrl = Route::url(
                    'index.php?option=com_wishlist&category=general&id=1'
                ) . '/?tags=projects,projects:' . $feature['tag'] . ',com_projects';

                $isPublications = ($key === 'publications');
                $isWip = $isPublications && !$publishing;
            @endphp

            <div id="feature-{{ $key }}"
                 class="card bg-base-100 shadow-sm {{ $isWip ? 'opacity-60' : '' }}">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        {{-- Feature image / title column --}}
                        <div class="md:col-span-3 flex flex-col items-center text-center">
                            <h3 class="card-title text-lg">
                                {!! Lang::txt($feature['title']) !!}@if ($isWip)*@endif
                            </h3>
                            @if ($isWip)
                                <span class="badge badge-warning mt-2">
                                    {{ Lang::txt('COM_PROJECTS_FEATURES_IN_THE_WORKS') }}
                                </span>
                            @endif
                        </div>

                        {{-- About text column --}}
                        <div class="md:col-span-6">
                            @if ($key === 'blog')
                                <p>{{ Lang::txt('COM_PROJECTS_FEATURES_BLOG_ABOUT') }}</p>
                                <h4 class="font-semibold mt-4 mb-2">
                                    {{ Lang::txt('COM_PROJECTS_FEATURES_BLOG_ABOUT_LEARN') }}
                                </h4>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>{{ Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_TEAM') }}</li>
                                    <li>{{ Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_BLOG') }}</li>
                                    <li>{{ Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_TODO') }}</li>
                                    <li>{{ Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_NOTES') }}</li>
                                    <li>{{ Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_FILES') }}</li>
                                    @if ($publishing)
                                        <li>{{ Lang::txt('COM_PROJECTS_FEATURES_BLOG_LEARN_PUB') }}</li>
                                    @endif
                                </ul>
                            @elseif ($key === 'files')
                                <p>
                                    {{ Lang::txt('COM_PROJECTS_FEATURES_FILES_ABOUT_START') }}
                                    <a class="link link-primary"
                                       href="http://git-scm.com/"
                                       rel="external">
                                        {{ Lang::txt('COM_PROJECTS_FEATURES_FILES_ABOUT_GIT') }}
                                    </a>
                                    {{ Lang::txt('COM_PROJECTS_FEATURES_FILES_ABOUT_END') }}
                                </p>
                            @elseif ($isPublications)
                                <p>
                                    {{ $publishing
                                        ? Lang::txt('COM_PROJECTS_FEATURES_PUBLICATIONS_ABOUT')
                                        : Lang::txt('COM_PROJECTS_FEATURES_PUBLICATIONS_ABOUT_WIP') }}
                                </p>
                            @else
                                <p>{{ Lang::txt($feature['about']) }}</p>
                            @endif

                            @if ($wishlist && $config->get('suggest_feature', 1))
                                <h4 class="font-semibold mt-4 mb-2">
                                    {{ Lang::txt('COM_PROJECTS_FEATURES_WANT_FEATURE') }}
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ $suggestUrl }}"
                                       class="btn btn-success btn-sm">
                                        {{ $suggestTxt }}
                                    </a>
                                    <a href="{{ $seeUrl }}"
                                       class="btn btn-ghost btn-sm">
                                        {{ $seeTxt }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Spacer column --}}
                        <div class="hidden md:block md:col-span-3"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-page-container>
