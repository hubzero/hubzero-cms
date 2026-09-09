{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js('import.js');

$label = $config->get('citation_label', 'number');
$rollover = $config->get('citation_rollover', 'no');

$citationsFormat = new \Components\Citations\Helpers\Format();
$template = $citationsFormat->getDefaultFormat();

$batch_download = $config->get('citation_batch_download', 1);

if ($label == 'none') {
    $citations_label_class = 'no-label';
} elseif ($label == 'type') {
    $citations_label_class = 'type-label';
} else {
    $citations_label_class = 'both-label';
}

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=citations';
@endphp

<div id="content-header-extra" class="mb-4">
    <a class="btn btn-ghost gap-2" href="{{ Route::url($base) }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        {{ Lang::txt('PLG_GROUPS_CITATIONS_BACK') }}
    </a>
</div>

<section id="import" class="section">
    <div class="section-inner">
        @foreach ($messages as $message)
            <div class="alert {{ $message['type'] === 'error' ? 'alert-error' : 'alert-success' }} mb-4">
                <p>{!! $message['message'] !!}</p>
            </div>
        @endforeach

        {{-- Steps indicator --}}
        <ul class="steps steps-horizontal w-full mb-6">
            <li class="step step-primary">
                <a href="{{ Route::url($base . '&action=import') }}">
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1') }}
                    <span class="block text-xs opacity-70">{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1_NAME') }}</span>
                </a>
            </li>
            <li class="step step-primary">
                <a href="{{ Route::url($base . '&action=review') }}">
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2') }}
                    <span class="block text-xs opacity-70">{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2_NAME') }}</span>
                </a>
            </li>
            <li class="step step-primary">
                <a href="{{ Route::url($base . '&action=saved') }}">
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3') }}
                    <span class="block text-xs opacity-70">{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3_NAME') }}</span>
                </a>
            </li>
        </ul>

        @if (count($citations) > 0)
            @php
            $formatter = new \Components\Citations\Helpers\Format();
            $formatter->setTemplate($template);
            $counter = 1;
            @endphp

            <div class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_SUCCESS') }}</span>
            </div>

            <table class="table table-zebra citations">
                <tbody>
                    @foreach ($citations as $cite)
                        <tr>
                            @if ($label != 'none')
                                <td class="{{ e($citations_label_class) }}">
                                    @php
                                    $type = '';
                                    foreach ($types as $t) {
                                        if ($t['id'] == $cite->type) {
                                            $type = $t['type_title'];
                                        }
                                    }
                                    $type = ($type != '') ? $type : 'Generic';
                                    @endphp

                                    @switch($label)
                                        @case('number')
                                            <span class="badge badge-ghost">{{ $counter }}.</span>
                                            @break
                                        @case('type')
                                            <span class="badge badge-outline">{{ $type }}</span>
                                            @break
                                        @case('both')
                                            <span class="badge badge-ghost">{{ $counter }}.</span>
                                            <span class="badge badge-outline">{{ $type }}</span>
                                            @break
                                    @endswitch
                                </td>
                            @endif
                            <td>
                                {!! $formatter->formatCitation($cite, $filters['search'], false, $config) !!}

                                @if ($rollover == 'yes' && $cite->abstract != '')
                                    <div class="mt-2 text-sm opacity-70">
                                        <p>{!! nl2br(e($cite->abstract)) !!}</p>
                                    </div>
                                @endif

                                <div class="bg-base-200 p-4 rounded text-sm mt-2">
                                    {!! $formatter->citationDetails($cite, $database, $config, $openurl) !!}

                                    @if ($config->get('citation_show_badges', 'no') == 'yes')
                                        {!! \Components\Citations\Helpers\Format::citationBadges($cite, $database) !!}
                                    @endif

                                    @if ($config->get('citation_show_tags', 'no') == 'yes')
                                        {!! \Components\Citations\Helpers\Format::citationTags($cite, $database) !!}
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @php $counter++; @endphp
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</section>
