{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div{!! ($moduleclass) ? ' class="' . $moduleclass . '"' : '' !!}>
    <div class="flex gap-2 mb-3">
        <a class="btn btn-sm btn-outline"
            href="{{ Route::url('index.php?option=com_publications&controller=curation') }}"
        >{{ Lang::txt('MOD_MYCURATION_ALL_TASKS') }}</a>
    </div>

    <h4>
        <a href="{{ Route::url('index.php?option=com_publications&controller=curation&assigned=1') }}">
            {{ Lang::txt('MOD_MYCURATION_ASSIGNED') }}
            <span class="text-sm">{{ Lang::txt('MOD_MYCURATION_VIEW_ALL') }}</span>
        </a>
    </h4>

    @if (count($rows) <= 0)
        <p class="text-base-content/60"><em>{{ Lang::txt('MOD_MYCURATION_NO_ITEMS') }}</em></p>
    @else
        <ul class="list bg-base-100 rounded-box">
            @foreach ($rows as $row)
                @php
                    if ($row->state == 5) {
                        $rowUrl = Route::url('index.php?option=com_publications&controller=curation&id=' . $row->id);
                    } else {
                        $rowUrl = Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_number);
                    }
                    $thumbUrl = Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:$thumb';
                @endphp
                <li class="list-row">
                    <div class="list-col">
                        <img src="{{ $thumbUrl }}" alt="" class="w-10 h-10 rounded" />
                    </div>
                    <div class="list-col grow">
                        <a href="{{ $rowUrl }}">
                            {{ $row->title }} v.{{ $row->version_label }}
                        </a>
                        @if ($row->state == 5)
                            @php
                                $curationUrl = Route::url('index.php?option=com_publications&controller=curation&id=' . $row->id);
                            @endphp
                            <span class="text-xs">
                                <a class="btn btn-xs btn-outline btn-primary" href="{{ $curationUrl }}">
                                    {{ Lang::txt('MOD_MYCURATION_REVIEW') }}
                                </a>
                            </span>
                        @endif
                        @if ($row->state == 7)
                            <span class="badge badge-sm badge-warning">{{ Lang::txt('MOD_MYCURATION_PENDING_CHANGES') }}</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
