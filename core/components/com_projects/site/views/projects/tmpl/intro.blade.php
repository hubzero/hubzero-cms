{{--
 * Projects introduction / user dashboard page
 *
 * Variables:
 *   $title      - Page title
 *   $option     - Component option string
 *   $model      - Projects model
 *   $filters    - Active filters array
 *   $msg        - Status message string
 *   $publishing - Whether publishing is enabled (0/1)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $__view->css('introduction.css', 'system')
         ->css()
         ->js();

    $rows = $model->entries('list', $filters);

    $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
    $startUrl = Route::url('index.php?option=' . $option . '&task=start');
    $featuresUrl = Route::url('index.php?option=' . $option . '&task=features');
@endphp

<x-page-container :title="$title">
    <x-slot:actions>
        <a class="btn btn-ghost" href="{{ $browseUrl }}">
            {{ Lang::txt('COM_PROJECTS_BROWSE_PUBLIC_PROJECTS') }}
        </a>
    </x-slot:actions>

    @include('projects::_statusmsg', [
        'error' => $__view->getError(),
        'msg'   => $msg,
    ])

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Collaboration intro --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-lg">
                    {{ Lang::txt('COM_PROJECTS_INTRO_COLLABORATION_MADE_EASY') }}
                </h3>
                <p class="text-base-content/70">
                    {{ Lang::txt('COM_PROJECTS_INTRO_COLLABORATION_HOW') }}
                </p>
                @if (User::authorise('core.create', $option))
                    <div class="card-actions mt-2">
                        <a
                            href="{{ $startUrl }}"
                            id="projects-intro-start"
                            class="btn btn-primary"
                        >{{ Lang::txt('COM_PROJECTS_START_PROJECT') }}</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- What you get --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title text-lg">
                    {{ Lang::txt('COM_PROJECTS_INTRO_WHAT_YOU_GET') }}
                </h3>
                <ul class="list-disc list-inside space-y-1 text-base-content/70">
                    <li>{{ Lang::txt('COM_PROJECTS_INTRO_GET_WIKI') }}</li>
                    <li>{{ Lang::txt('COM_PROJECTS_INTRO_GET_TODO') }}</li>
                    <li>{{ Lang::txt('COM_PROJECTS_INTRO_GET_BLOG') }}</li>
                    @if ($publishing)
                        <li>{{ Lang::txt('COM_PROJECTS_INTRO_GET_PUBLISHING') }}</li>
                    @endif
                </ul>
                <div class="card-actions mt-2">
                    <a
                        href="{{ $featuresUrl }}"
                        id="projects-intro-features"
                        class="btn btn-outline"
                    >{!! Lang::txt('COM_PROJECTS_LEARN_MORE') !!}</a>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_PROJECTS_MY_PROJECTS') }}</h2>

    @if (count($rows) > 0)
        @include('projects::_list', [
            'rows'    => $rows,
            'filters' => [],
            'option'  => $option,
        ])
    @elseif (User::isGuest())
        @php
            $loginUrl = Route::url('index.php?option=' . $option . '&task=intro&action=login');
        @endphp
        <div class="alert alert-info">
            {{ Lang::txt('COM_PROJECTS_PLEASE') }}
            <a class="link" href="{{ $loginUrl }}" id="projects-intro-login">
                {{ Lang::txt('COM_PROJECTS_LOGIN') }}
            </a>
            {{ Lang::txt('COM_PROJECTS_TO_VIEW_YOUR_PROJECTS') }}
        </div>
    @else
        <x-empty-state
            :title="Lang::txt('COM_PROJECTS_YOU_DONT_HAVE_PROJECTS')"
        >
            @if (User::authorise('core.create', $option))
                <a href="{{ $startUrl }}" class="btn btn-primary">
                    {{ Lang::txt('COM_PROJECTS_START_PROJECT') }}
                </a>
            @endif
        </x-empty-state>
    @endif
</x-page-container>
