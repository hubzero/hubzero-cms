{{--
  Services — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $canDo = \Components\Services\Helpers\Permissions::getActions('service');
  $text  = $row->id ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SERVICES') }}: {{ Lang::txt('COM_SERVICES_SERVICES') }}: {{ $text }}"
    icon="services"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="field-category" class="label">
          {{ Lang::txt('COM_SERVICES_FIELD_CATEGORY') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[category]"
               id="field-category"
               class="input input-bordered w-full"
               required
               maxlength="250"
               value="{{ $row->category }}" />
      </div>

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_SERVICES_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               required
               maxlength="250"
               value="{{ $row->title }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">
          {{ Lang::txt('COM_SERVICES_FIELD_ALIAS') }}
        </label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               maxlength="250"
               value="{{ $row->alias }}" />
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_SERVICES_FIELD_ALIAS_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">
          {{ Lang::txt('COM_SERVICES_FIELD_DESCRIPTION') }}
        </label>
        <textarea name="fields[description]"
                  id="field-description"
                  class="textarea textarea-bordered w-full"
                  rows="4">{{ $row->description }}</textarea>
      </div>

  </x-admin-fieldset>

  {{-- Units / Pricing --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_SERVICES_UNITS') }}">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-currency" class="label">
            {{ Lang::txt('COM_SERVICES_FIELD_CURRENCY') }}
          </label>
          <input type="text"
                 name="fields[currency]"
                 id="field-currency"
                 class="input input-bordered w-full"
                 maxlength="10"
                 value="{{ $row->currency }}" />
        </div>

        <div class="admin-field">
          <label for="field-unitprice" class="label">
            {{ Lang::txt('COM_SERVICES_FIELD_UNITPRICE') }}
          </label>
          <input type="text"
                 name="fields[unitprice]"
                 id="field-unitprice"
                 class="input input-bordered w-full"
                 value="{{ $row->unitprice }}" />
        </div>
      </div>

      <div class="admin-field">
        <label for="field-pointsprice" class="label">
          {{ Lang::txt('COM_SERVICES_FIELD_POINTSPRICE') }}
        </label>
        <input type="text"
               name="fields[pointsprice]"
               id="field-pointsprice"
               class="input input-bordered w-full"
               maxlength="11"
               value="{{ $row->pointsprice }}" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-minunits" class="label">
            {{ Lang::txt('COM_SERVICES_FIELD_MINUNITS') }}
          </label>
          <input type="text"
                 name="fields[minunits]"
                 id="field-minunits"
                 class="input input-bordered w-full"
                 maxlength="11"
                 value="{{ $row->minunits }}" />
        </div>

        <div class="admin-field">
          <label for="field-maxunits" class="label">
            {{ Lang::txt('COM_SERVICES_FIELD_MAXUNITS') }}
          </label>
          <input type="text"
                 name="fields[maxunits]"
                 id="field-maxunits"
                 class="input input-bordered w-full"
                 value="{{ $row->maxunits }}" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-unitsize" class="label">
            {{ Lang::txt('COM_SERVICES_FIELD_UNITSIZE') }}
          </label>
          <input type="text"
                 name="fields[unitsize]"
                 id="field-unitsize"
                 class="input input-bordered w-full"
                 maxlength="11"
                 value="{{ $row->unitsize }}" />
        </div>

        <div class="admin-field">
          <label for="field-unitmeasure" class="label">
            {{ Lang::txt('COM_SERVICES_FIELD_UNITMEASURE') }}
          </label>
          <input type="text"
                 name="fields[unitmeasure]"
                 id="field-unitmeasure"
                 class="input input-bordered w-full"
                 value="{{ $row->unitmeasure }}" />
        </div>
      </div>

      <div class="admin-field">
        <label for="field-params" class="label">
          {{ Lang::txt('COM_SERVICES_FIELD_PARAMS') }}
        </label>
        <input type="text"
               name="fields[params]"
               id="field-params"
               class="input input-bordered w-full"
               value="{{ $row->params }}" />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_SERVICES_FIELD_ID') }}</td>
              <td>{{ $row->id ?: Lang::txt('JNEW') }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">

        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="fields[restricted]"
                   class="checkbox"
                   value="1"
                   @checked($row->restricted) />
            <span>{{ Lang::txt('COM_SERVICES_FIELD_RESTRICTED') }}</span>
          </label>
        </div>

        <div class="admin-field">
          <label for="field-status" class="label">
            {{ Lang::txt('COM_SERVICES_FIELD_STATUS') }}
          </label>
          <select name="fields[status]"
                  id="field-status"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->status == 0)>
              {{ Lang::txt('JUNPUBLISHED') }}
            </option>
            <option value="1" @selected($row->status == 1)>
              {{ Lang::txt('JPUBLISHED') }}
            </option>
            <option value="2" @selected($row->status == 2)>
              {{ Lang::txt('JTRASHED') }}
            </option>
          </select>
        </div>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
