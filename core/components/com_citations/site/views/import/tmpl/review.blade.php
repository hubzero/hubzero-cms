{{--
  Citations — import step 2: review parsed citations.

  Variables from controller (reviewTask):
    $title                         — Page title string
    $citations_require_attention   — Array of citations with duplicates
    $citations_require_no_attention — Array of new citations
    $messages                      — Array of notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_CITATIONS'),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      Lang::txt('COM_CITATIONS_IMPORT'),
      'index.php?option=' . $option . '&task=import'
  );
  Pathway::append(
      Lang::txt('COM_CITATIONS_IMPORT_REVIEW'),
      'index.php?option=' . $option . '&task=import_review'
  );

  Document::setTitle($title);

  $saveUrl = Route::url(
      'index.php?option=' . $option . '&task=import_save', false
  );
  $importUrl = Route::url(
      'index.php?option=' . $option . '&task=import', false
  );

  $noShow = ['errors', 'duplicate'];
@endphp

<x-page-container :title="Lang::txt('COM_CITATIONS_IMPORT_REVIEW')">
  @slot('actions')
    <a class="btn"
       href="{{ $importUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
      </svg>
      {{ Lang::txt('COM_CITATIONS_BACK') }}
    </a>
  @endslot

  <x-alert-list :notifications="$messages" />

  <x-step-nav :steps="[
      ['label' => Lang::txt('COM_CITATIONS_IMPORT_STEP1_NAME'), 'url' => $importUrl],
      Lang::txt('COM_CITATIONS_IMPORT_STEP2_NAME'),
      Lang::txt('COM_CITATIONS_IMPORT_STEP3_NAME'),
  ]" :current="1" />

  <form method="post" action="{{ $saveUrl }}">

    {{-- Citations requiring attention (duplicates) --}}
    @if($citations_require_attention)
      @php
        $attentionCount = count($citations_require_attention);
      @endphp
      <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">
          {!! Lang::txt('COM_CITATIONS_IMPORT_REQUIRE_ATTENTION', $attentionCount) !!}
        </h2>

        <div class="space-y-4">
          @php $counter = 0; @endphp
          @foreach($citations_require_attention as $c)
            @php
              $decodedTitle = html_entity_decode($c['title'] ?? 'Untitled');
              $inputName = 'citation_action_attention[' . $counter . ']';

              $recordAttributes = $c['duplicate']->getAttributes();
              $changedKeys = [];
              foreach ($c as $attribute => $value) {
                  if (!empty($recordAttributes[$attribute]) || !empty($value)) {
                      $changedKeys[] = $attribute;
                  }
              }

              $typeTitle = $c['duplicate']->relatedType->get('type_title');
              $citeTags = \Components\Citations\Helpers\Format
                  ::citationTags($c['duplicate'], false);
              $tags = implode(', ', $citeTags);
              $citeBadges = \Components\Citations\Helpers\Format
                  ::citationBadges($c['duplicate'], false);
              $badges = implode(', ', $citeBadges);
            @endphp
            <div class="card bg-base-100 shadow-sm" x-data="{ open: false }">
              <div class="card-body">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <span class="badge badge-warning badge-sm mb-1">
                      {{ Lang::txt('COM_CITATIONS_IMPORT_DUPLICATE') }}
                    </span>
                    <h3 class="font-semibold">{{ $decodedTitle }}</h3>
                    <button type="button"
                            class="btn btn-ghost btn-xs mt-1"
                            @click="open = !open">
                      <span x-text="open ? 'Hide details' : 'Show details'"></span>
                    </button>
                  </div>
                  <div class="flex flex-col gap-1 text-sm whitespace-nowrap">
                    <label class="label cursor-pointer gap-2">
                      <input type="radio"
                             name="{{ $inputName }}"
                             value="overwrite"
                             class="radio radio-sm"
                             checked />
                      <span class="label-text">
                        {{ Lang::txt('COM_CITATIONS_IMPORT_CITATION_REPLACE') }}
                      </span>
                    </label>
                    <label class="label cursor-pointer gap-2">
                      <input type="radio"
                             name="{{ $inputName }}"
                             value="both"
                             class="radio radio-sm" />
                      <span class="label-text">
                        {{ Lang::txt('COM_CITATIONS_IMPORT_CITATION_KEEP') }}
                      </span>
                    </label>
                    <label class="label cursor-pointer gap-2">
                      <input type="radio"
                             name="{{ $inputName }}"
                             value="discard"
                             class="radio radio-sm" />
                      <span class="label-text">
                        {{ Lang::txt('COM_CITATIONS_IMPORT_CITATION_NOTHING') }}
                      </span>
                    </label>
                  </div>
                </div>

                <div x-show="open" x-cloak class="mt-4">
                  <div class="overflow-x-auto">
                    <table class="table table-sm table-zebra">
                      <thead>
                        <tr>
                          <th class="w-40">
                            {{ Lang::txt('COM_CITATIONS_IMPORT_CITATION_DETAILS') }}
                          </th>
                          <th>{{ Lang::txt('COM_CITATIONS_IMPORT_JUST_UPLOADED') }}</th>
                          <th>{{ Lang::txt('COM_CITATIONS_IMPORT_ON_FILE') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($changedKeys as $k)
                          @if(!in_array($k, $noShow))
                            <tr>
                              <td class="font-medium">
                                {{ str_replace('_', ' ', ucfirst($k)) }}
                              </td>
                              <td>
                                <span class="text-success">
                                  {!! html_entity_decode(nl2br(e($c[$k]))) !!}
                                </span>
                              </td>
                              <td>
                                <span class="text-error">
                                  @switch($k)
                                    @case('type')
                                      {{ $typeTitle }}
                                      @break
                                    @case('tags')
                                      {{ $tags }}
                                      @break
                                    @case('badges')
                                      {{ $badges }}
                                      @break
                                    @default
                                      {!! html_entity_decode(
                                          nl2br(e($c['duplicate']->get($k)))
                                      ) !!}
                                  @endswitch
                                </span>
                              </td>
                            </tr>
                          @endif
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            @php $counter++; @endphp
          @endforeach
        </div>
      </div>
    @endif

    {{-- Citations requiring no attention (new) --}}
    @if($citations_require_no_attention)
      @php
        $noAttentionCount = count($citations_require_no_attention);
      @endphp
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
          <h2 class="text-lg font-semibold">
            {!! Lang::txt(
                'COM_CITATIONS_IMPORT_REQUIRE_NO_ATTENTION',
                $noAttentionCount
            ) !!}
          </h2>
          <label class="label cursor-pointer gap-2">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   checked
                   onchange="document.querySelectorAll('.cite-no-attention').forEach(
                       cb => cb.checked = this.checked
                   )" />
            <span class="label-text text-sm">Select all</span>
          </label>
        </div>

        <div class="space-y-2">
          @php $counter = 0; @endphp
          @foreach($citations_require_no_attention as $c)
            @php
              $cbName = 'citation_action_no_attention[' . $counter . ']';
              $cTitle = array_key_exists('title', $c)
                  ? html_entity_decode($c['title'])
                  : 'NO TITLE FOUND';
            @endphp
            <div class="card bg-base-100 shadow-sm"
                 x-data="{ open: false }">
              <div class="card-body py-3">
                <div class="flex items-start gap-3">
                  <input type="checkbox"
                         class="checkbox checkbox-sm mt-1 cite-no-attention"
                         name="{{ $cbName }}"
                         value="1"
                         checked />
                  <div class="flex-1 min-w-0">
                    <span class="font-medium">{{ $cTitle }}</span>
                    <button type="button"
                            class="btn btn-ghost btn-xs ml-2"
                            @click="open = !open">
                      <span x-text="open ? 'Hide' : 'Details'"></span>
                    </button>

                    <div x-show="open" x-cloak class="mt-3">
                      <div class="overflow-x-auto">
                        <table class="table table-sm table-zebra">
                          <thead>
                            <tr>
                              <th colspan="2">
                                {{ Lang::txt('COM_CITATIONS_IMPORT_CITATION_DETAILS') }}
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach(array_keys($c) as $k)
                              @if(!in_array($k, $noShow))
                                <tr>
                                  <td class="font-medium w-40">
                                    {{ str_replace('_', ' ', ucfirst($k)) }}
                                  </td>
                                  <td>{!! html_entity_decode(nl2br(e($c[$k]))) !!}</td>
                                </tr>
                              @endif
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @php $counter++; @endphp
          @endforeach
        </div>
      </div>
    @endif

    <div class="form-actions">
      <button type="submit" name="submit" class="btn btn-primary">
        {{ Lang::txt('COM_CITATIONS_IMPORT_SUBMIT_IMPORTED') }}
      </button>
    </div>

    {!! Html::input('token') !!}
    <input type="hidden" name="option" value="{{ $option }}" />
    @if(isset($group) && $group != '')
      <input type="hidden" name="group" value="{{ $group }}" />
    @endif
    <input type="hidden" name="task" value="import_save" />
  </form>

</x-page-container>
