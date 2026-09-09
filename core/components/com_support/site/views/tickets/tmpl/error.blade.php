{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div id="report-response">
    <div>
        <p>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_ERROR_PROCESSING_FORM') }}</p>
        <p>
            @php($editLabel = \Hubzero\Facades\Lang::txt('COM_SUPPORT_EDIT_REPORT'))
            <a href="javascript:HUB.Modules.ReportProblems.reshowForm();"
               title="{{ $editLabel }}">{{ $editLabel }}</a>
        </p>
    </div>
    <h3>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_ERROR') }}</h3>
    <p>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_ERROR_PROCESSING_DESCRIPTION') }}</p>
    @if ($__view->getError())
        <p>{!! $__view->getError() !!}</p>
    @endif
</div>

<script type="text/javascript">window.top.window.HUB.Modules.ReportProblems.hideTimer();</script>
