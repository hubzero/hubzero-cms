{{--
 * Member directory introduction / welcome page
 *
 * Variables:
 *   $title   - Page title
 *   $option  - Component option (e.g. 'com_members')
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $__view->css('introduction.css', 'system')->css();

    $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
@endphp

<x-page-container :title="$title">
    @if(User::isGuest())
        @slot('actions')
            <a class="btn btn-primary"
               href="{{ Route::url('index.php?option=com_members&controller=register') }}">
                {{ Lang::txt('COM_MEMBERS_REGISTER_NOW') }}
            </a>
        @endslot
    @endif

    {{-- Introduction section --}}
    <div id="introduction" class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3>{{ Lang::txt('COM_MEMBERS_WHY_BECOME_MEMBER') }}</h3>
                    <p>{{ Lang::txt('COM_MEMBERS_WHY_BECOME_MEMBER_EXPLANATION') }}</p>
                </div>
                <div>
                    <h3>{{ Lang::txt('COM_MEMBERS_HOW_TO_BECOME_MEMBER') }}</h3>
                    <p>{{ Lang::txt('COM_MEMBERS_HOW_TO_BECOME_MEMBER_EXPLANATION') }}</p>
                </div>
            </div>
        </div>
        <div>
            <ul class="menu bg-base-200 rounded-box">
                <li>
                    <a href="{{ Route::url('index.php?option=com_members&view=credentials&layout=remind') }}">
                        {{ Lang::txt('COM_MEMBERS_FORGOT_USERNAME') }}
                    </a>
                </li>
                <li>
                    <a href="{{ Route::url('index.php?option=com_members&view=credentials&layout=reset') }}">
                        {{ Lang::txt('COM_MEMBERS_FORGOT_PASSWORD') }}
                    </a>
                </li>
                <li>
                    <a class="popup" href="{{ Route::url('index.php?option=com_help&component=members') }}">
                        {{ Lang::txt('COM_MEMBERS_NEED_HELP') }}
                    </a>
                </li>
                <li>
                    <a href="{{ Route::url('index.php?option=com_groups') }}">
                        {{ Lang::txt('COM_GROUPS') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Find Members section --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div>
            <h2>{{ Lang::txt('COM_MEMBERS_FIND_MEMBERS') }}</h2>
        </div>
        <div class="lg:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <x-search-bar
                        :action="$browseUrl"
                        name="search"
                        :placeholder="Lang::txt('COM_MEMBERS_FIND_MEMBERS_SEARCH_LABEL')"
                        :buttonLabel="Lang::txt('Search')"
                    />
                    <p>{{ Lang::txt('COM_MEMBERS_FIND_MEMBERS_BY_SEARCH') }}</p>
                </div>
                <div>
                    <p>
                        <a href="{{ $browseUrl }}">
                            {{ Lang::txt('COM_MEMBERS_FIND_MEMBERS_BY_BROWSING') }}
                        </a>
                    </p>
                    <p>{{ Lang::txt('COM_MEMBERS_FIND_MEMBERS_LISTING') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-page-container>
