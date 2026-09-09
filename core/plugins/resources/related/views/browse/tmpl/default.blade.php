{{--
  Related resources — full display with ranking and tooltips.

  Variables (from plugin):
    $option  — string: component option
    $related — array: related resource objects

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<h3>{{ Lang::txt('PLG_RESOURCES_RELATED_HEADER') }}</h3>

@if($related)
  <table class="related-resources">
    <tbody>
      @foreach($related as $line)
        @php
          if ($line->section != 'Topic') {
              $sef = $line->alias
                  ? Route::url('index.php?option=' . $option . '&alias=' . $line->alias)
                  : Route::url('index.php?option=' . $option . '&id=' . $line->id);
          } else {
              $sef = ($line->group_cn != '' && $line->scope != '')
                  ? Route::url('index.php?option=com_groups&scope=' . $line->scope . '&pagename=' . $line->alias)
                  : Route::url('index.php?option=com_wiki&scope=' . $line->scope . '&pagename=' . $line->alias);
          }

          $line->ranking = round($line->ranking, 1);
          $r = (10 * $line->ranking);
          if (intval($r) < 10) {
              $r = '0' . $r;
          }
        @endphp
        <tr>
          <td class="ranking">
            {{ number_format($line->ranking, 1) }}
            <span class="rank-{{ $r }}">{{ Lang::txt('PLG_RESOURCES_RELATED_RANKING') }}</span>
          </td>
          <td>
            @if($line->section != 'Topic')
              {{ Lang::txt('PLG_RESOURCES_RELATED_PART_OF') }}
              <a href="{{ $sef }}"
                 class="fixedResourceTip"
                 title="DOM:rsrce{{ $line->id }}">{{ stripslashes($line->title) }}</a>
              <div class="hide" id="rsrce{{ $line->id }}">
                <h4>{{ stripslashes($line->title) }}</h4>
                <div>
                  <table>
                    <tbody>
                      <tr>
                        <th>{{ Lang::txt('PLG_RESOURCES_RELATED_TYPE') }}</th>
                        <td>{{ $line->section }}</td>
                      </tr>
                      <tr>
                        <th>{{ Lang::txt('PLG_RESOURCES_RELATED_DATE') }}</th>
                        <td>{{ Date::of($line->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                {!! \Hubzero\Utility\Str::truncate(stripslashes($line->introtext), 300) !!}
              </div>
            @else
              <a href="{{ $sef }}">{{ stripslashes($line->title) }}</a>
            @endif
          </td>
          <td class="type">{{ $line->section }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@else
  <p>{{ Lang::txt('PLG_RESOURCES_RELATED_NO_RESULTS_FOUND') }}</p>
@endif
