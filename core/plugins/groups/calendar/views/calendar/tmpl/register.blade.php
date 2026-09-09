{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$year  = date("Y", strtotime($event->get('publish_up')));
$month = date("m", strtotime($event->get('publish_up')));

$cn = $group->get('cn');
$eventId = $event->get('id');
$baseUrl = 'index.php?option=' . $option
    . '&cn=' . $cn . '&active=calendar';
$deleteUrl = Route::url(
    $baseUrl . '&action=delete&event_id=' . $eventId
);
$editUrl = Route::url(
    $baseUrl . '&action=edit&event_id=' . $eventId
);
$detailsUrl = Route::url(
    $baseUrl . '&action=details&event_id=' . $eventId
);
$registerUrl = Route::url(
    $baseUrl . '&action=register&event_id=' . $eventId
);
$registrantsUrl = Route::url(
    $baseUrl . '&action=registrants&event_id=' . $eventId
);
$calendarUrl = Route::url(
    'index.php?option=' . $option
    . '&cn=' . $group->cn
    . '&active=calendar&year=' . $year
    . '&month=' . $month
);
$isOwnerOrManager = $user->get('id') == $event->get('created_by')
    || $authorized == 'manager';
$hasRegistration = $event->get('registerby')
    && $event->get('registerby') != '0000-00-00 00:00:00';

$firstName = $register['first_name'] ?? '';
$lastName = $register['last_name'] ?? '';
$affiliation = $register['affiliation'] ?? '';
$telephone = $register['telephone'] ?? '';
$positionOther = $register['position_other'] ?? '';
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

<ul id="page_options">
    <li>
        <a class="btn btn-ghost gap-2" href="{{ $calendarUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            {{ Lang::txt('Back to Calendar') }}
        </a>
    </li>
</ul>

<div class="flex items-center justify-between mb-4">
    <h3 class="text-xl font-semibold">
        {{ $event->get('title') }}
    </h3>
    @if ($isOwnerOrManager)
        <div class="flex gap-2">
            <a class="btn btn-sm btn-outline" href="{{ $editUrl }}">{{ Lang::txt('Edit') }}</a>
            <a class="btn btn-sm btn-error btn-outline" href="{{ $deleteUrl }}">{{ Lang::txt('Delete') }}</a>
        </div>
    @endif
</div>

<div role="tablist" class="tabs tabs-border mb-6">
    <a role="tab" class="tab" href="{{ $detailsUrl }}">
        {{ Lang::txt('Details') }}
    </a>
    @if ($hasRegistration)
        <a role="tab" class="tab tab-active" href="{{ $registerUrl }}">
            {{ Lang::txt('Register') }}
        </a>
        @if ($isOwnerOrManager)
            <a role="tab" class="tab" href="{{ $registrantsUrl }}">
                {{ Lang::txt('Registrants') }} ({{ $registrants }})
            </a>
        @endif
    @endif
</div>

<form action="{{ $registerUrl }}"
    id="hubForm"
    method="post"
    class="full">
    <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
        <legend class="font-semibold px-2">{{ Lang::txt('Name & Title') }}</legend>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ Lang::txt('First Name:') }}</span>
                    <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required') }}</span>
                </label>
                <input type="text"
                    name="register[first_name]"
                    class="input input-bordered w-full"
                    value="{{ e($firstName) }}" />
            </div>
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ Lang::txt('Last Name:') }}</span>
                    <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required') }}</span>
                </label>
                <input type="text"
                    name="register[last_name]"
                    class="input input-bordered w-full"
                    value="{{ e($lastName) }}" />
            </div>
        </div>

        @if ($params->get('show_affiliation') || $params->get('show_title'))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                @if ($params->get('show_affiliation'))
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('Affiliation:') }}</span>
                            <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required') }}</span>
                        </label>
                        <input type="text"
                            name="register[affiliation]"
                            class="input input-bordered w-full"
                            value="{{ e($affiliation) }}" />
                    </div>
                @endif
                @if ($params->get('show_title'))
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('Title:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        <input type="text"
                            name="register[title]"
                            class="input input-bordered w-full"
                            value="{{ e($register['title'] ?? '') }}" />
                    </div>
                @endif
            </div>
        @endif
    </fieldset>

    <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
        <legend class="font-semibold px-2">{{ Lang::txt('Contact Information') }}</legend>

        @if ($params->get('show_address'))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">{{ Lang::txt('City:') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <input type="text" name="register[city]" class="input input-bordered w-full"
                        value="{{ e($register['city'] ?? '') }}" />
                </div>
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">{{ Lang::txt('State/Province:') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <input type="text" name="register[state]" class="input input-bordered w-full"
                        value="{{ e($register['state'] ?? '') }}" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">{{ Lang::txt('Zip/Postal code:') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <input type="text" name="register[zip]" class="input input-bordered w-full"
                        value="{{ e($register['zip'] ?? '') }}" />
                </div>
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">{{ Lang::txt('Country:') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <input type="text" name="register[country]" class="input input-bordered w-full"
                        value="{{ e($register['country'] ?? '') }}" />
                </div>
            </div>
        @endif

        @if ($params->get('show_telephone') || $params->get('show_fax'))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                @if ($params->get('show_telephone'))
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('Telephone:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        <input type="text" name="register[telephone]" class="input input-bordered w-full"
                            value="{{ e($telephone) }}" />
                    </div>
                @endif
                @if ($params->get('show_fax'))
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('Fax:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        <input type="text" name="register[fax]" class="input input-bordered w-full"
                            value="{{ e($register['fax'] ?? '') }}" />
                    </div>
                @endif
            </div>
        @endif

        @if ($params->get('show_email') || $params->get('show_website'))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if ($params->get('show_email'))
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('E-mail:') }}</span>
                            <span class="label-text-alt badge badge-error badge-sm">{{ Lang::txt('Required') }}</span>
                        </label>
                        <input type="text" name="register[email]" class="input input-bordered w-full"
                            value="{{ e($register['email'] ?? '') }}" />
                    </div>
                @endif
                @if ($params->get('show_website'))
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('Website:') }}</span>
                            <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                        </label>
                        <input type="text" name="register[website]" class="input input-bordered w-full"
                            value="{{ e($register['website'] ?? '') }}" />
                    </div>
                @endif
            </div>
        @endif
    </fieldset>

    @php
    $showDemographics = $params->get('show_position')
        || $params->get('show_degree')
        || $params->get('show_gender')
        || $params->get('show_race');
    @endphp

    @if ($showDemographics)
        <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
            <legend class="font-semibold px-2">{{ Lang::txt('Demographics') }}</legend>

            @if ($params->get('show_position'))
                <div class="form-control w-full mb-4">
                    <label class="label">
                        <span class="label-text">{{ Lang::txt('Which best describes your current position?') }}</span>
                        <span class="label-text-alt badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </label>
                    <select name="register[position]" class="select select-bordered w-full">
                        <option value="" selected>{{ Lang::txt('(select from list or enter below)') }}</option>
                        <option value="university">{{ Lang::txt('University / College Student or Staff') }}</option>
                        <option value="precollege">{{ Lang::txt('K-12 (Pre-College) Student or Staff') }}</option>
                        <option value="nationallab">{{ Lang::txt('National Laboratory') }}</option>
                        <option value="industry">{{ Lang::txt('Industry / Private Company') }}</option>
                        <option value="government">{{ Lang::txt('Government Agency') }}</option>
                        <option value="military">{{ Lang::txt('Military') }}</option>
                        <option value="unemployed">{{ Lang::txt('Retired / Unemployed') }}</option>
                    </select>
                    <input name="register[position_other]"
                        type="text"
                        class="input input-bordered w-full mt-2"
                        value="{{ e($positionOther) }}" />
                </div>
            @endif

            @if ($params->get('show_degree'))
                <div class="mb-4">
                    <p class="font-medium mb-2">
                        {{ Lang::txt('Highest academic degree earned:') }}
                        <span class="badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </p>
                    @php
                    $degreeVal = $register['degree'] ?? '';
                    @endphp
                    <div class="flex flex-col gap-2">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="radio" name="register[degree]" value="Bachelors" class="radio radio-sm" {{ $degreeVal == 'Bachelors' ? 'checked' : '' }} />
                            <span class="label-text">{{ Lang::txt('Bachelors degree') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="radio" name="register[degree]" value="Masters" class="radio radio-sm" {{ $degreeVal == 'Masters' ? 'checked' : '' }} />
                            <span class="label-text">{{ Lang::txt('Masters degree') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="radio" name="register[degree]" value="Doctoral" class="radio radio-sm" {{ $degreeVal == 'Doctoral' ? 'checked' : '' }} />
                            <span class="label-text">{{ Lang::txt('Doctoral degree') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="radio" name="register[degree]" value="Other" class="radio radio-sm" {{ $degreeVal == 'Other' ? 'checked' : '' }} />
                            <span class="label-text">{{ Lang::txt('None of the above') }}</span>
                        </label>
                    </div>
                </div>
            @endif

            @if ($params->get('show_gender'))
                @php
                $sexVal = $register['sex'] ?? '';
                @endphp
                <div class="mb-4">
                    <p class="font-medium mb-2">
                        {{ Lang::txt('Gender:') }}
                        <span class="badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </p>
                    <div class="flex flex-col gap-2">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="radio" name="register[sex]" value="Male" class="radio radio-sm" {{ $sexVal == 'Male' ? 'checked' : '' }} />
                            <span class="label-text">{{ Lang::txt('Male') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="radio" name="register[sex]" value="Female" class="radio radio-sm" {{ $sexVal == 'Female' ? 'checked' : '' }} />
                            <span class="label-text">{{ Lang::txt('Female') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="radio" name="register[sex]" value="Refused" class="radio radio-sm" {{ $sexVal == 'Refused' ? 'checked' : '' }} />
                            <span class="label-text">{{ Lang::txt('Do not wish to reveal') }}</span>
                        </label>
                    </div>
                </div>
            @endif

            @if ($params->get('show_race'))
                <div class="mb-4">
                    <p class="font-medium mb-2">
                        {{ Lang::txt('Race:') }}
                        <span class="badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </p>
                    <p class="text-sm text-base-content/60 mb-2">{{ Lang::txt('Select one or more that apply.') }}</p>
                    <div class="flex flex-col gap-2">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="race[nativeamerican]" id="racenativeamerican" value="Native American" class="checkbox checkbox-sm" />
                            <span class="label-text">{{ Lang::txt('American Indian or Alaska Native') }}</span>
                        </label>
                        <div class="ml-8 form-control w-full">
                            <label class="label">
                                <span class="label-text">{{ Lang::txt('Tribal Affiliation(s):') }}</span>
                            </label>
                            <input name="race[nativetribe]" id="racenativetribe" type="text" class="input input-bordered input-sm w-full" value="" />
                        </div>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="race[asian]" id="raceasian" value="Asian" class="checkbox checkbox-sm" />
                            <span class="label-text">{{ Lang::txt('Asian') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="race[black]" id="raceblack" value="African American" class="checkbox checkbox-sm" />
                            <span class="label-text">{{ Lang::txt('Black or African American') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="race[hawaiian]" id="racehawaiian" value="Hawaiian" class="checkbox checkbox-sm" />
                            <span class="label-text">{{ Lang::txt('Native Hawaiian or Other Pacific Islander') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="race[white]" id="racewhite" value="White" class="checkbox checkbox-sm" />
                            <span class="label-text">{{ Lang::txt('White') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="race[hispanic]" id="racehispanic" value="Hispanic" class="checkbox checkbox-sm" />
                            <span class="label-text">{{ Lang::txt('Hispanic or Latino') }}</span>
                        </label>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="race[refused]" id="racerefused" value="Refused" class="checkbox checkbox-sm" />
                            <span class="label-text">{{ Lang::txt('Do not wish to reveal') }}</span>
                        </label>
                    </div>
                </div>
            @endif
        </fieldset>
    @endif

    @if ($params->get('show_arrival') || $params->get('show_departure'))
        <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
            <legend class="font-semibold px-2">{{ Lang::txt('Arrival/Departure') }}</legend>

            @if ($params->get('show_arrival'))
                <div class="mb-4">
                    <p class="font-medium mb-2">
                        {{ Lang::txt('Arrival Information:') }}
                        <span class="badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">{{ Lang::txt('Arrival Day') }}</span></label>
                            <input type="text" name="arrival[day]" class="input input-bordered w-full"
                                value="{{ e($arrival['day'] ?? '') }}" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">{{ Lang::txt('Arrival Time') }}</span></label>
                            <input type="text" name="arrival[time]" class="input input-bordered w-full"
                                value="{{ e($arrival['time'] ?? '') }}" />
                        </div>
                    </div>
                </div>
            @endif

            @if ($params->get('show_departure'))
                <div class="mb-4">
                    <p class="font-medium mb-2">
                        {{ Lang::txt('Departure Information:') }}
                        <span class="badge badge-ghost badge-sm">{{ Lang::txt('Optional') }}</span>
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">{{ Lang::txt('Departure Day') }}</span></label>
                            <input type="text" name="departure[day]" class="input input-bordered w-full"
                                value="{{ e($departure['day'] ?? '') }}" />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">{{ Lang::txt('Departure Time') }}</span></label>
                            <input type="text" name="departure[time]" class="input input-bordered w-full"
                                value="{{ e($departure['time'] ?? '') }}" />
                        </div>
                    </div>
                </div>
            @endif
        </fieldset>
    @endif

    @if ($params->get('show_disability') || $params->get('show_dietary'))
        <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
            <legend class="font-semibold px-2">{{ Lang::txt('Disability/Dietary needs') }}</legend>

            @if ($params->get('show_disability'))
                <label class="label cursor-pointer justify-start gap-2 mb-2">
                    <input type="checkbox" name="disability" value="yes" class="checkbox checkbox-sm"
                        {{ (isset($disability) && $disability == 'yes') ? 'checked' : '' }} />
                    <span class="label-text">{{ Lang::txt('I have auxiliary aids or services due to a disability. Please contact me.') }}</span>
                </label>
            @endif

            @if ($params->get('show_dietary'))
                <label class="label cursor-pointer justify-start gap-2 mb-2">
                    <input type="checkbox" name="dietary[needs]" value="yes" class="checkbox checkbox-sm"
                        {{ (isset($dietary['needs']) && $dietary['needs'] == 'yes') ? 'checked' : '' }} />
                    <span class="label-text">{{ Lang::txt('I have specific dietary needs.') }}</span>
                </label>
                <div class="form-control w-full ml-8">
                    <label class="label"><span class="label-text">{{ Lang::txt('Please specify') }}</span></label>
                    <input type="text" name="dietary[specific]" class="input input-bordered w-full"
                        value="{{ e($dietary['specific'] ?? '') }}" />
                </div>
            @endif
        </fieldset>
    @endif

    @if ($params->get('show_dinner'))
        <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
            <legend class="font-semibold px-2">{{ Lang::txt('Dinner') }}</legend>
            <label class="label cursor-pointer justify-start gap-2">
                <input type="checkbox" name="dinner" id="filed-dinner" value="yes" class="checkbox checkbox-sm"
                    {{ (isset($dinner) && $dinner == 'yes') ? 'checked' : '' }} />
                <span class="label-text">{{ Lang::txt('I plan to attend the dinner.') }}</span>
            </label>
        </fieldset>
    @endif

    @if ($params->get('show_abstract'))
        <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
            <legend class="font-semibold px-2">{{ Lang::txt('Abstract') }}</legend>
            <div class="form-control w-full">
                @if ($params->get('abstract_text'))
                    <label class="label">
                        <span class="label-text">{!! stripslashes($params->get('abstract_text')) !!}</span>
                    </label>
                @endif
                <textarea name="register[abstract]"
                    rows="16"
                    cols="32"
                    class="textarea textarea-bordered w-full">{{ $register['abstract'] ?? '' }}</textarea>
            </div>
        </fieldset>
    @endif

    @if ($params->get('show_comments'))
        <fieldset class="border border-base-300 rounded-lg p-4 mb-4">
            <legend class="font-semibold px-2">{{ Lang::txt('Comments') }}</legend>
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">{{ Lang::txt('Please use the space below to provide any additional comments:') }}</span>
                </label>
                <textarea name="register[comment]"
                    rows="4"
                    cols="32"
                    class="textarea textarea-bordered w-full">{{ $register['comment'] ?? '' }}</textarea>
            </div>
        </fieldset>
    @endif

    <input type="hidden" name="option" value="com_groups" />
    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="active" value="calendar" />
    <input type="hidden" name="action" value="doregister" />
    <input type="hidden" name="event_id" value="{{ $event->get('id') }}" />

    <div class="mt-6">
        <button type="submit" name="event_submit" class="btn btn-primary">
            {{ Lang::txt('Submit') }}
        </button>
    </div>
</form>
