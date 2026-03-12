{{--
  Blog Entry — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\User;

  $canDo = \Components\Blog\Admin\Helpers\Permissions::getActions('entry');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_BLOG_TITLE') }}: {{ $text }}"
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

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-scope" class="label">{{ Lang::txt('COM_BLOG_FIELD_SCOPE') }}</label>
          @if($row->isNew() || User::authorise('core.admin', $option))
            {!! \Components\Blog\Admin\Helpers\Html::scopes(
                $row->get('scope'),
                'fields[scope]',
                'field-scope',
                'class="select select-bordered w-full"'
            ) !!}
          @else
            <input type="text"
                   name="fields[scope]"
                   id="field-scope"
                   class="input input-bordered w-full"
                   disabled
                   value="{{ $row->get('scope') }}" />
          @endif
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_BLOG_FIELD_SCOPE_HINT') }}</p>
        </div>

        <div class="admin-field">
          <label for="field-scope_id" class="label">{{ Lang::txt('COM_BLOG_FIELD_SCOPE_ID') }}</label>
          <input type="text"
                 name="fields[scope_id]"
                 id="field-scope_id"
                 class="input input-bordered w-full"
                 @if(!$row->isNew() && !User::authorise('core.admin', $option)) disabled @endif
                 value="{{ $row->get('scope_id', '') }}" />
        </div>
      </div>

      @if(!$row->isNew() && User::authorise('core.admin', $option))
        <div class="alert alert-warning text-sm">
          {{ Lang::txt('COM_BLOG_FIELD_SCOPE_WARNING') }}
        </div>
      @endif

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_BLOG_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="250"
               required
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">{{ Lang::txt('COM_BLOG_FIELD_ALIAS') }}</label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               maxlength="250"
               value="{{ $row->get('alias', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_BLOG_FIELD_ALIAS_HINT') }}</p>
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
            30,
            'field-content',
            ['class' => 'required', 'buttons' => false]
        ) !!}
      </div>

      <div class="admin-field">
        <label for="field-tags" class="label">{{ Lang::txt('COM_BLOG_FIELD_TAGS') }}</label>
        @php
          $tf = Event::trigger(
              'hubzero.onGetMultiEntry',
              [['tags', 'tags', 'field-tags', '', $row->tags('string')]]
          );
        @endphp
        @if(count($tf) > 0)
          {!! $tf[0] !!}
        @else
          <textarea name="tags"
                    id="field-tags"
                    class="textarea textarea-bordered w-full"
                    rows="3">{{ $row->tags('string') }}</textarea>
        @endif
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_BLOG_FIELD_TAGS_HINT') }}</p>
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
              <td>{{ Lang::txt('COM_BLOG_FIELD_HITS') }}</td>
              <td>
                {{ $row->get('hits') }}
                <input type="hidden"
                       name="fields[hits]"
                       value="{{ $row->get('hits') }}" />
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">

        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="fields[allow_comments]"
                   class="checkbox checkbox-sm"
                   value="1"
                   @checked($row->get('allow_comments')) />
            <span>{{ Lang::txt('COM_BLOG_FIELD_ALLOW_COMMENTS') }}</span>
          </label>
        </div>

        <div class="admin-field">
          <label for="field-access" class="label">{{ Lang::txt('COM_BLOG_FIELD_ACCESS_LEVEL') }}</label>
          <select name="fields[access]" id="field-access"
                  class="select select-bordered w-full">
            {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $row->get('access')) !!}
          </select>
        </div>

        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_BLOG_FIELD_STATE') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-publish_up" class="label">{{ Lang::txt('COM_BLOG_FIELD_PUBLISH_UP') }}</label>
          @php
            $publishUpVal = '';
            if ($row->get('publish_up') && $row->get('publish_up') != '0000-00-00 00:00:00') {
                $publishUpVal = Date::of($row->get('publish_up'))->toLocal('Y-m-d H:i:s');
            }
          @endphp
          {!! Html::input('calendar', 'fields[publish_up]', e($publishUpVal), ['id' => 'field-publish_up']) !!}
        </div>

        <div class="admin-field">
          <label for="field-publish_down" class="label">{{ Lang::txt('COM_BLOG_FIELD_PUBLISH_DOWN') }}</label>
          @php
            $publishDownVal = '';
            if ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00') {
                $publishDownVal = Date::of($row->get('publish_down'))->toLocal('Y-m-d H:i:s');
            }
          @endphp
          {!! Html::input('calendar', 'fields[publish_down]', e($publishDownVal), ['id' => 'field-publish_down']) !!}
        </div>

    </x-admin-fieldset>
  @endslot
</x-admin-edit>
