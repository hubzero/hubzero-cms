{{--
 * Abuse report form — allows users to flag content as abusive.
 *
 * Variables from controller (displayTask):
 *   $title      — Page title
 *   $option     — Component option (com_support)
 *   $controller — Controller name (abuse)
 *   $report     — Reported item object (or null if not found)
 *   $cat        — Category of reported item
 *   $refid      — Reference ID of reported item
 *   $parentid   — Parent ID
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
    use Hubzero\Utility\Sanitize;

    $no_html = Request::getInt('no_html', 0);

    $formAction = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=reportabuse'
    );
    $formId = 'hubForm' . ($no_html ? '-ajax' : '');

    $offensiveLabel = Lang::txt('COM_SUPPORT_REPORT_ABUSE_OFFENSIVE');
    $stupidLabel    = Lang::txt('COM_SUPPORT_REPORT_ABUSE_STUPID');
    $spamLabel      = Lang::txt('COM_SUPPORT_REPORT_ABUSE_SPAM');
    $otherLabel     = Lang::txt('COM_SUPPORT_REPORT_ABUSE_OTHER');
@endphp

@if ($no_html)
    {{-- AJAX fragment mode: form only, no page wrapper --}}
    @if ($report)
        @if ($__view->getError())
            <div class="alert alert-error">{{ $__view->getError() }}</div>
        @endif

        <form action="{{ $formAction }}" method="post" id="{{ $formId }}">
            <fieldset>
                <legend>{{ Lang::txt('COM_SUPPORT_REPORT_ABUSE') }}</legend>

                <fieldset class="mb-4">
                    <legend class="text-sm font-semibold mb-2">{{ Lang::txt('COM_SUPPORT_REPORT_ABUSE_REASON') }}</legend>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" class="radio radio-sm" name="subject"
                                   value="{{ $offensiveLabel }}" checked />
                            <span>{{ $offensiveLabel }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" class="radio radio-sm" name="subject"
                                   value="{{ $stupidLabel }}" />
                            <span>{{ $stupidLabel }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" class="radio radio-sm" name="subject"
                                   value="{{ $spamLabel }}" />
                            <span>{{ $spamLabel }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" class="radio radio-sm" name="subject"
                                   value="{{ $otherLabel }}" />
                            <span>{{ $otherLabel }}</span>
                        </label>
                    </div>
                </fieldset>

                <input type="hidden" name="option" value="{{ $__view->escape($option) }}" />
                <input type="hidden" name="controller" value="{{ $__view->escape($controller) }}" />
                <input type="hidden" name="task" value="save" />
                <input type="hidden" name="category" value="{{ $__view->escape($cat) }}" />
                <input type="hidden" name="referenceid" value="{{ $__view->escape($refid) }}" />
                <input type="hidden" name="link" value="{{ $__view->escape($report->href) }}" />
                <input type="hidden" name="no_html" value="{{ $no_html }}" />
                {!! Html::input('token') !!}

                <x-form-field name="field-report"
                              :label="Lang::txt('COM_SUPPORT_REPORT_ABUSE_DESCRIPTION')">
                    <textarea name="report" id="field-report"
                              class="textarea textarea-bordered w-full"
                              rows="10" cols="50"></textarea>
                </x-form-field>
            </fieldset>

            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-error">
                    {{ Lang::txt('COM_SUPPORT_SUBMIT') }}
                </button>
                <a class="btn btn-ghost" href="{{ $report->href }}">
                    {{ Lang::txt('JCANCEL') }}
                </a>
            </div>
        </form>
    @else
        @if ($__view->getError())
            <div class="alert alert-error">{{ $__view->getError() }}</div>
        @else
            <div class="alert alert-warning">{{ Lang::txt('COM_SUPPORT_ERROR_NO_INFO_ON_REPORTED_ITEM') }}</div>
        @endif
    @endif
@else
    {{-- Full page mode --}}
    <x-page-container :title="$title">
        @if ($report)
            @if ($__view->getError())
                <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main form --}}
                <div class="lg:col-span-2">
                    <form action="{{ $formAction }}" method="post" id="{{ $formId }}">
                        <fieldset>
                            <legend class="text-lg font-semibold mb-4">{{ Lang::txt('COM_SUPPORT_REPORT_ABUSE') }}</legend>

                            {{-- Report item preview --}}
                            <div class="card bg-base-200 mb-4">
                                <div class="card-body">
                                    @php
                                        $name = Lang::txt('JANONYMOUS');
                                        if ($report->anon == 0) {
                                            $user = User::getInstance($report->author);
                                            $name = Lang::txt('COM_SUPPORT_UNKNOWN');
                                            if (is_object($user)) {
                                                $name = $user->get('name');
                                            }
                                        }
                                        $citeName = ($report->anon != 0)
                                            ? Lang::txt('COM_SUPPORT_ANONYMOUS')
                                            : $name;
                                    @endphp
                                    <p>
                                        @if ($report->href)<a href="{{ $report->href }}">@endif
                                            {{ ucfirst($cat) }} by {{ ($report->anon != 0) ? Lang::txt('JANONYMOUS') : $name }}
                                        @if ($report->href)</a>@endif
                                    </p>
                                    @if ($report->subject)
                                        <p class="font-bold">{{ stripslashes($report->subject) }}</p>
                                    @endif
                                    <blockquote class="border-l-4 border-base-300 pl-4 italic" cite="{{ $citeName }}">
                                        <p>{{ Sanitize::html($report->text) }}</p>
                                    </blockquote>
                                </div>
                            </div>

                            {{-- Abuse reason radios --}}
                            <fieldset class="mb-4">
                                <legend class="text-sm font-semibold mb-2">{{ Lang::txt('COM_SUPPORT_REPORT_ABUSE_REASON') }}</legend>
                                <div class="flex flex-col gap-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" class="radio radio-sm" name="subject"
                                               value="{{ $offensiveLabel }}" checked />
                                        <span>{{ $offensiveLabel }}</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" class="radio radio-sm" name="subject"
                                               value="{{ $stupidLabel }}" />
                                        <span>{{ $stupidLabel }}</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" class="radio radio-sm" name="subject"
                                               value="{{ $spamLabel }}" />
                                        <span>{{ $spamLabel }}</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" class="radio radio-sm" name="subject"
                                               value="{{ $otherLabel }}" />
                                        <span>{{ $otherLabel }}</span>
                                    </label>
                                </div>
                            </fieldset>

                            <input type="hidden" name="option" value="{{ $__view->escape($option) }}" />
                            <input type="hidden" name="controller" value="{{ $__view->escape($controller) }}" />
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="category" value="{{ $__view->escape($cat) }}" />
                            <input type="hidden" name="referenceid" value="{{ $__view->escape($refid) }}" />
                            <input type="hidden" name="link" value="{{ $__view->escape($report->href) }}" />
                            <input type="hidden" name="no_html" value="{{ $no_html }}" />
                            {!! Html::input('token') !!}

                            <x-form-field name="field-report"
                                          :label="Lang::txt('COM_SUPPORT_REPORT_ABUSE_DESCRIPTION')">
                                <textarea name="report" id="field-report"
                                          class="textarea textarea-bordered w-full"
                                          rows="10" cols="50"></textarea>
                            </x-form-field>
                        </fieldset>

                        <div class="flex gap-2 mt-4">
                            <button type="submit" class="btn btn-error">
                                {{ Lang::txt('COM_SUPPORT_SUBMIT') }}
                            </button>
                            <a class="btn btn-ghost" href="{{ $report->href }}">
                                {{ Lang::txt('JCANCEL') }}
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Sidebar explanation --}}
                <div>
                    <div class="card bg-base-200">
                        <div class="card-body">
                            <p>{{ Lang::txt('COM_SUPPORT_REPORT_ABUSE_EXPLANATION') }}</p>
                            <p class="mt-2">{{ Lang::txt('COM_SUPPORT_REPORT_ABUSE_DESCRIPTION_HINT') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            @if ($__view->getError())
                <div class="alert alert-error">{{ $__view->getError() }}</div>
            @else
                <div class="alert alert-warning">{{ Lang::txt('COM_SUPPORT_ERROR_NO_INFO_ON_REPORTED_ITEM') }}</div>
            @endif
        @endif
    </x-page-container>
@endif
