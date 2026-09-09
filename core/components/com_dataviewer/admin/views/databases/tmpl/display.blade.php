@php
/**
 * Database list view (admin).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

<table class="adminlist">
    <thead>
        <tr>
            <th width="50px">#</th>
            <th width="40%">{{ Lang::txt('COM_DATAVIEWER_NAME') }}</th>
            <th>{{ Lang::txt('COM_DATAVIEWER_CONFIG') }}</th>
            <th width="30%">{{ Lang::txt('COM_DATAVIEWER_DATA_VIEWS') }}</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($databases as $i => $db)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $db['name'] }}</td>
            <td>
                <a href="/administrator/index.php?option=com_dataviewer&controller=databases&task=config&db={{ urlencode($db['id']) }}">
                    {{ Lang::txt('COM_DATAVIEWER_EDIT_CONFIG') }}
                </a>
            </td>
            <td>
                <a href="/administrator/index.php?option=com_dataviewer&controller=dataviews&db={{ urlencode($db['id']) }}" target="_blank">
                    {{ Lang::txt('COM_DATAVIEWER_DATAVIEWS') }}
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
