{{--
 * Member profile view page
 *
 * Shows a member's profile with plugin-driven content tabs in a
 * two-pane sidebar + main layout.
 *
 * Variables:
 *   $profile            - Member profile object
 *   $active             - Active tab key (e.g. 'profile', 'dashboard')
 *   $cats               - Array of tab categories from plugins
 *   $sections           - Array of tab sections (html + metadata)
 *   $overwrite_content  - Override content string (if set, replaces sections)
 *   $config             - Component config Registry
 *   $title              - Page title
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Event;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Notify;
    use Hubzero\Facades\Plugin;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Session;
    use Hubzero\Facades\User;

    $no_html = Request::getInt('no_html', 0);
    $user_messaging = Plugin::isEnabled('members', 'messages')
        ? $config->get('user_messaging', 0)
        : 0;

    $prefix = $profile->get('name') . "'s";
    $edit = false;
    $password = false;
    $messaging = false;

    $tab = $active;
    $tab_name = 'Dashboard';

    // Determine if messaging is allowed
    switch ($user_messaging) {
        case 0:
            $messaging = false;
            break;
        case 1:
            $common = \Hubzero\User\Helper::getCommonGroups(User::get('id'), $profile->get('id'));
            if (count($common) > 0) {
                $messaging = true;
            }
            break;
        case 2:
            $messaging = true;
            break;
    }

    // If user is viewing their own profile, enable editing and disable messaging
    if ($profile->get('id') == User::get('id')) {
        if ($active == 'profile') {
            $edit = true;
            $password = true;
        }
        $messaging = false;
        $prefix = 'My';
    }

    // No messaging if guest or account has an invalid email
    if (User::isGuest() || substr($profile->get('email'), -8) == '@invalid') {
        $messaging = false;
    }

    // Compute tab_name from cats before rendering sidebar
    foreach ($cats as $k => $c) {
        $key = key($c);
        if (!$key) {
            continue;
        }
        if ($active == $key) {
            $tab_name = $c[$key];
            break;
        }
    }
@endphp

@if (!$no_html)
    @php
        $__view->css()->js();
    @endphp

    <div class="innerwrap">
        <div id="page_container" class="flex flex-col lg:flex-row gap-6">
            <div id="page_sidebar" class="lg:w-64 shrink-0">
                <div id="page_identity" class="mb-4 text-center">
                    @php
                        $identityTitle = ($profile->get('id') == User::get('id'))
                            ? Lang::txt('COM_MEMBERS_GO_TO_MY_DASHBOARD')
                            : Lang::txt('COM_MEMBERS_GO_TO_MEMBER_PROFILE', $profile->get('name'));
                        $profilePic = $profile->picture(0, false);
                        $picAlt = Lang::txt(
                            'COM_MEMBERS_PROFILE_PICTURE_FOR',
                            e(stripslashes($profile->get('name')))
                        );
                    @endphp
                    <a href="{{ Route::url($profile->link()) }}"
                       id="page_identity_link"
                       title="{{ $identityTitle }}">
                        <img src="{{ $profilePic }}"
                             alt="{{ $picAlt }}"
                             class="rounded-full size-24 mx-auto object-cover" />
                    </a>
                </div>

                @if ($messaging)
                    <div class="mb-4 text-center">
                        @php
                            $msgLabel = Lang::txt('COM_MEMBERS_MESSAGE');
                            $escapedName = e(stripslashes($profile->get('name')));
                            $msgHref = Route::url(
                                'index.php?option=com_members&id=' . User::get('id')
                                . '&active=messages&task=new&to[]=' . $profile->get('id')
                            );
                        @endphp
                        <a class="btn btn-sm btn-outline w-full"
                           title="{{ Lang::txt('COM_MEMBERS_SEND_A_MESSAGE_TO', $escapedName) }}"
                           href="{{ $msgHref }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            {{ $msgLabel }}
                        </a>
                    </div>
                @endif

                @php
                    $memberProfileResults = Event::trigger('members.onMemberProfile', [$profile]);
                    $memberProfileResults = implode("\n", $memberProfileResults);
                @endphp

                @if ($memberProfileResults)
                    <div class="member-extensions mb-4">{!! $memberProfileResults !!}</div>
                @endif

                @include('profiles::_sidebar-menu', [
                    'cats'     => $cats,
                    'sections' => $sections,
                    'profile'  => $profile,
                    'active'   => $active,
                    'prefix'   => $prefix,
                ])

                @php
                    $statsThumb = '/site/stats/contributor_impact/impact_' . $profile->get('id') . '_th.gif';
                    $statsFull  = '/site/stats/contributor_impact/impact_' . $profile->get('id') . '.gif';
                @endphp

                @if (file_exists(PATH_APP . $statsThumb))
                    <a id="member-stats-graph"
                       rel="lightbox"
                       class="block mt-4"
                       title="{{ Lang::txt('COM_MEMBERS_MEMBER_IMPACT', $profile->get('name')) }}"
                       data-name="{{ $profile->get('name') }}"
                       data-type="Impact Graph"
                       href="{{ with(new \Hubzero\Content\Moderator(PATH_APP . $statsFull, 'public'))->getUrl() }}">
                        <img src="{{ with(new \Hubzero\Content\Moderator(PATH_APP . $statsThumb, 'public'))->getUrl() }}"
                             alt="{{ Lang::txt('COM_MEMBERS_MEMBER_IMPACT', $profile->get('name')) }}"
                             class="w-full rounded" />
                    </a>
                @endif
            </div>

            <div id="page_main" class="flex-1 min-w-0">
                <div id="page_header" class="mb-4">
                    @if ($profile->get('id') == User::get('id'))
                        @php
                            $privacyCls = 'badge-success';
                            $spanTitle = Lang::txt('COM_MEMBERS_PUBLIC_PROFILE_TITLE');
                            $privacyTitle = Lang::txt('COM_MEMBERS_PUBLIC_PROFILE_SET_PRIVATE_TITLE');

                            if ($profile->get('access') == 2) {
                                $privacyCls = 'badge-warning';
                                $spanTitle = Lang::txt('COM_MEMBERS_PROTECTED_PROFILE_TITLE');
                                $privacyTitle = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_SET_PUBLIC_TITLE');
                            }

                            if ($profile->get('access') > 2) {
                                $privacyCls = 'badge-error';
                                $spanTitle = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_TITLE');
                                $privacyTitle = Lang::txt('COM_MEMBERS_PRIVATE_PROFILE_SET_PUBLIC_TITLE');
                            }
                        @endphp

                        <div class="text-sm text-base-content/60 mb-1">
                            @if ($active == 'profile')
                                @php
                                    $privacyHref = Route::url($profile->link() . '?' . Session::getFormToken() . '=1');
                                @endphp
                                <a id="profile-privacy"
                                   href="{{ $privacyHref }}"
                                   data-id="{{ $profile->get('id') }}"
                                   data-private="{{ Lang::txt('Click here to set your profile private.') }}"
                                   data-public="{{ Lang::txt('Click here to set your profile public.') }}"
                                   class="badge {{ $privacyCls }} badge-sm"
                                   title="{{ $privacyTitle }}">
                                    {{ $spanTitle }}
                                </a>
                            @else
                                <span id="profile-privacy"
                                      data-id="{{ $profile->get('id') }}"
                                      data-private="{{ Lang::txt('Click here to set your profile private.') }}"
                                      data-public="{{ Lang::txt('Click here to set your profile public.') }}"
                                      class="badge {{ $privacyCls }} badge-sm">
                                    {{ $spanTitle }}
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2 min-w-0">
                            <h2 class="text-xl font-bold truncate">
                                <a href="{{ Route::url($profile->link()) }}" class="link link-hover">
                                    {{ e(stripslashes($profile->get('name'))) }}
                                </a>
                            </h2>
                            <span class="text-base-content/30" aria-hidden="true">&#9658;</span>
                            <h3 class="text-lg text-base-content/70">{{ $tab_name }}</h3>
                        </div>

                        @if ($edit || $password)
                            <div class="flex gap-2 shrink-0">
                                @if ($edit)
                                    @php
                                        $editHref = Route::url($profile->link() . '&task=edit');
                                    @endphp
                                    <a class="btn btn-sm btn-ghost"
                                       id="edit-profile"
                                       title="{{ Lang::txt('COM_MEMBERS_EDIT_PROFILE') }}"
                                       href="{{ $editHref }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        {{ Lang::txt('COM_MEMBERS_EDIT_PROFILE') }}
                                    </a>
                                @endif
                                @if ($password)
                                    @php
                                        $changePassUrl = Route::url($profile->link('changepassword'));
                                    @endphp
                                    <a class="btn btn-sm btn-ghost"
                                       id="change-password"
                                       title="{{ Lang::txt('COM_MEMBERS_CHANGE_PASSWORD') }}"
                                       href="{{ $changePassUrl }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                        </svg>
                                        {{ Lang::txt('COM_MEMBERS_CHANGE_PASSWORD') }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div id="page_notifications">
                    @if ($__view->getError())
                        <div class="alert alert-error mb-4" role="alert">
                            {!! implode('<br />', $__view->getErrors()) !!}
                        </div>
                    @endif

                    @php
                        $notificationResults = [];
                        $notifications = Notify::messages('com_members.profile');
                        foreach ($notifications as $notification) {
                            $notificationResults[] = $notification['message'];
                        }
                        $notificationText = implode("<br />\n", $notificationResults);
                    @endphp

                    @if ($notificationText)
                        <div class="alert alert-info mb-4" role="status">
                            {!! $notificationText !!}
                        </div>
                    @endif
                </div>

                <div id="page_content" class="member_{{ $active }}">
@endif

@if ($overwrite_content)
    {!! $overwrite_content !!}
@else
    @foreach ($sections as $s)
        @if ($s['html'] != '')
            {!! $s['html'] !!}
        @endif
    @endforeach
@endif

@if (!$no_html)
                </div>
            </div>
        </div>
    </div>
@endif
