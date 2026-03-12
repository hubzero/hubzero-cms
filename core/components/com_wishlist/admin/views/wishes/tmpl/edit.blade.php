{{--
  Wishlist Wish — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $canDo = \Components\Wishlist\Helpers\Permissions::getActions('list');

  // Build owner/assignee JSON data for JS
  $data = [];
  if ($ownerassignees) {
      foreach ($ownerassignees as $k => $items) {
          foreach ($items as $v) {
              $data[] = [$k, $v->id, $v->name];
          }
      }
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WISHLIST') }}: {{ Lang::txt('COM_WISHLIST_WISH') }}: {{ $row->get('id') ? Lang::txt('COM_WISHLIST_EDIT') : Lang::txt('COM_WISHLIST_NEW') }}"
    icon="wishlist"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<template id="owner-data">
    {
        "data": {!! json_encode($data) !!}
    }
</template>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Left column — Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_WISHLIST_DETAILS') }}">
    <div class="admin-field">
      <label class="label" for="field-wishlist">
        {{ Lang::txt('COM_WISHLIST_CATEGORY') }}
        <span class="text-error">*</span>
      </label>
      <select name="fields[wishlist]"
              id="field-wishlist"
              class="select select-bordered w-full"
              required>
        <option value="0">{{ Lang::txt('COM_WISHLIST_NONE') }}</option>
        @if ($lists)
          @foreach ($lists as $list)
            <option value="{{ $list->id }}"
                    @selected($row->get('wishlist') == $list->id)>
              {{ $list->get('title') }}
            </option>
          @endforeach
        @endif
      </select>
    </div>

    <div class="admin-field">
      <label class="label" for="field-subject">
        {{ Lang::txt('COM_WISHLIST_TITLE') }}
        <span class="text-error">*</span>
      </label>
      <input type="text"
             name="fields[subject]"
             id="field-subject"
             class="input input-bordered w-full"
             maxlength="150"
             required
             value="{{ $row->get('subject', '') }}" />
    </div>

    <div class="admin-field">
      <label class="label" for="field-about">
        {{ Lang::txt('COM_WISHLIST_DESCRIPTION') }}
      </label>
      @php
        $aboutVal = e(
            preg_replace(
                '/^(<!-- \{FORMAT:.*\} -->)/i',
                '',
                $row->get('about', '')
            )
        );
      @endphp
      {!! $__view->editor(
          'fields[about]',
          $aboutVal,
          50,
          30,
          'field-about',
          ['class' => 'minimal no-footer', 'buttons' => false]
      ) !!}
    </div>

    <div class="admin-field">
      <label class="label" for="field-tags">
        {{ Lang::txt('COM_WISHLIST_TAGS') }}
      </label>
      <input type="text"
             name="fields[tags]"
             id="field-tags"
             class="input input-bordered w-full"
             maxlength="150"
             value="{{ $row->tags('string') }}" />
    </div>
  </x-admin-fieldset>

  {{-- Plan --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_WISHLIST_PLAN') }}">
    @if ($row->plan->get('id'))
      <div class="flex items-center gap-2">
        <input type="checkbox"
               class="checkbox checkbox-sm"
               name="plan[create_revision]"
               id="plan-create_revision"
               value="1" />
        <label for="plan-create_revision">
          {{ Lang::txt('COM_WISHLIST_PLAN_NEW_REVISION') }}
        </label>
      </div>
    @endif

    <fieldset class="border border-base-300 rounded p-4">
      <legend class="px-2 text-sm font-semibold">
        {{ Lang::txt('COM_WISHLIST_DUE') }}
      </legend>
      @php
        $due   = $row->get('due');
        $noDue = (!$due || $due == '0000-00-00 00:00:00');
      @endphp
      <div class="flex flex-col gap-2">
        <label class="flex items-center gap-2">
          <input type="radio"
                 class="radio radio-sm"
                 name="fields[due]"
                 value="0"
                 @checked($noDue) />
          {{ Lang::txt('COM_WISHLIST_DUE_NEVER') }}
        </label>
        <div class="flex items-center gap-2">
          <input type="radio"
                 class="radio radio-sm"
                 name="fields[due]"
                 id="field-due-on"
                 value="0"
                 @checked(!$noDue) />
          <label for="field-due-on">{{ Lang::txt('COM_WISHLIST_DUE_ON') }}</label>
          <input type="text"
                 name="fields[due]"
                 id="field-due"
                 class="input input-bordered input-sm"
                 size="10"
                 maxlength="19"
                 aria-label="{{ Lang::txt('COM_WISHLIST_DUE') }}"
                 value="{{ $row->get('due') }}" />
        </div>
      </div>
    </fieldset>

    <div class="admin-field">
      <label class="label" for="fieldassigned">
        {{ Lang::txt('COM_WISHLIST_ASSIGNED') }}
      </label>
      <select name="fields[assigned]"
              id="fieldassigned"
              class="select select-bordered w-full">
        <option value="0">{{ Lang::txt('COM_WISHLIST_UNASSIGNED') }}</option>
        @if ($assignees)
          @foreach ($assignees as $assignee)
            <option value="{{ $assignee->id }}"
                    @selected($row->get('assigned') == $assignee->id)>
              {{ $assignee->name }}
            </option>
          @endforeach
        @endif
      </select>
    </div>

    <div class="admin-field">
      <label class="label" for="plan-pagetext">
        {{ Lang::txt('COM_WISHLIST_PAGETEXT') }}
      </label>
      @php
        $planVal = e(
            preg_replace(
                '/^(<!-- \{FORMAT:.*\} -->)/i',
                '',
                $row->plan->pagetext ?? ''
            )
        );
      @endphp
      {!! $__view->editor(
          'plan[pagetext]',
          $planVal,
          50,
          30,
          'plan-pagetext',
          ['class' => 'minimal no-footer', 'buttons' => false]
      ) !!}
    </div>

    <input type="hidden" name="plan[id]" value="{{ $row->plan->id ?? 0 }}" />
    <input type="hidden" name="plan[wishid]" value="{{ $row->id ?? 0 }}" />
    <input type="hidden" name="plan[version]" value="{{ $row->plan->version ?? 0 }}" />
    <input type="hidden" name="plan[approved]" value="{{ $row->plan->approved ?? 0 }}" />
    @if (!$row->plan->get('id'))
      <input type="hidden" name="plan[create_revision]" value="0" />
    @endif
  </x-admin-fieldset>

  {{-- Right column — Sidebar --}}
  @slot('sidebar')
    @if ($row->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_ID') }}</td>
              <td>{{ $row->get('id') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_CREATED') }}</td>
              <td>
                <time datetime="{{ $row->get('proposed') }}">
                  {{ Date::of($row->get('proposed'))->toLocal() }}
                </time>
              </td>
            </tr>
            <tr>
              @php $creator = User::getInstance($row->get('proposed_by')); @endphp
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_CREATOR') }}</td>
              <td>{{ $creator->get('name', Lang::txt('COM_WISHLIST_UNKNOWN')) }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_RANKING') }}</td>
              <td>{{ $row->get('ranking') }}</td>
            </tr>
          </tbody>
        </table>
      </x-admin-fieldset>
    @endif

    <x-admin-fieldset legend="{{ Lang::txt('COM_WISHLIST_PARAMETERS') }}">
      <div class="flex items-center gap-2">
        <input type="checkbox"
               class="checkbox checkbox-sm"
               name="fields[anonymous]"
               id="field-anonymous"
               value="1"
               @checked($row->get('anonymous')) />
        <label for="field-anonymous">{{ Lang::txt('JANONYMOUS') }}</label>
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox"
               class="checkbox checkbox-sm"
               name="fields[private]"
               id="field-private"
               value="1"
               @checked($row->get('private')) />
        <label for="field-private">{{ Lang::txt('COM_WISHLIST_PRIVATE') }}</label>
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox"
               class="checkbox checkbox-sm"
               name="fields[accepted]"
               id="field-accepted"
               value="1"
               @checked($row->get('accepted')) />
        <label for="field-accepted">{{ Lang::txt('COM_WISHLIST_ACCEPTED') }}</label>
      </div>

      <div class="admin-field">
        <label class="label" for="field-points">
          {{ Lang::txt('COM_WISHLIST_POINTS') }}
        </label>
        <input type="text"
               name="fields[points]"
               id="field-points"
               class="input input-bordered w-full"
               value="{{ $row->get('points') }}" />
      </div>

      <div class="admin-field">
        <label class="label" for="field-status">
          {{ Lang::txt('COM_WISHLIST_STATUS') }}
        </label>
        <select name="fields[status]"
                id="field-status"
                class="select select-bordered w-full">
          @php $st = $row->get('status'); @endphp
          <option value="0" @selected($st == 0)>
            {{ Lang::txt('COM_WISHLIST_STATUS_PENDING') }}
          </option>
          <option value="1" @selected($st == 1)>
            {{ Lang::txt('COM_WISHLIST_STATUS_GRANTED') }}
          </option>
          <option value="2" @selected($st == 2)>
            {{ Lang::txt('COM_WISHLIST_STATUS_DELETED') }}
          </option>
          <option value="3" @selected($st == 3)>
            {{ Lang::txt('COM_WISHLIST_STATUS_REJECTED') }}
          </option>
          <option value="4" @selected($st == 4)>
            {{ Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN') }}
          </option>
        </select>
      </div>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[proposed]" value="{{ $row->get('proposed') }}" />
  <input type="hidden" name="fields[proposed_by]" value="{{ $row->get('proposed_by') }}" />
  <input type="hidden" name="fields[ranking]" value="{{ $row->get('ranking') }}" />
  <input type="hidden" name="wishlist" value="{{ $wishlist }}" />
</x-admin-edit>
