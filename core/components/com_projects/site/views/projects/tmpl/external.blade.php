{{--
 * Project external/public view — bespoke layout
 *
 * Shows project public profile with membership request logic and
 * plugin public content sections. Does NOT use <x-page-container>.
 *
 * Variables:
 *   $model    - Project model object
 *   $option   - Component option string
 *   $title    - Page title
 *   $active   - Active tab name
 *   $tabs     - Array of plugin tab definitions
 *   $reviewer - Reviewer type string (if reviewer view)
 *   $config   - Component config Registry
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Component;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Session;
    use Hubzero\Facades\User;
    use Hubzero\Facades\Event;

    $__view->css()->js()->css('external')->css('extended.css');

    $params = $model->params;
    $theme = $params->get('theme', $config->get('theme', 'light'));
    $__view->css('theme' . $theme . '.css');

    $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
    $startUrl = Route::url('index.php?option=' . $option . '&task=start');
    $requestUrl = Route::url(
        'index.php?option=com_projects&task=requestaccess&alias='
        . $model->get('alias') . '&' . Session::getFormToken() . '=1'
    );
    $member = $model->member();
@endphp

<div id="project-wrap" class="theme publicview">
    <div id="content-header-extra" class="flex justify-end gap-2 mb-4">
        <a class="btn btn-sm btn-outline" href="{{ $browseUrl }}">
            {{ Lang::txt('COM_PROJECTS_ALL_PROJECTS') }}
        </a>
        @if (User::authorise('core.create', $option))
            <a class="btn btn-sm btn-outline" href="{{ $startUrl }}">
                {{ Lang::txt('COM_PROJECTS_START_NEW') }}
            </a>
        @endif
    </div>

    @if ($model->access('member') && !$reviewer)
        <div id="project-preview" class="alert alert-info mb-4">
            @php
                $returnUrl = Route::url(
                    'index.php?option=' . $option . '&alias=' . $model->get('alias')
                );
            @endphp
            <span>
                {{ Lang::txt('COM_PROJECTS_THIS_IS_PROJECT_PREVIEW') }}
                {{ Lang::txt('COM_PROJECTS_RETURN_TO') }}
                <a href="{{ $returnUrl }}" class="link link-primary">{{ Lang::txt('COM_PROJECTS_PROJECT_PAGE') }}</a>
            </span>
        </div>
    @elseif ($reviewer)
        <div id="project-preview" class="alert alert-info mb-4">
            @php
                $reviewBrowseUrl = Route::url(
                    'index.php?option=' . $option . '&task=browse&reviewer=' . $reviewer
                );
            @endphp
            <span>
                {{ Lang::txt('COM_PROJECTS_REVIEWER_PROJECT_PREVIEW') }}
                {{ Lang::txt('COM_PROJECTS_RETURN_TO') }}
                <a href="{{ $reviewBrowseUrl }}" class="link link-primary">{{ Lang::txt('COM_PROJECTS_PROJECT_LIST') }}</a>
            </span>
        </div>
    @endif

    @include('projects::_topheader', [
        'model'      => $model,
        'publicView' => true,
        'option'     => $option,
    ])

    @include('projects::_topmenu', [
        'model'      => $model,
        'active'     => $active,
        'tabs'       => $tabs ?? [],
        'option'     => $option,
        'guest'      => User::isGuest(),
        'publicView' => true,
    ])

    <section class="main section p-4">
        <div class="project-inner-wrap">
            @if ($model->allowMembershipRequest())
                @if (!$member || $member->status == 2)
                    <div class="mb-4">
                        <a href="{{ $requestUrl }}" class="btn btn-success">
                            {{ Lang::txt('COM_PROJECTS_REQUEST_MEMBERSHIP') }}
                        </a>
                    </div>
                @elseif ($member->get('status') == 3)
                    <div class="mb-4 tooltip" data-tip="Membership Request Pending">
                        <button class="btn btn-success btn-disabled" disabled>
                            {{ Lang::txt('COM_PROJECTS_REQUEST_MEMBERSHIP') }}
                        </button>
                    </div>
                @elseif ($member->get('status') == 4)
                    @php
                        $memberParams = new \Hubzero\Config\Registry($member->get('params'));
                        $denyMessage = 'Membership has been denied. Reason: ' . $memberParams->get('denyMessage');
                    @endphp
                    <div class="mb-4 tooltip" data-tip="{{ $denyMessage }}">
                        <button class="btn btn-success btn-disabled" disabled>
                            {{ Lang::txt('COM_PROJECTS_REQUEST_MEMBERSHIP') }}
                        </button>
                    </div>
                @endif
            @endif

            @if ($model->about('parsed'))
                @php
                    $aboutVal = $model->about('parsed');
                    $componentPath = Component::path('com_redirect');
                    if ($componentPath) {
                        $aboutVal = \Components\Redirect\Helpers\Converter::convert($aboutVal);
                    } else {
                        $aboutVal = preg_replace(
                            '#<a\s[^>]*href="([^"]*)"[^>]*?>(.*?)</a>#is',
                            "<a href='$1' rel='nofollow'>$2</a>",
                            $aboutVal
                        );
                    }
                @endphp
                <div class="mb-2">
                    <h3 class="text-lg font-semibold">{{ Lang::txt('COM_PROJECTS_ABOUT') }}</h3>
                </div>
                <div class="prose max-w-none mb-6">
                    {!! $aboutVal !!}
                </div>
            @endif

            @php
                $sections = Event::trigger('projects.onProjectPublicList', [$model]);
            @endphp

            @if (!empty($sections))
                @foreach ($sections as $section)
                    @if (!empty($section))
                        {!! $section !!}
                    @endif
                @endforeach
            @endif
        </div>
    </section>
</div>
