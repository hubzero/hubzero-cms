@php
/**
 * Column customizer panel partial (Blade / daisyUI).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

<div id="dv_customizer_panel"
    class="hidden clear-both p-2 mt-0 bg-base-200 rounded-b-lg dv_top_pannel">
    <div id="dv_customizer_content" class="h-full w-auto border border-base-300 rounded-lg bg-base-100">
        <div id="dv_customizer_group_by" title="{{ Lang::txt('COM_DATAVIEWER_CUSTOMIZER_GROUP_ROWS') }}"
            class="hidden w-[500px] h-[350px]">
            <p>{{ Lang::txt('COM_DATAVIEWER_CUSTOMIZER_GROUP_DESC') }}</p>
            <div id="dv_customizer_group_by_list">{!! $group_by !!}</div>
        </div>
        <p id="dv_customizer_title_container" class="pl-2">
            {{ Lang::txt('COM_DATAVIEWER_NEW_TITLE') }} <input id="dv_customizer_view_title"
                value="{{ $dd['title'] }}" type="text"
                class="input input-bordered input-sm w-[550px]" />
            &nbsp;&nbsp;
            <input id="dv_customizer_group_by_btn"
                data-view-url="{{ $settings['view_url'] }}"
                value="{{ Lang::txt('COM_DATAVIEWER_GROUP_BY') }}" type="button"
                class="hidden btn btn-xs" />
            &nbsp;&nbsp;
            <input id="dv_customizer_launch_view_btn"
                data-view-url="{{ $settings['view_url'] }}"
                value="{{ Lang::txt('COM_DATAVIEWER_LAUNCH_CUSTOM_VIEW') }}" type="button"
                class="hidden btn btn-xs btn-primary" />
        </p>
        <table class="border-0">
            <tr id="dv_customizer_lists_top">
                <td class="w-[400px]">{{ Lang::txt('COM_DATAVIEWER_FULL_COLUMNS_LIST') }}</td>
                <td class="w-[400px]">{{ Lang::txt('COM_DATAVIEWER_CUSTOM_LIST') }}</td>
            </tr>
            <tr>
                <td>
                    <div class="dv_customizer_lists overflow-auto">
                    <ul id="dv_customizer_full_list" class="dv_customizer_col_lists">
                        {!! $full_list !!}
                    </ul>
                    </div>
                </td>
                <td>
                    <div class="dv_customizer_lists overflow-auto">
                    <ul id="dv_customizer_selected" class="dv_customizer_col_lists">
                        {!! $selected_list !!}
                    </ul>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
