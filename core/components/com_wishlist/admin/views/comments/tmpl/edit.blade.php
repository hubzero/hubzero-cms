{{--
  Wishlist Comment — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $canDo = \Components\Wishlist\Helpers\Permissions::getActions('list');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WISHLIST') }}: {{ Lang::txt('COM_WISHLIST_COMMENT') }}: {{ $row->id ? Lang::txt('COM_WISHLIST_EDIT') : Lang::txt('COM_WISHLIST_NEW') }}"
    icon="wishlist"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Left column — Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_WISHLIST_DETAILS') }}">
    <div class="admin-field">
      <label class="label" for="field-content">
        {{ Lang::txt('COM_WISHLIST_COMMENT') }}
        <span class="text-error">*</span>
      </label>
      @php
        $contentVal = e(
            preg_replace(
                '/^(<!-- \{FORMAT:.*\} -->)/i',
                '',
                $row->content ?? ''
            )
        );
      @endphp
      {!! $__view->editor(
          'fields[content]',
          $contentVal,
          50,
          30,
          'field-content',
          ['class' => 'required minimal no-footer', 'buttons' => false]
      ) !!}
    </div>
  </x-admin-fieldset>

  {{-- Right column — Sidebar --}}
  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
      <table class="admin-meta">
        <tbody>
          <tr>
            <td>{{ Lang::txt('COM_WISHLIST_REFERENCEID') }}</td>
            <td>{{ $row->item_id }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_WISHLIST_FIELD_CATEGORY') }}</td>
            <td>{{ $row->item_type }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_WISHLIST_FIELD_ID') }}</td>
            <td>{{ $row->id }}</td>
          </tr>
          @if ($row->created)
            <tr>
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_CREATED') }}</td>
              <td>
                <time datetime="{{ $row->created }}">
                  {{ Date::of($row->created)->toLocal() }}
                </time>
              </td>
            </tr>
            <tr>
              @php $creator = User::getInstance($row->created_by); @endphp
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_CREATOR') }}</td>
              <td>
                {{ $creator ? $creator->get('name') : Lang::txt('unknown') }}
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </x-admin-fieldset>

    <x-admin-fieldset legend="{{ Lang::txt('COM_WISHLIST_PARAMETERS') }}">
      <div class="flex items-center gap-2">
        <input type="checkbox"
               class="checkbox checkbox-sm"
               name="fields[anonymous]"
               id="field-anonymous"
               value="1"
               @checked($row->anonymous) />
        <label for="field-anonymous">{{ Lang::txt('JANONYMOUS') }}</label>
      </div>

      <div class="admin-field">
        <label class="label" for="field-state">
          {{ Lang::txt('COM_WISHLIST_STATUS') }}
        </label>
        <select name="fields[state]"
                id="field-state"
                class="select select-bordered w-full">
          @php $st = $row->state; @endphp
          <option value="0" @selected($st == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
          <option value="1" @selected($st == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
          <option value="2" @selected($st == 2)>{{ Lang::txt('JTRASHED') }}</option>
        </select>
      </div>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
  <input type="hidden" name="fields[item_id]" value="{{ $row->item_id }}" />
  <input type="hidden" name="fields[item_type]" value="{{ $row->item_type }}" />
  <input type="hidden" name="fields[created]" value="{{ $row->created }}" />
  <input type="hidden" name="fields[created_by]" value="{{ $row->created_by }}" />
  <input type="hidden" name="wish" value="{{ $wish }}" />
</x-admin-edit>
