{{--
  Compare two publication versions side by side.

  Variables from controller:
    $lft          — left publication version model
    $rgt          — right publication version model
    $diffs        — array of diff results (keys are field names, values are HTML or nested arrays)
    $customFields — custom field definitions with labels

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;

  $__view->css('compare.css');

  // Resolve the best available publish date for a version
  $resolveDate = function ($version) {
      $dt = $version->get('publish_up');
      if (!$dt || $dt == '0000-00-00 00:00:00') {
          $dt = $version->get('approved');
          if (!$dt || $dt == '0000-00-00 00:00:00') {
              $dt = $version->get('submitted');
              if (!$dt || $dt == '0000-00-00 00:00:00') {
                  $dt = $version->get('created');
              }
          }
      }
      return $dt;
  };
@endphp

<x-page-container>
  <h2>{{ Lang::txt('COM_PUBLICATIONS') }}: {{ Lang::txt('COM_PUBLICATIONS_COMPARE') }}</h2>
</x-page-container>

<section class="main section">
  {{-- Version info cards --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    {{-- Left version --}}
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <span class="font-mono text-sm">
          #{{ $lft->get('publication_id') }}, v{{ $lft->get('version_label') }}
        </span>
        <span class="text-sm text-base-content/70">
          {{ Lang::txt('COM_PUBLICATIONS_PUBLISHED') }}:
          @if($lft->isPublished())
            @php
              $dt = $resolveDate($lft);
              $isoDate = Date::of($dt)->format('Y-m-d\TH:i:s\Z');
              $localDate = Date::of($dt)->toLocal();
            @endphp
            <time datetime="{{ $isoDate }}">{{ $localDate }}</time>
          @else
            --
          @endif
        </span>
      </div>
    </div>

    {{-- Right version --}}
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <span class="font-mono text-sm">
          #{{ $rgt->get('publication_id') }}, v{{ $rgt->get('version_label') }}
        </span>
        <span class="text-sm text-base-content/70">
          {{ Lang::txt('COM_PUBLICATIONS_PUBLISHED') }}:
          @if($rgt->isPublished())
            @php
              $dt = $resolveDate($rgt);
              $isoDate = Date::of($dt)->format('Y-m-d\TH:i:s\Z');
              $localDate = Date::of($dt)->toLocal();
            @endphp
            <time datetime="{{ $isoDate }}">{{ $localDate }}</time>
          @else
            --
          @endif
        </span>
      </div>
    </div>
  </div>

  {{-- Diff results --}}
  <div class="diff-results">
    @foreach($diffs as $key => $result)
      @if(is_array($result))
        @foreach($result as $k => $v)
          @if($key == 'metadata')
            @foreach($customFields['fields'] as $field)
              @if($field['name'] == $k)
                <h3 class="text-lg font-semibold border-b border-base-300 pb-2 mt-6 mb-3" id="diffed-{{ $k }}">
                  <span>{{ e($field['label']) }}</span>
                </h3>
              @endif
            @endforeach
          @endif
          @if($v)
            {!! $v !!}
          @else
            <p class="text-base-content/60 italic">{{ Lang::txt('COM_PUBLICATIONS_NO_CHANGES') }}</p>
          @endif
        @endforeach
      @else
        <h3 class="text-lg font-semibold border-b border-base-300 pb-2 mt-6 mb-3" id="diffed-{{ $key }}">
          <span>{{ Lang::txt('COM_PUBLICATIONS_' . strtoupper($key)) }}</span>
        </h3>
        @if($result)
          {!! $result !!}
        @else
          <p class="text-base-content/60 italic">{{ Lang::txt('COM_PUBLICATIONS_NO_CHANGES') }}</p>
        @endif
      @endif
    @endforeach

    {{-- Attachment comparison --}}
    @php
      $lattachments = $lft->attachments()->order('element_id', 'asc')->order('ordering', 'asc')->rows();
      $rattachments = $rgt->attachments()->order('element_id', 'asc')->order('ordering', 'asc')->rows();

      $attachments = [];

      if ($lattachments->count() && $rattachments->count()) {
          $l = 0;
          foreach ($lattachments as $lattachment) {
              $info = new stdClass();
              $info->lft = $lattachment;
              $info->rgt = null;

              $key = $l;

              $z = 0;
              foreach ($rattachments as $rattachment) {
                  if ($z == $l) {
                      $info->rgt = $rattachment;
                      break;
                  }
                  $z++;
              }

              $attachments[$key] = $info;
              $l++;
          }

          $r = 0;
          foreach ($rattachments as $rattachment) {
              if (isset($attachments[$r])) {
                  $r++;
                  continue;
              }

              $info = new stdClass();
              $info->lft = null;
              $info->rgt = $rattachment;

              $attachments[$r] = $info;
              $r++;
          }
      }
    @endphp

    @if(count($attachments))
      <h3 class="text-lg font-semibold border-b border-base-300 pb-2 mt-6 mb-3" id="diffed-files">
        <span>{{ Lang::txt('COM_PUBLICATIONS_ATTACHMENTS') }}</span>
      </h3>
      <table class="table table-sm w-full">
        <tbody>
          @foreach($attachments as $i => $att)
            @php
              $cls = '';

              if (!$att->lft || !$att->rgt || $att->lft->get('type') != $att->rgt->get('type')) {
                  $cls = 'bg-warning/10';
              } else {
                  if ($att->lft->get('type') == 'file') {
                      if ($att->lft->get('content_hash') != $att->rgt->get('content_hash')) {
                          $cls = 'bg-warning/10';
                      }
                  } elseif ($att->lft->get('type') == 'link') {
                      if ($att->lft->get('path') != $att->rgt->get('path')) {
                          $cls = 'bg-warning/10';
                      }
                  } elseif ($att->rgt->get('type') == 'publication') {
                      if ($att->lft->get('path') != $att->rgt->get('path')) {
                          $cls = 'bg-warning/10';
                      }
                  }
              }
            @endphp
            <tr class="{{ $cls }}">
              <th>{{ $att->lft ? ($i + 1) : '' }}</th>
              <td>
                @if($att->lft)
                  {{ $att->lft->get('type') }} &mdash; {{ e($att->lft->get('title', $att->lft->get('path'))) }}
                @endif
              </td>
              <th>{{ $att->rgt ? ($i + 1) : '' }}</th>
              <td>
                @if($att->rgt)
                  {{ $att->rgt->get('type') }} &mdash; {{ e($att->rgt->get('title', $att->rgt->get('path'))) }}
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</section>
