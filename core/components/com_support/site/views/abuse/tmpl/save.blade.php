{{--
 * Abuse report confirmation — displayed after successful submission.
 *
 * Variables from controller (saveTask):
 *   $title      — Page title
 *   $report     — The saved report model
 *   $refid      — Reference ID
 *   $cat        — Category
 *   $returnlink — URL to return to the original content
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\Lang;
@endphp

<x-page-container :title="$title">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="text-3xl font-bold">
                {!! Lang::txt('COM_SUPPORT_REPORT_NUMBER', $report->id) !!}
            </div>
        </div>
        <div>
            <div class="card bg-base-200">
                <div class="card-body">
                    <h3 class="card-title">{{ Lang::txt('COM_SUPPORT_REPORT_ABUSE_THANKS') }}</h3>
                    @if ($report)
                        <p>{{ Lang::txt('COM_SUPPORT_REPORT_NUMBER_REFERENCE', $report->id) }}</p>
                    @endif
                    @if ($returnlink)
                        <div class="card-actions mt-4">
                            <a class="btn btn-primary" href="{{ $returnlink }}">
                                {{ Lang::txt('COM_SUPPORT_REPORT_ABUSE_CONTINUE') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-page-container>
