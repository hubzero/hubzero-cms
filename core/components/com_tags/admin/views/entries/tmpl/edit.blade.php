{{--
  Tags — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $canDo = \Components\Tags\Helpers\Permissions::getActions();
  $text  = $tag->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  $__view->js()
      ->js('api')
      ->js('tagActivityLog')
      ->js('tagLogListItem')
      ->js('tagLogsFetcher');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_TAGS') }}: {{ $text }}"
    icon="tags"
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
        <label for="field-admin" class="label">
          {{ Lang::txt('COM_TAGS_FIELD_TYPE') }}
        </label>
        <select name="fields[admin]"
                id="field-admin"
                class="select select-bordered w-full">
          <option value="0" @selected($tag->get('admin') == 0)>
            {{ Lang::txt('COM_TAGS_FIELD_TYPE_USER') }}
          </option>
          <option value="2" @selected($tag->get('admin') == 2)>
            {{ Lang::txt('COM_TAGS_FIELD_TYPE_CORE') }}
          </option>
          <option value="1" @selected(is_null($tag->get('admin')) || $tag->get('admin') == 1)>
            {{ Lang::txt('COM_TAGS_FIELD_TYPE_ADMIN') }}
          </option>
        </select>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_TAGS_FIELD_ADMIN_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-raw_tag" class="label">
          {{ Lang::txt('COM_TAGS_FIELD_RAW_TAG') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[raw_tag]"
               id="field-raw_tag"
               class="input input-bordered w-full"
               required
               maxlength="250"
               value="{{ $tag->get('raw_tag', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">
          {!! Lang::txt('COM_TAGS_FIELD_TAG_HINT') !!}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-tag" class="label">
          {{ Lang::txt('COM_TAGS_FIELD_TAG') }}
        </label>
        <input type="text"
               name="fields[tag]"
               id="field-tag"
               class="input input-bordered w-full"
               disabled
               maxlength="250"
               placeholder="{{ Lang::txt('COM_TAGS_FIELD_TAG_PLACEHOLDER') }}"
               value="{{ $tag->get('tag') }}" />
      </div>

      <div class="admin-field">
        <label for="field-substitutions" class="label">
          {{ Lang::txt('COM_TAGS_FIELD_ALIAS') }}
        </label>
        <textarea name="fields[substitutions]"
                  id="field-substitutions"
                  class="textarea textarea-bordered w-full"
                  rows="5">{{ $tag->substitutes }}</textarea>
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_TAGS_FIELD_ALIAS_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">
          {{ Lang::txt('COM_TAGS_FIELD_DESCRIPTION') }}
        </label>
        {!! $__view->editor(
            'fields[description]',
            $tag->get('description', ''),
            50,
            4,
            'field-description',
            ['class' => 'minimal', 'buttons' => false]
        ) !!}
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_TAGS_FIELD_ID') }}</td>
              <td>{{ $tag->get('id') ?: Lang::txt('JNEW') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_TAGS_FIELD_CREATOR') }}</td>
              <td>
                @php
                  if (!$tag->get('created_by') && $tag->get('id')) {
                      if ($logs = $tag->logs()->rows()) {
                          foreach ($logs as $logEntry) {
                              if ($logEntry->get('action') == 'tag_created') {
                                  $tag->set('created_by', $logEntry->get('user_id'));
                                  $tag->set('created', $logEntry->get('timestamp'));
                                  break;
                              }
                          }
                      }
                  }
                  $creatorName = $tag->creator->get('name');
                @endphp
                {{ $creatorName ?: Lang::txt('COM_TAGS_UNKNOWN') }}
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_TAGS_FIELD_CREATED') }}</td>
              <td>
                @php $created = $tag->created(); @endphp
                {{ ($created && $created != '0000-00-00 00:00:00')
                    ? $created : Lang::txt('COM_TAGS_UNKNOWN') }}
              </td>
            </tr>
            @if ($tag->get('id') && $tag->wasModified())
              <tr>
                <td>{{ Lang::txt('COM_TAGS_FIELD_MODIFIER') }}</td>
                <td>
                  @php
                    $editorName = Lang::txt('COM_TAGS_UNKNOWN');
                    if ($tag->get('modified_by')) {
                        $editor = User::getInstance($tag->get('modified_by'));
                        $editorName = $editor->get('name');
                    }
                  @endphp
                  {{ $editorName }}
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_TAGS_FIELD_MODIFIED') }}</td>
                <td>
                  @php $modified = $tag->modified(); @endphp
                  {{ ($modified && $modified != '0000-00-00 00:00:00')
                      ? $modified : Lang::txt('COM_TAGS_UNKNOWN') }}
                </td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Activity Log --}}
    @if (!$tag->isNew())
      <x-admin-fieldset legend="{{ Lang::txt('COM_TAGS_LOG') }}">
          <ul class="entry-log" id="entry-log">
            @foreach ($tag->logs()->ordered()->limit(100)->rows() as $log)
              @include('com_tags::admin.views.entries.tmpl._activity_log_item', ['log' => $log])
            @endforeach
          </ul>
      </x-admin-fieldset>
    @endif
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $tag->get('id') }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
