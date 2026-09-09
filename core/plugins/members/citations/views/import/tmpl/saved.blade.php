{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css('import.css')->js('import.js');
$base = $member->link() . '&active=citations';

$label = $config->get('citation_label', 'number');
$rollover = $config->get('citation_rollover', 'no');
$batch_download = $config->get('citation_batch_download', 1);

if ($label == 'none') {
    $citations_label_class = 'no-label';
} elseif ($label == 'type') {
    $citations_label_class = 'type-label';
} else {
    $citations_label_class = 'both-label';
}
@endphp

<div class="flex justify-end mb-4">
    <a class="btn btn-ghost btn-sm" href="{{ Route::url($base) }}">
        {{ Lang::txt('PLG_MEMBERS_CITATIONS_BACK') }}
    </a>
</div>

<section id="import">
    @if (isset($messages))
        @foreach ($messages as $message)
            <div class="alert alert-{{ $message['type'] }}">{{ $message['message'] }}</div>
        @endforeach
    @endif

    {{-- Step indicator --}}
    <ul class="steps w-full mb-6">
        <li class="step step-primary">
            <a href="{{ Route::url($base . '&task=import') }}">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP1') }}
                <span class="block text-xs">{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP1_NAME') }}</span>
            </a>
        </li>
        <li class="step step-primary">
            <a href="{{ Route::url($base . '&task=review') }}">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP2') }}
                <span class="block text-xs">{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP2_NAME') }}</span>
            </a>
        </li>
        <li class="step step-primary">
            <a href="{{ Route::url($base . '&task=saved') }}">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3') }}
                <span class="block text-xs">{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3_NAME') }}</span>
            </a>
        </li>
    </ul>

    @if (count($citations) > 0)
        <h3 class="text-xl font-semibold mb-4">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_SUCCESS') }}
        </h3>

        <div class="overflow-x-auto">
            <table class="table w-full">
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach ($citations as $cite)
                        <tr>
                            @if ($label != 'none')
                                <td class="citation-label {{ $citations_label_class }} w-24">
                                    @php
                                        $type = $cite->relatedType->get('type_title', 'Generic');
                                    @endphp
                                    @if ($label == 'number')
                                        <span class="badge badge-ghost">{{ $counter }}.</span>
                                    @elseif ($label == 'type')
                                        <span class="badge badge-outline">{{ $type }}</span>
                                    @elseif ($label == 'both')
                                        <span class="badge badge-ghost">{{ $counter }}.</span>
                                        <span class="badge badge-outline">{{ $type }}</span>
                                    @endif
                                </td>
                            @endif
                            <td class="citation-container">
                                {!! $cite->formatted([], $filters['search']) !!}

                                @if ($rollover == 'yes' && $cite->abstract != '')
                                    <div class="citation-notes mt-2 text-sm text-base-content/70">
                                        <p>{!! nl2br(e($cite->abstract)) !!}</p>
                                    </div>
                                @endif

                                <div class="citation-details mt-2">
                                    {!! $cite->citationDetails($openurl) !!}

                                    @if ($config->get('citation_show_badges', 'no') == 'yes')
                                        {!! \Components\Citations\Helpers\Format::citationBadges($cite) !!}
                                    @endif

                                    @if ($config->get('citation_show_tags', 'no') == 'yes')
                                        {!! \Components\Citations\Helpers\Format::citationTags($cite) !!}
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @php $counter++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
