{{--
  Curation review — individual publication curation page with block checklist.

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $pub        — publication model
    $history    — curation history record (or null)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  // Push stylesheets
  \Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications');
  \Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'curation.css');

  $__view->css()
      ->js()
      ->css('jquery.fancybox.css', 'system')
      ->css('curation.css')
      ->js('curation.js');

  $status = $pub->getStatusName();
  $statusCss = $pub->getStatusCss();
  $typetitle = \Components\Publications\Helpers\Html::writePubCategory(
      $pub->category()->alias,
      $pub->category()->name
  );

  $listUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller);
  $formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller);
  $saveRoute = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&id=' . $pub->id . '&task=save'
  );
  $thumbUrl = Route::url(
      'index.php?option=com_publications&id='
      . $pub->id . '&v=' . $pub->version_id
  ) . '/Image:thumb';
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-ghost" href="{{ $listUrl }}">
      {{ Lang::txt('COM_PUBLICATIONS_CURATION_LIST') }}
    </a>
  @endslot

  <form action="{{ $formAction }}"
        method="post"
        id="curation-form"
        name="curation-form">
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="id" id="pid"
           value="{{ $pub->id }}"
           data-route="{{ $saveRoute }}" />
    <input type="hidden" name="vid" id="vid" value="{{ $pub->version_id }}" />
    <input type="hidden" name="task" id="task" value="save" />

    <div class="curation-wrap">
      {{-- Publication title and type --}}
      <div class="pubtitle">
        <h3>
          <span class="restype indlist">{!! $typetitle !!}</span>
          {{ \Hubzero\Utility\Str::truncate($pub->title, 65) }}
          | {{ Lang::txt('COM_PUBLICATIONS_CURATION_VERSION') . ' ' . $pub->version_label }}
        </h3>
      </div>

      {{-- Publication info --}}
      <p class="instruct">
        <span class="pubimage">
          <img src="{{ $thumbUrl }}" alt="" />
        </span>
        <strong class="block">
          @php
            $submittedLabel = $pub->reviewed
                ? Lang::txt('COM_PUBLICATIONS_CURATION_RESUBMITTED')
                : Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED');
            $modName = $pub->modifier('name') ?: Lang::txt('JUNKNOWN');
          @endphp
          {{ $submittedLabel }}
          {{ Date::of($pub->submitted)->toLocal('M d, Y') }}
          {{ Lang::txt('COM_PUBLICATIONS_CURATION_BY', $modName) }}
        </strong>
        @if ($pub->curator())
          <span class="block">
            {!! Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGNED_CURATOR')
                . ' <strong>' . e($pub->curator('name'))
                . ' (' . e($pub->curator('username')) . ')</strong>' !!}
          </span>
        @endif
        {{ Lang::txt('COM_PUBLICATIONS_CURATION_REVIEW_AND_ACT') }}
        <span class="legend">
          <span class="legend-checker-none">{{ Lang::txt('COM_PUBLICATIONS_CURATION_LEGEND_NONE') }}</span>
          <span class="legend-checker-pass">{{ Lang::txt('COM_PUBLICATIONS_CURATION_LEGEND_PASS') }}</span>
          <span class="legend-checker-fail">{{ Lang::txt('COM_PUBLICATIONS_CURATION_LEGEND_FAIL') }}</span>
          <span class="legend-checker-update">{{ Lang::txt('COM_PUBLICATIONS_CURATION_LEGEND_UPDATE') }}</span>
        </span>
      </p>

      <div class="clear"></div>

      {{-- Action buttons --}}
      <div class="submit-curation">
        <p>
          <span class="button-wrapper icon-kickback">
            <input type="submit"
                   value="{{ Lang::txt('COM_PUBLICATIONS_CURATION_LOOKS_BAD') }}"
                   class="btn btn-primary active icon-kickback btn-curate curate-kickback" />
          </span>
          <span class="button-wrapper icon-apply">
            <input type="submit"
                   value="{{ Lang::txt('COM_PUBLICATIONS_CURATION_LOOKS_GOOD') }}"
                   class="btn btn-success active icon-apply btn-curate curate-save" />
          </span>
        </p>
      </div>

      {{-- Submitter comment --}}
      @if ($history && $history->comment)
        <div class="submitter-comment">
          <h5>{{ Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTER_COMMENT') }}</h5>
          <p>{!! $history->comment !!}</p>
        </div>
      @endif

      {{-- Curation blocks --}}
      <div class="curation-blocks">
        @foreach ($pub->curation('blocks') as $blockId => $block)
          @if (isset($block->active) && $block->active == 0)
            @continue
          @endif

          @php
            $pub->_curationModel->setBlock($block->name, $blockId);
          @endphp

          @if ($block->name != 'review')
            {!! $pub->_curationModel->parseBlock('curator') !!}
          @endif
        @endforeach
      </div>
    </div>
  </form>

  {{-- Hidden notice dialog for fancybox --}}
  <div class="hidden">
    <div id="addnotice" class="addnotice">
      <form id="notice-form" name="noticeForm" action="{{ $formAction }}" method="post">
        <fieldset>
          <legend>{{ Lang::txt('COM_PUBLICATIONS_CURATION_NOTICE_TITLE') }}</legend>

          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="{{ $controller }}" />
          <input type="hidden" name="task" value="save" />
          <input type="hidden" name="id" value="{{ $pub->get('id') }}" />
          <input type="hidden" name="vid" value="{{ $pub->get('version_id') }}" />
          <input type="hidden" name="ajax" value="1" />
          <input type="hidden" name="no_html" value="1" />
          <input type="hidden" name="p" id="props" value="" />
          <input type="hidden" name="pass" value="0" />

          <p class="notice-item" id="notice-item"></p>

          <div class="form-group">
            <label for="notice-review">
              <span class="block">{{ Lang::txt('COM_PUBLICATIONS_CURATION_NOTICE_LABEL') }}</span>
              <textarea name="review"
                        id="notice-review"
                        class="textarea textarea-bordered w-full"
                        rows="5"
                        cols="10"></textarea>
            </label>
          </div>
        </fieldset>
        <p class="submitarea">
          <input type="submit"
                 id="notice-submit"
                 class="btn btn-primary"
                 value="{{ Lang::txt('COM_PUBLICATIONS_CURATION_MARK_AS_FAIL') }}" />
        </p>
      </form>
    </div>
  </div>

</x-page-container>
