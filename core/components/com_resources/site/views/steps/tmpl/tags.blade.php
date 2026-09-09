{{--
  Resource contribution step — Tags & focus areas.

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $task       — current task
    $step       — current step number
    $next_step  — next step number
    $steps      — array of step names
    $id         — resource ID
    $row        — resource model
    $fas        — focus area tag groups
    $existing   — existing tags
    $progress   — array of step completion flags

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('create.css');
  $__view->js('create.js');
  $__view->js('tags.js');

  $draftUrl = Route::url('index.php?option=' . $option . '&task=draft');
  $actionUrl = Route::url(
      'index.php?option=' . $option
      . '&task=draft&step=' . $next_step
      . '&id=' . $id
  );

  if (!function_exists('stem')) {
      function stem($str)
      {
          $suffixPattern = '/(?:e[dr]|ing|e?s|or|ator|able|ible|acious'
              . '|ary|ate|ation|cy|eer|or|escent|fic|fy|iferous'
              . '|ile?|ism|ist|ity|ive|ise|ize|oid|ose|osis'
              . '|ous|tude)+$/';
          $prefixPattern = '/^(?:a[bdcfglnpst]?|ant[ei]?|be'
              . '|co[mlnr]?|de|di[as]?|e[nmxf]|extra|hemi'
              . '|hyper|hypo|over|peri|post|pr[eo]|re|semi'
              . '|su[bcfgprs]|sy[nm]|trans|ultra|un|under)+/';
          return preg_replace(
              $prefixPattern,
              '',
              preg_replace($suffixPattern, '', $str)
          );
      }
  }

  $recommended = new \Components\Resources\Helpers\RecommendedTags($id, $existing);
@endphp

@php
  /**
   * Render focus area controls recursively.
   */
  function renderFaControls($idx, $fas, $fa_props, $existing, $parent = null, $depth = 1)
  {
      $html = '';
      foreach ($fas as $fa) {
          $props = $fa_props[$fa['label']];
          $multiple = !is_null($props['multiple_depth']) && $props['multiple_depth'] <= $depth;
          $inputType = $multiple ? 'checkbox' : 'radio';
          $inputName = 'tagfa-' . $idx . ($parent ? '-' . $parent : '') . '[]';
          $inputId = 'tagfa-' . $idx . '-' . $fa['tag'];
          $isChecked = isset($existing[strtolower($fa['raw_tag'])]);

          $classes = 'flex items-center gap-2';
          if ($depth === 1) {
              $classes .= ' mt-2';
          } else {
              $classes .= ' ml-6 mt-1';
          }

          $html .= '<div class="' . $classes . '">';
          $html .= '<input class="' . ($multiple ? 'checkbox checkbox-sm' : 'radio radio-sm') . '"'
              . ' type="' . $inputType . '"'
              . ($isChecked ? ' checked' : '')
              . ' id="' . e($inputId) . '"'
              . ' name="' . e($inputName) . '"'
              . ' value="' . e($fa['tag']) . '" />';
          $html .= '<label for="' . e($inputId) . '"';
          if ($fa['description']) {
              $html .= ' title="' . e($fa['description']) . '"';
          }
          $html .= '>' . e($fa['raw_tag']) . '</label>';
          $html .= '</div>';

          if ($fa['children']) {
              $html .= renderFaControls($idx, $fa['children'], $fa_props, $existing, $fa['tag'], $depth + 1);
          }
      }
      return $html;
  }
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ $draftUrl }}">
      {{ Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION') }}
    </a>
  @endslot

  @include('steps::steps', [
      'option'   => $option,
      'step'     => $step,
      'steps'    => $steps,
      'id'       => $id,
      'resource' => $row,
      'progress' => $progress,
  ])

  @if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{{ $__view->getError() }}</span>
    </div>
  @endif

  <form action="{{ $actionUrl }}" method="post" id="hubForm" class="space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
          <legend class="fieldset-legend text-lg font-semibold">
            {{ Lang::txt('COM_CONTRIBUTE_TAGS_ADD') }}
          </legend>

          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="{{ $controller }}" />
          <input type="hidden" name="task" value="{{ $task }}" />
          <input type="hidden" name="step" value="{{ $next_step }}" />
          <input type="hidden" name="id" value="{{ $id }}" />

          {{-- Focus areas --}}
          @if(count($fas) > 0)
            @php
              $fa_existing = $recommended->getExistingFocusAreasMap();
              $fa_props = $recommended->getFocusAreaProperties();
              $idx = 0;
            @endphp

            @foreach($fas as $label => $faGroup)
              @php
                $idx++;
                $isRequired = !empty($fa_props[$label]['mandatory_depth']);
              @endphp
              <fieldset class="fieldset border border-base-300 p-3 rounded-box mb-4">
                <legend class="fieldset-legend font-medium">
                  Select {{ e($label) }}:
                  @if($isRequired)
                    <span class="text-error text-sm">*</span>
                  @endif
                </legend>
                {!! renderFaControls($idx, $faGroup, $fa_props, $fa_existing) !!}
              </fieldset>
            @endforeach
          @endif

          {{-- Tag input --}}
          <div class="mt-4">
            <label for="actags" class="label">
              <span class="label-text">
                {{ Lang::txt('COM_CONTRIBUTE_TAGS_ASSIGNED') }}
              </span>
            </label>
            @php
              $tf = Event::trigger(
                  'hubzero.onGetMultiEntry',
                  [['tags', 'tags', 'actags', '', $recommended->getExistingTagsValueList()]]
              );
            @endphp

            @if(count($tf) > 0)
              {!! $tf[0] !!}
            @else
              <textarea name="tags"
                        id="actags"
                        rows="6"
                        class="textarea textarea-bordered w-full"
                        aria-label="{{ Lang::txt('COM_CONTRIBUTE_TAGS_ASSIGNED') }}"
              >{{ $recommended->getExistingTagsValueList() }}</textarea>
            @endif

            <p class="text-sm text-base-content/70 mt-1">
              {{ Lang::txt('COM_CONTRIBUTE_TAGS_NEW_EXPLANATION') }}
            </p>
          </div>

          {{-- Suggested tags --}}
          @php $rec = $recommended->getTags(); @endphp
          @if($rec)
            <div class="mt-4">
              <p class="text-sm font-medium mb-2">
                {{ Lang::txt('Suggested tags:') }}
                <span class="text-base-content/50">
                  ({{ Lang::txt('click to add to your contribution') }})
                </span>
              </p>
              <div class="flex flex-wrap gap-2">
                @foreach($rec as $tag)
                  <button type="button"
                          class="badge badge-outline badge-lg cursor-pointer hover:badge-primary"
                          data-tag="{{ e($tag['text']) }}"
                          aria-label="{{ Lang::txt('Add tag: %s', e($tag['text'])) }}">
                    {{ e($tag['text']) }}
                  </button>
                @endforeach
              </div>
            </div>
          @endif
        </fieldset>
      </div>

      <div class="space-y-4">
        <div class="card bg-base-200 shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-base">
              {{ Lang::txt('COM_CONTRIBUTE_TAGS_WHAT_ARE_TAGS') }}
            </h3>
            <p class="text-sm text-base-content/70">
              {{ Lang::txt('COM_CONTRIBUTE_TAGS_EXPLANATION') }}
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end">
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('COM_CONTRIBUTE_NEXT') }}
      </button>
    </div>
  </form>
</x-page-container>
