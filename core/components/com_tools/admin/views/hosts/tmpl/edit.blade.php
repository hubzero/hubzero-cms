{{--
  Tool Host — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOSTS') . ': ' . $text, 'tools');
  Toolbar::apply();
  Toolbar::save();
  Toolbar::spacer();
  Toolbar::cancel();
@endphp

@if($__view->getError())
  <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
@endif

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

    <div class="admin-field">
      <label for="field-hostname" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_NAME') }}</label>
      <input type="text"
             name="fields[hostname]"
             id="field-hostname"
             class="input input-bordered w-full"
             maxlength="255"
             value="{{ $row->hostname ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-service_host" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_SERVICE_HOST') }}</label>
      <input type="text"
             name="fields[service_host]"
             id="field-service_host"
             class="input input-bordered w-full"
             maxlength="255"
             value="{{ $row->service_host ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-hosttype" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_TYPES') }}</label>
      <div class="flex flex-wrap gap-2">
        @foreach($hosttypes as $ht)
          @php $active = (int)$ht->value & (int)($row->provisions ?? 0); @endphp
          <label class="flex items-center gap-1 cursor-pointer">
            <input type="checkbox"
                   name="hosttype[{{ $ht->name }}]"
                   value="{{ $ht->name }}"
                   class="checkbox checkbox-sm"
                   @checked($active) />
            <span class="text-sm">{{ $ht->name }}</span>
          </label>
        @endforeach
      </div>
    </div>

    <div class="admin-field">
      <label for="field-zone_id" class="label text-base-content">{{ Lang::txt('COM_TOOLS_FIELD_ZONE') }}</label>
      <select name="fields[zone_id]" id="field-zone_id" class="select select-bordered w-full">
        <option value="0">{{ Lang::txt('COM_TOOLS_SELECT') }}</option>
        @foreach($zones as $zone)
          <option value="{{ $zone->id }}" @selected($zone->id == ($row->zone_id ?? 0))>
            {{ $zone->zone ?? '' }}
          </option>
        @endforeach
      </select>
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_FIELD_STATUS') }}</td>
          <td>
            @if($row->status)
              <span class="badge badge-sm {{ $row->status === 'up' ? 'badge-success' : 'badge-error' }}">
                {{ $row->status }}
              </span>
            @else
              <span class="text-muted-foreground">—</span>
            @endif
          </td>
        </tr>
      </tbody>
    </table>

    @if(!empty($toolCounts) && count($toolCounts) > 0)
      <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_FIELDSET_SESSIONS') }}">
        <table class="admin-meta w-full">
          <tbody>
            @foreach($toolCounts as $c)
              <tr>
                <td class="font-mono text-sm">{{ $c->appname }}</td>
                <td class="text-right font-medium">{{ $c->count }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </x-admin-fieldset>
    @endif

    @if(!empty($statusCounts) && count($statusCounts) > 0)
      <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_FIELDSET_CONTAINERS') }}">
        <table class="admin-meta w-full">
          <tbody>
            @foreach($statusCounts as $c)
              <tr>
                <td class="text-sm">{{ $c->status }}</td>
                <td class="text-right font-medium">{{ $c->count }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </x-admin-fieldset>
    @endif
  @endslot

  <input type="hidden" name="fields[status]" value="{{ $row->status ?: 'check' }}" />
  <input type="hidden" name="fields[id]" value="{{ $row->hostname ?? '' }}" />
</x-admin-edit>
