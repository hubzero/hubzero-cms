{{--
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $__view->js('orcid.js')->css('orcid.css');

    $fname = Request::getString('fname', '');
    $lname = Request::getString('lname', '');
    $email = Request::getString('email', '');

    $returnOrcid = Request::getInt('return', 0);
    $isRegister = $returnOrcid == 1;

    if (!$isRegister) {
        $userProfile = User::getInstance();
        if ($userProfile) {
            $fname = $fname ?: $userProfile->get('givenName');
            $lname = $lname ?: $userProfile->get('surname');
            $email = $email ?: $userProfile->get('email');
        }
    }

    $srv = $config->get('orcid_service', 'members');
    $tkn = $config->get('orcid_' . $srv . '_token');
    $clientID = $config->get('orcid_' . $srv . '_client_id', '');
    $redirectURI = $config->get('orcid_' . $srv . '_redirect_uri', '');
@endphp

<x-page-container :title="Lang::txt('COM_MEMBERS_PROFILE_ORCID_ASSOCIATE_ORCID')">
    <form name="orcid-search-form">
        @if ($srv != 'public' && !$tkn)
            <div class="alert alert-warning">
                {!! Lang::txt('COM_MEMBERS_PROFILE_ORCID_UNAVAILABLE', Route::url('index.php?option=com_support')) !!}
            </div>
        @else
            <h3>{{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_ASSOCIATE_ORCID') }}</h3>
            <fieldset>
                <legend>{{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_PROFILE_INFO') }}</legend>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="first-name">
                            {{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_FIRST_NAME') }}<span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_SEARCH_ORCID_REQUIRED') }}</span>
                            <input
                                type="text"
                                id="first-name"
                                name="first-name"
                                class="input input-bordered w-full"
                                value="{{ e($fname) }}" />
                        </label>
                    </div>
                    <div>
                        <label for="last-name">
                            {{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_LAST_NAME') }}<span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_SEARCH_ORCID_REQUIRED') }}</span>
                            <input
                                type="text"
                                id="last-name"
                                name="last-name"
                                class="input input-bordered w-full"
                                value="{{ e($lname) }}" />
                        </label>
                    </div>
                    <div id="alert-message" class="hidden">
                        <p>{{ Lang::txt('COM_MEMBERS_SEARCH_ORCID_ALERT_NAME') }}</p>
                    </div>
                </div>

                <input
                    type="hidden"
                    name="base_uri"
                    id="base_uri"
                    value="{{ rtrim(Request::base(true), '/') }}" />
            </fieldset>

            <div class="orcid-section orcid-search">
                <h4>{{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_SEARCH_FOR_EXISTING') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <p>{{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_FILL_AND_SEARCH') }}</p>
                    </div>
                    <div>
                        <p>
                            <a
                                id="get-orcid-results"
                                class="btn btn-primary"
                                data-action="fetch-orcid"
                                data-fname="{{ e($fname) }}"
                                data-lname="{{ e($lname) }}">{{ Lang::txt('Search ORCID') }}</a>
                        </p>
                    </div>
                </div>

                <div id="section-orcid-results">
                    @isset($orcid_records_html)
                        {!! $orcid_records_html !!}
                    @endisset
                </div>
            </div>

            @if ($config->get('orcid_service', 'members') != 'public')
                <div class="orcid-section orcid-create">
                    <h4>{{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_CREATE_ORCID') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <p>{{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_CLICK_CREATE_BUTTON') }}</p>
                        </div>
                        <div>
                            @php
                                $orcidHost = ($config->get('orcid_service', 'members') == 'sandbox')
                                    ? 'sandbox.orcid.org'
                                    : 'orcid.org';
                                $orcidUrl = 'https://' . $orcidHost
                                    . '/oauth/authorize?client_id=' . $clientID
                                    . '&response_type=code'
                                    . '&scope=/authenticate'
                                    . '&redirect_uri=' . urlencode($redirectURI)
                                    . '&family_names=' . e($lname)
                                    . '&given_names=' . e($fname)
                                    . '&email=' . e($email);
                            @endphp
                            <p><a
                                id="create-orcid"
                                class="btn btn-primary"
                                href="{{ $orcidUrl }}"
                                rel="nofollow external">{{ Lang::txt('COM_MEMBERS_PROFILE_ORCID_CREATE_OR_CONNECT') }}</a></p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </form>
</x-page-container>
