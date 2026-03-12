{{--
  mod_popular — popular articles table

  Shows most-viewed articles with created date and hit count.

  Variables: $list, $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@if (is_countable($list) && count($list))
  <table class="w-full text-xs">
    <thead>
      <tr class="border-b border-base-200">
        <th class="text-left font-medium opacity-70 pb-1.5 pr-2">
          {{ Lang::txt('MOD_POPULAR_ITEMS') }}
        </th>
        <th class="text-left font-medium opacity-70 pb-1.5 pr-2 whitespace-nowrap">
          {{ Lang::txt('MOD_POPULAR_CREATED') }}
        </th>
        <th class="text-right font-medium opacity-70 pb-1.5">
          {{ Lang::txt('JGLOBAL_HITS') }}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($list as $item)
        <tr class="border-b border-base-200/50">
          <td class="py-1.5 pr-2 truncate max-w-0">
            @if ($item->link)
              <a href="{{ $item->link }}" class="link link-hover">
                {{ e($item->title) }}
              </a>
            @else
              {{ e($item->title) }}
            @endif
          </td>
          <td class="py-1.5 pr-2 opacity-70 whitespace-nowrap">
            <time datetime="{{ $item->created }}">
              {{ Date::of($item->created)->toLocal('Y-m-d') }}
            </time>
          </td>
          <td class="py-1.5 text-right tabular-nums font-medium">
            {{ number_format($item->hits) }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@else
  <div class="text-center opacity-70 py-4 text-xs">
    {{ Lang::txt('MOD_POPULAR_NO_MATCHING_RESULTS') }}
  </div>
@endif
