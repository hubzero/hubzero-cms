{{--
  Forum Thread/Post — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo  = \Components\Forum\Helpers\Permissions::getActions('thread');
  $access = Html::access('assetgroups');

  $isReply = (bool) $row->get('parent');
  $text  = ($isReply ? Lang::txt('COM_FORUM_POSTS') : Lang::txt('COM_FORUM_THREADS')) . ': ';
  $text .= ($row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

  Toolbar::title(Lang::txt('COM_FORUM') . ': ' . $text, 'forum');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('post');
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    enctype="multipart/form-data"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-scope" class="label">{{ Lang::txt('COM_FORUM_FIELD_SCOPE') }}</label>
          <input type="text"
                 name="fields[scope]"
                 id="field-scope"
                 class="input input-bordered w-full"
                 maxlength="150"
                 value="{{ $row->get('scope') }}" />
        </div>
        <div class="admin-field">
          <label for="field-scope_id" class="label">{{ Lang::txt('COM_FORUM_FIELD_SCOPE_ID') }}</label>
          <input type="text"
                 name="fields[scope_id]"
                 id="field-scope_id"
                 class="input input-bordered w-full"
                 maxlength="11"
                 value="{{ $row->get('scope_id') }}" />
        </div>
      </div>

      <div class="admin-field">
        <label for="field-object_id" class="label">{{ Lang::txt('COM_FORUM_FIELD_OBJECT_ID') }}</label>
        <input type="text"
               name="fields[object_id]"
               id="field-object_id"
               class="input input-bordered w-full"
               maxlength="11"
               value="{{ $row->get('object_id') }}" />
      </div>

      @if(!$isReply)
        <div class="admin-field">
          <label for="field-category_id" class="label">
            {{ Lang::txt('COM_FORUM_FIELD_CATEGORY') }} <span class="text-error">*</span>
          </label>
          <select name="fields[category_id]" id="field-category_id"
                  class="select select-bordered w-full">
            <option value="-1">{{ Lang::txt('COM_FORUM_FIELD_CATEGORY_SELECT') }}</option>
            @foreach($sections as $group => $sects)
              <optgroup label="{{ $group }}">
                @foreach($sects as $sect)
                  <optgroup label="&nbsp; &nbsp; {{ $sect->title }}">
                    @if(isset($sect->categories))
                      @foreach($sect->categories as $cat)
                        <option value="{{ $cat->id }}" @selected($row->get('category_id') == $cat->id)>
                          &nbsp; &nbsp; {{ $cat->title }}
                        </option>
                      @endforeach
                    @endif
                  </optgroup>
                @endforeach
              </optgroup>
            @endforeach
          </select>
        </div>
      @endif

      @if($isReply)
        <div class="admin-field">
          <label for="field-parent" class="label">{{ Lang::txt('COM_FORUM_FIELD_PARENT') }}</label>
          <select name="fields[parent]" id="field-parent"
                  class="select select-bordered w-full">
            <option value="0">{{ Lang::txt('COM_FORUM_FIELD_PARENT_SELECT') }}</option>
            @php
              $posts = \Components\Forum\Models\Post::all()
                  ->whereEquals('thread', $row->get('thread'))
                  ->ordered()
                  ->rows();
            @endphp
            @foreach($posts as $post)
              @if($post->get('id') != $row->get('id'))
                <option value="{{ $post->get('id') }}" @selected($row->get('parent') == $post->get('id'))>
                  {{ $post->get('title') }}
                </option>
              @endif
            @endforeach
          </select>
        </div>
      @endif

      <div class="admin-field">
        <label for="field-title" class="label">{{ Lang::txt('COM_FORUM_FIELD_TITLE') }}</label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="250"
               value="{{ $row->get('title') }}" />
      </div>

      <div class="admin-field">
        <label for="field-comment" class="label">
          {{ Lang::txt('COM_FORUM_FIELD_COMMENTS') }} <span class="text-error">*</span>
        </label>
        <textarea name="fields[comment]"
                  id="field-comment"
                  class="textarea textarea-bordered w-full"
                  rows="10"
                  required>{{ $row->get('comment') }}</textarea>
      </div>

      @if(!$isReply)
        <div class="admin-field">
          <label for="field-tags" class="label">{{ Lang::txt('COM_FORUM_FIELD_TAGS') }}</label>
          <textarea name="tags"
                    id="field-tags"
                    class="textarea textarea-bordered w-full"
                    rows="3">{{ $row->tags('string') }}</textarea>
        </div>
      @endif

  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_FORUM_LEGEND_ATTACHMENTS') }}">

      @if($row->get('id'))
        @php
          $mediaSrc = Route::url(
              'index.php?option=' . $option
              . '&controller=media&tmpl=component&id=' . $row->get('id')
              . '&t=' . Date::toUnix(),
              false, false
          );
        @endphp
        <iframe width="100%"
                height="200"
                name="media"
                id="media"
                title="{{ Lang::txt('COM_FORUM_LEGEND_ATTACHMENTS') }}"
                class="border border-base-300 rounded"
                src="{{ $mediaSrc }}"></iframe>
      @endif

      <div class="admin-field">
        <label for="upload" class="label">{{ Lang::txt('COM_FORUM_FIELD_FILE') }}</label>
        <input type="file" name="upload" id="upload" class="file-input file-input-bordered w-full" />
      </div>

      <div class="admin-field">
        <label for="field-attach-description" class="label">{{ Lang::txt('COM_FORUM_FIELD_DESCRIPTION') }}</label>
        <input type="text"
               name="description"
               id="field-attach-description"
               class="input input-bordered w-full"
               value="" />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            @if($isReply)
              <tr>
                <td>{{ Lang::txt('COM_FORUM_FIELD_THREAD') }}</td>
                <td>{{ $row->get('thread') }}</td>
              </tr>
            @endif
            <tr>
              <td>{{ Lang::txt('COM_FORUM_FIELD_CREATOR') }}</td>
              <td>{{ $row->creator->get('name') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_FORUM_FIELD_CREATED') }}</td>
              <td>{{ $row->get('created', Date::of('now')->toSql()) }}</td>
            </tr>
            @if($row->get('modified_by'))
              <tr>
                <td>{{ Lang::txt('COM_FORUM_FIELD_MODIFIER') }}</td>
                <td>{{ $row->modifier->get('name') }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_FORUM_FIELD_MODIFIED') }}</td>
                <td>{{ $row->get('modified') }}</td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">
        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="fields[anonymous]"
                   id="field-anonymous"
                   class="checkbox checkbox-sm"
                   value="1"
                   @checked($row->get('anonymous')) />
            <span class="label-text">{{ Lang::txt('COM_FORUM_FIELD_ANONYMOUS') }}</span>
          </label>
        </div>

        @if(!$isReply)
          <div class="admin-field">
            <label class="label cursor-pointer justify-start gap-2">
              <input type="checkbox"
                     name="fields[sticky]"
                     id="field-sticky"
                     class="checkbox checkbox-sm"
                     value="1"
                     @checked($row->get('sticky')) />
              <span class="label-text">{{ Lang::txt('COM_FORUM_FIELD_STICKY') }}</span>
            </label>
          </div>
        @endif

        <div class="admin-field">
          <label for="field-state" class="label">
            <span class="label-text">{{ Lang::txt('COM_FORUM_FIELD_STATE') }}</span>
          </label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-access" class="label">
            <span class="label-text">{{ Lang::txt('COM_FORUM_FIELD_ACCESS') }}</span>
          </label>
          <select name="fields[access]" id="field-access"
                  class="select select-bordered w-full">
            {!! Html::select('options', $access, 'value', 'text', $row->get('access')) !!}
          </select>
        </div>
    </x-admin-fieldset>

    @if($canDo->get('core.admin') && isset($form))
      <x-admin-fieldset legend="{{ Lang::txt('COM_FORUM_FIELDSET_RULES') }}">
          {!! $form->getInput('rules') !!}
      </x-admin-fieldset>
    @endif
  @endslot

  @if($isReply)
    <input type="hidden" name="fields[category_id]" value="{{ $row->get('category_id') }}" />
  @else
    <input type="hidden" name="fields[parent]" value="{{ $row->get('parent') }}" />
  @endif
  <input type="hidden" name="fields[thread]" value="{{ $row->get('thread') }}" />
  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[created_by]" value="{{ $row->get('created_by') }}" />
  <input type="hidden" name="fields[created]" value="{{ $row->get('created') }}" />
  <input type="hidden" name="thread" value="{{ $row->get('thread') }}" />
  <input type="hidden" name="parent" value="{{ $row->get('parent') }}" />
  @if($row->get('modified_by'))
    <input type="hidden" name="fields[modified_by]" value="{{ $row->get('modified_by') }}" />
    <input type="hidden" name="fields[modified]" value="{{ $row->get('modified') }}" />
  @endif
</x-admin-edit>
