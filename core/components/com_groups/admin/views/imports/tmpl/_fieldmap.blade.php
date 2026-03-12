{{-- /**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */ --}}
@php
use Hubzero\Facades\Lang;
@endphp

@if ($import->get('id'))
    @php
    $columns = [
        'gidNumber',
        'cn',
        'description',
        'published',
        'approved',
        'restrict_msg',
        'join_policy',
        'discoverability',
        'discussion_email_autosubscribe',
        'plugins',
        'created',
        'created_by',
        'members',
        'managers',
        'tags',
    ];

    $customFields = \Components\Groups\Models\Orm\Field::all()->ordered()->rows();
    @endphp

    <x-admin-fieldset legend="{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_MAPPING') }}">

        <table class="field-map">
            <thead>
                <tr>
                    <th>{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_COL_FIELD_COLUMN') }}</th>
                    <th>{{ Lang::txt('COM_GROUPS_IMPORT_EDIT_COL_FIELD_MEMBER') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($import->fields() as $mapping)
                    @php $mName = $mapping['name']; @endphp
                    <tr{{ !$mapping['field'] ? ' class="field-unknown"' : '' }}>
                        <td>
                            <label class="label text-base-content" for="mapping-{{ $mName }}">
                                {{ $mapping['label'] }}
                            </label>
                        </td>
                        <td>
                            <input type="hidden"
                                name="mapping[{{ $mName }}][name]"
                                value="{{ $mName }}" />
                            <input type="hidden"
                                name="mapping[{{ $mName }}][label]"
                                value="{{ $mapping['label'] }}" />
                            <select
                                name="mapping[{{ $mName }}][field]"
                                id="mapping-{{ $mName }}"
                                class="select select-bordered w-full">
                                <option value="">{{ Lang::txt('COM_GROUPS_UNKNOWN') }}</option>
                                <optgroup label="{{ Lang::txt('COM_GROUPS_IMPORT_FIELDS_DETAILS') }}">
                                    @foreach ($columns as $column)
                                        <option
                                            value="{{ $column }}"
                                            {{ $mapping['field'] == $column ? 'selected="selected"' : '' }}>
                                            {{ $column }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ Lang::txt('COM_GROUPS_IMPORT_FIELDS_DESCRIPTION') }}">
                                    @foreach ($customFields as $field)
                                        @php $fn = $field->get('name'); @endphp
                                        <option
                                            value="{{ $fn }}"
                                            {{ $mapping['field'] == $field->get('name') ? 'selected="selected"' : '' }}>
                                            {{ $fn }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin-fieldset>
@endif
