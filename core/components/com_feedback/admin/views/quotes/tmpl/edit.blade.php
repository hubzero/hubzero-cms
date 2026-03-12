{{--
  Feedback Quote — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo = \Components\Feedback\Helpers\Permissions::getActions('quote');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  $short_quote = $row->get('short_quote');
  $miniquote   = $row->get('miniquote');
  if (!$short_quote) {
      $short_quote = substr($row->get('quote'), 0, 270);
  }
  if (!$miniquote) {
      $miniquote = substr($short_quote, 0, 150);
  }
  if (strlen($short_quote) >= 271) {
      $short_quote = $short_quote . '...';
  }

  $__view->js();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_FEEDBACK') }}: {{ $text }}"
    icon="feedback"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    enctype="multipart/form-data"
>
  {{-- Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_FEEDBACK_DETAILS') }}">

      <div class="admin-field">
        <label class="label cursor-pointer justify-start gap-3">
          <input type="checkbox"
                 name="fields[notable_quote]"
                 id="field-notable_quote"
                 value="1"
                 class="checkbox"
                 @checked($row->get('notable_quote') == 1) />
          <span>{{ Lang::txt('COM_FEEDBACK_SELECT_FOR_QUOTES') }}</span>
        </label>
      </div>

      <div class="admin-field">
        <label for="field-fullname" class="label">
          {{ Lang::txt('COM_FEEDBACK_FULL_NAME') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[fullname]"
               id="field-fullname"
               class="input input-bordered w-full"
               required
               value="{{ $row->get('fullname') }}" />
      </div>

      <div class="admin-field">
        <label for="field-org" class="label">
          {{ Lang::txt('COM_FEEDBACK_ORGANIZATION') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[org]"
               id="field-org"
               class="input input-bordered w-full"
               required
               value="{{ $row->get('org') }}" />
      </div>

      <div class="admin-field">
        <label for="field-user_id" class="label">
          {{ Lang::txt('COM_FEEDBACK_USER_ID') }}
        </label>
        <input type="text"
               name="fields[user_id]"
               id="field-user_id"
               class="input input-bordered w-full"
               value="{{ $row->get('user_id') }}"
               @if($row->get('id') && $row->get('user_id')) readonly @endif />
        @if(!$row->get('id'))
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_FEEDBACK_USER_ID_EXPLANATION') }}
          </p>
        @endif
      </div>

      <fieldset class="border border-base-300 rounded-lg p-4">
        <legend class="text-sm font-semibold px-2">
          {{ Lang::txt('COM_FEEDBACK_AUTHOR_CONSENTS') }}
        </legend>
        <div class="space-y-2 mt-2">
          <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox"
                   name="fields[publish_ok]"
                   id="publish_ok"
                   value="1"
                   class="checkbox"
                   @checked($row->get('publish_ok') == 1)
                   @if($row->get('id')) disabled @endif />
            <span>{{ Lang::txt('COM_FEEDBACK_AUTHOR_CONSENT_PUBLISH') }}</span>
          </label>
          <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox"
                   name="fields[contact_ok]"
                   id="contact_ok"
                   value="1"
                   class="checkbox"
                   @checked($row->get('contact_ok') == 1)
                   @if($row->get('id')) disabled @endif />
            <span>{{ Lang::txt('COM_FEEDBACK_AUTHOR_CONSENT_CONTACT') }}</span>
          </label>
        </div>
      </fieldset>

      <div class="admin-field">
        <label for="field-quote" class="label">
          {{ Lang::txt('COM_FEEDBACK_FULL_QUOTE') }}
          <span class="text-error">*</span>
        </label>
        {!! $__view->editor(
            'fields[quote]',
            $row->get('quote'),
            50,
            10,
            'field-quote',
            ['class' => 'required']
        ) !!}
      </div>

      <div class="admin-field">
        <label for="field-short_quote" class="label">
          {{ Lang::txt('COM_FEEDBACK_SHORT_QUOTE') }}
        </label>
        {!! $__view->editor(
            'fields[short_quote]',
            $short_quote,
            40,
            10,
            'field-short_quote'
        ) !!}
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_FEEDBACK_SHORT_QUOTE_NOTE') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="miniquote" class="label">
          {{ Lang::txt('COM_FEEDBACK_MINIQUOTE') }}
        </label>
        <input type="text"
               name="fields[miniquote]"
               id="miniquote"
               class="input input-bordered w-full"
               maxlength="150"
               value="{{ $miniquote }}" />
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_FEEDBACK_MINIQUOTE_HINT') }}
        </p>
      </div>

      <div class="admin-field">
        <label for="field-notes" class="label">
          {{ Lang::txt('COM_FEEDBACK_EDITOR_NOTES') }}
        </label>
        {!! $__view->editor(
            'fields[notes]',
            $row->get('notes'),
            50,
            10,
            'field-notes'
        ) !!}
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_FEEDBACK_EDITOR_NOTES_EXPLANATION') }}
        </p>
  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_FEEDBACK_DETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_FEEDBACK_COL_ID') }}</td>
              <td>{{ $row->get('id', 0) }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_FEEDBACK_QUOTE_SUBMITTED') }}</td>
              <td>
                <input type="text"
                       name="fields[date]"
                       id="field-date"
                       class="input input-bordered input-sm w-full"
                       aria-label="{{ Lang::txt('COM_FEEDBACK_QUOTE_SUBMITTED') }}"
                       value="{{ $row->get('date', Date::toSql()) }}" />
              </td>
            </tr>
            @if($row->get('user_id'))
              @php $author = \Hubzero\Facades\User::getInstance($row->get('user_id')); @endphp
              @if(is_object($author) && $author->get('id'))
                <tr>
                  <td>{{ Lang::txt('COM_FEEDBACK_COL_AUTHOR') }}</td>
                  <td>{{ $author->get('name') }}</td>
                </tr>
              @endif
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Pictures --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_FEEDBACK_PICTURE') }}" body-class="space-y-3">
        @php $pictures = $row->files(); @endphp
        @foreach($pictures as $counter => $picture)
          @php
            list($ow, $oh, $type, $attr) = getimagesize($picture->getPathname());
            $num = max($ow / 120, $oh / 120);
            $mw = ($num > 1) ? round($ow / $num) : $ow;
            $mh = ($num > 1) ? round($oh / $num) : $oh;
            $img = substr($picture->getPathname(), strlen(PATH_ROOT));
          @endphp
          <div id="picture-{{ $counter }}" class="flex items-start gap-2">
            <input type="hidden"
                   name="existingPictures[{{ $counter }}]"
                   value="{{ $picture->getFilename() }}" />
            <img src="{{ $img }}"
                 height="{{ $mh }}"
                 width="{{ $mw }}"
                 alt=""
                 class="rounded border border-base-300" />
            <button type="button"
                    class="btn btn-sm btn-error btn-outline delete-image"
                    id="{{ $counter }}">
              {{ Lang::txt('COM_FEEDBACK_DELETE') }}
            </button>
          </div>
        @endforeach

        <div>
          <label for="imgInp" class="sr-only">{{ Lang::txt('COM_FEEDBACK_PICTURE') }}</label>
          <input id="imgInp"
                 type="file"
                 name="files[]"
                 multiple
                 class="file-input file-input-bordered file-input-sm w-full" />
          <div id="uploadImages" class="mt-2 flex flex-wrap gap-2"></div>
        </div>
    </x-admin-fieldset>
  @endslot

  {{-- Extra hidden fields --}}
  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="id" value="{{ $row->get('id') }}" />
</x-admin-edit>
