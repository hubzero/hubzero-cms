{{--
  Support — ACL ARO row partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $acos = [
      'tickets'          => ['read', 'update', 'delete'],
      'comments'         => ['create', 'read'],
      'private_comments' => ['create', 'read'],
  ];
@endphp

<tr>
    <td class="text-center">
        <input
            type="checkbox"
            name="id[]"
            id="cb{{ $i }}"
            value="{{ $row->id }}"
            aria-label="{{ $row->alias }} ({{ $row->foreign_key }})"
            class="checkbox checkbox-sm checkbox-toggle"
            data-check-item="id[]"
        />
    </td>
    <td class="text-center text-sm text-muted-foreground">
        {{ $row->id }}
    </td>
    <td class="font-medium">
        {{ $row->alias }} ({{ $row->foreign_key }})
    </td>
    <td class="text-sm">
        {{ $row->model }}
    </td>

    @foreach ($acos as $aco => $actionList)
        @foreach ($actionList as $action)
            @include('com_support::admin/views/acl/tmpl/_acl_aro_row_toggle_field', [
                'id'        => $data[$aco]['id'],
                'isEnabled' => $data[$aco][$action],
                'action'    => $action,
            ])
        @endforeach
    @endforeach
</tr>
