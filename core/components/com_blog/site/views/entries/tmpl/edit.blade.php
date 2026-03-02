{{--
  Blog entry edit/new form — daisyUI v5 fieldset layout.

  Variables from controller (editTask):
    $entry    — Entry model instance (new or existing)
    $archive  — Archive model instance
    $config   — Component params (Registry)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  // Breadcrumbs
  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_BLOG'), 'index.php?option=' . $option);
  }
  $editTitle = $entry->isNew() ? Lang::txt('JACTION_NEW') : Lang::txt('JACTION_EDIT');
  Pathway::append($editTitle, $entry->link('edit'));

  Document::setTitle(Lang::txt('COM_BLOG') . ': ' . $editTitle);

  $__view->css();
  $__view->js();

  // Normalize empty publish_down
  if ($entry->get('publish_down') === '0000-00-00 00:00:00') {
      $entry->set('publish_down', '');
  }

  // Form URLs
  $saveUrl = Route::url('index.php?option=' . $option . '&task=save');
  $cancelUrl = $entry->get('id')
      ? Route::url($entry->link())
      : Route::url('index.php?option=' . $option);
  $archiveUrl = Route::url('index.php?option=' . $option);

  // File manager URL
  $mediaUrl = Route::url(
      'index.php?option=' . $option . '&tmpl=component&controller=media'
  );

  // Timezone offset for date fields
  $tzOffset = timezone_offset_get(
      new DateTimeZone(Config::get('offset')),
      Date::getRoot()
  ) / 60;

  // Format dates for inputs
  $publishUp = $entry->get('publish_up')
      ? Date::of($entry->get('publish_up'))->toLocal('Y-m-d\TH:i')
      : '';
  $publishDown = $entry->get('publish_down')
      ? Date::of($entry->get('publish_down'))->toLocal('Y-m-d\TH:i')
      : '';

  $access = $entry->get('access', 1);
@endphp

{{-- Page header --}}
<header class="page-header">
  <h1>{{ Lang::txt('COM_BLOG') . ': ' . $editTitle }}</h1>
  <div class="page-header-actions">
    <a class="btn" href="{{ $archiveUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
      </svg>
      {{ Lang::txt('COM_BLOG_ARCHIVE') }}
    </a>
  </div>
</header>

<section class="edit-form page-body">
  <div class="page-layout">
    <div class="page-main">

      <form id="hubForm" method="post" action="{{ $saveUrl }}" class="space-y-6">

        {{-- Section: Details --}}
        <div class="form-section">
          <h2 class="form-section-heading">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            {{ Lang::txt('COM_BLOG_EDIT_DETAILS') }}
          </h2>

          <div class="form-section-body">
            {{-- Title --}}
            <div class="form-field">
              <label class="form-field-label" for="field-title">
                {{ Lang::txt('COM_BLOG_FIELD_TITLE') }}
                <span class="text-error">*</span>
              </label>
              <input type="text"
                     id="field-title"
                     name="entry[title]"
                     class="input w-full"
                     value="{{ e(stripslashes($entry->get('title', ''))) }}"
                     placeholder="{{ Lang::txt('COM_BLOG_FIELD_TITLE') }}"
                     required />
              <p class="form-field-hint text-error hidden" id="field-title-error" role="alert">
                {{ Lang::txt('COM_BLOG_ERROR_PROVIDE_TITLE') }}
              </p>
            </div>

            {{-- Content --}}
            <div class="form-field">
              <label class="form-field-label" for="entrycontent">
                {{ Lang::txt('COM_BLOG_FIELD_CONTENT') }}
                <span class="text-error">*</span>
              </label>
              {!! $__view->editor(
                  'entry[content]',
                  e($entry->content('raw')),
                  50, 20,
                  'entrycontent',
                  ['class' => 'textarea w-full']
              ) !!}
              <p class="form-field-hint text-error hidden" id="field-content-error" role="alert">
                {{ Lang::txt('COM_BLOG_ERROR_PROVIDE_CONTENT') }}
              </p>
            </div>

            {{-- Tags --}}
            <div class="form-field">
              <label class="form-field-label" for="actags">
                {{ Lang::txt('COM_BLOG_FIELD_TAGS') }}
              </label>
              {!! $__view->autocompleter(
                  'tags', 'tags',
                  e($entry->tags('string')),
                  'actags'
              ) !!}
              <p class="form-field-hint">
                {{ Lang::txt('COM_BLOG_FIELD_TAGS_HINT') }}
              </p>
            </div>
          </div>
        </div>

        {{-- Section: Publishing --}}
        <div class="form-section">
          <h2 class="form-section-heading">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            {{ Lang::txt('COM_BLOG_FIELD_PUBLISH_UP') }}
          </h2>

          <div class="form-section-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              {{-- Allow comments --}}
              <div class="flex items-end h-full">
                <label class="checkbox-label mb-2.5">
                  <input type="checkbox"
                         class="checkbox"
                         name="entry[allow_comments]"
                         id="field-allow_comments"
                         value="1"
                         @if($entry->get('allow_comments', 1) == 1) checked @endif />
                  <span>{{ Lang::txt('COM_BLOG_FIELD_ALLOW_COMMENTS') }}</span>
                </label>
              </div>

              {{-- Privacy / Access --}}
              <div>
                <label class="form-field-label" for="field-access">
                  {{ Lang::txt('COM_BLOG_FIELD_PRIVACY') }}
                </label>
                <select id="field-access"
                        name="entry[access]"
                        class="select w-full">
                  <option value="1" @if($access == 1) selected @endif>
                    {{ Lang::txt('COM_BLOG_FIELD_PRIVACY_PUBLIC') }}
                  </option>
                  <option value="2" @if($access == 2) selected @endif>
                    {{ Lang::txt('COM_BLOG_FIELD_PRIVACY_REGISTERED') }}
                  </option>
                  <option value="5" @if($access > 2) selected @endif>
                    {{ Lang::txt('COM_BLOG_FIELD_PRIVACY_PRIVATE') }}
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              {{-- Publish up --}}
              <div>
                <label class="form-field-label" for="field-publish_up">
                  {{ Lang::txt('COM_BLOG_FIELD_PUBLISH_UP') }}
                </label>
                <input type="datetime-local"
                       id="field-publish_up"
                       name="entry[publish_up]"
                       class="input w-full"
                       value="{{ $publishUp }}" />
                <p class="form-field-hint">
                  {{ Lang::txt('COM_BLOG_FIELD_PUBLISH_HINT') }}
                </p>
              </div>

              {{-- Publish down --}}
              <div>
                <label class="form-field-label" for="field-publish_down">
                  {{ Lang::txt('COM_BLOG_FIELD_PUBLISH_DOWN') }}
                </label>
                <input type="datetime-local"
                       id="field-publish_down"
                       name="entry[publish_down]"
                       class="input w-full"
                       value="{{ $publishDown }}" />
                <p class="form-field-hint">
                  {{ Lang::txt('COM_BLOG_FIELD_PUBLISH_HINT') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        {{-- Form actions --}}
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">
            {{ Lang::txt('JSAVE') }}
          </button>
          <a class="btn btn-ghost" href="{{ $cancelUrl }}">
            {{ Lang::txt('JCANCEL') }}
          </a>
        </div>

        {{-- Hidden fields --}}
        <input type="hidden" name="id" value="{{ $entry->get('id') }}" />
        <input type="hidden" name="entry[id]" value="{{ $entry->get('id') }}" />
        <input type="hidden" name="entry[alias]" value="{{ $entry->get('alias') }}" />
        <input type="hidden" name="entry[created]" value="{{ $entry->get('created') }}" />
        <input type="hidden" name="entry[created_by]" value="{{ $entry->get('created_by') }}" />
        <input type="hidden" name="entry[scope]" value="site" />
        <input type="hidden" name="entry[scope_id]" value="0" />
        <input type="hidden" name="entry[state]" value="{{ $entry->get('state', 1) }}" />
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="task" value="save" />
        {!! Html::input('token') !!}

      </form>

    </div>

    {{-- Sidebar --}}
    <aside class="page-sidebar">

      {{-- File manager --}}
      <div class="file-manager-card">
        <div class="file-manager-header">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
          </svg>
          {{ Lang::txt('COM_BLOG_FIELD_FILES') }}
        </div>
        <iframe height="520"
                name="filer"
                id="filer"
                src="{{ $mediaUrl }}"
                class="w-full border-0"
                title="{{ Lang::txt('COM_BLOG_FIELD_FILES') }}"></iframe>
      </div>

    </aside>
  </div>
</section>
