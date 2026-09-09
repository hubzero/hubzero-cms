{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css('import.css')->js('import.js');
$base = $member->link() . '&active=citations';
$no_show = ['errors', 'duplicate'];
@endphp

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
        <li class="step">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3') }}
            <span class="block text-xs">{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3_NAME') }}</span>
        </li>
    </ul>

    <form method="post"
        id="hubForm"
        action="{{ Route::url($base . '&task=process') }}">

        {{-- Citations requiring attention (duplicates) --}}
        @if ($citations_require_attention)
            @php
                $attentionCount = count($citations_require_attention);
            @endphp
            <div class="overflow-x-auto mb-6">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_REQUIRE_ATTENTION', $attentionCount) }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($citations_require_attention as $counter => $c)
                            @php
                                $cc = $c['duplicate'];
                                $type_title = $cc->relatedType->get('type_title');
                                $citeTags = implode(', ', \Components\Citations\Helpers\Format::citationTags($c['duplicate'], false));
                                $citeBadges = implode(', ', \Components\Citations\Helpers\Format::citationBadges($c['duplicate'], false));
                                $dupTitle = html_entity_decode($c['title']);
                                $fieldName = 'citation_action_attention[' . $counter . ']';
                                $replaceId = 'citation_action_attention-' . $counter . '-replace';
                                $keepId = 'citation_action_attention-' . $counter . '-keep';
                                $nothingId = 'citation_action_attention-' . $counter . '-nothing';
                            @endphp
                            <tr>
                                <td>
                                    <span class="font-semibold">
                                        <u>{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_DUPLICATE') }}</u>:
                                        {{ $dupTitle }}
                                    </span>

                                    <details class="collapse collapse-arrow bg-base-200 mt-2">
                                        <summary class="collapse-title text-sm cursor-pointer">
                                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_SHOW_CITATION_DETAILS') }}
                                        </summary>
                                        <div class="collapse-content">
                                            <div class="overflow-x-auto">
                                                <table class="table table-compact w-full">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION_DETAILS') }}
                                                            </th>
                                                            <th>
                                                                <div class="flex flex-wrap gap-4">
                                                                    <label class="flex items-center gap-1 cursor-pointer" for="{{ $replaceId }}">
                                                                        <input type="radio"
                                                                            class="radio radio-sm radio-primary citation_require_attention_option"
                                                                            name="{{ $fieldName }}"
                                                                            id="{{ $replaceId }}"
                                                                            value="overwrite"
                                                                            checked />
                                                                        {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION_REPLACE') }}
                                                                    </label>
                                                                    <label class="flex items-center gap-1 cursor-pointer" for="{{ $keepId }}">
                                                                        <input type="radio"
                                                                            class="radio radio-sm radio-primary citation_require_attention_option"
                                                                            name="{{ $fieldName }}"
                                                                            id="{{ $keepId }}"
                                                                            value="both" />
                                                                        {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION_KEEP') }}
                                                                    </label>
                                                                    <label class="flex items-center gap-1 cursor-pointer" for="{{ $nothingId }}">
                                                                        <input type="radio"
                                                                            class="radio radio-sm radio-primary citation_require_attention_option"
                                                                            name="{{ $fieldName }}"
                                                                            id="{{ $nothingId }}"
                                                                            value="discard" />
                                                                        {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION_NOTHING') }}
                                                                    </label>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $recordAttributes = $cc->getAttributes();
                                                            $changedKeys = [];
                                                            foreach ($c as $attribute => $value) {
                                                                if (!empty($recordAttributes[$attribute]) || !empty($value)) {
                                                                    $changedKeys[] = $attribute;
                                                                }
                                                            }
                                                        @endphp
                                                        @foreach ($changedKeys as $k)
                                                            @if (!in_array($k, $no_show))
                                                                <tr>
                                                                    <td class="font-medium align-top">
                                                                        {{ str_replace('_', ' ', $k) }}
                                                                    </td>
                                                                    <td>
                                                                        <table class="table table-compact w-full">
                                                                            <tr>
                                                                                <td class="text-xs text-base-content/50 w-24">
                                                                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_JUST_UPLOADED') }}:
                                                                                </td>
                                                                                <td>
                                                                                    <span class="text-success">
                                                                                        {!! html_entity_decode(nl2br($c[$k])) !!}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-xs text-base-content/50 w-24">
                                                                                    {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_ON_FILE') }}:
                                                                                </td>
                                                                                <td>
                                                                                    <span class="text-error line-through">
                                                                                        @switch($k)
                                                                                            @case('type')
                                                                                                {{ $type_title }}
                                                                                                @break
                                                                                            @case('tags')
                                                                                                {{ $citeTags }}
                                                                                                @break
                                                                                            @case('badges')
                                                                                                {{ $citeBadges }}
                                                                                                @break
                                                                                            @default
                                                                                                {!! html_entity_decode(nl2br($cc->get($k))) !!}
                                                                                        @endswitch
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Citations requiring no attention (new) --}}
        @if ($citations_require_no_attention)
            @php
                $noAttentionCount = count($citations_require_no_attention);
            @endphp
            <div class="overflow-x-auto mb-6">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th class="w-10">
                                <input type="checkbox"
                                    class="checkbox checkbox-sm checkall"
                                    name="select-all-no-attention"
                                    checked />
                            </th>
                            <th>
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_REQUIRE_NO_ATTENTION', $noAttentionCount) }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($citations_require_no_attention as $counter => $c)
                            <tr>
                                <td>
                                    <input type="checkbox"
                                        class="checkbox checkbox-sm check-single"
                                        name="citation_action_no_attention[{{ $counter }}]"
                                        checked
                                        value="1" />
                                </td>
                                <td>
                                    <span class="font-semibold">
                                        @if (array_key_exists('title', $c))
                                            {{ html_entity_decode($c['title']) }}
                                        @else
                                            NO TITLE FOUND
                                        @endif
                                    </span>

                                    <details class="collapse collapse-arrow bg-base-200 mt-2">
                                        <summary class="collapse-title text-sm cursor-pointer">
                                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_SHOW_CITATION_DETAILS') }}
                                        </summary>
                                        <div class="collapse-content">
                                            <div class="overflow-x-auto">
                                                <table class="table table-compact w-full">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2">
                                                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION_DETAILS') }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach (array_keys($c) as $k)
                                                            @if (!in_array($k, $no_show))
                                                                <tr>
                                                                    <td class="font-medium">
                                                                        {{ str_replace('_', ' ', $k) }}
                                                                    </td>
                                                                    <td>
                                                                        {!! html_entity_decode(nl2br($c[$k])) !!}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex gap-2 mt-6">
            <button type="submit" name="submit" class="btn btn-primary">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_SUBMIT_IMPORTED') }}
            </button>
            <a class="btn btn-ghost" href="{{ Route::url($base) }}">
                {{ Lang::txt('JCANCEL') }}
            </a>
        </div>

        {!! Html::input('token') !!}
        <input type="hidden" name="option" value="com_members" />
        <input type="hidden" name="id" value="{{ $member->get('id') }}" />
        <input type="hidden" name="active" value="citations" />
        <input type="hidden" name="action" value="process" />
    </form>
</section>
