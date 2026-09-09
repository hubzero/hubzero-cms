@php
/**
 * DataTables table partial (Blade / daisyUI).
 *
 * Renders the main spreadsheet table with tfoot column filters.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

<div id="dv-spreadsheet-container" class="m-0 p-0" style="{{ $hide_str }}">
<table id="dv-spreadsheet-tbl" class="mt-0">
    <thead></thead>
    <tfoot>
        <tr>
        @foreach ($dd['cols'] as $id => $conf)
            @if (!isset($conf['hide']))
                @php
                $label = isset($conf['label']) ? $conf['label'] : $id;
                $label = str_replace('<br />', ' ', $label);
                $label = html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8');

                $filter_msg = '';
                if ($settings['serverside']) {
                    $filter_msg = "  \n\n" . Lang::txt('COM_DATAVIEWER_FILTER_HINT_SERVERSIDE');
                } else {
                    $filter_msg = "\t\n\n" . Lang::txt('COM_DATAVIEWER_FILTER_HINT_CLIENTSIDE');
                }

                if (isset($conf['filter_hint'])) {
                    $filter_hint = $conf['filter_hint'];
                } elseif (
                    $d_arr['field_types'][$id] == 'number'
                    || $d_arr['field_types'][$id] == 'numrange'
                ) {
                    $filter_hint = Lang::txt('COM_DATAVIEWER_FILTER_HINT_NUMBER');
                } elseif ($d_arr['field_types'][$id] == 'datetime') {
                    $filter_hint = Lang::txt('COM_DATAVIEWER_FILTER_HINT_DATETIME');
                } else {
                    $filter_hint = Lang::txt('COM_DATAVIEWER_FILTER_HINT_STRING');
                }

                $title = $filter_hint . $filter_msg;
                @endphp

                @if (isset($conf['type']) && $conf['type'] == 'image')
                <th><input title="{{ $title }}" type="text"
                    placeholder="{{ $label }}" disabled="disabled"
                    class="input input-xs input-bordered w-full" /></th>
                @elseif (isset($filtered_view[$id]))
                <th><input type="text"
                    placeholder="{{ $filtered_view[$id] }}"
                    disabled="disabled"
                    class="input input-xs input-bordered w-full bg-warning/20" /></th>
                @else
                <th><input title="{{ $title }}" type="text"
                    placeholder="{{ $label }}"
                    class="search_init input input-xs input-bordered w-full bg-base-100" /><span
                    class="dv-col-clear-filter -ml-4 text-transparent cursor-pointer"><i
                    class="icon-remove-sign"></i></span></th>
                @endif
            @endif
        @endforeach
        </tr>
    </tfoot>
</table>
</div>
