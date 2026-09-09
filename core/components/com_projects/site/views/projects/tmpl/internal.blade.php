{{--
 * Project internal/member dashboard — bespoke layout
 *
 * Two layout variants: extended (top header + horizontal nav) and
 * standard (sidebar nav + content). Does NOT use <x-page-container>.
 *
 * Variables:
 *   $model   - Project model object
 *   $option  - Component option string
 *   $title   - Page title
 *   $active  - Active tab name
 *   $tabs    - Array of plugin tab definitions
 *   $content - Plugin-rendered content HTML
 *   $msg     - Status message string
 *   $config  - Component config Registry
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Document;
    use Hubzero\Facades\User;
    use Hubzero\Facades\Event;

    $__view->css()->js()->css('jquery.fancybox.css', 'system');

    $counts = $model->get('counts');
    $new = isset($counts['new']) && $counts['new'] > 0 ? $counts['new'] : 0;

    // Add new activity count to page title
    $pageTitle = $new && $active == 'feed'
        ? $title . ' (' . $new . ')'
        : $title;
    Document::setTitle($pageTitle);

    $params = $model->params;
    $layout = $params->get('layout', $config->get('layout', 'standard'));
    $theme = $params->get('theme', $config->get('theme', 'light'));

    if ($layout == 'extended') {
        $__view->css('extended.css')->css('theme' . $theme . '.css');
    } else {
        $__view->css('standard.css');
    }

    // Get notifications
    $notification = Event::trigger('projects.onProjectNotification', [$model, $active]);
    $notification = $notification && !empty($notification) ? $notification[0] : null;

    // Get side content
    $sideContent = Event::trigger('projects.onProjectExtras', [$model, $active]);
    $sideContent = $sideContent && !empty($sideContent) ? $sideContent[0] : null;
@endphp

<div id="project-wrap" class="theme">
    @if ($layout == 'extended')
        @include('projects::_topheader', [
            'model'      => $model,
            'publicView' => false,
            'option'     => $option,
        ])
        @include('projects::_topmenu', [
            'model'      => $model,
            'active'     => $active,
            'tabs'       => $tabs,
            'option'     => $option,
            'guest'      => User::isGuest(),
            'publicView' => false,
        ])
        <div class="project-inner-wrap p-4">
    @else
        @include('projects::_header', [
            'model'         => $model,
            'showPic'       => 0,
            'showPrivacy'   => 2,
            'showOptions'   => 1,
            'goBack'        => 0,
            'showUnderline' => 1,
            'option'        => $option,
        ])
        <div class="flex gap-6 project-inner-wrap" id="project-innerwrap">
            <div class="w-56 shrink-0 main-menu">
                @include('projects::_image', [
                    'model'  => $model,
                    'option' => $option,
                ])
                @include('projects::_menu', [
                    'model'  => $model,
                    'active' => $active,
                    'tabs'   => $tabs,
                    'option' => $option,
                ])
            </div>
            <div class="flex-1 min-w-0 main-content">
    @endif

                @include('projects::_statusmsg', [
                    'error' => $__view->getError(),
                    'msg'   => $msg,
                ])

                <div id="plg-content" class="content-{{ $active }}">
                    @if ($notification)
                        {!! $notification !!}
                    @endif

                    @if ($sideContent)
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-9 main-col">
                    @endif

                                @if ($content)
                                    {!! $content !!}
                                @endif

                    @if ($sideContent)
                            </div>
                            <div class="md:col-span-3 side-col">
                                <div class="side-content">
                                    {!! $sideContent !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            @if ($layout != 'extended')
            </div>{{-- / .main-content --}}
            @endif
        </div>
</div>
