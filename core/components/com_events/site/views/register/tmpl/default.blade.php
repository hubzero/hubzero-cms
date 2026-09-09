{{--
  Registration form for event
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$params = new \Hubzero\Config\Registry($event->params);

$regVal = function($key) use ($register) {
    return $register[$key] ?? '';
};

$yearUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year
);
$monthUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year . '&month=' . $month
);
$weekUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year . '&month=' . $month
    . '&day=' . $day . '&task=week'
);
$dayUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&year=' . $year . '&month=' . $month
    . '&day=' . $day
);
$detailsUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&task=details&id=' . $event->id
);
$registerUrl = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option . '&task=details&id=' . $event->id . '&page=register'
);

$periodTabs = [
    $yearUrl  => \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_YEAR'),
    $monthUrl => \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_MONTH'),
    $weekUrl  => \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_WEEK'),
    $dayUrl   => \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REP_DAY'),
];
$activePeriod = match($task) {
    'year'  => $yearUrl,
    'week'  => $weekUrl,
    'day'   => $dayUrl,
    default => $monthUrl,
};

$subTabs = [
    $detailsUrl => \Hubzero\Facades\Lang::txt('EVENTS_OVERVIEW'),
];
if ($pages) {
    foreach ($pages as $p) {
        $pUrl = \Hubzero\Facades\Route::url(
            'index.php?option=' . $option . '&task=details&id=' . $event->id
            . '&page=' . $p->alias
        );
        $subTabs[$pUrl] = trim(stripslashes($p->title));
    }
}
$subTabs[$registerUrl] = \Hubzero\Facades\Lang::txt('EVENTS_REGISTER');
$activeSubTab = $page->alias == '' ? $detailsUrl : ($page->alias == 'register'
    ? $registerUrl
    : \Hubzero\Facades\Route::url(
        'index.php?option=' . $option . '&task=details&id=' . $event->id
        . '&page=' . $page->alias
    ));
@endphp

<x-page-container :title="$title">
    @slot('actions')
        @if ($authorized)
            <a class="btn btn-primary btn-sm"
                href="{{ \Hubzero\Facades\Route::url('index.php?option=' . $option . '&task=add') }}">
                {{ \Hubzero\Facades\Lang::txt('EVENTS_ADD_EVENT') }}
            </a>
        @endif
    @endslot

    {{-- Period navigation tabs --}}
    <x-filter-tabs :options="$periodTabs" :active="$activePeriod" class="mb-6" />

    <h3 class="text-xl font-semibold mb-4">{{ stripslashes($event->title) }}</h3>

    {{-- Sub-tabs: Overview / Pages / Register --}}
    <x-filter-tabs :options="$subTabs" :active="$activeSubTab" class="mb-6" />

    @if ($__view->getError())
        <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
    @endif

    <form method="post" action="index.php" id="hubForm">
        <div class="mb-6 p-4 bg-base-200 rounded-box">
            <p class="font-semibold">{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_EXPLAINATION') }}</p>
            @if (trim($event->contact_info))
                {!! stripslashes($event->contact_info) !!}
            @else
                <p>{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_EXPLAINATION_NO_EXPLAINATION') }}</p>
            @endif
        </div>

        {{-- Name fieldset --}}
        <fieldset class="fieldset mb-6">
            <legend class="fieldset-legend">
                {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_NAME') }}
            </legend>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_FIRST_NAME') }}
                            <span class="text-error">{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REQUIRED') }}</span>
                        </span>
                    </div>
                    <input type="text"
                           name="register[firstname]"
                           value="{{ $regVal('firstname') }}"
                           class="input input-bordered w-full"
                           required />
                </label>
                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_LAST_NAME') }}
                            <span class="text-error">{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REQUIRED') }}</span>
                        </span>
                    </div>
                    <input type="text"
                           name="register[lastname]"
                           value="{{ $regVal('lastname') }}"
                           class="input input-bordered w-full"
                           required />
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                @if ($params->get('show_affiliation'))
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">
                                {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_AFFILIATION') }}
                                <span class="text-error">{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REQUIRED') }}</span>
                            </span>
                        </div>
                        <input type="text"
                               name="register[affiliation]"
                               value="{{ $regVal('affiliation') }}"
                               class="input input-bordered w-full"
                               required />
                    </label>
                @endif
                @if ($params->get('show_title'))
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">
                                {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_TITLE') }}
                            </span>
                        </div>
                        <input type="text"
                               name="register[title]"
                               value="{{ $regVal('title') }}"
                               class="input input-bordered w-full" />
                    </label>
                @endif
            </div>

            <input type="hidden" name="id" value="{{ $event->id }}" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="task" value="process" />
        </fieldset>

        {{-- Contact info fieldset --}}
        @if ($params->get('show_address')
            || $params->get('show_telephone')
            || $params->get('show_fax')
            || $params->get('show_email')
            || $params->get('show_website'))
            <fieldset class="fieldset mb-6">
                <legend class="fieldset-legend">
                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_INFO') }}
                </legend>
                @if ($params->get('show_address'))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_CITY') }}
                                </span>
                            </div>
                            <input type="text"
                                   name="register[city]"
                                   value="{{ $regVal('city') }}"
                                   class="input input-bordered w-full" />
                        </label>
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_STATE') }}
                                </span>
                            </div>
                            <input type="text"
                                   name="register[state]"
                                   value="{{ $regVal('state') }}"
                                   class="input input-bordered w-full" />
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_ZIP') }}
                                </span>
                            </div>
                            <input type="text"
                                   name="register[postalcode]"
                                   value="{{ $regVal('postalcode') }}"
                                   class="input input-bordered w-full" />
                        </label>
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_COUNTRY') }}
                                </span>
                            </div>
                            <input type="text"
                                   name="register[country]"
                                   value="{{ $regVal('country') }}"
                                   class="input input-bordered w-full" />
                        </label>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    @if ($params->get('show_telephone'))
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_PHONE') }}
                                </span>
                            </div>
                            <input type="text"
                                   name="register[telephone]"
                                   value="{{ $regVal('telephone') }}"
                                   class="input input-bordered w-full" />
                        </label>
                    @endif
                    @if ($params->get('show_fax'))
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_FAX') }}
                                </span>
                            </div>
                            <input type="text"
                                   name="register[fax]"
                                   value="{{ $regVal('fax') }}"
                                   class="input input-bordered w-full" />
                        </label>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    @if ($params->get('show_email'))
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_EMAIL') }}
                                    <span class="text-error">{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REQUIRED') }}</span>
                                </span>
                            </div>
                            <input type="text"
                                   name="register[email]"
                                   value="{{ $regVal('email') }}"
                                   class="input input-bordered w-full"
                                   required />
                        </label>
                    @endif
                    @if ($params->get('show_website'))
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_WEBSITE') }}
                                </span>
                            </div>
                            <input type="text"
                                   name="register[website]"
                                   value="{{ $regVal('website') }}"
                                   class="input input-bordered w-full" />
                        </label>
                    @endif
                </div>
            </fieldset>
        @endif

        {{-- Demographics fieldset --}}
        @if ($params->get('show_position')
            || $params->get('show_degree')
            || $params->get('show_gender')
            || $params->get('show_race'))
            <fieldset class="fieldset mb-6">
                <legend class="fieldset-legend">
                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DEMOGRAPHICS') }}
                </legend>

                @if ($params->get('show_position'))
                    @php
                    $posOpts = [
                        ''            => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_NULL',
                        'university'  => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_UNIVERSITY',
                        'precollege'  => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_PRECOLLEGE',
                        'nationallab' => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_NATIONALLAB',
                        'industry'    => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_INDUSTRY',
                        'government'  => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_GOVERNMENT',
                        'military'    => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_MILITARY',
                        'unemployed'  => 'COM_EVENTS_REGISTER_FIELD_POSITION_OPTION_UNEMPLOYED',
                    ];
                    @endphp
                    <label class="form-control w-full max-w-md">
                        <div class="label">
                            <span class="label-text">
                                {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_POSITION') }}
                            </span>
                        </div>
                        <select name="register[position]" class="select select-bordered w-full">
                            @foreach ($posOpts as $val => $langKey)
                                <option value="{{ $val }}" @selected($val === '')>
                                    {{ \Hubzero\Facades\Lang::txt($langKey) }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-control w-full max-w-md mt-2">
                        <input name="register[position_other]"
                               type="text"
                               value="{{ $regVal('position_other') }}"
                               class="input input-bordered w-full" />
                    </label>
                @endif

                @if ($params->get('show_degree'))
                    <fieldset class="fieldset mt-4">
                        <legend class="fieldset-legend">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_DEGREE') }}:
                        </legend>
                        @php
                        $degreeOpts = [
                            'bachelors'          => 'COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_BACHELORS',
                            'masters'            => 'COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_MASTERS',
                            'doctoral'           => 'COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_DOCTORAL',
                            'none of the above'  => 'COM_EVENTS_REGISTER_FIELD_DEGREE_OPTION_NULL',
                        ];
                        @endphp
                        @foreach ($degreeOpts as $val => $langKey)
                            <label class="flex items-center gap-2 cursor-pointer py-1">
                                <input type="radio"
                                       class="radio"
                                       name="register[degree]"
                                       value="{{ $val }}"
                                       @checked(($register['degree'] ?? '') == $val) />
                                <span>{{ \Hubzero\Facades\Lang::txt($langKey) }}</span>
                            </label>
                        @endforeach
                    </fieldset>
                @endif

                @if ($params->get('show_gender'))
                    <fieldset class="fieldset mt-4">
                        <legend class="fieldset-legend">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_GENDER') }}:
                        </legend>
                        @php
                        $genderOpts = [
                            'male'    => 'COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_MALE',
                            'female'  => 'COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_FEMALE',
                            'refused' => 'COM_EVENTS_REGISTER_FIELD_GENDER_OPTION_NULL',
                        ];
                        @endphp
                        @foreach ($genderOpts as $val => $langKey)
                            <label class="flex items-center gap-2 cursor-pointer py-1">
                                <input type="radio"
                                       class="radio"
                                       name="register[sex]"
                                       value="{{ $val }}"
                                       @checked(($register['sex'] ?? '') == $val) />
                                <span>{{ \Hubzero\Facades\Lang::txt($langKey) }}</span>
                            </label>
                        @endforeach
                    </fieldset>
                @endif

                @if ($params->get('show_race'))
                    <fieldset class="fieldset mt-4">
                        <legend class="fieldset-legend">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE') }}:
                        </legend>
                        <p class="text-sm text-base-content/70 mb-2">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_HINT') }}
                        </p>
                        <label class="flex items-center gap-2 cursor-pointer py-1">
                            <input type="checkbox"
                                   class="checkbox"
                                   name="race[nativeamerican]"
                                   id="racenativeamerican"
                                   value="nativeamerican" />
                            <span>{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_OPTION_AMERICAN') }}</span>
                        </label>
                        <label class="form-control w-full max-w-md ml-8 mt-1">
                            <div class="label">
                                <span class="label-text">
                                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_RACE_AFFILIATIONS') }}:
                                </span>
                            </div>
                            <input name="race[nativetribe]"
                                   id="racenativetribe"
                                   type="text"
                                   value=""
                                   class="input input-bordered w-full" />
                        </label>
                        @php
                        $raceOpts = [
                            'asian'    => ['raceasian',    'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_ASIAN'],
                            'black'    => ['raceblack',    'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_BLACK'],
                            'hawaiian' => ['racehawaiian', 'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_HAWAIIAN'],
                            'white'    => ['racewhite',    'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_WHITE'],
                            'hispanic' => ['racehispanic', 'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_HISPANIC'],
                            'refused'  => ['racerefused',  'COM_EVENTS_REGISTER_FIELD_RACE_OPTION_NULL'],
                        ];
                        @endphp
                        @foreach ($raceOpts as $name => $info)
                            <label class="flex items-center gap-2 cursor-pointer py-1">
                                <input type="checkbox"
                                       class="checkbox"
                                       name="race[{{ $name }}]"
                                       id="{{ $info[0] }}" />
                                <span>{{ \Hubzero\Facades\Lang::txt($info[1]) }}</span>
                            </label>
                        @endforeach
                    </fieldset>
                @endif
            </fieldset>
        @endif

        {{-- Arrival/Departure fieldset --}}
        @if ($params->get('show_arrival') || $params->get('show_departure'))
            <fieldset class="fieldset mb-6">
                <legend class="fieldset-legend">
                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ARRIVAL_OR_DEPARTURE') }}
                </legend>

                @if ($params->get('show_arrival'))
                    <fieldset class="fieldset mt-2">
                        <legend class="fieldset-legend">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ARRIVAL') }}
                        </legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">
                                        {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_ARRIVAL_DAY') }}
                                    </span>
                                </div>
                                <input type="text"
                                       name="arrival[day]"
                                       value="{{ $arrival['day'] ?? '' }}"
                                       class="input input-bordered w-full" />
                            </label>
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">
                                        {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_ARRIVAL_TIME') }}
                                    </span>
                                </div>
                                <input type="text"
                                       name="arrival[time]"
                                       value="{{ $arrival['time'] ?? '' }}"
                                       class="input input-bordered w-full" />
                            </label>
                        </div>
                    </fieldset>
                @endif

                @if ($params->get('show_departure'))
                    <fieldset class="fieldset mt-4">
                        <legend class="fieldset-legend">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DEPARTURE') }}
                        </legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">
                                        {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_DEPARTURE_DAY') }}
                                    </span>
                                </div>
                                <input type="text"
                                       name="departure[day]"
                                       value="{{ $departure['day'] ?? '' }}"
                                       class="input input-bordered w-full" />
                            </label>
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">
                                        {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_DEPARTURE_TIME') }}
                                    </span>
                                </div>
                                <input type="text"
                                       name="departure[time]"
                                       value="{{ $departure['time'] ?? '' }}"
                                       class="input input-bordered w-full" />
                            </label>
                        </div>
                    </fieldset>
                @endif
            </fieldset>
        @endif

        {{-- Disability/Dietary fieldset --}}
        @if ($params->get('show_disability') || $params->get('show_dietary'))
            <fieldset class="fieldset mb-6">
                <legend class="fieldset-legend">
                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DISABILITY') }}
                </legend>
                @if ($params->get('show_disability'))
                    <label class="flex items-center gap-2 cursor-pointer py-1">
                        <input type="checkbox"
                               class="checkbox"
                               name="disability"
                               value="yes" />
                        <span>{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_DISABILTIY') }}</span>
                    </label>
                @endif
                @if ($params->get('show_dietary'))
                    <label class="flex items-center gap-2 cursor-pointer py-1">
                        <input type="checkbox"
                               class="checkbox"
                               name="dietary[needs]"
                               value="yes" />
                        <span>{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_DIETARY') }}</span>
                    </label>
                    <label class="form-control w-full max-w-md ml-8 mt-1">
                        <div class="label">
                            <span class="label-text">
                                {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_DIETARY_DETAILS') }}
                            </span>
                        </div>
                        <input type="text"
                               name="dietary[specific]"
                               class="input input-bordered w-full" />
                    </label>
                @endif
            </fieldset>
        @endif

        {{-- Dinner fieldset --}}
        @if ($params->get('show_dinner'))
            <fieldset class="fieldset mb-6">
                <legend class="fieldset-legend">
                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_DINNER') }}
                </legend>
                <label class="flex items-center gap-2 cursor-pointer py-1">
                    <input type="checkbox"
                           class="checkbox"
                           name="dinner"
                           id="filed-dinner"
                           value="yes" />
                    <span>{{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_DINNER') }}</span>
                </label>
            </fieldset>
        @endif

        {{-- Abstract fieldset --}}
        @if ($params->get('show_abstract'))
            <fieldset class="fieldset mb-6">
                <legend class="fieldset-legend">
                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_ABSTRACT') }}
                </legend>
                <label class="form-control w-full">
                    @if ($params->get('abstract_text'))
                        <div class="label">
                            <span class="label-text">{{ stripslashes($params->get('abstract_text')) }}</span>
                        </div>
                    @endif
                    <textarea name="register[additional]"
                              rows="16"
                              class="textarea textarea-bordered w-full"></textarea>
                </label>
            </fieldset>
        @endif

        {{-- Comments fieldset --}}
        @if ($params->get('show_comments'))
            <fieldset class="fieldset mb-6">
                <legend class="fieldset-legend">
                    {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELDSET_COMMENTS') }}
                </legend>
                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text">
                            {{ \Hubzero\Facades\Lang::txt('COM_EVENTS_REGISTER_FIELD_COMMENTS') }}:
                        </span>
                    </div>
                    <textarea name="register[comments]"
                              rows="4"
                              class="textarea textarea-bordered w-full"></textarea>
                </label>
            </fieldset>
        @endif

        <div class="mt-6">
            <button type="submit" class="btn btn-primary">
                {{ \Hubzero\Facades\Lang::txt('EVENTS_SUBMIT') }}
            </button>
        </div>
    </form>
</x-page-container>
