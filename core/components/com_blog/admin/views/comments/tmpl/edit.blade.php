{{--
  Blog Comment — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\User;

  $canDo = \Components\Blog\Admin\Helpers\Permissions::getActions('entry');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_BLOG_TITLE') }}: {{ Lang::txt('COM_BLOG_COL_COMMENTS') }}: {{ $text }}"
    icon="blog"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label class="label cursor-pointer justify-start gap-2">
          <input type="checkbox"
                 name="fields[anonymous]"
                 class="checkbox checkbox-sm"
                 value="1"
                 @checked($row->get('anonymous')) />
          <span>{{ Lang::txt('COM_BLOG_FIELD_ANONYMOUS') }}</span>
        </label>
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_BLOG_FIELD_ANONYMOUS_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="field-content" class="label">
          {{ Lang::txt('COM_BLOG_FIELD_CONTENT') }}
          <span class="text-error">*</span>
        </label>
        {!! $__view->editor(
            'fields[content]',
            e($row->get('content')),
            50,
            15,
            'field-content',
            ['class' => 'required minimal no-footer', 'buttons' => false]
        ) !!}
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Metadata table --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_BLOG_FIELD_ID') }}</td>
              <td>
                {{ $row->get('id', 0) }}
                <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
                <input type="hidden" name="id" value="{{ $row->get('id') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_BLOG_FIELD_CREATOR') }}</td>
              <td>
                @php $editor = User::getInstance($row->get('created_by')); @endphp
                {{ $editor->get('name') }}
                <input type="hidden"
                       name="fields[created_by]"
                       value="{{ $row->get('created_by') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_BLOG_FIELD_CREATED') }}</td>
              <td>
                {{ Date::of($row->get('created'))->toLocal() }}
                <input type="hidden"
                       name="fields[created]"
                       value="{{ $row->get('created') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_BLOG_FIELD_ENTRY') }}</td>
              <td>
                {{ $row->get('entry_id') }}
                <input type="hidden"
                       name="fields[entry_id]"
                       value="{{ $row->get('entry_id') }}" />
                <input type="hidden"
                       name="entry_id"
                       value="{{ $row->get('entry_id') }}" />
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">
        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_BLOG_FIELD_STATE') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
            <option value="3" @selected($row->get('state') == 3)>{{ Lang::txt('COM_BLOG_FIELD_STATE_FLAGGED') }}</option>
          </select>
      </x-admin-fieldset>
    </div>
  @endslot

  {{-- Preserve parent comment --}}
  <input type="hidden" name="fields[parent]" value="{{ $row->get('parent') }}" />
</x-admin-edit>
