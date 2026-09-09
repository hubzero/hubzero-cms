{{--
 * Member directory browse / search results page
 *
 * Variables:
 *   $title    - Page title
 *   $option   - Component option (e.g. 'com_members')
 *   $fields   - Collection of profile fields
 *   $filters  - Active filter values (search, q, sort, sort_Dir, tags)
 *   $rows     - Paginated member results
 *   $config   - Component configuration
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Event;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Plugin;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $__view->css()
           ->js()
           ->js('hubzero', 'system')
           ->js('browse');

    $base = 'index.php?option=' . $option . '&task=browse';

    // Separate fields into search (text-like) and filter (select/checkbox) groups
    $searches = [];
    $filterFields = [];
    foreach ($fields as $field) {
        if ($field->get('type') == 'hidden' || $field->get('type') == 'number') {
            continue;
        }
        if (in_array($field->get('type'), ['text', 'textarea', 'orcid', 'address'])) {
            $searches[] = $field;
        } else {
            $filterFields[] = $field;
        }
    }

    // Messaging setup
    $messaging = false;
    $usersgroups = [];
    if ($config->get('user_messaging') > 0 && !User::isGuest()) {
        switch ($config->get('user_messaging')) {
            case 1:
                $xgroups = User::groups();
                if (!empty($xgroups)) {
                    foreach ($xgroups as $group) {
                        if ($group->regconfirmed) {
                            $usersgroups[] = $group->cn;
                        }
                    }
                }
                break;
            case 2:
            case 0:
            default:
                break;
        }
        $messaging = true;
    }

    if (!Plugin::isEnabled('members', 'messages')) {
        $messaging = false;
    }

    // Build query string fragments for filter removal links
    $qs = [];
    if (!empty($filters['q'])) {
        foreach ($filters['q'] as $i => $q) {
            if (is_array($q['value'])) {
                $qs[$i] = [];
                foreach ($q['value'] as $key => $val) {
                    $qs[$i][] = '&q[' . $q['field'] . '][]=' . $val;
                }
            } elseif ($q['field'] == 'search') {
                $qs[$i] = '&' . $q['field'] . '=' . $q['value'];
            } else {
                $qs[$i] = '&q[' . $q['field'] . ']=' . $q['value'];
            }
        }
    }

    // Set up pagination
    $pageNav = $rows->pagination;
    if ($filters['search']) {
        $pageNav->setAdditionalUrlParam('search', $filters['search']);
    }
    if ($filters['tags']) {
        $pageNav->setAdditionalUrlParam('tags', $filters['tags']);
    }
    if ($filters['sort']) {
        $pageNav->setAdditionalUrlParam('sort', $filters['sort']);
    }
    if ($filters['sort_Dir']) {
        $pageNav->setAdditionalUrlParam('sort_Dir', $filters['sort_Dir']);
    }
    if (!empty($filters['q'])) {
        foreach ($filters['q'] as $i => $q) {
            if (is_array($q['value'])) {
                foreach ($q['value'] as $val) {
                    $pageNav->setAdditionalUrlParam('q[' . $q['field'] . '][]', $val);
                }
            } else {
                $pageNav->setAdditionalUrlParam('q[' . $q['field'] . ']', $q['value']);
            }
        }
    }
@endphp

<x-page-container :title="$title">
    <form action="{{ Route::url($base) }}" method="get">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Filter sidebar --}}
            <aside class="lg:w-1/4">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <fieldset>
                            <legend class="font-semibold text-lg mb-4">
                                {{ Lang::txt('COM_MEMBERS_BROWSE_FILTERS') }}
                            </legend>

                            {{-- Search input --}}
                            <div class="form-control mb-4">
                                <label class="label" for="filter-value-name">
                                    <span class="label-text">{{ Lang::txt('COM_MEMBERS_SEARCH') }}</span>
                                </label>
                                <input
                                    type="text"
                                    class="input input-bordered input-sm w-full"
                                    name="search"
                                    id="filter-value-name"
                                    value="{{ e($filters['search']) }}"
                                    placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER') }}" />
                            </div>

                            {{-- Field filters --}}
                            <fieldset class="mb-4">
                                <legend class="font-medium text-sm mb-2">
                                    {{ Lang::txt('COM_MEMBERS_BROWSE_FILTER') }}
                                </legend>

                                @foreach ($filterFields as $field)
                                    @php
                                        $value = [];
                                        foreach ($filters['q'] as $i => $q) {
                                            if ($q['field'] == $field->get('name')) {
                                                if (is_array($q['value'])) {
                                                    $value = array_merge($value, $q['value']);
                                                } else {
                                                    $value[] = $q['value'];
                                                }
                                            }
                                        }
                                    @endphp

                                    <div class="form-control mb-3">
                                        @if ($field->get('type') == 'radio' || $field->get('type') == 'checkboxes')
                                            <fieldset>
                                                <legend class="label-text font-medium mb-1">
                                                    {{ e($field->get('label')) }}
                                                </legend>
                                                @foreach ($field->options as $fieldOption)
                                                    @php
                                                        $filterId = e($field->get('name') . '-' . $fieldOption->get('value'));
                                                        $checked = in_array($fieldOption->get('value'), $value);
                                                    @endphp
                                                    <label
                                                        class="label cursor-pointer justify-start gap-2 py-1"
                                                        for="filter-value-{{ $filterId }}">
                                                        <input
                                                            class="checkbox checkbox-sm"
                                                            type="checkbox"
                                                            name="q[{{ e($field->get('name')) }}][]"
                                                            value="{{ e($fieldOption->get('value')) }}"
                                                            id="filter-value-{{ $filterId }}"
                                                            @checked($checked) />
                                                        <span class="label-text">{{ e($fieldOption->get('label')) }}</span>
                                                    </label>
                                                @endforeach
                                            </fieldset>
                                        @elseif ($field->get('type') == 'select')
                                            <label class="label" for="filter-value-{{ e($field->get('name')) }}">
                                                <span class="label-text">{{ e($field->get('label')) }}</span>
                                            </label>
                                            <select
                                                class="select select-bordered select-sm w-full"
                                                name="q[{{ e($field->get('name')) }}]"
                                                id="filter-value-{{ e($field->get('name')) }}">
                                                <option value="">- All -</option>
                                                @foreach ($field->options as $fieldOption)
                                                    <option
                                                        value="{{ e($fieldOption->get('value')) }}"
                                                        @selected(in_array($fieldOption->get('value'), $value))
                                                    >{{ e($fieldOption->get('label')) }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($field->get('type') == 'number')
                                            @php $fieldVal = e(implode('', $value)); @endphp
                                            <label class="label" for="filter-value-{{ e($field->get('name')) }}">
                                                <span class="label-text">{{ e($field->get('label')) }}</span>
                                            </label>
                                            @if ($field->get('max'))
                                                <input
                                                    type="range"
                                                    class="range range-sm"
                                                    name="q[{{ e($field->get('name')) }}]"
                                                    id="filter-value-{{ e($field->get('name')) }}"
                                                    min="{{ $field->get('min', 0) }}"
                                                    max="{{ $field->get('max') }}"
                                                    step="1"
                                                    value="{{ $fieldVal }}" />
                                            @else
                                                <input
                                                    type="number"
                                                    class="input input-bordered input-sm w-full"
                                                    name="q[{{ e($field->get('name')) }}]"
                                                    id="filter-value-{{ e($field->get('name')) }}"
                                                    @if($field->get('min')) min="{{ $field->get('min') }}" @endif
                                                    @if($field->get('max')) max="{{ $field->get('max') }}" @endif
                                                    value="{{ $fieldVal }}" />
                                            @endif
                                        @else
                                            <label class="label" for="filter-value-{{ e($field->get('name')) }}">
                                                <span class="label-text">{{ e($field->get('label')) }}</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="input input-bordered input-sm w-full"
                                                name="q[{{ e($field->get('name')) }}]"
                                                id="filter-value-{{ e($field->get('name')) }}"
                                                value="{{ e(implode('', $value)) }}" />
                                        @endif
                                    </div>
                                @endforeach
                            </fieldset>

                            {{-- Sort options --}}
                            <fieldset class="mb-4">
                                <legend class="font-medium text-sm mb-2">
                                    {{ Lang::txt('COM_MEMBERS_BROWSE_SORT') }}
                                </legend>

                                <div class="form-control mb-3">
                                    <label class="label" for="filter-value-sort">
                                        <span class="label-text">{{ Lang::txt('COM_MEMBERS_BROWSE_SORT_BY') }}</span>
                                    </label>
                                    <select class="select select-bordered select-sm w-full" name="sort" id="filter-value-sort">
                                        <option value="name">Name</option>
                                        @foreach ($fields as $field)
                                            <option
                                                value="{{ e($field->get('name')) }}"
                                                @selected($field->get('name') == $filters['sort'])
                                            >{{ e($field->get('label')) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-control mb-3">
                                    <label class="label" for="filter-value-sort-dir">
                                        <span class="label-text">{{ Lang::txt('COM_MEMBERS_BROWSE_SORT_DIR') }}</span>
                                    </label>
                                    <select class="select select-bordered select-sm w-full" name="sort_Dir" id="filter-value-sort-dir">
                                        <option value="asc" @selected($filters['sort_Dir'] == 'asc')>
                                            {{ Lang::txt('COM_MEMBERS_BROWSE_SORT_DIR_ASC') }}
                                        </option>
                                        <option value="desc" @selected($filters['sort_Dir'] == 'desc')>
                                            {{ Lang::txt('COM_MEMBERS_BROWSE_SORT_DIR_DESC') }}
                                        </option>
                                    </select>
                                </div>
                            </fieldset>

                            <button class="btn btn-primary btn-sm w-full" type="submit">
                                {{ Lang::txt('COM_MEMBERS_APPLY') }}
                            </button>
                        </fieldset>
                    </div>
                </div>
            </aside>

            {{-- Results --}}
            <div class="flex-1">

                {{-- Applied filter chips --}}
                @if (!empty($filters['q']))
                    <div class="mb-4 flex flex-wrap gap-2 items-center">
                        <span class="text-sm font-medium">
                            {{ Lang::txt('COM_MEMBERS_BROWSE_FILTER_APPLIED') }}:
                        </span>
                        @foreach ($filters['q'] as $i => $q)
                            @if (is_array($q['human_value']))
                                @foreach ($q['human_value'] as $key => $val)
                                    @php
                                        $route = $base;
                                        foreach ($qs as $k => $s) {
                                            if ($k == $i) {
                                                if (is_array($s)) {
                                                    foreach ($s as $kkey => $ss) {
                                                        if ($kkey == $key) {
                                                            continue;
                                                        }
                                                        $route .= (is_array($ss) ? implode('', $ss) : $ss);
                                                    }
                                                }
                                                continue;
                                            }
                                            $route .= (is_array($s) ? implode('', $s) : $s);
                                        }
                                    @endphp
                                    <a href="{{ Route::url($route) }}"
                                       class="badge badge-lg gap-2"
                                       title="{{ Lang::txt('COM_MEMBERS_BROWSE_FILTER_REMOVE') }}">
                                        <em>{{ $q['human_field'] }}</em>: {{ e($val) }}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             class="inline-block w-4 h-4 stroke-current">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                @endforeach
                            @else
                                @php
                                    $route = $base;
                                    foreach ($qs as $k => $s) {
                                        if ($k == $i) {
                                            continue;
                                        }
                                        $route .= (is_array($s) ? implode('', $s) : $s);
                                    }
                                @endphp
                                <a href="{{ Route::url($route) }}"
                                   class="badge badge-lg gap-2"
                                   title="{{ Lang::txt('COM_MEMBERS_BROWSE_FILTER_REMOVE') }}">
                                    <em>{{ $q['human_field'] }}</em>: {{ e($q['human_value']) }}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         class="inline-block w-4 h-4 stroke-current">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Member results --}}
                <div id="listOfMembers">
                    @if ($rows->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($rows as $row)
                                @php
                                    $cls = '';
                                    if ($row->get('access') != 1) {
                                        $cls = 'private';
                                    }

                                    $id = ($row->get('id') < 0) ? 'n' . -$row->get('id') : $row->get('id');

                                    if ($row->get('id') == User::get('id')) {
                                        $cls .= ($cls) ? ' me' : 'me';
                                    }

                                    // Build display name
                                    if (!$row->get('surname')) {
                                        $bits = explode(' ', $row->get('name'));
                                        $row->set('surname', array_pop($bits));
                                        if (count($bits) >= 1) {
                                            $row->set('givenName', array_shift($bits));
                                        }
                                        if (count($bits) >= 1) {
                                            $row->set('middleName', implode(' ', $bits));
                                        }
                                    }

                                    $name = stripslashes($row->get('surname', ''));
                                    if ($row->get('givenName')) {
                                        $name .= ($row->get('surname')) ? ', ' : '';
                                        $name .= stripslashes($row->get('givenName'));
                                        $name .= ($row->get('middleName'))
                                            ? ' ' . stripslashes($row->get('middleName')) : '';
                                    }
                                    if (!trim($name)) {
                                        $name = Lang::txt('COM_MEMBERS_UNKNOWN') . ' (' . $row->get('username') . ')';
                                    }

                                    // Per-row messaging check
                                    $messageuser = false;
                                    if (
                                        $messaging
                                        && $row->get('id') > 0
                                        && $row->get('uidNumber') != User::get('id')
                                        && substr($row->get('email'), -8) != '@invalid'
                                    ) {
                                        switch ($config->get('user_messaging')) {
                                            case 1:
                                                $pgroups = \Hubzero\User\Helper::getGroups($row->get('id'), 'all', 1);
                                                $profilesgroups = [];
                                                if (!empty($pgroups)) {
                                                    foreach ($pgroups as $group) {
                                                        if ($group->regconfirmed) {
                                                            $profilesgroups[] = $group->cn;
                                                        }
                                                    }
                                                }
                                                if (count(array_intersect($usersgroups, $profilesgroups)) > 0) {
                                                    $messageuser = true;
                                                }
                                                break;
                                            case 2:
                                                $messageuser = true;
                                                break;
                                            case 0:
                                            default:
                                                $messageuser = false;
                                                break;
                                        }
                                    }

                                    $results = Event::trigger('members.onMemberProfile', [$row]);
                                    $extras = implode("\n", $results);
                                @endphp

                                @include('profiles::_member-card', [
                                    'row'         => $row,
                                    'option'      => $option,
                                    'fields'      => $fields,
                                    'messageuser' => $messageuser,
                                    'extras'      => $extras,
                                    'name'        => $name,
                                    'id'          => $id,
                                    'cls'         => $cls,
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="alert">
                            <p>{{ Lang::txt('COM_MEMBERS_BROWSE_NO_MEMBERS_FOUND') }}</p>
                        </div>
                    @endif

                    {{-- Pagination --}}
                    {!! $pageNav !!}
                </div>
            </div>

        </div>
    </form>
</x-page-container>
