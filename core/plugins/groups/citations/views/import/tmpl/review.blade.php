{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js('import.js');

$database = App::get('db');

$citations_require_attention = $citations_require_attention;
$citations_require_no_attention = $citations_require_no_attention;

$no_show = ['errors', 'duplicate', 'bdsk-file-1'];

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=citations';
@endphp

<section id="import" class="section">
    <div class="section-inner">
        @if ($messages)
            @foreach ($messages as $message)
                <div class="alert {{ $message['type'] === 'error' ? 'alert-error' : 'alert-info' }} mb-4">
                    <p>{!! $message['message'] !!}</p>
                </div>
            @endforeach
        @endif

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
            <li class="step">
                <span>
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3') }}
                    <span class="block text-xs opacity-70">{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3_NAME') }}</span>
                </span>
            </li>
        </ul>

        <form method="post" id="hubForm" action="{{ Route::url($base . '&action=process') }}">
            {{-- Citations requiring attention (duplicates) --}}
            @if ($citations_require_attention)
                <div class="card bg-base-200 mb-6">
                    <div class="card-body">
                        <h3 class="card-title text-warning">
                            {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_REQUIRE_ATTENTION', count($citations_require_attention)) }}
                        </h3>

                        @php $counter = 0; @endphp
                        @foreach ($citations_require_attention as $c)
                            @php
                            $cc = $c['duplicate'];
                            $type_title = $cc->relatedType->get('type_title');
                            $dupTags = implode(
                                ', ',
                                \Components\Citations\Helpers\Format::citationTags($c['duplicate'], false)
                            );
                            $dupBadges = implode(
                                ', ',
                                \Components\Citations\Helpers\Format::citationBadges($c['duplicate'], false)
                            );
                            @endphp

                            <div class="collapse collapse-arrow bg-base-300 mb-2">
                                <input type="checkbox" />
                                <div class="collapse-title">
                                    <span class="badge badge-warning badge-sm mr-2">
                                        {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_DUPLICATE') }}
                                    </span>
                                    {{ html_entity_decode($c['title']) }}
                                </div>
                                <div class="collapse-content">
                                    {{-- Action options --}}
                                    <div class="flex flex-wrap gap-4 mb-4 p-3 bg-base-200 rounded-lg">
                                        @php
                                        $attName = "citation_action_attention[{$counter}]";
                                        @endphp
                                        <label class="label cursor-pointer gap-2">
                                            <input type="radio"
                                                class="radio radio-sm citation_require_attention_option"
                                                name="{{ $attName }}"
                                                id="citation_action_attention-{{ $counter }}-replace"
                                                value="overwrite"
                                                checked />
                                            <span class="label-text">
                                                {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_REPLACE') }}
                                            </span>
                                        </label>
                                        <label class="label cursor-pointer gap-2">
                                            <input type="radio"
                                                class="radio radio-sm citation_require_attention_option"
                                                name="{{ $attName }}"
                                                id="citation_action_attention-{{ $counter }}-keep"
                                                value="both" />
                                            <span class="label-text">
                                                {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_KEEP') }}
                                            </span>
                                        </label>
                                        <label class="label cursor-pointer gap-2">
                                            <input type="radio"
                                                class="radio radio-sm citation_require_attention_option"
                                                name="{{ $attName }}"
                                                id="citation_action_attention-{{ $counter }}-nothing"
                                                value="discard" />
                                            <span class="label-text">
                                                {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_NOTHING') }}
                                            </span>
                                        </label>
                                    </div>

                                    {{-- Differences table --}}
                                    @php
                                    $recordAttributes = $cc->getAttributes();
                                    $changedKeys = [];
                                    foreach ($c as $attribute => $value) {
                                        if (!empty($recordAttributes[$attribute]) || !empty($value)) {
                                            $changedKeys[] = $attribute;
                                        }
                                    }
                                    @endphp

                                    <table class="table table-zebra table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_DETAILS') }}</th>
                                                <th>{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_JUST_UPLOADED') }} / {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_ON_FILE') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($changedKeys as $k)
                                                @if (!in_array($k, $no_show))
                                                    @php
                                                    $newVal = html_entity_decode(nl2br($c[$k]));
                                                    $oldVal = '';
                                                    switch ($k) {
                                                        case 'type':
                                                            $oldVal = $type_title;
                                                            break;
                                                        case 'tags':
                                                            $oldVal = $dupTags;
                                                            break;
                                                        case 'badges':
                                                            $oldVal = $dupBadges;
                                                            break;
                                                        default:
                                                            $attrKeys = array_keys($cc->getAttributes());
                                                            if (in_array($k, $attrKeys)) {
                                                                $oldVal = html_entity_decode(nl2br($cc->$k));
                                                            }
                                                            break;
                                                    }
                                                    @endphp
                                                    <tr>
                                                        <td class="font-semibold">{{ str_replace('_', ' ', $k) }}</td>
                                                        <td>
                                                            <div class="mb-1">
                                                                <span class="badge badge-success badge-xs mr-1">new</span>
                                                                <span class="text-success">{!! $newVal !!}</span>
                                                            </div>
                                                            <div>
                                                                <span class="badge badge-error badge-xs mr-1">old</span>
                                                                <span class="text-error opacity-60">{!! $oldVal !!}</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @php $counter++; @endphp
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Citations requiring no attention --}}
            @if ($citations_require_no_attention)
                <div class="card bg-base-200 mb-6">
                    <div class="card-body">
                        <div class="flex items-center gap-2 mb-4">
                            <input type="checkbox"
                                class="checkbox checkbox-sm checkall"
                                name="select-all-no-attention"
                                checked />
                            <h3 class="card-title m-0">
                                {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_REQUIRE_NO_ATTENTION', count($citations_require_no_attention)) }}
                            </h3>
                        </div>

                        @php $counter = 0; @endphp
                        @foreach ($citations_require_no_attention as $c)
                            @php
                            $noAttName = "citation_action_no_attention[{$counter}]";
                            $counter++;
                            @endphp

                            <div class="collapse collapse-arrow bg-base-300 mb-2">
                                <input type="checkbox" />
                                <div class="collapse-title flex items-center gap-2">
                                    <input type="checkbox"
                                        class="checkbox checkbox-sm check-single"
                                        name="{{ $noAttName }}"
                                        checked
                                        value="1"
                                        onclick="event.stopPropagation();" />
                                    <span class="font-semibold text-base-content/60">
                                        @if (array_key_exists('title', $c))
                                            {{ html_entity_decode($c['title']) }}
                                        @else
                                            NO TITLE FOUND
                                        @endif
                                    </span>
                                </div>
                                <div class="collapse-content">
                                    <table class="table table-zebra table-sm">
                                        <thead>
                                            <tr>
                                                <th colspan="2">
                                                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION_DETAILS') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (array_keys($c) as $k)
                                                @if (!in_array($k, $no_show))
                                                    <tr>
                                                        <td class="font-semibold w-1/4">{{ str_replace('_', ' ', $k) }}</td>
                                                        <td>{!! html_entity_decode(nl2br($c[$k])) !!}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex gap-2">
                <button type="submit"
                    id="review-input"
                    name="submit"
                    class="btn btn-primary"
                    value="{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_SUBMIT_IMPORTED') }}">
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_SUBMIT_IMPORTED') }}
                </button>
                <a class="btn btn-ghost" href="{{ Route::url($base) }}">
                    {{ Lang::txt('JCANCEL') }}
                </a>
            </div>

            {!! Html::input('token') !!}
            <input type="hidden" name="option" value="com_groups" />
            <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
            <input type="hidden" name="active" value="citations" />
            <input type="hidden" name="action" value="process" />
        </form>
    </div>
</section>
