{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Request;

    $ticketUrl = Route::url(
        'index.php?option=com_support&task=ticket&id=' . $ticket
    );
@endphp

@if ($no_html)
    <div id="report-response">
        <div>
            <p>
                {{ Lang::txt('COM_FEEDBACK_YOUR_TICKET') }} #
                <span>
                    <a href="{{ $ticketUrl }}" title="View ticket">{{ $ticket }}</a>
                </span>
            </p>
            <p>
                @php($newReportLabel = Lang::txt('COM_FEEDBACK_NEW_REPORT'))
                <button class="btn btn-reset" title="{{ $newReportLabel }}">
                    {{ $newReportLabel }}
                </button>
            </p>
        </div>
        <p>
            {{ Lang::txt('COM_FEEDBACK_TROUBLE_THANKS') }}<br /><br />
            {{ Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_TIMES') }}
        </p>
    </div>
    <script type="text/javascript">window.top.window.HUB.ReportProblem.hideTimer();</script>
@else
    <x-page-container :title="$title">
        @if ($__view->getError())
            <div class="alert alert-error" role="alert">
                {!! $__view->getError() !!}
            </div>
        @endif

        <p>{{ Lang::txt('COM_FEEDBACK_TROUBLE_THANKS') }}</p>
        <p class="alert alert-info">{{ Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_TIMES') }}</p>

        @if ($ticket)
            <p>{!! Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_REFERENCE', $ticket) !!}</p>
        @endif
    </x-page-container>
@endif
