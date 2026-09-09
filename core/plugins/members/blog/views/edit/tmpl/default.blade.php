{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Config;
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;

$tf = Event::trigger(
    'hubzero.onGetMultiEntry',
    [['tags', 'tags', 'actags', '', $entry->tags('string')]]
);

if ($entry->get('publish_down') && $entry->get('publish_down') == '0000-00-00 00:00:00') {
    $entry->set('publish_down', '');
}

$__view->css('jquery.datepicker.css', 'system')
    ->css('jquery.timepicker.css', 'system')
    ->css()
    ->js('jquery.timepicker', 'system')
    ->js();
@endphp

<div class="mb-4">
  <a class="btn btn-ghost btn-sm" href="{{ Route::url($member->link() . '&active=blog') }}">
    {{ Lang::txt('PLG_MEMBERS_BLOG_ARCHIVE') }}
  </a>
</div>

@if ($__view->getError())
  <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
@endif

<form action="{{ Route::url($member->link() . '&active=blog&task=save') }}"
      method="post"
      id="hubForm"
      class="space-y-4">

  <fieldset class="space-y-4">
    <legend class="text-lg font-semibold">{{ Lang::txt('PLG_MEMBERS_BLOG_EDIT_DETAILS') }}</legend>

    {{-- Title --}}
    <div class="form-control w-full">
      <label class="label" for="field-title">
        <span class="label-text">
          {{ Lang::txt('PLG_MEMBERS_BLOG_TITLE') }}
          <span class="text-error">*</span>
        </span>
      </label>
      <input type="text"
             class="input input-bordered w-full {{ ($task == 'save' && !$entry->get('title')) ? 'input-error' : '' }}"
             name="entry[title]"
             id="field-title"
             value="{{ e(stripslashes($entry->get('title', ''))) }}"
             required />
      @if ($task == 'save' && !$entry->get('title'))
        <label class="label">
          <span class="label-text-alt text-error">{{ Lang::txt('PLG_MEMBERS_BLOG_ERROR_PROVIDE_TITLE') }}</span>
        </label>
      @endif
    </div>

    {{-- Content --}}
    <div class="form-control w-full">
      <label class="label" for="entrycontent">
        <span class="label-text">
          {{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_CONTENT') }}
          <span class="text-error">*</span>
        </span>
      </label>
      {!! $__view->editor(
          'entry[content]',
          $entry->get('content'),
          50,
          30,
          'entrycontent',
          ['class' => 'form-control']
      ) !!}
      @if ($task == 'save' && !$entry->get('content'))
        <label class="label">
          <span class="label-text-alt text-error">{{ Lang::txt('PLG_MEMBERS_BLOG_ERROR_PROVIDE_CONTENT') }}</span>
        </label>
      @endif
    </div>

    {{-- Files --}}
    <fieldset>
      <legend class="font-medium">{{ Lang::txt('PLG_MEMBERS_BLOG_UPLOADED_FILES') }}</legend>
      @php
        $iframeSrc = Request::base(true)
            . '/index.php?option=com_blog&controller=media&id='
            . $member->get('id')
            . '&scope=member&tmpl=component';
      @endphp
      <iframe width="100%" height="260" name="filer" id="filer"
              src="{{ $iframeSrc }}" class="border rounded-box mt-2"></iframe>
    </fieldset>

    {{-- Tags --}}
    <div class="form-control w-full">
      <label class="label" for="actags">
        <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_TAGS') }}</span>
      </label>
      @if (count($tf) > 0)
        {!! implode("\n", $tf) !!}
      @else
        <input type="text"
               class="input input-bordered w-full"
               name="tags"
               id="actags"
               value="{{ e($entry->tags('string')) }}" />
      @endif
      <label class="label">
        <span class="label-text-alt">{{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_TAGS_HINT') }}</span>
      </label>
    </div>

    {{-- Options row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="label cursor-pointer justify-start gap-2">
        <input type="checkbox"
               class="checkbox"
               name="entry[allow_comments]"
               id="field-allow_comments"
               value="1"
               {{ $entry->get('allow_comments') == 1 ? 'checked' : '' }} />
        <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_ALLOW_COMMENTS') }}</span>
      </label>

      <div class="form-control">
        <label class="label" for="field-access">
          <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_PRIVACY') }}</span>
        </label>
        <select class="select select-bordered w-full" name="entry[access]" id="field-access">
          <option value="1" {{ $entry->get('access') == 1 ? 'selected' : '' }}>
            {{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_PRIVACY_PUBLIC') }}
          </option>
          <option value="2" {{ $entry->get('access') == 2 ? 'selected' : '' }}>
            {{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_PRIVACY_REGISTERED') }}
          </option>
          <option value="5" {{ $entry->get('access') > 2 ? 'selected' : '' }}>
            {{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_PRIVACY_PRIVATE') }}
          </option>
        </select>
      </div>
    </div>

    {{-- Publish dates --}}
    @php
      $tzOffset = timezone_offset_get(
          new DateTimeZone(Config::get('offset')),
          Date::of('now')
      ) / 60;
      $publishUpVal = ($entry->get('publish_up'))
          ? e(Date::of($entry->get('publish_up'))->toLocal('Y-m-d H:i:s'))
          : '';
      $publishDownVal = ($entry->get('publish_down'))
          ? e(Date::of($entry->get('publish_down'))->toLocal('Y-m-d H:i:s'))
          : '';
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="form-control">
        <label class="label" for="field-publish_up">
          <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_PUBLISH_UP') }}</span>
        </label>
        <input type="text"
               class="input input-bordered w-full"
               name="entry[publish_up]"
               id="field-publish_up"
               data-timezone="{{ $tzOffset }}"
               value="{{ $publishUpVal }}" />
        <label class="label">
          <span class="label-text-alt">{{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_PUBLISH_HINT') }}</span>
        </label>
      </div>
      <div class="form-control">
        <label class="label" for="field-publish_down">
          <span class="label-text">{{ Lang::txt('PLG_MEMBERS_BLOG_PUBLISH_DOWN') }}</span>
        </label>
        <input type="text"
               class="input input-bordered w-full"
               name="entry[publish_down]"
               id="field-publish_down"
               data-timezone="{{ $tzOffset }}"
               value="{{ $publishDownVal }}" />
        <label class="label">
          <span class="label-text-alt">{{ Lang::txt('PLG_MEMBERS_BLOG_FIELD_PUBLISH_HINT') }}</span>
        </label>
      </div>
    </div>
  </fieldset>

  <input type="hidden" name="id" value="{{ e($entry->get('created_by')) }}" />
  <input type="hidden" name="entry[id]" value="{{ e($entry->get('id')) }}" />
  <input type="hidden" name="entry[alias]" value="{{ e($entry->get('alias')) }}" />
  <input type="hidden" name="entry[created]" value="{{ e($entry->get('created')) }}" />
  <input type="hidden" name="entry[created_by]" value="{{ e($entry->get('created_by')) }}" />
  <input type="hidden" name="entry[scope]" value="member" />
  <input type="hidden" name="entry[scope_id]" value="{{ $entry->get('scope_id') }}" />
  <input type="hidden" name="entry[state]" value="{{ $entry->get('state', 1) }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="active" value="blog" />
  <input type="hidden" name="task" value="view" />
  <input type="hidden" name="action" value="save" />

  {!! Html::input('token') !!}

  <div class="flex gap-2">
    <button type="submit" class="btn btn-primary">
      {{ Lang::txt('PLG_MEMBERS_BLOG_SAVE') }}
    </button>
    @if ($entry->get('id'))
      <a class="btn btn-ghost" href="{{ Route::url($entry->link()) }}">
        {{ Lang::txt('JCANCEL') }}
      </a>
    @endif
  </div>
</form>
