{{--
  Thread/post edit/new form.

  Variables from controller (editTask):
    $post     — Post model instance (new or existing)
    $category — Category model
    $section  — Section model
    $forum    — Manager model
    $config   — Component params (Registry)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();

  $category->set('section_alias', $section->get('alias'));
  $post->set('section', $section->get('alias'));
  $post->set('category', $category->get('alias'));

  $isNew = !$post->get('id');
  $editTitle = $isNew
      ? Lang::txt('COM_FORUM_NEW_DISCUSSION')
      : Lang::txt('COM_FORUM_EDIT_DISCUSSION');

  $formAction = $isNew
      ? Route::url($post->link('new'), false)
      : Route::url($post->link('edit'), false);

  $categoryUrl = Route::url($category->link(), false);

  if ($isNew) {
      $post->set('access', 0);
  }

  $canManage  = $config->get('access-manage-thread');
  $isParent   = !$post->get('parent');
  $attachment = $post->attachments()->row();
@endphp

<x-page-container :title="Lang::txt('COM_FORUM') . ': ' . $editTitle">
  @slot('actions')
    <a class="btn" href="{{ $categoryUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
      </svg>
      {{ Lang::txt('COM_FORUM_ALL_DISCUSSIONS') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card>
      <p class="text-sm mb-2">
        <strong>{{ Lang::txt('COM_FORUM_WHAT_IS_STICKY') }}</strong><br />
        {{ Lang::txt('COM_FORUM_STICKY_EXPLANATION') }}
      </p>
      <p class="text-sm">
        <strong>{{ Lang::txt('COM_FORUM_WHAT_IS_LOCKING') }}</strong><br />
        {{ Lang::txt('COM_FORUM_LOCKING_EXPLANATION') }}
      </p>
    </x-sidebar-card>
  @endslot

  <form action="{{ $formAction }}" method="post" id="commentform"
        enctype="multipart/form-data" class="space-y-6">

    @if($isParent)
      <x-form-section :heading="$editTitle">
        {{-- Sticky / Closed (manager only) --}}
        @if($canManage)
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="flex items-end h-full">
              <label class="checkbox-label mb-2.5">
                <input type="checkbox" class="checkbox" name="fields[sticky]"
                       id="field-sticky" value="1"
                       @if($post->get('sticky')) checked @endif />
                <span>{{ Lang::txt('COM_FORUM_FIELD_STICKY') }}</span>
              </label>
            </div>
            <div class="flex items-end h-full">
              <label class="checkbox-label mb-2.5">
                <input type="checkbox" class="checkbox" name="fields[closed]"
                       id="field-closed" value="1"
                       @if($post->get('closed')) checked @endif />
                <span>{{ Lang::txt('COM_FORUM_FIELD_CLOSED_THREAD') }}</span>
              </label>
            </div>
          </div>
        @else
          <input type="hidden" name="fields[sticky]" value="{{ $post->get('sticky') }}" />
          <input type="hidden" name="fields[closed]" value="{{ $post->get('closed') }}" />
        @endif

        {{-- Access --}}
        <x-form-field name="field-access"
                      :label="Lang::txt('COM_FORUM_FIELD_READ_ACCESS')">
          <select id="field-access" name="fields[access]" class="select w-full">
            <option value="1" @if($post->get('access') == 1) selected @endif>
              {{ Lang::txt('COM_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC') }}
            </option>
            <option value="2" @if($post->get('access') == 2) selected @endif>
              {{ Lang::txt('COM_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED') }}
            </option>
          </select>
        </x-form-field>

        {{-- Category --}}
        <x-form-field name="field-category_id"
                      :label="Lang::txt('COM_FORUM_FIELD_CATEGORY')"
                      :required="true">
          <select id="field-category_id" name="fields[category_id]" class="select w-full" required>
            @php
              $catFilters = ['state' => 1, 'access' => User::getAuthorisedViewLevels()];
            @endphp
            @foreach($forum->sections($catFilters)->rows() as $sec)
              @php
                $cats = $sec->categories()
                    ->whereEquals('state', $catFilters['state'])
                    ->whereIn('access', $catFilters['access'])
                    ->rows();
              @endphp
              @if($cats->count() > 0)
                <optgroup label="{{ e(stripslashes($sec->get('title'))) }}">
                  @foreach($cats as $cat)
                    <option value="{{ $cat->get('id') }}"
                            @if($category->get('alias') == $cat->get('alias')) selected @endif>
                      {{ e(stripslashes($cat->get('title'))) }}
                    </option>
                  @endforeach
                </optgroup>
              @endif
            @endforeach
          </select>
        </x-form-field>

        {{-- Title --}}
        <x-form-field name="field-title"
                      :label="Lang::txt('COM_FORUM_FIELD_TITLE')">
          <input type="text" id="field-title" name="fields[title]" class="input w-full"
                 value="{{ e(stripslashes($post->get('title', ''))) }}" />
        </x-form-field>
      </x-form-section>
    @else
      {{-- Reply: hide thread-level fields --}}
      <input type="hidden" name="fields[category_id]" value="{{ $post->get('category_id') }}" />
      <input type="hidden" name="fields[access]" value="{{ $post->get('access', 0) }}" />
    @endif

    {{-- Comment --}}
    <x-form-section :heading="Lang::txt('COM_FORUM_FIELD_COMMENTS')">
      <div class="form-field">
        {!! $__view->editor(
            'fields[comment]',
            e(stripslashes($post->get('comment', ''))),
            35, 15, 'fieldcomment',
            [
                'class' => 'minimal no-footer',
                'mentions' => [[
                    'minChars' => 0,
                    'feed' => '/api/members/mentions/list?search={encodedQuery}',
                    'itemTemplate' => '<li data-id="{id}"><img class="photo" src="{picture}" /><strong class="username">{username}</strong><span class="fullname">{name}</span></li>',
                    'outputTemplate' => '<a href="/members/{id}" data-user-id="{id}" target="_blank">@{username}</a>&nbsp;&nbsp;',
                ]]
            ]
        ) !!}
      </div>

      {{-- Tags --}}
      <div class="form-field">
        <label class="form-field-label" for="actags">
          {{ Lang::txt('COM_FORUM_FIELD_TAGS') }}
        </label>
        {!! $__view->autocompleter('tags', 'tags', e($post->tags('string')), 'actags') !!}
      </div>

      {{-- Attachment --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-field">
          <label class="form-field-label" for="upload">
            {{ Lang::txt('COM_FORUM_FIELD_FILE') }}
            @if($attachment->get('filename'))
              <strong class="text-sm">{{ e(stripslashes($attachment->get('filename'))) }}</strong>
            @endif
          </label>
          <input type="file" name="upload" id="upload" class="file-input file-input-bordered w-full" />
        </div>
        <div class="form-field">
          <label class="form-field-label" for="field-attach-description">
            {{ Lang::txt('COM_FORUM_FIELD_DESCRIPTION') }}
          </label>
          <input type="text" name="description" id="field-attach-description" class="input w-full"
                 value="{{ e(stripslashes($attachment->get('description', ''))) }}" />
        </div>
      </div>
      <input type="hidden" name="attachment" value="{{ $attachment->get('id') }}" />
      @if($attachment->get('id'))
        <p class="text-sm text-warning mt-1">{{ Lang::txt('COM_FORUM_FIELD_FILE_WARNING') }}</p>
      @endif

      {{-- Anonymous --}}
      @if($config->get('allow_anonymous'))
        <label class="checkbox-label mt-2">
          <input type="checkbox" class="checkbox" name="fields[anonymous]"
                 id="field-anonymous" value="1"
                 @if($post->get('anonymous')) checked @endif />
          <span>{{ Lang::txt('COM_FORUM_FIELD_ANONYMOUS') }}</span>
        </label>
      @endif
    </x-form-section>

    {{-- Form actions --}}
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">{{ Lang::txt('JSUBMIT') }}</button>
      <a class="btn btn-ghost" href="{{ $categoryUrl }}">{{ Lang::txt('JCANCEL') }}</a>
    </div>

    {{-- Hidden fields --}}
    <input type="hidden" name="fields[parent]" value="{{ $post->get('parent') }}" />
    <input type="hidden" name="fields[state]" value="1" />
    <input type="hidden" name="fields[thread]" value="{{ $post->get('thread') }}" />
    <input type="hidden" name="fields[id]" value="{{ $post->get('id') }}" />
    <input type="hidden" name="fields[scope]" value="site" />
    <input type="hidden" name="fields[scope_id]" value="0" />
    <input type="hidden" name="fields[scope_sub_id]" value="{{ $post->get('scope_sub_id') }}" />
    <input type="hidden" name="fields[object_id]" value="{{ $post->get('object_id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="threads" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="section" value="{{ e($section->get('alias')) }}" />
    {!! Html::input('token') !!}
  </form>
</x-page-container>
