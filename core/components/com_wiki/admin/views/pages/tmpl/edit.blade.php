{{--
  Wiki Page — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $canDo = \Components\Wiki\Helpers\Permissions::getActions('page');
  $text  = $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  $authors = [];
  foreach ($row->authors()->rows() as $author) {
      $authors[] = $author->user()->get('username');
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WIKI') }}: {{ Lang::txt('COM_WIKI_PAGE') }}: {{ $text }}"
    icon="wiki"
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
        <label for="pagetitle" class="label">
          {{ Lang::txt('COM_WIKI_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="page[title]"
               id="pagetitle"
               class="input input-bordered w-full"
               required
               maxlength="255"
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="pagename" class="label">
          {{ Lang::txt('COM_WIKI_FIELD_PAGENAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="page[pagename]"
               id="pagename"
               class="input input-bordered w-full"
               required
               maxlength="255"
               value="{{ $row->get('pagename', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_WIKI_FIELD_PAGENAME_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-path" class="label">
          {{ Lang::txt('COM_WIKI_FIELD_PATH') }}
        </label>
        <input type="text"
               name="page[path]"
               id="field-path"
               class="input input-bordered w-full"
               maxlength="255"
               value="{{ $row->get('path', '') }}" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-scope" class="label">
            {{ Lang::txt('COM_WIKI_FIELD_SCOPE') }}
          </label>
          <input type="text"
                 name="page[scope]"
                 id="field-scope"
                 class="input input-bordered w-full"
                 maxlength="255"
                 value="{{ $row->get('scope', '') }}" />
        </div>
        <div class="admin-field">
          <label for="field-scope_id" class="label">
            {{ Lang::txt('COM_WIKI_FIELD_SCOPE_ID') }}
          </label>
          <input type="text"
                 name="page[scope_id]"
                 id="field-scope_id"
                 class="input input-bordered w-full"
                 maxlength="255"
                 value="{{ $row->get('scope_id', '') }}" />
        </div>
      </div>

      <div class="admin-field">
        <label for="pageauthors" class="label">
          {{ Lang::txt('COM_WIKI_FIELD_AUTHORS') }}
        </label>
        <textarea name="page[authors]"
                  id="pageauthors"
                  class="textarea textarea-bordered w-full"
                  rows="3">{{ implode(', ', $authors) }}</textarea>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_WIKI_FIELD_AUTHORS_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-tags" class="label">
          {{ Lang::txt('COM_WIKI_FIELD_TAGS') }}
        </label>
        <textarea name="page[tags]"
                  id="field-tags"
                  class="textarea textarea-bordered w-full"
                  rows="3">{{ $row->tags('string') }}</textarea>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_WIKI_FIELD_TAGS_HINT') }}
        </p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_ID') }}</td>
              <td>{{ $row->get('id') ?: Lang::txt('JNEW') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_CREATED') }}</td>
              <td>{{ $row->created('time') . ' ' . $row->created('date') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_CREATOR') }}</td>
              <td>{{ $row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')) }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_HITS') }}</td>
              <td>{{ $row->get('hits') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WIKI_FIELD_REVISIONS') }}</td>
              <td>{{ $row->versions()->total() }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Parameters --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_WIKI_FIELDSET_PARAMETERS') }}">

        <div class="admin-field">
          <label for="param-mode" class="label">
            {{ Lang::txt('COM_WIKI_MODE_LABEL') }}
          </label>
          <select name="params[mode]"
                  id="param-mode"
                  class="select select-bordered w-full">
            <option value="wiki" @selected($row->param('mode') == 'wiki')>
              {{ Lang::txt('COM_WIKI_MODE_WIKI') }}
            </option>
            <option value="knol" @selected($row->param('mode') == 'knol')>
              {{ Lang::txt('COM_WIKI_MODE_KNOL') }}
            </option>
            <option value="static" @selected($row->param('mode') == 'static')>
              {{ Lang::txt('COM_WIKI_MODE_STATIC') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="params[hide_authors]"
                   class="checkbox"
                   value="1"
                   @checked($row->param('hide_authors') == 1) />
            <span>{{ Lang::txt('COM_WIKI_FIELD_HIDE_AUTHORS') }}</span>
          </label>
        </div>

        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="params[allow_changes]"
                   class="checkbox"
                   value="1"
                   @checked($row->param('allow_changes') == 1) />
            <span>{!! Lang::txt('COM_WIKI_FIELD_ALLOW_CHANGES') !!}</span>
          </label>
        </div>

        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="params[allow_comments]"
                   class="checkbox"
                   value="1"
                   @checked($row->param('allow_comments') == 1) />
            <span>{{ Lang::txt('COM_WIKI_FIELD_ALLOW_COMMENTS') }}</span>
          </label>
        </div>

        <div class="admin-field">
          <label for="field-state" class="label">
            {{ Lang::txt('COM_WIKI_FIELD_STATE') }}
          </label>
          <select name="page[state]"
                  id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>
              {{ Lang::txt('COM_WIKI_STATE_OPEN') }}
            </option>
            <option value="1" @selected($row->get('state') == 1)>
              {{ Lang::txt('COM_WIKI_STATE_LOCKED') }}
            </option>
            <option value="2" @selected($row->get('state') == 2)>
              {{ Lang::txt('COM_WIKI_STATE_TRASHED') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-access" class="label">
            {{ Lang::txt('COM_WIKI_FIELD_ACCESS') }}
          </label>
          <select name="page[access]"
                  id="field-access"
                  class="select select-bordered w-full">
            {!! Html::select(
                'options',
                Html::access('assetgroups'),
                'value',
                'text',
                $row->get('access')
            ) !!}
          </select>
        </div>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="id" value="{{ $row->get('id') }}" />
  <input type="hidden" name="page[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
