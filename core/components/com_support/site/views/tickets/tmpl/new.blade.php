{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Config;
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Event;
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    // Are we remotely loading ticket form
    $tmpl = Request::getString('tmpl', '') ? '&tmpl=component' : '';
    $no_html = Request::getInt('no_html');

    // Are we trying to assign a group
    $group = Request::getString('group', '');

    // Populate the row for guests that have issues with the page
    if (User::isGuest()) {
        $row->submitter->set('username', Request::getString('reporter[login]', null, 'post'));
        $row->set('report', Request::getString('problem[long]', null, 'post'));
        $row->set('name', Request::getString('reporter[name]', null, 'post'));
        $row->set('email', Request::getString('reporter[email]', null, 'post'));
    }

    $formAction = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller . '&task=new' . $tmpl
    );

    $tmp = '-' . time();
    $jbase = rtrim(Request::base(true), '/');
    $uploadAction = $jbase . '/index.php?option=com_support&amp;no_html=1'
        . '&amp;controller=media&amp;task=upload&amp;ticket=' . $tmp;
    $listAction = $jbase . '/index.php?option=com_support&amp;no_html=1'
        . '&amp;controller=media&amp;task=list&amp;ticket=' . $tmp;
@endphp

<x-page-container :title="$title">
    <div class="alert alert-info" role="alert">
        {!! Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES') !!}
    </div>

    @if ($__view->getError())
        <div class="alert alert-error" role="alert">
            {!! implode('<br />', $__view->getErrors()) !!}
        </div>
    @endif

    <form
        action="{{ $formAction }}"
        id="hubForm"
        method="post"
        enctype="multipart/form-data"
        @if ($no_html) class="full" @endif
    >
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="save" />
        <input type="hidden" name="verified" value="{{ $__view->escape($row->get('verified')) }}" />
        <input type="hidden" name="problem[referer]" value="{{ $__view->escape($row->get('referrer')) }}" />
        <input type="hidden" name="problem[tool]" value="{{ $__view->escape($row->get('tool')) }}" />
        <input type="hidden" name="problem[short]" value="{{ $__view->escape($row->get('short')) }}" />
        <input type="hidden" name="no_html" value="{{ $no_html }}" />

        @if ($row->get('verified'))
            <input type="hidden" name="botcheck" value="" />
        @endif

        {{-- Guest user info --}}
        @if (User::isGuest())
            @if (!$tmpl && !$no_html)
                <div class="card bg-base-100 shadow-sm mb-6">
                    <div class="card-body">
                        <p>{!! Lang::txt('COM_SUPPORT_TROUBLE_OTHER_OPTIONS') !!}</p>
                    </div>
                </div>
            @endif

            <fieldset class="fieldset">
                <legend class="fieldset-legend">
                    {{ Lang::txt('COM_SUPPORT_TROUBLE_USER_INFORMATION') }}
                </legend>

                @php
                    $loginVal = $__view->escape(
                        $row->get('login', $row->submitter->get('username'))
                    );
                    $nameVal = $__view->escape(
                        $row->get('name', $row->submitter->get('name'))
                    );
                    $emailVal = $__view->escape(
                        $row->get('email', $row->submitter->get('email'))
                    );
                @endphp

                <x-form-field
                    name="reporter[login]"
                    input-id="reporter_login"
                    :label="Lang::txt('COM_SUPPORT_USERNAME')"
                >
                    <input
                        type="text"
                        name="reporter[login]"
                        id="reporter_login"
                        class="input input-bordered w-full"
                        value="{{ $loginVal }}"
                    />
                </x-form-field>

                <x-form-field
                    name="reporter[name]"
                    input-id="reporter_name"
                    :label="Lang::txt('COM_SUPPORT_NAME')"
                    :required="true"
                    :error="($__view->getError() && !$row->get('name'))
                        ? Lang::txt('COM_SUPPORT_ERROR_MISSING_NAME') : ''"
                >
                    <input
                        type="text"
                        name="reporter[name]"
                        id="reporter_name"
                        class="input input-bordered w-full"
                        value="{{ $nameVal }}"
                    />
                </x-form-field>

                <x-form-field
                    name="reporter[email]"
                    input-id="reporter_email"
                    :label="Lang::txt('COM_SUPPORT_EMAIL')"
                    :required="true"
                    :error="($__view->getError() && !$row->get('email'))
                        ? Lang::txt('COM_SUPPORT_ERROR_MISSING_EMAIL') : ''"
                >
                    <input
                        type="email"
                        name="reporter[email]"
                        id="reporter_email"
                        class="input input-bordered w-full"
                        value="{{ $emailVal }}"
                    />
                </x-form-field>

                @php
                    $orgVal = $__view->escape(
                        $row->get('organization', $row->submitter->get('organization'))
                    );
                @endphp
                <input type="hidden" name="reporter[org]" value="{{ $orgVal }}" id="reporter_org" />
            </fieldset>
        @else
            @php
                $loginVal = $__view->escape($row->get('login', $row->submitter->get('username')));
                $nameVal  = $__view->escape($row->get('name', $row->submitter->get('name')));
                $emailVal = $__view->escape($row->get('email', $row->submitter->get('email')));
            @endphp
            <input type="hidden" name="reporter[login]" value="{{ $loginVal }}" id="reporter_login" />
            <input type="hidden" name="reporter[name]" value="{{ $nameVal }}" id="reporter_name" />
            <input type="hidden" name="reporter[email]" value="{{ $emailVal }}" id="reporter_email" />
        @endif

        {{-- Problem description --}}
        <fieldset class="fieldset">
            <legend class="fieldset-legend">
                {{ Lang::txt('COM_SUPPORT_TROUBLE_YOUR_PROBLEM') }}
            </legend>

            <x-form-field
                name="problem[long]"
                input-id="problem_long"
                :label="Lang::txt('COM_SUPPORT_TROUBLE_DESCRIPTION')"
                :required="true"
                :error="($__view->getError() && !$row->get('report'))
                    ? Lang::txt('COM_SUPPORT_ERROR_MISSING_DESCRIPTION') : ''"
            >
                <textarea
                    name="problem[long]"
                    cols="40"
                    rows="10"
                    class="textarea textarea-bordered w-full"
                    id="problem_long"
                >{{ $row->get('report') }}</textarea>
            </x-form-field>

            {{-- File attachments --}}
            <fieldset class="fieldset">
                <legend class="fieldset-legend">
                    {{ Lang::txt('COM_SUPPORT_COMMENT_LEGEND_ATTACHMENTS') }}
                </legend>

                <div
                    id="ajax-uploader"
                    data-instructions="{{ Lang::txt('COM_SUPPORT_CLICK_OR_DROP_FILE') }}"
                    data-action="{{ $uploadAction }}"
                    data-list="{{ $listAction }}"
                >
                    <noscript>
                        <x-form-field
                            name="upload[]"
                            input-id="upload"
                            :label="Lang::txt('COM_SUPPORT_COMMENT_FILE') . ':'"
                        >
                            <input
                                type="file"
                                name="upload[]"
                                id="upload"
                                class="file-input file-input-bordered w-full"
                                multiple="multiple"
                            />
                        </x-form-field>

                        <x-form-field
                            name="description"
                            input-id="field-description"
                            :label="Lang::txt('COM_SUPPORT_COMMENT_FILE_DESCRIPTION') . ':'"
                        >
                            <input
                                type="text"
                                name="description"
                                id="field-description"
                                class="input input-bordered w-full"
                                value=""
                            />
                        </x-form-field>
                    </noscript>
                </div>
                <div class="file-list" id="ajax-uploader-list"></div>
                <input type="hidden" name="tmp_dir" id="ticket-tmp_dir" value="{{ $tmp }}" />

                <span class="text-sm opacity-60">
                    (.{{ str_replace(',', ', .', $file_types) }})
                </span>
            </fieldset>
        </fieldset>

        {{-- Admin details --}}
        @if ($row->get('verified') && $acl->check('update', 'tickets') > 0)
            <fieldset class="fieldset">
                <legend class="fieldset-legend">
                    {{ Lang::txt('COM_SUPPORT_DETAILS') }}
                </legend>

                {{-- Tags --}}
                <x-form-field
                    name="tags"
                    input-id="tags"
                    :label="Lang::txt('COM_SUPPORT_COMMENT_TAGS') . ':'"
                >
                    @php
                        $tf = Event::trigger(
                            'hubzero.onGetMultiEntry',
                            [['tags', 'tags', 'actags', '', '']]
                        );
                    @endphp
                    @if (count($tf) > 0)
                        {!! $tf[0] !!}
                    @else
                        <input
                            type="text"
                            name="tags"
                            id="tags"
                            class="input input-bordered w-full"
                            value=""
                            size="35"
                        />
                    @endif
                </x-form-field>

                {{-- Group and Owner --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-field
                        name="problem[group_id]"
                        input-id="acgroup"
                        :label="Lang::txt('COM_SUPPORT_COMMENT_GROUP') . ':'"
                    >
                        @php
                            $gc = Event::trigger(
                                'hubzero.onGetSingleEntryWithSelect',
                                [['groups', 'problem[group_id]', 'acgroup', '',
                                  $__view->escape($group), '', 'ticketowner']]
                            );
                        @endphp
                        @if (count($gc) > 0)
                            {!! $gc[0] !!}
                        @else
                            <input
                                type="text"
                                name="group_id"
                                id="acgroup"
                                class="input input-bordered w-full"
                                value=""
                                autocomplete="off"
                            />
                        @endif
                    </x-form-field>

                    <x-form-field
                        name="problem[owner]"
                        input-id="problemowner"
                        :label="Lang::txt('COM_SUPPORT_COMMENT_OWNER') . ':'"
                    >
                        {!! $lists['owner'] !!}
                    </x-form-field>
                </div>

                {{-- Severity and Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-field
                        name="problem[severity]"
                        input-id="ticket-field-severity"
                        :label="Lang::txt('COM_SUPPORT_COMMENT_SEVERITY')"
                    >
                        <select
                            name="problem[severity]"
                            id="ticket-field-severity"
                            class="select select-bordered w-full"
                        >
                            @foreach ($lists['severities'] as $severity)
                                <option
                                    value="{{ $severity }}"
                                    @selected($severity == 'normal')
                                >
                                    {{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)) }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field
                        name="problem[status]"
                        input-id="ticket-field-status"
                        :label="Lang::txt('COM_SUPPORT_COMMENT_STATUS') . ':'"
                    >
                        <select
                            name="problem[status]"
                            id="ticket-field-status"
                            class="select select-bordered w-full"
                        >
                            <optgroup label="{{ Lang::txt('COM_SUPPORT_COMMENT_OPT_OPEN') }}">
                                <option value="0" selected="selected">
                                    {{ Lang::txt('COM_SUPPORT_COMMENT_OPT_NEW') }}
                                </option>
                                @foreach (\Components\Support\Models\Status::allOpen()->rows() as $status)
                                    <option value="{{ $status->get('id') }}">
                                        {{ $__view->escape($status->get('title')) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="{{ Lang::txt('COM_SUPPORT_CLOSED') }}">
                                <option value="0">
                                    {{ Lang::txt('COM_SUPPORT_COMMENT_OPT_CLOSED') }}
                                </option>
                                @foreach (\Components\Support\Models\Status::allClosed()->rows() as $status)
                                    <option value="{{ $status->get('id') }}">
                                        {{ $__view->escape($status->get('title')) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </x-form-field>
                </div>

                {{-- Target date --}}
                @php
                    $tzOffset = timezone_offset_get(
                        new DateTimeZone(Config::get('offset')),
                        Date::getRoot()
                    ) / 60;
                @endphp
                <x-form-field
                    name="problem[target_date]"
                    input-id="field-target_date"
                    :label="Lang::txt('COM_SUPPORT_COMMENT_TARGET_DATE') . ':'"
                >
                    <input
                        type="text"
                        name="problem[target_date]"
                        class="input input-bordered w-full datetime-field"
                        id="field-target_date"
                        data-timezone="{{ $tzOffset }}"
                        placeholder="YYYY-MM-DD hh:mm:ss"
                        value=""
                    />
                </x-form-field>

                {{-- Category --}}
                @if (isset($lists['categories']) && $lists['categories'])
                    <x-form-field
                        name="problem[category]"
                        input-id="ticket-field-category"
                        :label="Lang::txt('COM_SUPPORT_COMMENT_CATEGORY')"
                    >
                        <select
                            name="problem[category]"
                            id="ticket-field-category"
                            class="select select-bordered w-full"
                        >
                            <option value="">{{ Lang::txt('COM_SUPPORT_NONE') }}</option>
                            @foreach ($lists['categories'] as $category)
                                <option value="{{ $__view->escape($category->alias) }}">
                                    {{ $__view->escape(stripslashes($category->title)) }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>
                @endif

                {{-- CC email --}}
                <x-form-field
                    name="cc"
                    input-id="acmembers"
                    :label="Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC') . ':'"
                >
                    @php
                        $mc = Event::trigger(
                            'hubzero.onGetMultiEntry',
                            [['members', 'cc', 'acmembers', '', '']]
                        );
                    @endphp
                    @if (count($mc) > 0)
                        <span class="text-sm opacity-60">
                            {{ Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE') }}
                        </span>
                        {!! $mc[0] !!}
                    @else
                        <span class="text-sm opacity-60">
                            {{ Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS') }}
                        </span>
                        <input
                            type="text"
                            name="cc"
                            id="acmembers"
                            class="input input-bordered w-full"
                            value=""
                            size="35"
                        />
                    @endif
                </x-form-field>
            </fieldset>
        @else
            @if ($group)
                <input type="hidden" name="group_id" value="{{ $__view->escape($group) }}" />
            @endif
        @endif

        {{-- CAPTCHA section --}}
        @if (!$row->get('verified'))
            <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body">
                    <p>{!! Lang::txt('COM_SUPPORT_MATH_EXPLANATION') !!}</p>
                </div>
            </div>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">
                    {{ Lang::txt('COM_SUPPORT_HUMAN_CHECK') }}
                </legend>

                <label id="fbBotcheck-label" for="fbBotcheck">
                    {{ Lang::txt('COM_SUPPORT_LEAVE_FIELD_BLANK') }}
                    <span class="text-error">*</span>
                    <input type="text" name="botcheck" id="fbBotcheck" value="" />
                </label>

                @if (count($captchas) > 0)
                    @foreach ($captchas as $captcha)
                        {!! $captcha !!}
                    @endforeach
                @endif

                @if ($__view->getError() == 3)
                    <p class="text-error">
                        {{ Lang::txt('COM_SUPPORT_ERROR_BAD_CAPTCHA_ANSWER') }}
                    </p>
                @endif
            </fieldset>
        @endif

        {!! Html::input('token') !!}

        <p class="mt-6">
            <button class="btn btn-success" type="submit" name="submit">
                {{ Lang::txt('COM_SUPPORT_SUBMIT') }}
            </button>
        </p>
    </form>
</x-page-container>
