{{--
  Event creation/editing form
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$browseUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option);
$saveUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&task=save');
$cancelUrl = $row->id
    ? \Hubzero\Facades\Route::url('index.php?option=' . $option . '&task=details&id=' . $row->id)
    : $browseUrl;

$titleVal = e(html_entity_decode(stripslashes($row->title ?: '')));
$adresseVal = e(stripslashes($row->adresse_info ?: ''));
$extraVal = e(stripslashes($row->extra_info ?: ''));
$emailVal = e(stripslashes($row->email ?: ''));
$restrictedVal = e(stripslashes($row->restricted ?: ''));
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-ghost btn-sm" href="{{ $browseUrl }}">
            {{ \Hubzero\Facades\Lang::txt('EVENTS_BROWSE') }}
        </a>
    @endslot

    <form action="{{ $saveUrl }}"
        method="post"
        id="hubForm"
        class="card bg-base-100 border border-base-300">
        <div class="card-body">
            @if ($__view->getError())
                <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
            @endif

            <fieldset>
                <legend class="text-lg font-semibold mb-4">
                    @if ($row->id)
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_UPDATE_EVENT') }}
                    @else
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_NEW_EVENT') }}
                    @endif
                </legend>

                {{-- Category --}}
                <div class="form-control mb-4">
                    <label class="label" for="catid">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY') }}:
                        <span class="text-error">{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REQUIRED') }}</span>
                    </label>
                    {!! \Components\Events\Helpers\Html::buildCategorySelect($row->catid, '', $gid, $option) !!}
                </div>

                {{-- Title --}}
                <div class="form-control mb-4">
                    <label class="label" for="title">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_TITLE') }}:
                        <span class="text-error">{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REQUIRED') }}</span>
                    </label>
                    <input type="text"
                        name="title"
                        id="title"
                        class="input input-bordered w-full"
                        maxlength="250"
                        value="{{ $titleVal }}">
                </div>

                {{-- Description (WYSIWYG editor) --}}
                <div class="form-control mb-4">
                    <label class="label" for="econtent">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION') }}:
                    </label>
                    {!! \Hubzero\Facades\App::get('editor')->display(
                        'econtent',
                        $row->content,
                        '',
                        '',
                        10,
                        15,
                        false,
                        'econtent',
                        null,
                        null,
                        ['class' => 'minimal no-footer']
                    ) !!}
                </div>

                {{-- Address --}}
                <div class="form-control mb-4">
                    <label class="label" for="adresse_info">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_ADRESSE') }}
                    </label>
                    <input type="text"
                        name="adresse_info"
                        id="adresse_info"
                        class="input input-bordered w-full"
                        maxlength="120"
                        value="{{ $adresseVal }}">
                </div>

                {{-- Extra info URL --}}
                <div class="form-control mb-4">
                    <label class="label" for="extra_info">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_EXTRA') }}
                    </label>
                    <input type="text"
                        name="extra_info"
                        id="extra_info"
                        class="input input-bordered w-full"
                        maxlength="240"
                        value="{{ $extraVal }}">
                </div>

                {{-- Custom fields --}}
                @if ($fields)
                    @foreach ($fields as $field)
                        <div class="form-control mb-4">
                            <label class="label" for="field-{{ $field[0] }}">
                                {{ $field[1] }}:
                                @if ($field[3])
                                    <span class="text-error">{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_REQUIRED') }}</span>
                                @endif
                            </label>
                            @if ($field[2] == 'checkbox')
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="checkbox"
                                        name="fields[{{ $field[0] }}]"
                                        id="field-{{ $field[0] }}"
                                        class="checkbox checkbox-primary"
                                        value="1"
                                        @checked(stripslashes(end($field)) == 1)>
                                    <span>{{ $field[1] }}</span>
                                </label>
                            @else
                                <input type="text"
                                    name="fields[{{ $field[0] }}]"
                                    id="field-{{ $field[0] }}"
                                    class="input input-bordered w-full"
                                    maxlength="255"
                                    value="{{ e(stripslashes(end($field))) }}">
                            @endif
                        </div>
                    @endforeach
                @endif

                {{-- Tags --}}
                <div class="form-control mb-4">
                    <label class="label" for="actags">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_E_TAGS') }}
                    </label>
                    @php
                    $tf = \Hubzero\Facades\Event::trigger(
                        'hubzero.onGetMultiEntry',
                        [['tags', 'tags', 'actags', '', $lists['tags']]]
                    );
                    @endphp
                    @if (count($tf) > 0)
                        {!! $tf[0] !!}
                    @else
                        <input type="text"
                            name="tags"
                            id="actags"
                            class="input input-bordered w-full"
                            value="{{ e($lists['tags']) }}">
                    @endif
                </div>

                {{-- Date & Time --}}
                <fieldset class="border border-base-300 rounded-lg p-4 mt-4">
                    <legend class="text-base font-semibold px-2">
                        {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_TIME') }}
                    </legend>

                    {{-- Start date & time --}}
                    <div class="form-control mb-4">
                        <label class="label" for="publish_up">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_STARTDATE') }}
                            &amp;
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_STARTTIME') }}
                        </label>
                        <div class="flex items-center gap-2 flex-wrap">
                            <input type="text"
                                name="publish_up"
                                id="publish_up"
                                class="input input-bordered w-40"
                                maxlength="10"
                                value="{{ $times['start_publish'] }}">
                            <input type="text"
                                name="start_time"
                                id="start_time"
                                class="input input-bordered w-24"
                                maxlength="6"
                                value="{{ $times['start_time'] }}">
                            @if ($config->getCfg('calUseStdTime') == 'YES')
                                <label class="label cursor-pointer gap-1">
                                    <input type="radio"
                                        name="start_pm"
                                        id="start_pm0"
                                        class="radio radio-sm"
                                        value="0"
                                        @checked(!$times['start_pm'])>
                                    <span class="text-sm">AM</span>
                                </label>
                                <label class="label cursor-pointer gap-1">
                                    <input type="radio"
                                        name="start_pm"
                                        id="start_pm1"
                                        class="radio radio-sm"
                                        value="1"
                                        @checked($times['start_pm'])>
                                    <span class="text-sm">PM</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    {{-- End date & time --}}
                    <div class="form-control mb-4">
                        <label class="label" for="publish_down">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_ENDDATE') }}
                            &amp;
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_ENDTIME') }}
                        </label>
                        <div class="flex items-center gap-2 flex-wrap">
                            <input type="text"
                                name="publish_down"
                                id="publish_down"
                                class="input input-bordered w-40"
                                maxlength="10"
                                value="{{ $times['stop_publish'] }}">
                            <input type="text"
                                name="end_time"
                                id="end_time"
                                class="input input-bordered w-24"
                                maxlength="6"
                                value="{{ $times['end_time'] }}">
                            @if ($config->getCfg('calUseStdTime') == 'YES')
                                <label class="label cursor-pointer gap-1">
                                    <input type="radio"
                                        name="end_pm"
                                        id="end_pm0"
                                        class="radio radio-sm"
                                        value="0"
                                        @checked(!$times['end_pm'])>
                                    <span class="text-sm">AM</span>
                                </label>
                                <label class="label cursor-pointer gap-1">
                                    <input type="radio"
                                        name="end_pm"
                                        id="end_pm1"
                                        class="radio radio-sm"
                                        value="1"
                                        @checked($times['end_pm'])>
                                    <span class="text-sm">PM</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    {{-- Timezone --}}
                    <div class="form-control">
                        <label class="label" for="time_zone">
                            {{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_TIME_ZONE') }}
                        </label>
                        {!! \Components\Events\Helpers\Html::buildTimeZoneSelect($times['time_zone'], '') !!}
                    </div>
                </fieldset>
            </fieldset>

            <div class="flex gap-2 mt-6">
                <button type="submit" class="btn btn-primary">
                    {{ \Hubzero\Facades\Lang::txt('EVENTS_SAVE') }}
                </button>
                <a href="{{ $cancelUrl }}" class="btn btn-ghost">
                    {{ \Hubzero\Facades\Lang::txt('JCANCEL') }}
                </a>
            </div>

            {{-- Hidden fields --}}
            {!! \Hubzero\Facades\Html::input('token') !!}
            {!! \Hubzero\Facades\Html::input('honeypot') !!}
            <input type="hidden" name="state" value="{{ e($row->state) }}">
            <input type="hidden" name="email" value="{{ $emailVal }}">
            <input type="hidden" name="restricted" value="{{ $restrictedVal }}">
            <input type="hidden" name="created_by" value="{{ $row->created_by }}">
            <input type="hidden" name="option" value="{{ $option }}">
            <input type="hidden" name="task" value="save">
            <input type="hidden" name="id" id="event-id" value="{{ $row->id }}">
        </div>
    </form>
</x-page-container>
